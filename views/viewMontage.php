<?php
	session_start();
	
	if(empty($_SESSION["loggued_on"]))
	{
		header('Location: Accueil');
	}

	$t = "Camagru montage";
	ob_start();

	if(!empty($_POST["imgBase64"]))
	{
		self::save_image($_POST["imgBase64"], $_SESSION["loggued_on"], 0);
	}


	if(!empty($_FILES["image"]))
	{
		unset($_SESSION["upload_file"]);
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$_SESSION["img_name"] = $_FILES["image"]["name"];
			$_SESSION["img_size"] = $_FILES["image"]["size"];
			$_SESSION["img_tmp_name"] = $_FILES["image"]["tmp_name"];
			if(self::upload_image($_SESSION["loggued_on"]) == -1)
			{
				unset($_FILES["image"]);
				unset($_SESSION["img_name"]);
				unset($_SESSION["img_size"]);
				unset($_SESSION["img_tmp_name"]);
			}

		
		}		
	}

	if(!empty($_SESSION["upload_file"]))
	{
		unset($_FILES["image"]);
		unset($_SESSION["img_name"]);
		unset($_SESSION["img_size"]);
		unset($_SESSION["img_tmp_name"]);
	}

	if(!empty($_SESSION["err_format"]))
	{
		unset($_SESSION["err_format"]);
		echo "<div class='alert alert-danger' role='alert'>
		Attention on accepte uniquement les images sous format jpg jpeg et png !</div>";
	}

?>
<script>
var user = '<?php echo $_SESSION["loggued_on"]; ?>';
function print_new_picture()
	{
		$.ajax({
			type: "GET",
			url: "Montage",
			data: 'fonction=g3li&user=' + user,
			dataType: "json",
			success:function(images){
			var data = images;
			function length(obj)
			{
				return Object.keys(obj).length;
			}
			var len = length(data);
			var pic0 = document.getElementById("gallery0");
			var pic1 = document.getElementById("gallery1");
			var pic2 = document.getElementById("gallery2");
			var button0 = document.getElementById("button0");
			var button02 = document.getElementById("button02");
			var button1 = document.getElementById("button1");
			var button12 = document.getElementById("button12");
			var button2 = document.getElementById("button2");
			var button22 = document.getElementById("button22");

				button0.style.visibility='hidden';
				button02.style.visibility='hidden';
				button1.style.visibility='hidden';
				button12.style.visibility='hidden';
				button2.style.visibility='hidden';
				button22.style.visibility='hidden';
				pic0.style.visibility = 'hidden';
				pic1.style.visibility = 'hidden';
				pic2.style.visibility = 'hidden';
			if (len == 1)
			{
				var pic0 = document.getElementById("gallery0");
				pic0.style.visibility = 'visible';
				pic0.src = data[0];
				button0.style.visibility='visible';
				button02.style.visibility='visible';

			}
			if (len == 2)
			{
				var pic0 = document.getElementById("gallery0");
				pic0.style.visibility = 'visible';
				pic0.src = data[0];

				var pic1 = document.getElementById("gallery1");
				pic1.style.visibility = 'visible';
				pic1.src = data[1];

				button0.style.visibility='visible';
				button02.style.visibility='visible';
				button1.style.visibility='visible';
				button12.style.visibility='visible';
			}
			if (len == 3)
			{
				var pic0 = document.getElementById("gallery0");
				pic0.style.visibility = 'visible';
				pic0.src = data[0];
				
				var pic1 = document.getElementById("gallery1");
				pic1.style.visibility = 'visible';
				pic1.src = data[1];

				var pic2 = document.getElementById("gallery2");
				pic2.style.visibility = 'visible';
				pic2.src = data[2];

				button0.style.visibility='visible';
				button02.style.visibility='visible';
				button1.style.visibility='visible';
				button12.style.visibility='visible';
				button2.style.visibility='visible';
				button22.style.visibility='visible';
				
			}

			},
			error: function(xhr, status, error) {
			console.log('erreur ' + status + ' message: ' + error);
			}
		});
	
	}

	function send_picture(data)	
	{
		$.ajax({
			type: "POST",
			url: "Montage",
			data: { 
				imgBase64: data
			},
			success:function()
			{
				fusion();	
			}
			});
	}

	function fusion()
	{
		value = getCookie('value');
		$.ajax({
			type: "GET",
			url: "Montage",
			data: 'fonction=merge&user=' + user +'&filtre=' + value,
			success:function()
			{
				print_new_picture();
			},
			error: function(xhr, status, error)
			{
				console.log('erreur' + status + 'message:' + error);
			}
		});
	}

	function ask_delete_db(id)
	{
		if(confirm("Vous etes sure de vouloir supprimer la photo ?"))
		{
			$.ajax({
				type: "GET",
				url: "Montage",
				data: 'fonction=delete&photo=' + id + '&user=' + user,
				success:function()
				{
					print_new_picture();
				},
				error: function(xhr, status, error)
				{
					console.log('erreur' + status + 'message:' + error);
				}
			});
		}
		else
	{}
	}

	function ask_savephoto_db(id)
	{
		$.ajax({
		type: "GET",
		url: "Montage",
		data: 'fonction=save&photo=' + id + '&user=' + user,
		success:function()
		{
			alert('Votre photo a bien été enregistreée !')
		},
		error: function(xhr, status, error)
			{
				console.log('erreur' + status + 'message:' + error);
			;
			} 
		});
	}
	function delete_photo(id)
	{
		if(id.slice(-1) == '0')
		{
			ask_delete_db(0);
		}
		else if (id.slice(-1) == '1')
		{
			ask_delete_db(1);
		}
		else if(id.slice(-1) == '2')
		{
			ask_delete_db(2);
		}
		print_new_picture();
	}

	function save_photo(id)
	{
		if(id.slice(-1) == '0')
		{
			ask_savephoto_db(0);
		}
		else if (id.slice(-1) == '1')
		{
			ask_savephoto_db(1);
		}
		else if(id.slice(-1) == '2')
		{
			ask_savephoto_db(2);
		}
	}

	print_new_picture();	
