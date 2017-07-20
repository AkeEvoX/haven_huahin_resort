<?php
session_start();
include("../../lib/common.php");
include("../managers/option_manager.php");

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

$base = new Option_Manager();

$title_th = GetParameter("title_th");
$title_en = GetParameter("title_en");
$price = GetParameter("price");
$detail_th = GetParameter("detail_th");
$detail_en = GetParameter("detail_en");
$remark_th = GetParameter("remark_th");
$remark_en = GetParameter("remark_en");

//upload image
if($_FILES['file_image']['name']!=""){
		$filename = "images/".date('Ymd_His')."_".$_FILES['file_image']['name'];
		$distination =  "../../".$filename;
		$source = $_FILES['file_image']['tmp_name'];  
		$image = $filename;
		upload_image($source,$distination);
}

$result = $base->insert_option($title_th,$title_en,$price,$detail_th,$detail_en,$image,$remark_th,$remark_en);




global $result_code; //call global variable
$result_code="0";

return $result;
}

function ModifyItem(){
	
$base = new Option_Manager();
$id = GetParameter("id");
$title_th = GetParameter("title_th");
$title_en = GetParameter("title_en");
$price = GetParameter("price");
$detail_th = GetParameter("detail_th");
$detail_en = GetParameter("detail_en");
$remark_th = GetParameter("remark_th");
$remark_en = GetParameter("remark_en");

//upload image
if($_FILES['file_image']['name']!=""){
		$filename = "images/".date('Ymd_His')."_".$_FILES['file_image']['name'];
		$distination =  "../../".$filename;
		$source = $_FILES['file_image']['tmp_name'];  
		$image = $filename;
		upload_image($source,$distination);
}

$result = $base->edit_option($id,$title_th,$title_en,$price,$detail_th,$detail_en,$image,$remark_th,$remark_en);
global $result_code; //call global variable
$result_code="0";
return $result;
}

function DeleteItem(){
	$base = new Option_Manager();
	$id = GetParameter("id");
	$base->delete_option($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function ListItem(){
	
	$base = new Option_Manager();
	$dataset = $base->list_option();

	$result .= initial_column();

	if($dataset){
		
		while($row = $dataset->fetch_object()){
			
			$result .= "<tr>";
			$result .="<td>".$row->id."</td>";
			$result .="<td>".$row->title_en."</td>";
			$result .="<td>".$row->price."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/option_service.php?type=item' data-page='option_edit.html' data-title='Modify' onclick='page.modify(this);' ><span class='glyphicon glyphicon-pencil'></span> Edit</button> ";
			$result .="<button class='btn btn-danger' data-id='".$row->id."' data-item='services/option_service.php?type=item' data-page='option_del.html' data-title='Remove' onclick='page.remove(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
			$result .= "</tr>";
			
		}
	}
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function GetItem(){
	
	
	
	$base = new Option_Manager();
	$id = GetParameter("id");
	//echo "id=".$id."<br>";
	$dataset = $base->get_option($id);
	$row = $dataset->fetch_object();
	//var_dump($row);
	$result = array(
		"id"=>$row->id,
		"title_th"=>$row->title_th,
		"title_en"=>$row->title_en,
		"price"=>$row->price,
		"detail_th"=>$row->detail_th,
		"detail_en"=>$row->detail_en,
		"image"=>$row->image,
		"remark_th"=>$row->remark_th,
		"remark_en"=>$row->remark_en
	);
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}

function initial_column(){
	$column = "<tr>";
	$column .= "<th class='col-md-1'>No</th>";
	$column .= "<th class='col-md-4'>Title</th>";
	$column .= "<th class='col-md-2'>Price</th>";
	$column .= "<th></th>";
	$column .= "</tr>";
	return $column;
}

?>