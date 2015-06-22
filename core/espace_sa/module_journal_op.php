<?php
/***********************************************

Kean de Souza
Crée le 18/06/2015
module_journal_op.php

Objectif :	- Lister toutes les opérations sur les tables dans un journal


***********************************************/

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
	echo ('<h1> Journal des opérations </h1> 
		<div>
		
	<form method="post" action="index.php?page=journal_operation">
			<label for="sel_user">Visualiser les opérations sur une table : </label>  
			<select name="select_table" id="sel_user"> 
				<option selected="selected" value=" "> Aucune sélection</option>
				<option value="aeronefs"> Table des aeronefs</option>
				<option value="compte_utilisateur"> Table des utilisateurs </option>
				<option value="config_forfait"> Table de la configuration des forfaits</option>
				<option value="mouvement"> Table des mouvements</option>
				<option value="operation"> Table des opérations</option>
				<option value="statut"> Table des status</option>
				<option value="tarif"> Table des tarifs</option>	
				<option value="vol"> Table des planches de vol</option>
			</select>
		<p>Visualiser les opérations d\'une date précise : <input type="radio" name="regle_date" value="date"/>
			<input type="date" style="width: 150px;"name="date_text" placeholder="jj/mm/aaaa" />   </p>
		<p>Visualiser les opérations d\'une année <input type="radio" name="regle_annee" value="annee"/>
			<input type="date" style="width: 150px;"name="annee_text" placeholder="aaaa" /> </p>
		<p><input type="submit" name="voir_selection" value="Consulter" style="position:relative;left:39%;width:12%; padding:10px" /> </p>
		<p><i><a href="index.php?page=administrateur">Retour au menu</a></i> </p>
	</form>
</div>


	<script type="text/javascript">$(document).ready(function() { document.title = \'Admin : Journal Des Opérations\';});</script> ');
	
	// Demande de soumission pour 
	if ( (isset($_POST['select_table'])) &&  (!isset($_POST['regle_date'])) && (!isset($_POST['regle_annee'])) )	{
		$annee_now = date('Y');
		$res=AfficherOpe($_POST['select_table'], $annee_now);
		$affiche=AffichageOpe($res);
		echo '<p> Opération pour l\'année '.$annee_now.'</p>'.$affiche; 
		}
	// Rechecher des OP pour une date donnée, si input non saisi date du jour
	elseif ( (isset($_POST['select_table'])) &&  (isset($_POST['regle_date'])) ) {
		if(empty($_POST['date_text'])) $date_recherche = date('d/m/Y');
		else $date_recherche = $_POST['date_text'];
		
		$resultat=AfficherOpe($_POST['select_table'], $date_recherche);
		$affiche=AffichageOpe($resultat); 
		echo '<p> Opération pour liée au '.$date_recherche.'</p>'.$affiche;
		}
	// Rechecher OP pour l'annee
	elseif ( (isset($_POST['select_table'])) &&  (isset($_POST['regle_annee'])) ) {
		if(empty($_POST['annee_text'])) $annee_recherche= date('Y');
		else $annee_recherche = $_POST['annee_text'];
		
		$resultat=AfficherOpe($_POST['select_table'], $annee_recherche);
		$affiche=AffichageOpe($resultat); 
			echo '<p> Opération pour l\'année '.$annee_recherche.'</p>'.$affiche; 
		}
				
	}

?>

<?php

function AffichageOpe($resultat_tableau) {
	
	//SELECT id_operation, id_utilisateur, client.nom as cli_nom, client.prenom as cli_pre, id_gestionnaire, gestionnaire.nom as gest_nom, 
	//gestionnaire.prenom as gest_prenom, demande_valid, table_modif, champ_modif, valeur, typ_op, DATE(date_op) as date_operation 
	if (!empty($resultat_tableau)) {
		$table_ope ='<table border="1" style="font-size: 15px; text-align:center;" align="center">
			<tr>
				<th>Opération</th>
				<th>Utilisateur</th>
				<th>Gestionnaire</th>
				<th>Date de modification</th>
				<th>Table	</th>
				<th>Champs	</th>
				<th>Valeur</th>
				<th>Type d\'opération</th>
			</tr>';
	
		foreach ($resultat_tableau as $value) {
			$table_ope.='<tr>
				<td>'.$value['id_operation'].'</td>
				<td>'.$value['cli_nom'].' '.$value['cli_pre'].'</td>
				<td>'.$value['gest_nom'].' '.$value['gest_prenom'].'</td>
				<td>'.dateUS2FR($value['date_operation']).'</td>
				<td>'.$value['table_modif'].'</td>
				<td>'.$value['champ_modif'].'</td>
				<td>'.$value['valeur'].'</td>
				<td>'.$value['typ_op'].'</td>
			</tr>';
			}
		$table_ope.= '</table>';
	} 
	else 
		$table_ope = '<p style="text-align: center;">Aucune information disponible </p>';
	
	return $table_ope;
}

?>