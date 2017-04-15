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
	case "":

	break;
}

function get_gallery($data){

	$result = "";
	while($row = $data->fetch_object()){
		$result[] = array("id"=>$row->id,"image"=>$row->image);
	}
	return $result;
}
	
echo json_encode(array("data"=>$result));

?>