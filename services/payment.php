<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");


$type =  GetParameter("type");

switch($type){
	case "manual":
		
		$result = payment_manual($_POST);
		$key = GetParameter('payment_reserve_id');
		send_mail_complete($key);
		
	break;
	case "online":
	
		$result = "payment online complete.";
		
	break;
	case "confirm" :
	
	
		/* verify unique key */
		
		$key = GetParameter('reserve_number');
		$email = GetParameter('email');
		
		$result = verify_key($key,$email);
		
		if($result=="true")
		{
			echo "<script>window.location='../confirmation.html?reserve_id=".$key."';</script>";	
		}else{
			echo "<script>alert('Sorry!! reserve number or email is invalid. '); window.history.back();</script>";	
		}
		
		
		exit();
		
	break;
}

echo json_encode(array("result"=> $result ,"code"=>"0"));

function payment_manual($items){
	
	if($_FILES['file_evident']['name']!=""){
		
					$filename = "images/evident/". date("His") ."_".$_FILES['file_evident']['name'];
					$distination =  "../".$filename;
					$source = $_FILES['file_evident']['tmp_name'];  
					$items["payment_evident"] = $filename;
		}
		
		$manager = new reserve_manager();
		$manager->update_payment($items);
		
		upload_image($source,$distination);
		
	return "upload payment complete.";
}

function verify_key($key,$email){
	
	$result = 'false';
	$base = new Reserve_Manager();

	//## get reserve info ##
	$reserve_data = $base->get_reserve_info($key);
	$data = $reserve_data->fetch_object();
	
	/* verify email */
	if($data->email==$email){
		$result = 'true';
	}
	
	return $result;
	
	
}

function send_mail_complete($key){

	$reserve = get_reserve($key,"en");

	$message = file_get_contents("../templete_email_complete.html");

	$message = str_replace("{reserve_id}",$key,$message);
	$message = str_replace("{start_date}",full_date_format($reserve["info"]->date_start,"en"),$message);
	$message = str_replace("{end_date}",full_date_format($reserve["info"]->date_end,"en"),$message);
	$message = str_replace("{expire_date}",full_date_format($reserve["info"]->date_expire,"en"),$message);
	$message = str_replace("{adults}",$reserve["info"]->adults,$message);
	$message = str_replace("{children_2}",$reserve["info"]->children_2,$message);
	$message = str_replace("{children_1}",$reserve["info"]->children,$message);
	$cust_fullname = $reserve["customer"]->title_name . " " . $reserve["customer"]->fname . " ". $reserve["customer"]->lname;
	$message = str_replace("{customer_name}",$cust_fullname,$message);
	$message = str_replace("{customer_mobile}",$reserve["customer"]->mobile,$message);
	$message = str_replace("{customer_email}",$reserve["customer"]->email,$message);
	$message = str_replace("{special_request}",$reserve["customer"]->comment,$message);
	$message = str_replace("{list_reserve}",set_email_list_reserve($reserve),$message);
	
	/* get package info */
	$rooms = (array)$reserve['rooms'];
	$pack_id = $rooms[0]["key"];
	$base_room = new Room_Manager();
	$package_obj = $base_room->get_item_package($pack_id,'en'); //fix language 
	$package_info = $package_obj->fetch_object();
	
		/* initial policy */
	$package_name = $package_info->title;
	$date_range = datediff(date('Y-m-d'),$reserve["info"]->date_start); 
	$lang = 'en';
	
	/* cancel policy */
	$expire_date = full_date_format($reserve["info"]->date_expire,"en");
	$cancel_policy = get_cancel_policy($package_name,$date_range,$lang);
	$cancel_policy = str_replace('{expire_date}',$expire_date,$cancel_policy);
	$message = str_replace("{cancel_policy}",$cancel_policy,$message);		
	
	/* payment policy */
	$payment_policy = get_payment_policy($package_name,$date_range,$lang);
	$message = str_replace('{payment_policy}',$payment_policy,$message);	
	
	/*show package condition*/
	$message = str_replace("{condition_detail}",get_condition($date_range,$package_info),$message);
	

	/* initial email */
	$cust_name = $reserve['customer']->title . ' '. $reserve['customer']->fname . ' '.$reserve['customer']->lname;
	$receive[] = array("email"=>$reserve["customer"]->email ,"alias"=>$cust_name);
	$sender = "system@haven-huahin.com";
	$sender_name = "system haven huahin resort";
	$subject = "Your Reservation has been successful.";

	SendMail($receive,$sender,$subject,$message,$sender_name);
}

function set_email_list_reserve($reserve){

	$info = $reserve["info"];
	$date_start = full_date_format($info->date_start,"en");
	$date_end = full_date_format($info->date_end,"en");
	$result = "<table>";
	$result .= "<tr><td colspan='3' style='font-size:8px;'>Reservation details, from ".$date_start." to ".$date_end." </td></tr>";

	$summary = $reserve["summary"];
	$rooms = $reserve["rooms"];
	$options = $reserve["options"];
	//##room
	if(isset($rooms)){
		$room_seq=0;
		foreach($rooms as $val){
				$room_seq+=1;
				$result .= "<tr>";
				$result .= "<td class='text-top'>".$room_seq."</td>";
				$result .= "<td >Room Type : ".$val["type"]."<br/>Bedding options : ".$val["bed_name"]."<br/>Rate Plan : ".$val["room"]."</td>";
				$result .= "<td class='text-top' style='text-align:right'>฿ ".number_format($val["price"],2)."</td>";
				$result .= "</tr>";
		}
	}

	$result .= "<tr class='table_small' ><td>&nbsp;</td><td>Service Charge </td><td class='text-right'>฿ ".number_format($summary->serivce,2)."</td></tr>";
	$result .= "<tr class='table_small' ><td>&nbsp;</td><td>VAT  </td><td class='text-right'>฿ ".number_format($summary->vat,2)."</td></tr>";
	$result .= "<tr><td><b>Total<b/></td><td></td><td class='text-right'>฿ ".number_format($summary->sum,2)."</td></tr>";

	//options
	if(isset($options)){
		$result ."<tr><td colspan='3'><hr/></td></tr>";
		foreach($options as $val){
				$result .= "<tr>";
				$result .= "<td >&nbsp;</td>";
				$result .= "<td >".$val["title"]."<br/>".$val["desc"]."</td>";
				$result .= "<td style='text-align:right'>฿ ".number_format($val["price"],2)."</td>";
				$result .= "</tr>";
		}
	}

	
	
	$result .= "<tr><td>&nbsp;</td><td  class='table_small' ></td><td class='text-right'>฿ ".number_format($summary->net,2)."</td></tr>";
	$result .= "</table>";

	return $result;
}


