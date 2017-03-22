<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");

//if(isset($_SESSION["query"])){
	//$info = $_SESSION["query"];
	//$reserve = array("info"=>$_SESSION["query"],"reserve"=>$_SESSION["reserve"]);
	//"info"=> $info ,
	//echo json_encode(array("data"=>$reserve));
//}
//$_SESSION["query"] = $data;

//var_dump($_SESSION);

$key = GetParameter("key");
if(!isset($key)) return;

$base = new Reserve_Manager();
$reserve_data = $base->get_reserve_info($key);

$data = $reserve_data->fetch_object();
$info = null;
$customer=null;
$payment=null;
if(isset($data)){
	$info = array(
				"date_start"=>$data->reserve_startdate
				,"date_end"=>$data->reserve_enddate
				,"night"=>$data->night
				,"adults"=>$data->adults
				,"children"=>$data->children
				,"code"=>$data->acc_code);

	$customer = array(
				"email"=>$data->email
				,"title"=>$data->title_name
				,"fname"=>$data->first_name
				,"lname"=>$data->last_name
				,"prefix_mobile"=>$data->prefix
				,"mobile"=>$data->mobile
				);
				
	$payment = array(
				"card_type"=>$data->payment_type
				,"card_number"=>substr($data->payment_number,0,8)."xxxxxxxx"
				,"card_holder"=>$data->payment_holder
				,"card_expire"=>$data->payment_expire
				,"card_validate"=>"xxx"
				);
}
			
$room_data = $base->get_reserve_rooms($key);

if(isset($room_data)){
	while($row = $room_data->fetch_object()){
		$rooms[] = array(
						"key"=>$row->id
						,"room"=>$row->room_title
						,"type"=>$row->type_title
						,"price"=>$row->room_price
						);
	}
}

$option_data = $base->get_reserve_options($key);
if(isset($option_data)){
	while($row = $option_data->fetch_object()){
		$options[] = array(
						"key"=>$row->id
						,"title"=>$row->option_title
						,"price"=>$row->price
						);
	}
}

$reserve = array("info"=>$info
		,"rooms"=>$rooms
		,"options"=>$options
		,"customer"=>$customer
		,"payment"=>$payment);

echo json_encode(array("data"=>$reserve));

?>