<?php
session_start();
include("../lib/logger.php");
include("../lib/common.php");

	$receive = array("svargalok@gmail.com"=>"customer");
	//$receive[] = array("email"=>"neosvargalok@hotmail.com.com","alias"=>"support");
	//$receive="svargalok@gmail.com";
	$sender = "contact@baankunnan.com";
	$sender_name = "system haven huahin resort";
	$subject = "Thank You Reservation(Testing)";
	$message = "Your ID is 9iJ09e01909iOSXWwd7w81";
	SendMail($receive,$sender,$subject,$message,$sender_name);

?>