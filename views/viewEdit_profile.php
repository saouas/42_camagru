<?php 
	$t = "Camagru Parametres";
	session_start();
	ob_start();
	unset($_SESSION["err_db"]);
	if(empty($_SESSION["loggued_on"]))
	{
		header('Location: Accueil');
	}
	$edit_profile = self::placeholder_editprofile($_SESSION["loggued_on"]);
	$notification = self::placeholder_notification($_SESSION["loggued_on"]);
	if ($notification != false)
	{
		$check= $notification[0];
	}


	if(!empty($_POST["submit_edit_profile"]) && !(isset($_SESSION["err_login_mail"])))
	{
		if(isset($_POST["name_ep"]) && isset($_POST["user_ep"]) && isset($_POST["bio_ep"]) && isset($_POST["mail_ep"]))
		{
				self::update_edit_profile($_SESSION["loggued_on"]);
				if(!empty($_SESSION["success_change"]))
				{
					echo "<div class='alert alert-success' role='alert'>
					Vos informations ont bien été mises à jours !</div>";
					unset($_SESSION["success_change"]);
					header("refresh: 0");
				}
		}
	}

	if(!empty($_POST["submit_password"]))
	{
		if(isset($_POST["old_passwd"]) && isset($_POST["passwd2"]) && isset($_POST["passwd"]))
		{
			self::update_password($_SESSION["loggued_on"]);
		}
	}

	if(!empty($_POST["submit_notifications"]))
	{
		if(isset($_POST["notifications"]))
		{
			self::update_notifications($_SESSION["loggued_on"]);
			header("refresh:2");
		}
	}

	if(!empty($_FILES["image_profile"]))
	{
		if(is_uploaded_file($_FILES["image_profile"]["tmp_name"]))
		{
			$_SESSION["imgprofile_name"] = $_FILES["image_profile"]["name"];
			$_SESSION["imgprofile_size"] = $_FILES["image_profile"]["size"];
			$_SESSION["imgprofile_tmp_name"] = $_FILES["image_profile"]["tmp_name"];
			if(!empty($_SESSION["loggued_on"]))
			{
				$user = $_SESSION["loggued_on"];
			};
			self::upload_profile_picture($user);
		}
	}

	if(!empty($_SESSION["err_login_mail"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Ce mail et/ou ce pseudonyme est dejà utilisé(s). Veuillez en choisir un autre(s)!</div>";
		unset($_SESSION["err_login_mail"]);
	}

	if(!empty($_SESSION["err_nom"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Le nom que vous avez entré n'est pas valide !</div>";
		unset($_SESSION["err_nom"]);
	}

	if(!empty($_SESSION["err_bio"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Votre biographie ne peut être vide !</div>";
		unset($_SESSION["err_bio"]);
	}
	if(!empty($_SESSION["err_user"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Votre pseudo ne doit pas contenir d'espaces. Il doit contenir uniquement des lettres !</div>";
		unset($_SESSION["err_user"]);
	}
	if(!empty($_SESSION["passwd_change"]))
	{
		echo "<div class='alert alert-success' role='alert'>
		Votre mot de passe a bien été changé !</div>";
		unset($_SESSION["passwd_change"]);
	}
	if(!empty($_SESSION["passwd_err_format"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
	Erreur de format mot de passe: Minimum 8 caractères, une majuscule et une minuscule merci !</div>";
		unset($_SESSION["passwd_err_format"]);
	}
	if(!empty($_SESSION["passwd_err_old"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Ancien mot de passe erroné !</div>";
		unset($_SESSION["passwd_err_old"]);
	}
	if(!empty($_SESSION["success_notif"]))
{
		echo "<div class='alert alert-success' role='alert'>
		Votre choix de notifications par email a bien été pris en compte !</div>";
		unset($_SESSION["success_notif"]);
	}
	if(!empty($_SESSION["err_db"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Une erreur est survenu, contactez un administrateur !</div>";
		unset($_SESSION["err_db"]);
	}
?>
<script>
	function show_me(id)
	{
		close_all_tabs();
		var div = document.getElementById(id);
			div.style.display = "block";
	}

	function close_all_tabs()
	{
		var div1 = document.getElementById('edit_profile');
		div1.style.display = "none";
		var div2 = document.getElementById('change_password_profile');
		div2.style.display = "none";
		var div3 = document.getElementById('notifications');
		div3.style.display = "none";
		var div4 = document.getElementById('aide');
		div4.style.display = "none";
	}

	function exec_upload()
	{
		 var upload = document.getElementById("img_upload");
		 upload.click();

		 upload.onchange = function (e)
		 {
		 	document.getElementById("upload").submit();
		 }
	}

</script>
	<script>
	window.onload = function () { show_me('edit_profile');};
	</script>
	<div class="container_parametres">
		<div class="menu_param">
			<ul class="nav nav-tabs" id="myTab" role="tablist" >
				<li class="nav-item">
					<a href="#" class="nav-link" onclick="show_me('edit_profile')">Editez profile</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link"  onclick="show_me('change_password_profile')">Changez mot de passe</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link"  onclick="show_me('notifications')">Notifications</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link"  onclick="show_me('aide')">Aide</a>
				</li>
			</ul>

		</div>
			<div class="tab-content">
				<div class="first_block_param">
					<div class="profile_pics_param">
						<h1><?php 
						if(!empty($_SESSION["loggued_on"]))
						{
							$user = $_SESSION["loggued_on"];
						};
						echo ucfirst(htmlspecialchars($user));
						
						?></h1>
						<img class="profile_picture" src="<?php  
						if(!empty($_SESSION["loggued_on"]))
						{
							$user = $_SESSION["loggued_on"];
						};
						echo htmlspecialchars(self::print_profile_picture($user)); ?>">

					</div>
				</div>
				<div id ="edit_profile" class="second_block_param">
					<div id = "changer_photo_profile">
					<form id="upload" method="POST" enctype="multipart/form-data">
						<input type="file" name="image_profile" id="img_upload" style="display:none"/>
						<input type="submit" value="send" name="submit_img_upload" style="display:none"/>
					</form>
					<a href="#" onclick="exec_upload();"><p>Changer photo de profil</p></a></div>
					<div class="edit_profile_form">
					<form action="" method="post">
						<label>Nom<input class="field" type="text" name="name_ep" value="<?php echo htmlspecialchars($edit_profile["name"][0]);?>"></label>
						<label>Pseudo<input class="field" type="text" name="user_ep" value="<?php echo htmlspecialchars($edit_profile["user"]);?>"></label>
						<label>Bio<textarea class="field" name="bio_ep" ><?php echo htmlspecialchars($edit_profile["bio"][0]); ?></textarea></label>
						<label>Email<input class="field" type="email" name="mail_ep" value="<?php echo htmlspecialchars($edit_profile["mail"][0]); ?>"></label>
                  		<input class="field btn btn-primary mb-2 submit_edit_profile" type="submit" name="submit_edit_profile" value="Mise à jour">
                	</form>
					</div>
				</div>

				<div id="change_password_profile" class="second_block_param">
					<form action="" method="post">
							<label>Ancien mot de passe<input class="field" type="password" name="old_passwd" placeholder="Ancien mot de passe" value=""></label>
							<label>Nouveau mot de passe<input class="field" type="password" name="passwd" placeholder="Nouveau mot de passe encore" value=""></label>
							<label>Nouveau mot de passe encore<input class="field" type="password" name="passwd2" placeholder="Nouveau mot de passe encore" value=""></label>
							<input class="field btn btn-primary mb-2 submit_edit_profile" type="submit" name="submit_password" value="Mise à jour">
					</form>
				</div>

				<div id="notifications" class="second_block_param">
					<h2>Notifications via email </h2>
					<p>Lorsque une image reçoit un nouveau commentaire, l’auteur de cette image doit<br>
						en être informé par mail. Cette préférence est activée par défaut, mais peut être<br>
						désactivée ci-dessous</p>
					<div class="custom-control custom-checkbox">
						<form action ="" method="POST">
							<div class="radio">
								<label><input type="radio" name="notifications" value="1" <?php if($check == true) { echo htmlspecialchars("checked");} ?> >Oui j'accepte</label>
							</div>
							<br><br>
							<div class="radio">
								<label><input type="radio" name="notifications" value="0" <?php if($check == false) { echo htmlspecialchars("checked");}?> >Non je refuse</label>
							</div>
							<input class="field btn btn-primary mb-2 submit_edit_profile" type="submit" name="submit_notifications" value="Mise à jour">
						</form>
					</div>
				</div>

				<div id="aide" class="second_block_param">
					<div class="box_help_center">
						<br>
						<center><h2>Help center </h2></center>
						<hr>
						<h5>Lorem Ipsum ?</h5>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.<br> 
							Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.<br>
							Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.<br>
							Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					
							<h5>Lorem Ipsum ?</h5>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.<br> 
							Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.<br>
							Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.<br>
							Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					</div>
					<center>	<button type="button" class="btn btn-primary">Contactez-nous</button> </center>
				</div>

			</div>

<?php $content = ob_get_clean();?>

<?php require_once('template.php');?>