<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Room_Manager{
	
	protected $mysql;
	function __construct(){

		try{

			$this->mysql = new database();
			$this->mysql->connect();
			//echo "initial database.";
		}
		catch(Exception $e)
		{
			die("initial room manager error : ". $e->getMessage());
		}
	}

	function __destruct(){ //page end
		$this->mysql->disconnect();
	}
	
	function insert_room_type($title_th,$title_en,$seq){
		
		try{
			
			$create_by = 0; //system
			
			$sql = "insert into room_types(title_th,title_en,seq,create_by,create_date) ";
			$sql .= "values('$title_th','$title_en',$seq,'$create_by',now()) ";
			
			log_warning("insert_room_type > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
		}
		
	}
	
	function edit_room_type($id,$title_th,$title_en,$seq){
		
		try{
			
			$update_by = 0;//system
			
			$sql = "update room_types set ";
			$sql .= " title_th='$title_th',title_en='$title_en',seq='$seq' ,update_by='$update_by' ,update_date=now() ";
			$sql .= " where id='".$id."';";
			
			log_warning("edit_room_type > " . $sql);
			
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
	
	
	function delete_room_type($id){
		
		try{
			
			$sql = "delete from room_types ";
			$sql .= " where id='".$id."' ";
			
			log_warning("delete_room_type > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
		}
		
	}
	
	function get_room_type($id){
		try{
			
			$sql = "select * from  room_types where id='".$id."' ";
			log_warning("get_room_type > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
	}

	function list_room_type(){
		try{
			
			$sql = "select * from  room_types";
			log_warning("list_room_type > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
	}
}

?>