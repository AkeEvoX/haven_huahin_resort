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

$base = new Reserve_Manager();
$reserve_data = $base->get_reserve_info($key);

$data = $reserve_data->fetch_object();

$info = array(
			"date_start"=>$data->date_start
			,"date_end"=>$data->data_end
			,"night"=>$data->night
			,"adults"=>$data->adults
			,"children"=>$data->children
			,"code"=>$data->code);

$customer = array(
			"email"=>$data->email
			,"title"=>$data->title
			,"fname"=>$data->first_name
			,"lname"=>$data->last_name
			,"prefix_mobile"=>$data->prefix
			,"mobile"=>$data->mobile
			);
			
$payment = array(
			"card_type"=>$data->payment_type
			,"card_number"=>$data->payment_number
			,"card_holder"=>$data->payment_holder
			,"card_expire"=>$data->payment_expire
			,"card_validate"=>$data->payment_secure
			);
$room_data = $base->get_reserve_rooms($key);
while($row = $room_data->fetch_object()){
	$rooms[] = array(
					"key"=>$row->key
					,"room"=>$row->room
					,"type"=>$row->type
					,"price"=>$row->price
					);
}

$option_data = $base->get_reserve_options($key);
while($row = $option_data->fetch_object()){
	$options[] = array(
					"key"=>$row->key
					,"title"=>$row->title
					,"price"=>$row->price
					);
}

$reserve = array("info"=>$info
		,"rooms"=>$rooms
		,"options"=>$options
		,"customer"=>$customer
		,"payment"=>$payment);

echo json_encode(array("data"=>$reserve));

?>