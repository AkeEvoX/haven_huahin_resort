<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class User_Manager{
	
	protected $mysql;
	function __construct(){

		try{

			$this->mysql = new database();
			$this->mysql->connect();
			//echo "initial database.";
		}
		catch(Exception $e)
		{
			die("initial user manager error : ". $e->getMessage());
		}
	}

	function __destruct(){ //page end
		$this->mysql->disconnect();
	}
	
	function authen($user,$pass){
		
		try{
			
			$sql ="select id,firstname,lastname,role_id from users where user_name='$user' and pass_word='$pass' ";
			
			log_warning("authen > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("authen error > " . $e->getMessage());
		}
		
	}
	
	function insert_item($firstname,$lastname,$username,$password,$role_id,$status){
		
		try{
			
			$create_by = $_SESSION["user"]["id"];
			$create_date = "now()";
			
			$sql = "insert into users (firstname,lastname,user_name,pass_word,role_id,status,create_by,create_date) ";
			$sql .= "values('$firstname','$lastname','$username','$password',$role_id,$status,'$create_by',$create_date) ";
			
			log_warning("user > insert > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("user > insert > error > " . $e->getMessage());
		}
		
	}
	
	function edit_item($id,$firstname,$lastname,$username,$password,$role_id,$status){
		
		try{
			
			$update_by = "0";
			$update_date = "now()";
			
			$sql = "update users set ";
			$sql .= " firstname='$firstname' ";
			$sql .= ",lastname='$lastname'  ";
			$sql .= ",user_name='$username'  ";
			
			if($password!="")
				$sql .= ",pass_word='$password'  ";
			
			$sql .= ",role_id='$role_id'  ";
			$sql .= ",status='$status'  ";
			$sql .= " where id='".$id."';";
			
			log_warning("user > modify > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "MODIFY SUCCESS.";
			}else{
				$result = "MODIFY FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("user > modify > error > " . $e->getMessage());
		}
		
	}
	
	
	function delete_item($id){
		
		try{
			
			$sql = "delete from users ";
			$sql .= " where id='".$id."' ";
			
			log_warning("user > delete > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("user > delete > error > ".$e->getMessage());
		}
		
	}
	
	function get_item($id){
		try{
			
			$sql = "select users.*,role.title as role_name from  users ";
			$sql .= "left join user_roles role on users.role_id = role.id ";
			$sql .= "where users.id='".$id."'  ";
			log_warning("user > get item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("user > get item > error > " . $e->getMessage());
		}
	}

	function list_item(){
		try{
			
			$sql = "select users.*,role.title as role_name from  users ";
			$sql .= "left join user_roles role on users.role_id = role.id ";
			log_warning("user > list > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("user > list > error > ".$e->getMessage());

		}
	}
	
	function list_role(){
		try{
			
			$sql = "select id as role_id,title as role_name from  user_roles ";
			log_warning("user > list role > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("user > list role > error > ".$e->getMessage());

		}
	}
}

?>