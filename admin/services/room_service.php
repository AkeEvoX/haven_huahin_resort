<?php
session_start();
include("../../lib/common.php");
include("../managers/room_manager.php");

#get parameter
$type = GetParameter("type");
$result_code = "-1";
//echo "type=".$type;
switch($type){
	case "list": 
		$result =  ListRoom();
	break;
	case "listobject": 
		$result =  Listobject();
	break;
	case "item": 
		$result =  GetRoomType();
	break;
	case "create": 
		$result = CreateRoom();
	break;
	case "modify" :
		$result = ModifyRoom();
	break;
	case "remove" :
		$result = DeleteRoom();
	break;
		case "upload_gallery" :
		$result = call_upload_gallery($_POST);
	break;
	case "list_gallery" :
		$id = GetParameter("id");
		$result = ListGallery($id);
	break;
	case "del_gallery":
		$id = GetParameter("id");
		$result = Delete_Gallery($id);
	break;
}

echo json_encode(array("result"=> $result ,"code"=>$result_code));

function CreateRoom(){

$base = new Room_Manager();

$title_th = GetParameter("title_th");
$title_en = GetParameter("title_en");
$seq = GetParameter("seq");

$result = $base->insert_room_type($title_th,$title_en,$seq);

global $result_code; //call global variable
$result_code="0";

return $result;
}

function ModifyRoom(){
	
$base = new Room_Manager();
$id = GetParameter("id");
$title_th = GetParameter("title_th");
$title_en = GetParameter("title_en");
$seq = GetParameter("seq");

$result = $base->edit_room_type($id,$title_th,$title_en,$seq);
global $result_code; //call global variable
$result_code="0";
return $result;
}

function DeleteRoom(){
	$base = new Room_Manager();
	$id = GetParameter("id");
	$base->delete_room_type($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}

function Delete_Gallery(){
	$base = new Room_Manager();
	$id = GetParameter("id");
	$base->delete_room_gallery($id);
	$result = "delete success";
	global $result_code; //call global variable
	$result_code="0";
	return $result;
}



function Listobject(){

	$base = new Room_Manager();
	$dataset = $base->list_room_type();
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
	$column .= "<th class='col-md-4'>Title</th>";
	$column .= "<th class='col-md-1'>Seq</th>";
	$column .= "<th></th>";
	$column .= "</tr></thead><tbody>";
	return $column;
}

function ListRoom(){
	
	$base = new Room_Manager();
	$dataset = $base->list_room_type();

	$result .= initial_column();

	if($dataset){
		
		while($row = $dataset->fetch_object()){
			
			$result .= "<tr>";
			$result .="<td>".$row->id."</td>";
			$result .="<td>".$row->title_en."</td>";
			$result .="<td>".$row->seq."</td>";
			$result .="<td><button class='btn btn-warning' data-id='".$row->id."' data-item='services/room_service.php?type=item' data-page='room_edit.html' data-title='Modify' onclick='page.modify(this);' ><span class='glyphicon glyphicon-pencil'></span> Edit</button> ";
			$result .="<button class='btn btn-danger' data-id='".$row->id."' data-item='services/room_service.php?type=item' data-page='room_del.html' data-title='Remove' onclick='page.remove(this);'><span class='glyphicon glyphicon-trash'></span> Del</button></td>";
			$result .= "</tr>";
			
		}
	}
	$result .= "</tbody>";
	global $result_code; //call global variable
	$result_code = "0";
	return $result;
}

function GetRoomType(){
	
	$base = new Room_Manager();
	$id = GetParameter("id");
	$dataset = $base->get_room_type($id);
	$row = $dataset->fetch_object();
	$result = array(
		"id"=>$row->id,
		"title_th"=>$row->title_th,
		"title_en"=>$row->title_en,
		"product_gallery"=>ListGallery($row->id),
		"seq"=>$row->seq
	);
	global $result_code; //call global variable
	$result_code="0";
	return $result ;
}


function call_upload_gallery($args){
	
	$base = new Room_Manager();
	$total = count($_FILES['file_image']['name']);
	$id = $args["id"];
	
	for($i=0;$i<$total;$i++){
		
		$source  = $_FILES['file_image']['tmp_name'][$i];
		if($source!=""){
			
			$name = $_FILES['file_image']['name'][$i];
			$dir =  "images/rooms/".$id."/";
			createdir("../../".$dir);
			$ext = pathinfo($_FILES['file_image']['name'][$i],PATHINFO_EXTENSION);
			$filename = date("s").gettimeofday()["usec"].".".$ext;
			$distination =  "../../".$dir.$filename;
			
			upload_image($source,$distination);
			$result = $base->insert_gallery($id,$dir.$filename);
		}
	}
	
	return "upload complete";
	
}

function ListGallery($id){
	
	$base = new Room_Manager();
	$dataset = $base->list_gallery($id);
	
	if($dataset->num_rows===0){
		$result .= "ไม่พบรูปภาพ";
	}
	else {
		while($row = $dataset->fetch_object()){
			$result.= "<div class='col-md-4' id='".$row->id."'>";
			$result.= "<span style='cursor:pointer' class='glyphicon glyphicon-remove' data-id='".$row->id."' onclick='del_image(this)'></span>";
			$result.= "<img class='img-responsive' style='height:172.88px' src='../".$row->image."' />";
			$result.= "</div>";
		}
	}
	
	return $result;
}

?>