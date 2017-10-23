<?php
session_start();
include("../../lib/common.php");
include("../managers/package_price_manager.php");

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

$base = new Package_Price_Manager();
$pack_id = GetParameter("pack_id");
$price_id = GetParameter("price_id");
$price = GetParameter("price");
$status = (GetParameter("status") == "on") ? "1" : "0";

$result = $base->insert_item($pack_id,$price_id,$price,$status);

global $result_code; //call global variable
$result_code="0";

return $result;
}

function ModifyItem(){
	
$base = new Package_Price_Manager();
$id = GetParameter("id");
$pack_id = GetParameter("pack_id");
$price_id = GetParameter("price_id");
$price = GetParameter("price");
$status = (GetParameter("status") == "on") ? "1" : "0";

$result = $base->edit_item($id,$pack_id,$price_id,$price,$status);

global $result_code; //call global variable
$result_code="0";
return $result;
}

function DeleteItem(){
	$base = new Package_Price_Manager();
	$id = GetParameter("id");
	$base->delete_item($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function Listobject(){

	$base = new Package_Price_Manager();
	$dataset = $base->list_item();
	if($dataset){
		
		while($row = $dataset->fetch_object()){
			$result[] = array("id"=>$row->id,"title"=>$row->title_en,"price"=>$row->package_price);
		}
	}

	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function ListItem(){
	
	$base = new Package_Price_Manager();
	$dataset = $base->list_item();

	$result .= initial_column();

	if($dataset){
		
		while($row = $dataset->fetch_object()){
			
			$item_status = $row->status == 1? '<span class="glyphicon glyphicon-ok" style="color:green;" ></span>' : '<span class="glyphicon glyphicon-remove" style="color:red;" ></span>' ;
			
			$result .= "<tr>";
			$result .="<td>".$row->id."</td>";
			$result .="<td>".$row->room_type."</td>";
			$result .="<td>".$row->pack_name."</td>";
			$result .="<td>".$row->price_type."</td>";
			$result .="<td>".$row->price."</td>";
			$result .="<td>".$item_status."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/package_price_service.php?type=item' data-page='package_price_edit.html' data-title='Modify' onclick='page.modify(this);' ><span class='glyphicon glyphicon-pencil'></span> Edit</button> ";
			$result .="<button class='btn btn-danger' data-id='".$row->id."' data-item='services/package_price_service.php?type=item' data-page='package_price_del.html' data-title='Remove' onclick='page.remove(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
			$result .= "</tr>";
			
		}
	}
	$result .= "</tbody>";
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function GetItem(){
	
	$base = new Package_Price_Manager();
	$id = GetParameter("id");
	$dataset = $base->get_item($id);
	$row = $dataset->fetch_object();

	$result = array(
		"id"=>$row->id,
		"room_type"=>$row->room_type,
		"room_name"=>$row->room_name,
		"pack_id"=>$row->pack_id,
		"pack_name"=>$row->pack_name,
		"price_id"=>$row->price_id,
		"price_name"=>$row->price_name,
		"price"=>$row->price,
		"status"=>$row->status
	);
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}

function initial_column(){
	$column = "<thead><tr>";
	$column .= "<th class='col-md-1'>No</th>";
	$column .= "<th class='col-md-2'>Room Type</th>";
	$column .= "<th class='col-md-2'>Package Name</th>";
	$column .= "<th class='col-md-2'>Price Type</th>";
	$column .= "<th class='col-md-1'>Price</th>";
	$column .= "<th class='col-md-1'>Status</th>";
	$column .= "<th class='col-md-2'></th>";
	$column .= "</tr></thead><tbody>";
	return $column;
}

?>