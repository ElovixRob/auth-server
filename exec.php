<?php
	if(!isset($_GET['f']) || $_GET['f'] == 0) {
		header("Location: index.php");
		die();
	}
	
	require_once("init.php");

	switch($_GET['f']) { //auth
		case 1: {
			if(!isset($_POST['user']) || !isset($_POST['password']) || strlen($_POST['user']) == 0 || strlen($_POST['password']) == 0) {
				header("Location: login.php?e=1");
				die();
			}
			
			$username = $db->escape_string($_POST['user']);
			$clearpassword = $db->escape_string($_POST['password']);
			$password = NTLMHash($clearpassword);

			if ($result = $db->query("SELECT * FROM `radcheck` WHERE `username` = '".$username."' AND `value` = '".$password."' LIMIT 1;")) {
				if($result->num_rows > 0){
					session_start();
					$_SESSION['user'] = $username;
					$_SESSION['level'] = 0;
					$result->close();
					if($result = $db->query("SELECT * FROM `radmin` WHERE `username` = '".$username."'")) {
						$row = $result->fetch_assoc();
						$_SESSION['level'] = $row['level'];
						$result->close();
					}
					
					header("Location: index.php");
					die();
				}
				else {
					$result->close();
					header("Location: login.php?e=2");
					die();
				}
			}
			else {
				$result->close();
				header("Location: login.php?e=3");
				die();
			}
			break;
		}
			
		case 2: { //change password (user)
			if(!isset($_POST['cpass']) || !isset($_POST['npass']) || !isset($_POST['npass2']) || strlen($_POST['cpass']) == 0 || strlen($_POST['npass']) == 0) {
				header("Location: change-pass.php?e=1");
				die();
			}
			if($_POST['npass'] != $_POST['npass2']) {
				header("Location: change-pass.php?e=2");
				die();
			}
			if(strlen($_POST['npass']) < 6) {
				header("Location: change-pass.php?e=3");
				die();
			}
			$clearcpass = $db->escape_string($_POST['cpass']);
			$cpass = NTLMHash($clearcpass);
			$query = "SELECT * FROM `radcheck` WHERE `username` = '".$_SESSION['user']."' AND `value` = '".$cpass."';";
			if ($result = $db->query($query)) {
				if($result->num_rows > 0){ //cpass correct
					$clearnpass = $db->escape_string($_POST['npass']);
					$npass = NTLMHash($clearnpass);
					$query = "UPDATE `radcheck` SET `value` = '".$npass."' WHERE `username` = '".$_SESSION['user']."';";
					$db->query($query);
					$result->close();
					header("Location: index.php");
					die();
				}
				else {
					$result->close();
					header("Location: change-pass.php?e=4");
					die();
				}
			}
			else {
				$result->close();
				header("Location: change-pass.php?e=5");
				die();
			}
			break;
		}
			
		case 4: { //update user (administrative capacity)
			if(!isset($_SESSION['user'])) {
				header("Location: login.php");
				die();
			}
			
			if(!isset($_POST['un']) || !isset($_POST['first']) || !isset($_POST['last']) || !isset($_POST['access']) || !isset($_POST['enabled']) || $_POST['un'] == "" || $_POST['first'] == "" || $_POST['last'] == "" || $_POST['access'] == "" || $_POST['enabled'] == "") {
				header("Location: user-control.php");
				die();
			}
			
			$tun = $db->escape_string($_POST['un']);
			$tid = $db->escape_string($_POST['uid']);
			$tfn = $db->escape_string($_POST['first']);
			$tln = $db->escape_string($_POST['last']);
			$tac = $db->escape_string($_POST['access']);
			$tar = $db->escape_string($_POST['enabled']);
			
			$query = "SELECT * FROM `radmin` WHERE `username` = '".$_SESSION['user']."'";
			
			if($result = $db->query($query)) {
				if($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$ual = $row['level'];
					if($ual < 1) {
						$result->close();
						header("Location: index.php");
						die();
					}
					$result->close();
				} else {
					$result->close();
					header("Location: index.php");
					die();
				}
			} else {
				$result->close();
				header("Location: index.php");
				die();
			}
				
			if($tac < 0 || $tac > 5) {
				header("Location: edit-user.php?u=".$tid);
				die();
			}
			
			if($tac > $ual) {
				header("Location: edit-user.php?u=".$tid);
				die();
			}
			
			if($tar != "y" && $tar != "n") {
				header("Location: edit-user.php?u=".$tid);
				die();
			}
			
			$uq = "UPDATE `radusers` SET `firstname` = '".$tfn."', `lastname` = '".$tln."' WHERE `username` = '".$tun."';";
			$aq = "UPDATE `radmin` SET `level` = '".$tac."' WHERE `username` = '".$tun."';";
			
			$result = $db->query("SELECT * FROM `radcheck` WHERE `attribute` = 'Auth-Type' AND `username` = '".$tun."';");
			if($result->num_rows > 0) $blocked = true;
			else $blocked = false;
			$result->close();
			
			if($tar == "y") {
				if($blocked) $cq = "DELETE FROM `radcheck` WHERE `attribute` = 'Auth-Type' AND `username` = '".$tun."';";
				else $cq = false;
			} else if($tar == "n") {
				if($blocked) $cq = false;
				else $cq = "INSERT INTO `radcheck` (`username`, `attribute`, `op`, `value`) VALUES ('".$tun."', 'Auth-Type', ':=', 'Reject');";
			}
			
			$db->query($uq);
			$db->query($aq);
			if($cq)	$db->query($cq);
			
			header("Location: user-control.php");
			die();
			
			break;
		}
			
		default: {
			
			break;
		}
	}
?>