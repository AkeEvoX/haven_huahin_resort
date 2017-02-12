<?php 
require_once('class.phpmailer.php');
date_default_timezone_set('Asia/Bangkok');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE) ;
/*E_PARSE | E_WARNING | E_NOTICE , E_ALL | E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR*/


/*define variable globle for client;*/
$base_dir = "../";
	
if(!isset($_SESSION["lang"]) or empty($_SESSION["lang"]) or $_SESSION["lang"]== "" ){
	$_SESSION["lang"] = "en";
} 

if(isset($_SESSION["lang"]))
	$lang = $_SESSION["lang"];


function SendMail($redirect,$receive,$sender,$subject,$message,$custname)
{
		$mail = new PHPMailer();

		$mail->IsSMTP();

		$mail->Subject = $subject;
		$mail->MsgHTML($message);//body mail
	
		$mail->CharSet = "utf-8";
		$mail->Host="mail.baankunnan.com";
		$mail->SMTPAuth = true;
		$mail->IsHTML(true);
		$mail->Username = "contact@baankunnan.com"; 
		$mail->Password = "hmcKxJfCj"; 
		$mail->SetFrom($sender, $custname);
		//$mail->SetFrom("contact@baankunnan.com", "starsanitaryware.com");
		$mail->AddBcc("contact@baankunnan.com", "contact :: starsanitaryware");
		//$mail->AddReplyTo("mail@andamantaxis.com", "admin");
		
		/*list receive email */
		foreach($receive as $key=>$item){
			$mail->AddAddress($item->email,$item->alias); 
		}
		
		//$mail->AddAddress("sales@starsanitaryware.com"); 
		
		if(!$mail->Send()) {
			//echo "Mailer Error: " . $mail->ErrorInfo;
			if($_SESSION["lang"]!="en")
				echo "<script>alert('ขออภัยระบบขัดข้องค่ะ');</script>";
			else 
				echo "<script>alert('Sorry !! , Send Feedback Error.');</script>";
		} else {
			if($_SESSION["lang"]!="en")
				echo "<script>alert('ข้อมูลของคุณส่งเรียบร้อยแล้วค่ะ');</script>";
			else 
				echo "<script>alert('Thankyou for feedback.');</script>";
		}
		
		echo "<script>window.location.href='".$redirect."';</script>";
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

?>
