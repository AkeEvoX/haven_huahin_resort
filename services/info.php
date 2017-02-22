<?php
Session_start();
include("../lib/common.php");

if(isset($_SESSION["query"])){

	//$info = $_SESSION["query"];
	$reserve = array("info"=>$_SESSION["query"],"reserve"=>$_SESSION["reserve"]);
	//"info"=> $info ,
	echo json_encode(array("data"=>$reserve));
}


?>