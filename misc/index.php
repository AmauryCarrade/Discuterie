<?php
	session_start();

	if(isset($_SESSION['loggedIn'])) {
		require_once('chat.php');
	}
	else if(isset($_GET['do'])) {
		if($_GET['do'] == 'new') {
			require_once('new.php');
		}
		else {
			require_once('login.php');
		}
	}

	else {
		require_once('login.php');
	}

?>