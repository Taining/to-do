<?php
    session_save_path("sess");
	session_start();
	
	if(!isset($_REQUEST['taskid'])) {
		header("Location: home.php");
		exit;
	} else {
		$taskid = $_REQUEST['taskid'];
	}
	
	$page = "edit-task";
	
	if (isset($_SESSION['user'])) {
		$userid = $_SESSION['user'];
	} else {
		header("Location: login.php");
		exit;
	}
	
	require "config.inc";
	require "header.php";
		
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if (!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	$query = "SELECT * FROM tasks WHERE taskid=$taskid";
	$result = pg_query($dbconn, $query);
	$row = pg_fetch_array($result);
	$dscrp = $row['dscrp'];
	$details = $row['details'];
	$total = $row['total'];
?>
	<form action="update-task-in-database.php">
		<input type="hidden" name="taskid" value=<?php echo($taskid); ?> >
		<label>Task Description:<br><input type="text" name="dscrp" value=<?php echo($dscrp); ?>></label><br>
		<label>Details:<br><textarea name='details' cols=70 rows=6><?php echo($details); ?></textarea></label><br>
		<label>Estimated total time (30mins as one unit):<br><input type="text" name="total" value=<?php echo($total); ?>></label><br>
		<input type="submit" name="submit" value="Update task information">
		<button><a href='home.php'>Go back</a></button>
	</form>