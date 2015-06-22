<?php
/*
Kean de Souza
Espace gestionnaire - fichier principal
index.php
Objectif : 
	- Menu pour les différentes fonctionnalité  
*/

if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_GESTION)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit n&eacute;cessaires pour consulter ce fichier. Vous serez redirig&eacute;.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_GESTION)
	{
	echo ('
		<h1>Espace gestionnaire</h1>
		<ul>
			<li> <a href="index.php?page=alerte">Notifications</a></li>
			<li> <a href="index.php?page=consultation_membre&amp;lien">Consultation</a> </li>
			<li> <a href="index.php?page=journal_des_mouvements">Journal Mouvements</a> </li>
			<li> <a href="index.php?page=import">Importation</a> </li>
			<li> <a href="index.php?page=planche_vol">Planche</a> </li>
			<li> <a href="index.php?page=operations_mouvements">Mouvements</a> </li>
			<li> <a href="index.php?page=membre" onclick="window.open(this.href); return false;"> Espace Membre </a> </li>
		</ul>
		<script type="text/javascript">$(document).ready(function() { document.title = \'Espace Gestionnaire\';});</script>
			');
	}

?>