<?php

require_once('database.php');
$DSN = "mysql:host=" . $DB_HOST;
$bdd = new PDO($DSN, $DB_USER, $DB_PASSWORD);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "CREATE DATABASE ".$DB_NAME;
$result = $bdd->exec($sql);



$bdd = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql='CREATE TABLE `commentaires` (
	`id` int(255) NOT NULL,
	`id_photo` int(255) NOT NULL,
	`id_user` int(11) NOT NULL,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`commentaire` varchar(255) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
$result = $bdd->exec($sql);

$sql = 'CREATE TABLE `password_reset` (
	`id` int(11) UNSIGNED NOT NULL,
	`id_related` int(11) NOT NULL,
	`mail` varchar(255) DEFAULT NULL,
	`token` char(64) DEFAULT NULL,
	`expires` bigint(20) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
$result = $bdd->exec($sql); 
 
 $sql='CREATE TABLE `studio` (
	`id` int(10) NOT NULL,
	`id_owner` int(10) NOT NULL,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`file` longtext NOT NULL,
	`key_value` int(11) DEFAULT NULL,
	`saved` int(1) DEFAULT NULL,
	`likes` mediumtext
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
 $result = $bdd->exec($sql);
 
  
 $sql='CREATE TABLE `user` (
	`id` int(11) NOT NULL,
	`mail` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`user` varchar(255) NOT NULL,
	`passwd` varchar(255) NOT NULL,
	`bio` varchar(255) NOT NULL DEFAULT \'Biographie : vous pouvez la changer depuis votre profil !\',
	`notif_comment_mail` int(1) NOT NULL DEFAULT \'1\',
	`photo` longtext,
	`cle` varchar(32) DEFAULT NULL,
	`actif` int(11) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
  $result = $bdd->exec($sql);
  
 
  $sql='ALTER TABLE `commentaires` 
  ADD PRIMARY KEY (`id`)';
  $result = $bdd->exec($sql);
  
  
  $sql='ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`)';
  $result = $bdd->exec($sql); 
  
 
  $sql='ALTER TABLE `studio`
  ADD PRIMARY KEY (`id`)';
  $result = $bdd->exec($sql); 
  

  $sql = 'ALTER TABLE `user`
  ADD PRIMARY KEY (`id`)';
  $result = $bdd->exec($sql); 
  
  $sql='ALTER TABLE `commentaires`
	MODIFY `id` int(255) NOT NULL AUTO_INCREMENT';
  $result = $bdd->exec($sql); 
  
  $sql='ALTER TABLE `password_reset`
	MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT';
  $result = $bdd->exec($sql); 
  
  $sql='ALTER TABLE `studio`
	MODIFY `id` int(10) NOT NULL AUTO_INCREMENT';
  $result = $bdd->exec($sql); 

  $sql= 'ALTER TABLE `user`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';
  $result = $bdd->exec($sql); 
  
  $sql='COMMIT';
  $result = $bdd->exec($sql); 
  
	echo "Success installation of website".PHP_EOL;
?>