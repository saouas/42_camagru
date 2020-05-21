<?php

class ControllerMontage extends Model
{
	private $_photoManager;
	private $_view;

	public function __construct($url)
	{
		if(isset($url) && @count($url) > 1)
			{
				throw new Exception('Page introuvable 404 NOT FOUND');
			}
			else
			{
				$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				if (parse_url($actual_link, PHP_URL_QUERY) != null)
				{
					$ajax = parse_url($actual_link)["query"];
					$ret = explode('=',$ajax);
					$user = $ret[2];
					$function = explode('&', $ret[1])[0];
					if($function == "delete")
					{
						$parse = explode('&', $ajax);
						$photo = explode('=', $parse[1])[1];
						$user = explode('=', $parse[2])[1];
						if($user)
						{
							self::delete_photo($photo, $user);
						}
					}
					if($function == "save")
					{
						$parse = explode('&', $ajax);
						$photo = explode('=', $parse[1])[1];
						$user = explode('=', $parse[2])[1];

						self::save_photo($photo, $user);
					}
					if($function == "g3li")
					{
						self::get_3_last_image($user);
					}
					if($function == "merge")
					{
						$user = explode('&',$user)[0];
						$filter = $ret[3];
						if($filter == "undefined")
						{
							self::valid_tmp_img($user, 1);
						}

						else
						{
							$last_user_img = self::last_image($user);
							if(!(empty($last_user_img)))
							{
								$filtre_img = self::convert_img_to_base64($filter);
								self::fusion_image($filtre_img, $last_user_img, $user);
							}
							
						}
					}
				}
				else
				{
					$this->print_montage();
				}
			}
	}

	public function print_montage()
	{
		require_once('views/viewMontage.php');

		$this->_view = new View('Montage');
		$this->_view->generate(array(''));
	}

	public function valid_tmp_img($user, $key)
	{
		$this->_photoManager = new PhotoManager(array(''));
		$retour = $this->_photoManager->change_key_value_img($user, $key);

		return ($retour);
	}

	public function save_image($data, $user, $key)
	{
		$this->_photoManager = new PhotoManager(array('file' => $data));
		$retour = $this->_photoManager->upload_image($data, $user, $key);
		if($retour == false)
		{
			return (false);
		}
		else
		{
			return (true);
		}

	}

	public function last_image($user)
	{
		$this->_photoManager = new PhotoManager(array(''));
		$retour = $this->_photoManager->get_last_image($user);


		return ($retour[3]);
	
	}

	public function total_img_user($user)
	{
		$this->_photoManager = new PhotoManager(array(''));
		$retour = $this->_photoManager->count_total_img($user);

		return $retour;
	}
	
	public function get_3_last_image($user)
	{
		$this->_photoManager = new PhotoManager(array(''));
		$retour = $this->_photoManager->get_three_last_image($user);

		$nb_img  = self::total_img_user($user);
		if($nb_img > 3)
			$nb_img = 3;
		$i = 0;
		$tab = array();
		while($i != $nb_img)
		{
			$tab[$i] = $retour[$i][0];
			$i++;
		}
		echo json_encode($tab, JSON_FORCE_OBJECT);
	}

