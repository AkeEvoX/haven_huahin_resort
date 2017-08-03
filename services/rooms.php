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
		$result = call_item_package($pack_id,$lang);
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

function call_item_package($id,$lang){
	
	$base = new Room_Manager();
	$data = $base->get_item_package($id,$lang);
	$row = $data->fetch_object();
		$result = array(
			"id"=>$row->id,
			"title"=>$row->title,
			"price"=>$row->package_price,
			"food_service"=>$row->food_service,
			"cancel_room"=>$row->cancel_room,
			"payment_online"=>$row->payment_online,
			"max_person"=>$row->max_person,
			"extra_bed"=>$row->extra_bed,
			"detail"=>$row->detail,
			"conditions"=>$row->conditions);
	
	return $result;
}



function call_rooms_available($startdate,$enddate,$lang){

	$base = new Room_Manager();
	$lang='en';
	$data = $base->get_room_available($startdate,$enddate,$lang);
	$range_date =  datediff(date('Y-m-d'),$startdate);

	if($range_date <=0) $range_date=1;
	
	while($row = $data->fetch_object()){

		//check room exist
		
		$exist_room = check_duplicate_room($result[]);
		
		//if()
	
		/*
		$result[] = array(
			"id"=>$row->id,
			"title"=>$row->title,
			"unit"=>$row->unit,
			"beds"=>call_room_bed($row->id,$lang),
			"packages"=>call_room_package($row->id,$range_date,$lang),
			"gallerys"=>call_room_gallery($row->id,$lang)
		);
		*/

	}
	return $result;
}


function check_duplicate_room($source , $room_id){
	
	if(isset($source)){
		
		while($room = $source){
			
			if($room->id == $room_id){
				return true;
				break;
			}
			
		}
		return false;
	}
	
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