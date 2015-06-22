<?php
/*
Kean de Souza et Christeddy Milapie
15/06/2015
Objectif : 
	Espace SuperUtilisateur
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
		// Permet de modifier le statut de l'utilisateur
		$selectS=SelectStatut();
		echo ('
		<p> ID CLUB: <input type="text" name="club"/></p>
		<p> Pseudo : <input type="text" name="pseudo"/></p>
		<p> Mot de passe : <input type="password" name="password"/></p>
		<p>Statut:'.$selectS.'</p>
		<input type="submit" name="valider"/>
		');
	if (isset($_POST['valider'])){
		$reqNewUser1->bindValue(':club',$_POST['id_club'] ,PDO::PARAM_STR);
		$reqNewUser1->bindValue(':pseudo',$_POST['pseudo'] ,PDO::PARAM_STR);
		$reqNewUser1->bindValue(':password',$_POST['password'] ,PDO::PARAM_STR);
		$reqNewUser1->bindValue(':select_stat',$_POST['select_stat'] ,PDO::PARAM_STR);
		
		if(($reqNewUser1->execute())==TRUE){
			echo' nouveau membre';
		}
		
	}
	}	
?>