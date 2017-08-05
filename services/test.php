<?
include("../lib/common.php");
$date1="2017/07/29";
$date2="2017/07/28";
$diff=datediff($date1,$date2);
echo "current date = " . date('Y/m/d');
echo "date diff = " . $diff;
echo "<br/>";


?>