<?php
	session_start();
	if(!isset($_SESSION['user'])) {
		header("Location: login.php");
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Home | Elovix RADIUS Authentication Control</title>
	<link rel="stylesheet" href="main.css" />
</head>

<body>
	<div id="top">
		<?php include("nav.php"); ?>
	</div>
	<div id="container">
		<span id="message">Unsure how to use this utility?<br>Contact %SERVERADMIN% (%CONTACTINFO%)</span>
	</div>
</body>
</html>
