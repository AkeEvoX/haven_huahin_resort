<?php
session_start();
include("../../lib/common.php");
include("../managers/room_manager.php");

#get parameter

$base = new Room_Manager();
$dataset = $base->list_room_type();

$result .= initial_column();
$result_code = "-1";

if($dataset){
	
	while($row = $dataset->fetch_object()){
		
		$result .= "<tr>";
		$result .="<td>".$row->id."</td>";
		$result .="<td>".$row->title_en."</td>";
		$result .="<td>".$row->unit."</td>";
		$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-source='services/room_service.php?type=edit' onclick='page.edit(this);' ><span class='glyphicon glyphicon-pencil'></span> Edit</button> ";
		$result .="<button class='btn btn-danger' data-id='".$row->id."' data-source='services/room_service.php?type=del' onclick='page.delete(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
		$result .= "</tr>";
		
	}
	
	$result_code = "0";

}

echo json_encode(array("result"=> $result ,"code"=>$result_code));

function initial_column(){
	$column = "<tr>";
	$column .= "<th class='col-md-1'>No</th>";
	$column .= "<th class='col-md-4'>Title</th>";
	$column .= "<th class='col-md-1'>Unit</th>";
	$column .= "<th></th>";
	$column .= "</tr>";
	return $column;
}
?>