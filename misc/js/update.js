$(document).ready(function() {

	// NOTICE: the server uses an UTC time.


	var lastUpdate = new Date();
	lastUpdate = Date.parse(lastUpdate.toUTCString());

	var updater = function() {
		$.ajax({
			dataType: 'json',
			data: {
				do: 'update',
				auth: authKey,
				lastUpdate: lastUpdate
			},
			success: function(data) {
				console.log(data.posts);
				for(postField in data.posts) {
					author = getUsername(data.posts[postField]['userId']);
					text   = data.posts[postField]['content'];
					date   = new Date(data.posts[postField]['pubDate'].replace(/-/g, '/'));

					htmlMessage = generateMessage(text, author, date);

					appendMessage(htmlMessage, data.posts[postField]['roomId']);
				}

				lastUpdate = new Date();
				lastUpdate = Date.parse(lastUpdate.toUTCString());
			},
			error: function(xhr, error) {
				console.log(error);
			}
		});
	};

	var timer = setInterval(updater, 4000);
});

