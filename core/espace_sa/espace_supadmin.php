
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
echo (' 
	
	<h1>Espace Super Administrateur</h1>
			<ul>
				<li> <a href="index.php?page=ez4gz6gaf1eh"> Désinscrire les membres </a> </li>
				<li> <a href="index.php?page=creation_utilisateur"> Créer un membre</a> </li>
			</ul>
			<ul>
				<li> <a href="index.php?page=membre" onclick="window.open(this.href); return false;" >Acc&egrave;s &agrave; mon espace membre</a> </li>
				<li> <a href="index.php?page=gestionnaire" onclick="window.open(this.href); return false;" >Acc&egrave;s &agrave; mon espace gestionnaire</a> </li>
				<li> <a href="index.php?page=administrateur" onclick="window.open(this.href); return false;" >Acc&egrave;s &agrave; mon espace administrateur</a> </li>
			</ul>
			
	<script type="text/javascript">$(document).ready(function() { document.title = \'Espace Super Administrateur\';});</script>
	');
	}

?>