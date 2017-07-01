<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Option_Manager{
	
	protected $mysql;
	function __construct(){

		try{

			$this->mysql = new database();
			$this->mysql->connect();
			//echo "initial database.";
		}
		catch(Exception $e)
		{
			die("initial options manager error : ". $e->getMessage());
		}
	}

	function __destruct(){ //page end
		$this->mysql->disconnect();
	}
	
	function insert_option($title_th,$title_en,$price,$detail_th,$detail_en,$image,$remark_th,$remark_en){
		
		try{
			
			$create_by = "0";
			$create_date = "now()";
			
			$sql = "insert into room_options (title_th,title_en,price,detail_th,detail_en,image,remark_th,remark_en,create_by,create_date) ";
			$sql .= "values('$title_th','$title_en','$price','$detail_th','$detail_en','$image','$remark_th','$remark_en',$create_by,$create_date) ";
			
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
	
	function edit_option($id,$title_th,$title_en,$price,$detail_th,$detail_en,$image,$remark_th,$remark_en){
		
		try{

			$update_by = "0";
			$update_date = "now()";

			$sql = "update room_options set ";
			$sql .= " title_th='$title_th' ";
			$sql .= ",title_en='$title_en' ";
			$sql .= ",price='$price' ";
			$sql .= ",detail_th='$detail_th' ";
			$sql .= ",detail_en='$detail_en' ";

			if(isset($image)) $sql .= ",image='$image' ";

			$sql .= ",remark_th='$remark_th' ";
			$sql .= ",remark_en='$remark_en' ";
			$sql .= ",update_by=$update_by ";
			$sql .= ",update_date=$update_date ";
			$sql .= " where id='".$id."';";
			
			log_warning("edit_option > " . $sql);
			
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
	
	
	function delete_option($id){
		
		try{
			
			$sql = "delete from room_options";
			$sql .= " where id='".$id."' ";
			
			log_warning("delete_option > " . $sql);
			
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
	
	function get_option($id){
		try{
			
			$sql = "select * from  room_options where id='".$id."' ";
			log_warning("get_option > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
	}

	function list_option(){
		try{
			
			$sql = "select * from  room_options";
			log_warning("list_option > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug($e->getMessage());
			//echo "Cannot insert_room_type : ".$e->getMessage();
		}
	}
}

?>