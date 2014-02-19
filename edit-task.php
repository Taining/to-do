<?php
    session_save_path("sess");
	session_start();
	
	$page = "edit-task";
	
	require "config.inc";
	require "functions.php";
	require "header.php";
	
	// authentication of user
	$userid = authenticate();
	
	// get task info
	if (isset($_REQUEST['taskid']) && isset($_REQUEST['uid']) && $_REQUEST['uid']==$_SESSION['user']) {
		$taskid = $_REQUEST['taskid'];
	} else {
		header("Location: home.php");
		exit;
	}
	
	$errMessage = "";
	$dbconn = connectToDatabase($db_name, $db_user, $db_password);

	if (isset($_REQUEST['submit'])) {
		if($_REQUEST['dscrp'] == "" || $_REQUEST['total'] == "") {
			$errMessage = "Please fill in all required fields.";
		} elseif (!is_numeric($_REQUEST['total'])) {
			$errMessage = "Please enter a numeric time units.";
		} else {
			updateToDatabase($dbconn, $taskid, $_REQUEST['dscrp'], $_REQUEST['details'], $_REQUEST['total'], $_REQUEST['priority']);
		}

		if($errMessage != "") {
			$dscrp = $_REQUEST['dscrp'];
			$details = $_REQUEST['details'];
			$total = $_REQUEST['total'];
		}	
	}
	
	// get task info
	$query = "SELECT * FROM tasks WHERE taskid=$taskid";
	$result = pg_query($dbconn, $query);
	if ($result) {
		$row = pg_fetch_array($result);
		$dscrp = $row['dscrp'];
		$details = $row['details'];
		$total = $row['total'];
		$priority = $row['priority'];
	}
	
?>

<div class="container">
	<form method="POST">
		<table class="form" id="add-task">
		<tr>
			<td><div class="error" <?php if($errMessage=="") echo "hidden"; ?> ><?php echo $errMessage; ?></div></td>
		</tr>
		<tr>
			<td><label>Task Description:<br><input type="text" name="dscrp" value="<?php echo $dscrp; ?>" ></label></td>
		</tr>
		<tr>
			<td><label>Details:<br><textarea name='details' cols=70 rows=6><?php echo $details; ?></textarea></label></td>
		</tr>
		<tr>
			<td><label>Estimated total time (30mins as one unit):<br><input type="text" name="total" value="<?php echo $total; ?>"></label></td>
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