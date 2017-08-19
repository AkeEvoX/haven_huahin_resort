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

	function get_room_available($startdate,$enddate,$range,$lang){
		try{
			
			$sql = " SELECT room.id AS room_id,	room.title_".$lang." AS room_name,	pack.room_type, pack.id pack_id, pack.title_".$lang." AS pack_name, pack_price.money, pack_price.room_unit, COALESCE(vrr.unit,0) as reserve_unit ";
			$sql .= "FROM	packages pack ";
			$sql .= "LEFT JOIN room_types room ON pack.room_type = room.id ";
			$sql .= "INNER JOIN ( ";
			$sql .= "SELECT price.pack_id	,sum(price.price) AS money ,sum(period.room_unit) as room_unit ";
			$sql .= "FROM	room_packages AS period ";
			$sql .= "INNER JOIN room_prices price ON period.room_price_id = price.id ";
			$sql .= "WHERE period.pack_date >= '".$startdate." 00:00:00' AND period.pack_date < '".$enddate." 00:00:00' ";
			$sql .= "GROUP BY price.pack_id ";
			$sql .= " ) pack_price ON pack.id = pack_price.pack_id ";
			$sql .= " left join ( ";
			$sql .= "select vrr.pack_id , count(vrr.pack_id) as unit from ";
			$sql .= "view_room_reserve vrr where vrr.pack_date >='".$startdate." 00:00:00' and vrr.pack_date < '".$enddate." 00:00:00' ";
			$sql .= "group by vrr.pack_id ";
			$sql .= ") vrr on vrr.pack_id = pack.id ";
			$sql .= "where pack.status=1 and pack.special_date=0 and COALESCE(vrr.unit, 0) < pack_price.room_unit ";
			$sql .= "or (pack.special_date=".$range." and COALESCE(vrr.unit, 0) < pack_price.room_unit) "; //-- for tomorrow 
			$sql .= "or (pack.special_date <= ".$range." and pack.special_date > 30 and COALESCE(vrr.unit, 0) < pack_price.room_unit)";    // -- reserve more 31 day
			$sql .= "or (pack.special_date = datediff('".$enddate."','".$startdate."') and COALESCE(vrr.unit, 0) < pack_price.room_unit) ;"; //-- continue reserve day;

			$result = $this->mysql->execute($sql);

			log_warning("get_room_available > " . $sql);

			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get get room available : ".$e->getMessage();
		}
	}

	function get_item_package($pack_id,$lang){

		try{

			$sql = " select id,title_".$lang." as title,package_price,food_service,cancel_room,payment_online ";
			$sql .= ",extra_bed,max_person ,extra_price_children,extra_price_adults ";
			$sql .= ",detail_".$lang." as detail ,condition_".$lang." as conditions ";
			$sql .= " from packages where id='".$pack_id."'  and status=1 ";

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