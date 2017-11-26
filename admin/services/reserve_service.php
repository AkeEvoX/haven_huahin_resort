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
	case "list_option": 
		$result =  ListOption();
	break;
		case "get_payment": 
		$result =  GetPayment();
	break;
	case "list_payment": 
		$result =  ListPayment();
	break;
	case "list_booking": 
		$result =  ListBooking();
	break;
	case "list_room": 
		$result =  GetRoom();
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

function ListOption(){
	
	$unique = GetParameter("unique");
	$base = new Reserve_Manager();
	$dataset = $base->get_options($unique); 
	
	
	$result .= initial_column_option();
	if($dataset->num_rows===0){
		$result .= "<tr><td class='text-center' colspan='2'>ไม่พบข้อมูล</td></tr>";
	}
	else{
		while($row = $dataset->fetch_object()){
			$result .= "<tr>";
			$result .="<td>".$row->option_name."</td>";
			$result .="<td>".$row->price."</td>";
			$result .= "</tr>";
		}
	}
	
	$result .= "</tbody>";
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
	
	
}

function GetPayment(){
	
	$unique = GetParameter("unique");
	$base = new Reserve_Manager();
	$dataset = $base->get_payment($unique); 
	
	$result = $dataset->fetch_object();
	return $result;
	
}


function ListPayment(){
	
	$base = new Reserve_Manager();
	$status= "1";
	$dataset = $base->list_item($unique,$customer,$mobile,$find_start,$find_end,$status); 
	$result .= initial_column();
	
	if($dataset->num_rows===0){
		$result .= "<tr><td class='text-center' colspan='8'>ไม่พบข้อมูล</td></tr>";
	}
	else{
		
		while($row = $dataset->fetch_object()){
			
			$result .= "<tr>";
			$result .="<td>".$row->unique_key."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->start_date)) ."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->end_date)) ."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->register_date)) ."</td>";
			$result .="<td>".$row->cust_name ."</td>";
			$result .="<td>".$row->cust_mobile ."</td>";
			$result .="<td>".$row->status_name ."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/reserve_service.php?type=item' data-page='reserve_view.html' data-title='Reserve View' onclick='page.modify(this);'><span class='glyphicon glyphicon-pencil'></span> VIew</button> </td>";
			$result .= "</tr>";
			
		}
	}
	$result .= "</tbody>";
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function ListBooking(){
	$base = new Reserve_Manager();
	$status= "0";
	$dataset = $base->list_item($unique,$customer,$mobile,$find_start,$find_end,$status); 
	$result .= initial_column();
	
	if($dataset->num_rows===0){
		$result .= "<tr><td class='text-center' colspan='8'>ไม่พบข้อมูล</td></tr>";
	}
	else{
		
		while($row = $dataset->fetch_object()){
			
			$result .= "<tr>";
			$result .="<td>".$row->unique_key."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->start_date)) ."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->end_date)) ."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->register_date)) ."</td>";
			$result .="<td>".$row->cust_name ."</td>";
			$result .="<td>".$row->cust_mobile ."</td>";
			$result .="<td>".$row->status_name ."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/reserve_service.php?type=item' data-page='reserve_view.html' data-title='Reserve View' onclick='page.modify(this);'><span class='glyphicon glyphicon-pencil'></span> VIew</button> </td>";
			$result .= "</tr>";
			
		}
	}
	$result .= "</tbody>";
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
	$status = GetParameter("status");
	$base = new Reserve_Manager();
	$dataset = $base->list_item($unique,$customer,$mobile,$find_start,$find_end,$status); 

	$result .= initial_column();

	if($dataset->num_rows===0){
		$result .= "<tr><td class='text-center' colspan='8'>ไม่พบข้อมูล</td></tr>";
	}
	else{
		
		while($row = $dataset->fetch_object()){
			
			//$item_status = $row->status == 1? '<span class="glyphicon glyphicon-ok" style="color:green;" ></span>' : '<span class="glyphicon glyphicon-remove" style="color:red;" ></span>' ;
			
			$result .= "<tr>";
			$result .="<td>".$row->unique_key."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->start_date)) ."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->end_date)) ."</td>";
			$result .="<td>".date('d/m/Y',strtotime($row->register_date)) ."</td>";
			$result .="<td>".$row->cust_name ."</td>";
			$result .="<td>".$row->cust_mobile ."</td>";
			$result .="<td>".$row->status_name ."</td>";
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
	
	$room_data = $base->get_room($row->unique_key);
	
	$room_obj = $room_data->fetch_object();
	
	
	//default image not found.
	$payment_evident ='../images/common/unavaliable.jpg'; 
	
	
	if($row->payment_evident != "")
		$payment_evident  = '../'.$row->payment_evident;
		
	

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
		"remark"=>$row->reserve_comment,
		"email"=>$row->email,
		"payment_type"=>$row->payment_type,
		"payment_bank"=>$row->payment_bank,
		"payment_holder"=>$row->payment_holder,
		"payment_date"=>$row->payment_date,
		"payment_amount"=>$row->payment_amount,
		"payment_evident"=>$payment_evident,
		"payment_remark"=>$row->payment_remark,
		"room_name"=>$room_obj->room_name,
		"pack_name"=>$room_obj->pack_name,
		"bed_name"=>$room_obj->bed_name,
		"status"=>$row->status
	);
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}

function GetRoom(){
	
	$id = GetParameter("unique");
	$base = new Reserve_Manager();
	$room_data = $base->get_room($id);
	$room_obj = $room_data->fetch_object();
	
	$result = array(
	"room_name"=>$room_obj->room_name,
	"pack_name"=>$room_obj->pack_name,
	"bed_name"=>$room_obj->bed_name
	);
	
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
	
}

function initial_column_option(){
	$column = "<thead><tr>";
	$column .= "<th class='col-md-2'>Name</th>";
	$column .= "<th class='col-md-2'>Price</th>";
	$column .= "</tr></thead><tbody>";
	return $column;
	
}

function initial_column(){
	$column = "<thead><tr>";
	$column .= "<th class='col-md-2'>Booking no.</th>";
	$column .= "<th class='col-md-2'>Check in</th>";
	$column .= "<th class='col-md-2'>Check out</th>";
	$column .= "<th class='col-md-2'>Booking Date</th>";
	$column .= "<th class='col-md-2'>Customer</th>";
	$column .= "<th class='col-md-1'>Mobile</th>";
	$column .= "<th class='col-md-1'>Status</th>";
	$column .= "<th class='col-md-1'></th>";
	$column .= "</tr></thead><tbody>";
	return $column;
}

?>