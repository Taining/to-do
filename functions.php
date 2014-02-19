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
	
?>