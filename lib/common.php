<?php 
require_once('class.phpmailer.php');
require_once('logger.php');
date_default_timezone_set('Asia/Bangkok');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE) ;
/*E_PARSE | E_WARNING | E_NOTICE , E_ALL | E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR*/


/*define variable globle for client;*/
$base_dir = "../";

function SendMail($receive,$sender,$subject,$message,$sender_name)
{
		$mail = new PHPMailer();
		$mail->SMTPOptions = array(
		'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
		));
		$mail->IsSMTP();

		$mail->Subject = $subject;
		$mail->MsgHTML($message);//body mail
		$mail->CharSet = "utf-8";
		$mail->Host="mail.baankunnan.com"; //mail.haven-huahin.com
		//$mail->Port = '25';
		$mail->SMTPAuth = true;
		$mail->IsHTML(true);
		//$mail->SMTPSecure = 'tls';
		//$mail->SMTPDebug = 2;
		$mail->Username = "contact@baankunnan.com"; 
		$mail->Password = "hmcKxJfCj"; 
		
		
		/*
		$mail->Subject = $subject;
		$mail->MsgHTML($message);//body mail
		$mail->CharSet = "utf-8";
		$mail->Host="mail.haven-huahin.com";
		$mail->SMTPAuth = true;
		$mail->SMTPDebug = 2;
		$mail->SMTPSecure = 'tls';
		$mail->IsHTML(true);
		$mail->Username = "system@haven-huahin.com"; 
		$mail->Password = "WvvolZ4v"; 
		*/
		//service@haven-huahin.com
		//LT8ANWg9
		$mail->SetFrom($sender, $sender_name);

		//list send email 
		foreach($receive as $to){
			$mail->AddAddress($to["email"],$to["alias"]);
		}
		
		if(!$mail->Send()) {
			//echo "Mailer Error: " . $mail->ErrorInfo;
			if($_SESSION["lang"]!="en")
				echo "<script>alert('Sorry!! Email Communication has interrupt');</script>";
			else 
				echo "<script>alert('Sorry !! Can't Send email .');</script>";
		} else {
			if($_SESSION["lang"]!="en"){
				foreach($receive as $to){
					log_info("Send Email is complete: " . $to["email"]);
				}
			}
				//echo "<script>alert('ข้อมูลของคุณส่งเรียบร้อยแล้วค่ะ');</script>";
			else 
				echo "<script>alert('Send email complete, Thankyou.');</script>";
		}
		
		//echo "<script>window.location.href='".$redirect."';</script>";
}

function replace_specialtext($message){
	
	$result = str_replace("'","\'",$message);
	
	return $result;
}

function GetParameter($id){
	
	$result = "";
	if(isset($_POST)){
		if(isset($_POST[$id]))
		{
			$result =$_POST[$id];
		}
	}
	
	if(isset($_GET)){
		if(isset($_GET[$id]))
		{
			$result =$_GET[$id];
		}
	}
	return $result;
}

function upload_image($source,$distination){
	
	if($source=="") return;
	
	if(file_exists($distination)){
		log_debug('exists file upload > '.$distination);
		return ;
	}
	
	if(move_uploaded_file($source,$distination))
	{
		log_debug('upload image success. > '.$distination);
	}
	else{
		log_debug('upload image Failed. >'.$distination);
	}
}

function full_date_format($date,$lang){


	switch($lang){
	case "en":

		$month = date("m",strtotime($date));
		$day =  date("d",strtotime($date));
		$year = date("Y",strtotime($date));
		$month_str = array("01"=>"January","02"=>"Faburary","03"=>"March","04"=>"May","05"=>"June","06"=>"June","07"=>"July","08"=>"August","09"=>"Sebtember","10"=>"Octuber","11"=>"November","12"=>"December");
		$month = $month_str[$month];
		$result = $month." ".$day." ".$year;

	break;
	case "th":
		$month = date("m",strtotime($date));
		$day =  date("d",strtotime($date));
		$year = date("Y",strtotime($date)) + 543;
		$month_str = array("01"=>"มกราคม","02"=>"กุมภาพันธ์","03"=>"มีนาคม","04"=>"เมษายน","05"=>"พฤษภาคม","06"=>"มิถุนายน","07"=>"กรกฏาคม","08"=>"สิงหาคม","09"=>"กันยายน","10"=>"ตุลาคม","11"=>"พฤศจิกายน","12"=>"ธันวาคม");
		$month = $month_str[$month];
		$result = $month." ".$day." ".$year;
	break;
	}

	return $result;

}

function datediff($startdate,$enddate){
	return (strtotime($enddate) - strtotime($startdate))/  ( 60 * 60 * 24 );
}

?>
