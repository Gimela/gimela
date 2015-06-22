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

	echo (' <form method="post" action="#"> <input type="submit" name="desincription" value="desincription"/> </form> '):
	
	if (isset ($_POST["desincription"])) $reqdesincript->execute();
		
	}

?>