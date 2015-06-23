<?php
/*
Kean de Souza et Christeddy Milapie
15/06/2015
Objectif : 
	- Créer un membre avec un pseudo et un statut
Modification le 23/06/15/06/2015
	- Erreur lors de l'execution SQL, celle-ci n'avait pas lieu.
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
		echo ('
		<form method="post" action="#">
			<h1> Création d\'un membre </h1>
			<p> ID CLUB: <input type="text" required="required" name="club"/></p>
			<p> Pseudo : <input type="text" required="required" name="pseudo"/></p>
			<p> Mot de passe : <input type="password" required="required" name="password"/></p>
			<p>Statut : '.$selectS.'</p>
			<input type="submit" name="valider"/>
		</form>
		<p><i><a href="index.php?page=kjazh42tgh41">Retour au menu</a></i></p> ');

		
	if (isset($_POST['valider'])){
		
		$bdd=ConnectBddGestionnaire();
		$req = $bdd->prepare('INSERT INTO compte_utilisateur (id_club, pseudo, password, id_statut) VALUES (:club, :pseudo, :password, :select_stat ) ');
		$req->bindValue(':club',$_POST['club'] ,PDO::PARAM_INT);
		$req->bindValue(':pseudo',$_POST['pseudo'] ,PDO::PARAM_STR);
		$req->bindValue(':password',md5($_POST['password']),PDO::PARAM_STR);
		$req->bindValue(':select_stat',$_POST['select_stat'] ,PDO::PARAM_STR);
		$req->execute();
		
		if( $req == TRUE ){echo 'Le nouveau membre a été crée ';	 return TRUE ; } else { echo 'Erreur ';	 return FALSE; }
	
		}
	}

?>