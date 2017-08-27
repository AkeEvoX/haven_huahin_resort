<?php
session_start();
include("../../lib/common.php");
include("../managers/package_manager.php");

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

$base = new Package_Manager();

$title_th = GetParameter("title_th");
$title_en = GetParameter("title_en");
$room_type = GetParameter("room_type");
$package_price = GetParameter("package_price");
$food_service = (GetParameter("food_service")=="on") ? "1" : "0" ;
$cancel_room = (GetParameter("cancel_room") == "on") ? "1" : "0";
$payment_online = (GetParameter("payment_online") == "on") ? "1" : "0";
$rent_unit = GetParameter("rent_unit");

$result = $base->insert_item($title_th,$title_en,$room_type,$package_price,$food_service,$cancel_room,$payment_online,$rent_unit);

global $result_code; //call global variable
$result_code="0";

return $result;
}

function ModifyItem(){
	
$base = new Package_Manager();
$id = GetParameter("id");
$title_th = GetParameter("title_th");
$title_en = GetParameter("title_en");
$room_type = GetParameter("room_type");
$package_price = GetParameter("package_price");
$food_service = (GetParameter("food_service")=="on") ? "1" : "0" ;
$cancel_room = (GetParameter("cancel_room") == "on") ? "1" : "0";
$payment_online = (GetParameter("payment_online") == "on") ? "1" : "0";
$rent_unit = GetParameter("rent_unit");

$result = $base->edit_item($id,$title_th,$title_en,$room_type,$package_price,$food_service,$cancel_room,$payment_online,$rent_unit);
global $result_code; //call global variable
$result_code="0";
return $result;
}

function DeleteItem(){
	$base = new Package_Manager();
	$id = GetParameter("id");
	$base->delete_item($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function Listobject(){

	$base = new Package_Manager();
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
	
	$base = new Package_Manager();
	$dataset = $base->list_item();

	$result .= initial_column();

	if($dataset){
		
		while($row = $dataset->fetch_object()){
			
			$foodstatus_status = $row->food_service == 1? '<span class="glyphicon glyphicon-ok" ></span>' : '<span class="glyphicon glyphicon-remove" ></span>' ;
			$cancelroom_status = $row->cancel_room == 1? '<span class="glyphicon glyphicon-ok" ></span>' : '<span class="glyphicon glyphicon-remove" ></span>' ;
			$payment_status = $row->payment_online == 1? '<span class="glyphicon glyphicon-ok" ></span>' : '<span class="glyphicon glyphicon-remove" ></span>' ;
			$item_status = $row->status == 1? '<span class="glyphicon glyphicon-ok" style="color:green;" ></span>' : '<span class="glyphicon glyphicon-remove" style="color:red;" ></span>' ;
			
			
			$result .= "<tr>";
			$result .="<td>".$row->id."</td>";
			$result .="<td>".$row->room_name."</td>";
			$result .="<td>".$row->title_en."</td>";
			$result .="<td>".$foodstatus_status."</td>";
			$result .="<td>".$cancelroom_status."</td>";
			$result .="<td>".$payment_status."</td>";
			$result .="<td>".$item_status."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/package_service.php?type=item' data-page='package_edit.html' data-title='Modify' onclick='page.modify(this);' ><span class='glyphicon glyphicon-pencil'></span> Edit</button> ";
			$result .="<button class='btn btn-danger' data-id='".$row->id."' data-item='services/package_service.php?type=item' data-page='package_del.html' data-title='Remove' onclick='page.remove(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
			$result .= "</tr>";
			
		}
	}
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function GetItem(){
	
	$base = new Package_Manager();
	$id = GetParameter("id");
	$dataset = $base->get_item($id);
	$row = $dataset->fetch_object();

	$result = array(
		"id"=>$row->id,
		"title_th"=>$row->title_th,
		"title_en"=>$row->title_en,
		"room_type"=>$row->room_type,
		"package_price"=>$row->package_price,
		"food_service"=>$row->food_service,
		"cancel_room"=>$row->cancel_room,
		"payment_online"=>$row->payment_online,
		"status"=>$row->status
	);

	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}

function initial_column(){
	$column = "<tr>";
	$column .= "<th class='col-md-1'>No</th>";
	$column .= "<th class='col-md-1'>Room Type</th>";
	$column .= "<th class='col-md-2'>Package Name</th>";
	$column .= "<th class='col-md-1'>food<br/>service</th>";
	$column .= "<th class='col-md-1'>cancel<br>room</th>";
	$column .= "<th class='col-md-1'>pay<br>now</th>";
	$column .= "<th class='col-md-1'>Status</th>";
	$column .= "<th class='col-md-2'></th>";
	$column .= "</tr>";
	return $column;
}

?>