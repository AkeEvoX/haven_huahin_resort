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

	$_SESSION["query"] = $data;

	header("Location: ../selection_room.html");
	exit();

}
//booking room & price addion option
function step_two($data){

	$rooms = $_SESSION["rooms"];
	//print_r($data["data_reserve"]);
	//echo $data["data_reserve"];
	
	
	$item =  json_encode($data["data_reserve"]);
	
	//echo json_decode($item);
	$out = array_values($data);
	
	print_r($out);
	var_dump(json_decode(json_encode($data),true));
	// foreach($item as $val){
		// var_dump($val);
	// }
	
	// foreach($data["data_reserve"]  as $val){
		// print_r($val);
	// }
	
	// foreach($data->data_reserve as $value){
		// echo $value->key;
	// }
	
	// foreach($data["data_reserve"] as $row){
		// print_r($row);
	// }
	
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