(function() {

	if (window.matchMedia("(max-width: 450px)").matches)
		  {
			var htmlElement = document.getElementsByTagName("html")[0];
			htmlElement.style.backgroundColor = "#eaeaea";
		  }
	//Avoid to re-submit upload photos
	
	
	// The width and height of the captured photo. We will set the
	// width to the value defined here, but the height will be
	// calculated based on the aspect ratio of the input stream.
  
	var width = 720;
	var height = 720;
  
	// |streaming| indicates whether or not we're currently streaming
	// video from the camera. Obviously, we start at false.
  
	var streaming = false;
  
	// The various HTML elements we need to configure or control. These
	// will be set by the startup() function.
  
	var video = null;
	var canvas = null;
	var photo = null;
	var startbutton = null;

  
	function startup() {
	
	
	var e = document.getElementById('select_filtre');
	value = e.options[e.selectedIndex].value
	
	
		
	  video = document.getElementById('video');
	  canvas = document.getElementById('canvas');
	  photo = document.getElementById('photo');
	  startbutton = document.getElementById('startbutton');
	  upload = document.getElementById('open_img_upload');
	  startbutton.disabled = true;
	  upload.disabled = true;
  
	  navigator.mediaDevices.getUserMedia({video: true, audio: false})
	  .then(function(stream) {
		video.srcObject = stream;
		video.play();
	  })
	  .catch(function(err) {
		console.log("An error occurred: " + err);
	  });

	  video.addEventListener('canplay', function(ev){
		if (!streaming) {
		  height = video.videoHeight / (video.videoWidth/width);
	
		  // Firefox currently has a bug where the height can't be read from
		  // the video, so we will make assumptions if this happens.
			  
		  if (isNaN(height)) {
			height = width / (4/3);
		  }

		  if (window.matchMedia("(max-width: 450px)").matches)
		  {
		
		  }
		
		  video.setAttribute('width', width);
		  video.setAttribute('height', height);
		  canvas.setAttribute('width', width);
		  canvas.setAttribute('height', height);
		  streaming = true;
		}
	  }, false);
  
	  startbutton.addEventListener('click', function(ev){
		takepicture();
		var e = document.getElementById('select_filtre');
		value = e.options[e.selectedIndex].value;
		if(data_tmp != null && value != 0)
		{
			send_picture(data_tmp);
		}
		ev.preventDefault();
	  }, false);
	 
	  
	  var button_upload = document.getElementById('open_img_upload');
		button_upload.addEventListener('click', exec_upload);
	
		   
	  clearphoto();

	if(getCookie('upload'))
	{
		fusion();
		print_new_picture();
		delete_cookie('upload');
		

	}
	}	

	// Fill the photo with an indication that none has been
	// captured.

	if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
  
	function clearphoto() {
	  var context = canvas.getContext('2d');
	  context.fillStyle = "#AAA";
	  context.fillRect(0, 0, canvas.width, canvas.height);
		function fusion()
	{
		value = getCookie('value');
		$.ajax({
			type: "GET",
			url: "Montage",
			data: 'fonction=merge&user=' + user +'&filtre=' + value,
			success:function()
			{
				print_new_picture();
				delete_cookie('value');
			},
			error: function(xhr, status, error)
			{
				console.log('erreur' + status + 'message:' + error);
			}
		});
	}	  var data = canvas.toDataURL('image/png');
	  photo.setAttribute('src', data);
	}
	
	// Capture a photo by fetching the current contents of the video
	// and drawing it into a canvas, then converting that to a PNG
	// format data URL. By drawing it on an offscreen canvas and then
	// drawing that to the screen, we can change its size and/or apply
	// other changes before drawing it.

	var data_tmp;
	function takepicture() {
	  var context = canvas.getContext('2d');
	  if (width && height) {
		canvas.width = width;
		canvas.height = height;
		context.drawImage(video, 0, 0, width, height);
	  
		data_tmp = canvas.toDataURL('image/png');
		photo.setAttribute('src', data_tmp);

	  } else {
		clearphoto();
	  }
	}
	
	function exec_upload()
	{
		var upload = document.getElementById("img_upload");
		upload.click();

		upload.onchange = function (e)
		{
			document.cookie='upload=true';
			document.getElementById("upload").submit();
		}
		

			
	}


	// Set up our event listener to run the startup process
	// once loading is complete.
	window.addEventListener('load', startup, false);
	
  })();

	var value;

	function delete_cookie(c_name) 
	{
  		document.cookie = c_name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	}
	function getCookie(c_name)
	{
		var i,x,y,ARRcookies=document.cookie.split(";");

		for (i=0;i<ARRcookies.length;i++)
		{
			x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
			y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
			x=x.replace(/^\s+|\s+$/g,"");
			if (x==c_name)
			{
				return unescape(y);
			}
		}
	}

	function get_option()
	{
		resize_filter_div();
		var e = document.getElementById('select_filtre');
		value = e.options[e.selectedIndex].value;
		if(value != 0)
		{
			startbutton.disabled = false;
		  	upload.disabled = false;
		}
		else
		{
			startbutton.disabled = true;
	  		upload.disabled = true;
		}
		wrapper_filtre(value);
		document.cookie ='value=' + value; //register the index as cookie js
	}

	function resize_filter_div()
	{
		var y = document.getElementById("video").height;
		var x = document.getElementById("video").width;
		
		var src = document.getElementById("div_filtre");
		 if (window.matchMedia("(max-width: 450px)").matches)
		  {
			src.style.height = 10;
			src.style.width = 10
		  }
		else
		{
			src.style.height = y;
			src.style.width = x;
		}
	}

	function wrapper_filtre(filtre)
	{
		if(filtre == 1)
		{
			var img = document.createElement("img");
			img.id = "filtre1";
			var getLocation = window.location.hostname;
			img.src = 'http://' + getLocation + '/views/images/filter_1.png';
			var src = document.getElementById("div_filtre");
			src.appendChild(img);

		}

		if(filtre == 2)
		{
			var img = document.createElement("img");
			img.id = "filtre1";
			var getLocation = window.location.hostname;
			img.src = 'http://' + getLocation  + '/views/images/filter_2.png';
			var src = document.getElementById("div_filtre");
			src.appendChild(img);

		}

		if(filtre == 3)
		{
			var img = document.createElement("img");
			img.id = "filtre1";
			var getLocation = window.location.hostname;
			img.src = 'http://' + getLocation  + '/views/images/filter_3.png';
			var src = document.getElementById("div_filtre");
			src.appendChild(img);

		}

		if (filtre == 4)
		{
			var img = document.createElement("img");
			img.id = "filtre1";
			var getLocation = window.location.hostname;
			img.src = 'http://' + getLocation  + '/views/images/filter_4.png';
			var src = document.getElementById("div_filtre");
			src.appendChild(img);
		}

		if (filtre == 5)
		{
			var img = document.createElement("img");
			img.id = "filtre1";
			var getLocation = window.location.hostname;
			img.src = 'http://' + getLocation  + '/views/images/filter_5.png';
			var src = document.getElementById("div_filtre");
			src.appendChild(img);
		}

		if (filtre == 6)
		{
			var img = document.createElement("img");
			img.id = "filtre1";
			var getLocation = window.location.hostname;
			img.src = 'http://' + getLocation  + '/views/images/filter_6.png';
			var src = document.getElementById("div_filtre");
			src.appendChild(img);
		}

		if (filtre == 7)
		{
			var img = document.createElement("img");
			img.id = "filtre1";
			var getLocation = window.location.hostname;
			img.src = 'http://' + getLocation  + '/views/images/filter_7.png';
			var src = document.getElementById("div_filtre");
			src.appendChild(img);
		}

		if (filtre == 8)
		{
			var img = document.createElement("img");
			img.id = "filtre1";
			var getLocation = window.location.hostname;
			img.src = 'http://' + getLocation  + '/views/images/filter_8.png';
			var src = document.getElementById("div_filtre");
			src.appendChild(img);
		}

	
	}

  </script>

