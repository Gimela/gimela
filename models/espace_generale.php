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
elseif($_SESSION['id_statut'] < STATUT_ACCES_MEMBRE)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit n&eacute;cessaires pour consulter ce fichier. Vous serez redirig&eacute;.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_MEMBRE)
	{
	echo ('
	
		<h1>Menu générale</h1>

				<p> Membre </p>
				<ul id="espace_membre">
					<li> <a href="index.php?page=membre">Mon compte pilote </a> </li>
				</ul>
				
				<p> Gestionnaire </p>
				<ul id="espace_gestionnaire">
					<li> <a href="index.php?page=alerte"> Notifications nouveau inscrit ou modification</a></li>
					<li> <a href="index.php?page=consultation_membre&amp;lien"> Consulter un compte pilote </a> </li>
					<li> <a href="index.php?page=journal_des_mouvements"> Consulter le journal des mouvements </a> </li>
					<li> <a href="index.php?page=operations_mouvements">Créer, modifier ou supprimer un mouvement </a> </li>
					<li> <a href="index.php?page=import"> Importer une planche de vol </a> </li>
					<li> <a href="index.php?page=planche_vol"> Consulter une planche de vol </a> </li>
				</ul>
				
				<p> Adminstrateur </p>
				<ul id="espace_administrateur">
					<li> <a href="index.php?page=gestion_tarif">Gestion des tarifs</a> </li>
					<li> <a href="index.php?page=gestion_forfait">Gestion des formules</a> </li>
					<li> <a href="index.php?page=journal_operation">Consulter le journal des opérations</a> </li> 
					<li> <a href="index.php?page=suppression_membre">Suppression des membres</a> </li>
				</ul>
				
				<p> Super Administrateur</p>
				<ul id="espace_sup_admin">
						<li> <a href="index.php?page=ez4gz6gaf1eh"> Désinscrire les membres </a> </li>
						<li> <a href="index.php?page=creation_utilisateur"> Créer un membre</a> </li>
				</ul>
			');
	}

?>