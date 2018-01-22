<?php
require("./config/database.php");
  session_start();
  date_default_timezone_set('Europe/Paris');
  if (isset($_SESSION['log']) && $_SESSION['hello'] == 0)
  {
    echo "<div class='infor'>Bonjour " . $_SESSION['user_name'] . "</div>";
    $_SESSION['hello']++;
  }

  try {
	  $bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
  } catch (Exception $e) {
	  die('Erreur : ' . $e->getMessage());
  }

if (isset($_POST['del_butt']) && $_POST['del_butt'] == 'del')
{
	$name_img = $_POST['image_name_del'];

	$sql = "DELETE FROM pictures WHERE name = :name_img";
	$req = $bdd->prepare($sql);
	$req->bindParam(':name_img', $name_img);
	$req->execute();

	$sql = "DELETE FROM likes WHERE image = :name_img";
	$req = $bdd->prepare($sql);
	$req->bindParam(':name_img', $name_img);
	$req->execute();

	$sql = "DELETE FROM comments WHERE image = :name_img";
	$req = $bdd->prepare($sql);
	$req->bindParam(':name_img', $name_img);
	$req->execute();
}

if (isset($_POST['del_buton']) && $_POST['del_buton'] == 'del')
{
	$name_com = $_POST['comment_name_del'];

	$sql = "DELETE FROM comments WHERE id = :name_com";
	$req = $bdd->prepare($sql);
	$req->bindParam(':name_com', $name_com);
	$req->execute();
}

/*if (isset($_SESSION['log']) && isset($_POST['dislike_buton']) && $_POST['dislike_buton'] == 'dislike')
{
	$sql = "DELETE FROM likes WHERE image = :name_image AND liker = :pers";
	$req = $bdd->prepare($sql);
	$req->bindParam(':name_image', $_POST['image_name']);
	$req->bindParam(':pers', $_SESSION['user_name']);
	$req->execute();
}*/

/////////////////////////// SYSTEM DE PAGINATION
$nombre_de_pic_par_page = 5;

$repo = $bdd->prepare('SELECT COUNT(*) AS contenu FROM pictures');
$repo->execute();
$total_pic = $repo->fetch();
$nombre_pic = $total_pic['contenu'];

$nb_pages = ceil($nombre_pic / $nombre_de_pic_par_page);

if (isset($_GET['page']))
{
    $page = $_GET['page']; // On récupère le numéro de la page indiqué dans l'adresse (livredor.php?page=4)
}
else // La variable n'existe pas, c'est la première fois qu'on charge la page
{
    $page = 1; // On se met sur la page 1 (par défaut)
}


$premierPicAafficher = ($page - 1) * $nombre_de_pic_par_page;


////////////////////////////////////////////////////////

  	$sql = "SELECT autor, name FROM pictures ORDER BY id DESC LIMIT :first, :seconde";
  	$req = $bdd->prepare($sql);
  	$req->bindValue(':first', intval($premierPicAafficher), PDO::PARAM_INT);
	$req->bindValue(':seconde', intval($nombre_de_pic_par_page), PDO::PARAM_INT);
	$req->execute();
  	$donnees = $req->fetchALL(/*PDO::FETCH_COLUMN, 'name'*/);
  	$sql = null;


  $sql = "SELECT COUNT(*) FROM pictures";
  $req = $bdd->prepare($sql);
  $req->execute();
  $nbr_pic = $req->fetchColumn();

