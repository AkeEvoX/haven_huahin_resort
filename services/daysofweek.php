<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

$result = "";
if(isset($_SESSION["info"])){
	
	$date = $_SESSION["info"]["check_in_date"]; 
	$days = get_days_th(date('w',strtotime($date.'-2 days'))) ." ".date('d',strtotime($date.'-2 days'))." ".get_month_name_th(date('m',strtotime($date.'-2 days')));
	$result[] = array("format"=>$days,"date"=>date('d',strtotime($date.'-2 days')));	
	$days = get_days_th(date('w',strtotime($date.'-1 days'))) ." ".date('d',strtotime($date.'-1 days'))." ".get_month_name_th(date('m',strtotime($date.'-1 days')));
	$result[] = array("format"=>$days,"date"=>date('d',strtotime($date.'-1 days')));
	$days = get_days_th(date('w',strtotime($date.'0 days'))) ." ".date('d',strtotime($date.'0 days'))." ".get_month_name_th(date('m',strtotime($date.'0 days')));
	$result[] = array("format"=>$days ,"date"=>date('d',strtotime($date.'0 days')));
	$days = get_days_th(date('w',strtotime($date.'+1 days'))) ." ".date('d',strtotime($date.'+1 days'))." ".get_month_name_th(date('m',strtotime($date.'+1 days')));
	$result[] = array("format"=>$days ,"date"=>date('d',strtotime($date.'+1 days')));
	$days = get_days_th(date('w',strtotime($date.'+2 days'))) ." ".date('d',strtotime($date.'+2 days'))." ".get_month_name_th(date('m',strtotime($date.'+2 days')));
	$result[] = array("format"=>$days ,"date"=>date('d',strtotime($date.'+2 days')));
	
}

echo json_encode(array("data"=>$result));
/*
$date =date('Y-m-d'); 
echo "day of week<br/>";
echo "date :" .$date."<br/>";
echo "day full:" .  date('l', strtotime($date))."<br/>";
echo "day short :" .  date('D', strtotime($date))."<br/>";
echo "day number :" .  date('w', strtotime($date))."<br/>";
echo "date 1 : ".date('l',strtotime('-2 days'))." / ".date('Y-m-d',strtotime($date.'-2 days'))."<br/>";
echo "date 2 : ".date('l',strtotime('-1 days'))." / ".date('Y-m-d',strtotime($date.'-1 days'))."<br/>";
echo "date 3 : ".date('l',strtotime('0 days'))." / ".date('Y-m-d',strtotime($date.'0 days'))."<br/>";
echo "date 4 : ".date('l',strtotime('+1 days'))." / ".date('Y-m-d',strtotime($date.'+1 days'))."<br/>";
echo "date 5 : ".date('l',strtotime('+2 days'))." / ".date('Y-m-d',strtotime($date.'+2 days'))."<br/>";
*/

function get_days_th($num){
	if($num=="0") return "อา.";
	else if($num=="1") return "จ.";
	else if($num=="2") return "อ.";
	else if($num=="3") return "พ.";
	else if($num=="4") return "พฤ.";
	else if($num=="5") return "ศ.";
	else if($num=="6") return "ส.";
}
function get_days_en($num){
	if($num=="0") return "Sun";
	else if($num=="1") return "Mon";
	else if($num=="2") return "Tue";
	else if($num=="3") return "Wed";
	else if($num=="4") return "Thu";
	else if($num=="5") return "Fri";
	else if($num=="6") return "Sat";
}
function get_month_full_name_th($num){
	
	if($num=="01") return "";
	else if($num=="02") return "กุมภาพันธ์";
	else if($num=="03") return "มีนาคม";
	else if($num=="04") return "เมนษายน";
	else if($num=="05") return "พฤษภาคม";
	else if($num=="06") return "มิถุนายน";
	else if($num=="07") return "กรกฏาคม";
	else if($num=="08") return "สิงหาคม";
	else if($num=="09") return "กันยายน";
	else if($num=="10") return "ตุลาคม";
	else if($num=="11") return "พฤศจิกายน";
	else if($num=="12") return "ธันวาคม";
}
function get_month_name_th($num){
	if($num=="01") return "ม.ค.";
	else if($num=="02") return "ก.พ.";
	else if($num=="03") return "มี.ค.";
	else if($num=="04") return "เม.ย.";
	else if($num=="05") return "พ.ค.";
	else if($num=="06") return "มิ.ย.";
	else if($num=="07") return "ก.ค";
	else if($num=="08") return "ส.ค.";
	else if($num=="09") return "ก.ย.";
	else if($num=="10") return "ต.ค";
	else if($num=="11") return "พ.ย.";
	else if($num=="12") return "ธ.ค.";
}
?>