<?php
Session_start();
include("../lib/common.php");

if(isset($_SESSION["query"])){

	$info = $_SESSION["query"];
	$rooms = $_SESSION["rooms"];
	echo json_encode(array("info"=> $info ,"rooms"=>$rooms));
}


?>