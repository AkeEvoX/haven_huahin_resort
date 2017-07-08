<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Package_Manager{
	
	protected $mysql;
	function __construct(){

		try{

			$this->mysql = new database();
			$this->mysql->connect();
			//echo "initial database.";
		}
		catch(Exception $e)
		{
			die("initial package manager error : ". $e->getMessage());
		}
	}

	function __destruct(){ //page end
		$this->mysql->disconnect();
	}
	
	function insert_item($title_th,$title_en,$room_type,$package_price,$isFoodService,$isCancelRoom,$isPaymenOnline,$room_unit){
		
		try{
			
			$create_by = "0";
			$create_date = "now()";
			$sql = "insert into room_packages (title_th,title_en,room_type,package_price,food_service,cancel_room,payment_online,rent_unit,create_by,create_date) ";
			$sql .= "values('$title_th','$title_en','$room_type','$package_price','$isFoodService','$isCancelRoom','$isPaymenOnline','$room_unit',$create_by,$create_date) ";
			
			log_warning("package > insert item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
		
	}
	
	function edit_item($id,$title_th,$title_en,$room_type,$package_price,$food_service,$cancel_room,$payment_online,$rent_unit){
		
		try{

			$update_by = "0";
			$update_date = "now()";

			$sql = "update room_packages set ";
			$sql .= " title_th='$title_th' ";
			$sql .= ",title_en='$title_en' ";
			$sql .= ",room_type='$room_type' ";
			$sql .= ",package_price='$package_price' ";
			$sql .= ",food_service='$food_service' ";
			$sql .= ",cancel_room='$cancel_room' ";
			$sql .= ",payment_online='$payment_online' ";
			$sql .= ",rent_unit='$rent_unit' ";
			$sql .= ",update_by=$update_by ";
			$sql .= ",update_date=$update_date ";
			$sql .= " where id='".$id."';";
			
			log_warning("package > edit item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "MODIFY SUCCESS.";
			}else{
				$result = "MODIFY FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
		}
		
	}
	
	
	function delete_item($id){
		
		try{
			
			$sql = "delete from room_packages";
			$sql .= " where id='".$id."' ";
			
			log_warning("package > delete item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
		
	}
	
	function get_item($id){
		try{
			
			$sql = "select * from  room_packages where id='".$id."' ";
			log_warning("package > get item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
	}

	function list_item(){
		try{
			
			$sql = "select * from  room_packages";
			log_warning("package > get list > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
	}
}

?>