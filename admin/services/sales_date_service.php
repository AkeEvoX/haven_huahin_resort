<?php
session_start();
include("../../lib/common.php");
include("../managers/sales_date_manager.php");

#get parameter
$type = GetParameter("type");
$result_code = "-1";

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
		
		$data = json_decode(file_get_contents("php://input"));
		
		foreach($data->deletes as $id){
			DeleteItem($id);
		}
		
		$result = "item had deleted";
		
	break;
}

echo json_encode(array("result"=> $result ,"code"=>$result_code));

function CreateItem(){

$base = new Sales_Date_Manager();
$room_price_id = GetParameter("room_price_id");
$room_unit = GetParameter("room_unit");
$status = (GetParameter("status") == "on") ? "1" : "0";
$start_date = strtotime(str_replace('/','-',GetParameter("pack_date_start")));
$end_date = strtotime(str_replace('/','-',GetParameter("pack_date_end")));

$begin = new DateTime(date("Y-m-d",$start_date));
$end = new DateTime(date("Y-m-d",$end_date));

for($i = $begin; $i <= $end; $i->modify('+1 day')){
    $pack_date = $i->format("Y-m-d");
	$result = $base->insert_item($room_price_id,$pack_date,$room_unit,$status);
}

global $result_code; //call global variable
$result_code="0";

return $result;
}

function ModifyItem(){
	
$base = new Sales_Date_Manager();
$id = GetParameter("id");
$room_price_id = GetParameter("price_id_edit");
$room_unit = GetParameter("room_unit");
$status = (GetParameter("status") == "on") ? "1" : "0";

//room_price_id,room_unit,status

$result = $base->edit_item($id,$room_price_id,$room_unit,$status);

global $result_code; //call global variable
$result_code="0";
return $result;
}

function DeleteItem($id){
	$base = new Sales_Date_Manager();
	$base->delete_item($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function Listobject(){

	$base = new Sales_Date_Manager();
	$dataset = $base->list_item();
	if($dataset){
		
		while($row = $dataset->fetch_object()){
			$result[] = array(
			"id"=>$row->id
			,"title"=>$row->title_en
			,"price"=>$row->package_price
			);
		}
	}

	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function ListItem(){
	
	$room_type = GetParameter("room_type");
	$pack_id = GetParameter("pack_id");
	$price_id = GetParameter("price_id");
	$find_start = GetParameter("find_start");
	$find_end = GetParameter("find_end");
	$base = new Sales_Date_Manager();
	$dataset = $base->list_item($room_type,$pack_id,$price_id,$find_start,$find_end);

	$result .= initial_column();

	if($dataset){
		
		/*
<div class="material-switch pull-left"><input id="status" name="status" type="checkbox"/><label for="status" class="label-success"></label></div>
		*/
		
		while($row = $dataset->fetch_object()){
			
			$item_status = $row->status == 1? '<span class="glyphicon glyphicon-ok" style="color:green;" ></span>' : '<span class="glyphicon glyphicon-remove" style="color:red;" ></span>' ;
			
			$result .= "<tr>";
			$result .="<td>".$row->id."</td>";
			$result .="<td>".$row->pack_date ."</td>";
			$result .="<td>".$row->room_unit ."</td>";
			$result .="<td>".$row->price_name ."</td>";
			$result .="<td>".$row->price ."</td>";
			$result .="<td>".$item_status ."</td>";
			$result .="<td><div class='material-switch pull-left'><input id='status_".$row->id."' data-id='".$row->id."' name='status_".$row->id."' onclick='delete_change(this)' type='checkbox'/><label for='status_".$row->id."' class='label-danger'></label></div></td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/sales_date_service.php?type=item' data-page='sale_date_edit.html' data-title='Modify' onclick='page.modify(this);'><span class='glyphicon glyphicon-pencil'></span> Edit</button> </td>";
			//$result .="<button class='btn btn-danger' data-id='".$row->id."' data-item='services/sales_date_service.php?type=item' data-page='sale_date_del.html' data-title='Remove' onclick='page.remove(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
			$result .= "</tr>";
			
		}
	}
	$result .= "</tbody>";
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function GetItem(){
	
	$base = new Sales_Date_Manager();
	$id = GetParameter("id");
	$dataset = $base->get_item($id);
	$row = $dataset->fetch_object();

	$result = array(
		"id"=>$row->id,
		"room_type_edit"=>$row->room_type,
		"room_name"=>$row->room_name,
		"pack_id_edit"=>$row->pack_id,
		"pack_name"=>$row->pack_name,
		"price_id_edit"=>$row->price_id,//field room_price_id
		"price_name"=>$row->price_name,
		"price"=>$row->price,
		"room_unit"=>$row->room_unit,
		"pack_date"=>$row->pack_date,
		"status"=>$row->status
	);
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}

function initial_column(){
	$column = "<thead><tr>";
	$column .= "<th class='col-md-1'>No</th>";
	$column .= "<th class='col-md-2'>Date</th>";
	$column .= "<th class='col-md-1'>Unit</th>";
	$column .= "<th class='col-md-2'>Price Type</th>";
	$column .= "<th class='col-md-1'>Price</th>";
	$column .= "<th class='col-md-1'>Status</th>";
	$column .= "<th class='col-md-1'>Del</th>";
	$column .= "<th class='col-md-2'></th>";
	$column .= "</tr></thead><tbody>";
	return $column;
}

?>