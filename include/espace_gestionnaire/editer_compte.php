<?php
/*
Kean de Souza

edite_compte.php
Objectif : Modifier les informations personnelles d'un compte pilote par le gestionnaire

Crée le 09/07/15
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
	
	if (isset($_GET['id'])){
		
		if(isset($_POST['valider_modification']))
			{
			$resultat = $reqUserStatutById->execute(array(':id'=>$_GET['id']));
			$resultat = $reqUserStatutById->fetch(); // Récuperer les infos concernant l'utilisateur
			$reqUserStatutById->CloseCursor();
			
			$modif=0; //savoir si il y'a une ou plusieurs modification
			if ( $resultat['nom'] != $_POST['md_nom']){ MajInfoGest($_GET['id'], 'nom', $_POST['md_nom'], $_SESSION['id_gestionnaire'] ); $modif++;}
			if ( $resultat['prenom'] != $_POST['md_prenom']){ MajInfoGest($_GET['id'], 'prenom', $_POST['md_prenom'], $_SESSION['id_gestionnaire'] ); $modif++;}
			if ( $resultat['sexe'] != $_POST['mod_sexe']){ MajInfoGest($_GET['id'], 'sexe', $_POST['mod_sexe'], $_SESSION['id_gestionnaire'] ); $modif++;}
			if ( $resultat['date_naissance'] != $_POST['md_date']){ MajInfoGest($_GET['id'], 'date_naissance', dateFR2US($_POST['md_date']), $_SESSION['id_gestionnaire']) ; $modif++;}
			if ( $resultat['adresse'] != $_POST['md_adr'] ) 	  { MajInfoGest($_GET['id'], 'adresse', $_POST['md_adr'], $_SESSION['id_gestionnaire']); $modif++;}
			if ( $resultat['cp'] != $_POST['md_cp'] ) 	  { MajInfoGest($_GET['id'], 'cp', $_POST['md_cp'], $_SESSION['id_gestionnaire']); $modif++;}
			if ( $resultat['ville'] != $_POST['md_ville']){ MajInfoGest($_GET['id'], 'ville', $_POST['md_ville'], $_SESSION['id_gestionnaire']); $modif++;}
			if ( $resultat['mail'] != $_POST['md_mail'])  { MajInfoGest($_GET['id'], 'mail', $_POST['md_mail'], $_SESSION['id_gestionnaire']); $modif++;}
			if ( $resultat['tel_fixe'] != $_POST['md_fixe']) 		{ MajInfoGest($_GET['id'], 'tel_fixe', $_POST['md_fixe'], $_SESSION['id_gestionnaire']); $modif++;}
			if ( $resultat['tel_mobile'] != $_POST['md_mob']) 		{ MajInfoGest($_GET['id'], 'tel_mobile', $_POST['md_mob'], $_SESSION['id_gestionnaire']) ; $modif++;}
			if ( $resultat['num_licence'] != $_POST['mod_licence']) { MajInfoGest($_GET['id'], 'num_licence', $_POST['mod_licence'], $_SESSION['id_gestionnaire']) ; $modif++;}
			if ( $resultat['date_licence'] != $_POST['mod_date_licence']) { MajInfoGest($_GET['id'], 'date_licence', dateFR2US($_POST['mod_date_licence']), $_SESSION['id_gestionnaire']) ; $modif++;}
			if ( $resultat['visit_med'] != $_POST['mod_visite_med']) 	  { MajInfoGest($_GET['id'], 'visit_med', dateFR2US($_POST['mod_visite_med']), $_SESSION['id_gestionnaire']) ; $modif++;}
			if ( $resultat['comm'] != $_POST['md_com']) 				  { MajInfoGest($_GET['id'], 'comm', $_POST['md_com'], $_SESSION['id_gestionnaire']) ; $modif++;}
			if ( $modif > 1 ) $modif_info='Les modifications se sont bien déroulé';
			elseif ($modif == 1) $modif_info='La modification s\'est bien déroulé';
			MessageAlert($modif_info);
			header('URL: index.php?page=editer_compte_pilote&amp;id='.$_GET['id'].'');
			}
		
		$resultat = $reqUserStatutById->execute(array(':id'=>$_GET['id']));
		$resultat = $reqUserStatutById->fetch(); // Récuperer les infos concernant l'utilisateur
		$reqUserStatutById->CloseCursor();
		
		$select_sexe = '<select style="width:28%;position:relative;left:27px" name="mod_sexe">';
		if (!is_null($resultat["sexe"])){
			$select_sexe.='<option selected="selected "value="'.$resultat["sexe"].'"> '.$resultat["sexe"].' </option>';
			}
		$select_sexe.='<option value="Masculin">Homme </option> <option value="Feminin">Femme </option> </select> ';

		echo '	<form method="post" action="#">
				<h1> Editer un compte pilote  </h1>
				<ul>
					<li><strong>Identifiant CVVFR :</strong> '.$resultat['id_club'].'</li>
					<li><strong>Pseudo :</strong> '.$resultat['pseudo'].'</li>
					<li><strong>Nom :</strong> <input style="width:28%;position:relative;left:60px" type="text" value="'.$resultat["nom"].'" name="md_nom" /> </li> 
					<li><strong>Prenom : </strong> <input style="width:28%;position:relative;left:30px" type="text" value="'.$resultat["prenom"].'" name="md_prenom" />  </li>
					<li><strong>Sexe :</strong>'.$select_sexe.'</li>
					<li><strong>Date de naissance :</strong> <input type="text 	" style="width:28%;position:relative;left:27px" value="'.dateUS2FR($resultat["date_naissance"]).'" name="md_date" /> </li>
					<li><strong>Adresse :</strong> <input style="width:28%;position:relative;left:32px" type="text" value="'.$resultat["adresse"].'" name="md_adr"/> </li>
					<li><strong>Code Postal :</strong> <input style="width:28%" type="text"  value="'.$resultat["cp"].'" name="md_cp"/> </li>
					<li><strong>Ville :</strong> <input style="width:28%;position:relative;left:63px" type="text" value="'.$resultat["ville"].'" name="md_ville"/> </li>
					<li><strong>E-Mail :</strong> <input style="width:28%;position:relative;left:46px" type="text" size="50" value="'.$resultat["mail"].'" name="md_mail"/></li>
					<li><strong>Tél.fixe :</strong> <input style="width:28%;position:relative;left:37px" type="text" value="'.$resultat["tel_fixe"].'" name="md_fixe"/></li>
					<li><strong>Portable :</strong> <input style="width:28%;position:relative;left:24px" type="text" value="'.$resultat["tel_mobile"].'" name="md_mob"/></li>
					<li><strong>Numéro de licence :</strong> <input style="width:28%;position:relative;left:21px" type="text" value="'.$resultat["num_licence"].'" name="mod_licence" /> </li>
					<li><strong>Date d\'obtention :</strong> <input style="width:28%;position:relative;left:37px" type="text" value="'.dateUS2FR($resultat["date_licence"]).'" name="mod_date_licence" /></li>
					<li><strong>Date de la dernière visite médicale :</strong> <input style="width:28%;position:relative;left:21px" type="text" value="'.dateUS2FR($resultat["visit_med"]).'" name="mod_visite_med" /> </li>
					<li><strong>Message important :</strong> <input style="position:relative;left:21px" type="text"  value="'.$resultat["comm"].'" name="md_com"/> </li>
				</ul>
				<input type="submit" name="valider_modification" value="Mettre à jour les informations" />
				</form>
				<a href="index.php?page=editer_compte_pilote"> Retourner au choix du pilote</a> 	';
		
		}
	else {
		$resultat=GetUsersNomPrenomIdClub();
		
		$tabCompte='<table border="1" align="center" style="font-size: 18px; text-align:center;" ><tr><th>ID</th><th>PSEUDO</th><th>NOM</th><th>PRENOM</th></tr>';
		
		foreach($resultat as $row){
			$tabCompte.='<tr> 
				<td> <a href="index.php?page=editer_compte_pilote&amp;id='.$row['id_util'].'"> '.$row['id_club'].'</a></td>
				<td>'.$row['pseudo'].'</td>
				<td>'.$row['nom'].'</td>
				<td>'.$row['prenom'].'</td>
				</tr>';
			}
		$tabCompte.='</table>';
			
		echo'
			<form method="post" action=#>
			
			<h1>Editer un compte pilote</h1>
			'.$tabCompte.'
			<a href="index.php?page=menu"> Retourner au menu</a></i>
			';
	}
	
	}
?>
