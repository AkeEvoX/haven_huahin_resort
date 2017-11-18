<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Sales_Date_Manager{
	
	protected $mysql;
	function __construct(){

		try{

			$this->mysql = new database();
			$this->mysql->connect();
			//echo "initial database.";
		}
		catch(Exception $e)
		{
			die("initial sales date manager error : ". $e->getMessage());
		}
	}

	function __destruct(){ //page end
		$this->mysql->disconnect();
	}
	
	function insert_item($room_price_id,$pack_date,$room_unit,$status){
		
		try{
			
			$create_by = "0";
			$create_date = "now()";
			$sql = "insert into room_packages (room_price_id,pack_date,room_unit,status,create_by,create_date) ";
			$sql .= "values('$room_price_id','$pack_date','$room_unit',$status,$create_by,$create_date)  ";
			
			log_warning("sale date > insert item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "INSERT SUCCESS.";
			}else{
				$result = "INSERT FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("sales date  > insert item > error > " .$e->getMessage());
		}
		
	}
	
	function edit_item($id,$room_price_id,$room_unit,$status){
		
		try{

			$update_by = "0";
			$update_date = "now()";

			$sql = "update room_packages set ";
			$sql .= " room_price_id='$room_price_id' ";
			$sql .= ",room_unit='$room_unit' ";
			$sql .= ",status='$status' ";
			$sql .= ",update_by=$update_by ";
			$sql .= ",update_date=$update_date ";
			$sql .= " where id='".$id."';";
			
			log_warning("sales date  > edit item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "MODIFY SUCCESS.";
			}else{
				$result = "MODIFY FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("sales date  > edit item > error > " .$e->getMessage());
		}
		
	}
	
	
	function delete_item($id){
		
		try{
			
			$sql = "delete from room_packages";
			$sql .= " where id='".$id."' ";
			
			log_warning("sales date  > delete item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			if($result=="true"){
				$result = "DELETE SUCCESS.";
			}else{
				$result = "DELETE FAILURE.";
			}
			
			return $result;
		}catch(Exception $e){
			log_debug("sales date > delete item > error > " . $e->getMessage());
		}
		
	}
	
	function get_item($id){
		try{
	
			$sql = "select dates.id,dates.pack_date,dates.room_unit,dates.status,unit.price ";
			$sql .= ",room.id as room_type,room.title_en as room_name,pack.id as pack_id ,pack.title_en as pack_name,dates.room_price_id as price_id, price.`name` as price_name ";
			$sql .= "from room_packages dates ";
			$sql .= "left join room_prices unit on unit.id = dates.room_price_id ";
			$sql .= "left join packages pack on pack.id = unit.pack_id ";
			$sql .= "left join price_type price on price.id = unit.price_id ";
			$sql .= "left join room_types room on pack.room_type = room.id where 1=1 ";
			$sql .= "and dates.id='".$id."' ";
			log_warning("sales date > get item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("package price> get item > " . $e->getMessage());
		}
	}

	function list_item($room_type,$pack_id,$price_id,$find_start,$find_end){
		try{
		
			
			$sql = "select dates.id,dates.pack_date,dates.room_unit,dates.status,unit.price ";
			$sql .= ",room.title_en as room_name,pack.title_en as pack_name,price.`name` as price_name ";
			$sql .= "from room_packages dates ";
			$sql .= "left join room_prices unit on unit.id = dates.room_price_id ";
			$sql .= "left join packages pack on pack.id = unit.pack_id ";
			$sql .= "left join price_type price on price.id = unit.price_id ";
			$sql .= "left join room_types room on pack.room_type = room.id where 1=1 ";
			
			if($room_type!=""){
				$sql .= "and room.id='$room_type' " ;
			}
			
			if($pack_id!=""){
				$sql .= "and pack.id='$pack_id' " ;
			}
			
			if($price_id!=""){
				$sql .= "and price.id='$price_id' " ;
			}
			
			if($find_start !="" && $find_end!=""){
				//DD//MM/YYYY
				$find_start = $find_start. " 00:00:00";
				$find_end = $find_end. " 23:59:59";
				$sql .= "and dates.pack_date between '$find_start' and '$find_end' ";
				
			}
			$sql .= "order by dates.pack_date ";
			
			log_warning("sales date> get list > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("sales date > get list > error >".$e->getMessage());
		}
	}
}

?>