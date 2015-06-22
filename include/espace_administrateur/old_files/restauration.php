<?php
/*
Christeddy Milapie
*/

session_start();

require("../cfg/functions.php"); // Appel des fonctions
require("../cfg/requetesPDO.php"); // Requetes prépare SQL

?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />		
		<?php include "../cfg/entete.php"; ?>
		<title> Restauration de membre</title>
	</head>
	<body>
	<H1>Restaurer un membre</H1>
<form method="post" action="#" enctype="multipart/form-data">
     <label for="mon_fichier">Fichier (format: .csv| max. 1 Ko) :</label><br />
     <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
     <input type="file" name="mon_fichier" id="mon_fichier" /><br />
	 <input type="submit" name="submit" value="Envoyer" /><br />
	 <a href="../espace_administrateur_sysv.php">Retourner au menu</a>
</form>

<?php
if(isset($_FILES['mon_fichier']))
{ 

$dossier ='membres_supprimer/';
$fichier=$_FILES['mon_fichier']['name']; 

$extensionsAuth = array(".csv", ".html"); // tableau des extensions autorisées
$taille_maxi = 100000; // Taille maximal du fichier
global $erreur; // Variable d'errer globale

	$extension = strrchr($_FILES['mon_fichier']['name'],'.');
	if(!in_array($extension, $extensionsAuth)) //Si l'extension n'est pas dans le tableau
		{
		$erreur = 'Vous devez uploader un fichier de type csv ou html';
		}
	$taille = filesize($_FILES['mon_fichier']['tmp_name']);
	if($taille>$taille_maxi)
		{
		$erreur = 'Le fichier est trop gros...';
		}
		
	if(!isset($erreur))
		{
		echo ' cette personne a été ajouter dans la base';
		}
		else //Sinon (la fonction renvoie FALSE).
		{
		  echo 'Echec de l\'upload !';
		}	
			
$fichier1 = $dossier.$fichier; 

$reqchargement=$bdd->prepare("LOAD DATA LOCAL INFILE '".$fichier1."'
        INTO TABLE compte_utilisateur
		FIELDS
			TERMINATED BY ';'
			ENCLOSED BY '\\\"'
			ESCAPED BY '\\\\'
		LINES
			STARTING BY ''
			TERMINATED BY '\\n'
			(id_util, id_club, id_Fffvv,date_inscription, nom, prenom, pseudo, profession, sexe, date_naissance, adresse, cp, ville, tel_fixe, tel_mobile, mail, password, date_licence, num_licence, visit_med, heure_forfait, p_instructeur, p_remorqeure, p_eleve, id_statut )
			");
$reqchargement->execute();
}
 
?>