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
		header("Location: add-task.php");
		exit;
	} else if ((trim($_REQUEST['dscrp']) == "") || (trim($_REQUEST['total']) == "")) {
		header("Location: add-task.php?dscrp=$_REQUEST[dscrp]&total=$_REQUEST[total]&details=$_REQUEST[details]&error=1");
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
	$query = "INSERT INTO tasks(uid, taskid, dscrp, details, total, progress, ordering) VALUES($userid, $taskid, $1, $2, $3, 0, $taskid + 1)";
	$result = pg_prepare($dbconn, "my_query", $query);
	$result = pg_execute($dbconn, "my_query", array($_REQUEST['dscrp'], $_REQUEST['details'], $_REQUEST['total']));
	if($result) {
		header("Location: home.php");
	} else {
		echo("Failed to add task to database.");
		exit;
	}
?>
	</head>
	<body>
		
	</body>
</html>