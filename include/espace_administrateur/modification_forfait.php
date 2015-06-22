<?php
/*-----------------------------------
Kean de Souza - Projet 8
19/06/15


------------------------------------*/
if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_ADMINISTRATION)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit n&eacute;cessaires pour consulter ce fichier. Vous serez redirig&eacute;.');
	}
else
	{ 
	if(isset($_GET['id_forfait'])) {
		$forfait_info = GetInfoForfaitTarif($_GET['id_forfait']);
		echo('<script type="text/javascript">$(document).ready(function() { document.title = \'Modification des formules ou forfait CVVFR\';});</script>
		<h1>Modifier la '.$forfait_info['description'].' </h1>'); 	
	
		if(isset($_POST['modification_forfait'])) {
			
			if(isset($_POST['id_mod']))
				{
				$taille_mod= sizeof($_POST['id_mod']);
				if ($taille_mod > 0) {	
					for ($j=0; $j<$taille_mod; $j++)
						{
						$bdd=ConnectBddGestionnaire();
						$req = $bdd->prepare('UPDATE config_forfait SET heure_forfait=:heure ,
						tarif_planeur_jeune=:planeur_jeune , tarif_planeur_adulte = :planeur_adulte , supplement =:sup 
						WHERE  id=:id_ligne ');
						$req->bindValue(':heure', $_POST['heure_forfait'][$j], PDO::PARAM_STR );
						$req->bindValue(':planeur_jeune', $_POST['modification_j'][$j], PDO::PARAM_STR );
						$req->bindValue(':planeur_adulte', $_POST['modification_a'][$j], PDO::PARAM_STR );
						$req->bindValue(':sup', $_POST['modification_s'][$j], PDO::PARAM_STR );
						$req->bindValue(':id_ligne', $_POST['id_mod'][$j], PDO::PARAM_INT );
						$req->execute();
						$req->CloseCursor();
						}
				}
			}
			
			
			if(isset($_POST['suppression_planeur'])) {
				$taille_sup_planeur=sizeof($_POST['suppression_planeur']);
				if (DEBUG) { echo $taille_sup_planeur; echo $_POST['suppression_planeur'][0];}
				
				if ($taille_sup_planeur > 0) {
					for ($i=0; $i <$taille_sup_planeur; $i++) {
						$bdd=ConnectBddGestionnaire();
						$req = $bdd->prepare('DELETE FROM config_forfait WHERE id=:id_sup');
						$req->bindValue(':id_sup', $_POST['suppression_planeur'][$i], PDO::PARAM_INT );
						$req->execute();
						$req->CloseCursor();
						echo ('<p> La supression s\'est bien déroulé</p>');
						}
					}
				}
			
			if (isset($_POST['select_aeronef']) && $_POST['select_aeronef'] !== '' ) {
				if(empty($_POST['aj_heure'])) $heure = 0; else $heure = $_POST['aj_heure'];
				if(empty($_POST['aj_montant_jeune'])) $montant_jeune = 0; else $montant_jeune = $_POST['aj_montant_jeune'];
				if(empty($_POST['aj_montant_adulte'])) $montant_adulte = 0; else $montant_adulte = $_POST['aj_montant_adulte'];
				if(empty($_POST['aj_montant_sup'])) $montant_sup = 0; else $montant_sup = $_POST['aj_montant_sup'];
				if (DEBUG) echo 'Heure : '.$heure.' Jeune : '.$montant_jeune.' Adulte : '.$montant_adulte.' Sup: '.$montant_sup ;
				$bdd=ConnectBddGestionnaire();
				
				$present = AeronefDejaCF($_POST['id_forfait'], $_POST['select_aeronef']);
		
				if ($present == FALSE)
					{	
					$req = $bdd->prepare('INSERT INTO config_forfait (heure_forfait, tarif_associe, tarif_planeur_jeune, tarif_planeur_adulte, supplement, planeur_concerne) 
										VALUES (:hr, :tarif_forfait,:jeune,:adulte,:supplement,:planeur)');
					$req->bindValue(':hr', $heure, PDO::PARAM_STR );
					$req->bindValue(':tarif_forfait', $_POST['id_forfait'], PDO::PARAM_INT );
					$req->bindValue(':jeune', $montant_jeune, PDO::PARAM_STR );
					$req->bindValue(':adulte', $montant_adulte, PDO::PARAM_STR );
					$req->bindValue(':supplement', $montant_sup, PDO::PARAM_STR );
					$req->bindValue(':planeur', $_POST['select_aeronef'], PDO::PARAM_INT );
					if (($req->execute()) == TRUE ) echo ('<p> L\'ajout s\'est bien déroulé</p>');
					$req->CloseCursor();
					}
				else echo '<p> L\'aéronef est déjà configuré... </p>';
				
				}	
			
			
		}
		// Modification d'un forfait
		$info_config_forfait = GetInfoForfaitConfig($_GET['id_forfait']);
		
		$tableau_config =' <br/> <table border="1" align="center" style="font-size: 18px; text-align:center;"> 
							<tr>
								<th style="white-space:normal;">Heure</th>
								<th> Planeur </th>
								<th> Tarif Jeune</th>
								<th> Tarif Adulte</th>
								<th> Supplement</th>
								<th> Supprimer ? </th>
							</tr>';
		$selectplaneur = SelectAeronefs();
		
		if(!empty($info_config_forfait)) {
			foreach ($info_config_forfait as $donnee )
				{
					 //<input type="text" value = " " name="modification[] />"
				$tableau_config.= ' <tr>
					<td style="white-space:normal;"> <input type="text" style="width: 40px;" value = "'.$donnee['heure'].'" name="heure_forfait[]" /> </td>
					<td><input type="hidden" value = "'.$donnee['id'].'" name="id_mod[]" /> '.$donnee['modele'].' </td>
					<td><input type="text" style="width: 40px;" value = "'.$donnee['tarif_planeur_jeune'].'" name="modification_j[]" /> '.SIGLE_MONETAIRE.'</td>
					<td><input type="text" style="width: 40px;" value = "'.$donnee['tarif_planeur_adulte'].'" name="modification_a[]" /> '.SIGLE_MONETAIRE.'</td>
					<td><input type="text" style="width: 40px;" value = "'.$donnee['supplement'].'" name="modification_s[]" /> '.SIGLE_MONETAIRE.'</td> 
					<td> <input type="checkbox" name="suppression_planeur[]" value="'.$donnee['id'].'" /> </td>
					</tr>';
					
				}
		}
		
			$tableau_config.='<tr>
					<td> <input type="text" name="aj_heure" style="width: 50px;" /> </td>
				<td align="center"> '.$selectplaneur.' </td>
				<td> <input type="text" name="aj_montant_jeune" style="width: 40px;" /> </td>
				<td> <input type="text" name="aj_montant_adulte" style="width: 40px;" /> </td>
				<td> <input type="text" name="aj_montant_sup" style="width: 40px;" /> </td>				
				</tr>
				</table>';
		
		echo ('<form method="post" action="#" >
		<p> Configuration de la '.$forfait_info['description'].' </p>
		'.$tableau_config.'
		<br/>
		<input type="hidden" name="id_forfait" value="'.$_GET['id_forfait'].'"/>
		<input type="submit" style="width:250px; text-align:center;" value="VALIDER LES MODIFICATIONS" name="modification_forfait"/>
		<p><a href="index.php?page=gestion_forfait"> Retour vers la gestion des forfaits </a></p>
		</form>');
		} 
	else{
		global $forfait;
		$tableau_id_forfait = array_keys($forfait);
		$taille=sizeof($forfait);
		
		if(DEBUG) {
			echo ('<p>Taille du tableau des forfaits paramétrés :'.$taille.'</p>');
			echo ('<p>Clées représentant les ID de la table des tarifs pour les forfaits</p>');
			if(DEBUG) print_r($tableau_id_forfait);
			foreach ($forfait as $indice)
				{
				echo '<p>Heure : '.$indice.'</p>';
					
				}
			}
			
		$i=0;
		$forfait_dispo ='<br/> <table border="1" align="center" style="font-size: 20px; text-align:center;"> 
							<tr>
								<th> ID Forfait </th>
								<th> Description</th>
								<th> Tarif Jeune</th>
								<th> Tarif Adulte</th>
								<th> Code Barre Tarif</th>
							</tr>';
								
		for ($i=0; $i < $taille; $i++)
			{
			$forfait_info = GetInfoForfaitTarif($tableau_id_forfait[$i]);
			$forfait_dispo.= ' <tr> 
				<td style="white-space:normal;"> <a href="index.php?page=gestion_forfait&amp;id_forfait='.$forfait_info['id_tarif'].'"> '.$forfait_info['id_tarif'].' </a> </td>
				<td>'.$forfait_info['description'].'</td>
				<td>'.$forfait_info['tarif_jeune'].' '.SIGLE_MONETAIRE.'</td>
				<td>'.$forfait_info['tarif_adulte'].' '.SIGLE_MONETAIRE.'</td>
				<td><a href="index.php?page=modification_tarif&amp;id= '.$forfait_info['code_barre'].'" >'.$forfait_info['code_barre'].'</a>  </td> 
				</tr>';
			}
		$forfait_dispo.='</table>';
		
		echo('<script type="text/javascript">$(document).ready(function() { document.title = \'Modification des formules ou forfait CVVFR\';});</script>
		<h1>Modifier une formule</h1>
		<form method="post" action="#" >
		<p> Liste des forfaits disponibles actuellement : </p>
		'.$forfait_dispo.'
		<p>  <a href="index.php?page=administrateur"> < Revenir au menu </a></p>
		</form>');
		}
	
	}

?>