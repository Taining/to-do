<?php
	session_save_path("sess");
	session_start();
	
	$page = "ordering";
	
	require "config.inc";
	require "header.php";
	
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	
	$query = "SELECT taskid, dscrp, ordering FROM tasks WHERE uid=$_SESSION[user] ORDER BY ordering";
	$result = pg_query($dbconn, $query);
	if(!$result) {
		echo("Cannot access database.");
		exit;
	}
	
	$numOfTasks = pg_num_rows($result);
	
	if(isset($_REQUEST['error'])) {
		echo("Invalid ordering.");
	}
	
	echo("<form action='update-ordering.php' method='post'>");
	
	while ($row = pg_fetch_array($result)) {
		echo("<label>$row[dscrp]:
					<select name=$row[taskid]>");
		for($i = 1; $i <= $numOfTasks; $i++) {
			if($i == $row['ordering']) {
				echo("<option selected='selected'>$i</option>");
			} else {
				echo("<option>$i</option>");
			}
		}
		echo("</select></label><br>");
	}
	echo("<input type='hidden' name='numOfTasks' value=$numOfTasks>");
	echo("<input type='submit' name='submit' value='update'>
			</form>");
?>