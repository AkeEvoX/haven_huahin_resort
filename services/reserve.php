<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");

$key = GetParameter("key");
if(!isset($key)) return;

$lang = 'en';

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
				,"date_expire"=>$data->reserve_expire
				,"date_reserve"=>$data->create_date
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

//reserve_room_price
//reserve_options_price

}


	$room_data = $base->get_reserve_rooms($key,$lang);
	
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

	$option_data = $base->get_reserve_options($key,$lang);
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

	$reserve = array("info"=>$info
		,"rooms"=>$rooms
		,"options"=>$options
		,"customer"=>$customer
		,"payment"=>$payment
		,"summary"=>$summary
		);

echo json_encode(array("data"=>$reserve));

?>