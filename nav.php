<nav>
	<ul>
		<a href="index.php"><li>Home</li></a>
		<?php if($_SESSION['level'] > 0) { echo("<a href=\"user-control.php\"><li>User Control</li></a>"); } ?>
		<a href="change-pass.php"><li>Change My Password</li></a>
		<a href="logout.php"><li>Logout</li></a>
	</ul>
</nav>
<span id="status">Current User: <em><?=$_SESSION['user']?></em></span>