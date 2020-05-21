<?php

    class UserManager extends Model
    {
		private $_mail;
		private $_name;
		private $_user;
		private $_passwd;
		private $_passwd2;
		private $_bio;

		public function __construct(array $data)
		{
			if(isset($data['mail']))
				$this->_mail = $data["mail"];
			if(isset($data["name"]))
				$this->_name = $data["name"];
			if(isset($data["user"]))
				$this->_user = $data["user"];
			if(isset($data["passwd"]))
				$this->_passwd = $data["passwd"];
			if(isset($data["passwd2"]))
				$this->_passwd2 = $data["passwd2"];
			if(isset($data["bio"]))
				$this->_bio = $data["bio"];
		}
		/*
			SETTERS
		*/

		protected function set_mail($mail)
		{
			$this->_mail = $mail;
		}

		protected function set_name($name)
		{
			$this->_name = $name;
		}

		protected function set_user($user)
		{
			$this->_user = $user;
		}

		protected function set_passwd($passwd)
		{
			$this->_passwd = $passwd;
		}
	
		protected function set_passwd2($passwd2)
		{
			$this->_passwd2 = $passwd2;
		}
		
		protected function set_bio($bio)
		{
			$this->_bio = $bio;
		}
		/*
			GETTERS
		*/

		public function get_user($id)
		{
			$db = Model::getBdd();
			$data = ['id' => $id];
			$req = "SELECT user FROM user WHERE id = :id";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch();

			return ($retour[0]);
		}

		protected function get_name()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			$req = "SELECT name FROM user WHERE user = :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch();

			return ($retour);
		}

		protected function get_bio()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			$req = "SELECT bio FROM user WHERE user = :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch();

			return ($retour);
		}

		public function get_mail()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			$req = "SELECT mail FROM user WHERE user = :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch();

			return ($retour);
		}

		protected function get_notif_comment_mail()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			$req = "SELECT notif_comment_mail FROM user WHERE user = :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch();

			return ($retour);
		}

		public function get_profile_picture()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			$req = "SELECT photo FROM user WHERE user = :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch();

			return ($retour);
		}

		public function get_id()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			$req = "SELECT id FROM user WHERE user = :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch();

			return ($retour[0]);
		}

		/*
			Methode permettant de
			recupérer la cle d'activation
			enregistré sur la db ainsi
			que la row actif
			@params cle
			@return array[0] ->cle array[1]->actif
		*/

		protected function get_info_activation($cle)
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			$req = "SELECT cle,actif FROM user WHERE user like :user";
			$stmt = $db->prepare($req);
			if($stmt->execute($data) && $row = $stmt->fetch())
			{
				$cle_bdd = $row['cle'];
				$actif_bdd = $row['actif'];
			}

			$retour = array('cle' => $cle_bdd, 'actif' => $actif_bdd);
			return ($retour);
		}

        public function getUsers()
        {
           return($this->getAll('user', 'User'));
		}
	
		protected function check_mail()
		{
			$mail = strtolower(trim(strip_tags($this->_mail)));
			if(!preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}$/", $this->_mail))
			{
				return false;
			}
			return true;
		}

		public function check_passwd()
		{
			$this->_passwd = trim(strip_tags($this->_passwd));
			$this->_passwd2 = trim(strip_tags($this->_passwd2));
			$number = preg_match("#[0-9]#", $this->_passwd);
			$lowercase = preg_match("#[a-z]#", $this->_passwd);
			$uppercase = preg_match("#[A-Z]#", $this->_passwd);

			if ($this->_passwd == $this->_passwd2)
			{
				if((strlen($this->_passwd) >= 8) && $number && $lowercase && $uppercase )  //MDP DOIT CONTENIR AU MOINS 8 CARACTERES
				{
					return (true);
				}
			}
			else
			{
				return (false);
			}
		}
		
		protected function check_name()
		{
			$this->_name = trim(strip_tags($this->_name));
			if(preg_match("#^[a-zA-Z-]#", $this->_name))
			{
				return true;
			}
			else
				return false;
		}

		protected function check_login()
		{
			$this->_user = trim(strip_tags($this->_user));
			if (preg_match("#^[a-zA-Z-]#", $this->_user))
			{
				if(strstr($this->_user, ' '))
				{
					return false;
				}
				return true;
			}
			else
				return false;
		}

		/*	Methode pour vérifier
			si un utilisateur est
			un doublon dans la db
			@return bool
			@params none
		*/
		
		protected function check_user_db()
		{
			$db = Model::getBdd();
			$data1 = ['user' => $this->_user];
			$data2 = ['mail' => $this->_mail];
			$req = "SELECT * FROM user WHERE user = :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data1);
			$count1 = $stmt->rowCount();

			$req2 = "SELECT * FROM user WHERE mail = :mail";
			$stmt2 = $db->prepare($req2);
			$stmt2->execute($data2);
			$count2 = $stmt2->rowCount();
			if(!$count1 && !$count2)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/*
			Methode pour vérifier
			si un autre utilisateur
			que soit meme contient
			email et pseudo
			@params none
			@return bool
		*/

		protected function check_other_user_db($user_to_be)
		{
			$db = Model::getBdd();
			$id = $this->get_id_related();
			$data1 = [
				'user' => $user_to_be,
				'id' => $id];
			$data2 = [
				'mail' => $user_to_be,
				'id' => $id];
			$req = "SELECT id FROM user WHERE user = :user AND id <> :id";
			$stmt = $db->prepare($req);
			$stmt->execute($data1);
			$count1 = $stmt->rowCount();

			$req2 = "SELECT id FROM user WHERE mail = :mail AND id <> :id";
			$stmt2 = $db->prepare($req2);
			$stmt2->execute($data2);
			$count2 = $stmt2->rowCount();
			
			if ($count1 >=1 || $count2 >=1)
			{
				return (false);
			}
			else
			{
				return (true);
			}
		}

		/* 
			Methode qui permet
			de verifier qu'il
			n'y a pas de doublons
			de mail dans la db
			@params none
			@return bool	
		*/

		protected function check_mail_db()
		{
			$db = Model::getBdd();
			$data2 = ['mail' => $this->_mail];
			$req2 = "SELECT * FROM user WHERE mail = :mail";
			$stmt2 = $db->prepare($req2);
			$stmt2->execute($data2);
			$count2 = $stmt2->rowCount();
		
			if($count2 == 0)
			{
				return (true);
			}
			else
			{
				return (false);
			}
		}

		/*	Fonction qui va
			demander confirmation
			de creation de compte
			sur le mail du client
			@params cle
			@return none
		*/

		public function verification_account($cle)
		{
			$data = [
				'cle' => $cle,
				'user' => $this->_user
			];
			$db = Model::getBdd();
			$req = "UPDATE user SET cle=:cle WHERE user like :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
		}

		/*	Methode inscription
			avec les methodes de
			validations testés
			@params none
			@return bool
		*/

		public function register()
		{
			if($this->check_mail() && $this->check_passwd() && $this->check_name() && 
				$this->check_login() && $this->check_user_db())
			{
				$photo = "data:image/png;base64,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";
				$data = [
					'mail' => $this->_mail,
					'name' =>  $this->_name,
					'user' => $this->_user,
					'passwd' => $this->hash_passwd(),
					'photo' => $photo
				];
				$db = Model::getBdd();
				$req = "INSERT INTO user (mail, name, user, passwd, photo) VALUES (:mail, :name, :user, :passwd, :photo)";
				$stmt = $db->prepare($req);
				$stmt->execute($data);
				return (true);
			}
			else
			{
				return (false);
			}
		}

		/*	Methode Hash Passwd */

		protected function hash_passwd()
		{
			return password_hash($this->_passwd, PASSWORD_BCRYPT);
		}

		protected function hash_passwd_ext($pass)
		{
			return password_hash($pass, PASSWORD_BCRYPT);
		}

		/*	Methode qui verifie
			si le compte est validé
			lors de la connexion
			@params none
			@return bool
		*/

		protected function check_actif()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];

			$req = "SELECT actif FROM user WHERE user like :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$row = $stmt->fetch();
			
			if($row['actif'] == '1')
				return (1);
			else
				return (0);
		}
		/*	Methode connexion
			@params none
			@return bool
		*/

		public function connexion()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			if($this->check_login() && $this->check_actif())
			{
				$req = "SELECT passwd FROM user WHERE user = :user";
				$stmt = $db->prepare($req);
				$stmt->execute($data);
				$result = $stmt -> fetch();
				if(password_verify($this->_passwd, $result[0]))
				{
					return (true);
				}
			}
			return (false);
		}

		/*	Methode pour
			changer de mdp
			@params mdp et id user
			@return bool	
		*/

		public function edit_password($id)
		{
			$db = Model::getBdd();
			$pass_hash = $this->hash_passwd();
			$data = [
				'passwd' => $pass_hash,
				'id' => $id 
			];
			$req = "UPDATE user SET passwd = :passwd WHERE id = :id";
			$stmt = $db->prepare($req);
			$test = $stmt->execute($data);

			return ($test);
		}

		/*	Methode pour
			vérifier qu'un
			unique id valide le
			couple (mail,user) dans
			la db
			@params none
			@return bool (result or false)
		*/
		protected function check_id_account()
		{
			$data = [
				'user' => $this->_user,
				'mail' => $this->_mail
			];
			$db = Model::getBdd();
			$req = "SELECT id FROM user WHERE user = :user AND mail = :mail";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$result = $stmt->fetchAll();
			if ($stmt->rowCount() > 1)
			{
				return (false);
			}
			else
			{
				return ($result);
			}
		}
		/*	Methode pour
			créer le pass de 
			recorvery composé
			de id , mail , token , expiration
			@params id_related, mail, token, expires
			@return bool
		*/
		protected function create_recovery_pass($id, $mail, $token, $expires)
		{
			$db = Model::getBdd();
			$data = [
				'id_related' => $id,
				'mail' => $mail,
				'token' => $token,
				'expires' => $expires
			];
			$req = "INSERT INTO password_reset (id_related, mail, token, expires) VALUES (:id_related, :mail, :token, :expires)";
			$stmt = $db->prepare($req);
			$test = $stmt->execute($data);
			if($test == true)
			{
				return (true);
			}
			else
			{
				return (false);
			}
		}
		/*	Methode pour
		vérifier le pass de
		recovery composé de
		token et expiration
		@params token
		@return (result or false)
		*/

		protected function check_recovery_pass($token)
		{
			date_default_timezone_set("Europe/Paris");
			$db = Model::getBdd();
			$data = [
				'token' => $token,
				'expires' => time() + 60*60
			];
			$req= "SELECT id_related FROM password_reset WHERE token = :token AND expires >= :expires";
			$stmt= $db->prepare($req);
			$stmt->execute($data);
			$result = $stmt->fetchAll();
			if($stmt->rowCount() > 0)
			{
				return ($result);
			}
			else
			{
				return (-1);
			}

		}
		/*	Methode pour
			supprimer tout les tokens
			de reinitilisation de passwd
			détenus par l'utilisateur
			@params id_related
			@return bool
		*/
		protected function delete_tokens_password($id)
		{
			$db = Model::getBdd();
			$data = ['id_related' => $id];
			$req = "DELETE FROM password_reset WHERE id_related = :id_related";
			$stmt = $db->prepare($req);
			$test = $stmt->execute($data);

			return ($test);
		}

		/*	Methode pour recupérer
				un id associer a un user
				@params none
				@return (int) id
		*/

		public function get_id_related()
		{
			$db = Model::getBdd();
			$data = ['user' => $this->_user];
			$req = "SELECT id FROM user WHERE user = :user";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch();
			if($retour == false)
			{
				$_SESSION["err_db"] = "1";
				return (-1);
			}
			return(($retour)[0]);
		}

		/*
			Methode qui met a jour
			le profil d'un utilisateur
			@params none
			@return bool
		*/

		protected function update_profile($id_original_user, $user)
		{
			$db = Model::getBdd();
			$data = [
				'name' => $this->_name,
				'user' => $user,
				'bio' => $this->_bio,
				'mail' => $this->_mail,
				'original_user' => $id_original_user
			];

			$req = "UPDATE user SET name = :name, user = :user, bio = :bio, mail = :mail WHERE id = :original_user";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);

			return ($retour);
		}

		/*
			Methode pour mettre
			a jour un mot de passe
			d'une user
			@params user
			@return bool
		*/

		protected function update_password_db($user)
		{
			$db = Model::getBdd();
			$data = [
				'passwd' => $this->hash_passwd($this->_passwd),
				'user' => $user,
			];
			$req = "UPDATE user SET passwd = :passwd WHERE user = :user";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);

			return ($retour);
		}

		/*
			Methode pour refuser
			ou accepter de recevoir
			des notifications par mail
			@params choice (0 or 1)
			@return bool
		*/

		protected function update_notifications_db($choice)
		{
			$db = Model::getBdd();
			$data = [
			'user' => $this->_user,
			'choice' => $choice,
			];
			$req = "UPDATE user SET notif_comment_mail = :choice WHERE user = :user";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);

			return ($retour);
		}
		/*
			Methode pour upload
			une image de profile
			depuis base64
			@params base64 img (string)
			@return bool
		*/

		protected function update_profile_pics($new_picture)
		{
			$db = Model::getBdd();
			$data = [
				'img' => $new_picture,
				'user' => $this->_user,
			];
			$req = "UPDATE user SET photo = :img WHERE user = :user";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);

			return ($retour);
		}


		/*
			Methode pour activer
			un compte qui a un couple
			cle,actif valide.
			@params none
			@return bool
		*/

		protected function activer_compte()
		{
			$db = Model::getBdd();
			$data = [
				'user' => $this->_user,
			];
			$req = "UPDATE user SET actif = 1 WHERE user like :user";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);

			return($retour);
		}
	}

?>