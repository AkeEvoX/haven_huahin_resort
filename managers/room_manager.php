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
	
	function get_room_gallery($room_id){
		
		try{

			$sql = "select * ";
			$sql .= "from room_gallery where room_type='".$room_id."' ";
			$result = $this->mysql->execute($sql);

			log_warning("get_room_gallery > " . $sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get room gallery : ".$e->getMessage();
		}
		
	}
	
	function get_room_options($lang){
		
		try{

			$sql = "select id,title_".$lang." as title,detail_".$lang." as detail , remark_".$lang." as remark ,price,image ";
			$sql .= "from options_type order by id ";
			$result = $this->mysql->execute($sql);

			log_warning("options_type > " . $sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get room optinals : ".$e->getMessage();
		}
		
	}

	function get_room_available($startdate,$enddate,$lang){
		try{

			$sql = "select t.id as room_id,t.title_en as room_type,p.id as pack_id,p.title_en as package_name,p.package_price,p.package_start,p.package_end,p.room_unit ";
			$sql .= ",COALESCE(reserve.reserve_unit,0) as reserve_unit ";
			$sql .= "from room_packages p ";
			$sql .= "inner join room_types t on p.room_type = t.id ";
			$sql .= "left join ( ";
			$sql .= "select b.room_key as room_id,count(b.room_key) as reserve_unit from reserve_info as info ";
			$sql .= "left join reserve_rooms b on info.unique_key = b.unique_key ";
			$sql .= "where info.create_date between '2017-08-05 00:00:00' and '2017-08-09 00:00:00' ";
			$sql .= "group by b.room_key ";
			$sql .= ") reserve on reserve.room_id = t.id";
			$sql .= "where  p.package_start <='2017-08-05 00:00:00' and p.package_end >='2017-08-09 00:00:00'";
			$sql .= "and COALESCE(reserve.reserve_unit, 0) < p.room_unit ";
			$sql .= "order by t.seq  ;";
			
			//where info.create_date between '".$startdate." 00:00:00' and '".$enddate." 00:00:00' 
			/*




*/

			$result = $this->mysql->execute($sql);

			log_warning("get_room_available > " . $sql);

			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get room available : ".$e->getMessage();
		}
	}
	

//code backup
/*
	function get_room_available($startdate,$enddate,$lang){
		try{

			$sql = "select a.id ,a.title_".$lang."  as title,a.unit,COALESCE(r.reserve_unit, 0) as reserve_unit ";
			$sql .= "from room_types a left join (";
			$sql .= "select b.room_key,count(b.room_key) as reserve_unit from reserve_info as info ";
			$sql .= "left join reserve_rooms b on info.unique_key = b.unique_key ";
			$sql .= "where info.create_date between '".$startdate." 00:00:00' and '".$enddate." 00:00:00' ";
			$sql .= "group by b.room_key ";
			$sql .= ") r on a.id = r.room_key ";
			$sql .= "where COALESCE(r.reserve_unit, 0)  <= unit  ";
			//$sql .= " and";
			$sql .= "order by a.seq";
			$result = $this->mysql->execute($sql);

			log_warning("get_room_available > " . $sql);

			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get room available : ".$e->getMessage();
		}
	}
*/
	function get_item_package($pack_id,$lang){

		try{

			$sql = " select id,title_".$lang." as title,package_price,food_service,cancel_room,payment_online,extra_bed,max_person ";
			$sql .= ",detail_".$lang." as detail ,condition_".$lang." as conditions ";
			$sql .= " from room_packages where id='".$pack_id."'  and status=1 ";

			$result = $this->mysql->execute($sql);

			log_warning("get_item_package > " . $sql);

			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get item package : ".$e->getMessage();
		}

	}

	function get_room_packages($room_id,$range_date,$lang){
		try{

			$sql = " select id,title_".$lang." as title,package_price,food_service,cancel_room,payment_online,extra_bed,max_person ,extra_price_adults ,extra_price_children";
			$sql .= ",detail_".$lang." as detail ,condition_".$lang." as conditions ";
			$sql .= " from room_packages where room_type='".$room_id."'  and status=1 and special_date=0 ";
			$sql .= " or (special_date=".$range_date." and room_type=".$room_id." and status=1 )  ";//same day
			$sql .= " or (special_date <= ".$range_date." and special_date > 30 and room_type=".$room_id." and status=1 ) "; //over month
		
//#exsample
/*
select id,title_en as title,package_price,food_service,cancel_room,payment_online,special_date,room_type,extra_bed,max_person
from room_packages where room_type=2  and status=1 and special_date=0 or (special_date=33 and room_type=1)
or (special_date <= 33 and special_date > 30)
*/
			$result = $this->mysql->execute($sql);

			log_warning("get_room_package > " . $sql);

			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get room package : ".$e->getMessage();
		}
	}

	function get_room_bed($room_id,$lang){
		try{

			$sql = "select b.id,b.title_".$lang." as title ";
			$sql .= "from room_beds a left join bed_type b on a.bed_id=b.id ";
			$sql .= "where a.room_id='".$room_id."' ";
			$result = $this->mysql->execute($sql);

			log_warning("get_room_bed > " . $sql);

			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get room bed : ".$e->getMessage();
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