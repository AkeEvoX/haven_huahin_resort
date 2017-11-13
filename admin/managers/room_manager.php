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
	
	function insert_gallery($id,$path){
		
		try{
			
			$create_by = 0; //system
			
			$sql = "insert into room_gallery(room_type,image,create_by,create_date) ";
			$sql .= "values('$id','$path','$create_by',now()) ";
			
			log_warning("room > gallery > insert > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("room > gallery > insert > " .$e->getMessage());
		}
	}
	
	function insert_room_type($title_th,$title_en,$seq){
		
		try{
			
			$create_by = 0; //system
			
			$sql = "insert into room_types(title_th,title_en,seq,create_by,create_date) ";
			$sql .= "values('$title_th','$title_en',$seq,'$create_by',now()) ";
			
			log_warning("room > insert > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("room > insert > " . $e->getMessage());
		}
		
	}
	
	function edit_room_type($id,$title_th,$title_en,$seq){
		
		try{
			
			$update_by = 0;//system
			
			$sql = "update room_types set ";
			$sql .= " title_th='$title_th',title_en='$title_en',seq='$seq' ,update_by='$update_by' ,update_date=now() ";
			$sql .= " where id='".$id."';";
			
			log_warning("room > modity > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "MODIFY SUCCESS.";
			}else{
				$result = "MODIFY FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("room > modity > " . $e->getMessage());
		}
		
	}
	
	
	function delete_room_type($id){
		
		try{
			
			$sql = "delete from room_types ";
			$sql .= " where id='".$id."' ";
			
			log_warning("room > delete > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("room > delete > " . $e->getMessage());
		}
		
	}
	
	function delete_room_gallery($id){
		
		try{
			
			$sql = "delete from room_gallery ";
			$sql .= " where id='".$id."' ";
			
			log_warning("room > gallery >  delete > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("room > gallery > delete > " . $e->getMessage());
		}
		
	}
	
	
	function get_room_type($id){
		try{
			
			$sql = "select * from  room_types where id='".$id."' ";
			log_warning("room > get > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("room > get > " .$e->getMessage());
		}
	}

	function list_room_type(){
		try{
			
			$sql = "select * from  room_types";
			log_warning("room > list > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("room > list > " . $e->getMessage());
		}
	}
	
	function list_gallery($id){
		try{
			
			$sql = "select * from  room_gallery where room_type='$id' ";
			log_warning("room > gallery > list > " . $sql);
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("room > gallery > list > " .$e->getMessage());
		}
		
	}
}

?>