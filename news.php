<?php
	session_save_path('sess');
	session_start();

	$page = "news";

	if (isset($_SESSION['user'])) {
		$userid = $_SESSION['user'];
	} else {
		header("Location: login.php");
		exit;
	}

	require "config.inc";
	require "header.php";
	include 'functions.php';

	$dbconn = connectToDatabase($db_name, $db_user, $db_password);

	$result = pg_query($dbconn, "SELECT U.fname, T.dscrp, T.createtime FROM appuser U, tasks T WHERE U.uid=T.uid ORDER BY T.taskid DESC LIMIT 10;");
	echo "<div class='container'>
			<h2>News</h2>
			<ol>";
	while ($row = pg_fetch_array($result)) {
		echo "<li>User <font color='#F94C4C'>$row[fname]</font> added task <font color='#F94C4C'>$row[dscrp]</font> on <code>$row[createtime]</code>.</li>";
	}
	echo "</ol></div>";

	require 'footer.php';
?>