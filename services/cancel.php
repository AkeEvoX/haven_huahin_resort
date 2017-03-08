<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");


$key = GetParameter("reserve_number");
$email = GetParameter("email");
if(!isset($key)) return;

$base = new Reserve_Manager();

$result_verify = $base->verify_cancel($key,$email);

$verify = $result_verify->fetch_object();

if($verify->found=="0"){
	echo "<script>alert('Sorry !!! cannot cancel reservation. \\n information not match.');window.location='../cancel_confirm.html';</script>";
	exit();
}
else{

	$result = $base->cancel_reserve($key,$email);	
	echo "<script>alert('Cancel reserve success. \\n Thank you.');window.location='../cancellation.html?reserve_id=".$key."';</script>";
	//header("Location: ../cancellation.html?reserve_id=".$key);
	exit();
}

?>