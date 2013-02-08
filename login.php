<?php
	if(!defined('_NO_DIRECT_ACCESS')) exit;

	if(isset($_SESSION['loggedIn'])) {
		header('Location: index.php');
		exit;
	}

	$error  = false;
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$pdo    = SQLiteConnect();
		$query  = $pdo->prepare('SELECT * FROM users WHERE name = ? AND password = ?');
		$query->execute(array($_POST['username'], sha1($_POST['password'] . SALT)));
		$data = $query->fetchAll();

		if($data === array()) {
			$error = true;
		}
		else {
			$_SESSION['loggedIn'] = true;
			$_SESSION['user']     = $data[0];
			header('Location: index.php');
			exit;
		}
	}
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
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">Discuterie</a>
          <div class="nav-collapse collapse"></div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

	    <div class="row">
		    <div class="span4 offset4 well">
			    <legend>Connectez-vous pour discuter</legend>
			    <?php if($error): ?>
			    <div class="alert alert-error">
			    	<a class="close" data-dismiss="alert" href="#">×</a>
			    	Nom d'utilisateur ou mot de passe incorrect&nbsp;!<br />
			    	<small>
			    		Auriez-vous changé votre com récemment ? Attention, ce nom est impacté par un <span style="font-family: monospace;">/nick</span> !
			    	</small>
			    </div>
				<?php endif; ?>
			    <form method="POST" action="" accept-charset="UTF-8">
				    <input type="text" id="username" class="span4" name="username" placeholder="Nom d'utilisateur" />
				    <input type="password" id="password" class="span4" name="password" placeholder="Mot de passe" />
				    <button type="submit" name="submit" class="btn btn-primary btn-block">Connexion</button>
				    <br />
				    <a href="?do=new" class="btn btn-block">Nouveau ? Vous discutez dans 30 secondes !</a>
			    </form>
		    </div>
	    </div>
    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script src="misc/js/bootstrap.js"></script>

  </body>
</html>