<?php

    class PhotoManager extends UserManager
    {
        private $_userManager;
        private $_id;
        private $_date;
        private $_file;
        
        public function __construct(array $data)
        {
            if(isset($data['id']))
                $this->_id = $data["id"];
            if(isset($data["date"]))
                $this->_date = $data["date"];
            if(isset($data["file"]))
                $this->_file = $data["file"];
        }

		public function set_id($id)
		{
			$this->_id = $id;
		}
		/* 
			Methode pour upload
            une image sur la db
            a l'user correspondant
            @params image en base64, user , key (tmp_file = 0 | final file = 1)
            @return bool
        */

        public function upload_image($image, $user, $key)
        {
            $this->_userManager = new UserManager(array('user' => $user));
            $this->_id = $this->_userManager->get_id_related();
        
            $db = Model::getBdd();
            $data = [
                'id_owner' => $this->_id,
				'file' => $image,
				'key' => $key,
            ];
            $req = "INSERT INTO studio (id, id_owner, date, file, key_value) VALUES (0, :id_owner, now(), :file, :key)";
            $stmt = $db->prepare($req);
            $test = $stmt->execute($data);
            
            return ($test);
        }

        /*  Methode permettant
            de récuperer la derniere
            image enregistrer dans
			le studio photo en mode tmp
			key value = 0;
            @params user
            @return img or false (-1)
        */

        public function get_last_image($user)
        {
            $this->_userManager = new UserManager(array('user' => $user));
            $this->_id = $this->_userManager->get_id_related();


            $db = Model::getBdd();
            $data = [
                'id_owner' => $this->_id,
            ];
            $req = "SELECT * FROM studio WHERE id_owner = :id_owner AND key_value = 0 ORDER BY id DESC";
            $stmt = $db->prepare($req);
            $stmt->execute($data);
            $retour = $stmt->fetch();
            if($retour == false)
			{
				$_SESSION["err_db"] = "1";
				return (-1);
			}
            return(($retour));
        }

        /*  Methode permettant
            d'avoir le nbr total
            d'images dans le studio
            reliées à une personne
            @params user
            @return (int) nbr of images
        */

        public function count_total_img($user)
        {
            $this->_userManager = new UserManager(array('user' => $user));
            $this->_id = $this->_userManager->get_id_related();

            $db = Model::getBdd();
            $data = [
                'id_owner' => $this->_id,
            ];
            $req = "SELECT * FROM studio WHERE id_owner = :id_owner AND key_value = 1";
            $stmt = $db->prepare($req);
            $stmt->execute($data);
            $retour = $stmt->rowCount();

            return ($retour);
        }

        /*  Methode permettant
            d'avoir les 3 dernieres
            images d'un user prise
            depuis le studio
            @params user
            @return double array 
        */

        public function get_three_last_image($user)
        {
            $this->_userManager = new UserManager(array('user' => $user));
            $this->_id = $this->_userManager->get_id_related();

            $db = Model::getBdd();
            $data = [
                'id_owner' => $this->_id,
            ];
            $req = "SELECT file FROM studio WHERE id_owner = :id_owner AND key_value = 1 ORDER BY id DESC LIMIT 3";
            $stmt = $db->prepare($req);
            $stmt->execute($data);
            $retour = $stmt->fetchAll();

            return ($retour);
		}
		
		/*	Methode permettant
			de changer le statut
			d'une image de la 
			table studio photo
			@params user , key
			@return bool
		*/
		public function change_key_value_img($user, $key)
		{
			$last_img = $this->get_last_image($user)[3];
			$this->_userManager = new UserManager(array('user' => $user, 'file' => $last_img));
			$this->_id = $this->_userManager->get_id_related();
			
			
			$db = Model::getBdd();
            $data = [
				'key_value' => $key,
				'id_owner' => $this->_id,
				'file' => $last_img,
			];
			$req = "UPDATE studio SET key_value = :key_value WHERE id_owner = :id_owner AND file = :file";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);
			return ($last_img);
		}

		/*
			Methode permettant
			de supprimer une des 3
			dernières image
			finies (1) du studio sous
			demande de l'utilisateur
			@params (int) 0,1 ou 2, user
			@return bool
		*/
		public function delete_photo_bdd($position_from_the_end, $user)
		{
			$this->_userManager = new UserManager(array('user' => $user));
			$this->_id = $this->_userManager->get_id_related();

			$db = Model::getBdd();
			$data = [
				'id_owner' => $this->_id,
			];
			$req = "SELECT COUNT(id) FROM studio WHERE id_owner = :id_owner AND key_value = 1";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);
			$last_img = $stmt->fetch()[0];

			$nb_img = $last_img - $position_from_the_end - 1;
		 	$data2 = [
		 		'id_owner' => (int)$this->_id,
		 		'last_img' => (int)$nb_img,
		 	];
			$req2 = $db->prepare('SELECT id FROM studio WHERE id_owner = :id_owner AND key_value = 1 LIMIT :last_img, 1');
			$req2->bindParam(':id_owner', $data2['id_owner'], PDO::PARAM_INT);
			$req2->bindParam(':last_img', $data2['last_img'], PDO::PARAM_INT);
			$retour = $req2->execute();
			$id_to_delete = $req2->fetch()[0];
			 

			$data3 = ['id' => (int)$id_to_delete];
			$prev_id = $data3['id'] - 1;
			$req3 = $db->prepare("DELETE FROM studio WHERE id IN (:id, :prev_id)");
			$req3->bindParam(':id', $data3['id'], PDO::PARAM_INT);
			$req3->bindParam(':prev_id', $prev_id, PDO::PARAM_INT);
			$retour2 = $req3->execute();

			 return ($retour2);
		}

		/*
			Methode permettant de 
			sauvegarder une image 
			de facon definitive dans
			la db : set saved to 1
			@params (int) 0,1 ou 2, user
			@return bool
		*/

			public function save_photo_bdd($photo, $user)
			{
				$this->_userManager = new UserManager(array('user' => $user));
				$this->_id = $this->_userManager->get_id_related();

				$db = Model::getBdd();

				$data = [
					'id_owner' => $this->_id,
				];
				$req = "SELECT COUNT(id) FROM studio WHERE id_owner = :id_owner AND key_value = 1";
				$stmt = $db->prepare($req);
				$retour = $stmt->execute($data);
				$last_img = $stmt->fetch()[0];
				
				$nb_img = $last_img - $photo - 1;
		 		$data2 = [
		 		'id_owner' => (int)$this->_id,
		 		'last_img' => (int)$nb_img,
		 		];
				$req2 = $db->prepare('SELECT id FROM studio WHERE id_owner = :id_owner AND key_value = 1 LIMIT :last_img, 1');
				$req2->bindParam(':id_owner', $data2['id_owner'], PDO::PARAM_INT);
				$req2->bindParam(':last_img', $data2['last_img'], PDO::PARAM_INT);
				$retour = $req2->execute();
				$id_to_delete = $req2->fetch()[0];
				
				

				$data3 = ['id' => (int)$id_to_delete];
				$req3 = $db ->prepare("UPDATE studio SET saved = 1 WHERE id = :id");
				$req3->bindParam(':id', $data3['id'], PDO::PARAM_INT);
				$retour = $req3->execute();

				return ($retour);
			}
		
		/*
			Methode permettant de 
			calculer le nombre totales
			d'images saved dans le studio
			photos
			@params
			@return (int)
		*/

			public function nb_total_saved_photos_home()
			{
				$this->_userManager = new UserManager(array(''));

				$db = Model::getBdd();
				$req = "SELECT COUNT(id) FROM studio WHERE saved = 1";
				$stmt = $db->prepare($req);
				$stmt->execute();
				$retour = $stmt->fetch()[0];

				return ($retour);
			}

		/*
			Methode permettant de
			recupérer toute les photos
			ordonées depuis un OFFSET
			Départ et pour une LIMIT
			@params départ(int), image_per_page($int)
			@return array(array)
		*/

		public function select_all_photos_home($depart, $image_per_page)
		{
			$this->_userManager = new UserManager(array(''));

			$db = Model::getBdd();
			$data =[
				'depart' => $depart,
				'image_per_page' => $image_per_page,
			];
			$req = $db->prepare("SELECT * FROM studio WHERE saved = 1 ORDER BY date DESC LIMIT :depart, :image_per_page");
			$req->bindParam(':depart', $data['depart'], PDO::PARAM_INT);
			$req->bindParam(':image_per_page', $data['image_per_page'], PDO::PARAM_INT);
			$req->execute();
			$retour = $req->fetchAll();
			
			return ($retour);
		}

		/*
			Methode permettant de 
			recupérer le user d'un
			propriétaire d'une image
			@params id of img
			@return user (string)
		*/
			
		public function get_user_of_img($id)
		{
			$this->_userManager = new UserManager(array(''));

			$db = Model::getBdd();
			$data =[
				'id' => $id
			];
			$req = "SELECT id_owner FROM studio WHERE id = :id";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch()[0];
			$id_user = (int)$retour;

			$data2=[
				'id' => $id_user
			];
			$req = $db->prepare("SELECT user FROM user WHERE id like :id");
			$req->bindParam(':id', $data2['id'], PDO::PARAM_INT);
			$req->execute();
			$user = $req->fetch()[0];
			
			return ($user);
		}

		/*
			Methode permettant de
			recupérer la photo de profil
			d'un propriétaire d'une image
			@params id of img
			@return user (string)
		*/

		public function get_profilepics_of_img($id)
		{
			
			$this->_userManager = new UserManager(array(''));
			$db = Model::getBdd();
			$data =[
				'id' => (int)$id
			];
			$req = "SELECT id_owner FROM studio WHERE id = :id";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch()[0];
			$id_user = (int)$retour;

			$data2=[
				'id' => $id_user
			];
			$req = $db->prepare("SELECT photo FROM user WHERE id like :id");
			$req->bindParam(':id', $data2['id'], PDO::PARAM_INT);
			$req->execute();
			$photo = $req->fetch()[0];

			
			return ($photo);
		}

		/*
			Methode permettant de
			selectionné toute les photos
			(saved) d'un profil utilisateur
			@params
			@return (int) nbr
		*/

		public function select_all_photos_profile($depart, $image_per_page, $user)
		{
			$this->_userManager = new UserManager(array('user' => $user));
			$this->_id = $this->_userManager->get_id_related();


			$db = Model::getBdd();
			$data =[
				'depart' => $depart,
				'image_per_page' => $image_per_page,
				'id_owner' => $this->_id
			];

			$req = $db->prepare("SELECT * FROM studio WHERE saved = 1 AND id_owner = :id_owner ORDER BY date DESC LIMIT :depart, :image_per_page");
			$req->bindParam(':depart', $data['depart'], PDO::PARAM_INT);
			$req->bindParam(':image_per_page', $data['image_per_page'], PDO::PARAM_INT);
			$req->bindParam(':id_owner', $data['id_owner'], PDO::PARAM_STR);
			$req->execute();
			$retour = $req->fetchAll();
			return ($retour);
		}

		/*
			Methode permettant de
			recupérer le nbr total d'images
			saved d'un utilisateur
			@params
			@return 
		*/

		public function nb_total_saved_photos_profile($user)
		{
			$this->_userManager = new UserManager(array('user' => $user));
			$this->_id = $this->_userManager->get_id_related();

			$db = Model::getBdd();
			$data =[
				'id_owner' => $this->_id,
			];
			$req = "SELECT COUNT(id) FROM studio WHERE saved = 1 AND id_owner = :id_owner";
			$stmt = $db->prepare($req);
			$stmt->execute($data);
			$retour = $stmt->fetch()[0];

			return ($retour);
		}

		/*
			Teste si l'utilisateur a activé
			notifications par mail , si oui
			un avertissement avec le commentaire
			lui est envoyé sinon rien ne se passe
			@params user et commentaire (string both)
			@return bool
		*/

		public function sendmail_if_notifcomment($id_photo)
		{
			$user_owner_photo = $this->get_user_of_img($id_photo);
			$this->_userManager = new UserManager(array('user' => $user_owner_photo));
			$this->_id = $this->_userManager->get_id_related();

			$db = Model::getBdd();
			$data = [
				'id' => $this->_id,
			];
			$req = "SELECT notif_comment_mail FROM user WHERE id = :id";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);
			$retour = $stmt->fetch()[0];

			if ($retour == 1)
			{
				return (true);
			}
			else
			{
				return false;
			}
		}

		/*
			Methode permettant d'insérer
			un commentaire sur une photo
			precise et ecrit utilisateur
			precis
			@params id_photo(int) , user(string), commentaire(string)
			@return bool
		*/
		public function insert_commentaire($id_photo, $user, $commentaire)
		{
			$this->_userManager = new UserManager(array('user' => $user));
			$this->_id = $this->_userManager->get_id_related();

			$db = Model::getBdd();
			$data =[
				'id_photo' => $id_photo,
				'id_user' => $this->_id,
				'commentaire' => $commentaire,
			];
			$req = "INSERT INTO commentaires (id_photo, id_user, commentaire) VALUES (:id_photo, :id_user, :commentaire)";
			$stmt = $db->prepare($req);
			$retour = $stmt->execute($data);

			if($retour != false)
			{
			
				$this->sendmail_if_notifcomment($user, $commentaire, $id_photo);
			}
			
			return ($retour);
		}

		/*
			Methode pour recupérer
			une image à partir de
			sond id (debug)
			@params id(int)
			@return img base64 (string);
		*/

		public function get_image($id)
		{
			$db = Model::getBdd();
			$data = [
				'id' => $id,
			];
			$req = "SELECT file from studio WHERE id = :id";
			$stmt = $db->prepare($req);
			$stmt->execute($data);

			$retour = $stmt->fetch();

			return ($retour['file']);
		}

		/*
			Methode permettant
			de recupérer les commentaires
			d'une image (limit,offset)
			@params id_photo (int)
			@return array(array)
		*/

		public function get_commentaire($id_photo, $depart, $nbr_of_comments)
		{
			$db = Model::getBdd();
			$data = [
				'id_photo' => $id_photo,
				'depart'=> $depart,
				'nbr_of_comments' => $nbr_of_comments
			];
			$req = $db->prepare("SELECT * FROM commentaires WHERE id_photo = :id_photo ORDER BY date DESC LIMIT :depart, :nbr_of_comments");
			$req->bindParam(':id_photo', $data['id_photo'], PDO::PARAM_INT);
			$req->bindParam(':depart', $data['depart'], PDO::PARAM_INT);
			$req->bindParam(':nbr_of_comments', $data['nbr_of_comments'], PDO::PARAM_INT);
			$req->execute();
			$retour = $req->fetchAll();

			return ($retour);
		}

		/*
			Methode permettant d'avoir
			le nombre total de commentaires
			sur une photo d'une utilisateur
			@params id_photo (int)
			@return nbr(int)
		*/

		public function count_commentaire($id_photo)
		{
			$db = Model::getBdd();
			$data = [
				'id_photo' => $id_photo,
			];
			$req = $db->prepare("SELECT COUNT(id) FROM commentaires WHERE id_photo = :id_photo");
			$req->bindParam(':id_photo', $data["id_photo"], PDO::PARAM_INT);
			$req->execute();
			$retour = $req->fetch();

			return ($retour[0]);
		}

		/*
			Methode permettant d'avoir
			le nombre total de likes 
			sur une photo du studio
			@params none
			@return nbr (int)
		*/
		public function count_likes()
		{
			$db = Model::getBdd();
			$data = [
				'id_photo' => $this->_id,
			];
			$stmt = $db->prepare("SELECT likes FROM studio WHERE id = :id_photo");
			$stmt->execute($data);
			$result = $stmt->fetch();
			$result = $result[0];

			if(!empty($result))
			{
				$tab = explode(';', $result);
				$likes = (count($tab));
				return ($likes);
			}
			else
			{
				return (0);
			}
		}

		/*
			Methode permettant de vérifier
			si une photo est like par
			un utilisateur précisé en param pour
			une photo donneée
			@params user
			@return bool
		*/

		public function is_user_liked($user)
		{
			$this->_userManager = new UserManager(array('user' => $user));
			$id_user = $this->_userManager->get_id();
			$db = Model::getBdd();
			$data = [
				'id_photo' => $this->_id,
			];
			$stmt = $db->prepare("SELECT likes FROM studio WHERE id = :id_photo");
			$stmt->execute($data);
			$result = $stmt->fetch();
			$result = $result[0];
			if($result != NULL)
			{
				$tab = explode(';', $result);
				if(strstr($result, ';') == false)
				{
					$tab = explode(' ', $result);

				}
				if(!(empty($tab)))
				{
					foreach($tab as $value)
					{
						if($value == $id_user)
						{
							return (true);
						}
					}
					return (false);
				}
			}		
			return(false);
		}

		/*	
			Methode permettant d'ajouter
			un like a une photo existante
			les données sont formatés tel que:
			string = id_pseudo1;id_pseudo2;id_pseudo3...;
			@params none
			@return bool
		*/

		public function add_like_to_photo($user)
		{
			$this->_userManager = new UserManager(array('user' => $user));
			$id_user = $this->_userManager->get_id();
			$db = Model::getBdd();
			$data = [
				'id_photo' => $this->_id,
			];
			$stmt = $db->prepare("SELECT likes FROM studio WHERE id = :id_photo");
			$stmt->execute($data);
			$result = $stmt->fetch();
			$result = $result[0];
			$final_string = "";
			if($result != NULL)
			{
				$tab = explode(';', $result);
				if(!(empty($tab)))
				{
					foreach($tab as $value)
					{
						if($value == $id_user)
						{
							return (false);
						}
					}
					array_push($tab, $id_user);
					$final_string = implode(';', $tab);
					
					$data2 =[
						'id_photo' => $this->_id,
						'likes' => $final_string,
					];
					$stmt2 = $db->prepare("UPDATE studio SET likes = :likes WHERE id = :id_photo");
					$retour = $stmt2->execute($data2);
					return ($retour);
					
				}
			}
			$final_string = "$id_user";
			$data2 =[
				'id_photo' => $this->_id,
				'likes' => $final_string,
			];
			$stmt2 = $db->prepare("UPDATE studio SET likes = :likes WHERE id = :id_photo");
			$retour = $stmt2->execute($data2);
			return ($retour);

				
		}

		public function delete_like_to_photo($user)
		{
			$this->_userManager = new UserManager(array('user' => $user));
			$id_user = $this->_userManager->get_id();
			$db = Model::getBdd();
			$data = [
				'id_photo' => $this->_id,
			];
			$stmt = $db->prepare("SELECT likes FROM studio WHERE id = :id_photo");
			$stmt->execute($data);
			$result = $stmt->fetch();
			$result = $result[0];

			if(!empty($result))
			{
				$tab = explode(';', $result);
				if(!empty($tab))
				{

					foreach($tab as $key => $value)
					{
						if($id_user == $value)
						{
							unset($tab[$key]);
						}
					}
					$tab = array_values($tab);

					$final_string = implode(';', $tab);
					$data2 =[
						'id_photo' => $this->_id,
						'likes' => $final_string,
					];
					$stmt2 = $db->prepare("UPDATE studio SET likes = :likes WHERE id = :id_photo");
					$retour = $stmt2->execute($data2);
					return ($retour);
				}
			}
			return (0);
		}
	}

?>