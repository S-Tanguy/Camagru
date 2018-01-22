<?php
require("./config/database.php");
session_start();
if (isset($_SESSION['log']) && $_SESSION['log'] == 1){
  header('Location: ./index.php');
  exit();
}

if (isset($_POST['password']) && isset($_POST['user_name'])
	&& (!preg_match(" /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/ ", $_POST['password'])
	|| !preg_match("/^[a-zA-Z0-9_]{4,16}$/ ", $_POST['user_name'])))
{
	echo "<div class='infor'> Le mot de passe ou/et le login n'est/ne sont pas au bon format mdp: lettre(s) min, maj, chiffre(s), char spéciaux($ @ % * + - _ !), entre 8 et 15chars,  !</div>";
}
else
{
	if (isset($_POST['password']) && isset($_POST['user_name'])){
	$password = hash('sha512', $_POST['password']);
	$user_name = $_POST['user_name'];
	}
	else {
	  $password = NULL;
	  $user_name = NULL;
	}

	$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$req = $bdd->prepare('SELECT id,actif,email FROM users WHERE nom = :nom AND password = :password');
	$req->execute(array(
	    'nom' => htmlspecialchars($user_name),
	    'password' => htmlspecialchars($password)));
	$result = $req->fetch();
	if (isset($_POST['submit']))
	{
	  if (!$result)
	  {
	      echo "<div class='infor'>Mauvais identifiant ou mot de passe !</div>";
	  }
	  else
	  {
	    if(!isset($_SESSION['log']))
	    {
	      if ($result['actif'] == 1)
	      {
	        //session_start();
	        $_SESSION['id'] = $result['id'];
	        $_SESSION['user_name'] = $user_name;
			$_SESSION['email'] = $result['email'];
	        $_SESSION['log'] = 1;
	        $_SESSION['hello'] = 0;
	        header('Location: ./index.php');
	        //echo 'Vous êtes connecté avec l\'adresse '. $_SESSION['email'] . ' !';
	      }
	      else {
	        echo "<div class='infor'>Votre compte n'est pas activé</div>";
	      }
	    }
	    else {
	      echo "<div class='infor'>Vous êtes déjà connecté avec l\'adresse " . $_SESSION['email'] . " !</div>";
	    }
	  }
	}
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="login.css" />
    <title>Login</title>
  </head>
  <body>
    <?php include('menu.php'); ?>
	<div class="log">
	    <form action="login.php" method="post">
	        <div>
	            <label for="user_name">Login :</label>
	            <input name='user_name' type="text" id="user_name">
	        </div>
			<br>
	        <div>
	            <label for="password">Mot de passe :</label>
	            <input name='password' type="password" id="password"></input>
	        </div>
			<br>
	        <div class="button">
	            <button name='submit' type="submit" value='OK'>Login</button>
	        </div>
	    </form>
		<br>
		<a href="./forget_password.php">Mot de passe oublié ?</a>
	</div>
  </body>
</html>
