<?php
session_start();
include("../../lib/common.php");
include("../managers/user_manager.php");

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
	case "list_role": 
		$result =  ListRole();
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

$base = new User_Manager();

$firstname = GetParameter("firstname");
$lastname = GetParameter("lastname");
$username = GetParameter("username");
$password = GetParameter("password");
$role_id = GetParameter("role_id");
$status = (GetParameter("status")=="on") ? "1" : "0" ;

$result = $base->insert_item($firstname,$lastname,$username,$password,$role_id,$status);

global $result_code; //call global variable
$result_code="0";

return $result;
}

function ModifyItem(){
	
	$base = new User_Manager();
	$id = GetParameter("id");
	$firstname = GetParameter("firstname");
	$lastname = GetParameter("lastname");
	$username = GetParameter("username");
	
	if(GetParameter("password")!="")
		$password = md5(md5(GetParameter("password")));
	
	$role_id = GetParameter("role_id");
	$status = (GetParameter("status")=="on") ? "1" : "0" ;


	$result = $base->edit_item($id,$firstname,$lastname,$username,$password,$role_id,$status);
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function DeleteItem(){
	$base = new User_Manager();
	$id = GetParameter("id");
	$base->delete_item($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function ListRole(){

	$base = new User_Manager();
	$dataset = $base->list_role();
	if($dataset){
		
		while($row = $dataset->fetch_object()){
			$result[] = array(
			"role_id"=>$row->role_id
			,"role_name"=>$row->role_name
			);
		}
	}

	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}


function Listobject(){

	$base = new User_Manager();
	$dataset = $base->list_item();
	if($dataset){
		
		while($row = $dataset->fetch_object()){
			$result[] = array(
			"id"=>$row->id
			,"firstname"=>$row->firstname
			,"lastname"=>$row->lastname
			,"role_name"=>$row->role_name
			,"status"=>$row->status
			);
		}
	}

	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function ListItem(){
	
	$base = new User_Manager();
	$dataset = $base->list_item();

	$result .= initial_column();

	if($dataset){
		
		while($row = $dataset->fetch_object()){
			
			$item_status = $row->status == 1? '<span class="glyphicon glyphicon-ok" style="color:green;" ></span>' : '<span class="glyphicon glyphicon-remove" style="color:red;" ></span>' ;
			$item_role = $row->role==1 ? "Admin" : "User";
			
			$result .= "<tr>";
			$result .="<td>".$row->id."</td>";
			$result .="<td>".$row->firstname."</td>";
			$result .="<td>".$row->lastname."</td>";
			$result .="<td>".$row->role_name."</td>";
			$result .="<td>".$item_status."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/user_service.php?type=item' data-page='user_edit.html' data-title='Modify' onclick='page.modify(this);' ><span class='glyphicon glyphicon-pencil'></span> Edit</button> ";
			$result .="<button class='btn btn-danger' data-id='".$row->id."' data-item='services/user_service.php?type=item' data-page='user_del.html' data-title='Remove' onclick='page.remove(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
			$result .= "</tr>";
			
		}
	}
	$result .= "</tbody>";
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function GetItem(){
	
	$base = new User_Manager();
	$id = GetParameter("id");
	$dataset = $base->get_item($id);
	$row = $dataset->fetch_object();

	$result = array(
		"id"=>$row->id,
		"firstname"=>$row->firstname,
		"lastname"=>$row->lastname,
		"username"=>$row->user_name,
		"role_id"=>$row->role_id,
		"status"=>$row->status
	);
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}

function initial_column(){
	$column = "<thead><tr>";
	$column .= "<th class='col-md-1'>No</th>";
	$column .= "<th class='col-md-1'>First Name</th>";
	$column .= "<th class='col-md-1'>Last Name</th>";
	$column .= "<th class='col-md-1'>Role Name</th>";
	$column .= "<th class='col-md-1'>Status</th>";
	$column .= "<th class='col-md-2'></th>";
	$column .= "</tr></thead><tbody>";
	return $column;
}

?>