<?php
/*
Christeddy Milapie
vérification.php
Crée le 24/03/15
Dernière modification le : 12/06/15 par Kean de Souza

Edité avec Notepad++ 10/01/15 - 17:20:21 ( Je suis Charlie Edition)

Objectif : Connexion au système

Kean de Souza - 29/05/15
Ajout d'une clause de tri pour rediriger les utilisateurs selon leur statut
Ajout des variables sessions contenant nom, prenom et id_util et id_cvvfr

*/

if (isset($_POST['auth']))
{
	
	if(!empty($_POST['pseudo']) && !empty($_POST['passe']))
		{
		$reqVerifUserLogPassByLOG->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
		$reqVerifUserLogPassByLOG->bindValue(':password', md5($_POST['passe']), PDO::PARAM_STR);
		$reqVerifUserLogPassByLOG->execute();
		$resul=$reqVerifUserLogPassByLOG->fetch(PDO::FETCH_ASSOC);
		}
	
	
	if(empty($resul))
		{
		echo '<p> Vous n\'êtes pas inscrit dans la base de données ou les données renseignées sont erronées... </p>';
		header('refresh: 3; URL=index.php?page=accueil');	
	}
	else {
		echo '<p> Bienvenue <mark><i>'.$resul["pseudo"].'</i></mark> Vous serez redirigé dans un instant... </p>';
		
		$_SESSION['id']=$resul['id_util'];
		$_SESSION['id_statut'] = $resul['id_statut'];
		$_SESSION['id_club']=$resul['id_club'];
		$_SESSION['pseudo']=$resul['pseudo'];
		$_SESSION['nom']=$resul['nom'];
		$_SESSION['prenom']=$resul['prenom'];
		$_SESSION['date_naissance']=$resul['date_naissance'];
		
		switch($_SESSION['id_statut'])
				{
				case 1 :	$_SESSION['page_defaut'] ='membre';
							header('Refresh: 1; URL=index.php?page=membre'); 
							break;
				
				case 2 : 	$_SESSION['page_defaut'] ='membre';
							header('Refresh: 1; URL=index.php?page=membre'); 
							break;
				
				case 3 : 	echo 'Gestion des désinscrit'; 
							$_SESSION['page_defaut'] ='membre'; 
							break;

				case 4 : 	$_SESSION['id_gestionnaire'] = $resul['id_util']; 
							$_SESSION['page_defaut'] ='gestionnaire';
							header('Refresh: 1; URL=index.php?page=gestionnaire'); 
							break;

				case 5 : 	$_SESSION['id_gestionnaire'] = $resul['id_util']; 
							$_SESSION['page_defaut'] ='administrateur';
							header('Refresh: 1; URL=index.php?page=administrateur'); 
							break;
				
				case 6 : 	$_SESSION['id_gestionnaire'] = $resul['id_util']; 
							$_SESSION['page_defaut'] ='kjazh42tgh41';
							header('Refresh: 1; URL=index.php?page=kjazh42tgh41'); 
							break;
				
				
				default : header('Refresh: 1; URL=index.php?page=membre');  break;
				}
		
	}	
}
else{	
header('refresh: 1; URLindex.php?page=accueil');
}
?>
