<?php
/*
Kean de Souza
Tâches automatisées - CRON
17/06/15
Permet de mettre à jour le système périodiquement ou selon des heures, jours et dates spécifié
*/


	  
$bdd=ConnectBdd();


// Requête permettant d'indexer chaque table pour une recherche plus rapide lors des requetes SELECT
$req1 = $bdd-> exec('OPTIMIZE TABLE vol');
$req1 = $bdd-> exec('OPTIMIZE TABLE mouvement');
$req1 = $bdd-> exec('OPTIMIZE TABLE operation');
$req1 = $bdd-> exec('OPTIMIZE TABLE compte_utilisateur');
$req1 = $bdd-> exec('OPTIMIZE TABLE aeronefs');
$req1 = $bdd-> exec('OPTIMIZE TABLE config_forfait');

?>

<?php

function ConnectBdd()
	  {
	//Informations de connextion à la base de donnée
	$dsn = 'mysql:dbname=aeroclub;host=127.0.0.1';
	$user = 'aeroclub';
	$password = 'aeroclub';
	try {
		$bdd = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_LOCAL_INFILE=>true));
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$bdd->exec("SET CHARACTER SET utf8");
		} 
		
	catch (PDOException $e) 
		{
		echo 'Échec lors de la connexion : ' . $e->getMessage();
		}
	return $bdd;
	  };

?>