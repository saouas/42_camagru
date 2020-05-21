<?php
	require_once('views/View.php');

	class ControllerActivation extends UserManager
	{

		private $_view;
		private $_userManager;

		public function __construct($url)
		{
			if(isset($url) && @count($url) > 1)
			{
				throw new Exception('Page introuvable 404 NOT FOUND');
			}
			else
			{
				$this->print_activation();
			}
		}

		public function print_activation()
		{
			require_once('views/viewActivation.php');

			$this->_view = new View('Activation');
			$this->_view->generate(array(''));
		}

		public function activation($login, $cle)
		{
			$this->_userManager = new UserManager(array('user' => $login));
			$retour = $this->_userManager->get_info_activation($cle);
			$cle_user = $retour['cle'];
			$actif = $retour['actif'];
			if ($actif == '1')
			{
				$_SESSION["compte_deja_actif"] = "1";
			}
			else
			{
				if($cle == $cle_user)
				{
					if($this->_userManager->activer_compte() == true)
					{
						$_SESSION["compte_activer"] = "1";
					}
					else
					{
						$_SESSION["err_db"] = '1';
					}
				}
				else
				{
					$_SESSION["err_activation"] = "1";
				}
			}

		}
	}
?>