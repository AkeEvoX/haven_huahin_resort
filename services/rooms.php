<?php
session_start();
include("../lib/common.php");
include("../managers/room_manager.php");

//if(isset($_SESSION["query"])){
	//$info = $_SESSION["query"];
	//$reserve = array("info"=>$_SESSION["query"],"reserve"=>$_SESSION["reserve"]);
	//"info"=> $info ,
	//echo json_encode(array("data"=>$reserve));
//}
//$_SESSION["query"] = $data;

//var_dump($_SESSION);

$service = GetParameter("service");
switch($service){
	case "gallery":

		$room_type = GetParameter("room_type");
		$lang = "en";
		$result = call_room_gallery($room_type,$lang);

	break;
	case "options":
		$lang = "en";
		$result = call_room_options($lang);
	break;
	case "package":
		$lang = GetParameter('lang');
		$pack_id = GetParameter('id');
		$result = call_item_package($pack_id,null,$lang);
	break;
	case "filter":
		$startdate = GetParameter("startdate");
		$enddate = GetParameter("enddate");
		$lang = 'en';

		$result = call_rooms_available($startdate,$enddate,$lang);

	break;
}

function get_gallery($data){

	$result = "";
	while($row = $data->fetch_object()){
		$result[] = array("id"=>$row->id,"image"=>$row->image);
	}
	return $result;
}

function call_room_gallery($room_id,$lang){
	$base = new Room_Manager();
	$data = $base->get_room_gallery($room_id,$lang);
	while($row = $data->fetch_object()){
		$result[] = array(
			"id"=>$row->id,
			"image"=>$row->image
		);
	}

	return $result;
}

function call_room_options($lang){
	
	$base = new Room_Manager();
	$data = $base->get_room_options($lang);
	while($row = $data->fetch_object()){
		$result[] = array(
			"id"=>$row->id,
			"title"=>$row->title,
			"detail"=>$row->detail,
			"remark"=>$row->remark,
			"image"=>$row->image,
			"price"=>$row->price
		);
	}

	return $result;
}

function call_item_package($id,$money,$lang){
	
	$base = new Room_Manager();
	$data = $base->get_item_package($id,$lang);
	$row = $data->fetch_object();
	
	if($money==null){
		$money = $row->package_price;
	}
	
		$result = array(
			"id"=>$row->id,
			"title"=>$row->title,
			"price"=>$money,
			"food_service"=>$row->food_service,
			"cancel_room"=>$row->cancel_room,
			"payment_online"=>$row->payment_online,
			"max_person"=>$row->max_person,
			"extra_bed"=>$row->extra_bed,
			"extra_price_children"=>$row->extra_price_children,
			"extra_price_adults"=>$row->extra_price_adults,
			"detail"=>$row->detail,
			"conditions"=>$row->conditions);
	
	return $result;
}



function call_rooms_available($startdate,$enddate,$lang){

	$base = new Room_Manager();
	//$lang='en';
	$range_date =  datediff(date('Y-m-d'),$startdate);
	if($range_date <=0) $range_date=1;
	
	
	$data = $base->get_room_available($startdate,$enddate,$range_date,$lang);
	
	
	while($row = $data->fetch_object()){

		
		if(!isset($result["rooms"][$row->room_id])){
			$result["rooms"][$row->room_id] = array(
				"room_id"=>$row->room_id
				,"room_name"=>$row->room_name
				,"beds"=>call_room_bed($row->room_id,$lang)
				,"gallerys"=>call_room_gallery($row->room_id,$lang)
			);
		}
		 // exist room
		$result["rooms"][$row->room_id]["packages"][] = call_item_package($row->pack_id,$row->money,$lang);
	}
	return $result;
}

function call_room_package($room_id,$range_date,$lang){
	$base = new Room_Manager();
	$data = $base->get_room_packages($room_id,$range_date,$lang);
	while($row = $data->fetch_object()){
		$result[] = array(
			"id"=>$row->id,
			"title"=>$row->title,
			"price"=>$row->package_price,
			"food_service"=>$row->food_service,
			"cancel_room"=>$row->cancel_room,
			"payment_online"=>$row->payment_online,
			"max_person"=>$row->max_person,
			"extra_bed"=>$row->extra_bed,
			"extra_price_children"=>$row->extra_price_children,
			"extra_price_adults"=>$row->extra_price_adults,
			"detail"=>$row->detail,
			"conditions"=>$row->conditions
		);
	}

	return $result ;
}

function call_room_bed($room_id,$lang){
	$base = new Room_Manager();
	$data = $base->get_room_bed($room_id,$lang);
	while($row = $data->fetch_object()){
		$result[] = array(
			"id"=>$row->id,
			"title"=>$row->title
			);
	}

	return $result;
}
	
echo json_encode(array("data"=>$result));

?>