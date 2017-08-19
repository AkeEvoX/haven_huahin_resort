<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");


$_step = GetParameter("step");

switch($_step){
	case "1":
		step_one($_POST);
	break;
	case "2":
		step_two($_POST);
	break;
	case "3":
		step_three($_POST);
	break;
	case "4":
		step_four($_POST);
	break;
	case "5":

	break;
}
//

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
	$end_date = date('d/m/Y',strtotime(str_replace('/','-',$start_date). "+ ". $night ."days")) ;
	$expire_date = date('d/m/Y',strtotime(date('d-m-Y').'+ 14days'));
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
	$_SESSION["info"]["expire_date"] = date('d/m/Y',strtotime(str_replace('/','-',$data["checkpoint_date"]). "+ 14days")) ;
	
	//summary
	//$reserve = json_decode($data["data_reserve"]); 
	
	/*
	//print value room reserve
	foreach($reserve as $val){
		
		echo $val->key ."|" .$val->room ."|".$val->type ."|".$val->price ."|"."<br/>";
		
	}
	*/
	//echo "reserve is > </br>";
	//print_r($_SESSION["reserve"]);
	//var_dump($_SESSION["reserve"]);
	//echo "<br/>---------</br>";
	//echo json_decode($item);
	log_warning("step 2 get reserve object >" . $item);
	//redirect to next page
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

	$_SESSION["reserve"]->summary->vat=$vat;
	$_SESSION["reserve"]->summary->service=$service;
	$_SESSION["reserve"]->summary->sum = $sum;
	$_SESSION["reserve"]->summary->net=$net;
	
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
	,"prefix_mobile"=>$data["prefix_mobile"]
	,"birthdate"=>$birth_date
	,"mobile"=>$data["mobile"]);

	$_SESSION["customer"] = $customer;
	
	/*insert to database*/	
	$base = new Reserve_Manager();
	//$unique_key = generateRandomString();
	$unique_key = $base->insert_reserve($info,$customer,$payment,$_SESSION["reserve"]->summary);
	
	$_SESSION["unique_key"] = $unique_key;
	$_SESSION["reserve"]->info = $info;
	/*insert rooms*/
	foreach($_SESSION["reserve"]->rooms as $val){
		$base->insert_rooms($unique_key,$val->package,$val->price,$val->bed,$val->adults,$val->older_children,$val->young_children);
	}
	
	/*insert options*/
	if($_SESSION["reserve"]->options!=null){
		foreach($_SESSION["reserve"]->options as $val){
			$base->insert_options($unique_key,$val->key,$val->price,$val->desc);
		}
	}

	/*notify mail for reserve complete.*/
	$cust_name = $customer["title"]." ".$customer["fname"]." ".$customer["lname"];
	$receive[] = array("email"=>$customer["email"],"alias"=>$cust_name);
	$sender = "contact@baankunnan.com";
	$sender_name = "system haven huahin resort";
	$subject = "Thank You Reservation";
	//$message = "Your ID is ".$unique_key;
	$message = file_get_contents("../templete_email_booking.html");
	$message = str_replace("{reserve_id}",$unique_key,$message);
	$message = str_replace("{start_date}",full_date_format($info["start_date"],"en"),$message);
	$message = str_replace("{end_date}",full_date_format($info["end_date"],"en"),$message);
	$message = str_replace("{expire_date}",full_date_format($info["expire_date"],"en"),$message);
	$message = str_replace("{adults}",$info["adults"],$message);
	$message = str_replace("{children_2}",$info["children_2"],$message);
	$message = str_replace("{children_1}",$info["children"],$message);
	$message = str_replace("{customer_name}",$customer["title"]." ".$customer["fname"]." ".$customer["lname"],$message);
	$message = str_replace("{customer_mobile}",$customer["prefix_mobile"].$customer["mobile"],$message);
	$message = str_replace("{customer_email}",$customer["email"],$message);
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
	$date_start = full_date_format($info->start_date,"en");
	$date_end = full_date_format($info->end_date,"en");
	$result = "<table>";
	$result .= "<tr><td colspan='3' style='font-size:8px;'>Reservation details, from ".$date_start." to ".$date_end." </td></tr>";

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

	$result .= "<tr class='table_small' ><td>&nbsp;</td><td>Not included: Service Charge </td><td class='text-right'>฿ ".number_format($summary->service,2)."</td></tr>";
	$result .= "<tr class='table_small' ><td>&nbsp;</td><td>Not included: VAT  </td><td class='text-right'>฿ ".number_format($summary->vat,2)."</td></tr>";
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

	
	
	$result .= "<tr><td>&nbsp;</td><td  class='table_small' >The taxes which are not included are to be paid to the hotel. The total amount is: </td><td class='text-right'>฿ ".number_format($summary->net,2)."</td></tr>";
	$result .= "</table>";

	return $result;
}
?>