<?php
/*
Christeddy Milapie et Khalil Tabiti
Permet de lister les membres non iscrit
Afficher les informations remplies

Modifié par Kean de Souza le 12/06/15
*/

if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_GESTION)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit nécessaires pour consulter ce fichier. Vous serez redirigé.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_GESTION)
	{
	$res = RechercheMembreAValider();

	if (empty($res))
		{
		$tabCompte='<p>Aucune demande d\'accès pour le moment</p>';	
		}
	else
		{
		$tabCompte='<table border="1" style="font-size: 18px; font-size:16px;" align="center"> <tr><th>ID</th><th>Nom</th><th>Prenom</th><th>Date d\'inscription</th></tr>';
		foreach($res as $row)
			{
			$tabCompte.='<tr><td> <a href="index.php?page=membre&amp;maj&amp;id='.$row['id_util'].'" onclick="window.open(this.href); return false;" > '.$row['id_util'].' </a> </td><td>'.$row['nom'].'</td><td>'.$row['prenom'].'</td><td>'.dateUS2FR($row['date_inscription']).' - '.$row['heure_inscription'].'</td></tr>';	
			}
		$tabCompte.='</table>';
		}
	
	$tabinfos = RechercheInformationAValider();
	
	if(empty($tabinfos))
		{
		$listeinfos = '<p>Aucune demande de vérification pour le moment</p>';
		}
		
	else
		{
		$listeinfos ='<table border="1" align="center"  style="font-size: 16px;">
			<tr>
				<th>ID</th>
				<th>Nom</th>
				<th>Prenom</th>
				<th>Date de demande de modification</th>
				<th>Champs à modifer</th>
				<th>Valeur actuelle</th>
				<th>Nouvelle valeur</th>
				<th>Valider</th>
			</tr>';
			
		foreach ($tabinfos as $champs)
			{
			$listeinfos.='<tr>
				<td><a href="index.php?page=membre&amp;majid='.$champs['id_utilisateur'].'" onclick="window.open(this.href); return false;">'.$champs['id_utilisateur'].'</a></td>
				<td>'.$champs['nom'].'</td>
				<td>'.$champs['prenom'].'</td>
				<td>'.$champs['date_op'].'</td>
				<td>'.$champs['champ_modif'].'</td>
				<td>'.$champs[$champs['champ_modif']].'</td>
				<td>'.$champs['valeur'].'</td>
				<td><a href="index.php?page=membre&amp;id='.$champs['id_utilisateur'].'&amp;maj='.$champs['valeur'].'&amp;champ='.$champs['champ_modif'].'&amp;operation='.$champs['id_operation'].'" onclick="window.open(this.href); return false;">Valider la modification</a></td>
				</tr>';
			}
		$listeinfos.='</table>';
		}
	
	echo'
		<form method="post" action="index.php?page=membre">
		
		<h1>Notification</h1>
		<h2>Nouvelles demandes d\'accès membre</h2>
		'.$tabCompte.'
		<h2>Nouvelles demandes de mises à jour d\'informations</h2>
		'.$listeinfos.'
		</form><br/>
		<a href="index.php?page=gestionnaire" > Retourner au menu Gestionnaire</a></i>
		';
	}
?>
