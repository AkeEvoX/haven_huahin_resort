<?php
Session_start();
include("../lib/common.php");

if(isset($_SESSION["query"])){

	$info = $_SESSION["query"];
	$reserve = $_SESSION["reserve"];
	
	echo json_encode(array("info"=> $info ,"reserve"=>$reserve));
}


?>