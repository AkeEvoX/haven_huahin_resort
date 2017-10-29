<?php
require_once("../../lib/database.php");
require_once("../../lib/logger.php");
class Reserve_Manager{
	
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
	
	function get_item($id){
		try{
			
			$sql = "select id,unique_key,reserve_startdate as start_date,reserve_enddate as end_date "; 
			$sql .= ",price_option,price_sum,price_service,price_vat,price_net, "; 
			$sql .= ",reserve_status as status,adults,children,children_2,night,acc_code "; 
			$sql .= ",concat(title_name,first_name,' ',last_name) as cust_name,concat(prefix,mobile) as cust_mobile "; 
			$sql .= ",birthdate,email,create_date as register_date "; 
			$sql .= "from reserve_info where id=$id ";
			
			log_warning("sales date > get item > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("package price> get item > " . $e->getMessage());
		}
	}

	function list_item($unique,$customer,$mobile,$find_start,$find_end){
		try{
			
			
			$sql = "select id,unique_key,reserve_startdate as start_date,reserve_enddate as end_date,price_net,reserve_status as status ";
			$sql .= ",concat(title_name,first_name,' ',last_name) as cust_name,concat(prefix,mobile) as cust_mobile ";
			$sql .= ",create_date as register_date  ";
			$sql .= "from reserve_info where 1=1 ";
			
			if($unique!=""){
				$sql .= "and unique_key='$unique' " ;
			}
			
			if($customer!=""){
				$sql .= "and concat(first_name,' ',last_name)  like '%$customer%' " ;
			}
			
				if($mobile!=""){
				$sql .= "and concat(prefix,mobile) like '%$mobile%' " ;
			}
			
			if($find_start !="" && $find_end!=""){
				//DD//MM/YYYY
				$find_start = $find_start. " 00:00:00";
				$find_end = $find_end. " 23:59:59";
				$sql .= "and create_date between '$find_start' and '$find_end' ";
				
			}
			$sql .= "order by create_date ";
			
			log_warning("reserve > get list > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			log_debug("reserve > get list > error >".$e->getMessage());
		}
	}
}

?>