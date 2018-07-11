<?php
	session_start();
	if(!isset($_SESSION['user'])) {
		header("Location: login.php");
	}

	if(!isset($_GET['e'])) $e = 0;
	else $e = $_GET['e'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Change Password | Elovix RADIUS Authentication Control</title>
	<link rel="stylesheet" href="main.css" />
</head>

<body>
	<div id="top">
		<?php include("nav.php"); ?>
	</div>

	<div id="container">
		<span id="message"><p>Please ensure you tell your devices to <em>forget</em> the WiFi networks<strong> 1124 <u>and</u> 115</strong> after changing your password. You will then need to login again with your new credentials.</p></span>
		<?php
		if($e > 0) {
			echo("<span id=\"error-message\">Error: ");
			switch($e) {
				case 1: {
					echo("Please ensure the change password form is filled out correctly and try again.");
					break;
				}
				case 2: {
					echo("New password confirmation field did not match new password field. Please try again.");
					break;
				}
				case 3: {
					echo("Your new password did not meet strength requirements. Please ensure it is at least 6 characters long.");
					break;
				}
				case 4: {
					echo("Your current password was incorrect. Please re-enter and try again.");
					break;
				}
				case 5: {
					echo("Something went wrong. Please try again.");
					break;
				}
				default: {
					break;
				}
			}
			echo("</span>");
		}
		?>
		<form name="changepass" action="exec.php?f=2" method="POST">
			<input type="password" id="cpass" name="cpass" class="field" placeholder="Current Password"/>
			<input type="password" id="npass" name="npass" class="field" placeholder="New Password"/>
			<input type="password" id="npass2" name="npass2" class="field" placeholder="New Password Again"/>
			<input type="submit" id="submit" name="submit" class="button" value="Change Password"/>
		</form>
	</div>
</body>
</html>