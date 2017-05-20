<?php
require_once("../lib/database.php");
require_once("../lib/logger.php");
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
	
	function get_room_gallery($room_type_id){
		
		try{

			$sql = "select * ";
			$sql .= "from room_gallery where room_type='".$room_type_id."' ";
			$result = $this->mysql->execute($sql);

			log_warning("get_room_gallery > " . $sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get room gallery : ".$e->getMessage();
		}
		
	}
	
	function insert_options($unique_key,$option_key,$price){
		try{
			$sql = "insert into reserve_options(unique_key,option_key,option_price)";
			$sql .= "values('$unique_key','$option_key','$price'); ";
			
			log_warning("insert_options > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
			
		}catch(Exception $e){
			echo "Cannot insert_options : ".$e->getMessage();
		}
	}
	
	function insert_rooms($unique_key,$room_key,$price){
		
		try{
			
			$sql = "insert into reserve_rooms(unique_key,room_key,room_price)";
			$sql .= "values('$unique_key','$room_key','$price'); ";
			
			log_warning("insert_rooms > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;
		}catch(Exception $e){
			echo "Cannot insert_rooms : ".$e->getMessage();
		}
		
	}

	//## command date add on my sql
	//select reserve_startdate ,DATE_ADD(reserve_startdate, INTERVAL 2 day) as enddate from reserve_info limit 1 ;
	/*
	##filter room by date##
	select a.*,COALESCE(r.reserve_unit, 0) as reserve_unit from room_types a
		left join (
 			select b.room_key,count(b.room_key) as reserve_unit from reserve_info as info 
 			left join reserve_rooms b on info.unique_key = b.unique_key 
 			where info.create_date between '2017-04-01 :00:00:00' and '2017-04-30 :00:00:00'
 			group by b.room_key
 		) r on a.id = r.room_key
	where COALESCE(r.reserve_unit, 0)  <= unit 
;

	*/
}

?>