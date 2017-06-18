<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");


$key = GetParameter("ref");
//$email = GetParameter("email");
if(!isset($key)) return;

$base = new Reserve_Manager();
$status = "1";//complete
$result = $base->payment_status($key,'online',$status);

//echo "<script>alert('Thank you for reservation. \\n Thank you.');window.location='../quick_reservation.html';</script>";
header("Location: ../quick_reservation.html");

exit();

?>