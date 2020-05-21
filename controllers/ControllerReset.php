<?php

	require_once('views/View.php');
	class ControllerReset extends UserManager
	{
		public function __construct($url)
		{
			if(isset($url) && @count($url) > 2)
			{
				throw new Exception('Page introuvable 404 NOT FOUND');
			}
			else
			{
				$this->print_reset();
			}
		}

		public function print_reset()
		{
			require_once('views/viewReset.php');

			$this->_view = new View('Reset');
			$this->_view->generate(array(''));
			unset($_SESSION["err_token"]);
			unset($_SESSION["err_db"]);
			unset($_SESSION["success_reset"]);
			unset($_SESSION["err_same_password"]);
		}

		public function check_pass($token)
		{
			$this->_userManager = new UserManager(array(''));
			$test = $this->_userManager->check_recovery_pass($token);
			if ($test == -1)
			{
				$_SESSION["err_token"] = "1";
			}
			return ($test[0][0]);
		}

		public function process_reset($passwd, $passwd2, $token, $id)
		{
			$this->_userManager->set_passwd($passwd);
			$this->_userManager->set_passwd2($passwd2);
			if($id >= 0)
			{
				$test3 = $this->_userManager->check_passwd();
				if ($test3 == true)
				{
					$test2 = $this->_userManager->edit_password($id);
					if ($test2 == false)
					{
						$_SESSION["err_db"] = "1";
					}
					else
					{
						$this->_userManager->delete_tokens_password($id);
						$_SESSION["success_reset"] = "1";
					}
				}
				else
				{
					$_SESSION["err_same_password"] = "1";
				}
			}
		}

	}