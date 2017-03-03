<?php
session_start();
include("../lib/logger.php");
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
	//##variable list
	//check_in_date 	date
	//nights 			dropdown
	//adults 			dropdown
	//childern 			text
	//code 				text

	$date = $args["check_in_date"];
	$night = $args["nights"];
	$adults = $args["adults"];
	$children = $args["children"];
	$code = $args["code"];

	$data = array("date"=>$date
		,"night"=>$night
		,"adults"=>$adults
		,"children"=>$children
		,"code"=>$code);
	//keep data 
	$_SESSION["info"] = $data;	
	//redirect to next page
	header("Location: ../selection_room.html");
	exit();

}
//booking room & price addion option
function step_two($data){

	//keep data
	//$_SESSION["reserve"] = "";
	$_SESSION["reserve"] = json_decode($data["data_reserve"]);
	$reserve = json_decode($data["data_reserve"]);
	//var_dump($reserve);
	/*
	//print value room reserve
	foreach($reserve as $val){
		
		echo $val->key ."|" .$val->room ."|".$val->type ."|".$val->price ."|"."<br/>";
		
	}
	*/
	//redirect to next page
	header("Location: ../option_reserve.html");
	
	exit();
}
//booking add option
function step_three($data){
	
	$_SESSION["reserve"] = json_decode($data["data_reserve"]);
	
	header("Location: ../summery.html");
	exit();
}
//confirm trasection
function step_four($data){
	
	$_SESSION["reserve"] = json_decode($data["data_reserve"]);

	
	$customer	= array("email"=>$data["email"]
	,"title"=>$data["title"]
	,"fname"=>$data["fname"]
	,"lname"=>$data["lname"]
	,"prefix_mobile"=>$data["prefix_mobile"]
	,"mobile"=>$data["mobile"]);

	$_SESSION["customer"] = $customer;
		
	$payment = array("card_type"=>$data["card_type"]
		,"card_number"=>$data["card_number"]
		,"card_holder"=>$data["card_holder"]
		,"card_expire_month"=>$data["card_expire_month"]
		,"card_expire_year"=>$data["card_expire_year"]
		,"card_validate"=>$data["card_validate"]);
		
	$_SESSION["payment"] = $payment;

	/*insert to database*/
	
	$base = new Reserve_Manager();
	//$unique_key = generateRandomString();
	$unique_key = $base->insert_reserve($_SESSION["reserve"]->info,$customer,$payment);
	
	/*insert rooms*/
	foreach($_SESSION["reserve"]->rooms as $val){
		$base->insert_rooms($unique_key,$val->key,$val->price);
	}
	
	/*insert options*/
	foreach($_SESSION["reserve"]->options as $val){
		$base->insert_options($unique_key,$val->key,$val->price);
	}
	
	//echo "insert complete.";
	header("Location: ../confirmation.html");
	//exit();
}
//complete trasection
function step_five($data){

}

?>