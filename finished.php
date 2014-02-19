<?php
    session_save_path("sess");
	session_start();
	
	$page = "finished";
	
	require "config.inc";
	require "header.php";
	require "functions.php";
		
	$userid = authenticate();	
		
	$dbconn = connectToDatabase($db_name, $db_user, $db_password);
	
	// undo
	if(isset($_REQUEST['undo'])) {
		$taskid = $_REQUEST['undo'];
		undo($dbconn, $taskid);
	}	
	
	$query = "SELECT * FROM tasks WHERE uid=$userid AND progress=total ORDER BY taskid";
	$result = pg_query($dbconn, $query);
	if(pg_num_rows($result) < 1) {
		echo("<div class='container'>You have no finished tasks.</div>");
		require 'footer.php';
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
					<?php echo("<a class='dscrp' href='edit-task.php?taskid=$taskid'>$dscrp </a>
								(<a href='delete-task.php?taskid=$taskid'>remove</a>)")?>
					<table border=1>
					<tr>
						<?php
							for($i = 0; $i < $total; $i++) {
								if($i < $progress - 1) {
									echo("<td class='completed'></td>");
								} else if ($i == $progress - 1) {
									echo("<td class='last'>
											<form method='post'>
												<input type='hidden' name='undo' value=$taskid>
												<input type='submit' name='submit' value='Undo' class='btn'>
											</form>
										  </td>");
								} else if ($i == $progress){
									echo("<td class='next'>
											<form method='post'>
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
		</div>
	</div> <!-- end of content -->
	
<?php 
	require "footer.php"; 
?>