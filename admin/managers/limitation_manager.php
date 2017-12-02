<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Limitation_Manager{
	
	protected $mysql;
	function __construct(){

		try{

			$this->mysql = new database();
			$this->mysql->connect();
			//echo "initial database.";
		}
		catch(Exception $e)
		{
			die("initial reserve limitation manager error : ". $e->getMessage());
		}
	}

	function __destruct(){ //page end
		$this->mysql->disconnect();
	}
	
	function insert_item($pack_id,$startdate,$enddate,$min_reserve){
		
		try{
			
			$create_by = "0";
			$create_date = "now()";
			$sql = "insert into reserve_limitation (pack_id,startdate,enddate,min_reserve,create_by,create_date) ";
			$sql .= "values('$pack_id','$startdate','$enddate',$min_reserve,$create_by,$create_date)  ";
			
			log_warning("reserve limitation > insert item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("reserve limitation  > insert item > error > " .$e->getMessage());
		}
		
	}
	
	function edit_item($id,$pack_id,$startdate,$enddate,$min_reserve){
		
		try{

			$update_by = "0";
			$update_date = "now()";

			$sql = "update reserve_limitation set ";
			$sql .= " pack_id='$pack_id' ";
			$sql .= ",startdate='$startdate' ";
			$sql .= ",enddate='$enddate' ";
			$sql .= ",min_reserve='$min_reserve' ";
			$sql .= ",update_by=$update_by ";
			$sql .= ",update_date=$update_date ";
			$sql .= " where id='".$id."';";
			
			log_warning("reserve limitation  > edit item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "MODIFY SUCCESS.";
			}else{
				$result = "MODIFY FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("reserve limitation  > edit item > error > " .$e->getMessage());
		}
		
	}
	
	function delete_item($id){
		
		try{

			$update_by = "0";
			$update_date = "now()";

			$sql = "delete from  reserve_limitation ";
			$sql .= "where id='".$id."';";
			
			log_warning("reserve limitation  > delete item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("reserve limitation  > delete item > error > " .$e->getMessage());
		}
		
	}
	
	function get_item($id){
		try{
			
			$sql = "select * from view_minimum_reserve ";
			$sql .= "where id=$id ;";
			
			log_warning("reserve limitation > get item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("reserve limitation > get item > " . $e->getMessage());
		}
	}
	
	function list_item(){
		try{
			
			$sql = "select * from view_minimum_reserve;";
			
			log_warning("reserve limitation > get list > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("reserve limitation > get list > error >".$e->getMessage());
		}
	}
}

?>