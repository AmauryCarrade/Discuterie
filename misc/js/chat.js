(function($) {

	$(document).ready(function() {
		// Set the height of the chatbox
		$('#content').css('height', (document.body.clientHeight - 250) + 'px')
					 .css('max-height', (document.body.clientHeight - 250) + 'px')
					 .scrollTop(1000);


		lastMessageAuthor = null;


		// Send a message
		sendMessage = function() {
			lastMessageAuthor = 'Amaury';
		}

		$('#messageText').keydown(function (e) {
			if ((e.keyCode === 10 || e.keyCode == 13) && e.ctrlKey) { 
				sendMessage(); 
			}
		});
		$('#send').click(sendMessage);
	});



})(jQuery);