<?php
/*
Kean de Souza et Christeddy Milapie
15/06/2015
Objectif : 
	Espace SuperUtilisateur : Mise à jour d'un membre
*/
if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_SA)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit nécessaires pour consulter ce fichier. Vous serez redirigé.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_SA)
	{

	$selectS=SelectStatut();
	$membres = SelectUsersStatut();
	echo ('
	<h1> Promotion d\'un membre </h1>
	<form method="post" action="#">
	<p> Membre : '.$membres.' </p>
	<p> Statut:'.$selectS.'</p>
	<input type="submit" name="valider" value="Valider la promotion" /> </form>
	<p><a href="index.php?page=menu"> Retourner au menu</a></p>');

		
	if (isset($_POST['valider'])){
		print_r($_POST); 
		$succes=UpdateStatutUser($_POST['select_user'],$_POST['select_stat']);
		if ($succes) $msg='Mise à jour du statut du membre effectué' ;
			else $msg ='Une erreur a été rencontré, veuillez réessayer!';
		MessageAlert($msg);
		header('refresh: 0; URL=index.php?page=promotion_utilisateur');
		}
	}	
?>