<?php
	require_once('views/View.php');
    class ControllerAccueil
    {
		private $_userManager;
		private $_photoManager;
		private $_view;
		public $nb_pics_page;

        public function __construct($url)
		{	if(!empty($_POST["myData"]))
			{
				$this->treat_comment($_POST["myData"]);
			}
			else if(!empty($_POST["myData2"]))
			{
				$this->update_commentaires($_POST["myData2"]);
			}	
			else if(!empty($_POST["myData3"]))
			{
				$this->update_last_commentaires($_POST["myData3"]);
			}
			else if(!empty($_POST["myData4"]))
			{
				$this->add_like_photo($_POST["myData4"]);
			}
			else if(!(empty($_POST["myData5"])))
			{
				$this->delete_like_photo($_POST["myData5"]);
			}
			else if(!(empty($_POST["myData6"])))
			{
				$this->update_like_photo($_POST["myData6"]);
			}
			else if(isset($url) && @count($url) > 1)
			{
				throw new Exception('Page introuvable 404 not found');
			}
			else
			{
				$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				if(parse_url($actual_link, PHP_URL_QUERY) != null)
				{
					$request = parse_url($actual_link)["query"];
					$page = explode('=', $request)[1];
					$page++;
					$this->print_users();
					$this->print_img($page);
					$_SESSION["page_courante"] = $page;
					return ;
				}	
				$this->print_users();
				$this->print_img('1');
				$_SESSION["page_courante"] = 1;
			}
        }

        public function print_users()
        {
            $this->_userManager = new UserManager(array(''));

			require_once('views/viewAccueil.php');
			
			$this->_view = new View('Accueil');
			$this->_view->generate(array(''));
		}
		
		public function count_total_img()
		{
			$this->_photoManager = new PhotoManager(array(''));
			$total_img = $this->_photoManager->nb_total_saved_photos_home();
			
			return ($total_img);
		}

		public function count_element_tab($array2)
		{
			$count = 0;
			foreach($array2 as $photos)
			{
				$count = $count + count($array2);
			}

			return ($count);
		}
		public function temps_ecoule($date,$type) {
			if($type == "timestamp") {
				$date2 = $date; // depuis cette date
			} elseif($type == "date") {
				$date2 = strtotime($date); // depuis cette date
			} else {
				return "Non reconnu";
			}
			$Ecart = time()-$date2;
			$Annees = date('Y',$Ecart)-1970;
			$Mois = date('m',$Ecart)-1;
			$Jours = date('d',$Ecart)-1;
			$Heures = date('H',$Ecart)-1;
			$Minutes = date('i',$Ecart);
			$Secondes = date('s',$Ecart);
			if($Annees > 0) {
				return "Il y a ".$Annees." an".($Annees>1?"s":"")." et ".$Jours." jour".($Jours>1?"s":""); // on indique les jours avec les année pour être un peu plus précis
			}
			if($Mois > 0) {
				return "Il y a ".$Mois." mois et ".$Jours." jour".($Jours>1?"s":""); // on indique les jours aussi
			}
			if($Jours > 0) {
				return "Il y a ".$Jours." jour".($Jours>1?"s":"");
			}
			if($Heures > 0) {
				return "Il y a ".$Heures." heure".($Heures>1?"s":"");
			}
			if($Minutes > 0) {
				return "Il y a ".$Minutes." minute".($Minutes>1?"s":"");
			}
			if($Secondes > 0) {
				return "Il y a ".$Secondes." seconde".($Secondes>1?"s":"");
			}
		}

		public function print_pagination($nb_page)
		{
			$i = 0;
			echo "<div class='pagination_home'>
			<nav aria-label='Page navigation example'>
				<ul class='pagination'>
					";
					while($i != $nb_page)
					{
						echo "<li class='page-item'><a class='page-link' href='Accueil?p=$i'>$i</a></li>";
						$i++;
					}
					
			echo"	</ul>
			</nav>
		</div>
	</div>";
		}

		public function get_id_photo($pos_id_photo, $curr_page)
		{
			$this->_photoManager = new PhotoManager(array(''));
			$total_img = $this->count_total_img();
			$total_pages = $total_img / 6;
			$coeff = 6;
			$curr_page_tmp = $curr_page;
			if($curr_page <= 0)
			{
				$curr_page = 1;
				$curr_page_tmp  = 0;
				$coeff = 1;
			}
			$pos_photo_tmp = $curr_page - 1*$curr_page + $pos_id_photo;
			
			$total_photos = $this->_photoManager->select_all_photos_home($curr_page_tmp*$coeff, 6);
			$myPhoto = $total_photos[$pos_photo_tmp]["id"];

			return ($myPhoto);

		}

		public function treat_comment($data)
		{
			$data = json_decode($data);
			$pos_id_photo = $data->id_photo;
			$page = $data->curr_page;
			$user = $data->user;
			$commentaire = filter_var(strip_tags($data->commentaire), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$id_photo = $this->get_id_photo($pos_id_photo, $page);
			if($commentaire != "")
			{
				if(is_numeric($page) && is_numeric($id_photo))
				{
					if($this->_photoManager->insert_commentaire($id_photo, $user, $commentaire))
					{
						$x = ($this->_photoManager->sendmail_if_notifcomment($id_photo));
						if($x == true)
						{
							$user_owner_photo = $this->_photoManager->get_user_of_img($id_photo);
							$this->_userManager = new UserManager(array('user' => $user_owner_photo));
							$this->_photoManager->set_id($this->_userManager->get_id_related());
							$mail = $this->_userManager->get_mail()[0];
								$sujet = "Camagru :: Nouveau commentaire sur votre photo";
								$entete = "From: noreply@camagru.saouas";
								$entete='Content-Type: text/html; charset="UTF-8"'."\n";
								$entete.='Content-Transfer-Encoding: 8bit';;
								$message = 'Salut '.$user_owner_photo. ',
							
								Votre photo que vous avez poster sur Camagru , vient de se voir recevoir
								un nouveau commentaire de la part de de '.$user .':<br><br>
								
								" '.$commentaire .' "
	
								<br><br>Si vous voulez desactivez cette option de notifications par mail ,
								rendez vous dans votre espace editez profil.
	
								<br>	A plus tard !
	
								
							<br>---------------
								<br>Ceci est un mail automatique, Merci de ne pas y repondre.';
								mail($mail, $sujet,$message, $entete);
						}
						
						echo "Votre commentaire a bien été posté :)";
					}
					else
					{
						echo "Erreur du server , contactez un administrateur.";
						
					}
				}
				else
				{
					echo "Erreur vos données ne sont pas correcte, contactez un administrateur.";
				}
			}
			else
			{
				echo "Erreur un commentaire ne peux etre vide ";
			}			
		}

		public function print_commentaires($pos_photo, $curr_page, $user)
		{
			$this->_userManager = new UserManager(array(''));
			$id_photo = $this->get_id_photo($pos_photo, $curr_page);
			$nb_commentaires = $this->_photoManager->count_commentaire($id_photo);
			$commentaire_per_page = 4;
			$commentaires_pages = $nb_commentaires/$commentaire_per_page;
			$tab_comments = $this->_photoManager->get_commentaire($id_photo, 0,$commentaire_per_page);
			$tab = "";	
			if($nb_commentaires >= 0)
			{
				if($nb_commentaires > 4)
					$nb_commentaires = 4;
				 for($i = 0 ; $i < $nb_commentaires ; $i++)
				 {
					$pseudo = $this->_userManager->get_user($tab_comments[$i]['id_user']);
					$commentaire = $tab_comments[$i]['commentaire'];
					$this->_userManager = new UserManager(array('user' => $pseudo));
					$profile_pics = $this->_userManager->get_profile_picture()[0];


				
				$tab .= "<div class='comment' id = 'commentaire$pos_photo$i' style='text-align:left'>
									<img style='height:30px;width:30px' <img class='profile_picture' src=$profile_pics>
									<p class='pseudo_commentaire'>$pseudo</p>
									<p class='commentaire'>$commentaire</p>
								</div>";

				}

				$isave =$i;
				while($isave < 4)
				{
					$tab .= "<div class='comment' id = 'commentaire$pos_photo$isave' style='text-align:left;display:none'>
					<img style='height:30px;width:30px' <img class='profile_picture' src=>
					<p class='pseudo_commentaire'></p>
					<p class='commentaire'></p>
				</div>";	
					$isave++;
				}
				

			}

			return ($tab);
		}

		public function update_last_commentaires($data)
		{
			$this->_userManager = new UserManager(array(''));
			$data = json_decode($data);
			$pos_photo = $data->pos_photo;
			$curr_page = $data->curr_page;
			$user = $data->user;

			$commentaire_per_page = 4;
			$depart = 0;
			$id_photo = $this->get_id_photo($pos_photo, $curr_page);

			$tab_comments = $this->_photoManager->get_commentaire($id_photo, $depart, $commentaire_per_page);
			$i = 0;
			$len = count($tab_comments);
			while($i < $len)
			{
				$user = $this->_userManager->get_user($tab_comments[$i]['id_user']);
				$this->_userManager = new UserManager(array('user' => $user));
				$tab_comments[$i]['id_photo'] = $this->_userManager->get_profile_picture()[0];
				$tab_comments[$i]['id_user'] = $this->_userManager->get_user($tab_comments[$i]['id_user']);
				$i++;
			}
			array_push($tab_comments, $pos_photo);
			echo json_encode($tab_comments);
		}

		public function update_commentaires($data)
		{
			$this->_userManager = new UserManager(array(''));
			$data = json_decode($data);
			$pos_photo = $data->pos_photo;
			$curr_page = $data->curr_page;
			$count = $data->count;
			$user = $data->user;

			$commentaire_per_page = 4;
			$depart = $count*$commentaire_per_page;
			$id_photo = $this->get_id_photo($pos_photo, $curr_page);

			$tab_comments = $this->_photoManager->get_commentaire($id_photo, $depart, $commentaire_per_page);
			$i = 0;
			$len = count($tab_comments);
			while($i < $len)
			{
				$user = $this->_userManager->get_user($tab_comments[$i]['id_user']);
				$this->_userManager = new UserManager(array('user' => $user));
				$tab_comments[$i]['id_photo'] = $this->_userManager->get_profile_picture()[0];
				$tab_comments[$i]['id_user'] = $this->_userManager->get_user($tab_comments[$i]['id_user']);
				$i++;
			}
			array_push($tab_comments, $pos_photo);
			echo json_encode($tab_comments);
			}

		public function calcul_nb_likes($id_photo)
		{
			$likes = 0;
			$this->_photoManager = new PhotoManager(array('id' => $id_photo));
			$likes = $this->_photoManager->count_likes();

			return ($likes);
		}

		public function test_user_liked($user, $id_photo)
		{
			$this->_photoManager = new PhotoManager(array('id' => $id_photo));
			$retour = $this->_photoManager->is_user_liked($user);

			return ($retour);
		}

		public function add_like_photo($data)
		{
			session_start();
			$tab = json_decode($data);
			if(!empty($_SESSION["loggued_on"]))
			{
				$user = $_SESSION["loggued_on"];
			}
			$pos = $tab->pos_photo;
			$curr_page = $tab->curr_page;

			$id_photo = $this->get_id_photo($pos, $curr_page);
			$this->_photoManager = new PhotoManager(array('id' => $id_photo));
			$retour = $this->_photoManager->add_like_to_photo($user);			

			return ($retour);
		}

		public function delete_like_photo($data)
		{
			session_start();
			$tab = json_decode($data);
			if(!empty($_SESSION["loggued_on"]))
			{
				$user = $_SESSION["loggued_on"];
			}
			$pos = $tab->pos_photo;
			$curr_page = $tab->curr_page;


			$id_photo = $this->get_id_photo($pos, $curr_page);
			$this->_photoManager = new PhotoManager(array('id' => $id_photo));
			$retour = $this->_photoManager->delete_like_to_photo($user);

			return ($retour);
		}

		public function update_like_photo($data)
		{
			session_start();
			$tab = json_decode($data);
			if(!empty($_SESSION["loggued_on"]))
			{
				$user = $_SESSION["loggued_on"];
			}
			$pos = $tab->pos_photo;
			$curr_page = $tab->curr_page;
			$id_photo = $this->get_id_photo($pos, $curr_page);

			$this->_photoManager = new PhotoManager(array('id' => $id_photo));
			$nb_likes = $this->_photoManager->count_likes();
			echo ($nb_likes);
		}

		public function print_img($page)
		{
		$this->_photoManager = new PhotoManager(array(''));
		$total_img = $this->count_total_img();
		$image_per_page = 6;
		$page_courante = 1;
		if($page != 1)
			$page_courante = $page;
		$nb_page = ceil($total_img / $image_per_page);
		$depart = ($page_courante - 1) * $image_per_page;
		$photos_tab = $this->_photoManager->select_all_photos_home($depart, $image_per_page);

		$likes_img = URL . 'views/images/like.png';
		$comments_img = URL . 'views/images/comments_home.png';
		$nb_real_img = (count($photos_tab));
		$i = 0;
		if($nb_real_img >= 1)
		{
			while ($i != $nb_real_img)
			{
				$liked = false;
				$likes_img = URL . 'views/images/like.png';
				$img = $photos_tab[$i]['file'];
				$date = $photos_tab[$i]['date'];
				$date = $this->temps_ecoule($date, "date");
				$user = $this->_photoManager->get_user_of_img($photos_tab[$i]['id']);
				$photo = $this->_photoManager->get_profilepics_of_img($photos_tab[$i]['id']);
				$this->nb_pics_page = 5;
				$id_photo = ($photos_tab[$i]['id']);
				$nb_likes = $this->calcul_nb_likes($id_photo);
				if(!empty($_SESSION["loggued_on"]))
				{
					$liked = $this->test_user_liked($_SESSION["loggued_on"], $id_photo);
				}
				if($liked == true)
					$likes_img = URL . 'views/images/like_plein.png';
				/* IMG MAIN PART */
				echo "<div class='container_photo_home'>
				<div class='top_home_pics'>
					<img src=$photo class='profile_picture' >
					<p>$user</p>
				</div>
				<a href='' id='myBtn$i'><img src=$img></a>
				<div class='icon_home_pics'>
					<a href='' id='comments_home$i' ><img id='comments_home' src=$comments_img></a>
					<a href='' id='like_home$i'><img id='like_home' src=$likes_img></a>
					<p>$date</p>
					<p class='likes' id='likes$i'>$nb_likes likes</p>
				</div>
				<hr>
				<div class='comment_home'>
					<form id='form$i' method='POST'>
						<input class='comment_textfield' type='text' id='text$i' placeholder='Ajoute un commentaire ..' value=''>
						<input type='submit' hidden='true'>
					</form>
				</div>
				</div>";
				
				/* MODAL PART */
	echo"<div id='myModal$i' class='modal'>
				<div class='modal-content-box'>
				<span id = 'close$i' class='close'>&times;</span>
				<div class='left_box'>
					<img src=$img>
				</div>
				<div class='right_box'>
					<img id='img_photo' class='profile_picture' src=$photo>
					<p style='text-align:left'>$user</p>

					<div class='comments_box'>" 
					.$x = $this->print_commentaires($i, $depart/6, $user);
				
					if($x != "")
					{
						echo "<br><br><a href='' id='comment$i'><p>Regardez d'autres commentaires ..</p></a>";
					}
					// else
					// {
					// 	echo "Ouuups. Aucun commentaire pour le moment :/";
					// } 

					echo"
					</div>
					<div class='box_interaction'>
							<a href='' id='like_home_modal$i'><img id='like_home' src=$likes_img></a>
							<br><br>
							<p class='likes' id='likes_modal$i'>$nb_likes likes</p>
							<form id='form-modal$i' method='POST'>
								<input class='comment_textfield' type ='text' id='text-modal$i' placeholder='Ajoute un commentaire ..' value=''>
								<input type='submit' hidden='true'>
							</form>

						</div>
				</div>
			</div>
			</div>";
			$this->print_commentaires($i, $depart/6, $user);
			$i++;

			}
			$this->print_pagination($nb_page);
		}
		else
		{
			echo "no image in db";
		}
	}

    }

?>