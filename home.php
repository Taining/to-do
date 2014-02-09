<?php
	$page = "home";
	require "config.inc";
	require "header.php";
	$userid = 0;
	
	$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=$db_name user=$db_user password=$db_password");
	if(!$dbconn){
		echo("Can't connect to the database");	
		exit;
	}
	
	if(isset($_GET['makeProgress'])) {
		$taskid = $_GET['makeProgress'];
		$get_progress_query = "SELECT progress FROM tasks WHERE taskid=$taskid";
		$result = pg_query($dbconn, $get_progress_query);
		$row = pg_fetch_row($result);
		$progress = $row[0];
		$progress += 1;
		$update_query = "UPDATE tasks SET progress=$progress WHERE taskid=$taskid";
		pg_query($dbconn, $update_query);
	}
	
	$query = "SELECT * FROM tasks WHERE userid=$userid";
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
			<a href="add-task.php">Add new task</a>
		</div>
		
<?php
	require "footer.php";
?>