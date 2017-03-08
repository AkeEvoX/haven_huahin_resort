<?
//default select local db
//uncommenct $source for connect server database
$source = "";

if($source=="server")
{
	$mysqli_host="192.168.1.5";
	$mysqli_user="remotedb"; //super admin  user :root / pass:root
	$mysqli_pass="remotedb";//P@ssw0rd
	$mysqli_db="stardb";
}
else
{
	$mysqli_host="127.0.0.1";
	$mysqli_user="root"; //super admin  user :root / pass:root
	$mysqli_pass="P@ssw0rd";//P@ssw0rd
	$mysqli_db="startdb";
}

	$consqli = new Mysqli($mysqli_host, $mysqli_user, $mysqli_pass, $mysqli_db) ;
	$consqli->set_charset("utf8");

	// Check connection
	if ($consqli->connect_error) {
		die("Connection failed: " . $consqli->connect_error);
	}


function closedb($consli,$result)
{
	mysqli_free_result($result);
	mysqli_close($consli);
}

?>