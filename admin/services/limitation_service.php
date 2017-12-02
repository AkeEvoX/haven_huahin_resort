<?php
session_start();
include("../../lib/common.php");
include("../managers/limitation_manager.php");

#get parameter
$type = GetParameter("type");
$result_code = "-1";
//echo "type=".$type;
switch($type){
	case "list": 
		$result =  ListItem();
	break;
	case "listobject": 
		$result =  Listobject();
	break;
	case "item": 
		$result =  GetItem();
	break;
	case "create": 
		$result = CreateItem();
	break;
	case "modify" :
		$result = ModifyItem();
	break;
	case "remove" :
		$result = DeleteItem();
	break;
}

echo json_encode(array("result"=> $result ,"code"=>$result_code));

function CreateItem(){

$base = new Limitation_Manager();

$pack_id = GetParameter("pack_id");
$start_date = GetParameter("start_date");
$end_date = GetParameter("end_date");
$min_reserve = GetParameter("min_reserve");

$result = $base->insert_item($pack_id,$start_date,$end_date,$min_reserve);

global $result_code; //call global variable
$result_code="0";

return $result;
}

function ModifyItem(){
	
$base = new Limitation_Manager();
$id = GetParameter("id");
$pack_id = GetParameter("pack_id");
$start_date = GetParameter("start_date");
$end_date = GetParameter("end_date");
$min_reserve = GetParameter("min_reserve");


$result = $base->edit_item($id,$pack_id,$start_date,$end_date,$min_reserve);
global $result_code; //call global variable
$result_code="0";
return $result;
}

function DeleteItem(){
	$base = new Limitation_Manager();
	$id = GetParameter("id");
	$base->delete_item($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function GetItem(){
	
	$id = GetParameter("id");
	$base = new Limitation_Manager();
	$dataset = $base->get_item($id);
	if($dataset){
		
		while($row = $dataset->fetch_object()){
			$result= array(
			"id"=>$row->id
			,"room_type"=>$row->room_type
			,"room_name"=>$row->room_name
			,"pack_id"=>$row->pack_id
			,"pack_name"=>$row->pack_name
			,"start_date"=>$row->startdate
			,"end_date"=>$row->enddate
			,"min_reserve"=>$row->min_reserve
			);
		}
	}

	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function Listobject(){

	$base = new Limitation_Manager();
	$dataset = $base->list_item();
	if($dataset){
		
		while($row = $dataset->fetch_object()){
			$result[] = array("id"=>$row->id,"title"=>$row->title_en);
		}
	}

	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function initial_column(){
	$column = "<thead><tr>";
	$column .= "<th class='col-md-1'>No</th>";
	$column .= "<th class='col-md-2'>Room Name</th>";
	$column .= "<th class='col-md-2'>Package Name</th>";
	$column .= "<th class='col-md-1'>StartDate</th>";
	$column .= "<th class='col-md-1'>EndDate</th>";
	$column .= "<th class='col-md-1'>Days</th>";
	$column .= "<th></th>";
	$column .= "</tr></thead><tbody>";
	return $column;
}

function ListItem(){
	
	$base = new Limitation_Manager();
	$dataset = $base->list_item();

	$result .= initial_column();

	if($dataset){
		
		while($row = $dataset->fetch_object()){
			
			$result .= "<tr>";
			$result .="<td>".$row->id."</td>";
			$result .="<td>".$row->room_name."</td>";
			$result .="<td>".$row->pack_name."</td>";
			$result .="<td>".$row->startdate."</td>";
			$result .="<td>".$row->enddate."</td>";
			$result .="<td>".$row->min_reserve."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/limitation_service.php?type=item' data-page='limitation_edit.html' data-title='Modify' onclick='page.modify(this);' ><span class='glyphicon glyphicon-pencil'></span> Edit</button> ";
			$result .="<button class='btn btn-danger' data-id='".$row->id."' data-item='services/limitation_service.php?type=item' data-page='limitation_del.html' data-title='Remove' onclick='page.remove(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
			$result .= "</tr>";
			
		}
	}
	$result .= "</tbody>";
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}


?>
