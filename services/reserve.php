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
		step_two($_POST);
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
	//keep data 
	$_SESSION["query"] = $data;
	//redirect to next page
	header("Location: ../selection_room.html");
	exit();

}
//booking room & price addion option
function step_two($data){

	//keep data
	$_SESSION["rooms"] = json_decode($data["data_reserve"]);
	$reserve = json_decode($data["data_reserve"]);
	
	/*
	//print value room reserve
	foreach($reserve as $val){
		
		echo $val->key ."|" .$val->room ."|".$val->type ."|".$val->price ."|"."<br/>";
		
	}
	*/
	//redirect to next page
	header("Location: ../option_reserve.html");
	
	exit();
}
//booking add option
function step_tree($data){

}
//confirm trasection
function step_four($data){

}
//complete trasection
function step_five($data){

}

?>