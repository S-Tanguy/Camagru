<?php
require("./database.php");
  try {
      $bdd = new PDO('mysql:host=localhost', $DB_USER, $DB_PASSWORD);
      $req = 'CREATE DATABASE IF NOT EXISTS db_camagru';
      $req = $bdd->prepare($req);
      $req->execute();


      echo '<p> Base de données créée correctement</p>';
      try {
          $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
          echo '<p>Et pas de problème de connexion à celle-ci</p>';
      }
      catch (PDOException $e) {
         echo 'Connexion impossible : ' . $e->getMessage();
      }
      $bdd = null;
  }
  catch (PDOException $e){
      echo 'Erreur lors de la création de la base de donées : ' . $e->getMessage();
  }

  try {
    $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);

    $req = $bdd->prepare("CREATE TABLE `users` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `nom` TEXT NOT NULL ,
        `email` TEXT NOT NULL ,
        `password` TEXT NOT NULL,
        `cle` VARCHAR(32),
        `actif` INT DEFAULT 0,
        `forget_pass` INT DEFAULT 0,
        `cle_password` VARCHAR(32))");
    $req->execute();

	$req = $bdd->prepare("CREATE TABLE `pictures` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `autor` TEXT NOT NULL ,
        `name` TEXT NOT NULL )");
    $req->execute();

	$req = $bdd->prepare("CREATE TABLE `comments` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `autor` TEXT NOT NULL ,
		`image` TEXT NOT NULL ,
		`datecom` DATETIME NOT NULL,
        `comment` TEXT NOT NULL )");
    $req->execute();

	$req = $bdd->prepare("CREATE TABLE `likes` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`image` TEXT NOT NULL ,
        `liker` TEXT NOT NULL)");
    $req->execute();
	$bdd = null;
  }
  catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}

?>