function get_reserve($uniqueKey,$lang){
	
$base = new Reserve_Manager();

//## get reserve info ##
$reserve_data = $base->get_reserve_info($uniqueKey);
$data = $reserve_data->fetch_object();
$info = null;
$customer=null;
$payment=null;

	$info = array(
		"date_start"=>$data->reserve_startdate
		,"date_end"=>$data->reserve_enddate
		,"date_expire"=>$data->reserve_expire
		,"night"=>$data->night
		,"adults"=>$data->adults
		,"children"=>$data->children
		,"children_2"=>$data->children_2
		,"code"=>$data->acc_code
	);

	$customer = array(
		"email"=>$data->email
		,"title"=>$data->title_name
		,"fname"=>$data->first_name
		,"lname"=>$data->last_name
		,"prefix_mobile"=>$data->prefix
		,"mobile"=>$data->mobile
		,"birthdate"=>$data->birthdate
		,"comment"=>$data->reserve_comment
		);
		
	$summary = array(
		"sum"=>$data->price_sum
		,"serivce"=>$data->price_service
		,"vat"=>$data->price_vat
		,"net"=>$data->price_net
	);

//## ger reserve room  ##
				
	$room_data = $base->get_reserve_rooms($uniqueKey,$lang);
	
	if(isset($room_data)){
		while($row = $room_data->fetch_object()){
			$rooms[] = array(
			"key"=>$row->id
			,"room"=>$row->title
			,"type"=>$row->room_type
			,"price"=>$row->room_price
			,"bed_name"=>$row->bed_name
			);
		}
	}
//## ger reserve option  ##
	$option_data = $base->get_reserve_options($uniqueKey,$lang);
	if(isset($option_data)){
		while($row = $option_data->fetch_object()){
			$options[] = array(
				"key"=>$row->id
				,"title"=>$row->title
				,"price"=>$row->option_price
				,"desc"=>$row->option_desc
			);
		}
	}

//## consolidate reservation ##

	$reserve = array("info"=>(object) $info
		,"rooms"=>(object) $rooms
		,"options"=>(object) $options
		,"customer"=>(object) $customer
		,"summary"=>(object) $summary
	);
	//var_dump($reserve);
	return $reserve;
}


function get_condition($date_range,$package_info){
	
	$result = "";
	if($date_range <= 14 and strpos(strtolower($package_info->title), 'internet') !== false){
		$result = "<b>Terms & conditions </b><br/><br/>";
		$result = "* The above rate are include breakfast. <br/>";
		$result .= "* Full payment in advance required. Your credit card will be charged at the time of reservation. <br/>";
		$result .= "* Cancellation NO REFUND in the event of the reservation being cancelled, amended or no show.  <br/>";
		$result .= "* If you depart early or you cancel or fail to honor this reservation for any reason, you will not receive any credit or refund. <br/>";
		$result .= "* Extensions will require a new reservation for the additional date(s), subject to availability and prevailing rates, and this rate shall not apply.  <br/>";
		$result .= "* This rate is not combinable with any other offers and promotions and is not available to groups. <br/>";
		$result .= "* Rates are subject to availability. <br/>";
		$result .= "* Rates are quoted in Thai Baht(THB). <br/>";
		$result .= "* Rates quoted are subjected to 7% government tax and 10% service charge. <br/>";
		$result .= "* Rate is subject to change without notice. <br/><br/>";
		$result .= "* Check in time is from 14:00 hours & Check out until 12:00 noon. <br/>";
		$result .= "* Please note that children age 12 and older are charged the adult rate. Please include them in the number entered   in the No. of Adults box. <br/>";
		$result .= "* Children between 5 to 11 years old sharing the existing bed include breakfast is charged THB 234 per child per day. <br/>";
		$result .= "Or and extra bed with breakfast is THB 510 per child per day. Rates are subject to Tax and Service Charge. <br/>";
		$result .= "* Baby cot is free and advance request must be made.  <br/>";
		$result .= "* Extra bed over 12 years old with full daily breakfast is charge at THB 1020 per bed per night subject to tax and service charge. <br/>"; 
		$result .= "* Should you wish to change an existing reservation, you are required to cancel the existing booking and proceed to create a new reservation. This maybe done by visiting our website www.haven-huahin.com <br/>";
		$result .= "* Should you wish to cancel an existing reservation, simply click on the reservation link at our website www.haven-huahin.com <br/>";
		$result .= "* Please do not hesitate to contact us at the following e-mail address: rsvn@haven-huahin.com, we are at your disposal for any further information you need. <br/>";
	}
	else{
		$result  = $package_info->conditions;
	}
	
	return $result;
		
}
?>