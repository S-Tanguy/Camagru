<?php
require("./config/database.php");
	session_start();
	define('UPLOAD_DIR', 'photos/');
	define('UPLOAD_D', 'images/');


	if (is_dir("photos") == FALSE)
	{
		mkdir("photos", 0755);
	}

	$img = $_POST['image'];
	$filtre = $_POST['filtre'];

	if ($img == 'data:,')
	{
		exit();
	}

	$dest_xx = $_POST['x'];
	$dest_yy = $_POST['y'];
	$dist_g = $_POST['distance_gauche'];
	$dist_t = $_POST['distance_top'];


	$dest_xx = strrev($dest_xx);
	$dest_xx = substr($dest_xx, 2);
	$dest_xx = strrev($dest_xx);
	$dest_xx = intval($dest_xx);

	$dest_yy = strrev($dest_yy);
	$dest_yy = substr($dest_yy, 2);
	$dest_yy = strrev($dest_yy);
	$dest_yy = intval($dest_yy);

	$file = UPLOAD_DIR . $_SESSION['user_name'] . uniqid() . '.png';

	// On charge d'abord les images
	$source = imagecreatefrompng(UPLOAD_D . '/' . $filtre); // Le logo est la source

	$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);

	$destination = imagecreatefromstring($data); // La photo est la destination

	// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
	$largeur_source = imagesx($source);
	$hauteur_source = imagesy($source);
	$largeur_destination = imagesx($destination);
	$hauteur_destination = imagesy($destination);

	imagesavealpha($destination, true);


	// On veut placer le logo en bas à droite, on calcule les coordonnées où on doit placer le logo sur la photo
	//$destination_x = $largeur_destination - $largeur_source;
	//$destination_y =  $hauteur_destination - $hauteur_source;


	// On met le logo (source) dans l'image de destination (la photo)
	imagecopy($destination, $source, $dest_xx - $dist_g, $dest_yy - $dist_t/*- $hauteur_source - 43*/, 0, 0, $largeur_source, $hauteur_source);

	header('Content-Type: image/png');
	// On affiche l'image de destination qui a été fusionnée avec le logo
	imagepng($destination, $file);


	try {
		$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	} catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
	}

	if(!empty($destination))
	{
	$sql = "INSERT INTO `pictures` (`autor`, `name`) VALUES (:autor, :name)";
	$req = $bdd->prepare($sql);
	$req->execute(array(
		'autor' => $_SESSION['user_name'],
		'name' => $file));
	}
	echo $file;
?>
