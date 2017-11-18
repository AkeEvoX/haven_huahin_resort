<?php
session_start();

$result_code="0";
$result = $_SESSION["user"];

echo json_encode(array("result"=> $result ,"code"=>$result_code));

?>