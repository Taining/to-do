<?php
	require "config.inc";
		if (isset($_REQUEST['makeProgress'])) {
			$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
		if (!$dbconn){
			echo("Can't connect to the database");	
			exit;
		}
		$taskid = $_REQUEST['makeProgress'];
		$get_progress_query = "SELECT progress, uid FROM tasks WHERE taskid=$taskid";
		$result = pg_query($dbconn, $get_progress_query);
		$row = pg_fetch_row($result);
		
		// progress++
		$progress = $row[0];
		$progress += 1;
		$update_query = "UPDATE tasks SET progress=$progress WHERE taskid=$taskid";
		pg_query($dbconn, $update_query);
		
		// done++
		$uid = $row[1];
		$get_done_query = "SELECT done FROM appuser WHERE uid=$uid";
		$result = pg_query($dbconn, $get_done_query);
		$row = pg_fetch_row($result);
		$done = $row[0];
		$done += 1;
		$update_query = "UPDATE appuser SET done=$done WHERE uid=$uid";
		pg_query($dbconn, $update_query);
	}
	header("Location: home.php");
?>