<?php
require_once("../lib/database.php");

class AboutManager{
	
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

	function getItem($lang){
		
		try{

			$sql = "select id,title_".$lang." as title,detail_".$lang." as detail ,type,link_".$lang." as link,update_date ";
			$sql .= "from about where active=1 order by create_date desc ";
			$result = $this->mysql->execute($sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get  About View: ".$e->getMessage();
		}
		
	}
	
	function getInfo($id){
		
		try{

			$sql = "select id,title_th,title_en ,detail_th,detail_en,link_th,link_en ";
			$sql .= "from about where active=1 order by create_date desc limit 1 ";
			$result = $this->mysql->execute($sql);
			
			return  $result;
		}
		catch(Exception $e){
			echo "Cannot Get  About info: ".$e->getMessage();
		}
		
	}
	
	function update_about($items){
		
		try{
			$id = $items["id"];
			$title_th = $items["title_th"];
			$title_en = $items["title_en"];
			$detail_th = $items["detail_th"];
			$detail_en = $items["detail_en"];
			$link_th = $items["link_th"];
			$link_en = $items["link_en"];
			$update_by = $_SESSION["profile"]->id;
			$update_date = "current_timestamp";
			
			$sql = "update about set  ";
			$sql .= "title_th='$title_th' ";
			$sql .= ",title_en='$title_en' ";
			$sql .= ",detail_th='$detail_th' ";
			$sql .= ",detail_en='$detail_en' ";
			$sql .= ",link_th='$link_th' ";
			$sql .= ",link_en='$link_en' ";
			$sql .= ",update_by=$update_by ";
			$sql .= ",update_date=$update_date ";
			$sql .= "where id=$id ;";
			
			//echo $sql."<br/>";
			log_warning("update_about > " . $sql);
			
			$result = $this->mysql->execute($sql);
			return $result;
		}
		catch(Exception $e){
			echo "Cannot Update AboutManager Info: ".$e->getMessage();
		}
	}

	function insert_reserve($data){
		try{
			$title_th = $items["title_th"];
			$title_en = $items["title_en"];
			$detail_th = $items["detail_th"];
			$detail_en = $items["detail_en"];
			$link_th = $items["link_th"];
			$link_en = $items["link_en"];
			$update_by = $_SESSION["profile"]->id;
			$update_date = "current_timestamp";
			
			$sql = "update about set  ";
			$sql .= "title_th='$title_th' ";
			$sql .= ",title_en='$title_en' ";
			$sql .= ",detail_th='$detail_th' ";
			$sql .= ",detail_en='$detail_en' ";
			$sql .= ",link_th='$link_th' ";
			$sql .= ",link_en='$link_en' ";
			$sql .= ",update_by=$update_by ";
			$sql .= ",update_date=$update_date ";
			$sql .= "where id=$id ;";
			
			//echo $sql."<br/>";
			log_warning("insert_reserve > " . $sql);
			
			$this->mysql->execute($sql);

			$result = $this->mysql->newid();

			return $result;
		}
		catch(Exception $e){
			echo "Cannot Insert Reserve : ".$e->getMessage();
		}
	}

	function insert_customer($first,$last,$age,$email){

	}

	function insert_payment($card,$month,$year,$security){

	}

	
}

?>