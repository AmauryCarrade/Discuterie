<?php
	if(!defined('_NO_DIRECT_ACCESS')) exit;


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Disussion instantanée</title>
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
						<ul class="nav nav-list">
							<li class="nav-header">Salons</li>
							<li class="active"><a href="#">Salon 1</a></li>
							<hr />
							<li><a href="#">Liste des salons</a></li>
							<li><a href="#">Rejoindre un salon</a></li>
						</ul>
					</div><!--/.well -->
				</div>

			    <div class="span6">
			    	<div id="chat">
		    			<div class="page-header">
		    				<h2>
		    					<div class="pull-right">
		    					    <div class="btn-group">
										<button class="btn" title="Quitter le salon">
											<span class="icon-remove"></span>
										</button>
										<ul class="dropdown-menu">
											<li class="active"><a href="#">Connecté</a></li>
											<li><a href="#">Absent</a></li>
											<li><a href="#">Ne pas déranger</a></li>
											<li><a href="#">Invisible</a></li>
											<li class="divider"></li>
											<li><a href="?do=out">Déconnexion</a></li>
										</ul>
										<a class="btn btn-dropdown-toggle btn-success" data-toggle="dropdown" href="#">
											Connecté 
											<span class="caret"></span> 
										</a>
									</div>
			    				</div>
		    					Salon 1
		    				</h2>
		    			</div>
		    			<div id="content">
		    				<div class="wrapper">
			    				<p>
			    					<strong>Amaury</strong>&nbsp;&nbsp;
			    				</p>
			    				<div class="muted pull-right">6 février 2013 à 18:30</div>
			    				<p></p>
			    				<p>
			    					What's up?
	                  			</p>

	                  			<hr style="margin-top: 5px; margin-bottom: 5px;">

								<p>
									<strong>Emma</strong>
								</p>
								<div class="muted pull-right">6 février 2013 à 18:30</div>
								<p></p>
								<p>
									What about no?
								</p>
								<p>
									Bien et toi ?
								</p>

								<hr style="margin-top: 5px; margin-bottom: 5px;">
								<p>
									<strong>Amaury</strong>
								</p>
								<div class="muted pull-right">6 février 2013 à 18:30</div>
								<p></p>
								<p>
									Le code source : <br />
									<pre>
	&lt;div class="tabbable tabs-left"&gt;
		&lt;ul class="nav nav-tabs"&gt;
			...
		&lt;/ul&gt;
		&lt;div class="tab-content"&gt;
			...
		&lt;/div&gt;
	&lt;/div&gt;
									</pre>
								</p>

								<p>
			    					<strong>Amaury</strong>&nbsp;&nbsp;
			    				</p>
			    				<div class="muted pull-right">6 février 2013 à 18:30</div>
			    				<p></p>
			    				<p>
			    					What's up?
	                  			</p>

	                  			<hr style="margin-top: 5px; margin-bottom: 5px;">

								<p>
									<strong>Emma</strong>
								</p>
								<div class="muted pull-right">6 février 2013 à 18:30</div>
								<p></p>
								<p>
									What about no?
								</p>
								<p>
									Bien et toi ?
								</p>

								<hr style="margin-top: 5px; margin-bottom: 5px;">
								<p>
									<strong>Amaury</strong>
								</p>
								<div class="muted pull-right">6 février 2013 à 18:30</div>
								<p></p>
								<p>
									Le code source : <br />
									<pre>
	&lt;div class="tabbable tabs-left"&gt;
		&lt;ul class="nav nav-tabs"&gt;
			...
		&lt;/ul&gt;
		&lt;div class="tab-content"&gt;
			...
		&lt;/div&gt;
	&lt;/div&gt;
									</pre>
								</p>

								<p>
			    					<strong>Amaury</strong>&nbsp;&nbsp;
			    				</p>
			    				<div class="muted pull-right">6 février 2013 à 18:30</div>
			    				<p></p>
			    				<p>
			    					What's up?
	                  			</p>

	                  			<hr style="margin-top: 5px; margin-bottom: 5px;">

								<p>
									<strong>Emma</strong>
								</p>
								<div class="muted pull-right">6 février 2013 à 18:30</div>
								<p></p>
								<p>
									What about no?
								</p>
								<p>
									Bien et toi ?
								</p>

								<hr style="margin-top: 5px; margin-bottom: 5px;">
								<p>
									<strong>Amaury</strong>
								</p>
								<div class="muted pull-right">6 février 2013 à 18:30</div>
								<p></p>
								<p>
									Le code source : <br />
									<pre>
	&lt;div class="tabbable tabs-left"&gt;
		&lt;ul class="nav nav-tabs"&gt;
			...
		&lt;/ul&gt;
		&lt;div class="tab-content"&gt;
			...
		&lt;/div&gt;
	&lt;/div&gt;
									</pre>
								</p>
								<p></p>
							</div>
		    			</div>
			    		<div id="post">
			    			<form>
							    <textarea name="" id="messageText" class="span6" rows="1" placeholder="Envoyer un message..." style="resize: none;"></textarea>
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
			    	<div class="well sidebar-nav">
						<ul class="nav nav-list">
							<li class="nav-header">Utilisateurs connectés</li>
							<li class="active"><a href="#">Amaury</a></li>
						</ul>
					</div><!--/.well -->
			    </div>
		    </div>
	    </div> <!-- /container -->
	</div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script src="misc/js/bootstrap.js"></script>
    <!--<script src="misc/js/chat.js"></script>-->
    <script type="text/javascript">
    	(function($) {

			$(document).ready(function() {

				var lastMessageAuthor = '',
					me = {
						id: '<?php echo $_SESSION['user']['id']; ?>',
						name: '<?php echo $_SESSION['user']['name']; ?>'
					},
					$messageText = $('#messageText'),
					$messages    = $('#content .wrapper'),
					$content     = $('#content');


				// Set the height of the chatbox
				$content.css('height', (document.body.clientHeight - 250) + 'px')
						.css('max-height', (document.body.clientHeight - 250) + 'px')
						.scrollTop(1000);


				var generateHTMLMessage = function(message, author, date, preciseDate) {
					if(date == undefined) {
						var months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
						dateOb = new Date();
						day = dateOb.getDay() < 10 ? '0' + dateOb.getDay() : dateOb.getDay();
						date = day + ' ' + months[dateOb.getMonth()] + ' ' + dateOb.getFullYear() + ' à ' + dateOb.getHours() + ':' + dateOb.getMinutes();
						preciseDate = date + ':' + dateOb.getSeconds();
					}
					
					if(lastMessageAuthor != author) {
						return '<p><strong>' + author + '</strong></p><div class="muted pull-right" title="' + preciseDate + '">' + date + '</div><p></p><p>' + message + '</p>';
					}
					else {
						return '<p>' + message + '</p>';
					}

				}

				// Send a message
				var sendMessage = function() {

					var htmlMessage = generateHTMLMessage($messageText.val(), me.name);
					
					$messages.append(htmlMessage);
					$content.scrollTop(100000);

					lastMessageAuthor = me.name;

					$messageText.val('').focus();
				}

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