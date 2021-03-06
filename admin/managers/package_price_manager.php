<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Package_Price_Manager{
	
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
	
	function insert_item($pack_id,$price_id,$price,$status){
		
		try{
			
			$create_by = "0";
			$create_date = "now()";
			$sql = "insert into room_prices (pack_id,price_id,price,status,create_by,create_date) ";
			$sql .= "values('$pack_id','$price_id','$price',$status,$create_by,$create_date)  ";
			
			log_warning("package price > insert item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("package price > insert item > error > " .$e->getMessage());
		}
		
	}
	
	function edit_item($id,$pack_id,$price_id,$price,$status){
		
		try{

			$update_by = "0";
			$update_date = "now()";

			$sql = "update room_prices set ";
			$sql .= " pack_id='$pack_id' ";
			$sql .= ",price_id='$price_id' ";
			$sql .= ",price='$price' ";
			$sql .= ",status='$status' ";
			$sql .= ",update_by=$update_by ";
			$sql .= ",update_date=$update_date ";
			$sql .= " where id='".$id."';";
			
			log_warning("package price > edit item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "MODIFY SUCCESS.";
			}else{
				$result = "MODIFY FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("package price > edit item > error > " .$e->getMessage());
		}
		
	}
	
	
	function delete_item($id){
		
		try{
			
			$sql = "delete from room_prices";
			$sql .= " where id='".$id."' ";
			
			log_warning("package price > delete item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("package price > delete item > error > " . $e->getMessage());
		}
		
	}
	
	function get_item($id){
		try{
			
	/* 		$sql = "select price.*,pack.room_type  ";
			$sql .= " from  room_prices price ";
			$sql .= "left join packages pack on pack.id = price.pack_id  "; */
			
			$sql .= "select map.id as id ,room.id as room_type , room.title_en as room_name,pack.id as pack_id,pack.title_en as pack_name ";
			$sql .= ",price.id as price_id,price.name as price_name ";
			$sql .= ",map.price,map.status ";
			$sql .= "from room_prices map ";
			$sql .= "left join packages pack on pack.id = map.pack_id ";
			$sql .= "left join price_type price on price.id = map.price_id ";
			$sql .= "left join room_types room on pack.room_type = room.id ";

			$sql .= "where map.id='".$id."' ";
			log_warning("package > get item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("package price> get item > " . $e->getMessage());
		}
	}
	
	function item_object($room_price_id,$price_id,$pack_id){
		try{
			
			$sql = "select room.title_en as room_type,map.id,pack.title_en as pack_name,pack.id as pack_id,price.id as price_id,price.name as price_type,map.price,map.status ";
			$sql .=  "from room_prices map ";
			$sql .= "left join packages pack on pack.id = map.pack_id ";
			$sql .= "left join price_type price on price.id = map.price_id ";
			$sql .= "left join room_types room on pack.room_type = room.id  where 1=1 ";
			
			if($room_price_id!=""){
				$sql .= "and map.id=".$room_price_id;
			}
			
			if($pack_id!=""){
				$sql .= "and price.id=".$price_id;
			}
			if($price_id!=""){
				$sql .= " and pack.id =".$pack_id." ";
			}
			
			log_warning("package price> get item object > " . $sql );
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("package price> get item object > error >".$e->getMessage());
		}
	}

	function list_item($room_price_id,$price_id,$pack_id){
		try{
			
			$sql = "select room.title_en as room_type,map.id,pack.title_en as pack_name,price.id as price_id,price.name as price_type,map.price,map.status ";
			$sql .=  "from room_prices map ";
			$sql .= "left join packages pack on pack.id = map.pack_id ";
			$sql .= "left join price_type price on price.id = map.price_id ";
			$sql .= "left join room_types room on pack.room_type = room.id  where 1=1 ";
			
			if($room_price_id!=""){
				$sql .= "and map.id=".$room_price_id;
			}
			
			if($price_id!=""){
				$sql .= "and price.id=".$price_id;
			}
			
			if($pack_id!=""){
				$sql .= " and pack.id =".$pack_id." ";
			}
			
			log_warning("package price> get list > " . $sql );
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("package price> get list > error >".$e->getMessage());
		}
	}
}

?>