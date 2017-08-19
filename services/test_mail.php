<?php
session_start();
include("../lib/logger.php");
include("../lib/common.php");
include("../managers/reserve_manager.php");

	//$receive = array("neosvargalok@hotmail.com"=>"customer");
	//$receive = array("werawat.limprasert@gmail.com"=>"customer");

	$receive[] = array("email"=>"svargalok@gmail.com","alias"=>"customer gmail");
	$receive[] = array("email"=>"werawat.limprasert@gmail.com","alias"=>"customer gmail");

	//$receive="svargalok@gmail.com";
	$sender = "system@haven-huahin.com";
	$sender_name = "system haven huahin resort=".date("His");
	$subject = "Thank You Reservation (Testing) ".date("His");

	//$message = file_get_contents("../templete_email_booking.html");
	$message = file_get_contents("../templete_email_booking.html");
	$unique_key= "mSeaawcvFAhsnu6CJbpQ";
	$reserve = get_reserve($unique_key,"en");
	$message = str_replace("{reserve_id}",$unique_key,$message);
	$message = str_replace("{start_date}",full_date_format($reserve["info"]->date_start,"en"),$message);
	$message = str_replace("{end_date}",full_date_format($reserve["info"]->date_end,"en"),$message);
	$message = str_replace("{expire_date}",full_date_format($reserve["info"]->date_expire,"en"),$message);
	$message = str_replace("{adults}",$reserve["info"]->adults,$message);
	$message = str_replace("{children_2}",$reserve["info"]->children_2,$message);
	$message = str_replace("{children_1}",$reserve["info"]->children,$message);

	$message = str_replace("{customer_name}",$reserve["customer"]->fname,$message);
	$message = str_replace("{customer_mobile}",$reserve["customer"]->mobile,$message);
	$message = str_replace("{customer_email}",$reserve["customer"]->email,$message);
	$message = str_replace("{list_reserve}",set_email_list_reserve($reserve),$message);
	
	SendMail($receive,$sender,$subject,$message,$sender_name);
	foreach($receive as $to){
		echo "send email complete." . $to['email']."</br>";
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

	$result .= "<tr class='table_small' ><td>&nbsp;</td><td>Not included: Service Charge </td><td class='text-right'>฿ ".number_format($summary->service,2)."</td></tr>";
	$result .= "<tr class='table_small' ><td>&nbsp;</td><td>Not included: VAT  </td><td class='text-right'>฿ ".number_format($summary->vat,2)."</td></tr>";
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

		
	$result .= "<tr><td>&nbsp;</td><td  class='table_small' >The taxes which are not included are to be paid to the hotel. The total amount is: </td><td class='text-right'>฿ ".number_format($summary->net,2)."</td></tr>";
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
		,"comment"=>$data->reserve_comment
	);

	$customer = array(
		"email"=>$data->email
		,"title"=>$data->title_name
		,"fname"=>$data->first_name
		,"lname"=>$data->last_name
		,"prefix_mobile"=>$data->prefix
		,"mobile"=>$data->mobile
		,"birthdate"=>$data->birthdate
		);
	$summary = array(
		"room"=>$data->price_room
		,"option"=>$data->price_option
		,"sum"=>$data->price_sum
		,"service"=>$data->price_service
		,"vat"=>$data->price_vat
		,"net"=>$data->price_net
	);

//## ger reserve room  ##
				
	$room_data = $base->get_reserve_rooms($uniqueKey,$lang);
	
	if(isset($room_data)){
		//var_dump($room_data->fetch_object());
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

?>