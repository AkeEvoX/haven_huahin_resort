<?php
session_start();
include("../../lib/common.php");
include("../managers/room_manager.php");

#get parameter

$base = new Room_Manager();


$title_th = $_POST["title_th"];
$title_en = $_POST["title_en"];
$unit = $_POST["unit"];

$result = $base->insert_room_type($title_th,$title_en,$unit);

echo json_encode(array("result"=> $result ,"code"=>"0"));


?>