<?php
    session_save_path("sess");
	session_start();
	
	$page = "finished";
	
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
	
	$query = "SELECT * FROM tasks WHERE uid=$userid AND progress=total ORDER BY taskid";
	$result = pg_query($dbconn, $query);
	if(pg_num_rows($result) < 1) {
		echo("<div class='container'>You have no finished tasks.</div>");
		exit;
	}
?>

	<div class="container">
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
	</div> <!-- end of content -->
	
<?php 
	require "footer.php"; 
?>