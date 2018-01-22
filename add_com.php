<?php
require("./config/database.php");
session_start();
date_default_timezone_set('Europe/Paris');

try {
	$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
} catch (Exception $e) {
	die('Erreur : ' . $e->getMessage());
}

if (isset($_SESSION['log']) && isset($_POST['submit_com'])){
	if (isset($_POST['comment']) && isset($_POST['image_name']) && $_POST['comment'] != "")
	{
		$sql = "INSERT INTO comments (autor, image, datecom, comment) VALUES(:autor, :image_name, :datecom, :comment)";
		$req = $bdd->prepare($sql);
		$req->execute(array('autor' => $_SESSION['user_name'],
					'image_name' => $_POST['image_name'],
					'datecom' => $date = date('Y-m-d H:i:s'),
					'comment' => htmlspecialchars($_POST['comment'])));

		$sql = "SELECT autor FROM pictures WHERE name=:name_img";
		$req = $bdd->prepare($sql);
		$req->execute(array('name_img' => $_POST['image_name']));
		$name_desti = $req->fetch();

		$sql = "SELECT email FROM users WHERE nom=:name_user";
		$req = $bdd->prepare($sql);
		$req->execute(array('name_user' => $name_desti['autor']));
		$email_desti = $req->fetch();

		$destinataire = $email_desti['email'];
		$sujet = "INFO NEWS CAMAGRU" ;
		$entete = "From: newsinfo@camagru.com" ;

		$message = "                Bonjour, " . $name_desti['autor'] . ",

					Nous vous informons que " . $_SESSION['user_name'] . " vient de commenter une de vos photo CAMAGRU.

					---------------
					Ceci est un mail automatique, Merci de ne pas y repondre.";

		mail($destinataire, $sujet, $message, $entete);
	}
}

if (isset($_POST['page']))
{
	header("Location: index.php?page=" . $_POST['page']);
}
else {
	header("Location: index.php");
}
?>
