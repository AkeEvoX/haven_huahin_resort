<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Bed_Manager{
	
	protected $mysql;
	function __construct(){

		try{

			$this->mysql = new database();
			$this->mysql->connect();
			//echo "initial database.";
		}
		catch(Exception $e)
		{
			die("initial bed manager error : ". $e->getMessage());
		}
	}

	function __destruct(){ //page end
		$this->mysql->disconnect();
	}
	
	function insert_bed_type($title_th,$title_en){
		
		try{
			
			$sql = "insert into bed_type (title_th,title_en) ";
			$sql .= "values('$title_th','$title_en') ";
			
			log_warning("insert_bed_type > " . $sql);
			
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
	
	function edit_bed_type($id,$title_th,$title_en){
		
		try{
			
			$sql = "update bed_type set ";
			$sql .= " title_th='$title_th',title_en='$title_en' ";
			$sql .= " where id='".$id."';";
			
			log_warning("edit_bed_type > " . $sql);
			
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
	
	
	function delete_bed_type($id){
		
		try{
			
			$sql = "delete from bed_type ";
			$sql .= " where id='".$id."' ";
			
			log_warning("delete_bed_type > " . $sql);
			
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
	
	function get_bed_type($id){
		try{
			
			$sql = "select * from  bed_type where id='".$id."' ";
			log_warning("get_room_type > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
	}

	function list_bed_type(){
		try{
			
			$sql = "select * from  bed_type";
			log_warning("list_bed_type > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
	}
}

?>