<div class ="container_main_side">
	<div class="container_main">

		<div class="container_video">
			<h3>Studio Photos</h3>

			<br>
			<div class="camera">
				<video id="video">Erreur vidéo non disponible.</video><br>
    			<button class="btn btn-dark" id="startbutton">Prendre Photo</button>
				<form id="upload" method="POST" enctype="multipart/form-data"></span>
				<input type="file" name="image" id="img_upload" style="display:none"/><input type="button" class="btn btn-dark " id="open_img_upload" value="Upload Photo">
				<input type="submit" value="send" name="submit_img_upload" style="display:none" />
				</form>
			 </div>
			<div id ="div_filtre">
			</div>

	</div>

	<div class="container_side">
		<canvas id="canvas" hidden="true">
		</canvas>
	<img id =photo hidden="true">
	<?php
	
	$i = 0;
	$tmp_src = 0;
	while($i != 3)
	{
			echo ('<div class="container_photomontage_overlay">
			<img id=\'gallery' . $i .'\' src=' . $tmp_src .'>
			<div class="imgmontage_overlay">
			<button onclick="save_photo(gallery' . $i . '.id);" id =\'button' . $i . '\' type=\'button\' class=\'btn btn-success\'>Save</button>
			<button onclick="delete_photo(gallery' . $i . '.id);" id =\'button' . $i . '2\' type=\'button\' class=\'btn btn-danger\'>Delete</button>
				</div>
			</div>');
		$i++;
	}

	?>
	</div>

	<div class="container_filtre">
	<select id ="select_filtre" onchange="get_option()">
		<option value= "0">Choisissez un Filtre</option>
		<option id="filter1" value="1">Minions</option>
		<option id="filter2" value="2">Tête de mort</option>
		<option id="filter3" value="3">Charlie Chaplin</option>
		<option id="filter4" value="4">Algérie</option>
		<option id="filter5" value="5">Mauvais temps</option>
		<option id="filter6" value="6">Trump</option>
		<option id="filter7" value="7">Pepe Meme</option>
		<option id="filter8" value="8">Caca</option>
	</select>
	</div>
</div>

<?php 
$content = ob_get_clean();
require_once('template.php');
?>

