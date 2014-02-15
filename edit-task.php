<?php
    session_save_path("sess");
	session_start();
	
	function updateToDatabase($dbconn, $taskid, $dscrp, $details, $total) {
		$query = "UPDATE tasks SET dscrp=$1, details=$2, total=$3 WHERE taskid=$4";
		$result = pg_prepare($dbconn, "my_query", $query);
		$result = pg_execute($dbconn, "my_query", array($dscrp, $details, $total, $taskid));
		if($result) {
			header("Location: home.php");
		} else {
			echo("Failed to edit task.");
			return;
		}
	}
	
	// authentication of user
	if (isset($_SESSION['user'])) {
		$userid = $_SESSION['user'];
	} else {
		header("Location: login.php");
		exit;
	}
	
	// get task id
	if(!isset($_REQUEST['taskid'])) {
		header("Location: home.php");
		exit;
	} else {
		$taskid = $_REQUEST['taskid'];
	}
	
	$page = "edit-task";
	
	require "config.inc";
	require "header.php";
		
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if (!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	
	if(isset($_REQUEST['taskid']) && isset($_REQUEST['dscrp']) && isset($_REQUEST['details']) && isset($_REQUEST['total'])) {
		updateToDatabase($dbconn, $_REQUEST['taskid'], $_REQUEST['dscrp'], $_REQUEST['details'], $_REQUEST['total']);
	}
	
	// get task info
	$query = "SELECT * FROM tasks WHERE taskid=$taskid";
	$result = pg_query($dbconn, $query);
	$row = pg_fetch_array($result);
	$dscrp = $row['dscrp'];
	$details = $row['details'];
	$total = $row['total'];
?>

	<form>
		<input type="hidden" name="taskid" value=<?php echo($taskid); ?> >
		<label>Task Description:<br>
			<input type="text" name="dscrp" value=<?php echo($dscrp); ?>>
		</label>
		<br>
		<label>Details:<br>
			<textarea name='details' cols=70 rows=6>
				<?php echo($details); ?>
			</textarea>
		</label>
		<br>
		<label>Estimated total time (30mins as one unit):<br>
			<input type="text" name="total" value=<?php echo($total); ?>>
		</label>
		<br>
		<input type="submit" name="submit" value="Update task information">
		<button><a href='home.php'>Go back</a></button>
	</form>