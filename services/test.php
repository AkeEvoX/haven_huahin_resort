<?
include("../lib/common.php");
$date1="2017/07/29";
$date2="2017/07/28";
$diff=datediff($date1,$date2);
echo "current date = " . date('Y/m/d');
echo "date diff = " . $diff;
echo "<br/>";
//$expire = date("d-m-Y", strtotime('2017-10-13 00:00:00'));
$expire = date("d/m/Y", strtotime('2017-10-13'));
echo "convert exire date to " . $expire ;

?>