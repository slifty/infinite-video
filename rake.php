<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once("conf.php");
require_once("models/DbConn.php");

// Get connection
$mysqli = DbConn::connect();
if(!$mysqli || $mysqli->connect_error) {
	echo("Could not connect to DB.  Did you follow the install instructions in README?\n");
	die();
}

// Look up installed version
$result = $mysqli->query("select appinfo.version as version
				  			from appinfo");

if(!$result || $result->num_rows == 0)
	$version = 0;
else {
	$resultArray = $result->fetch_assoc();
	$version = $resultArray['version'];
	$result->free();
}

echo("Current Version: ".$version."\n");
switch($version) {
	case 0: // Never installed before
		echo("Fresh Install...\n");
		echo("Creating appinfo table\n");
		$mysqli->query("CREATE TABLE appinfo (version varchar(8))") or print($mysqli->error);
		$mysqli->query("INSERT INTO appinfo (version) values('1');") or print($mysqli->error);
			
	case 1: // First update
		echo("Creating videos table\n");
		$mysqli->query("CREATE TABLE videos (id int auto_increment primary key,
											url text,
											date_created datetime)") or print($mysqli->error);
		echo("Creating clips table\n");
		$mysqli->query("CREATE TABLE clips (id int auto_increment primary key,
											video_id int,
											start float(8,2),
											stop float(8,2))") or print($mysqli->error);
		echo("Creating users table\n");
		$mysqli->query("CREATE TABLE users (id int auto_increment primary key,
											username varchar(64),
											password char(64),
											type text,
											date_created datetime)") or print($mysqli->error);

		echo("Updating app version\n");
		$mysqli->query("UPDATE appinfo set version ='2';") or print($mysqli->error);
		
	default:
		echo("Finished updating the schema\n");
}
?>