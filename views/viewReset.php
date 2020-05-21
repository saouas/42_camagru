<?php
	session_start();
	$t = "Camagru reset mdp";
	ob_start();

	$page = explode("?", $_SERVER['REQUEST_URI']);
	$page = explode("=", $page[1]);
	$token = $page[1];
	$id = self::check_pass($token);

	if(!empty($_POST["submit"]) && !isset($_SESSION["err_token"]))
	{
		self::process_reset($_POST["passwd"], $_POST["passwd2"], $token, $id);
	}

	if(!empty($_SESSION["err_token"]))
	{
		echo"<div class='alert alert-danger' role='alert'>
		Il semblerait que vous avez entrez un mauvais lien de reinitialisation. Contactez un administrateur.
	  	</div>";
	}

	if(!empty($_SESSION["err_same_password"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Erreur mot de passe. Le mot de passe doit contenir au moins 8 caractères et le nom d'utilisateur ne doit pas contenir de caractères spéciaux. Merci.
		</div>";
	}

	if(!empty($_SESSION["err_db"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Erreur du server , contactez un administrateur.
	 	 </div>";
	}

	if(!empty($_SESSION["success_reset"]))
	{
		echo "<div class='alert alert-success' role='alert'>
		Votre mot de passe a bien été changé. Vous pouvez maintenant vous connecter !
	  </div>";
	}
?>

<div class="container_connexion">
		<h1 class="logo">Camagru</h1>
		<br>
		<div class="champs_connexion">
			<form action="" method="POST">
				<input class="field" type="password" name="passwd" placeholder="Nouveau mot de passe" value="">
				<input class="field" type="password" name="passwd2" placeholder="Répéter Mot de passe" value="">
			<br>	<input class="field btn btn-primary mb-2" type="submit" name="submit" value="Changer mon mot de passe">
			</form>
			<br>
			<hr>
</div>

<?php 
	$content = ob_get_clean();
	require_once('template.php');
?>