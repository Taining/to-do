<?php
	if(!isset($_REQUEST['undo'])) {
		header("Location: home.php");
		exit;	
	} else {
		$taskid = $_REQUEST['undo'];
	}
	require "config.inc";
	
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	
	$query = "SELECT progress FROM tasks WHERE taskid=$taskid";
	$result = pg_query($dbconn, $query);
	if(!result) {
		echo("Can't connect to the database");	
		exit;
	}
	$row = pg_fetch_array($result);
	$progress = $row['progress'];
	$progress = $progress - 1;
	
	$query = "UPDATE tasks SET progress = $progress WHERE taskid=$taskid";
	pg_query($dbconn, $query);
	
	header("Location: home.php");
?>
	