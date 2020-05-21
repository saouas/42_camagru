<header>
    <nav class="navbar navbar-expand navbar-light bg-light">
  <a class="navbar-brand" href="<?= URL ?>">Camagru</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarnav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul id="navbarnav" class="navbar-nav ml-auto">
      <li class="nav-item active">
        <a class="nav-link" href="<?= URL ?> ">Home</a>
      </li>
	  <?php if(!empty($_SESSION["loggued_on"]))
	  {
		  echo "<li class='nav-item'>
		  <a class='nav-link' href='Profile'>Profile</a>
		</li>";
	  }
    ?> 

	<?php if(!empty($_SESSION["loggued_on"]))
	{
		echo "<li class='nav-item'>
		  <a class='nav-link' href='Montage'>Studio</a>
		</li>";
	}
	?>

	<?php if(empty($_SESSION["loggued_on"]))
	{
		echo "<li class='nav-item'>
        <a class='nav-link' href='Connexion'>Se connecter</a>
	  </li>";
	} 
	  ?>
	  <?php if(empty($_SESSION["loggued_on"]))
	  {
		echo "<li class='nav-item'>
        <a class='nav-link' href='Register'>S'inscrire</a>
      </li>";
	  }

	  ?>
     <?php if(!empty($_SESSION["loggued_on"]))
	{ 
	  echo "<li class='nav-item'>
        <a class='nav-link' href='Connexion?fonction=deconnect'>Deconnexion</a>
	  </li>";
	}
	?>

    </ul>
  </div>
    </nav>
</header>