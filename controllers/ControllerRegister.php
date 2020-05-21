<?php

	require_once('views/View.php');
	class ControllerRegister extends UserManager
	{
		private $_userManager;
		private $_view;

		public function __construct($url)
		{
			if(isset($url) && @count($url) > 1)
			{
				throw new Exception('Page introuvable 404 NOT FOUND');
			}
			else
			{
				$this->print_register();
			}
		}

		public function print_register()
		{
			require_once('views/viewRegister.php');

			$this->_view = new View('Register');
			$this->_view->generate(array(''));
		}

		public function process_register($mail, $user)
		{
			$cle = md5(microtime(TRUE)*100000);
			$this->_userManager->verification_account($cle);
			
			$sujet = "Camagru :: Activation de votre compte";
			$entete = "From: noreply@camagru.saouas";
			$entete .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$entete .= "MIME-Version: 1.0" . "\r\n";
			$message = 'Bienvenue sur Camagru,
		
			Pour activer votre compte, veuillez cliquer sur le lien ci dessous
			ou copier/coller dans votre navigateur internet.
			
			http://192.168.99.100/Activation?log='.urlencode($user).'&cle='.urlencode($cle).'
			
			
			---------------
			Ceci est un mail automatique, Merci de ne pas y répondre.';

			mail($mail, $sujet, $message, $entete);
		}

		public function test_inscription($mail, $name, $user, $passwd, $passwd2)
		{
			$user = filter_var($user, FILTER_SANITIZE_STRING);
			$name = filter_var($name, FILTER_SANITIZE_STRING);
			$mail = filter_var($mail, FILTER_SANITIZE_EMAIL);
			$passwd = filter_var($passwd, FILTER_SANITIZE_STRING);
			$passwd2 = filter_var($passwd2, FILTER_SANITIZE_STRING);
			unset($_SESSION["err_inscription"]);
			unset($_SESSION["success_inscription"]);
			$this->_userManager = new UserManager(array('mail' => $mail, 'name' => $name, 'user' => $user, 'passwd' => $passwd, 'passwd2' => $passwd2));
			$test = $this->_userManager->register();

			if($test == false)
			{
				$_SESSION["err_inscription"] = "1";
				header('Location: '.$_SERVER['REQUEST_URI']);	
			}
			else
			{
				$this->process_register($mail, $user);
				$_SESSION["success_inscription"] = "1";
				header('Location: '.$_SERVER['REQUEST_URI']);
			}
		}	

	}