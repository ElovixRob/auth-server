<?php
	require_once("init.php");
	if(!isset($_SESSION['user'])) {
		header("Location: login.php");
		die();
	}
	
	
	if($result = $db->query("SELECT * FROM `radmin` WHERE `username` = '".$_SESSION['user']."'")) { //this is also stored in session, but check again because a) confidentiality b) uncleared session
		$row = $result->fetch_assoc();
		if($row['level'] > 0) {
			$_SESSION['level'];
			$result->close();
		} else {
			$result->close();
			header("Location: index.php?e=1"); //unauthorised
			die();
		}
	} else {
		$result->close();
		header("Location: index.php?e=1"); //unauthorised
		die();
	}

?>

<html>
<head>
	<meta charset="utf-8">
	<title>User Control | Elovix RADIUS Authentication Control</title>
	<link rel="stylesheet" href="main.css" />
</head>

<body>
	<div id="top">
		<?php include("nav.php"); ?>
	</div>
	<div id="container">
		<span id="message">Welcome to the user control panel. Please double-check all entries before confirming.</span>
		<?php
			echo("<table cellpadding=\"0\" cellspacing=\"0\">");
				$select = "SELECT `u`.`username`, `userid`, `level`, `firstname`, `lastname`, `datecreated`, `lastaccessed` FROM `radusers` `u` JOIN `radmin` `a` ON `u`.`username` = `a`.`username`";
				if($result = $db->query($select)) {
					echo("<tr><th>Access</th><th>Username</th><th>ID</th><th>Admin</th><th>First Name</th><th>Last Name</th><th>User Since</th><th>Last Seen</th><th colspan=\"2\">Control</th>");
					while($row = $result->fetch_assoc()) {
						echo("<tr id=\"".$row['userid']."\">");
							$row['datecreated'] = date_format(date_create($row['datecreated']), 'H:i, jS F Y [l]');
							$row['lastaccessed'] = date_format(date_create($row['lastaccessed']), 'H:i, jS F Y [l]');
							if($bancheck = $db->query("SELECT * FROM radcheck WHERE username = '".$row['username']."' AND attribute = 'Auth-Type' AND value = 'Reject'")) {
								if($bancheck->num_rows > 0) {
									echo("<td class=\"disallow\">Deny</td>");
								} else {
									echo("<td class=\"allow\">Allow</td>");
								}
							} else {
								echo("<td><em>failed</em></td>");
							}
							foreach($row as $item) {
								echo("<td>");
									echo($item);
								echo("</td>");
							}
							echo("<td><a href=\"edit-user.php?u=".$row['userid']."\">Edit</a></td>");
							if ($row['username'] != $_SESSION['user']) echo("<td><a href=\"delete-user.php?u=".$row['userid']."\">Delete</a></td>");
							else echo("<td style=\"color: #AAA;\">Delete</td>");
						echo("</tr>");
					}
					echo("<tr><td colspan=\"10\"><a href=\"add-user.php\">[+] Add New User</a></td></tr>");
				}
			echo("</table>");
		?>
	</div>
</body>
</html>

