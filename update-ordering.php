<?php
	session_save_path("sess");
	session_start();
	
	$tasks = array();
	
	require "config.inc";
	
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	
	$tasks_query = "SELECT taskid FROM tasks WHERE uid = $_SESSION[user]";
	$tasks_result = pg_query($dbconn, $tasks_query);
	
	while ($row = pg_fetch_row($tasks_result)) {
		$taskid = $row[0];
		$order = $_REQUEST[$taskid];
		
		if(isset($tasks[$order])) {
			header("Location: ordering.php?error=1");
			exit;
		}
		
		$tasks[$order] = 1;
		
		$query = "UPDATE tasks SET ordering=$order WHERE taskid = $taskid";
		pg_query($dbconn, $query);
	}
	
	header("Location: home.php");
?>