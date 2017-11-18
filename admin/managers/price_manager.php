<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Price_Manager{
	
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
	
	function insert_item($name,$status){
		
		try{
			
			$create_by = "0";
			$create_date = "now()";
			
			$sql = "insert into price_type (name,status,create_by,create_date) ";
			$sql .= "values('$name','$status','$create_by',$create_date) ";
			
			log_warning("price > insert item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_error("price > insert item > error >  "  . $e->getMessage());
		}
		
	}
	
	function edit_item($id,$name,$status){
		
		try{

			$update_by = "0";
			$update_date = "now()";

			$sql = "update price_type set ";
			$sql .= " name='$name' ";
			$sql .= ",status='$status' ";
			$sql .= ",update_by=$update_by ";
			$sql .= ",update_date=$update_date ";
			$sql .= " where id='".$id."';";
			
			log_warning("price > edit item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "MODIFY SUCCESS.";
			}else{
				$result = "MODIFY FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_error("price > edit item > error > ".$e->getMessage());
		}
		
	}
	
	
	function delete_item($id){
		
		try{
			
			$sql = "delete from price_type";
			$sql .= " where id='".$id."' ";
			
			log_warning("price > delete item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_error("price > delete item > error > " . $e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
		
	}
	
	function get_item($id){
		try{
			
			$sql = "select * from  price_type where id='".$id."' ";
			log_warning("price > get item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("price > get item > error > "  . $e->getMessage());
		}
	}

	function list_item(){
		try{
			
			$sql = "select * from price_type ";
			log_warning("price > get list > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_error("price > get list > error > " . $e->getMessage());
		}
	}
}

?>