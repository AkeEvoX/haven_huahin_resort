<?php
require_once("../lib/database.php");
require_once("../lib/logger.php");
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
			die("initial about manager error : ". $e->getMessage());
		}
	}

	function __destruct(){ //page end
		$this->mysql->disconnect();
	}
	
	private static function generateRandomString($length=20) {
		
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		return $randomString;
		
	}

	
	function get_reserve_options($unique_key){
		
		try{

			$sql = "select r.unique_key,r.option_price,o.option_title,o.price ";
			$sql .= "from reserve_options r inner join service_options o on r.option_key = o.id where unique_key='".$unique_key."' ";
			$result = $this->mysql->execute($sql);

			log_warning("get_reserve_options > " . $sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get Reserve Options : ".$e->getMessage();
		}
		
	}
	
	function get_reserve_rooms($unique_key){
		
		try{

			$sql = " select rr.unique_key,r.* ,rt.title as type_title ";
			$sql .= " from reserve_rooms rr inner join rooms r on rr.room_key = r.id ";
			$sql .= " left join room_types rt on r.room_type = rt.id ";
			$sql .= " where rr.unique_key='".$unique_key."'  ";
			$result = $this->mysql->execute($sql);
			
			log_warning("get_reserve_rooms > " . $sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get Reserve Rooms : ".$e->getMessage();
		}
		
	}
	
	
	function get_reserve_info($unique_key){
		
		try{

			$sql = "select * ";
			$sql .= "from reserve_info where unique_key='".$unique_key."'  ";
			$result = $this->mysql->execute($sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get Reserve info: ".$e->getMessage();
		}
	}
	
	function insert_reserve($info,$customer,$payment,$summary){
		
		try{
			
			$start_date = $date = str_replace('/', '-', $info->date);
			$start_date = date('Y-m-d', strtotime($start_date));
			
			$end_date = $date = str_replace('/', '-', $info->date);
			$end_date = date('Y-m-d', strtotime($end_date));

			$unique_key = self::generateRandomString();

			$reserve_startdate = $start_date;
			$reserve_enddate = $end_date;
			$reserve_status= "1"; /*0=delete,1=complete,2=cancel*/
			$reserve_amount = $summary->total_amount;
			$reserve_charge = $summary->charge;
			$reserve_tax = $summary->tax;
			$reserve_net = $summary->net;
			$reserve_comment = $info->comment;
			$adults = $info->adults;
			$children = $info->children;
			$children_2 = $info->children_2;
			$code = $info->code;
			$night = $info->night;
			
			$email = $customer["email"];
			$title_name = $customer["title"];
			$first_name = $customer["fname"];
			$last_name = $customer["lname"];
			$prefix = $customer["prefix_mobile"];
			$mobile = $customer["mobile"];
			$birthdate = $customer["birthdate"];
			
			$payment_type = $payment["card_type"];
			$payment_number = $payment["card_number"];
			$payment_holder = $payment["card_holder"];
			$payment_expire = $payment["card_expire_month"]."/".$payment["card_expire_year"];
			$payment_secure = $payment["card_validate"];
			
			$create_date = "now()";
			
			$sql = "insert into reserve_info(unique_key,reserve_startdate,reserve_enddate,reserve_status,reserve_amount,reserve_charge,reserve_tax,reserve_net,reserve_comment,adults,children,children_2,night,acc_code ";
			$sql .= " ,email ,title_name,first_name,last_name,prefix,mobile,birthdate";
			$sql .= " ,payment_type,payment_number,payment_holder,payment_expire,payment_secure,create_date ) ";
			$sql .= "values('$unique_key','$reserve_startdate','$reserve_enddate','$reserve_status','$reserve_amount','$reserve_charge','$reserve_tax'  ";
			$sql .= " ,'$reserve_net','$reserve_comment',$adults,$children,$children_2,$night,'$code'  ";
			$sql .= " ,'$email','$title_name','$first_name','$last_name','$prefix','$mobile','$birthdate' ";
			$sql .= " ,'$payment_type','$payment_number','$payment_holder','$payment_expire','$payment_secure',$create_date); ";
			
			log_warning("insert_reserve > " . $sql);
			
			$this->mysql->execute($sql);
			
			$result = $unique_key;

			return $result;
		}
		catch(Exception $e){
			echo "Cannot Insert Reserve : ".$e->getMessage();
		}
		
	}

	function insert_customer($first,$last,$age,$email){
		try{
			
		}catch(Exception $e){
			
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

	function verify_cancel($unique_key,$email){

		try{

			$sql = "select count(0) as found ";
			$sql .= "from reserve_info where unique_key='".$unique_key."' and email='".$email."' ";
			$result = $this->mysql->execute($sql);

			log_warning("verify_cancel > " . $sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get Reserve Verify Cancel : ".$e->getMessage();
		}
	}

	function cancel_reserve($unique_key,$email){

		try{
			$update_date = 'now()';
			$sql = "update reserve_info set reserve_status='2', update_date=$update_date ";
			$sql .= " where unique_key='".$unique_key."' and email='".$email."' ; ";
			
			log_warning("cancel_reserve > " . $sql);
			
			$result = $this->mysql->execute($sql);
			
			return $result;

		}catch(Exception $e){
			echo "Cannot Cancel Reserve ".$unique_key." : ".$e->getMessage();
		}

	}

	
}

?>
