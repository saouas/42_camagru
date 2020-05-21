<?php

	require_once('views/View.php');
	class ControllerPasswordforgot extends UserManager
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
				$this->print_password_forgot();
			}
		}

		public function print_password_forgot()
		{
			require_once('views/viewPasswordforgot.php');

			$this->_view = new View('Passwordforgot');
			$this->_view->generate(array(''));
			unset($_SESSION["err_db"]);
			unset($_SESSION["success_recovery"]);
			unset($_SESSION["err_pass"]);
		}

		public function process_recovery($user, $mail)
		{
			$user = filter_var($user, FILTER_SANITIZE_STRING);
			$mail = filter_var($mail, FILTER_SANITIZE_EMAIL);
			$this->_userManager = new UserManager(array('user' => $user, 'mail' => $mail));
			$test = $this->_userManager->check_id_account();
			if ($test == false)
			{
				$_SESSION["err_pass"] = "1";
			}
			else
			{
				date_default_timezone_set('Europe/Paris'); 
				$id = $test[0][0];
				$timestamp = time() + $id;
				$expires = time() + 3600*2;
				$token = strrev(md5($timestamp));

				$test2 = $this->_userManager->create_recovery_pass($id, $mail, $token, $expires);
				if($test2 == false)
				{
					$_SESSION["err_db"] = "1";
				}
				else
				{
					$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' .$_SERVER['HTTP_HOST'];
					$sujet = "Camagru :: Mot de passe oublier";
					$entete = "From: noreply@camagru.saouas\r\n";
					$entete .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$entete .= "MIME-Version: 1.0" . "\r\n";
					$message = 'Bienvenue sur Camagru,<br><br>
					
					Une demande de reinitialisation de mot de passe a été demandée. <br>
					Si ce n\'est pas vous qui avez effectué cette demande ignorez simplement ce mail, <br>
					<br>
					Dans le cas contraire voici votre lien vous permettant de changer votre mot de passe. <br>
					Celui-çi est valable une heure : <a href="'. $url . '/Reset?token='. $token .'">Cliquez ici</a> <br>
					<br>
					--------------- <br>
					Ceci est un mail automatique, Merci de ne pas y répondre.<br>';
					
					mail($mail, $sujet, $message, $entete);
					$_SESSION["success_recovery"] = "1";
				}
			}
		}
	}