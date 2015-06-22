<?php
/*
Kean de Souza et Christeddy Milapie
15/06/2015
Objectif : 
	Espace Super Administrateur
		- Bouton de désincription pour tous les membres sauf le Super Administrateur
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
	echo ('<p> Désinscription de tous les utilisateurs de niveaux inférieurs </p>
	 <form method="post" action="#"> <input type="submit" name="desincription" value="Désinscription de tous les utilisateurs"/> </form> ');
	
	echo ('<p><i><a href="index.php?page=kjazh42tgh41">Retour au menu</a></i></p> ');
	if (isset ($_POST["desincription"])) 
		{
		$reqdesincript->exec('UPDATE compte_utilisateur SET id_statut = 3');	
		echo ('La désinscription de tous les utilsateurs s\'est bien éxécuté');	
		}
	
		
	}

?>