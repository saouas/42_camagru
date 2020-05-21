<?php

	require_once('views/View.php');
	class ControllerConnexion extends UserManager
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
				$this->print_connexion();
			
				
			}
		}

		public function print_connexion()
		{
			require_once('views/viewConnexion.php');

			$this->_view = new View('Connexion');
			$this->_view->generate(array(''));
		}

		public function test_connexion($login, $password)
		{
			$login = filter_var($login, FILTER_SANITIZE_STRING);
			$password = filter_var($password, FILTER_SANITIZE_STRING);
			$this->_userManager = new UserManager(array('user' => $login, 'passwd' => $password));
			$test = $this->_userManager->connexion();
			if($test == false)
			{
				$_SESSION["err_connexion"] = '1';
			}
			else
			{
				$_SESSION['loggued_on'] = $login;
			}
		}

	}