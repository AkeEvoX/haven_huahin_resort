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
		$base = new Room_Manager();
		$data = $base->get_room_gallery($room_type);
		$result = get_gallery($data);

	break;
	case "filter":
		$startdate = GetParameter("startdate");
		$enddate = GetParameter("enddate");

		$result = call_rooms_avaliable($startdate,$enddate);

	break;
}

function get_gallery($data){

	$result = "";
	while($row = $data->fetch_object()){
		$result[] = array("id"=>$row->id,"image"=>$row->image);
	}
	return $result;
}

function call_room_avaliable($startdate,$enddate){

	$base = new Room_Manager();
	$data = $base->get_room_avaliable($startdate,$enddate);
	while($row = $data->fetch_object()){

		$result[] = array(""=>""
			,""=>"");

		/*
		room name
			->gallerys
			->beds
			->service
				->thai Set Dinner , price
				->Airport Transfer , price
			->packates
				->package_name
				->price
				->food_service on/off
				->cancel_reserve on/off
				->payment_online on/off

		*/

	}

}
	
echo json_encode(array("data"=>$result));

?>