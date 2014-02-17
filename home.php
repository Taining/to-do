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
	
	//mark a task as done
	if (isset($_GET['action']) && $_GET['action']=="done") {
		$task_info = pg_query($dbconn, "SELECT total FROM tasks WHERE uid=$userid AND taskid=$_GET[taskid]");
		$row = pg_fetch_array($task_info);

		$done_task_query = "UPDATE tasks SET (progress) = ($1) WHERE taskid = $2;";
		$done_result = pg_prepare($dbconn, "done_task", $done_task_query);
		$done_result = pg_execute($dbconn, "done_task", array($row['total'], $_GET['taskid']));
	}

	// display tasks
	$query = "SELECT * FROM tasks WHERE uid=$userid AND progress<total ORDER BY ordering";
	$result = pg_query($dbconn, $query);
	if(!$result) {
		echo("Cannot access database.");
		exit;
	}
?>
		
		<div id='content' class="container">
			<div class='tasks'>
				<ul>

				<?php
					while ($row = pg_fetch_array($result)) {
						$taskid = $row['taskid'];
						$dscrp = $row['dscrp'];
						$total = $row['total'];
						$progress = $row['progress'];
				?>
					<li>
						<?php echo("<a class='dscrp' href='edit-task.php?taskid=$taskid'>$dscrp </a>
									(<a href='delete-task.php?taskid=$taskid'>remove</a>
									<a href='home.php?action=done&taskid=$taskid'>done</a>)")?>
						<table border=1>
						<tr>
							<?php
								for($i = 0; $i < $total; $i++) {
									if($i < $progress - 1) {
										echo("<td class='completed'></td>");
									} else if ($i == $progress - 1) {
										echo("<td class='last'>
												<form action='undo.php' method='post'>
													<input type='hidden' name='undo' value=$taskid>
													<input type='submit' name='submit' value='Undo' class='btn'>
												</form>
											  </td>");
									} else if ($i == $progress){
										echo("<td class='next'>
												<form action='make-progress.php' method='post'>
													<input type='hidden' name='makeProgress' value=$taskid>
													<input type='submit' name='submit' value='Do it!' class='btn'>
												</form>
											  </td>");
									} else {
										echo("<td class='uncompleted'></td>");
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
			</div> <!-- end of tasks -->
			
			<div id=container-col>
				<div id="options">
					<p><a href="add-task.php">Add new task</a></p>
					<p><a href="ordering.php">Order of displaying tasks</a></p>
					<p><a href="finished.php">Finished tasks</a></p>
				</div>
			
				<div id="info">
					<div id="data">
					<?php 
						echo("Rate: $rate<br>");
						if(isset($remainingDays)) {
							echo("Remaining: $remaining = $remainingDays days work"); 
						} else {
							echo("Remaining: $remaining");
						}
					?>
					</div>
					<div id="instr">
						<br>Click on "Do it!" to make progress
						<br>Click on "Undo" to make the last unit as undone
						<br>Click on task title to see task detail
					</div>
				</div>
			</div>
			
		</div> <!-- end of content -->
		
<?php
	require "footer.php";
?>