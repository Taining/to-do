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
	$query = "UPDATE tasks SET dscrp=$1, details=$2, total=$3 WHERE taskid=$_REQUEST[taskid]";
	$result = pg_prepare($dbconn, "my_query", $query);
	$result = pg_execute($dbconn, "my_query", array($_REQUEST['dscrp'], $_REQUEST['details'], $_REQUEST['total']));
	if($result) {
		header("Location: home.php");
	} else {
		echo("Failed to edit task.");
		exit;
	}
?>