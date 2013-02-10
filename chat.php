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
		$allowedRooms[] = $allowedRoom['roomId'];
	}

	$bannedRoomsQ = $pdo->prepare('SELECT roomId FROM usersBanned WHERE userId = ?');
	$bannedRoomsQ->execute(array($_SESSION['user']['id']));
	$bannedRoomsQ = $bannedRoomsQ->fetchAll();
	$bannedRooms = array();
	foreach ($bannedRoomsQ as $bannedRoom) {
		$bannedRooms[] = $bannedRoom['roomId'];
	}

	$random = mt_rand(0, 123458) .  microtime();
	$_SESSION['auth'] = sha1(SALT . sha1(SALT . $random));
	$token = sha1(SALT . $random);
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
    <link href="misc/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        
      }

      html,
      body {
        height: 100%;
        /* The html and body elements cannot have any padding or margin. */
      }

      /* Wrapper for page content to push down footer */
      #wrap {
        min-height: 100%;
        height: auto !important;
        height: 100%;
        /* Negative indent footer by it's height */
        margin: 0;
      }

      .row {
      	padding-top: 5px; 
      }

      #content {
      	/*min-height: 100%;
        height: auto !important;
        height: 100%;
        max-height: 100%;*/
      	overflow: auto;
      }
      #content .wrapper {
      	padding: 5px;
      }
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
					<p>
						<small class="muted">
							Ce logiciel est actuellement en bêta.<br />
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
		    					<span id="roomName">Tous les salons</span>
		    				</h2>
		    			</div>
		    			<div id="content">
		    				<div class="wrapper"></div>
		    			</div>
			    		<div id="post">
			    			<form>
							    <textarea name="" id="messageText" class="span6" rows="1" disabled="disabled" placeholder="Envoyer un message..." style="resize: none;"></textarea>
							    <div>
							    	<div class="pull-right">
							    		<button type="button" class="btn btn-primary" id="send">Envoyer</button>
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
						<input type="text" class="secretRoom" class="input-xlarge" placeholder="Joindre un salon: son nom ?" autocomplete="off" />
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
    <script src="misc/js/bootstrap.js"></script>
    <!--<script src="misc/js/chat.js"></script>-->
    <script type="text/javascript">
    	(function($) {

    		// Utils
    		var nl2br = function(str) {
    			// Thx to http://phpjs.org/functions/nl2br/
    			return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br ' + '/>' + '$2');
    		};

			$(document).ready(function() {

				var me = {
						id: '<?php echo $_SESSION['user']['id']; ?>',
						name: '<?php echo $_SESSION['user']['name']; ?>'
					},

					$messageText   = $('#messageText'),
					$messages      = $('#content .wrapper'),
					$content       = $('#content'),
					$roomsList     = $('#roomsList'),

					authKey        = '<?php echo $token; ?>',

					rooms          = new Array(),
					avaliableRooms = new Array(),
					allowedRooms   = new Array(),
					typeaheadRooms = new Array(),
					$data          = $('#dataRooms'),
					currentRoom    = null;

				// Generated list of avaliable (public and protected) rooms.
				<?php
					foreach($rooms AS $room) {
						echo 'avaliableRooms[' . $room['room_id'] . '] = new Array(); avaliableRooms[' . $room['room_id'] . '][\'name\'] = \'' . $room['room_name'] . '\'; avaliableRooms[' . $room['room_id'] . '][\'type\'] = \'' . $room['room_type'] . '\';' . "\n";
					}
				?>
				<?php
					foreach($rooms AS $room) {
						echo 'typeaheadRooms[' . ($room['room_id'] - 1) . '] = \'' . $room['room_name'] . '\'' . "\n";
					}
				?>
				<?php
					foreach($allowedRooms AS $roomId) {
						echo 'allowedRooms[' . $roomId . '] = \'' . $roomId . '\'' . "\n";
					}
				?>

				$.ajaxSetup({
					url: 'do.php',
					type: 'POST'
				});

				// Set the height of the chatbox
				var updateContentHeight = function() {
					$content.css('height', (document.body.clientHeight - 250) + 'px')
							.css('max-height', (document.body.clientHeight - 250) + 'px')
							.scrollTop(1000);
				};
				$(window).resize(updateContentHeight);
				updateContentHeight();

				$('.tooltip-top').tooltip({placement: 'top'});

				// Show list of rooms on startup.
				$messages.html($roomsList.html());


				// New rooms
				$('.newRoom').live('click', function() {
					$('#modalNewRoom').modal('show');
				});
				$('#newRoomCreate').click(function() {
					var $button = $('#newRoomCreate');
					$button.button('loading');

					var $name = $('#newRoomName'),
						$type = $('#newRoomType');

					if($name.val() == '') {
						$name.parent().parent().addClass('error');
						$button.button('reset');
					}
					else {
						$.ajax({
							data: {
								name: $name.val(),
								type: $type.val(),
								do: 'newRoom',
								auth: authKey
							}, 
							success: function(id) {
								if(id == 0) { // SQL error
									var oldName = $button.val();
									$button.button('reset').addClass('btn-error').val('Erreur');
									setTimeout(function() {
										$button.removeClass('btn-error').val(oldName);
									}, 4000);
									return;
								}
								$('#modalNewRoom').modal('hide');
								var name = $name.val().replace(' ', '');
								var row = '<tr><td>' + id + '</td><td>' + name + '</td><td>0</td><td><a href="#" class="join" data-room-id="' + id + ' data-room-name="' + name + '">Joindre</a></td><td></td></tr>';
								if($('#tableRooms tbody tr:first').hasClass('noRooms')) {
									$('#tableRooms tbody').html(row);
								}
								else {
									$('#tableRooms tbody').append(row);
								}

								$name.val('');

								document.reload;
							},
							error: function() {
								var oldName = $button.val();
								$button.button('reset').addClass('btn-error').val('Erreur');
								setTimeout(function() {
									$button.removeClass('btn-error').val(oldName);
								}, 4000);
							}
						});
					}
				});



				var generateHTMLMessage = function(message, author, date, preciseDate) {
					if(date == undefined) {
						var months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
						var dateOb = new Date();
						var day = dateOb.getDate() < 10 ? '0' + dateOb.getDate() : dateOb.getDate();
						var hours = dateOb.getHours() < 10 ? '0' + dateOb.getHours() : dateOb.getHours();
						var minutes = dateOb.getMinutes() < 10 ? '0' + dateOb.getMinutes() : dateOb.getMinutes();
						var seconds = dateOb.getSeconds() < 10 ? '0' + dateOb.getSeconds() : dateOb.getSeconds();
						date = day + ' ' + months[dateOb.getMonth()] + ' ' + dateOb.getFullYear() + ' à ' + hours + ':' + minutes;
						preciseDate = date + ':' + seconds;
					}
					
					message = nl2br(message);
					if(rooms[currentRoom]['lastMessageAuthor'] != author) {
						return '<p><strong>' + author + '</strong></p><div class="muted pull-right" title="' + preciseDate + '">' + date + '</div><p></p><p>' + message + '</p>';
					}
					else {
						return '<p>' + message + '</p>';
					}

				};

				var generateUnnamedHTMLMessage = function(message) {
					message = nl2br(message);
					return '<div class="well well-small">' + message + '</div>';
				};


				// Duplicate the insertion of the message in the hidden data div.
				var appendMessage = function(content, roomId) {
					$messages.append(content);
					$content.scrollTop(100000);

					$('#data-room-' + roomId).append(content);
				};



				
				// Join & switch room
				var $menuRoomsList = $('#menuRoomsList'),
					$roomName  = $('#roomName'),
					$listConnectedUsers = $('#listConnectedUsers'), 
					$frameListConnectedUsers = $('#frameListConnectedUsers');
				$('.secretRoom').typeahead({
					source: typeaheadRooms,
					items: 8
				});
				$('.formSecretRoom').live('submit', function(e) {
					e.preventDefault();
					var roomName = $('.secretRoom').val();
					$.ajax({
						dataType: 'json',
						data: {
							auth: authKey,
							do: 'dataFromRoomName',
							roomName: roomName
						},
						success: function(room) {
							if(room.error == 'unknow') {
								$('.formSecretRoom').addClass('error');
								return;
							}
							if(room.type == 'public' || allowedRooms[room.id] != undefined) {
								$('.secretRoom').val('');
								$('.formSecretRoom').removeClass('error');
								joinRoom(room.id, roomName);
							}
						}
					});
				});

				var switchRoom = function(roomId) {
					if(currentRoom == roomId) return;

					$messageText.removeAttr('disabled');
					$roomName.text(rooms[roomId]['name']);
					$messages.html($('#data-room-' + roomId).html());
					currentRoom = roomId;

					$('#menuRoomsList li, .goToListRooms').removeClass('active');
					$('#goToRoom' + roomId).addClass('active');

					reloadUIConnectedUsers(rooms[roomId]['connected']);

					$messageText.focus();
				};

				var joinRoom = function(roomId, roomName) {
					$('#listRoomsNoRooms').hide();
					$('.goToListRooms').removeClass('active');
					$('.noticeNotConnected').hide();

					if(rooms[roomId] == undefined) {
						var html = '<li class="active" data-id="' + roomId + '" id="goToRoom' + roomId + '"><a href="#">' + roomName + '</a></li>';
						$menuRoomsList.append(html);

						$messageText.removeAttr('disabled');
						$roomName.text(roomName);

						currentRoom = roomId;

						rooms[currentRoom] = new Array();
						rooms[currentRoom]['name'] = roomName;
						rooms[currentRoom]['lastMessageAuthor'] = null;

						$data.append('<div id="data-room-' + currentRoom + '"></div>');

						$messages.html('');
						appendMessage(generateUnnamedHTMLMessage('Vous avez rejoint le salon.'), currentRoom);

						$messageText.focus();

						// Under the hole
						$.ajax({
							data: {
								do: 'join',
								auth: authKey,
								room: roomId
							},
							dataType: 'json',
							success: function(connected) {
								rooms[currentRoom]['connected'] = connected;
								reloadUIConnectedUsers(connected);
							}
						});
					}
					else {
						switchRoom(roomId);
					}
				};

				var reloadUIConnectedUsers = function(connected) {
					$listConnectedUsers.html('');
					$frameListConnectedUsers.show();
					$listConnectedUsers.append('<li class="nav-header">Utilisateurs connectés</li>');
					for(userRow in connected) {
						$listConnectedUsers.append('<li><a href="#" data-user-id="' + connected[userRow].userId + '">' + connected[userRow].name + '</a></li>');
					}
				};

				$('#menuRoomsList li').live('click', function() {
					switchRoom($(this).data('id'));
				});

				$('.join').live('click', function(event) {
					joinRoom($(this).data('room-id'), $(this).data('room-name'));
				});
				
				$('.goToListRooms').click(function() {
					currentRoom = null;
					$messageText.attr('disabled', 'disabled');
					$messages.html($roomsList.html());
					$('#menuRoomsList li').removeClass('active');
					$(this).addClass('active');
					$roomName.text('Tous les salons');
					$frameListConnectedUsers.hide();
				});


				// Send a message
				var sendMessage = function() {
					var regexMe = /^\/me/i;
					var htmlMessage;
					if(regexMe.test($messageText.val())) {
						htmlMessage = generateUnnamedHTMLMessage('<strong>' + me.name + '</strong>' + $messageText.val().replace('/me ', ' '));
						rooms[currentRoom]['lastMessageAuthor'] = null;
					}
					else {
						htmlMessage = generateHTMLMessage($messageText.val(), me.name);
						rooms[currentRoom]['lastMessageAuthor'] = me.name;
					}
					
					appendMessage(htmlMessage, currentRoom);

					$messageText.val('').focus();
				};

				$('#messageText').keydown(function (e) {
					if ((e.keyCode === 10 || e.keyCode == 13) && e.ctrlKey) { 
						sendMessage(); 
					}
				});
				$('#send').click(sendMessage);
			});



		})(jQuery);
    </script>
  </body>
</html>