<?php
/*logging for tracking 
type : info , debug , error ,warning 
*/
	function log_info($msg)
	{
		$msg = " [INFO] " . $msg;
		$filename = "../logs/info_".date('Y-m-d').".log";
		log_file($msg,$filename);
	}

	function log_debug($msg)
	{
		$msg = " [DEBUG] " . $msg;
		$filename = "../logs/debug_".date('Y-m-d').".log";
		log_file($msg,$filename);
	}

	function log_error($msg)
	{
		$msg =" [ERROR] " . $msg;
		$filename = "../logs/error_".date('Y-m-d').".log";
		log_file($msg,$filename);
	}

	function log_warning($msg)
	{
		$msg = " [WARN] " . $msg;
		$filename = "../logs/warning_".date('Y-m-d').".log";
		log_file($msg,$filename);
		
	}

	function log_file($msg,$filename)
	{
		
		$msg  =  date('Y-m-d H:i:s') . $msg ;
		checkdir($filename);
		file_put_contents($filename,$msg."\n",FILE_USE_INCLUDE_PATH | FILE_APPEND);
	}
	
	function checkdir($filename)
	{
		
		$parts = explode('/', $filename);
        $file = array_pop($parts);
        $dir = '';
		
        foreach($parts as $part)
		{
			
			if($part=="..")
				$dir="..";
			else
				$dir .= "/$part";
		
			$checkDir = $dir;
			if(!is_dir($checkDir)) 
			{
				mkdir($checkDir,0777,true);
			}
		}
	}
?>