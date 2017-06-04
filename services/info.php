<?php
session_start();
include("../lib/common.php");

//if(isset($_SESSION["query"])){
	//$info = $_SESSION["query"];
	//$reserve = array("info"=>$_SESSION["query"],"reserve"=>$_SESSION["reserve"]);
	//"info"=> $info ,
	//echo json_encode(array("data"=>$reserve));
//}

//$_SESSION["query"] = $data;
//"info"=>$_SESSION["info"]


//var_dump($_SESSION["reserve"]);

//echo print_r($_SESSION["reserve"]->reserve->rooms);
//echo "<p>";


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