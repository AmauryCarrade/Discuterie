$(document).ready(function() {
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
					var row = '<tr><td>' + id + '</td><td>' + name + '</td><td></td><td><a href="#" class="join" data-room-id="' + id + ' data-room-name="' + name + '">Joindre</a></td><td></td></tr>';
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


	// Join & switch room
	var $menuRoomsList = $('#menuRoomsList'),
		$roomName  = $('#roomName'),
		$listConnectedUsers = $('#listConnectedUsers'), 
		$frameListConnectedUsers = $('#frameListConnectedUsers'),

		$iconProtectedRoom = $('#iconProtectedRoom');

	$('.secretRoom').typeahead({
		source: typeaheadRooms,
		items: 8
	});
	$('.formSecretRoom').live('submit', function(e) {
		e.preventDefault();
		var roomName = $('.secretRoom').val();
		if(roomName == '') return;

		$.ajax({
			dataType: 'json',
			data: {
				auth: authKey,
				do: 'dataFromRoomName',
				roomName: roomName
			},
			success: function(room) {
				if(room.error != 'unknow' && (room.type == 'public' || allowedRooms[room.id] != undefined)) {
					$('.secretRoom').val('');
					$('.formSecretRoom').removeClass('error');
					joinRoom(room.id, roomName);
				}
				else if (room.error != 'unknow' && room.type != 'public' && allowedRooms[room.id] == undefined) {
					$('.formSecretRoom').addClass('error');
					setTimeout(function() { $('.formSecretRoom').removeClass('error'); }, 4000);
					$('.secretRoom').select();
					notify(roomName, texts['errorProtectedRoom'], 'error');
					return;
				}
				else {
					$('.formSecretRoom').addClass('error');
					setTimeout(function() { $('.formSecretRoom').removeClass('error'); }, 4000);
					$('.secretRoom').select();
					notify(roomName, texts['errorUnknowRoom'], 'error');
					return;
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

		if(rooms[roomId]['type'] != 'public') $iconProtectedRoom.show();
		else 								  $iconProtectedRoom.hide();

		reloadUIConnectedUsers(rooms[roomId]['connected']);

		$messageText.focus();
	};

	var joinRoom = function(roomId, roomName) {
		$('#listRoomsNoRooms').hide();
		$('.goToListRooms').removeClass('active');
		$('.noticeNotConnected').hide();

		if(rooms[roomId] == undefined) {
			$.ajax({
				data: {
					do: 'join',
					auth: authKey,
					room: roomId
				},
				dataType: 'json',
				success: function(roomData) {
					if(roomData['error'] == 'done') {
						var html = '<li class="active" data-id="' + roomId + '" id="goToRoom' + roomId + '"><a href="#">' + roomName + '</a></li>';
						$menuRoomsList.append(html);

						$messageText.removeAttr('disabled');
						$roomName.text(roomName);

						currentRoom = roomId;

						rooms[currentRoom] = new Array();
						rooms[currentRoom]['name'] = roomName;
						rooms[currentRoom]['type'] = roomData['type'];
						rooms[currentRoom]['lastMessageAuthor'] = null;

						console.log(rooms, currentRoom);

						if(rooms[currentRoom]['type'] != 'public') $iconProtectedRoom.show();
						else 									   $iconProtectedRoom.hide();

						$data.append('<div id="data-room-' + currentRoom + '"></div>');

						$messages.html('');
						appendMessage(generateUnnamedHTMLMessage('Vous avez rejoint le salon.'), currentRoom);

						$messageText.focus();

						rooms[currentRoom]['connected'] = roomData['connected'];
						reloadUIConnectedUsers(roomData['connected']);
					}
					else if(roomData['error'] == 'forbidden') {
						notify(roomName, texts['errorProtectedRoom'], 'error');
					}
					else {
						notify(roomName, texts['unknowError'], 'error');
					}
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
		$iconProtectedRoom.hide();
	});
});
