<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");
include("../managers/room_manager.php");

$_step = GetParameter("step");

switch($_step){
	case "1":
		step_one($_POST);  //quick reservation
	break;
	case "2":
		step_two($_POST);//select room
	break;
	case "3":
		step_three($_POST);//select option
	break;
	case "4":
		step_four($_POST);//summary
	break;
}

//find date available
function step_one($args){

	//detroy session
	session_destroy();
	session_start();

	$date = date('d-m-Y');
	$night = $args["nights"];
	$adults = $args["adults"];
	$children = $args["children"];
	$children_2 = $args["children_2"];
	$code = $args["code"];
	$start_date = $args["check_in_date"];//str_replace('/','-',$date);
	$end_date = date('d/m/Y',strtotime(str_replace('/','-',$start_date). "+ ". $night ."days"));
	
	//expire on 14 day before check in 
	$expire_date = date('d/m/Y',strtotime(str_replace('/','-',$start_date).'- 14days'));
	$data = array("date"=>$date
		,"night"=>$night
		,"adults"=>$adults
		,"children"=>$children // 0 - 4 year
		,"children_2"=>$children_2  //5 - 11 year
		,"code"=>$code //packate code
		,"start_date"=>$start_date 
		,"end_date"=>$end_date
		,"expire_date"=>$expire_date);
	//keep data 
	$_SESSION["info"] = $data;
	//redirect to next page
	header("Location: ../selection_room.html");
	exit();

}
//booking room & price addion option
function step_two($data){
	$item = str_replace("\\","",$data["data_reserve"]);
	$_SESSION["reserve"] = json_decode($item);
	$_SESSION["info"]["start_date"] = $data["checkpoint_date"];
	$_SESSION["info"]["end_date"] = $data["travel_date"];
	$_SESSION["info"]["night"] = $data["night_unit"];
	$_SESSION["info"]["adults"] = $data["adult_amount"];
	$_SESSION["info"]["children"] = $data["child_amount"];
	$_SESSION["info"]["children_2"] = $data["child_2_amount"];
	$_SESSION["info"]["code"] = $data["promo_code"];
	$_SESSION["info"]["expire_date"] = date('d/m/Y',strtotime(str_replace('/','-',$data["checkpoint_date"]). "- 14days")) ;

	log_warning("step 2 get reserve object >" . $item);
	/* redirect to next page */
	header("Location: ../option_reserve.html");
	
	exit();
}
//booking add option
function step_three($data){
	$item = str_replace("\\","",$data["data_reserve"]);
	$_SESSION["reserve"] = json_decode($item);
	$_SESSION["info"]["adults"] = $data["adult_amount"];
	$_SESSION["info"]["children"] = $data["child_amount"]; // 0 - 4 year
	$_SESSION["info"]["children_2"] = $data["child_2_amount"];//5 - 11 year
	$_SESSION["info"]["comment"] = $data["comment"];

	$night = $data["night"];

	$summary = $_SESSION["reserve"]->summary;
	$price_room = $summary->room;
	$price_option = $summary->option;
	
	
	//service charge 10%
	$service=($price_room*10)/100;
	//vat 7%
	$vat=(($price_room+$service)*7)/100;
	//sum price
	$sum = round($price_room,2)+ round($vat,2) + round($service,2);
	//net
	$net = $sum+round($price_option,2);

	$_SESSION["reserve"]->summary->vat=number_format($vat,2);
	$_SESSION["reserve"]->summary->service=number_format($service,2);
	$_SESSION["reserve"]->summary->sum = number_format($sum,2);
	$_SESSION["reserve"]->summary->net=number_format($net,2);
	
	var_dump($_SESSION["reserve"]->summary);
	
	header("Location: ../summary.html");
	exit();
}
//confirm trasection
function step_four($data){
	$item = str_replace("\\","",$data["data_reserve"]);
	$_SESSION["reserve"] = json_decode($item);
	//$_SESSION["reserve"] = json_decode($data["data_reserve"]);

	$info = $_SESSION["info"];
	
	//#transform date format
	$start_date = $date = str_replace('/', '-', $info["start_date"]);
	$start_date = date('Y-m-d', strtotime($start_date));
			
	$end_date = $date = str_replace('/', '-', $info["end_date"]);
	$end_date = date('Y-m-d', strtotime($end_date));
	
	$expire_date = $date = str_replace('/', '-', $info["expire_date"]);
	$expire_date = date('Y-m-d', strtotime($expire_date));
	
	$birth_date = $date = str_replace('/', '-', $data["birth_date"]);
	$birth_date = date('Y-m-d', strtotime($birth_date));
	
	
	$info["start_date"] = $start_date;
	$info["end_date"] = $end_date;
	$info["expire_date"] = $expire_date;
	
	$customer = array("email"=>$data["email"]
	,"title"=>$data["title"]
	,"fname"=>$data["fname"]
	,"lname"=>$data["lname"]
	,"birthdate"=>$birth_date
	,"prefix_mobile"=>$data["prefix_mobile"]
	,"mobile"=>$data["mobile"]
	,"comment"=>$data["comment"]
	);

	$_SESSION["customer"] = $customer;
	
	/* replace , to empty */
	$_SESSION["reserve"]->summary->sum = str_replace(",","",$_SESSION["reserve"]->summary->sum);
	$_SESSION["reserve"]->summary->net = str_replace(",","",$_SESSION["reserve"]->summary->net);
	
	/*insert to database*/	
	$base = new Reserve_Manager();
	
	$unique_key = $base->insert_reserve($info,$customer,$payment,$_SESSION["reserve"]->summary);
	
	$_SESSION["unique_key"] = $unique_key;
	$_SESSION["reserve"]->info = $info;
	/*insert rooms*/
	$pack_id = "";//package is support single package. 
	foreach($_SESSION["reserve"]->rooms as $val){
		$pack_id = $val->package;
		$base->insert_rooms($unique_key,$val->package,$val->price,$val->bed,$val->adults,$val->older_children,$val->young_children);
	}
	
	/*insert options*/
	if($_SESSION["reserve"]->options!=null){
		foreach($_SESSION["reserve"]->options as $val){
			$base->insert_options($unique_key,$val->key,$val->price,$val->desc);
		}
	}
	
	/*get package condition*/
	$base_room = new Room_Manager();
	$package_obj = $base_room->get_item_package($pack_id,'en'); //fix language 
	$package_info = $package_obj->fetch_object();
	
	/*notify mail for reserve complete.*/
	$cust_name = $customer["title"]." ".$customer["fname"]." ".$customer["lname"];
	$receive[] = array("email"=>$customer["email"],"alias"=>$cust_name);
	$sender = "contact@baankunnan.com";
	//$sender = "system@haven-huahin.com";  /* production */
	$sender_name = "system haven huahin resort";
	$subject = "Thank You Reservation";
	
	$message = file_get_contents("../templete_email_booking.html");
	$message = str_replace("{reserve_id}",$unique_key,$message);
	$message = str_replace("{start_date}",full_date_format($info["start_date"],"en"),$message);
	$message = str_replace("{end_date}",full_date_format($info["end_date"],"en"),$message);
	
	$message = str_replace("{adults}",$info["adults"],$message);
	$message = str_replace("{children_2}",$info["children_2"],$message);
	$message = str_replace("{children_1}",$info["children"],$message);
	$message = str_replace("{customer_name}",$customer["title"]." ".$customer["fname"]." ".$customer["lname"],$message);
	$message = str_replace("{customer_mobile}",$customer["prefix_mobile"].$customer["mobile"],$message);
	$message = str_replace("{customer_email}",$customer["email"],$message);
	$message = str_replace("{special_request}",$customer["comment"],$message);
	
	
	/*chack rule cancelled of room */
	if($package_info->cancel_room=="1"){
		
		$expire_date = full_date_format($info["expire_date"],"en");
		$rule_cancel = "Cancellation Policy <br><small>";
		$rule_cancel .= "This offer can be canceled or modified free of charge until ".$expire_date.", 00:00 (UTC).<br>";
		$rule_cancel .= "In case of cancellation after this date, a penalty of 100% of full stay will apply.<br>";
		$rule_cancel .= "In case of no-show, a penalty of 100% of full stay will apply.<br></small>";
		$message = str_replace("{rule_cancel_room}",$rule_cancel,$message);		
	}
	else{ // a rule cancel is balnk.
		$message = str_replace("{rule_cancel_room}",'',$message);	
	}
	
	/*show package condition*/
	$message = str_replace("{condition_detail}",get_condition($date_range,$package_info),$message);
	
	/*reserve information */
	$message = str_replace("{list_reserve}",set_email_list_reserve($_SESSION["reserve"]),$message);

	SendMail($receive,$sender,$subject,$message,$sender_name);
	
	echo "<script>window.location.href='../confirmation.html?reserve_id=".$unique_key."';</script>";
	exit();
}
//complete trasection
function step_five($data){

}

