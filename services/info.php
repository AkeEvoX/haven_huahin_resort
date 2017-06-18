<?php
session_start();
include("../lib/common.php");

//$date = date('d/m/Y',strtotime(date('d-mY').'+ 14days'));
//echo $date ."<br>";

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