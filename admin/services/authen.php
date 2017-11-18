<?php
session_start();
include("../../lib/common.php");
include("../managers/user_manager.php");

$user = GetParameter("inputUser");
$pass = GetParameter("inputPassword");

$pass = md5(md5($pass));

$base = new User_Manager();
$dataset = $base->authen($user,$pass);
$row = $dataset->fetch_object();
	

if($row->id!=""){
	
	$result = array(
		"id"=>$row->id,
		"firstname"=>$row->firstname,
		"lastname"=>$row->lastname,
		"role"=>$row->role_id,
	);
	//keep user profile.
	$_SESSION["user"] = $result;
	$result_code = "0";
}else{
	
	$result = "Sorry !! .username / password is invalid.";
	$result_code = "-1";
}


	


echo json_encode(array("result"=> $result ,"code"=>$result_code));

?>