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
		
		$query = "INSERT INTO tasks(uid, taskid, dscrp, details, total, progress, ordering, createtime) VALUES($userid, $taskid, $1, $2, $3, 0, $ordering, $4)";
		$result = pg_prepare($dbconn, "my_query", $query);
		$result = pg_execute($dbconn, "my_query", array($dscrp, $details, $total, date("Y-m-d")));
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
		$dscrp = $_REQUEST['dscrp'];
		$details = $_REQUEST['details'];
		$total = $_REQUEST['total'];
	}
?>

<div class="container">
	<form method="POST">
		<table class="form" id="add-task">
		<tr>
		<?php
			if($error == 1) {
				echo("<tr id='error-add'><td class='error'>Please fill in all required fields.</td></tr>");
			}
		?>
			<td><label>Task Description:<br><input type="text" name="dscrp" value=<?php echo($dscrp) ?> ></label></td>
		</tr>
		<tr>
			<td><label>Details:<br><textarea name='details' cols=70 rows=6><?php echo($details) ?></textarea></label></td>
		</tr>
		<tr>
			<td><label>Estimated total time (30mins as one unit):<br><input type="text" name="total" value=<?php echo($total) ?>></label></td>
		</tr>
		<tr>
			<td>
				Priority:
				<select>
					<option value="1">Low</option>
					<option value="2" selected="1">Normal</option>
					<option value="3">High</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><input type="submit" name="submit" class="submit" value="Add task"></td>
		</tr>
		</table>
	</form>
</div>
<?php
	require "footer.php";
?>	