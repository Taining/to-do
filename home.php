<?php
    session_save_path("sess");
	session_start();
	
	$page = "home";
	
	if (isset($_SESSION['user'])) {
		$userid = $_SESSION['user'];
	} else {
		header("Location: login.php");
		exit;
	}
	
	require "config.inc";
	require "header.php";
		
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if (!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	
	if (isset($_GET['makeProgress'])) {
		$taskid = $_GET['makeProgress'];
		$get_progress_query = "SELECT progress, uid FROM tasks WHERE taskid=$taskid";
		$result = pg_query($dbconn, $get_progress_query);
		$row = pg_fetch_row($result);
		
		// progress++
		$progress = $row[0];
		$progress += 1;
		$update_query = "UPDATE tasks SET progress=$progress WHERE taskid=$taskid";
		pg_query($dbconn, $update_query);
		
		// done++
		$uid = $row[1];
		$get_done_query = "SELECT done FROM appuser WHERE uid=$uid";
		$result = pg_query($dbconn, $get_done_query);
		$row = pg_fetch_row($result);
		$done = $row[0];
		$done += 1;
		$update_query = "UPDATE appuser SET done=$done WHERE uid=$uid";
		pg_query($dbconn, $update_query);
	}
	
	// caculate rate
	$query = "SELECT signupdate, done FROM appuser WHERE uid=$userid";
	$result = pg_query($dbconn, $query);
	if (!$result) {
		echo("Cannot access database.");
		exit;
	}
	$row = pg_fetch_array($result);
	$signupdate = $row['signupdate'];
	$done = $row['done'];
	
	$signup = strtotime(date("M d Y", strtotime($signupdate)));
	$cur = strtotime(date("M d Y"));
	$dateDiff = ($cur - $signup)/3600/24;
	$rate = $done / $dateDiff;
	
	// caculate remaining days
	$query = "SELECT SUM(total), SUM(progress) FROM tasks WHERE uid=$userid";
	$result = pg_query($dbconn, $query);
	if (!$result) {
		echo("Cannot access database.");
		exit;
	}
	$row = pg_fetch_row($result);
	$total = $row[0];
	$progress = $row[1];
	$remaining = $total - $progress;
	if(!$rate == 0) {
		$remainingDays = ceil($remaining / $rate);
	}
	
	// display tasks
	$query = "SELECT * FROM tasks WHERE uid=$userid ORDER BY taskid";
	$result = pg_query($dbconn, $query);
	if(!$result) {
		echo("Cannot access database.");
		exit;
	}
?>
		
		<div id='content'>
			<div id='tasks'>
				<ul>

				<?php
					while ($row = pg_fetch_row($result)) {
						$taskid = $row[1];
						$dscrp = $row[2];
						$total = $row[3];
						$progress = $row[4];
				?>
					<li>
						<?php echo($dscrp)?>
						<table border=1>
						<tr>
							<?php
								for($i = 0; $i < $total; $i++) {
									if($i < $progress) {
										echo("<td>completed</td>");
									} else if($i == $progress){
										echo("<td>
												<a href='?makeProgress=$taskid'>to complete</a>
											  </td>");
									} else {
										echo("<td>not completed</td>");
									}
								}
							?>
						</tr>
						</table>
					</li>
				<?php
					}
				?>
				
				</ul>
			</div>
			<p><a href="add-task.php">Add new task</a></p>
			
			<?php 
				echo("Rate: $rate<br>");
				if(isset($remainingDays)) {
					echo("Remaining: $remaining = $remainingDays days work"); 
				} else {
					echo("Remaining: $remaining");
				}
			?>
			
		</div>
		
<?php
	require "footer.php";
?>