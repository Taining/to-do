<?php
	$page = "home";
	require "header.php";
	if(isset($_REQUEST['error'])) {
		echo('<p>Please fill in all required fields.</p>');
		$dscrp = $_REQUEST['dscrp'];
		$details = $_REQUEST['details'];
		$total = $_REQUEST['total'];
	} else {
		$dscrp = "";
		$details = "";
		$total = "";
	}
?>
	<form action="add-task-to-database.php">
		<label>Task Description:<br><input type="text" name="dscrp" value=<?php echo($dscrp) ?> ></label><br>
		<label>Details:<br><textarea name='details' cols=70 rows=6><?php echo($details) ?></textarea></label><br>
		<label>Estimated total time (30mins as one unit):<br><input type="text" name="total" value=<?php echo($total) ?>></label><br>
		<input type="submit" name="submit" value="Add task">
	</form>
<?php
	require "footer.php";
?>	