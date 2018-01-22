<?php

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="menu.css" />
  </head>
  <body>
    <div class='ribbon'>
      <a href='index.php'><span>Home</span></a>
      <?php if (!isset($_SESSION['log'])) {?>
      <a href='login.php'><span>Connexion</span></a>
      <a href='inscription.php'><span>Inscription</span></a>
      <?php } ?>
      <?php if (isset($_SESSION['log']) && $_SESSION['log'] == 1) {?>
      <a href='picture.php'><span>Picture</span></a>
      <?php } ?>
      <?php if (isset($_SESSION['log']) && $_SESSION['log'] == 1) {?>
      <a href='Deconnexion.php'><span>Deconnexion</span></a>
      <?php } ?>
    </div>
  </body>
</html>
