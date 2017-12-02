<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");

$service = GetParameter("service");


switch($service){
	case "minimum_days":
		$date = GetParameter("date");
		$pack_id = GetParameter("pack_id");
		$base = new Reserve_Manager();
		$data = $base->get_minimum_days($pack_id,$date)->fetch_object();
		
		if(isset($data)){
			$result = array(
				"days"=>$data->min_reserve
			);
		}
		else{
			$result = array(
				"days"=>1 //default minimum min_reserve
			);
		}
	break;
	case "": 
		
	break;
}

echo json_encode(array("result"=>$result));

?>