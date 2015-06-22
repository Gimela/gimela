<?php
/*
Kean de Souza
Espace_membre_SysVv.php
Crée le 24/03/15
Dernière modification le : 01/04/15

Edité avec Notepad++ 10/01/15 - 17:20:21 ( Je suis Charlie Edition)

Objectif : Interface membre de SysVV
- Permettre la visualisation du compte de l'utilisateur à lui-même
- Voir les mouvements effectuées sur lson compte (vente, achat de produit)
- Voir les vols effectuée
- Permettre à l'utilisateur de modifier ses informations 

-------------- Fonctionnement principal de l'interface
| Le code qui suit indique le chemin que suivra la session de l'utilisateur
| Si tableau_session_vide => redirection à l'accueil
| Sinon requête de récupération des données user
| Si ( niveau_de_privilige < niveau_membre ) = accès à ses informations
| Sinon Si demande_déconnexion => fin de session puis redirection accueil
| Sinon Si demande_affichage_vol => Affichage de l'ensemble des vols
| Sinon Si demande_modification => Affichage des informations pouvant être modifier
| Sinon Si demande_affichage_mouvements => Affichage de l'ensemble des mouvements 
| Sinon (PAR DEFAUT) => Affichage de l'interface de base
|	Si (modification_valide) => Message de confirmation et d'attente
*/