function set_email_list_reserve($reserve){

	$info = $reserve->info;
	$date_start = full_date_format($info["start_date"],"en");
	$date_end = full_date_format($info["end_date"],"en");
	$result = "<table>";
	$result .= "<tr><td colspan='3' style='font-size:11px;'>Reservation details, from ".$date_start." to ".$date_end." </td></tr>";

	$summary = $reserve->summary;
	$rooms = $reserve->rooms;
	$options = $reserve->options;
	//##room
	if(isset($rooms)){
		$room_seq=0;
		foreach($rooms as $val){
				$room_seq+=1;
				$result .= "<tr>";
				$result .= "<td class='text-top'>".$room_seq."</td>";
				$result .= "<td >Room Type : ".$val->type."<br/>Bedding options : ".$val->bed_name."<br/>Rate Plan : ".$val->room."</td>";
				$result .= "<td class='text-top' style='text-align:right'>฿ ".number_format($val->price,2)."</td>";
				$result .= "</tr>";
		}
	}

	$result .= "<tr class='table_small' ><td>&nbsp;</td><td>Service Charge </td><td class='text-right'>฿ ".number_format($summary->service,2)."</td></tr>";
	$result .= "<tr class='table_small' ><td>&nbsp;</td><td>VAT  </td><td class='text-right'>฿ ".number_format($summary->vat,2)."</td></tr>";
	$result .= "<tr><td><b>Total<b/></td><td></td><td class='text-right'>฿ ".number_format($summary->sum,2)."</td></tr>";

	//##options##
	if(isset($options)){
		$result ."<tr><td colspan='3'><hr/></td></tr>";
		foreach($options as $val){
				$result .= "<tr>";
				$result .= "<td >&nbsp;</td>";
				$result .= "<td >".$val->title."<br/>".$val->desc."</td>";
				$result .= "<td style='text-align:right'>฿ ".number_format($val->price,2)."</td>";
				$result .= "</tr>";
		}
	}

	
	
	$result .= "<tr><td>&nbsp;</td><td  class='table_small' ></td><td class='text-right'>฿ ".number_format($summary->net,2)."</td></tr>";
	$result .= "</table>";

	return $result;
}


function get_condition($date_range,$package_info){
	
	$result = "";
	if($date_range <= 14 and strpos(strtolower($package_info->title), 'internet') !== false){
		
		$result = "<b>Terms & conditions </b><br/><br/>";
		$result .= "* The above rate are include breakfast. <br/>";
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