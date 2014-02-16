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
	
	// display tasks
	$query = "SELECT * FROM tasks WHERE uid=$userid AND progress<total ORDER BY ordering";
	$result = pg_query($dbconn, $query);
	if(!$result) {
		echo("Cannot access database.");
		exit;
	}
?>
		
		<div id='content' class="container">
			<div id='tasks'>
				<ul>

				<?php
					while ($row = pg_fetch_array($result)) {
						$taskid = $row['taskid'];
						$dscrp = $row['dscrp'];
						$total = $row['total'];
						$progress = $row['progress'];
				?>
					<li>
						<?php echo("<a href='edit-task.php?taskid=$taskid'>$dscrp </a>
									(<a href='delete-task.php?taskid=$taskid'>delete</a>)")?>
						<table border=1>
						<tr>
							<?php
								for($i = 0; $i < $total; $i++) {
									if($i < $progress - 1) {
										echo("<td><div class='cell'></div></td>");
									} else if ($i == $progress - 1) {
										echo("<td>
												<div class='cell'>
													<form action='undo.php' method='post'>
														<input type='hidden' name='undo' value=$taskid>
														<input type='submit' name='submit' value='Undo' class='btn'>
													</form>
												</div>
											  </td>");
									} else if ($i == $progress){
										echo("<td>
												<div class='cell'>
													<form action='make-progress.php' method='post'>
														<input type='hidden' name='makeProgress' value=$taskid>
														<input type='submit' name='submit' value='Do it!' class='btn'>
													</form>
												</div>
											  </td>");
									} else {
										echo("<td><div class='cell'></div></td>");
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
			
			<div id="options">
				<p><a href="add-task.php">Add new task</a></p>
				<p><a href="ordering.php">Order of displaying tasks</a></p>
			</div>
			
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
			
		</div> <!-- end of content -->
		
<?php
	require "footer.php";
?>