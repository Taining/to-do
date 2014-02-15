<?php
    session_save_path("sess");
	session_start();
	
	$page = "home";
	
	require "config.inc";
	require "header.php";
	
	function addToDatabase(&$dscrp, &$details, &$total, &$error, $db_name, $db_user, $db_password) {
		if ((trim($_REQUEST['dscrp']) == "") || (trim($_REQUEST['total']) == "")) {
			$dscrp=$_REQUEST['dscrp'];
			$total=$_REQUEST['total'];
			$details=$_REQUEST['details'];
			$error = 1;
			return;
		}
		$userid = $_SESSION['user'];
		//userid, task-dscrp, total-time, progress
		$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
		if(!$dbconn){
			echo("Can't connect to the database");	
			return;
		}
		
		$query = "SELECT MAX(taskid) FROM tasks;";
		$result=pg_query($dbconn, $query);
		$row = pg_fetch_row($result);
		$taskid = $row[0] + 1;
		
		$query = "SELECT COUNT(*) FROM tasks;";
		$result=pg_query($dbconn, $query);
		$row = pg_fetch_row($result);
		$ordering = $row[0] + 1;
		
		$query = "INSERT INTO tasks(uid, taskid, dscrp, details, total, progress, ordering) VALUES($userid, $taskid, $1, $2, $3, 0, $ordering)";
		$result = pg_prepare($dbconn, "my_query", $query);
		$result = pg_execute($dbconn, "my_query", array($dscrp, $details, $total));
		if($result) {
			header("Location: home.php");
		} else {
			echo("Failed to add task to database.");
			return;
		}
	}
	
	$dscrp = "";
	$details = "";
	$total = "";
	$error = 0;
	
	if(isset($_REQUEST['dscrp']) && isset($_REQUEST['total'])) {
		addToDatabase($_REQUEST['dscrp'], $_REQUEST['details'], $_REQUEST['total'], $error, $db_name, $db_user, $db_password);
	}
	
	if($error == 1) {
		echo('<p>Please fill in all required fields.</p>');
		$dscrp = $_REQUEST['dscrp'];
		$details = $_REQUEST['details'];
		$total = $_REQUEST['total'];
	}
?>
	<form>
		<label>Task Description:<br><input type="text" name="dscrp" value=<?php echo($dscrp) ?> ></label><br>
		<label>Details:<br><textarea name='details' cols=70 rows=6><?php echo($details) ?></textarea></label><br>
		<label>Estimated total time (30mins as one unit):<br><input type="text" name="total" value=<?php echo($total) ?>></label><br>
		<input type="submit" name="submit" value="Add task">
	</form>
<?php
	require "footer.php";
?>	