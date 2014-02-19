<?php
	session_save_path("sess");
	session_start();
	
	$page = "ordering";
	
	require "functions.php";
	require "config.inc";
	require "header.php";
	
	$userid = authenticate();
?>
	
	<div class=container>
	
<?php
	$dbconn = connectToDatabase($db_name, $db_user, $db_password);
	
	// update ordering to database
	if(isset($_REQUEST['submit'])) {
		defineOrdering($dbconn, $_REQUEST);
		exit;
	}
	
	// get ordering info according to current sorting method
	$query = "";
	switch ($_SESSION['sort']) {
		case "none":
			$query = "SELECT taskid, dscrp, ordering FROM tasks WHERE uid=$_SESSION[user] AND progress < total ORDER BY ordering";
			break;
		case "createtime":
			$query = "SELECT taskid, dscrp, ordering FROM tasks WHERE uid=$_SESSION[user] AND progress < total ORDER BY createtime, dscrp";
			break;	
		case "priority":
			$query = "SELECT taskid, dscrp, ordering FROM tasks WHERE uid=$_SESSION[user] AND progress < total ORDER BY priority, dscrp";
			break;	
		case "timeunit":
			$query = "SELECT taskid, dscrp, ordering FROM tasks WHERE uid=$_SESSION[user] AND progress < total ORDER BY total, dscrp";
			break;
	}
	$result = pg_query($dbconn, $query);
	if(!$result) {
		echo("Cannot access database.");
		exit;
	}
	
	$numOfTasks = pg_num_rows($result);
	
	if(isset($_REQUEST['error'])) {
		echo("Invalid ordering.");
	}
	
	echo("<form method='post'>");
	
	$line = 1;
	while ($row = pg_fetch_array($result)) {
		echo("<label>$row[dscrp]:
					<select name=$row[taskid]>");
		for($i = 1; $i <= $numOfTasks; $i++) {
			if($i == $line) {
				echo("<option selected='selected'>$i</option>");
			} else {
				echo("<option>$i</option>");
			}
		}
		echo("</select></label><br>");
		$line++;
	}
	
	echo("<input type='hidden' name='numOfTasks' value=$numOfTasks>");
	echo("<input type='submit' name='submit' value='update'>
			</form>");
	
?>
</div>

<?php require "footer.php"; ?>
