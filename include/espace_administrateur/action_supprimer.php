<?php

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
	echo ('<H1>Supprimer un membre</H1>');
		
	if($_GET['id']){
		// $reqinfo->bindValue(':id',$_GET["id"],PDO::PARAM_INT);
		$resultat1=$reqinfo->execute(array(':id'=>$_GET["id"]));
		$resultat1=$reqinfo->fetch(PDO::FETCH_ASSOC);
		$reqinfo->CloseCursor();
		echo('
				<h1> Membre  </h1>
				<form method="POST" action="#" enctype>
				<ul>
					<li>Nom: '.$resultat1["nom"].'</li>
					<li>Prenom:	'.$resultat1["prenom"].'  </li>
					<li>Age: '.Age($resultat1["date_naissance"]).' ans </li>
					<li>Adresse: 	'.$resultat1["adresse"].' </li>
					<li>Code Postal:	 '.$resultat1["cp"].'</li>
					<li>Ville:	'.$resultat1["ville"].'</li>
					<li>Mail:	'.$resultat1["mail"].'</li>
					<li>Sexe:	'.$resultat1["sexe"].'</li>
					<li>Numéro de licence | Date d\'obtention: 	'.$resultat1["num_licence"].' | '.dateUS2FR($resultat1["date_licence"]).' </li>
					<li>Date de la dernière visite médicale: '.dateUS2FR($resultat1["visit_med"]).' </li>
					<input type="submit" name="delete" value="suppression"/><BR>
					<a href="index.php?page=suppression_membre"><= Retourner au menu de suppresion</a>
				</form>
				');			
	}
	if(isset($_POST["delete"])){
		
			if($reqSuppresion->execute(array(':id'=>$_GET["id"]))){
			echo ('<p> Désormais ce membre n\'existe plus dans la base</p>');
			header('Refresh:2;url=index.php?page=suppression_membre');
			}
			else {echo '<p>Veuillez demander à un administrateur de supprimer toutes les informations concernant cette utilisateur dans la base';}
			
		}
	}
?>