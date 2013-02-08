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
      	padding-top: 60px; 
      }

      #content {
      	min-height: 100%;
        height: auto !important;
        height: 100%;
      	overflow: auto;
      }
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

  </head>

  <body>

    <div id="wrap">
	    <div class="navbar navbar-inverse navbar-fixed-top" style="position: absolute;">
	      <div class="navbar-inner">
	        <div class="container">
	          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a class="brand" href="#">Discuterie</a>
	          <div class="nav-collapse collapse">
	          	<div class="pull-right">
	          		<ul class="nav">
	          			<li><a href="?do=out">Déconnexion</a></li>
	          		</ul>
	          	</div>

	          </div><!--/.nav-collapse -->
	        </div>
	      </div>
	    </div>

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
										<a class="btn btn-dropdown-toggle btn-success" data-toggle="dropdown" href="#">
											Connecté 
											<span class="caret"></span> 
										</a>
										<ul class="dropdown-menu">
											<li class="active"><a href="#">Connecté</a></li>
											<li><a href="#">Absent</a></li>
											<li><a href="#">Ne pas déranger</a></li>
											<li><a href="#">Invisible</a></li>
										</ul>
									</div>
			    				</div>
		    					Salon 1
		    				</h2>
		    			</div>
		    			<div id="content">
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
			    		<div id="post">
			    			<form>
							    <div class="input-append">
							    <input type="text" id="appendedInputButton" class="span6">
							    <button type="button" class="btn btn-primary">Envoyer</button>
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
    <script src="misc/js/chat.js"></script>
  </body>
</html>