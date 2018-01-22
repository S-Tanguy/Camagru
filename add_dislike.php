<?php
require("./config/database.php");
session_start();

try {
	$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
} catch (Exception $e) {
	die('Erreur : ' . $e->getMessage());
}

if (isset($_SESSION['log']) && isset($_POST['dislike_buton']) && $_POST['dislike_buton'] == 'dislike')
{
	$sql = "DELETE FROM likes WHERE image = :name_image AND liker = :pers";
	$req = $bdd->prepare($sql);
	$req->bindParam(':name_image', $_POST['image_name']);
	$req->bindParam(':pers', $_SESSION['user_name']);
	$req->execute();
}

if (isset($_POST['page']))
{
	header("Location: index.php?page=" . $_POST['page']);
}
else {
	header("Location: index.php");
}
?>
