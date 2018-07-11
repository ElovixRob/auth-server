
<?php
	session_start();
	if(isset($_SESSION['user'])) {
		header("Location: index.php");
	}

	if(!isset($_GET['e'])) $e = 0;
	else $e = $_GET['e'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Login | Elovix RADIUS Authentication Control</title>
	<link rel="stylesheet" href="main.css" />
</head>

<body>
	<div id="top">
		<span id="status"><em>Elovix RADIUS Authentication Control Panel v1.0</em></span>
	</div>
	<div id="container">
		<span id="message"><p>Please enter your current username (you@elovix.com.au) and password.</p></span>
		<?php
		if($e > 0) {
			echo("<span id=\"error-message\">Error: ");
			switch($e) {
				case 1: {
					echo("Please ensure the form is entirely filled out and try again.");
					break;
				}
				case 2: {
					echo("Supplied credentials do not match any account on record.");
					break;
				}
				case 3: {
					echo("An error occurred. Please try again.");
					break;
				}
				default: {
					break;
				}
			}
			echo("</span>");
		}
		?>
		<form name="login" action="exec.php?f=1" method="POST">
			<input type="text" id="user" name="user" class="field" placeholder="Username"/>
			<input type="password" id="password" name="password" class="field" placeholder="Password"/>
			<input type="submit" id="submit" name="submit" class="button" value="Login"/>
		</form>
	</div>
</body>
</html>