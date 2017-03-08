<?php

function ErrorHandler($num,$str,$file,$line,$context=null)
{
	ExceptionHandler(new ErrorException($str,0,$num,$file,$line));
}

function FatalHandler(){

	$error = error_get_last();
	if($error["type"]==E_ERROR)
		ErrorHandler($error["type"],$error["message"],$error["file"],$error["line"]);
}

function ExceptionHandler(Exception $e)
{
	$config = parse_ini_file("config.ini");

	if($config["debug"]==true)
	{

        print '{"Type":"'.get_class($e).'"';
        print ',"Message":"'.$e->getMessage().'"';
        print ',"File":"'.$e->getFile().'"';
        print ',"Line":"'.$e->getLine().'"';
        print '}';
	}
	else
	{
		$errtime = new Date("Y-m-D H:i:s");
		$message = "Date: {$errtime}; Type: " .get_class($e).";message: {$e->getMessage()} ; File: {$e->getFile()}; Line: {$e->getLine()};";
		file_get_contents($config["logdir"]."error_".new Date("Y-m-D").".log",$message,PHP_EOL,FILE_APPEND);
		//header("Location:{$config["error_page"]} ");
	}

	exit();
}

register_shutdown_function("FatalHandler");
set_error_handler("ErrorHandler");
set_exception_handler("ExceptionHandler");
ini_set("display_errors","off");
error_reporting(E_ALL);

?>