<?php
	session_start();
	$t = "Camagru inscription";

	ob_start();
	if (!empty($_SESSION["err_inscription"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
	Erreur durant l'inscription. Le mot de passe doit contenir au moins 8 caractères dont une majuscule , une minuscule et un chiffre. Le nom d'utilisateur ne doit pas contenir de caractères spéciaux. Merci.
	</div>";
		unset($_SESSION["err_inscription"]);
	}

	if (!empty($_SESSION["success_inscription"]))
	{
		echo "<div class='alert alert-warning' role='alert'>
		Veuillez activez votre compte ! Un mail vous a été envoyé.
		</div>";
		unset($_SESSION["success_inscription"]);
	}

	if(!empty($_POST["submit"]))
	{
		self::test_inscription($_POST["mail"], $_POST["name"], $_POST["user"], $_POST["passwd"], $_POST["passwd2"]);
	}

	if(!empty($_SESSION["loggued_on"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Vous êtes déjà inscrit ! Veuillez vous deconnecter avant de poursuivre une inscription.
		</div>";
		header("Refresh:2; url=Accueil");
	}
	?>
	
        <title>Camgru inscription</title>
        <div class="container_register2">
            <h1 class="logo">Camagru</h1>
            <br>
            <div class="champs_connexion">
                <form action="" method="POST">
                    <input class="field" type="email" name="mail" placeholder="Email" value="">
                    <input class="field" type="text" name="name" placeholder="Nom complet" value="">
                    <input class="field" type="text" name="user" placeholder="Nom d'utilisateur" value="">
                    <input class="field" type="password" name="passwd" placeholder="Mot de passe" value="">
					<input class="field" type="password" name="passwd2" placeholder="Mot de passe encore" value="">
                    <input class="field btn btn-primary mb-2" type="submit" name="submit" value="Inscription">
                </form>
            </div>
            <hr>
            <p class = "ph_grey">En vous inscrivant, vous acceptez 
			nos Conditions générales. 
			Découvrez comment nous 
			recueillons, utilisons et partageons 
			vos données en lisant notre 
			Politique d’utilisation des
			données et comment nous 
			utilisons les cookies et autres
			technologies similaires en 
			consultant notre Politique 
			d’utilisation des cookies.</p>
        </div>
        <div class="container_register_down">
            <p>Vous avez un compte ?</p> <a href="Connexion"><p>Connectez-vous</p></a>
		</div>

		<?php 
		$content = ob_get_clean();
		require_once('template.php'); 
		?>