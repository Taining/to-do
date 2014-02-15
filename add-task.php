<?php
	$page = "home";
	require "header.php";
	if(isset($_REQUEST['error'])) {
		echo('<p>Please fill in all required fields.</p>');
	}
?>
	<form action="add-task-to-database.php">
		<label>Task Description:<br><input type="text" name="dscrp"></label><br>
		<label>Estimated total time (30mins as one unit):<br><input type="text" name="total"></label><br>
		<input type="submit" name="submit" value="Add task">
	</form>
<?php
	require "footer.php";
?>	