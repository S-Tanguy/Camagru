<?php
require("./config/database.php");
  session_start();

  if ($_SESSION['log'] != 1)
  {
    header('Location: ./index.php');
    exit();
  }

try{
$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
$sql = "SELECT name FROM pictures WHERE autor=:name_user ORDER BY id DESC LIMIT 4 ";
$req = $bdd->prepare($sql);
$req->execute(array('name_user' => $_SESSION['user_name']));
$name_photo = $req->fetchAll();
  }
 catch (Exception $e) {
	die('Erreur : ' . $e->getMessage());
}

// if (isset($_POST['submit']) && isset($_FILES['file']) && $_POST['submit'] == 'upload')
// 	print_r($_FILES['file']);
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="toto.css"/>
    <title>Take your photo</title>
  </head>
  <body>
    <h1>CAMAGRU</h1>
    <?php include('menu.php'); ?>

    <div class="main">
        <video id="video"></video>
        <button id="startbutton">Prendre une photo</button>
		<img id="photo" />

      	<canvas id="canvas" style="display:none;"></canvas>
    </div>


	<div class="upload">
		<!-- <form enctype="multipart/form-data" action="picture.php" method="post"> -->
			<input type="file" name="file" class="imageLoader">
			<!-- <input type="submit" name="submit" value="upload"> -->
		<!-- </form> -->
	</div>



	<div id="image_filtre">
		<img src='./images/chapeau_rouge.png'/>
	</div>
<div id="filtres">
	<form id="test">
	<label><input type="radio" name="test" value="moustache.png" class='images_filtre'><img class='sel-pic'src="images/moustache.png"  /></label>
	<label><input type="radio" name="test" value="chapeau_rouge.png" class='images_filtre' checked><img class='sel-pic' src="images/chapeau_rouge.png"  /></label>
	<label><input type="radio" name="test" value="bonnet_noel.png" class='images_filtre'><img class='sel-pic' src="images/bonnet_noel.png"  /></label>
	<label><input type="radio" name="test" value="geek.png" class='images_filtre'><img class='sel-pic' src="images/geek.png"  /></label>
	</form>
</div>

<div class="last_pic">
	<?php foreach($name_photo as $valeur){
	   echo "<img class='pic' src=" . $valeur['name'] . "/>";
	} ?>
</div>

<footer>
</footer>

    <script type="text/javascript">
    (function() {

      var streaming = false,
          video        = document.querySelector('#video'),
          cover        = document.querySelector('#cover'),
          canvas       = document.querySelector('#canvas'),
          photo        = document.querySelector('#photo'),
          startbutton  = document.querySelector('#startbutton'),
          width = 475,
          height = 0,
		  imageLoader = document.getElementsByClassName('imageLoader')[0],
		  main = document.getElementsByClassName('main')[0];

      navigator.getMedia = ( navigator.getUserMedia ||
                             navigator.webkitGetUserMedia ||
                             navigator.mozGetUserMedia ||
                             navigator.msGetUserMedia);

      navigator.getMedia(
        {
          video: true,
          audio: false
        },
        function(stream) {
          if (navigator.mozGetUserMedia) {
            video.mozSrcObject = stream;
          } else {
            var vendorURL = window.URL || window.webkitURL;
            video.src = vendorURL.createObjectURL(stream);
          }
          video.play();
        },
        function(err) {
          //console.log("An error occured! " + err);
        }
      );

      video.addEventListener('canplay', function(ev){
        if (!streaming) {
          height = video.videoHeight / (video.videoWidth/width);
          video.setAttribute('width', width);
          video.setAttribute('height', height);
          canvas.setAttribute('width', width);
          canvas.setAttribute('height', height);
          streaming = true;
        }
      }, false);
//////////////////////////////////////
	  //let body = document.getElementsByTagName('body');
	  const images_filtre = document.getElementsByClassName('images_filtre');
	  //console.log(images_filtre);

	  imageLoader.addEventListener('change', handleImage, false);
	  function handleImage(e)
	  {
	    let reader = new FileReader();
	    reader.onload = (evt) => {
	      let img = new Image();
	      img.onload = () => {
			let tmp_canvas = document.createElement('canvas');
			let tmp_context = tmp_canvas.getContext('2d');

			video.remove();
			tmp_canvas.setAttribute('id', 'video');
			tmp_canvas.setAttribute('type', '1');
			tmp_canvas.setAttribute('width', 475);
			tmp_canvas.setAttribute('height', 356.25);
			main.prepend(tmp_canvas);
			video = tmp_canvas;
			tmp_context.drawImage(img, 0, 0, 475, 356.25);
	      }
	      img.src = evt.target.result;
	    }
	    reader.readAsDataURL(e.target.files[0]);
	  }


	  [].forEach.call(images_filtre, function (element){
		  element.addEventListener('click', (e) => {


			  //console.log(e.target.value);
			  var img = e.target.value;
			  //var toto = this;
			  //console.log(toto);
			  document.getElementById("image_filtre").innerHTML = "<img src='images/" + img + "'/>";
			 //let div = document.createElement('div');

			// document..appendChild();
		  });
	  });
///////////////////////////////////////////////////////////////////////////////////////////////////////
var isDown;
let div = document.getElementById('image_filtre');
div.addEventListener('mousedown', function(e) {
	isDown = true;
	offset = [
		div.offsetLeft - e.clientX,
		div.offsetTop - e.clientY
	];
}, true);

document.addEventListener('mouseup', function() {
	isDown = false;
}, true);

document.addEventListener('mousemove', function(event) {
	event.preventDefault();
	if (isDown) {
		mousePosition = {

			x : event.clientX,
			y : event.clientY

		};
		div.style.left = (mousePosition.x + offset[0]) + 'px';
		div.style.top  = (mousePosition.y + offset[1]) + 'px';
	}
}, true);
///////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////
      function takepicture() {
		var filtre = document.getElementById("test");
		var toto = filtre.elements["test"].value;
		//var video = document.getElementById("video");
		var tmp_canvas = document.createElement('canvas');

        tmp_canvas.width = width;
        tmp_canvas.height = height;
		if (video.getAttribute('type') != null)
			tmp_canvas = video;
		else
        	tmp_canvas.getContext('2d').drawImage(video, 0, 0, width, height);
        //var data = canvas.toDataURL('image/png');
		//Data image base64
		var image = document.createElement('img');
		image = tmp_canvas.toDataURL();


		//console.log(image);
		/*
		** AJAX
		*/
		// Envoie data sur une page php
		var xhr = new XMLHttpRequest();
		// Ouverture de la page Php
		xhr.open('POST', 'http://localhost:4242/get_image.php');
		// Propre a la methode Post
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		// Envoie de donnees en Post a la page ouverte
		xhr.send('image=' + image + '&filtre=' + toto + '&x=' + div.style.left + '&y=' + div.style.top + '&distance_gauche=' + video.offsetLeft + '&distance_top=' + video.offsetTop);
		// Une fois la page appelee a fini de charger, je rentre dans la fonction () {}
		xhr.onload = function () {
			// Sur la page PHP il y a un texte a recuperer (le path)
			var path = xhr.responseText;
			// path contiendra le path
			console.log(path);
			// Affichage sur le site
			photo.src = path;
		};
      }

      startbutton.addEventListener('click', function(ev){
          takepicture();
        ev.preventDefault();
      }, false);

      })();
    </script>

  </body>
</html>
