<?php
Session_start();
include("../lib/common.php");

if(isset($_SESSION["query"])){

	$info = $_SESSION["query"];
	
	
	
	
	echo json_encode(array("result"=> $info ,"code"=>"0"));
}


?>