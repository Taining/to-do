<?php
	require "config.inc";
	if(!isset($_REQUEST['taskid']) || !isset($_REQUEST['dscrp']) || !isset($_REQUEST['details']) || !isset($_REQUEST['total'])) {
		header("Location: edit-task.php");
		exit;
	} 
	//userid, task-dscrp, details, total-time, progress
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	$query = "UPDATE tasks SET dscrp='$_REQUEST[dscrp]', details='$_REQUEST[details]', total=$_REQUEST[total] WHERE taskid=$_REQUEST[taskid]";
	$result = pg_query($dbconn, $query);
	if($result) {
		header("Location: home.php");
	} else {
		echo("Failed to edit task.");
		exit;
	}
?>