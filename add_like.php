<?php
require("./config/database.php");
session_start();

try {
	$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
} catch (Exception $e) {
	die('Erreur : ' . $e->getMessage());
}

if (isset($_POST['like_buton']) && $_POST['like_buton'] == 'like' && isset($_SESSION['log']))
{
		$sql = "INSERT INTO likes (image, liker) VALUES(:name_image, :liker)";
		$req = $bdd->prepare($sql);
		$req->execute(array(':name_image' => $_POST['image_name'],
					'liker' => $_SESSION['user_name']));
}
if (isset($_POST['page']))
{
	header("Location: index.php?page=" . $_POST['page']);
}
else {
	header("Location: index.php");
}
?>
