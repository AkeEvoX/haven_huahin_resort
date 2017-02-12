<?php
Session_Start();
//include("../lib/utility.php");
include("../lib/common.php");


$_step = GetParameter("step");

switch($_step){
	case "1":
		step_one($_POST);
	break;
	case "2":

	break;
	case "3":

	break;
}
//

//find date available
function step_one($args){

	//##variable list
	//check_in_date 	date
	//nights 			dropdown
	//adults 			dropdown
	//childern 			text
	//code 				text

	$date = $args["check_in_date"];
	$night = $args["nights"];
	$adults = $args["adults"];
	$childern = $args["childern"];
	$code = $args["code"];

	$data = array("date"=>$date
		,"night"=>$night
		,"adults"=>$adults
		,"childern"=>$childern
		,"code"=>$code);

	$_SESSION["query"] = $data;

	header("Location: ../selection_room.html");
	exit();

}
//booking date & addion option
function step_two($data){

}
//confirm trasection
function step_tree($data){

}
//complete trasection
function step_four($data){

}

?>