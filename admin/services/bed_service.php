<?php
session_start();
include("../../lib/common.php");
include("../managers/bed_manager.php");

#get parameter
$type = GetParameter("type");
$result_code = "-1";
//echo "type=".$type;
switch($type){
	case "list": 
		$result =  ListItem();
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

$base = new Bed_Manager();

$title_th = GetParameter("title_th");
$title_en = GetParameter("title_en");

$result = $base->insert_bed_type($title_th,$title_en,$unit);

global $result_code; //call global variable
$result_code="0";

return $result;
}

function ModifyItem(){
	
$base = new Bed_Manager();
$id = GetParameter("id");
$title_th = GetParameter("title_th");
$title_en = GetParameter("title_en");

$result = $base->edit_bed_type($id,$title_th,$title_en,$unit);
global $result_code; //call global variable
$result_code="0";
return $result;
}

function DeleteItem(){
	$base = new Bed_Manager();
	$id = GetParameter("id");
	$base->delete_bed_type($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function ListItem(){
	
	$base = new Bed_Manager();
	$dataset = $base->list_bed_type();

	$result .= initial_column();

	if($dataset){
		
		while($row = $dataset->fetch_object()){
			
			$result .= "<tr>";
			$result .="<td>".$row->id."</td>";
			$result .="<td>".$row->title_en."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/bed_service.php?type=item' data-page='bed_edit.html' data-title='Modify' onclick='page.modify(this);' ><span class='glyphicon glyphicon-pencil'></span> Edit</button> ";
			$result .="<button class='btn btn-danger' data-id='".$row->id."' data-item='services/bed_service.php?type=item' data-page='bed_del.html' data-title='Remove' onclick='page.remove(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
			$result .= "</tr>";
			
		}
	}
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function GetItem(){
	
	$base = new Bed_Manager();
	$id = GetParameter("id");
	$dataset = $base->get_bed_type($id);
	$row = $dataset->fetch_object();
	$result = array(
		"id"=>$row->id,
		"title_th"=>$row->title_th,
		"title_en"=>$row->title_en,
	);
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}

function initial_column(){
	$column = "<tr>";
	$column .= "<th class='col-md-1'>No</th>";
	$column .= "<th class='col-md-4'>Title</th>";
	$column .= "<th></th>";
	$column .= "</tr>";
	return $column;
}

?>