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


$.ajaxSetup({
	url: 'do.php',
	type: 'POST'
});


var nl2br = function(str) {
	// Thx to http://phpjs.org/functions/nl2br/
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br ' + '/>' + '$2');
};

var generateHTMLMessage = function(message, author, date, preciseDate, time) {
	if(date == undefined) {
		var months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
		var dateOb = new Date();
		var day = dateOb.getDate() < 10 ? '0' + dateOb.getDate() : dateOb.getDate();
		var hours = dateOb.getHours() < 10 ? '0' + dateOb.getHours() : dateOb.getHours();
		var minutes = dateOb.getMinutes() < 10 ? '0' + dateOb.getMinutes() : dateOb.getMinutes();
		var seconds = dateOb.getSeconds() < 10 ? '0' + dateOb.getSeconds() : dateOb.getSeconds();
		date = day + ' ' + months[dateOb.getMonth()] + ' ' + dateOb.getFullYear() + ' à ' + hours + ':' + minutes;
		preciseDate = date + ':' + seconds;
		time = 'à ' + hours + ':' + minutes;
	}
	
	message = nl2br(message);
	if(rooms[currentRoom]['lastMessageAuthor'] != author) {
		return '<p><strong>' + author + '</strong></p><p></p><div class="muted pull-right message-date" title="' + preciseDate + '"><small>' + date + '</small></div><p>' + message + '</p>';
	}
	else {
		return '<div class="muted pull-right message-date" title="' + preciseDate + '"><small>' + time + '</small></div><p>' + message + '</p>';
	}

};

var generateUnnamedHTMLMessage = function(message, enableDate, date, preciseDate, time) {
	if(enableDate != undefined) {
		if(date == undefined) {
			// DOUBLON -> exporter
			var months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
			var dateOb = new Date();
			var day = dateOb.getDate() < 10 ? '0' + dateOb.getDate() : dateOb.getDate();
			var hours = dateOb.getHours() < 10 ? '0' + dateOb.getHours() : dateOb.getHours();
			var minutes = dateOb.getMinutes() < 10 ? '0' + dateOb.getMinutes() : dateOb.getMinutes();
			var seconds = dateOb.getSeconds() < 10 ? '0' + dateOb.getSeconds() : dateOb.getSeconds();
			date = day + ' ' + months[dateOb.getMonth()] + ' ' + dateOb.getFullYear() + ' à ' + hours + ':' + minutes;
			preciseDate = date + ':' + seconds;
		}
	}
	message = nl2br(message);
	if(enableDate != undefined && enableDate == true) {
		return '<div class="well well-small"><div class="muted pull-right message-date" title="' + preciseDate + '"><small>' + date + '</small></div>' + message + '</div>';
	}
	else {
		return '<div class="well well-small">' + message + '</div>';
	}
};


// Duplicate the insertion of the message in the hidden data div.
var appendMessage = function(content, roomId) {
	$messages.append(content);
	$content.scrollTop(100000);

	$('#data-room-' + roomId).append(content);
};


var notify = function(title, message, type) {
	var classType = null;
	if(type = 'error') 			classType = 'alert-error';
	else if(type = 'success') 	classType = 'alert-success';
	else if(type = 'info') 		classType = 'alert-info';

	var html  = '<div class="alert ' + classType + '">';
	    html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		html += '<strong>' + title + '</strong><br />' + message + '</div>';
	$notifications.prepend(html);
};