if ($nbr_pic == 0 && isset($_SESSION['log']))
{
	echo "<div class='infor'>
			Desolé il n'y as pas encore de photos, soyez le premier à en prendre une ! Onglet: Picture
			</div>";
}
if ($nbr_pic == 0 && !isset($_SESSION['log']))
{
	echo "<div class='infor'>
			Desolé il n'y as pas encore de photos, soyez le premier à en prendre une ! Inscrivez vous ou connectez vous !
			</div>";
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="index.css" />
    <title>Home</title>
  </head>
  <body>
    <?php include('menu.php');?>
	<?php if($nbr_pic != 0){ ?>
	<div class='main'>
	<?php foreach($donnees as $valeur){
		$sql = "SELECT id, comment, autor, datecom FROM comments WHERE image=:image ORDER BY id DESC";
	    $req = $bdd->prepare($sql);
		$req->bindParam('image', $valeur['name']);
	    $req->execute();
	    $comments = $req->fetchAll();

		$req = $bdd->prepare('SELECT COUNT(*) AS nbr_like FROM likes WHERE image = :name_image');
		$req->bindParam('name_image', $valeur['name']);
		$req->execute();
		$nombre_likes = $req->fetch();


		if (isset($_SESSION['log']))
		{
			$req = $bdd->prepare('SELECT COUNT(*) AS exist FROM likes WHERE liker = :autor AND image = :name_image');
			$req->bindParam(':autor', $_SESSION['user_name']);
			$req->bindParam('name_image', $valeur['name']);
			$req->execute();
			$exist = $req->fetch();
		}
		//$bdd = null;
		//$sql = null;
		echo "<div class='block'>";
		if (isset($_SESSION['log']) && $valeur['autor'] == $_SESSION['user_name']){
			echo"<div class='formu_del'>
					<form action='index.php' method='post'>
						<input type='hidden' name='image_name_del' value=" . $valeur['name'] . ">
						<button type='submit' name='del_butt' value='del' style='border: 0; background: transparent'>
    						<img src='./images/delete.png' width='25' height='20' alt='submit' />
						</button>
					</form>
				</div>";
			}
			echo "<div><h2>" . $valeur['autor'] . "</h2></div>";
			echo"<div class='bite'> <div class='img'>
					<img src=" . $valeur['name'] . ">
				</div>
				<div class='comment'>";
				foreach($comments as $comment){
					echo "<h3 class='inline'>" . $comment['autor'] . "</h3>";
					echo "<h5 class='inline'	>" . $comment['datecom'] . "</h5>";
					//echo "<br>";
					echo "<p>" . $comment['comment'] . "</p>";
					if (isset($_SESSION['log']) && $comment['autor'] == $_SESSION['user_name']){
						echo"
								<form id='del_form_com'action='index.php' method='post'>
									<input type='hidden' name='comment_name_del' value=" . $comment['id'] . ">
									<button type='submit' name='del_buton' value='del' style='border: 0; background: transparent'>
			    						<img src='./images/14-512.png' width='25' height='20' alt='submit' />
									</button>
								</form>
							";
						}
					//echo "<br>";
				}


				echo "</div>
				</div>";

				echo $nombre_likes['nbr_like'] . " j'aime(s)";

				if (isset($_SESSION['log']))
				{
					if ($exist['exist'] == 0)
					{
						echo "<div class='formu_like'>
								<form action='add_like.php' method='post'>
									<input type='hidden' name='image_name' value=" . $valeur['name'] . ">";
									if (isset($_GET['page']))
									{
										echo "<input type='hidden' name='page' value=" . $_GET['page'] . ">";
									}
									echo "<button type='submit' name='like_buton' value='like' style='border: 0; background: transparent'>
										<img src='./images/like_it.png' width='42' height='34' alt='submit' />
									</button>
				  				</form>
							</div>";
					}
					if ($exist['exist'] != 0) {
						echo "<div class='formu_like'>
								<form action='add_dislike.php' method='post'>
									<input type='hidden' name='image_name' value=" . $valeur['name'] . ">";
									if (isset($_GET['page']))
									{
										echo "<input type='hidden' name='page' value=" . $_GET['page'] . ">";
									}
									echo "<button type='submit' name='dislike_buton' value='dislike' style='border: 0; background: transparent'>
										<img src='./images/dislike.png' width='32' height='30' alt='submit' />
									</button>
				  				</form>
							</div>";
					}
				echo "<div class='formu_like'>
						<form action='add_com.php' method='post'>
							<input id='comment_zone' type='textarea' name='comment'><br>";
							if (isset($_GET['page']))
							{
								echo "<input type='hidden' name='page' value=" . $_GET['page'] . ">";
							}
							echo "<input type='hidden' name='image_name' value=" . $valeur['name'] . ">
			    			<input type='submit' name='submit_com' value='Add'>
			  			</form>
					</div>";
			}
			echo "</div>";
	} ?>
	<?php echo "<div class='pagination'>Page : ";
for ($i = 1 ; $i <= $nb_pages ; $i++)
{
    echo '<a href="index.php?page=' . $i . '">' . $i . '</a> ';
}
echo "</div>"
?>
</div>
<?php } ?>
  </body>
</html>
