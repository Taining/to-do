<?php
    session_save_path("sess");
	session_start();
	
	if(!isset($_SESSION['postback'])) {
		$postback=mt_rand();
		$_SESSION['postback']=$postback;
	}
	
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
	if ($dateDiff != 0) {
		$rate = intval($done / $dateDiff);
	} else $rate = 0;
	
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
	if (isset($_GET['action'])) {
		//user can only modify his own task by check $_GET['uid'] = $_SESSION['user']
		if ($_GET['action']=="done" && $_GET['uid'] == $_SESSION['user']) {
			$task_info = pg_query($dbconn, "SELECT total FROM tasks WHERE uid=$userid AND taskid=$_GET[taskid]");
			$row = pg_fetch_array($task_info);

			$done_task_query = "UPDATE tasks SET (progress) = ($1) WHERE taskid = $2;";
			$done_result = pg_prepare($dbconn, "done_task", $done_task_query);
			$done_result = pg_execute($dbconn, "done_task", array($row['total'], $_GET['taskid']));
		} elseif ($_GET['action']=="remove" && $_GET['uid'] == $_SESSION['user'] && $_GET['postback'] == $_SESSION['postback']) {
			$delete_task_query = "DELETE FROM tasks WHERE taskid=$1;";
			$delete_result = pg_prepare($dbconn, "delete_task", $delete_task_query);
			$delete_result = pg_execute($dbconn, "delete_task", array($_GET['taskid']));
			$_SESSION['postback'] = mt_rand();
		} elseif ($_GET['postback'] != $_SESSION['postback']) {
			header("Location: home.php");
			exit;
		}
	}

	//change sort methods
	$query;
	if (isset($_GET['sort'])) {
		if ($_GET['sort']=="createdate") {
			$query = "SELECT * FROM tasks WHERE uid=$userid AND progress<total ORDER BY createtime";
		} elseif ($_GET['sort']=="priority") {
			$query = "SELECT * FROM tasks WHERE uid=$userid AND progress<total ORDER BY priority";
		} elseif ($_GET['sort']=="timeunit") {
			$query = "SELECT * FROM tasks WHERE uid=$userid AND progress<total ORDER BY total";
		}
	} else {
		// default sort method
		$query = "SELECT * FROM tasks WHERE uid=$userid AND progress<total ORDER BY ordering";
	}

	$result = pg_query($dbconn, $query);
	if(!$result) {
		echo("Cannot access database.");
		exit;
	}
?>
		
		<div id='content' class="container">
			<div class='tasks'>
				<ul>
					<!-- navagation to change sort methods -->	
					<div>
						Order by: 
						<a href="home.php?sort=createdate">Create Date</a>
						<a href="home.php?sort=priority">Priority</a>
						<a href="home.php?sort=timeunit">Time Units</a>
					</div>
				<?php
					while ($row = pg_fetch_array($result)) {
						$taskid = $row['taskid'];
						$dscrp = $row['dscrp'];
						$total = $row['total'];
						$progress = $row['progress'];
						$createtime = $row['createtime'];
				?>
					<li>
						<?php echo("<a class='dscrp' href='edit-task.php?taskid=$taskid&uid=$userid'>$dscrp</a>"); ?>
						<?php echo ("(<a href='home.php?action=remove&taskid=$taskid&uid=$userid&postback=$_SESSION[postback]'>remove</a>"); ?>			
						<?php echo ("<a href='home.php?action=done&taskid=$taskid&uid=$userid'>done</a>)"); ?>	 
						<?php echo ("<code>Created at $createtime</code>"); ?>
						
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

								//print how many percent of this task has been done
								$percent = intval($progress/$total*100);
								echo ("<td class='task-progress'>$percent%</td>");
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
				<div id="options" class="small-container">
					<p><a href="add-task.php">Add new task</a></p>
					<p class='small-text'>--Your new task</p>
					<p><a href="ordering.php">Define your own order of display</a></p>
					<p class='small-text'>--Define the order of displaying your tasks</p>
					<p><a href="finished.php">Finished tasks</a></p>
					<p class='small-text'>--An archive of tasks that you have finished</p>
				</div>
			
				<div id="info" class="small-container">
					<div id="data">
					<?php 
						echo("Rate: <span class='highlight'>$rate</span> units per day (One unit is 30 mins work)<br>");
						if(isset($remainingDays)) {
							echo("Remaining: $remaining = <span class='highlight'>$remainingDays</span> days work"); 
						} else {
							echo("Remaining: $remaining");
						}
					?>
					</div>
					<div id="instr">
						<br>Click on "<span class='highlight-less'>Do it!</span>" to make progress
						<br>Click on "<span class='highlight-less'>Undo</span>" to make the last unit as undone
						<br>Click on task title to see task detail
					</div>
				</div>
			</div>
			
		</div> <!-- end of content -->
		
<?php
	require "footer.php";
?>