<?php
	if(!defined('_NO_DIRECT_ACCESS')) exit;

	$pdo = SQLiteConnect();

	$rooms = $pdo->query('SELECT rooms.id AS room_id, 
								 rooms.name AS room_name, 
								 rooms.type AS room_type,
								 rooms.added AS room_added,
								 rooms.creator AS creator_id
						  FROM rooms
						  WHERE 
						  	rooms.type != \'private\'
						  ORDER BY room_name');
	$rooms = $rooms->fetchAll();

	$allowedRoomsQ = $pdo->prepare('SELECT roomId FROM usersAllowed WHERE userId = ?');
	$allowedRoomsQ->execute(array($_SESSION['user']['id']));
	$allowedRoomsQ = $allowedRoomsQ->fetchAll();
	$allowedRooms = array();
	foreach ($allowedRoomsQ as $allowedRoom) {
		$allowedRooms[] 			= $allowedRoom['roomId'];
		$_SESSION['allowedRooms'][] = $allowedRoom['roomId'];
	}

	$bannedRoomsQ = $pdo->prepare('SELECT roomId FROM usersBanned WHERE userId = ?');
	$bannedRoomsQ->execute(array($_SESSION['user']['id']));
	$bannedRoomsQ = $bannedRoomsQ->fetchAll();
	$bannedRooms = array();
	foreach ($bannedRoomsQ as $bannedRoom) {
		$bannedRooms[]			   = $bannedRoom['roomId'];
		$_SESSION['bannedRooms'][] = $bannedRoom['roomId'];
	}


	$random = mt_rand(0, 123458) .  microtime();
	$token = sha1(SALT . $random);
	$_SESSION['auth'] = sha1(SALT . $token);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>La discuterie</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="misc/css/bootstrap.min.css" rel="stylesheet">
    <link href="misc/css/chat.css" rel="stylesheet">

    <style>
      
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

  </head>

  <body>

    <div id="wrap">
	    <div class="container">

		    <div class="row">
			    <div class="span3">
			    	<div class="well sidebar-nav">
						<ul class="nav nav-list" id="menuRoomsList">
							<li class="nav-header">Salons</li>
							<li id="listRoomsNoRooms"><a href="#"><em>Aucun salon</em></a></li>
						</ul>
							<hr />
						<ul class="nav nav-list">	
							<li class="goToListRooms active"><a href="#">Liste des salons</a></li>
							<li class="newRoom"><a href="#">Nouveau salon</a></li>
						</ul>
					</div><!--/.well -->
					<div id="notifications"></div>
					<p style="text-align: center;">
						<small class="muted">
							Ce logiciel est actuellement en version alpha.<br />
							Suggestion&nbsp;? Bug&nbsp;? <a href="https://github.com/Bubbendorf/Discuterie/issues">Dites-le !</a> :)
						</small>
					</p>
				</div>

			    <div class="span6">
			    	<div id="chat">
		    			<div class="page-header">
		    				<h2>
		    					<div class="pull-right">
		    					    <div class="btn-group">
										<button class="btn" disabled="disabled">
											<?php echo $_SESSION['user']['name']; ?>
										</button>
										<ul class="dropdown-menu">
											<li class="active"><a href="#">Connecté</a></li>
											<li class="disabled"><a href="#">Absent</a></li>
											<li class="disabled"><a href="#">Ne pas déranger</a></li>
											<li class="disabled"><a href="#">Invisible</a></li>
											<li class="divider"></li>
											<li><a href="?do=out">Déconnexion</a></li>
										</ul>
										<a class="btn btn-dropdown-toggle btn-success" data-toggle="dropdown" href="#">
											Connecté 
											<span class="caret"></span> 
										</a>
									</div>
			    				</div>
		    					<span id="roomName">Tous les salons</span>&nbsp;<span id="iconProtectedRoom"><span class="icon-lock"></span></span>
		    				</h2>
		    			</div>
		    			<div id="content">
		    				<div class="wrapper"></div>
		    			</div>
			    		<div id="post">
			    			<form>
							    <textarea name="" id="messageText" class="span6" rows="1" disabled="disabled" placeholder="Envoyer un message..." style="resize: vertical;"></textarea>
							    <div>
							    	<div class="pull-right">
							    		<span id="loaderButton" class="hide"><img src="misc/img/loader-small.gif" alt="Envoi..." title="Envoi du message en cours..." /></span><button type="button" class="btn btn-primary" id="send">Envoyer</button>
							    	</div>
							    	<p class="muted"><small>Envoyez le message en tapant <code>Ctrl</code> + <code>Entrée</code>, ou avec le bouton.</small></p>
							    </div>
							</form>
						</div>
			    	</div>
			    </div>
			    <div class="span3">
			    	<div class="well sidebar-nav" style="display: none;" id="frameListConnectedUsers">
						<ul class="nav nav-list" id="listConnectedUsers">
							<li class="nav-header">Utilisateurs connectés</li>
						</ul>
					</div><!--/.well -->
			    </div>
		    </div>
	    </div> <!-- /container -->

		<!-- Data storage in divs -->
		<div style="display: none;">
			
			<div id="roomsList">
				<div class="alert alert-info noticeNotConnected">
					Vous n'êtes connecté(e) à aucun salon.<br />
					<small>
						Choisissez ci-dessous un salon à joindre. 
						Veuillez noter que certains salons sont protégés, et d'autres invisibles.<br />
						Pour vous connecter à un salon invisible, saisissez son nom ci-dessous.
					</small>
				</div>
				<div class="pull-right">
					<button class="btn btn-primary newRoom">Créer un salon</button>
				</div>
				
				<div class="input-append">
					<form class="formSecretRoom control-group">
						<input type="text" class="secretRoom" class="input-xlarge" placeholder="Joindre un salon : son nom ?" autocomplete="off" />
						<button class="btn btn-primary">Joindre</button>
					</form>
				</div>
				
				<h3>Liste des salons</h3>
				    <table class="table table-striped" id="tableRooms">
						<thead>
							<tr>
								<th>#</th>
								<th>Nom du salon</th>
								<th>Connectés</th>
								<th>Joindre</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($rooms == array()) {
									?>

									<tr class="noRooms">
										<td colspan="5" style="text-align: center; font-style: italic;">Aucun salon n'existe. Créez-en un à l'aide du bouton.</td>
									</tr>

									<?php
								}
								else {
									foreach($rooms AS $room) {
										?>

										<tr>
											<td><?php echo $room['room_id']; ?></td>
											<td><?php echo $room['room_name']; ?></td>
											<td><?php //echo $room['numConnected']; ?></td>
											<td>
												<?php if($room['room_type'] == 'public' || in_array($room['room_id'], $allowedRooms)): ?>
												<button class="btn btn-link join" data-room-id="<?php echo $room['room_id']; ?>" data-room-name="<?php echo $room['room_name']; ?>">Joindre</button>
												<?php else: ?>
												<button class="btn btn-link join disabled" data-room-id="<?php echo $room['room_id']; ?>" data-room-name="<?php echo $room['room_name']; ?>" disabled="disabled">Salon protégé</button>
												<?php endif; ?>
											</td>
											<td>
												<a href="#" class="tooltip-top" title="Opérateurs du salon"><span class="icon-briefcase"></span></a>
											</td>
										</tr>

										<?php
									}
								}
							?>
						</tbody>	
					</table>
			</div> <!-- /#roomsList -->

			<div id="dataRooms">
				
			</div>

		</div>

		<!-- Modals -->
		<div class="modal hide fade" id="modalNewRoom">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Créer un salon</h3>
			</div>
			<div class="modal-body">
				<p>
					Il existe plusieurs types de salons : 
					<ul>
						<li><strong>les salons publiques</strong>, qui sont accessibles à n'importe qui ; </li>
						<li><strong>les salons protégés</strong>, nécessitant une invitation ; et</li>
						<li><strong>les salons privés</strong>, qui nécessitent une invitation et ne sont pas affichés dans la liste des salons.</li>
					</ul>

					<form class="form-horizontal">
						<div class="control-group">
							<label class="control-label" for="newRoomName">Nom du salon</label>
							<div class="controls">
								<input type="text" id="newRoomName" placeholder="Espaces interdites">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="newRoomType">Type de salon</label>
							<div class="controls">
								<select id="newRoomType">
									<option value="public">Salon public</option>
									<option value="protected">Salon protégé</option>
									<option value="private">Salon privé</option>
								</select>
							</div>
						</div>
					</form>
				</p>
			</div>
			<div class="modal-footer">
				<div class="pull-left muted" style="font-size: xx-small; text-align: left;">
					Veuillez actualiser la page après création du/des salons.<br />
					Ce bug sera corrigé, <span title="MERCI, Captain Obvious...">mais ne l'est pas encore</span>.
				</div>
				<a href="#" class="btn" data-dismiss="modal">Annuler</a>
				<a href="#" class="btn btn-primary" id="newRoomCreate" data-loading-text="Création...">Créer le salon</a>
			</div>
		</div>

	</div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.1.0.min.js"></script>
    <script src="misc/js/bootstrap.min.js"></script>
    <script type="text/javascript">
		var me = {
				id: '<?php echo $_SESSION['user']['id']; ?>',
				name: '<?php echo $_SESSION['user']['name']; ?>'
			},

			$messageText   = $('#messageText'),
			$messages      = $('#content .wrapper'),
			$content       = $('#content'),
			$roomsList     = $('#roomsList'),
			$notifications = $('#notifications'),

			$loaderButton  = $('#loaderButton');

			authKey        = '<?php echo $token; ?>',

			rooms          = new Array(),
			avaliableRooms = new Array(),
			allowedRooms   = new Array(),
			typeaheadRooms = new Array(),
			$data          = $('#dataRooms'),
			currentRoom    = null,

			usersNames     = new Array(),

			texts          = new Array();

		// Texts
		texts['errorProtectedRoom'] = 'Ce salon est protégé et vous n\'avez pas le droit d\'y accéder.';
		texts['errorUnknowRoom'] = 'Ce salon n\'existe pas. Vous pouvez le créer en cliquant sur le bouton à droite du champ.';
		texts['unknowError'] = 'Une erreur s\'est produite. Veuillez réessayer.';

		texts['postErrorTitle'] = 'Erreur d\'envoi';
		texts['postUnknowRoom'] = 'Vous tentez de poster dans le salon {room}, qui n\'existe pas ou plus.';
		texts['postForbidden'] = 'Vous n\'avez pas le droit de poster dans le salon {room}.';

		// Generated list of avaliable (public and protected) rooms.
		<?php
			foreach($rooms AS $room) {
				echo 'avaliableRooms[' . $room['room_id'] . '] = new Array(); avaliableRooms[' . $room['room_id'] . '][\'name\'] = \'' . $room['room_name'] . '\'; avaliableRooms[' . $room['room_id'] . '][\'type\'] = \'' . $room['room_type'] . '\';' . "\n";
			}
		?>
		<?php
			$i = 0;
			foreach($rooms AS $room) {
				echo 'typeaheadRooms[' . $i . '] = \'' . $room['room_name'] . '\'' . "\n";
				$i++;
			}
			unset($i);
		?>
		<?php
			foreach($allowedRooms AS $roomId) {
				echo 'allowedRooms[' . $roomId . '] = \'' . $roomId . '\'' . "\n";
			}
		?>
    </script>
    <script src="misc/js/utilities.js"></script>
    <script src="misc/js/rooms.js"></script>
    <script src="misc/js/messages.js"></script>
    <script src="misc/js/update.js"></script>
  </body>
</html>
