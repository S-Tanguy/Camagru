<?php
require("./config/database.php");
$email = null;
$cle_password = null;

if (isset($_GET) && isset($_GET['log']) && isset($_GET['cle']))
{
  $email = htmlspecialchars($_GET['log']);
  $cle_password = htmlspecialchars($_GET['cle']);
}

if (isset($_POST) && isset($_POST['email']) && isset($_POST['cle_password']))
{
  $email = htmlspecialchars($_POST['email']);
  $cle_password = htmlspecialchars($_POST['cle_password']);
  $new_password = hash('sha512', htmlspecialchars($_POST['password_modif_one']));
}

$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
  // Récupération de la clé correspondant au $login dans la base de données
$req = $bdd->prepare("SELECT cle_password, forget_pass FROM users WHERE email=?");
$req->execute(array($email));
$result = $req->fetch();

$clebdd_password = $result['cle_password'];	// Récupération de la clé
$actif = $result['forget_pass']; // $forget_pass contiendra alors 0 ou 1, 0 = il n'y a pas de demande de réinitialisation, 1 = le client a fait la demande

$isgood = 0;
// On teste la valeur de la variable $forget_pass récupéré dans la BDD
if($actif == '0') // Si le compte n'a pas fait l'objet d'une demande de reinitialisation on prévient
  {
     echo "<div class='infor'> Votre compte n'a pas fait l'objet d'une demande de réinitialisation de mot de passe !</div>";
     $isgood = 0;
  }
else // Si ce n'est pas le cas on passe aux comparaisons
  {
     if($cle_password == $clebdd_password) // On compare nos deux clés
       {
          // Si elles correspondent on peut modifier le password !
          $isgood = 1;

          if (isset($_POST['submit']))
          {
            if (($_POST['password_modif_one'] == "" && $_POST['password_modif_two'] == "") || ( $_POST['password_modif_one'] == "" || $_POST['password_modif_two'] == "" ))
                echo "<div class='infor'>Un des deux champs est vide</div>";
            if ($_POST['password_modif_one'] != $_POST['password_modif_two'])
                echo "<div class='infor'>Desolé mais les deux champs ne sont pas identiques</div>";
            if ($_POST['password_modif_one'] == $_POST['password_modif_two'])
            {// La requête qui va passer notre champ forget_pass de 0 à 1
				if (!preg_match(" /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/ ", $_POST['password_modif_two']) || !preg_match(" /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/ ", $_POST['password_modif_one']))
				{
					echo "<div class='infor'> Le mot de passe n'est pas au bon format. MDP: lettre(s) min, maj, chiffre(s), char spéciaux($ @ % * + - _ !), entre 8 et 15chars,  !</div>";
				}
                else if(strlen($_POST['password_modif_one']) >= 8 && strlen($_POST['password_modif_two']) >= 8)
                {
                  $req = $bdd->prepare('UPDATE users SET forget_pass = :forget_pass, cle_password = :cle_password, password = :password WHERE email = :email');
                  $req->execute([
                    ':forget_pass' => 0,
                    'cle_password' => NULL,
                    'password' => $new_password,
                    ':email' => $email,
                  ]);
                  $isgood = 0;
				  echo"<div class='infor'>It's good</div>";
                }
                else {
                  echo"<div class='infor'>La taille de votre mot de passe est trop courte</div>";
                }
              }
            }
       }
  }
  $bdd = NULL;
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="login.css" />
    <title>Modification du mot de passe</title>
  </head>
  <body>
    <?php include('menu.php'); ?>
    <h1 id="title_mod">Modification du mot de passe</h1>
    <?php
    if ($isgood == 1 && $actif == 1) { ?>
	<div class="modif">
    <form action="modif_password.php" method="post">
        <div>
            <label for="password_modif_one">Mot de passe :</label>
            <input pattern=".{6,}" name='password_modif_one' type="password" required title="6 characters minimum">
        </div>
		<br>
        <div>
            <label for="password_modif_two">Mot de passe confirmation :</label>
            <input pattern=".{6,}" name='password_modif_two' type="password" required title="6 characters minimum">
        </div>
        <input type="hidden" name="email" value="<?php echo $_GET['log'] ?>">
        <input type="hidden" name="cle_password" value="<?php echo $_GET['cle'] ?>">
		<br>
        <div class="button">
            <button name='submit' type="submit" value='OK'>Modify</button>
        </div>
    </form>
	</div>
    <?php }
    else {?>
        <h3>                        </h3>
      <?php } ?>
  </body>
</html>
