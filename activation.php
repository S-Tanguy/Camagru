<?php
require("./config/database.php");
if ($_GET['log'] == "" || $_GET['cle'] == "")
  header('Location: ./index.php');
$email = $_GET['log'];
$cle = $_GET['cle'];


$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
  // Récupération de la clé correspondant au $login dans la base de données
$req = $bdd->prepare("SELECT cle,actif FROM users WHERE email=?");
$req->execute(array($email));
$result = $req->fetch();

$clebdd = $result['cle'];	// Récupération de la clé
$actif = $result['actif']; // $actif contiendra alors 0 ou 1


// On teste la valeur de la variable $actif récupéré dans la BDD
if($actif == '1') // Si le compte est déjà actif on prévient
  {
     echo "<div class='infor'>Votre compte est déjà actif !</div>";
  }
else // Si ce n'est pas le cas on passe aux comparaisons
  {
     if($cle == $clebdd) // On compare nos deux clés
       {
          // Si elles correspondent on active le compte !
          echo "<div class='infor'>Votre compte a bien été activé !</div>";

          // La requête qui va passer notre champ actif de 0 à 1
          $req = $bdd->prepare("UPDATE users SET actif = 1, cle = 0 WHERE email like :email ");
          $req->bindParam(':email', $email);
          $req->execute();
       }
     else // Si les deux clés sont différentes on provoque une erreur...
       {
          echo "<div class='infor'>Erreur ! Votre compte ne peut être activé...</div>";
       }
  }
  $bdd = NULL;
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
  </body>
</html>
