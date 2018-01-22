<?php
require("./config/database.php");
session_start();
if (isset($_SESSION['log']) && $_SESSION['log'] == 1){
  header('Location: ./index.php');
  exit();
}


if (isset($_POST['submit']) && $_POST['email'] == "")
{
  echo 'Le champ est vide, veuillez entrer un email valide';
}
else if(isset($_POST['submit']) && isset($_POST['email']))
{
  $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);

  $req = $bdd->prepare('SELECT COUNT(id) AS nbr FROM users WHERE email = ?');
  $req->execute(array($_POST['email']));
  $result = $req->fetch(PDO::FETCH_ASSOC);

  if ($result['nbr'] > 0) // L'email existe
  {
    $req = $bdd->prepare('SELECT forget_pass FROM users WHERE email = ?');
    $req->execute(array($_POST['email']));
    $result = $req->fetch(PDO::FETCH_ASSOC);

      if ($result['forget_pass'] == 0)    // Verification si le client n'a pas déjà envoyé un email de réinitialisation de mot de passe
      {
      $cle_password = md5(microtime(TRUE)*100000);
      $actif = 1;

      $req = $bdd->prepare("UPDATE users SET cle_password = :cle_password, forget_pass = :forget_pass  WHERE email = :email ");
      $req->execute([
        ':cle_password' => $cle_password,
        ':forget_pass' => $actif,
        ':email' => $_POST['email'],
     ]);
      // Préparation du mail contenant le lien d'activation
      $destinataire = $_POST['email'];
      $sujet = "Réinitialisation de votre mot de passe" ;
      $entete = "From: inscription@votresite.com" ;

      // Le lien d'activation est composé du login(log) et de la clé(cle)
      $message = 'Bonjour apres avoir recu une demande de réinitialisation de mot de passe de votre part,

      nous vous demandons de vous rendre sur ce lien afin de continuer la validation d\'un autre mot de passe.

      http://localhost:4242/modif_password.php?log='.urlencode($_POST['email']).'&cle='.urlencode($cle_password).'


      ---------------
      Ceci est un mail automatique, Merci de ne pas y répondre.';


      mail($destinataire, $sujet, $message, $entete) ;
      echo '<div class="infor">Un email vient de vous etre envoyer à l\'adresse ' . $_POST['email'] . '</div>';
    }
    else { // Le client à déja envoyer une requete afin de réinitialiser son mot de passe
      echo '<div class="infor">Vous avez déjà fait une requete de réinitialisation, regardez votre boite mail</div>';
    }
  }
  else {  // l'email n'existe pas
      echo '<div class="infor">Cet email n\'existe pas</div>';
  }
  $bdd = NULL;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="forget.css"/>
    <title>Reset password</title>
  </head>
  <body>
    <?php include('menu.php'); ?>
    <h1>Reset password</h1>
	<div class="log">
	    <form action="forget_password.php" method="post">
	        <div class="email_field">
	            <label for="login_e">Email :</label>
	            <input name='email' type="email" id="email" placeholder="L'email de votre compte">
	        </div>
	        <div class="button">
	            <button name='submit' type="submit" value='OK'>Send</button>
	        </div>
	    </form>
	</div>
  </body>
</html>
