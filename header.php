<?php
	if (isset($_GET['action']) && $_GET['action'] == "logout") {
		unset($_SESSION['user']);
		header("Location: login.php");
	}
	$currentURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>To-do Manager</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>			
		<header>
			<div class='header-container'>
				<div id='utility'>
				<?php
					switch($page) {
						case "login":
							break;
						case "signup":
							break;
						default:
							echo("<a href='account.php'>My account</a>
									<a href='$currentURL?action=logout'>Log out</a>");
					}
				?>
				</div>
				<div id='title'>
					<h1>To-do Manager</h1>
				</div>

				<nav>
					<ul>
						<?php
							switch($page) {
								case "home":
									echo('<li id="current-page"><a href="home.php">Home</a></li>
										<li><a href="news.php">News</a></li>
										<li><a href="contact.php">Contact</a></li>');
									break;
								case "news":
									echo('<li><a href="home.php">Home</a></li>
										<li id="current-page"><a href="news.php">News</a></li>
										<li><a href="contact.php">Contact</a></li>');
									break;		
								case "contact":
									echo('<li><a href="home.php">Home</a></li>
										<li><a href="news.php">News</a></li>
										<li id="current-page"><a href="contact.php">Contact</a></li>');
									break;
								case "login":
									echo('<li><a href="#">Home</a></li>
										<li><a href="#">News</a></li>
										<li><a href="contact.php">Contact</a></li>');
									break;
								case "signup":
									echo('<li><a href="#">Home</a></li>
										<li><a href="#">News</a></li>
										<li><a href="contact.php">Contact</a></li>');
									break;
								default:
									echo('<li><a href="home.php">Home</a></li>
										<li><a href="news.php">News</a></li>
										<li><a href="contact.php">Contact</a></li>');
									break;
							}				
						?>
					</ul>
				</nav>
			</div>
		</header>