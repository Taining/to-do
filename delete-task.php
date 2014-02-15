<?php
	if(!isset($_REQUEST['taskid'])) {
		header("Location: home.php");
		exit;
	}
	require "config.inc";
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	$query = "DELETE FROM tasks WHERE taskid=$_REQUEST[taskid]";
	$result = pg_query($dbconn, $query);
	if($result) {
		header("Location: home.php");
		exit;
	} else {
		echo("Can't delete the task or the task does not exist");
	}
?>
		