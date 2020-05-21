<?php
	session_start();
	$t = "Camagru mdp oublié";
	ob_start();

	if(!empty($_POST["submit"]))
	{
		self::process_recovery($_POST["user"], $_POST["email"]);
	}

	if(!empty($_SESSION["success_recovery"]))
	{
		echo "<div class='alert alert-success' role='alert'>
		Une demande de reinitialisation vient d'être envoyé par mail !
	  </div>";
	}
	if(!empty($_SESSION["err_pass"]))
	{
		echo"<div class='alert alert-danger' role='alert'>
		Il semblerait que vos identifiants sont mauvais. Réssayez à nouveau avec votre login et email.
	  </div>";
	}

	if(!empty($_SESSION["err_db"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Erreur du server , contactez un administrateur.
	  </div>";
	}
?>
<div class="container_connexion">
<h1 class="logo">Camagru</h1>
<br>
<div class="champs_connexion">
	<form action="" method="post">
		<input class="field" type="text" name="user" placeholder="Nom d'utilisateur" value="">
		<input class="field" type="text" name="email" placeholder="Email" value="">
		<input class="field btn btn-primary mb-2" type="submit" name="submit" value="Reinitialiser">
	</form>
</div>
<hr>
</div>
<div class="container_register">
<p>Vous n'avez pas de compte ?</p> <a href="Register"><p>Inscrivez-vous</p></a>
</div>
<?php $content = ob_get_clean(); ?>
<?php require_once('template.php') ?>