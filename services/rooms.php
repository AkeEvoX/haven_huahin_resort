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

function call_rooms_available($startdate,$enddate,$lange){

	$base = new Room_Manager();
	$lang='en';
	$data = $base->get_room_available($startdate,$enddate,$lang);
	while($row = $data->fetch_object()){

		$result[] = array(
			"id"=>$row->id,
			"title"=>$row->title,
			"unit"=>$row->unit,
			"beds"=>call_room_bed($row->id,$lang),
			"packages"=>call_room_package($row->id,$lang),
			"gallerys"=>call_room_gallery($row->id,$lang)
		);

	}

	return $result;

}

function call_room_package($room_id,$lang){
	$base = new Room_Manager();
	$data = $base->get_room_package($room_id,$lang);
	while($row = $data->fetch_object()){
		$result[] = array(
			"id"=>$row->id,
			"title"=>$row->title,
			"price"=>$row->package_price,
			"food_service"=>$row->food_service,
			"cancel_room"=>$row->cancel_room,
			"payment_online"=>$row->payment_online
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