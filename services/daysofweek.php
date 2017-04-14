<?php
session_start();
date_default_timezone_set('Asia/Bangkok');

$result = "";
if(isset($_SESSION["info"])){
	
	$get_date = $date = str_replace('/', '-', $_SESSION["info"]["date"]);
	$select_date = date("Y-m-d",strtotime($get_date)); //example:: 08/14/2017 (month/day/year)
	for($i=-2 ; $i<=2;$i++){
		$date_add = $i.' days';
		$week =get_days_th(date('w',strtotime($select_date.$date_add))) ;
		$day =date('d',strtotime($select_date.$date_add));
		$month =get_month_name_th(date('m',strtotime($select_date.$date_add)));
		$format_date = $week." ".$day." ".$month; //ex: พฤ. 06 เม.ย.
		$full_date = date('Y-m-d',strtotime($select_date.$date_add));

		//find reserve type room 
		

		$result[] = array("format_date"=>$format_date
			,"full_date"=>$full_date
			,"date_add"=>$date_add
			,"deluxe"=>0
			,"superior"=>0
			,"villa"=>0
			,"viall_at_sea"=>0);

	}

	
}

echo json_encode(array("data"=>$result,"transform_date"=>$select_date,"select_date"=>$_SESSION["info"]["date"]
,"check-in"=>$_SESSION["info"]["check_in_date"]));
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