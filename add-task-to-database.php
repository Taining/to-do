<?php
	session_save_path("sess");
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
<?php
	
	require "config.inc";
	if(!isset($_REQUEST['dscrp']) || !(isset($_REQUEST['total']))) {
		echo('<META HTTP-EQUIV="Refresh" Content="0; URL=add-task.php">');
		exit;
	} 
	$userid = $_SESSION['user'];
	//userid, task-dscrp, total-time, progress
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	$query = "SELECT COUNT(*) FROM tasks;";
	$result=pg_query($dbconn, $query);
	$row = pg_fetch_row($result);
	$taskid = $row[0];
	$query = "INSERT INTO tasks(uid, taskid, dscrp, total, progress) VALUES($userid, $taskid, '$_REQUEST[dscrp]', $_REQUEST[total], 0);";
	$result=pg_query($dbconn, $query);
	if($result) {
		echo('<META HTTP-EQUIV="Refresh" Content="0; URL=home.php">');
	} else {
		echo("Failed to add task to database.");
		exit;
	}
?>
	</head>
	<body>
		
	</body>
</html>