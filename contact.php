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

<div class="container" id="contact">
	<div class="person">
	<span class='link'>Wang Taining: <a href="mailto:taining.wang@nus.edu.sg">taining.wang@nus.edu.sg</a></span><br>
	<iframe src="//www.facebook.com/plugins/follow?href=https%3A%2F%2Fwww.facebook.com%2Ftaining.wang.3&amp;layout=standard&amp;show_faces=true&amp;colorscheme=light&amp;width=450&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:80px;" allowTransparency="true"></iframe><br>
	</div>
	<div class="person">
	<span class='link'>Tang Ning: <a href="mailto:tangning@nus.edu.sg">tangning@nus.edu.sg</a></span><br>
	<iframe src="//www.facebook.com/plugins/follow.php?href=http%3A%2F%2Fwww.facebook.com%2Fzuck&amp;layout=standard&amp;show_faces=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:80px;" allowTransparency="true"></iframe>
	</div>
</div>

<?php
	require 'footer.php';
?>