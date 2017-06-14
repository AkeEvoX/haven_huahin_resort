<?php
session_start();
include("../lib/common.php");

//if(isset($_SESSION["query"])){
	//$info = $_SESSION["query"];
	//$reserve = array("info"=>$_SESSION["query"],"reserve"=>$_SESSION["reserve"]);
	//"info"=> $info ,
	//echo json_encode(array("data"=>$reserve));
//}

//echo date('Y-m-d', strtotime('03-06-2017'))."<br>";

if(isset($_SESSION["reserve"])){
	
	//echo "found reserve.<br/>";
	$data = array(
		"rooms"=>$_SESSION["reserve"]->rooms
		,"options"=>$_SESSION["reserve"]->options
		,"summary"=>$_SESSION["reserve"]->summary
	);
	
	//var_dump($_SESSION["reserve"]->rooms);
	//echo "<br>";
}
	
$reserve = array("reserve"=>$data
		,"customer"=>$_SESSION["customer"]
		,"payment"=>$_SESSION["payment"]);

echo json_encode(array("data"=>$reserve,"info"=>$_SESSION["info"]));

?>