	public function convert_img_to_base64($filtre)
	{

		if ($filtre == 1)
		{
			$url = URL . 'views/images/filter_1.png';
			$image = file_get_contents($url);
			if($image !== false)
			{
				return 'data:image/jpg;base64,'.base64_encode($image);
			}
		}

		else if($filtre == 2)
		{
			$url = URL . 'views/images/filter_2.png';
			$image = file_get_contents($url);
			if($image !== false)
			{
				return 'data:image/jpg;base64,'.base64_encode($image);
			}
		}

		else if($filtre == 3)
		{
			$url = URL . 'views/images/filter_3.png';
			$image = file_get_contents($url);
			if($image !== false)
			{
				return 'data:image/jpg;base64,'.base64_encode($image);
			}	
		}

		else if($filtre == 4)
		{
			$url = URL . 'views/images/filter_4.png';
			$image = file_get_contents($url);
			if($image !== false)
			{
				return 'data:image/jpg;base64,'.base64_encode($image);
			}	
		}

		else if($filtre == 5)
		{
			$url = URL . 'views/images/filter_5.png';
			$image = file_get_contents($url);
			if($image !== false)
			{
				return 'data:image/jpg;base64,'.base64_encode($image);
			}	
		}

		else if($filtre == 6)
		{
			$url = URL . 'views/images/filter_6.png';
			$image = file_get_contents($url);
			if($image !== false)
			{
				return 'data:image/jpg;base64,'.base64_encode($image);
			}	
		}

		else if($filtre == 7)
		{
			$url = URL . 'views/images/filter_7.png';
			$image = file_get_contents($url);
			if($image !== false)
			{
				return 'data:image/jpg;base64,'.base64_encode($image);
			}	
		}

		else if($filtre == 8)
		{
			$url = URL . 'views/images/filter_8.png';
			$image = file_get_contents($url);
			if($image !== false)
			{
				return 'data:image/jpg;base64,'.base64_encode($image);
			}	
		}
		else
		{
			return (-1);
		}
	}
	public function fusion_image($filtre_img, $user_img, $user)
	{
		if(!empty($_SESSION["loggued_on"]))
		{
			$user = $_SESSION["loggued_on"];
		}
		$treat_filtre_img = explode(',', $filtre_img);
		$filtre_img = $treat_filtre_img[1];
		$image = base64_decode($filtre_img);

		
		$source = imagecreatefromstring($image);
		imagealphablending($source, true);
		$largeur_source = 420;
		$hauteur_source = 420;

	
		
		$treat_user_img = explode(',', $user_img);
		$user_img = $treat_user_img[1];
		$image2 = base64_decode($user_img);
		$destination = imagecreatefromstring($image2);
		$largeur_destination = imagesx($destination);
		$hauteur_destination = imagesy($destination);
		// Calcul des coordonnées pour placer l'image source dans l'image de destination
		$destination_x = 170;//($largeur_destination - $largeur_source)/2;
		$destination_y = 0;//($hauteur_destination - $hauteur_source)/2;

		// On place l'image source dans l'image de destination
		imagecopy($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source);
		//imagecopy($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source);
		
		// On affiche l'image de destination
		ob_start();
		imagepng($destination);
		$contents = ob_get_clean();
		ob_end_clean();
		$data = "data:image/png;base64," . base64_encode($contents);

		imagedestroy($source);
		imagedestroy($destination);
		self::save_image($data , $user, 1);
		//echo json_encode($data, JSON_FORCE_OBJECT);
 
	}

	public function upload_image($user)
	{
		$errors=array();
		$allowed_ext= array('jpg','jpeg','png');
		$file_name = $_SESSION["img_name"];
		$tmp = explode('.', $file_name);
		$file_ext = strtolower(end($tmp));
	
	
		$file_size= $_SESSION["img_size"];
		$file_tmp= $_SESSION["img_tmp_name"];
	
		$type = pathinfo($file_tmp, PATHINFO_EXTENSION);
		$data = file_get_contents($file_tmp);
		
	
	
		if(in_array($file_ext,$allowed_ext) === false)
		{
			$errors[]='Extension not allowed';
		}
	
		if($file_size > 2097152)
		{
			$errors[]= 'File size must be under 2mb';
	
		}
		if(empty($errors))
		{
			$base64 = 'data:image/' . 'png' . ';base64,' . base64_encode($data);
			if(self::save_image($base64, $user, 0) == true)
			{
				$_SESSION["upload_file"] ="1";
			}
			
		}
		else
		{
			$_SESSION["err_format"] = "1";
		}
	   //  print_r($errors);*/
	}

	public function delete_photo($photo, $user)
	{
		$this->_photoManager = new PhotoManager(array(''));
		$retour = $this->_photoManager->delete_photo_bdd($photo, $user);

		if($retour == false)
		{
			$_SESSION["err_db"] = "1";
		}
	}

	public function save_photo($photo, $user)
	{
		session_start();
		$this->_photoManager = new PhotoManager(array(''));
		$retour = $this->_photoManager->save_photo_bdd($photo, $user);

		if($retour == false)
		{
			$_SESSION["err_db"] = "1";
		}
		
	}
}
?>