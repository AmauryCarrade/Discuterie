<?php
	if(!defined('_NO_DIRECT_ACCESS')) exit;

  if(isset($_SESSION['loggedIn'])) {
		header('Location: index.php');
		exit;
	}

	$error  = false; $username = NULL;
	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password2'])) {
		$pdo = SQLiteConnect();
    $username = $_POST['username'];

    if(empty($_POST['username']) || empty($_POST['password']) || $_POST['password'] != $_POST['password2']){
      $error = true;
    }
    else {
      // 1: is the username valid?
      $query  = $pdo->prepare('SELECT * FROM users WHERE name = ?');
      $query->execute(array($_POST['username']));
      if($query->fetchAll() != array()) {
        $error = 'username';
      }

      $query  = $pdo->prepare('INSERT INTO users(name, previousName, password, loggedIn) VALUES(?, \'\', ?, 1)');
      $res    = $query->execute(array($_POST['username'], sha1($_POST['password'] . SALT)));
      if(!$query || !$res) {
        $error = true;
      }
      else {
        header('Location: index.php'); 
        exit;
      }
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
          <a class="brand" href="./">Discuterie</a>
          <div class="nav-collapse collapse"></div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

	    <div class="row">
		    <div class="span4 offset4 well">
			    <legend>Inscription</legend>
			    <?php if($error !== false): ?>
			    <div class="alert alert-error">
			    	<a class="close" data-dismiss="alert" href="#">×</a>
			    	Une erreur s'est produite. <br />
            <small>
              <?php 
                switch($error) {
                  case 'username': 
                    echo 'Ce nom d\'utilisateur est déjà pris.';
                    break;
                  default:
                    echo 'Auriez-vous fait une erreur de recopie du mot de passe ? Si non, réessayez. Il se peut que le problème vienne de chez nous.';
                    break;
                }
              ?>
			    </div>
				<?php endif; ?>
			    <form method="POST" action="" accept-charset="UTF-8">
				    <input type="text" id="username" class="span4" name="username" value="<?php echo $username; ?>" placeholder="Nom d'utilisateur (espaces interdites)" />
				    <input type="password" id="password" class="span4" name="password" placeholder="Mot de passe (minimum 6 caractères)" />
            <input type="password" id="password" class="span4" name="password2" placeholder="Retapez-le pour éviter toute faute de frappe" />
				    <button type="submit" name="submit" class="btn btn-primary btn-block">Inscription &amp; connexion</button>
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