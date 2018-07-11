<?php
	require_once("init.php");
	if(!isset($_SESSION['user'])) {
		header("Location: login.php");
		die();
	}
	
	$query = "SELECT * FROM `radmin` WHERE `username` = '".$_SESSION['user']."'";
	if($result = $db->query($query)) { 
		$row = $result->fetch_assoc();
		if($row['level'] > 0) {
			$_SESSION['level'] = $row['level'];
			$result->close();
		} else {
			$result->close();
			header("Location: index.php?e=1"); //unauthorised
			die();
		}
	} else {
		$result->close();
		header("Location: index.php?e=2"); //unauthorised
		die();
	}

	if(!isset($_GET['u'])) {
		header("Location: user-control.php?e=3");
		die();
	}

	//clean uid
	//uid exists? ->
	//find uname from uid ->
	//get all user info from uname ->
	//is level < authed user level? ->
	//display data

	$tid = $db->escape_string($_GET['u']);

	if($result = $db->query("SELECT * FROM `radusers` WHERE `userid` = '".$tid."'")) {
		if($result->num_rows > 0) { //exists
			$row = $result->fetch_assoc();
			$tun = $row['username'];
			$result->close();
		} else {
			$result->close();
			header("Location: user-control.php?e=4");
			die();
		}
	} else {
		$result->close();
		header("Location: user-control.php?e=5");
		die();
	}

	if($result = $db->query("SELECT `userid`, `firstname`, `lastname`, `datecreated`, `lastaccessed`, `level` FROM `radusers` u  JOIN `radmin` a ON u.username = a.username WHERE u.username = '".$tun."'")) {
		if($result->num_rows > 0) { //not corrupt
			$row = $result->fetch_assoc();
			$tid = $row['userid'];
			$tlv = $row['level'];
			$tfn = $row['firstname'];
			$tln = $row['lastname'];
			$tdc = $row['datecreated'];
			$tla = $row['lastaccessed'];
			$result->close();
		} else {
			header("Location: user-control.php?e=6");
			die();
		}
	} else {
		header("Location: user-control.php?e=7");
		die();
	}

	if($bancheck = $db->query("SELECT * FROM radcheck WHERE username = '".$tun."' AND attribute = 'Auth-Type' AND value = 'Reject'")) {
		if($bancheck->num_rows > 0) {
			$tar = false;
		} else {
			$tar = true;
		}
	} else {
		$bancheck->close();
		header("Location: user-control.php?e=8");
		die();
	}
	
	$bancheck->close();

	if($tlv >= $_SESSION['level']) {
		header("Location: user-control.php?e=9");
		die();
	}

	
?>

<html>
<head>
	<meta charset="utf-8">
	<title>Edit User | Elovix RADIUS Authentication Control</title>
	<link rel="stylesheet" href="main.css" />
</head>

<body>
	<div id="top">
		<?php include("nav.php"); ?>
	</div>
	<div id="container">
		<span id="message" style="margin-bottom: 40px;"><strong>Editing: <?=$tun?></strong></span>
		<form name="edituser" action="exec.php?f=4" method="POST">
			<input type="hidden" id="un" name="un" value="<?=$tun?>"/>
			<input type="hidden" id="uid" name="uid" value="<?=$tid?>"/>
			<label for="first">First Name:</label> 
			<input type="text" id="first" name="first" class="field" value="<?=$tfn?>"/>
			<label for="last">Last Name:</label>
			<input type="text" id="last" name="last" class="field" value="<?=$tln?>"/>
			<label for="access">Administrative Level:</label>
			<select id="access" name="access" class="field">
				<?php
					for($i = 0; $i <= $_SESSION['level']; $i++) {
						echo("<option value=\"".$i."\"");
						if($i == $tlv) echo(" selected");
						echo(">Level ".$i."</option>");
					}
				?>
			</select>
			<label for="enabled">Network Access Policy:</label>
			<select id="enabled" name="enabled" class="field">
				<option <?php if($tar) echo("selected"); ?> value="y">Access Permitted</option>
				<option <?php if(!$tar) echo("selected"); ?> value="n">No Access</option>
			</select>
			<div id="button-wrap">
			<button type="submit" id="submit" name="submit" class="button">Update User</button>
			<a href="user-control.php"><button type="button" id="back" name="back" class="button">Cancel Changes</button></a>
			</div>
		</form>
	</div>
</body>
</html>

