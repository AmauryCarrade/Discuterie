$(document).ready(function() {
	// Send a message
	var sendMessage = function() {
		if($messageText.val() == '') {
			$messageText.focus();
			return;
		}

		var regexMe = /^\/me/i;
		var htmlMessage;
		if(regexMe.test($messageText.val())) {
			htmlMessage = generateUnnamedHTMLMessage('<strong>' + me.name + '</strong>' + $messageText.val().replace('/me ', ' '), true); // true: show date
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
