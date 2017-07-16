<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");


$type =  GetParameter("type");

switch($type){
	case "manual":
		
		$result = payment_manual($_POST);
		
	break;
	case "online":
	
		$result = "payment online complete.";
		
	break;
}

echo json_encode(array("result"=> $result ,"code"=>"0"));

function payment_manual($items){
	
	if($_FILES['file_evident']['name']!=""){
		
					$filename = "images/evident/". date("His") ."_".$_FILES['file_evident']['name'];
					$distination =  "../".$filename;
					$source = $_FILES['file_evident']['tmp_name'];  
					$items["payment_evident"] = $filename;
		}
		
		$manager = new reserve_manager();
		$manager->update_payment($items);
		
		upload_image($source,$distination);
		
	return "upload payment complete.";
}

?>