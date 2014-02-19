<?php
	function authenticate() {
		if (isset($_SESSION['user'])) {
			return $_SESSION['user'];
		} else {
			header("Location: login.php");
			exit;
		}
	}
	
	function caculateRate($dbconn, $userid, &$rate) {
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
		} else {
			$rate = 0;
		}
	}

	function caculateRemaining ($dbconn, $rate, $userid, &$remaining, &$remainingDays) {
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
	}

	function connectToDatabase($db_name, $db_user, $db_password){
        $dbconn = pg_connect("host=localhost port=5432 dbname=$db_name user=$db_user password=$db_password");
        if(!$dbconn){
            echo "Aw, Snap!";
            exit;      
        }

        return $dbconn; 
    }

    function preventFormResubmission(){
    	unset($_POST);
    }
    
    function makeProgress($dbconn, $taskid) {
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
	
	function undo($dbconn, $taskid) {
		$query = "SELECT progress FROM tasks WHERE taskid=$taskid";
		$result = pg_query($dbconn, $query);

		$row = pg_fetch_array($result);
		$progress = $row['progress'];
		$progress = $progress - 1;
	
		$query = "UPDATE tasks SET progress = $progress WHERE taskid=$taskid";
		pg_query($dbconn, $query);
	}
	
	function addToDatabase(&$dscrp, &$details, &$total, $db_name, $db_user, $db_password, $priority) {
		$userid = $_SESSION['user'];
		//userid, task-dscrp, total-time, progress
		$dbconn = connectToDatabase($db_name, $db_user, $db_password);
		
		$query = "SELECT MAX(taskid) FROM tasks;";
		$result=pg_query($dbconn, $query);
		$row = pg_fetch_row($result);
		$taskid = $row[0] + 1;
		
		$query = "SELECT COUNT(*) FROM tasks;";
		$result=pg_query($dbconn, $query);
		$row = pg_fetch_row($result);
		$ordering = $row[0] + 1;
		
		$query = "INSERT INTO tasks(uid, taskid, dscrp, details, total, progress, ordering, createtime, priority) VALUES($userid, $taskid, $1, $2, $3, 0, $ordering, $4, $5)";
		$result = pg_prepare($dbconn, "my_query", $query);
		$result = pg_execute($dbconn, "my_query", array($dscrp, $details, $total, date("Y-m-d"), $priority));
		if($result) {
			header("Location: home.php");
		} else {
			echo("Failed to add task to database.");
			return;
		}
	}
	
	function updateToDatabase($dbconn, $taskid, $dscrp, $details, $total, $priority) {
		$query = "UPDATE tasks SET dscrp=$1, details=$2, total=$3, priority=$4 WHERE taskid=$5;";
		$result = pg_prepare($dbconn, "my_query", $query);
		$result = pg_execute($dbconn, "my_query", array($dscrp, $details, $total, $priority, $taskid));
		if($result) {
			header("Location: home.php");
		} else {
			echo("Failed to edit task.");
			return;
		}
	}
	
	function defineOrdering($dbconn, $orderings) {	
		$tasks_query = "SELECT taskid FROM tasks WHERE uid = $_SESSION[user]";
		$tasks_result = pg_query($dbconn, $tasks_query);
	
		while ($row = pg_fetch_row($tasks_result)) {
			$taskid = $row[0];
			$order = $orderings[$taskid];
		
			if(isset($tasks[$order])) {
				header("Location: ordering.php?error=1");
				exit;
			}
		
			$tasks[$order] = 1;
		
			$query = "UPDATE tasks SET ordering=$order WHERE taskid = $taskid";
			pg_query($dbconn, $query);
		}
		
		$_SESSION['sort'] = "none";
		header("Location: home.php");
	}
	
?>