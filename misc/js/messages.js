$(document).ready(function() {
	// Send a message
	var sendMessage = function() {
		if($messageText.val() == '') {
			$messageText.focus();
			return;
		}

		$loaderButton.show();

		htmlMessage = generateMessage($messageText.val(), me.name);
		
		appendMessage(htmlMessage, currentRoom);

		var text = $messageText.val();
		$messageText.val('').focus();

		$.ajax({
			dataType: 'json',
			data: {
				auth: authKey,
				do: 'savePost',
				room: currentRoom,
				text: text
			},
			success: function(error) {
				$loaderButton.hide();
				if(error.error != 'success') {
					if(error.error == 'unknow') {
						notify(t('postErrorTitle'), t('postUnknowRoom', {room: rooms[currentRoom]['name']}));
					}
					else if(error.error == 'forbidden') {
						notify(t('postErrorTitle'), t('postForbidden', {room: rooms[currentRoom]['name']}));
					}
					$messageText.val(text).focus();
				}
			}
		});
	};

	$('#messageText').keydown(function (e) {
		if ((e.keyCode === 10 || e.keyCode == 13) && e.ctrlKey) { 
			sendMessage(); 
		}
	});
	$('#send').click(sendMessage);
});
