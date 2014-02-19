<?php
    session_save_path("sess");
	session_start();
	
	$page = "home";
	
	require "config.inc";
	require "header.php";
	require "functions.php";
	
	$userid = authenticate();
	
	$dscrp = "";
	$details = "";
	$total = "";
	$errMessage = "";
	
	if (isset($_REQUEST['submit'])) {
		if($_REQUEST['dscrp'] == "" || $_REQUEST['total'] == "") {
			$errMessage = "Please fill in all required fields.";
		} elseif (!is_numeric($_REQUEST['total'])) {
			$errMessage = "Please enter a numeric time units.";
		} else {
			addToDatabase($_REQUEST['dscrp'], $_REQUEST['details'], $_REQUEST['total'], $db_name, $db_user, $db_password, $_REQUEST['priority']);
		}

		if($errMessage != "") {
			$dscrp = $_REQUEST['dscrp'];
			$details = $_REQUEST['details'];
			$total = $_REQUEST['total'];
		}	
	}
?>

<div class="container">
	<form method="POST">
		<table class="form" id="add-task">
		<tr>
			<td><div class="error" <?php if($errMessage=="") echo "hidden"; ?> ><?php echo $errMessage; ?></div></td>
		</tr>
		<tr>
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
					<option value="3">Low</option>
					<option value="2" selected="1">Normal</option>
					<option value="1">High</option>
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