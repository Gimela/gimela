<?php
/*
Kean de Souza et Christeddy Milapie
module_journal_mouvements.php
Objectif : 
	- Afficher le journal des mouvements après connexion
	- Afficher le journal des mouvements pour un utilisateur
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
		
	$champs_manquant = '<p>Veuillez remplir le champ correspondant</p>';

	$select=SelectUsers();
	echo ('
	<h1>Journal des mouvements</h1>
	<form method="post" action="index.php?page=journal_des_mouvements">
		<p><label for="sel_usr">Visualiser les mouvements d`\'un compte : </label> '.$select.'		</p>
		<p> Visualiser les mouvements d\'une date précise : <input type="radio" name="regle" value="date"/> 
			<input type="date" style="width: 150px;" placeholder="jj/mm/aaaa" name="recherche_date"/> </p>
		<p>Mouvements d\'une année <input type="radio" name="regle" value="annee"/> 
			<input type="text" style="width: 150px;" placeholder="aaaa" name="listage_par_an"/> </p>
		<p> Visualiser tous les mouvements de l\'année en cours <input style="left:54%;"type="radio" checked="checked" name="regle" value="all"/> </p> 
		<p><input type="submit" name="voir_selection" value="Consulter" style="position:relative;left:39%;width:12%; padding:10px" /></p>
	</form>
	<script type="text/javascript">$(document).ready(function() { document.title = \'Journal Des Mouvements\';});</script>
	<i><a href="index.php?page=gestionnaire">Retour au menu</a></i> 
	
	');
	
	if(isset($_GET['id']))
		{
		echo AffichageMouvementID($_GET['id']);
		}
	
	if (!empty($_POST['soldes'])) echo AfficherSoldeCompte($_POST['soldes']);
			
	if (isset($_POST['regle'])) $choix_utilisateur = $_POST['regle'];
	else $choix_utilisateur = 'all';
	
	$date_today=date('d/m/Y');
	switch($choix_utilisateur)
		{
		case 'all' : 	if(!empty($_POST['select_user'])) 
							AffichageMouvementClubID($_POST['select_user']);
						else 
							AffichageJournalMouvement(); 
						break;
					
		case 'date' : 	if ((empty($_POST['recherche_date'])) && (empty($_POST['select_user']))) 
							AffichageMouvementJournee();
						elseif((!empty($_POST['select_user'])) && (empty($_POST['recherche_date']) ))
							 AffichageMouvementClubID($_POST['select_user'], $date_today);
							elseif((empty($_POST['select_user'])) && (!empty($_POST['recherche_date']))) 
							AffichageMouvementJournee($_POST['recherche_date']);
						elseif((!empty($_POST['select_user'])) && (!empty($_POST['recherche_date']) )) 
							 AffichageMouvementClubID($_POST['select_user'], $_POST['recherche_date']);
						else 
							AffichageMouvementJournee($_POST['recherche_date']);
						break;
		
		case 'lister_date' : 	if(empty($_POST['listage_par_date'])) { echo $champs_manquant; AffichageMouvementJournee(); }
								else AffichageMouvementID($_POST['select_user'], $_POST['listage_par_date']);
								break;
					
		case 'id_cvvfr' : if(isset($_POST['date_specifie']))  AffichageMouvementClubID($_POST['select_user'], $_POST['recherche_date']);
					else AffichageMouvementClubID($_POST['select_user']);
					break;
							
		case 'annee' : 	if(empty($_POST['listage_par_an'])) { echo $champs_manquant; AffichageJournalMouvement();}
						else AffichageMouvementAn($_POST['listage_par_an']);
						break;
		
					
		default: AffichageJournalMouvement();
	
		};
	}
?>

<?php
	function AffichageJournalMouvement(){
		echo '<h2>Toutes les opérations effectuées sur TOUS les comptes pour l\'année '.date('Y').'</h2><br/>';
		$journalmouvement = JournalDesMouvements();
		ExploitationTableauMouvements($journalmouvement);
		}

	function AffichageMouvementID($id_saisie, $date_saisie = NULL){
		$nom = Information1Utilisateur($id_saisie);
		if (is_null($date_saisie)) 
			{
			foreach ($nom as $info) { echo '<h2> Mouvements liées à '.$info['id_club'].' - '.$info['nom'].' '.$info['prenom'].' </h2> ';}
			$listemouvement = RechercheMouvementParID($id_saisie);
			}
		else
			{ 
			foreach ($nom as $info) { echo '<h2> Mouvements liées à l\'identifiant '.$info['id_club'].' - '.$info['nom'].' '.$info['prenom'].' pour le '.$date_saisie.'</h2>' ;}
			$listemouvement = RechercheMouvementParID($id_saisie, dateFR2US($date_saisie));
			}
		ExploitationTableauMouvements($listemouvement);

		}

	function AffichageMouvementAn($an) {

		echo '<h2> Mouvements liées à l\'année '.$an.' </h2>' ;
		$listemouvement = RechercheMouvementParAn($an);

		ExploitationTableauMouvements($listemouvement);	
		}

	function AffichageMouvementClubID($id_saisie, $date_saisie = NULL) {
		$nom = Information1Utilisateur($id_saisie);
		if (is_null($date_saisie)) 
			{
			foreach ($nom as $info){ echo '<h2> Mouvements liées à l\'identifiant CVVFR '.$info['id_club'].' - '.$info['nom'].' '.$info['prenom'].' </h2>' ;}
			$listemouvement = RechercheMouvementParID($id_saisie);
			}
		else
			{ 
			foreach ($nom as $info){ echo '<h2> Mouvements liées à l\'identifiant CVVFR '.$info['id_club'].' - '.$info['nom'].' '.$info['prenom'].' pour le '.$date_saisie.'</h2>' ;}
			$listemouvement = RechercheMouvementParID($id_saisie, dateFR2US($date_saisie));
			}
		ExploitationTableauMouvements($listemouvement);
		}

	function AffichageMouvementJournee($jour = NULL){
		if (is_null($jour)) 
			{
			$jour = date('Y-m-d');
			echo '<h2> Mouvements liées à aujourd\'hui</h2> ';
			$listemouvement = RechercheMouvementParJournee($jour);
			}
		else
			{ 
			echo '<h2> Mouvements liées à la journée du '.$jour.'</h2>' ;
			$listemouvement = RechercheMouvementParJournee(dateFR2US($jour));
			}
		ExploitationTableauMouvements($listemouvement);
	}	

	// Permet de rendre le tableau lisible , affichage		
	function ExploitationTableauMouvements($tab_sql){
		if (count($tab_sql)>0)
		{
		$tableauliste='<table border="1" style="font-size: 16px; text-align:center; "align="center"> <tr><th>Mouvement</th> <th>Date et Heure</th> <th>Mode de paiement</th> <th style="witdh:100px;">Débit</th> <th>Crédit</th> <th>Libellé</th> <th>ID - Client</th> <th>ID - Gestionnaire</th></tr>';
		foreach($tab_sql as $cellule)
			{
			switch( $cellule['type_mouv'] )
				{
				case 'credit':
								$tableauliste.='<tr> <td>'.$cellule['id_mouv'].'</td> <td>'.dateUS2FR($cellule['date_mouv']).' - '.$cellule['heure_mouv'].'</td> <td>'.$cellule['mode_paie'].'</td> <td></td> <td>'.$cellule['montant'].' '.SIGLE_MONETAIRE.'</td> <td>'.$cellule['description'].'</td> <td>'.$cellule['id_client'].' - '.$cellule['client'].'</td> <td>'.$cellule['id_gestionnaire'].' - '.$cellule['gestionnaire'].'</td> </tr>';
								break;
				case 'debit':	$tableauliste.='<tr> <td>'.$cellule['id_mouv'].'</td> <td> '.dateUS2FR($cellule['date_mouv']).' - '.$cellule['heure_mouv'].'</td> <td>'.$cellule['mode_paie'].'</td> <td>'.$cellule['montant'].' '.SIGLE_MONETAIRE.'</td><td></td>  <td>'.$cellule['description'].'</td> <td>'.$cellule['id_client'].' - '.$cellule['client'].'</td> <td>'.$cellule['id_gestionnaire'].' - '.$cellule['gestionnaire'].'</td> </tr>';
					//		break;
				
				}
			}
		$tableauliste.='</table>';
		echo $tableauliste;	
		}
	else echo '<p> Aucune information disponible suite à la saisie';
	}
	
function AfficherSoldeCompte($choix_user)
	{
	$res = InfoUsers();
		
	$tab ='<table border="4" align="center" > <tr><th>ID CVVFR</th><th>Nom</th> <th>Prénom</th> <th>Solde</th> </tr>';
	foreach ($res as $user)
		{	
		$somme=CalculeSoldeCompte($user['id_util']);
		switch ($choix_user)
			{
				case 'credit' : if ($somme >= 0) $tab.='<tr><td>'.$user['id_club'].'</td> <td>'.$user['nom'].'</td> <td>'.$user['prenom'].'</td> <td>'.$somme.'</td> </tr>' ;	break;
				
				case 'debit' : if ($somme < 0) 	$tab.=' <tr><td>'.$user['id_club'].'</td> <td>'.$user['nom'].'</td> <td>'.$user['prenom'].'</td> <td>'.$somme.'</td> </tr>' ; break;

				default : 	$tab.='<tr><td>Lien</td> <td>'.$user['id_club'].'</td> <td>'.$user['nom'].'</td> <td>'.$user['prenom'].'</td> <td>'.$somme.'</td> </tr>' ;	break;
			}	
		}
	$tab.='</table>';
	return $tab;
	}
?>