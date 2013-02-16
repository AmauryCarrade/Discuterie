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

			break;

		case 'getUsername': // Args: userId
			$q = $pdo->prepare('SELECT name FROM users WHERE id = ?');
			$q->execute(array($_POST['userId']));
			$name = $q->fetchAll();

			if($name == array()) exit;
			else 				 echo $name[0]['name'];

			break;



		case 'savePost': // Args: room, text
			$roomId = $_POST['room']; $text = $_POST['text'];
			$userId = $_SESSION['user']['id'];

			// Data caching
			if(!isset($_SESSION['allowedRooms'])) {
				$allowedRoomsQ = $pdo->prepare('SELECT roomId FROM usersAllowed WHERE userId = ?');
				$allowedRoomsQ->execute(array($_SESSION['user']['id']));
				$allowedRoomsQ = $allowedRoomsQ->fetchAll();
				$allowedRooms = array();
				$_SESSION['allowedRooms'] = array();
				foreach ($allowedRoomsQ as $allowedRoom) {
					$_SESSION['allowedRooms'][] = $allowedRoom['roomId'];
				}
			}
			if(!isset($_SESSION['bannedRooms'])) {
				echo 'Yep';
				$bannedRoomsQ = $pdo->prepare('SELECT roomId FROM usersBanned WHERE userId = ?');
				$bannedRoomsQ->execute(array($_SESSION['user']['id']));
				$bannedRoomsQ = $bannedRoomsQ->fetchAll();
				$bannedRooms = array();
				$_SESSION['bannedRooms'] = array();
				foreach ($bannedRoomsQ as $bannedRoom) {
					$_SESSION['bannedRooms'][] = $bannedRoom['roomId'];
				}
			}

			// First check: is the user allowed to post in this room?
			$roomType = $pdo->prepare('SELECT type FROM rooms WHERE id = ?');
			$roomType->execute(array($roomId));
			$roomType = $roomType->fetchAll();
			if($roomType == array()) {
				echo json_encode(array('error' => 'unknow'));
				exit;
			}
			$roomType = $roomType[0]['type'];

			if(!in_array($roomId, $_SESSION['bannedRooms']) && ($roomType == 'public' || in_array($roomId, $_SESSION['allowedRooms']))) {
				// All done
				$query = $pdo->prepare('INSERT INTO posts (content, userId, roomId, pubDate) VALUES (?, ?, ?, datetime(\'now\'))');
			
				if($query->execute(array($text, $userId, $roomId))) {
					echo json_encode(array('error' => 'success'));
					exit;
				}
				else {
					echo json_encode(array('error' => 'saving'));
					exit;
				}
			}
			else {
				echo json_encode(array('error' => 'forbidden'));
				exit;
			}
			break;


		case 'update': // Args: lastUpdate (millisecondes since midnight of January 1, 1970 UTC)
			$_POST['lastUpdate'] = (float) $_POST['lastUpdate'];
			$timestamp = $_POST['lastUpdate']/1000;

			define('SQLITE_DATE_FORMAT', 'Y-m-d H:i:s');
			date_default_timezone_set('UTC');


			// First: what messages do we retrieve?
			$query = $pdo->prepare('SELECT roomId FROM usersRooms WHERE userId = ?');
			$query->execute(array($_SESSION['user']['id']));

			$answer = array();

			$rooms = array();
			foreach($query->fetchAll() AS $room) {
				$rooms[] = (int) $room['roomId'];
			}

			if($rooms != array()) {

				// Two: build the SQL request to load posts younger than "lastUpdate" and from these rooms.
				$sql = 'SELECT * FROM posts WHERE pubDate > datetime(\'' . date(SQLITE_DATE_FORMAT, $timestamp) . '\') AND userId != ? AND (';
				$lastItem = count($rooms) - 1;
				foreach ($rooms as $key => $room) {
					$sql .= 'roomId = ' . $room;
					if($key != $lastItem) {
						$sql .= ' OR ';
					}
				}
				$sql .= ')';
				
				// Three: execute it. #Obvious
				$query = $pdo->prepare($sql);
				$query->bindParam(1, $_SESSION['user']['id'], PDO::PARAM_INT);
				$query->execute();

				$answer['posts'] = $query->fetchAll();
			}

			/*echo $sql . "\n";
			echo $timestamp . "\n";
			echo $_SESSION['user']['id'] . "\n";*/

			echo json_encode($answer);

			break;
	}
