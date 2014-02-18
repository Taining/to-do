<?php
	session_save_path('sess');
	session_start();

	$page = "contact";

	if (isset($_SESSION['user'])) {
		$userid = $_SESSION['user'];
	} else {
		header("Location: login.php");
		exit;
	}

	require "config.inc";
	require "header.php";
?>

<div class="container">
	<a href="mailto:taining.wang@nus.edu.sg">Wang Taining: taining.wang@nus.edu.sg</a><br>
	<a href="mailto:tangning@nus.edu.sg">Tang Ning: tangning@nus.edu.sg</a>
	<iframe src="//www.facebook.com/plugins/follow.php?href=http%3A%2F%2Fwww.facebook.com%2Fzuck&amp;width=50&amp;height=80&amp;colorscheme=light&amp;layout=standard&amp;show_faces=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height:80px;" allowTransparency="true"></iframe>
</div>

<?php
	require 'footer.php';
?>