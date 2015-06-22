<?php
/*
Kean de Souza
Espace administrateur- fichier principal
Objectif : 
	- Menu pour les différentes fonctionnalité  
*/

if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_ADMINISTRATION)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit nécessaires pour consulter ce fichier. Vous serez redirigé.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_ADMINISTRATION)
	{
	echo ('
	<script type="text/javascript">$(document).ready(function() { document.title = \'Espace Administrateur\';});</script>
	<h1>Espace Administrateur</h1>
	<ul>
		<li> <a href="index.php?page=gestion_tarif">Gestion des tarifs</a> </li>
		<li> <a href="index.php?page=gestion_forfait">Gestion des formules</a> </li>
		<li> <a href="index.php?page=journal_operation">Journal des opérations</a> </li> 
		<li> <a href="index.php?page=suppression_membre">Suppression des membres</a> </li>
	</ul>
	<ul>
		<li> <a href="index.php?page=membre" onclick="window.open(this.href); return false;" ><strong>Mon espace membre</strong></a> </li>
		<li> <a href="index.php?page=gestionnaire" onclick="window.open(this.href); return false;" >Acc&egrave;s espace gestionnaire</a> </li>
	</ul>');
	}
else
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');	
	}
?>