if(empty($_SESSION)){
	echo '<p> Veuillez vous identifier ! </p>';
	header('refresh: 1; URL=index.php?page=accueil');
}
else{
	if (!empty($_GET['id']) && $_SESSION['id_statut'] >= STATUT_ACCES_GESTION ) 
		{
		$id_demande = $_GET['id'] ;	
		}
	else 
		{	
		$id_demande=$_SESSION['id'];
		}
		
	$resultat = $reqUserStatutById->execute(array(':id'=>$id_demande));
	$resultat = $reqUserStatutById->fetch(); // Récuperer les infos concernant l'utilisateur
	$reqUserStatutById->CloseCursor();
	$id_cvvfr_demande = $resultat['id_club'];
		
	if ($resultat['id_statut'] < 2 && $_SESSION['id_statut'] < 4) { // Personne non valide
		echo '<p> En attente de la validation par un gestionnaire, veuillez vous reconnectez plus tard </p>';		
		exit();
		}
	/*------------------------------------------------------------------------
					Succès ou Non de la MAJ du statut du membre
	--------------------------------------------------------------------------*/
	
	elseif ($_SESSION['id_statut'] >= 4 && isset($_POST['validation_membre'] ))
		{
		if ( UpdateNouveauUser($_GET['id'], 2, $_POST['identifiant_club']) == TRUE) $message_validation ="Membre mis à jour avec succès.";
		else $message_validation="Une erreur a été rencontré, veuillez réessayer et remplir le champ d'identifiant club.";
			
		echo ('<script type="text/javascript"> $(document).ready(function() {document.title = \'Membre '.$resultat["nom"].'\';}); </script>
					<p>'.$message_validation.' Vous pouvez fermer cette page.</p>
			');
		}
	/*-------------------------------------------------------------------------
				Mise a jour des informations des utilisateurs
	--------------------------------------------------------------------------*/
	
	elseif(!empty($_GET['id']) && $_SESSION['id_statut'] >= 4 && isset($_GET['maj']) && isset($_GET['champ']) )
		{
		$verification=MajInformationUser($_GET['id'], $_GET['champ'], $_GET['maj'], $_GET['operation'],$_SESSION['id'], 'Modification');
		if ($verification) echo '<p><mark style="background-color : green;">Mise à jour effectué, vous pouvez fermer la page.</mark></p>';
		else echo '<p> <mark style="background-color : red;">Une erreur a été rencontré, veuillez recommencer</mark></p>';
		}
	/*------------------------------------------------------------------------
			Interface de validation d'un membre -- GESTIONNAIRE
	--------------------------------------------------------------------------*/
	
	elseif(!empty($_GET['id']) && $_SESSION['id_statut'] >= 4 && isset($_GET['maj']) )
		{
			if (isset($_POST['identifiant_club']))
				{
				if(VerificationIdClub($_POST['identifiant_club']))
					{
					echo '<p>L\'identifiant '.$_POST['identifiant_club'].' est valide </p>';
					$id_club = $_POST['identifiant_club'];
					}
				else{ echo '<p>Identifiant '.$_POST['identifiant_club'].' déjà utilisé</p>'; }
				}
			else $id_club = ' ';

		echo (' <script type="text/javascript"> $(document).ready(function() {document.title = \'Membre '.$resultat["nom"].' à valider\';}); </script>
		<h1> Interface validation de membre  </h1>
		<form method="post" action="#">
				<h2> Informations du membre </h2>
				
				<ul>
					<li>Identifiant '.NOM_CLUB.' : '.$resultat['id_club'].'</li>
					<li>Nom: '.$resultat["nom"].'</li>
					<li>Prenom:	'.$resultat["prenom"].'  </li>
					<li>Age: '.Age($resultat["date_naissance"]).' ans </li>
					<li>Adresse: 	'.$resultat["adresse"].' </li>
					<li>Code Postal:	 '.$resultat["cp"].'</li>
					<li>Ville:	'.$resultat["ville"].'</li>
					<li>Mail:	'.$resultat["mail"].'</li>
					<li>Sexe:	'.$resultat["sexe"].'</li>
					<li>Numéro de licence | Date d\'obtention: 	'.$resultat["num_licence"].' | '.dateUS2FR($resultat["date_licence"]).' </li>
					<li>Date de la dernière visite médicale: '.dateUS2FR($resultat["visit_med"]).' </li>
					<li> Message important : </li>
				</ul>
				<label for="id_club"> Veuillez entrer l\'identifiant '.NOM_CLUB.' AVANT DE VALIDER : <input type="text" id="id_club" value="'.$resultat['id_club'].'" name="identifiant_club"/> 
					<p style="text-align: center">
				
				
					<input type="submit" style="margin: 0 auto; width: 250px; text-align:center;" name="verification_id" value="Vérifier si l\'identifiant est disponible"/> </p>
				
				<p> <input type="submit" name="validation_membre" value="Accepter le membre" style="width:250px;padding:5px"/>  </p>
				
				<p> Fermer la page si vous ne souhaitez pas valider ce membre. </p>
			</form> ');	
		}
	
	/*--------------------------------------------------------------------------
						Affichage de tous les vols effectués
	---------------------------------------------------------------------------*/
	
	elseif (isset($_POST['form_vol'])){
		
		$resultat4=$reqMoyVolPlanneurByIdId->execute(array(':id'=>$id_cvvfr_demande));
		$resultat4=$reqMoyVolPlanneurByIdId->fetch();
		$reqMoyVolPlanneurByIdId->CloseCursor();

		$tabVol2=ListeVolUtilisateur($id_cvvfr_demande);

		$tabVol2.='<p>Résumé : Heure de vol au total : '.MinutesAHeuresMinutes($resultat4['moyenne_vol']).'</p>';

		echo '<script type="text/javascript"> $(document).ready(function() {document.title = \'Liste des vols effectué - Interface membre du CVVFR\';}); </script>
		<h1>Interface membre</h1>
		<h2> Vol effectué au cours de l\'année</h2>
			'.$tabVol2.'
		<p><a href="index.php?page=membre">Retourner à mon espace</a></p>';
		}
	/*---------------------------------------------------------------
			Formulaire de modification des informations personnelles
	-----------------------------------------------------------------*/
	
	elseif (isset($_POST['form_info'])){ 
		//Requête SQL
		$resultat = $reqUserStatutById->execute(array(':id'=>$id_demande));
		$resultat = $reqUserStatutById->fetch(); // Récuperer les infos concernant l'utilisateur
		$reqUserStatutById->CloseCursor();

		echo('<script type="text/javascript"> $(document).ready(function() {document.title = \'Modification d\'informations - Interface membre du CVVFR\';}); </script>
				<form method="post" action="#">
				<h1> Interface membre  </h1>
				<h2> Mes informations </h2>
				<p><i>Certaines informations ne peuvent être que changées par le gestionnaire en présentant les pièces requises </i></p>
				<ul>
					<li><strong> Identifiant CVVFR :</strong> '.$resultat['id_club'].'</li>
					<li><strong>Nom:</strong> '.$resultat["nom"].'</li> 
					<li><strong>Prenom:</strong>	'.$resultat["prenom"].'  </li>
					<li><strong>Sexe:</strong>	'.$resultat["sexe"].'</li>
					<li><strong>Age:</strong> '.Age($resultat["date_naissance"]).' ans </li>
					<li><strong>Adresse:</strong> <input style="width:28%;position:relative;left:27px" type="text"  value="'.$resultat["adresse"].'" name="md_adr"/> </li>
					<li><strong>Code Postal:</strong> <input style="width:28%" type="text"  value="'.$resultat["cp"].'" name="md_cp"/> </li>
					<li><strong>Ville:</strong> <input style="width:28%;position:relative;left:51px" type="text"  value="'.$resultat["ville"].'" name="md_ville"/> </li>
					<li><strong>E-Mail:</strong> <input style="width:28%;position:relative;left:39px" type="text" size="50"  value="'.$resultat["mail"].'" name="md_mail"/></li>
					<li><strong>Tél.fixe :</strong> <input style="width:28%;position:relative;left:21px" type="text"  value="'.$resultat["tel_fixe"].'" name="md_fixe"/></li>
					<li><strong>Portable:</strong> <input style="width:28%;position:relative;left:21px" type="text"  value="'.$resultat["tel_mobile"].'" name="md_mob"/></li><br>
					<li><strong>Numéro de licence/ Date d\'obtention :</strong> '.$resultat["num_licence"].'	/ '.dateUS2FR($resultat["date_licence"]).' </li>
					<li><strong>Date de la dernière visite médicale:</strong> '.dateUS2FR($resultat["visit_med"]).' </li>
					<li><strong>Message important :</strong> </li>
				</ul>
				<p><input type="submit" name="mod_form_info" value="Modifer mes informations" style="width:25%;padding:10px"/> </p>
				<a href="index.php?page=membre">Retourner à mon espace</a>				
				</form>
				');
		}
	/*------------------------------------------------------------------------------------	
				Visualisation de toutes les opérations effectué sur le compte
	--------------------------------------------------------------------------------------*/
	
	elseif (isset($_POST['tab_mouv'])){

		$resultat3=$reqMouvementUserById->execute(array(':id'=>$id_demande));
		$resultat3=$reqMouvementUserById->fetchAll(PDO::FETCH_NAMED);
		$reqMouvementUserById->CloseCursor();	
	
		if(count($resultat3) < 1) $tabMouv2 = 'Aucune informations disponibles';
		else
				{
				$tabMouv2='<table border="4" align="center" style="text-align: center">
						<tr>
							<th>Date</th>
							<th>Libellé</th>
							<th>Montant</th>
							<th>Gestionnaire</th>
						</tr>';
				$solde_compte2=0.00;
				$total_deb=0.00;
				$total_cre=0.00;
				
				foreach($resultat3 as $row2)
					{
					$montant_req2=0.00;
					switch($row2['type_mouv'])
						{
						case 'debit' : 	$solde_compte2 = $solde_compte2 - $row2['montant'];
										$montant_req2 = $montant_req2 - $row2['montant'];
										$total_deb= $total_deb - $row2['montant'];
										break;
										
						case 'credit' : $solde_compte2 = $solde_compte2 + $row2['montant']; 
										$montant_req2 = $montant_req2 + $row2['montant'];
										$total_cre= $total_cre + $montant_req2;
										break;
						}
					$tabMouv2.='<tr><td>'.dateUS2FR($row2['date_mouv']).'</td> <td>'.$row2['description'].'</td> <td>'.$montant_req2.' '.SIGLE_MONETAIRE.' </td> <td>'.$row2['nom'].' '.$row2['prenom'].'</td></tr>';
					}
			$soldeAct=$total_cre + $total_deb ;
			
			if ($soldeAct > 0) $AffsoldeAct='<b style="color: green;">'.$soldeAct.' '.SIGLE_MONETAIRE.'</b>';
			else $AffsoldeAct='<b style="color: red;">'.$soldeAct.' '.SIGLE_MONETAIRE.' </b>';
			
			$tabMouv2.='<tr><th>Total</th><th> Crédit :  <b style="color: green;">'.$total_cre.' '.SIGLE_MONETAIRE.'</b>  <br/> Débit : <b style="color: red;">'.$total_deb.' '.SIGLE_MONETAIRE.' </b></th> <th> Solde Actuelle : <br/> '.$AffsoldeAct.'</th></tr> </table>';
			}
		echo'	<script type="text/javascript"> $(document).ready(function() {document.title = \'Visualisation du compte pilote\';}); </script>
				<h1> Interface membre  </h1>
				<h2>Mon compte pilote</h2>
				'.$tabMouv2.'
				<p><a href="index.php?page=membre">Retourner à mon espace</a></p>

				';
		}
	/*-----------------------------------------------------------
				Interface principale
	-------------------------------------------------------------*/
	else{
		// Si modifications auparavant mettre en attente le processus de modif
		if (isset($_POST['mod_form_info']))
			{ 
			$modif=0; //savoir si il y'a une ou plusieurs modification
			if ( $resultat['adresse'] != $_POST['md_adr']){ DemandeMajByUser($id_demande, 'compte_utilisateur', 'adresse', $_POST['md_adr'], 'Ajout'); $modif++;}
			if ( $resultat['cp'] != $_POST['md_cp'] ) { DemandeMajByUser($id_demande, 'compte_utilisateur', 'cp', $_POST['md_cp'], 'Ajout'); $modif++;}
			if ( $resultat['ville'] != $_POST['md_ville']) { DemandeMajByUser($id_demande, 'compte_utilisateur', 'ville', $_POST['md_ville'], 'Ajout'); $modif++;}
			if ( $resultat['mail'] != $_POST['md_mail']) { DemandeMajByUser($id_demande, 'compte_utilisateur', 'mail', $_POST['md_mail'], 'Ajout'); $modif++;}
			if ( $resultat['tel_fixe'] != $_POST['md_fixe']) { DemandeMajByUser($id_demande, 'compte_utilisateur', 'tel_fixe', $_POST['md_fixe'], 'Ajout'); $modif++;}
			if ( $resultat['tel_mobile'] != $_POST['md_mob']) { DemandeMajByUser($id_demande, 'compte_utilisateur', 'tel_mobile', $_POST['md_mob'], 'Ajout') ; $modif++;}
			if ( $modif > 1 ) $modif_info.='<p>Les modifications seront verifiées puis validées par un gestionnaire</p>';
			if ($modif == 1) $modif_info.='<p>Votre modification sera analysée puis validée par un gestionnaire</p>';
			
			}
					
		$resultat3=$reqMouvementUser5ById->execute(array(':id'=>$id_demande));
		$resultat3=$reqMouvementUser5ById->fetchAll(PDO::FETCH_NAMED);
		$reqMouvementUser5ById->CloseCursor();

		$resultat4=$reqMoyVolPlanneurByIdId->execute(array(':id'=>$id_cvvfr_demande));
		$resultat4=$reqMoyVolPlanneurByIdId->fetch();
		$reqMoyVolPlanneurByIdId->CloseCursor();

		$resultat5=$reqMoyVolForEachPlanneurByIdId->execute(array(':id'=>$id_cvvfr_demande));
		$resultat5=$reqMoyVolForEachPlanneurByIdId->fetchAll(PDO::FETCH_NAMED);
		$reqMoyVolForEachPlanneurByIdId->CloseCursor();
		
		$resultat6=$reqModifWaitUserById->execute(array(':id'=>$id_demande));
		$resultat6=$reqModifWaitUserById->fetchAll(PDO::FETCH_NAMED);
		$reqModifWaitUserById->CloseCursor();
		
		$total_remorquage = TotalRemorquage($id_cvvfr_demande);
		// Affichage des valeurs en demande de modification_valide
		$tabModif='';
		
		foreach($resultat6 as $row5){
			
			switch($row5['champ_modif'])
				{
					case 'adresse' : $champsmod='adresse :'; break;
					case 'cp' : $champsmod='code postal :'; break;
					case 'ville' : $champsmod='ville :'; break;
					case 'tel_fixe' : $champsmod='téléphone domicile :'; break;
					case 'tel_mobile' : $champsmod='portable :'; break;
					case 'mail' :  $champsmod='mail :'; break;
					default : $champsmod='Non défini';
				}
		}
		if(empty($resultat['mouvement_forfait'])) $info_forfait = 'Aucun';
		else	{
		$info_f = GetForfaitDescription($resultat['mouvement_forfait']);
		print_r($info_f);
		$info_forfait = $info_f['description'];
		$total_vol = CalculResteForfait($resultat['mouvement_forfait']);
		$info_forfait.= '<br/> Minutes vol planeur restantes : '.$total_vol.' ';
		}
		
		$tabModif='<ul>
				<li><strong>Nom: </strong>'.$resultat["nom"].'</li>
				<li><strong>Prenom: </strong>'.$resultat["prenom"].'  </li>
				<li><strong>Age: </strong> '.Age($resultat["date_naissance"]).' ans </li>
				<li><strong>Adresse: </strong>	'.$resultat["adresse"].' </li>
				<li><strong>Code Postal: </strong>	 '.$resultat["cp"].'</li>
				<li><strong>Ville: </strong>	'.$resultat["ville"].'</li>
				<li><strong>Mail: </strong>	'.$resultat["mail"].'</li>
				<li><strong>Téléphone fixe :</strong> '.$resultat["tel_fixe"].'</li>
				<li><strong>Portable: </strong> '.$resultat["tel_mobile"].'</li>
				<li><strong>Sexe: </strong>	'.$resultat["sexe"].'</li>
				<li><strong>Numéro de licence | Date d\'obtention: </strong>'.$resultat["num_licence"].' | '.dateUS2FR($resultat["date_licence"]).' </li>
				<li><strong>Date de la dernière visite médicale: </strong> '.dateUS2FR($resultat["visit_med"]).' </li>
				<li><strong>Message important: </strong></li>
			</ul>';
	
		//Construction de la table informative sur les 5 derniers vols effectuées et calcul des heures de vols.
		$tabVol = ListeVolUtilisateur($id_cvvfr_demande,1);
		//Construction de la table informative sur les 5 derniers mouvements et calcul du solde du compte
		$tabMouv='<table border="1" align="center" style="text-align:center;"> 
		<tr>
			<th>Date</th> 
			<th>Libellé</th> 
			<th>Montant</th>
			<th>Gestionnaire</th>
		</tr>';
		$solde_compte=0.00;

		foreach($resultat3 as $row2){ 
			
			$montant_req=0.00;
			
			switch($row2['type_mouv'])
				{
				case 'debit' : 	$montant_req = $montant_req - $row2['montant'];
								break;
								
				case 'credit' : $montant_req = $montant_req + $row2['montant'];
								break;
				}
			$tabMouv.='<tr>
			<td>'.dateUS2FR($row2['date_mouv']).'</td> 
			<td>'.$row2['description'].'</td> 
			<td>'.$montant_req.' '.SIGLE_MONETAIRE.'</td> 
			<td>'.$row2['nom'].' '.$row2['prenom'].'</td>
			</tr>';
		}
		
		$tabMouv.='</table>';
		$solde_compte=CalculeSoldeCompte($id_demande);
		
		if ($solde_compte >= 0 ) $solde_compte='<b style="color: green;"> '.$solde_compte.' '.SIGLE_MONETAIRE.'</b>';
			else $solde_compte='<b style="color: red;"> '.$solde_compte.' '.SIGLE_MONETAIRE.'</b>';
	
	/*---------------------------------------------------------------------------------------------
		Construction du tableau listant les planneurs utilisé et le temps de vol effectué
	------------------------------------------------------------------------------------------------*/
	
	$tabPlanneur='<table border="1" align="center" style="text-align: center"> <tr><th>Modèle</th><th>Temps de vol effectué</th></tr>';
		foreach($resultat5 as $row3){
			$tabPlanneur.='<tr><td>'.$row3['modele'].'</td><td>'.MinutesAHeuresMinutes($row3['moyenne_planneur']).'</td></tr>';
		}
		$tabPlanneur.='</table>';
		
	
		echo ('<h1> Interface membre  </h1>
				<form method="post" action="#">
				<h2> Mes informations </h2><br/>
				<div id="info_ponctuelle">
					<ul>
						<li> Forfait en cours : '.$info_forfait.' </li>
					</ul>
				</div>
				 '.$tabModif.'
				<p style="text-align:center;" > <input type="submit" name="form_info" value="Modifer mes informations" style="width:25%; padding;10px" /> </p>
				<h2>Mon carnet de bord<i> - mes 5 derniers vols</i></h2>
				<p> Heure de vol planeur au total : '.MinutesAHeuresMinutes($resultat4["moyenne_vol"]).'</p>
				<p> Heure de vol remorqué au total : '.$total_remorquage.' centièmes d\'heures</p>		
				'.$tabVol.'
				<h3>Planeurs utilisés</h3>
				<div>
				'.$tabPlanneur.'
				<p style="text-align:center;" > <input type="submit" name="form_vol" value="Voir les vols effectuées" style="width:25%;padding:10px"/> </p>
				<h2>Mon compte pilote</h2>
				<div style="text-align:center;">
					<p>Solde de votre compte : '.$solde_compte.'</p>
					<p>Vos 5 dernières opérations</p>
					'.$tabMouv.'
				</div>
				<p style="text-align:center"> <input type="submit" name="tab_mouv" value="Voir toutes mes opérations" style="width:25%; padding:10px; text-align: center;"/> </p>
				</div>
				</form>');
		}
}

?>