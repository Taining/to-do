<?php
    session_save_path("sess");
	session_start();
	
	$page = "edit-task";

	function updateToDatabase($dbconn, $taskid, $dscrp, $details, $total, $priority) {
		if ((trim($_REQUEST['dscrp']) == "") || (trim($_REQUEST['total']) == "")) {
			$dscrp=$_REQUEST['dscrp'];
			$total=$_REQUEST['total'];
			$details=$_REQUEST['details'];
			$error = 1;
			return $error;
		}
		$query = "UPDATE tasks SET dscrp=$1, details=$2, total=$3 priority=$4 WHERE taskid=$5";
		$result = pg_prepare($dbconn, "my_query", $query);
		$result = pg_execute($dbconn, "my_query", array($dscrp, $details, $total, $priority, $taskid));
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
	
	//get task info
	if (isset($_REQUEST['taskid'])) {
		$taskid = $_REQUEST['taskid'];
	} else {
		header("Location: home.php");
		exit;
	}

	require "config.inc";
	require "header.php";
	
	$error = 0;
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if (!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	if (isset($_REQUEST['submit'])) {
		if(isset($_REQUEST['taskid']) && isset($_REQUEST['dscrp']) && isset($_REQUEST['details']) && isset($_REQUEST['total']) && is_numeric($_REQUEST['total'])) {
			$error = updateToDatabase($dbconn, $_REQUEST['taskid'], $_REQUEST['dscrp'], $_REQUEST['details'], $_REQUEST['total'], $_REQUEST['priority']);
		}
	}
	
	if($error == 1) {
		$dscrp = $_REQUEST['dscrp'];
		$details = $_REQUEST['details'];
		$total = $_REQUEST['total'];
		$priority = $_REQUEST['priority'];
	} else {
		// get task info
		$query = "SELECT * FROM tasks WHERE taskid=$taskid";
		$result = pg_query($dbconn, $query);
		$row = pg_fetch_array($result);
		$dscrp = $row['dscrp'];
		$details = $row['details'];
		$total = $row['total'];
		$priority = $row['priority'];
	}
?>

<!--
	<form class="form" id="edit-task">
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
-->
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
				<select name="priority">
					<option value="3" <?php if($priority==3) echo "selected=1"; ?> >Low</option>
					<option value="2" <?php if($priority==2) echo "selected=1"; ?> >Normal</option>
					<option value="1" <?php if($priority==1) echo "selected=1"; ?> >High</option>
				</select>
			</td>
		</tr>
		<tr class='aux'>
			<td><button id="go-back"><a href='home.php'>Go back</a></button></td>
			<td><input type="submit" name="submit" class="submit" value="Update"></td>
		</tr>
		</table>
	</form>
</div>

<?php require 'footer.php' ?>