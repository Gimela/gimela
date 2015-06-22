<?php
/*
Kean de Souza
operation_compte.php
Crée le 01/06/15
Modifié le 03/06/15
Objectif : 
	- Créer un mouvement sur un compte pilote
	- Modifier un mouvement sur un compte pilote
	- Supprimer un mouvement sur un compte pilote
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
	echo ('
	<script type="text/javascript">$(document).ready(function() { document.title = \'Opérations - Mouvements\';});</script>
	<h1>Opérations sur les mouvements des comptes pilotes</h1>
	<p><a href="index.php?page=journal_des_mouvements" onclick="window.open(this.href); return false;">Afficher le journal des mouvements dans un autre onglet</a> </p>
	<form method="post" action="#">
	<p><input type="submit" name="ajouter_mouvement" value="Menu : Créer un mouvement" style ="width:40%;padding:10px" /></p>
	');
	
	if (isset($_POST['ajouter_mouvement'])) DeroulementAjouterMouvement();
	elseif(isset($_POST['valider_ajout_mouvement'])) CreerMouvement();
	
	echo ('
	<p>ID du mouvement à supprimer ou à modifier : <input type="text" name="id_mouvement" style="width:6%;padding:10px"/></p>
	<p><input type="submit" name="modifier_mouvement" value="Menu : Modifier un mouvement existant" style ="width:40%;padding:10px"/> </p>
	');
	if (isset($_POST['modifier_mouvement'])) DemandeModification();
	elseif(isset($_POST['soumettre_modifcation']))
		{
		$montant_saisi=$_POST['modification_montant'];
		$plus = '+'; $moins='-';
		
		if(strpos($montant_saisi, $plus) !== FALSE) 
			{
			$montant_saisi= str_replace($plus, '',$montant_saisi);
			$type_montant='credit';
			}
		elseif (strpos($montant_saisi, $moins) !== FALSE)
			{
			$montant_saisi= str_replace($moins, '',$montant_saisi);
			$type_montant='debit';	
			}
		else
			{
			$type_montant='credit';
			}
		echo $montant_saisi.$type_montant;
		if (MajMouvement($_SESSION['id'], $type_montant, $_POST['tarif_ajout'], $_POST['paiement_ajout'] , $_POST['commentaire_modif'], $_POST['id_mouvement_mod'], $montant_saisi)) 
			{
			echo 'La mise à jour s\'est bien déroulé';
			global $forfait;
			if(array_key_exists($_POST['tarif_ajout'], $forfait))
				{
				$tab=SelectionnerMouvement($_POST['id_mouvement_mod']);
				MAJForfaitUser($tab['id_client'], $_POST['tarif_ajout']);
				}
			}	
		else {echo 'Une erreur a été rencontré';}
		}
	echo ('<p><input type="submit" name="supprimer_mouvement" value="Menu : Supprimer un mouvement existant" style ="width:40%;padding:10px" /></p>');
	
		if (isset($_POST['supprimer_mouvement']))
			{
			if(empty($_POST['id_mouvement'])) echo '<p>Veuillez définir un ID</p>';
			else	
				{
				$tab=RechercheMouvementParIdMouvement($_POST['id_mouvement']);
				if (count($tab) < 1) echo '</p>Aucun résultat trouvé par rapport à l\'id renseigné </p>';
				else 
					{
					$mouvement=' <table border="1" align=center style="font-size: 18px; text-align:center">';
					foreach($tab as $cellule)
						{
						$mouvement.='<input type="text" hidden value="'.$cellule['id_mouv'].'" name="id_mouvement_mod"/> 
						<input type="text" hidden value="'.$cellule['id_client'].'" name="id_client_mouv"/>
						<tr>
							<td> Date et heure </td>
							<td> '.$cellule['date_mouv'].'</td>
						</tr>
						<tr>
							<td>Mode de paiement </td>
							<td> '.$cellule['mode_paie'].'</td>
						</tr>
						<tr>
							<td>Montant </td>
							<td> '.$cellule['montant'].' €</td>
						</tr>
						<tr>
							<td> Description </td>
							<td> '.$cellule['description'].'</td>
						</tr>
						<tr>
							<td> Client </td>
							<td> '.$cellule['client'].'</td>
						</tr>
						<tr>
							<td> Gestionnaire d\'origine </td>
							<td> '.$cellule['gestionnaire'].'</td><
						/tr>';
						}
					$mouvement.='</table><p> Commentaire :<br/> '.$cellule['comm'].' </p>';
					echo $mouvement;
					echo '<p><input type="submit" value="Supprimer le mouvement" name="suppression_mouvement"/></p>';
					}
				}
			}
		elseif(isset($_POST['suppression_mouvement']))
			{
			if (SupprimerMouvement($_POST['id_mouvement_mod'])) echo '<p>Supression effectuée avec succès</p>';
			else echo '<p>Une erreur inopiné a été rencontré, veuillez réessayez</p>';
			}
	echo ('
	<a href="index.php?page=tarif" onclick="window.open(this.href); return false;" >Voir les tarifs du club</a> 
	<p><a href="index.php?page=gestionnaire"><= Retour au menu</a> </p>
	</form>
	');
	}

	
	

?>

<?php

function DemandeModification()
	{
	if(empty($_POST['id_mouvement'])) echo 'Veuillez définir un ID';
		else	
			{
			$tab=RechercheMouvementParIdMouvement($_POST['id_mouvement']);
			if (count($tab) < 1) echo '</p>Aucun résultat trouvé par rapport à l\'id renseigné </p>';
			else 
				{
				$tarifs=MenuDeroulantListeTarifs();
				$paiement=MenuDeroulantListeMoyensPaiement();
				
				$mouvement=' ';
				foreach($tab as $cellule)
					{
					$mouvement.='<input type="hidden" value="'.$cellule['id_mouv'].'" name="id_mouvement_mod"/> 
					<input type="hidden" value="'.$cellule['id_client'].'" name="id_client_mouv"/> 
					<table border="1" style=" font-size=16px;"align="center">
					<tr>
						<td>Date et Heure</td>
						<td>'.$cellule['date_mouv'].'</td> 
					</tr>
					<tr>
						<td>Mode de paiement</td>
						<td>'.$paiement.' <option selected="selected" value="'.$cellule['mode_paie'].'">'.$cellule['mode_paie'].'</option> </select></td> 
					</tr>
					<tr>
						<td>Montant</td>
						<td><input type="text" value="'.$cellule['montant'].'" name="modification_montant" style="width:100px;"/> </td> 
					</tr>
					<tr>
						<td>Libellé</td>
						<td> '.$tarifs.' <option selected="selected" value="'.$cellule['type_tarif'].'"> '.$cellule['description'].'</option> </select> </td> 
					</tr>
					<tr>
						<td>Client</td>
						<td>'.$cellule['client'].'</td> 
					</tr>
					<tr>
						<td>Gestionnaire</td>
						<td>'.$cellule['gestionnaire'].'</td> 
					</tr>';
					}
				$mouvement.='</table>';
				$mouvement.='<p> Commentaire :<br/> <input type="text" size="100" value="'.$cellule['comm'].'" name="commentaire_modif"/> </p>';
				echo $mouvement;
				echo '<br/><input type="submit" value="Soumettre la modifcation" name="soumettre_modifcation"/>';
				}
			}
	}

function MenuDeroulantListeTarifs()
	{
	$liste_tarif=ListerTarifs();
	
	$tarifs='<select id="tarif" name="tarif_ajout" > <option value="0"> Aucune sélection</option>';
	foreach ($liste_tarif as $valeur)
		{	
		$tarifs.= '<option value="'.$valeur['id_tarif'].'"> '.$valeur['description'].' - Jeune : '.$valeur['tarif_jeune'].' € - Adulte : '.$valeur['tarif_adulte'].' €</option>';
		}
	return $tarifs;
	}
	
function MenuDeroulantListeMoyensPaiement()
	{
	 $menu = '<select name="paiement_ajout"> 
				<option value="especes">Espèce</option> 
				<option value="cheque">Chèque</option> 
				<option value="virement">Virement</option> 
				<option value="ANCV"> ANCV</option> 
				<option value="CB">Carte de crédit</option> ';
	return $menu;
	}
	
function DeroulementAjouterMouvement(){
	$select=SelectUsers();
	echo '<p> Client : '.$select.'</p>
	<p>Type d\'opération : <select name="type_operation"> <option value="credit">Créditer le compte</option> <option value="debit">Débiter le compte</option> </select> </p>
	<p> Type de mouvement </p>';
	$select2=MenuDeroulantListeTarifs();
	$paiement=MenuDeroulantListeMoyensPaiement();
	$paiement.='</select>';
	
	echo $select2.'</select><p> Montant du mouvement  <br/> <input type="text" style="width: 150px;" id="montant" name="montant_tarif"/> </p>
	'.$paiement.'
	<p> Commentaire</p> <input type="text" size="100"  name="commentaire_ajout" /> 
	<input type="submit" name="valider_ajout_mouvement" value="Ajouter le mouvement"/>
	<p>---------------------------------------------------------------</p>
	';
}

function CreerMouvement()	{
	//echo $_POST['id_utilisateur'].'-'.$_POST['tarif_ajout'].'-'.$_POST['montant_tarif'].'-'.$_POST['commentaire_ajout'].'-'.$_POST['paiement_ajout'];
	$montant_saisi=$_POST['montant_tarif'];
	$plus = '+'; $moins='-';
	
	if((strpos($montant_saisi, $plus) !== FALSE) && (!isset($_POST['type_operation']))) 
		{
		$montant_saisi= str_replace($plus, '',$montant_saisi);
		$type_montant='credit';
		}
	elseif ((strpos($montant_saisi, $moins) !== FALSE)  && (!isset($_POST['type_operation']))) 
		{
		$montant_saisi= str_replace($moins, '',$montant_saisi);
		$type_montant='debit';	
		}
	elseif (isset($_POST['type_operation']))
		{
		$type_montant= $_POST['type_operation'];
		}
		
	if (AjouterMouvement($_POST['select_user'], $_SESSION['id'], $type_montant, $_POST['tarif_ajout'],$_POST['paiement_ajout'], $_POST['commentaire_ajout'], $montant_saisi) )
		{
		echo 'Ajout effectué avec succès';

		global $forfait;
		if(array_key_exists($_POST['tarif_ajout'], $forfait))
					{
					
					$today = date('Y-m-d');
					$bdd = ConnectBddGestionnaire();
					$req= $bdd -> prepare('SELECT id_mouv FROM mouvement WHERE DATE(date_heure_mouv)=:date_now AND type_tarif=:id_forfait AND id_client=:id_user ORDER BY date_heure_mouv DESC');
					$req -> bindValue(':date_now', $today, PDO::PARAM_STR);
					$req -> bindValue(':id_forfait', $_POST['tarif_ajout'], PDO::PARAM_INT);
					$req -> bindValue(':id_user',$_POST['select_user'], PDO::PARAM_INT);
					$req -> execute();
					$res = $req -> fetch();
					$req->CloseCursor();
					
					MAJForfaitUser($_POST['select_user'], $res[0]);
					}
		}
	else {echo 'Une erreur inopiné a été rencontré';}	
	echo '<p>---------------------------------------------------------------</p>';
}


?>