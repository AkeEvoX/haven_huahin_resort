<?php
session_start();
include("../lib/common.php");
include("../managers/reserve_manager.php");


$key = GetParameter("reserve_number");
$email = GetParameter("email");
if(!isset($key)) return;

$base = new Reserve_Manager();
$base = new Reserve_Manager();
$status = "2";//complete
$result = $base->payment_status($key,$status);

//echo "<script>alert('Thank you for reservation. \\n Thank you.');window.location='../quick_reservation.html';</script>";
header("Location: ../confirmation.html?reserve_id=".$key);
//echo "<script>alert('Cancel reserve success. \\n Thank you.');window.location='../cancellation.html?reserve_id=".$key."';</script>";
exit();

?>