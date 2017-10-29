<?php
session_start();
include("../../lib/common.php");
include("../managers/reserve_manager.php");

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
}

echo json_encode(array("result"=> $result ,"code"=>$result_code));

function ModifyItem(){
	
$base = new Reserve_Manager();
$id = GetParameter("id");
$room_price_id = GetParameter("price_type");
$room_unit = GetParameter("room_unit");
$status = (GetParameter("status") == "on") ? "1" : "0";

//room_price_id,room_unit,status

$result = $base->edit_item($id,$room_price_id,$room_unit,$status);

global $result_code; //call global variable
$result_code="0";
return $result;
}

function Listobject(){

	$base = new Reserve_Manager();
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

	$unique = GetParameter("unique");
	$customer = GetParameter("customer");
	$mobile = GetParameter("mobile");
	$find_start = GetParameter("find_start");
	$find_end = GetParameter("find_end");
	$base = new Reserve_Manager();
	$dataset = $base->list_item($unique,$customer,$mobile,$find_start,$find_end); 

	$result .= initial_column();

	if($dataset){
		
		while($row = $dataset->fetch_object()){
			
			switch($row->status) {
				case "0": //booking
					$item_status =  "booking";
				break;
				case "1"://payment
					$item_status =  "payment";
				break;
				case "2"://cancel
					$item_status =  "cancel";
				break;
			}
			//$item_status = $row->status == 1? '<span class="glyphicon glyphicon-ok" style="color:green;" ></span>' : '<span class="glyphicon glyphicon-remove" style="color:red;" ></span>' ;
			
			$result .= "<tr>";
			$result .="<td>".$row->unique_key."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->start_date)) ."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->end_date)) ."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->register_date)) ."</td>";
			$result .="<td>".$row->cust_name ."</td>";
			$result .="<td>".$row->cust_mobile ."</td>";
			$result .="<td>".$item_status ."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/reserve_service.php?type=item' data-page='reserve_view.html' data-title='Reserve View' onclick='page.modify(this);'><span class='glyphicon glyphicon-pencil'></span> VIew</button> </td>";
			$result .= "</tr>";
			
		}
	}
	$result .= "</tbody>";
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function GetItem(){
	
	$base = new Reserve_Manager();
	$id = GetParameter("id");
	$dataset = $base->get_item($id);
	$row = $dataset->fetch_object();

	$result = array(
		"id"=>$row->id,
		"unique_key"=>$row->unique_key,
		"start_date"=>$row->start_date,
		"end_date"=>$row->end_date,
		"register_date"=>$row->register_date,
		"price_option"=>$row->price_option,
		"price_sum"=>$row->price_sum,
		"price_service"=>$row->price_service,
		"price_vat"=>$row->price_vat,
		"price_net"=>$row->price_net,
		"acc_code"=>$row->acc_code,
		"night"=>$row->night,
		"adults"=>$row->adults,
		"children"=>$row->children,
		"children_2"=>$row->children_2,
		"cust_name"=>$row->cust_name,
		"cust_mobile"=>$row->cust_mobile,		
		"birthdate"=>$row->birthdate,
		"email"=>$row->email,
		"status"=>$row->status
	);
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}

function initial_column(){
	$column = "<thead><tr>";
	$column .= "<th class='col-md-2'>Unique</th>";
	$column .= "<th class='col-md-2'>StartDate</th>";
	$column .= "<th class='col-md-2'>EndDate</th>";
	$column .= "<th class='col-md-2'>Register</th>";
	$column .= "<th class='col-md-2'>Customer</th>";
	$column .= "<th class='col-md-1'>Mobile</th>";
	$column .= "<th class='col-md-1'>Status</th>";
	$column .= "<th class='col-md-1'></th>";
	$column .= "</tr></thead><tbody>";
	return $column;
}

?>