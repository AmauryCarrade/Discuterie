<?php
	session_start();
	require_once('connect.php');
	require_once('salt.php');

	define('_NO_DIRECT_ACCESS', true);

	if(isset($_GET['do'])) {
		if($_GET['do'] == 'new' && !isset($_SESSION['loggedIn'])) {
			require_once('new.php');
			exit;
		}
		else if($_GET['do'] == 'out') {
			session_destroy();
			session_start();
		}
	}

	else if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
		require_once('chat.php');
		exit;
	}

	require_once('login.php');
?>
