<?php 
	session_start();
	$t= "Camagru Connexion";
	ob_start();

	if(!empty($_SESSION["err_connexion"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		La connexion a échouée ..
	  </div>";
		
	}

	if(!empty($_POST["submit"]))
	{
		unset($_SESSION["err_connexion"]);
		self::test_connexion($_POST["user"], $_POST["passwd"]);
		header('Location: Connexion');
	}

	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if(parse_url($actual_link, PHP_URL_QUERY) != null)
	{
		$request = parse_url($actual_link)["query"];

		$fonction = explode('=', $request)[1];
		if($fonction == "deconnect")
		{
			$_SESSION["deconnect"]	="1";
		}				
	}

	if(!empty($_SESSION["deconnect"]))
	{

		if(!empty($_SESSION['loggued_on']))
		{
			unset($_SESSION['loggued_on']);
			echo "<div class='alert alert-success' role='alert'>
			Vous avez bien été deconnectée de Camagru. Redirection en cours ..
			</div>";
			header("Refresh:2; url=Accueil"); 
		}

		else
		{
			echo "<div class='alert alert-danger' role='alert'>
			Ouppppps. Vous n'êtes pas connectée. Veuillez vous connecter :)
				</div>";
		}
		unset($_SESSION["deconnect"]);
	}
	else
	{
		if(!empty($_SESSION['loggued_on']))
		{
			echo "<div class='alert alert-success' role='alert'>
			Vous avez bien été connecté ".ucfirst($_SESSION['loggued_on']) .". Bienvenue sur Camagru. Redirection en cours ..
			</div>";
			header("Refresh:3; url=Profile"); 
		}
	}

?>
    <body>
        <div class="container_connexion">
            <h1 class="logo">Camagru</h1>
            <br>
            <div class="champs_connexion">
                <form action="" method="post">
                    <input class="field" type="text" name="user" placeholder="Nom d'utilisateur" value="">
                    <input class="field" type="password" name="passwd" placeholder="Mot de passe" value="">
                    <input class="field btn btn-primary mb-2" type="submit" name="submit" value="Connexion">
                </form>
            </div>
            <hr>
            <a href="Passwordforgot"><p>Mot de passe oublié ?</p></a>
        </div>
        <div class="container_register">
            <p>Vous n'avez pas de compte ?</p> <a href="Register"><p>Inscrivez-vous</p></a>
        </div>

<?php $content = ob_get_clean(); ?>
<?php require_once('template.php');?>