<?php
	require_once('connect.php');
	require_once('salt.php');
	session_start();

	$pdo = SQLiteConnect();

	if(!isset($_POST['auth'])) {
		exit;
	}
	else if(sha1(SALT . $_POST['auth']) != $_SESSION['auth']) {
		exit;
	}

	switch($_POST['do']) {
		case 'newRoom': // Args: name, type
			$_POST['name'] = str_replace(' ', '', $_POST['name']);
			$_POST['name'] = str_replace('\'', '', $_POST['name']);

			$exists = $pdo->prepare('SELECT id FROM rooms WHERE name = ?');
			$exists->execute(array($_POST['name']));
			if($exists->fetchAll() != array()) {
				echo 'exists'; exit;
			}

			$query = $pdo->prepare('INSERT INTO rooms (name, type, creator, added) VALUES (?, ?, ?, date(\'now\'))');
			
			if($query->execute(array($_POST['name'], $_POST['type'], $_SESSION['user']['id']))) {
				$id = $pdo->query('SELECT id FROM rooms ORDER BY id DESC LIMIT 1')->fetchAll();

				// The creator must be allowed to access his room.
				if($_POST['type'] != 'public') {
					$query = $pdo->prepare('INSERT INTO usersAllowed (roomId, userId) VALUES (?, ?)');
					$query->execute(array($id[0]['id'], $_SESSION['user']['id']));
				}
				echo $id[0]['id'];
			}
			else {
				echo 0;
			}
			break;

		case 'join': // Args: room (id)

			// Check auth
			$query = $pdo->prepare('SELECT type FROM rooms WHERE id = ?');
			$query->execute(array($_POST['room'])); 
			$type = $query->fetchAll();
			if($type[0]['type'] != 'public') {
				$query = $pdo->prepare('SELECT userId FROM usersAllowed WHERE roomId = ? AND userId = ?');
				$query->execute(array($_POST['room'], $_SESSION['user']['id'])); 
				if($query->fetchAll() == array()) {
					echo json_encode(array('error' => 'forbidden', 'data' => array($query->fetchAll(), $_POST['room'], $_SESSION['user']['id'])));
					exit;
				}
			}

			$query = $pdo->prepare('SELECT userId FROM usersRooms WHERE roomId = ? AND userId = ?');
			$query->execute(array($_POST['room'], $_SESSION['user']['id']));

			if($query->fetchAll() == array()) {
				$query = $pdo->prepare('INSERT INTO usersRooms(roomId, userId) VALUES (?, ?)');
				$query->execute(array($_POST['room'], $_SESSION['user']['id']));
			}

			$query = $pdo->prepare('SELECT usersRooms.userId,
										   users.name
									FROM usersRooms
									INNER JOIN users
										ON usersRooms.userId = users.id
									WHERE usersRooms.roomId = ?');
			$query->execute(array($_POST['room']));

			echo json_encode(array('type'	   => $type[0]['type'], 
								   'error'	   => 'done',
								   'connected' => $query->fetchAll()));
			break;

		case 'dataFromRoomName': // Args: roomName
			$ids = $pdo->prepare('SELECT id, type FROM rooms WHERE name = ?');
			$ids->execute(array($_POST['roomName']));
			$ids = $ids->fetchAll();
			$response;
			if($ids == array()){
				$response = array('error' => 'unknow');
			}
			else {
				$response = array('error' => 'done', 
								  'id'    => $ids[0]['id'], 
								  'type'  => $ids[0]['type']);
			}
			echo json_encode($response);
	}
