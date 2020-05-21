<?php
	session_start();
	ob_start();
	$t=  "Camagru activation compte";
	{
		if(explode("?", $_SERVER['REQUEST_URI'])!= false)
		{
			$page = explode("?", $_SERVER['REQUEST_URI']);
			if(explode("=", $page[1]) != false)
			{
				$page = explode("=", $page[1]); 
				$login = explode("&", $page[1])[0];
				$cle = $page[2];
				if($cle && $login)
				{
					self::activation($login, $cle);
				}
			}
		}
	}
	if(!empty($_SESSION["compte_deja_actif"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Votre compte est déjà actif , redirection vers la home page ...
	  </div>";
		unset($_SESSION["compte_deja_actif"]);
		header('refresh:3;url="'. URL .'"');
	}

	if(!empty($_SESSION["compte_activer"]))
	{
		echo "<div class='alert alert-success' role='alert'>Bienvenue sur Camagru , votre compte est maintenant actif !
			</div>";
		unset($_SESSION["compte_activer"]);
		header('refresh:3;url="'. URL .'"');
	}

	if(!empty($_SESSION["err_activation"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
			Erreur votre compte ne peux être activé ! 
	  </div>";
		unset($_SESSION["err_activation"]);
		header('refresh:3;url="'. URL .'"');
	}

	if(!empty($_SESSION["err_db"]))
	{
		echo "<div class='alert alert-danger' role='alert'>
		Erreur du server , contactez un administrateur.
	  </div>";
		unset($_SESSION["err_db"]);
		header('refresh:3;url="'. URL .'"');
	}

?>

<div class="tab-content">
</div>
<?php $content = ob_get_clean(); ?>
<?php require_once('template.php');?>
