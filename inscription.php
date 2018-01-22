<?php
require("./config/database.php");
session_start();
if (isset($_SESSION['log']) && $_SESSION['log'] == 1){
  header('Location: ./index.php');
  exit();
}

try {
  $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
  if (isset($_POST['submit']))
  {
	  if (isset($_POST['email']) && !preg_match("/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ ", htmlspecialchars($_POST['email'])))
	  {
		  echo "<div class='infor'> L'adresse email doit être valide </div>";
	  }
	  else if (isset($_POST['nom']) && !preg_match("/^[a-zA-Z0-9_]{4,16}$/ ", htmlspecialchars($_POST['nom'])))
	  {
		  echo "<div class='infor'> Le nom doit etre valide, lettre(s) min, maj et chiffre(s) entre 4 et 16chars </div>";
	  }
	  else if (isset($_POST['password']) && !preg_match(" /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/ ", htmlspecialchars($_POST['password'])))
	  {
		  echo "<div class='infor'> Le mdp doit contenir, lettre(s) min, maj, chiffre(s), char spéciaux($ @ % * + - _ !), entre 8 et 15chars </div>";
	  }
	  else if ($_POST['nom'] == NULL || $_POST['email'] == NULL || $_POST['password'] == NULL || $_POST['password_c'] == NULL && $_POST['submit'] == 'OK')
	  {
		  echo "<div class='infor'> Un ou plusieurs champ(s) sont vides </div>";
	  }
	  else
	  {
	      if ($_POST['nom'] != NULL && $_POST['email'] != NULL && $_POST['password'] != NULL && $_POST['password_c'] != NULL && $_POST['submit'] == 'OK')
	      {
	          if ($_POST['password'] == $_POST['password_c'])
	          {
	            $req = $bdd->prepare("SELECT COUNT(*) AS nbr FROM users WHERE email=?");
	            $req->execute(array(htmlspecialchars($_POST['email'])));
	            $count_email = $req->fetch(PDO::FETCH_ASSOC);
				if ($count_email['nbr'] > 0)
				{
					$pb = "email";
				}

				$req = $bdd->prepare("SELECT COUNT(*) AS nbr FROM users WHERE nom=?");
	            $req->execute(array(htmlspecialchars($_POST['nom'])));
	            $count_nom = $req->fetch(PDO::FETCH_ASSOC);
				if ($count_nom['nbr'] > 0)
				{
					$pb = "nom";
				}
	                                                                      ///////////////////////////////////////////////////////
				if ($count_email['nbr'] == 0 && $count_nom['nbr'] == 0 && preg_match('`^([a-zA-Z0-9-_]{2,36})$`', htmlspecialchars($_POST['nom'])))
	            {
	              $req = $bdd->prepare('INSERT INTO users(nom, email, password) VALUES(:nom, :email, :password)');
	              $req->execute([
	                  ':nom' => htmlspecialchars($_POST['nom']),
	                  ':email' => htmlspecialchars($_POST['email']),
	                  ':password' => hash('sha512', htmlspecialchars($_POST['password'])),
	              ]);

	              $cle = md5(microtime(TRUE)*100000);

	              $req = $bdd->prepare('UPDATE users SET cle=:cle WHERE email like :email');
	              $req->bindParam(':cle', $cle);
	              $req->bindParam(':email', $_POST['email']);
	              $req->execute();

	              $bdd = NULL;

	              // Préparation du mail contenant le lien d'activation
	              $destinataire = htmlspecialchars($_POST['email']);
	              $sujet = "Activer votre compte" ;
	              $entete = "From: inscription@votresite.com" ;

	              // Le lien d'activation est composé du login(log) et de la clé(cle)
	              $message = 'Bienvenue sur Camagru,

	              Pour activer votre compte, veuillez cliquer sur le lien ci dessous
	              ou copier/coller dans votre navigateur internet.

	              http://localhost:4242/activation.php?log='.urlencode($_POST['email']).'&cle='.urlencode($cle).'


	              ---------------
	              Ceci est un mail automatique, Merci de ne pas y répondre.';


	              mail($destinataire, $sujet, $message, $entete);

				 echo "<div class='infor'>
				 		<p> Un mail de confirmation vient de vous etre envoyé </p>
				 		</div>";
	            }
	            else
				{
					if ($pb == "email")
	              		echo "<div class='infor'>Cet email est déjà utilisé.</div>";
					else if ($pb == "nom")
						echo "<div class='infor'>Ce nom est déjà utilisé.</div>";
					else
						echo "<div class='infor'>Il y\'a un problème dans les données</div>";
				}
          }
          else
            echo "<div class='infor'> Deso mais les deux mots de passes ne sont pas identique.
				</div>";
      	  }
	  }
	}
  $dbh = null;
} catch (PDOException $e) {
  print "Erreur !: " . $e->getMessage() . "<br/>";
  die();
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="login.css" />
    <title>Inscription</title>
  </head>
  <body>
    <?php include('menu.php'); ?>
	<div class="inscription">
	    <form action="inscription.php" method="post">
	        <div>
	            <label for="nom">Nom :</label>
	            <input name='nom' type="text" id="nom" pattern=^([a-zA-Z0-9-_]{4,16})$ title="Veuilez éviter les caractères spéciaux, votre pseudo faire entre 4 et 16 caractères">
	        </div>
			<br>
	        <div>
	            <label for="email">Email :</label>
	            <input name='email' type="email" id="email"/>
	        </div>
			<br>
	        <div>
	            <label for="password">Mot de passe :</label>
	            <input name='password' type="password" id="mdp"></input>
	        </div>
			<br>
	        <div>
	            <label for="password_c">Mot de passe confirmation:</label>
	            <input name='password_c' type="password" id="mdp_c"></input>
	        </div>
			<br>
	        <div class="button">
	            <button name='submit' type="submit" value='OK'>Inscription</button>
	        </div>
	    </form>
	</div>
  </body>
</html>
