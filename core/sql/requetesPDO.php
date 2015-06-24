<?php
/*
|	requetesPDO.php
|	Fichier centralisant les requêtes PHP utilisé par le systèmes.
|
|
|

Crée le O1/04/15 par Kean de Souza (kean.desouza@gmail.com)
Dernière modification le : 01/04/15 par Kean de Souza 

Edité avec Notepad++ 10/01/15 - 17:20:21 ( Je suis Charlie Edition)

*/

//Fonction permettant de se connecter à la base de donnée pour les membres
function ConnectBddUser()
	  {
	//Informations de connextion à la base de donnée
	$dsn = 'mysql:dbname=aeroclub;host=127.0.0.1';
	$user = 'aeroclub_guest';
	$password = 'invite';
	
	try {
		$bdd = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_LOCAL_INFILE=>true));
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$bdd->exec("SET CHARACTER SET utf8");
		} 
		
	catch (PDOException $erreur) 
		{
		if (($erreur->getCode()) == 1045)
			{
			echo "<p> Accès refusé :
			Le compte utilisateur spécifié dans les paramètre n\'existe pas ou le mot de passe est erronné.\n</p>";
			echo 'Message SQL : '.$erreur->getMessage();	
			exit();
			}
		else echo 'Message SQL : '.$erreur->getMessage();
		}
	return $bdd;
	  };
	  
function ConnectBddGestionnaire()
	  {
	//Informations de connextion à la base de donnée
	$dsn = 'mysql:dbname=aeroclub;host=127.0.0.1';
	$user = 'aeroclub_admin';
	$password = 'invite';
	try {
		$bdd = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_LOCAL_INFILE=>true));
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$bdd->exec("SET CHARACTER SET utf8");
		} 
		
	catch (PDOException $erreur) 
		{
		if (($erreur->getCode()) == 1045)
			{
			echo "<p> Accès refusé :
			Le compte utilisateur spécifié dans les paramètre n\'existe pas ou le mot de passe est erronné.\n</p>";
			echo 'Message SQL : '.$erreur->getMessage();	
			exit();
			} 
		else echo 'Message SQL : '.$erreur->getMessage();
		}
	return $bdd;
	  };
	  
$bdd=ConnectBddGestionnaire();

/*--------------------------------------------------
|													|
|				REQUETES SYSTEMES					|
|													|
 ---------------------------------------------------*/

//Requete de sauvegarde des opérations de modification de champs
function AjoutOpeSystem($utilisateur, $table, $champ, $newvaleur) {
	$bdd=ConnectBddUser();
	$reqNewOperations = $bdd->prepare("INSERT INTO operation(id_utilisateur, id_gestionnaire,demande_valid, table_modif, champ_modif, valeur, typ_op, date_op) 
	VALUES(:id_utilisateur, :id_gest ,0, :table, :champ, :valeur, 'Ajout', now())");
	
	$reqNewOperations->bindValue(':id_utilisateur',$utilisateur,PDO::PARAM_STR);
	$reqNewOperations->bindValue(':id_gest', ID_SYS ,PDO::PARAM_STR);
	$reqNewOperations->bindValue(':table',$table ,PDO::PARAM_STR);	
	$reqNewOperations->bindValue(':champ',$champ ,PDO::PARAM_STR);	
	$reqNewOperations->bindValue(':valeur',$newvaleur ,PDO::PARAM_STR);	
	
	if(($reqNewOperations->execute()) == TRUE ){ return TRUE ;} else {return FALSE; }
};

//Requete de sauvegarde des opérations.
function DemandeMajByUser($utilisateur, $table, $champ, $newvaleur, $type) {
	$bdd=ConnectBddUser();
	$reqMajByUser = $bdd->prepare("INSERT INTO operation(id_utilisateur, id_gestionnaire, demande_valid, table_modif, champ_modif, valeur, typ_op, date_op) 
	VALUES(:id_utilisateur, 0,1, :table, :champ, :valeur, :type_ope, now())");
	
	$reqMajByUser->bindValue(':id_utilisateur',$utilisateur,PDO::PARAM_STR);
	$reqMajByUser->bindValue(':table',$table ,PDO::PARAM_STR);	
	$reqMajByUser->bindValue(':champ',$champ ,PDO::PARAM_STR);	
	$reqMajByUser->bindValue(':valeur',$newvaleur ,PDO::PARAM_STR);	
	$reqMajByUser->bindValue(':type_ope',$type ,PDO::PARAM_STR);	
	
	if(($reqMajByUser->execute()) == TRUE ){ return TRUE ;} else {return FALSE; }
};

// AJOUT LE 01/06
function MajInformationUser($utilisateur, $champ, $newvaleur, $id_operation, $id_gestionnaire) {
	$bdd=ConnectBddGestionnaire();
	$req = $bdd->prepare("UPDATE compte_utilisateur SET ".$champ."=:valeur WHERE id_util =:id");
	$req->bindValue(':id',$utilisateur,PDO::PARAM_STR);
	$req->bindValue(':valeur',$newvaleur ,PDO::PARAM_STR);	
	
	$req2=$bdd->prepare("UPDATE operation SET demande_valid=0, id_gestionnaire=:gestionnaire WHERE id_operation=:operation");
	$req2->bindValue(':gestionnaire',$id_gestionnaire,PDO::PARAM_STR);
	$req2->bindValue(':operation',$id_operation ,PDO::PARAM_STR);	
	
	if(($req->execute()) == TRUE && ($req2->execute()) == TRUE ){ return TRUE ;} else {return FALSE; }
};

//AJOUT LE 16/06/15

function GetForfait($id_mouv) {
	$bdd=ConnectBddUser();
	$mouv=$bdd->prepare('SELECT DATE(date_heure_mouv) as debut_forfait, type_tarif FROM mouvement WHERE id_mouv=:idmouv');
	$mouv->bindValue(':idmouv', $id_mouv, PDO::PARAM_INT);
	$mouv->execute();
	$res_mouv = $mouv->fetch(PDO::FETCH_NAMED);
	if(DEBUG_SQL) { echo 'RQ : '. __function__ .' <br/> ' ; print_r($res_mouv); }
	$mouv->CloseCursor();
}
//---------------------------------------------

//AJOUT lE 11/06/15
function GetInformationsUser($id_club) {
	$bdd=ConnectBddUser();
	$req2 = $bdd->prepare('SELECT * FROM compte_utilisateur WHERE id_club=:id');
	$req2->bindValue(':id', $id_club, PDO::PARAM_INT);
	$req2->execute();
	$res2 = $req2->fetch(PDO::FETCH_NAMED);
	if(DEBUG_SQL) { echo '<p>RQ : '. __function__ .' <br/> </p>' ; print_r($res2); }
	$req2->CloseCursor();
	return $res2;
}

function GetInformationsUserIdSYS($id_user) {
	$bdd=ConnectBddGestionnaire();
	$req2 = $bdd->prepare('SELECT * FROM compte_utilisateur WHERE id_util=:id');
	$req2->bindValue(':id', $id_user, PDO::PARAM_INT);
	$req2->execute();
	$res2 = $req2->fetch();
	if(DEBUG_SQL) {echo 'RQ : '. __function__ .' <br/> ' ; print_r($res2); }
	$req2->CloseCursor();
	return $res2;
}

//AJOUT LE 11/06/15$connexion=ConnectBdd()
function GetInformationsVol($id_vol) {
	$bdd=ConnectBddUser();
	$req = $bdd->prepare('SELECT date_vol, id_planeur, date_depart, date_arrivee, TIMESTAMPDIFF(MINUTE,`date_depart`,`date_arrivee`) AS minute_duree, 
	id_remorqueur, duree_vol_remq, type_vol, id_pilote, id_passager,
	aeronefs.tarif_associe as id_tarif_planeur, tarif.tarif_jeune, tarif.tarif_adulte, aero_remq.tarif_associe as id_tarif_remorqueur, 
	remq_tarif.tarif_jeune as remq_age_moins, remq_tarif.tarif_adulte as remq_age_plus 
FROM vol, aeronefs, aeronefs aero_remq, tarif, tarif remq_tarif 
WHERE id_vol=:id AND id_planeur = aeronefs.num_aeronef AND id_remorqueur = aero_remq.num_aeronef AND aeronefs.tarif_associe = tarif.id_tarif AND aero_remq.tarif_associe = remq_tarif.id_tarif  ');
	$req->bindValue(':id', $id_vol, PDO::PARAM_STR);
	$req->execute();
	$res = $req->fetch(PDO::FETCH_NAMED);
	if(DEBUG_SQL) {echo '<p> RQ : '. __function__ .'<br/> </p>' ; print_r($res); }
	$req->CloseCursor();
	return $res;
	}

function ForfaitPlaneur($forfait, $planeur){
	$bdd=ConnectBddGestionnaire();
	$req = $bdd -> prepare('SELECT * FROM config_forfait WHERE tarif_associe=:id_forfait_pilote AND planeur_concerne =:planeur');
	$req -> bindValue(':id_forfait_pilote', $forfait, PDO::PARAM_STR);
	$req -> bindValue(':planeur', $planeur, PDO::PARAM_STR);
	$req -> execute();
	$res = $req -> fetch(PDO::FETCH_NAMED);
	if(DEBUG_SQL) {echo '<p> RQ : ForfaitPlaneur <br/> </p>' ; print_r($res); }
	$req->CloseCursor();
	return $res;
	}
/*--------------------------------------------------
|													|
|		REQUETES SQL FORMULAIRE INSCRIPTION			|
|		& AUTHENTIFICATION							|
|													|
 ---------------------------------------------------*/

	// Verifier que le mot de passe et l'email sont bien dans la base de données.
	$reqVerifUserLogPassByLOG=$bdd->prepare("SELECT * FROM compte_utilisateur WHERE pseudo=:pseudo AND password=:password");
	
	// Rechercher les utilisateurs ayant :mail
	$reqVerifUserbyMail=$bdd->prepare("SELECT * FROM compte_utilisateur WHERE mail=:mail");
	
	//Inserer un nouvel utilsateur avec un bas staut
	$reqNewUser=$bdd->prepare("INSERT INTO compte_utilisateur 
	( date_inscription, pseudo, nom, prenom, profession, sexe, date_naissance, adresse, cp, ville, tel_fixe, tel_mobile, mail, password, id_statut)
	VALUES( NOW(), :pseudo,:nom, :prenom, :profession, :sexe, :date_naissance, :adresse, :cp, :ville, :tel_fixe, :tel_mobile, :mail, :password,1)");
	
/*--------------------------------------------------------------------
---------------------------------------------------------------------- */

/*-------------------------------------------
|											|
|		REQUETES SQL ESPACE MEMBRE			|
|											|
 --------------------------------------------*/
function UpdateMDP ($id_util, $mdp_md5)
	{
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare("UPDATE compte_utilisateur SET password=:new_pass WHERE id_util =:id_user");
	$requete->bindValue(':new_pass', $mdp_md5,PDO::PARAM_STR);	
	$requete->bindValue(':id_user', $id_util,PDO::PARAM_STR);	
	$requete->execute();	
		
	if(($requete->execute()) == TRUE ){ return TRUE ;} else {return FALSE; }
	}

function UpdateStatutUser($id, $statut) {
 //AJOUT LE 29/05  - Permet la mise à jour du statut du membre	
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare("UPDATE compte_utilisateur SET id_statut=:nouveau_statut WHERE id_util =:id_maj");
	$requete->bindValue(':nouveau_statut',$statut,PDO::PARAM_STR);	
	$requete->bindValue(':id_maj',$id,PDO::PARAM_STR);	
	$requete->execute();
	
	if(($requete->execute()) == TRUE ){ return TRUE ;} else {return FALSE; }
	}
	
  //AJOUT LE 08/06/15
function UpdateNouveauUser($id, $statut, $id_club) {
	
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare("UPDATE compte_utilisateur SET id_statut=:nouveau_statut, id_club=:club WHERE id_util =:id_maj");
	$requete->bindValue(':nouveau_statut',$statut,PDO::PARAM_STR);	
	$requete->bindValue(':id_maj',$id,PDO::PARAM_STR);	
	$requete->bindValue(':club',$id_club,PDO::PARAM_STR);	
	
	try {
		if(($requete->execute()) == TRUE ){ return TRUE ;} else {return FALSE; }
		} 
		
	catch (PDOException $e) 
		{
		echo '<p> Erreur : l\'id saisie est déjà utilisé </p>' . $e->getMessage();
		}
	}
	
function GetUsersNomPrenomIdClub() {
	$bdd=ConnectBddGestionnaire();
	$req2 = $bdd->prepare('SELECT id_util, id_club, nom , prenom FROM compte_utilisateur WHERE 1');
	$req2->execute();
	$res2 = $req2->fetchAll(PDO::FETCH_NAMED);
	if(DEBUG_SQL) {echo 'RQ : '.__function__ .' <br/> ' ; print_r($res2); }
	$req2->CloseCursor();
	return $res2;
	}
 
// Recuperer toutes les informations et le statut selon l'id utilisateur	
$reqUserStatutById=$bdd->prepare("SELECT * FROM compte_utilisateur INNER JOIN statut ON compte_utilisateur.id_statut=statut.num_statut WHERE id_util=:id");

//Retourne une liste des vols comme une planche, a integré directement dans la page HTML
function ListeVolUtilisateur($id,$limit=FALSE) {
	
	$bdd=ConnectBddUser();
	switch($limit)
		{	
			case TRUE : 	$req=$bdd->prepare('SELECT id_vol, date_depart, date_arrivee, date_vol, duree_vol_remq, type_vol, id_pilote, id_passager, id_planeur, aeronefs.immat as planneur, id_remorqueur, remorq.immat as remorqueur, compte_utilisateur.nom as cdb, passenger.nom as secondcdb,  TIME(date_depart) AS heure_depart ,TIME(date_arrivee) AS heure_arrivee FROM vol, compte_utilisateur, compte_utilisateur passenger, aeronefs, aeronefs remorq WHERE (id_pilote=:id OR id_passager=:id) AND compte_utilisateur.id_club=id_pilote AND passenger.id_club=id_passager AND aeronefs.num_aeronef=id_planeur AND remorq.num_aeronef=id_remorqueur ORDER BY vol.date_vol DESC LIMIT 0,5');
							break;//Affiche les 5 derniers vols		
			
			case FALSE : 	$req=$bdd->prepare('SELECT id_vol, date_depart, date_arrivee, date_vol, duree_vol_remq, type_vol, id_pilote, id_passager, id_planeur, aeronefs.immat as planneur, id_remorqueur, remorq.immat as remorqueur, compte_utilisateur.nom as cdb, passenger.nom as secondcdb,  TIME(date_depart) AS heure_depart ,TIME(date_arrivee) AS heure_arrivee FROM vol, compte_utilisateur, compte_utilisateur passenger, aeronefs, aeronefs remorq WHERE (id_pilote=:id OR id_passager=:id) AND compte_utilisateur.id_club=id_pilote AND passenger.id_club=id_passager AND aeronefs.num_aeronef=id_planeur AND remorq.num_aeronef=id_remorqueur ORDER BY vol.date_vol DESC');
							break;// Affiche tous les vols
			
			default :  		$req=$bdd->prepare('SELECT id_vol, date_depart, date_arrivee, date_vol, duree_vol_remq, type_vol, id_pilote, id_passager, id_planeur, aeronefs.immat as planneur, id_remorqueur, remorq.immat as remorqueur, compte_utilisateur.nom as cdb, passenger.nom as secondcdb,  TIME(date_depart) AS heure_depart ,TIME(date_arrivee) AS heure_arrivee FROM vol, compte_utilisateur, compte_utilisateur passenger, aeronefs, aeronefs remorq WHERE (id_pilote=:id OR id_passager=:id) AND compte_utilisateur.id_club=id_pilote AND passenger.id_club=id_passager AND aeronefs.num_aeronef=id_planeur AND remorq.num_aeronef=id_remorqueur ORDER BY vol.date_vol DESC');
		}
	
	$req->bindValue(':id',$id,PDO::PARAM_INT);	
	$req->execute();
	$resul=$req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ : '.__function__ .' <br/> ' ; print_r($resul); } 
	$req->CloseCursor();
	
	if (count($resul) < 1) $tab='Aucune information disponible';
	else
		{
		$tab ='<table border="1" style="text-align: center; font-size: 13px;" align="center">
				<tr>
					<th>Date de vol</th>
					<th>Planeur</th>
					<th>Départ</th>
					<th>Arrivée</th>
					<th>Durée du vol</th>
					<th>Prix du vol</th> 
					<th>Remorqueur</th> 
					<th> Durée du remorquage</th>
					<th>Prix du remorquage</th>
					<th>Facturation</th>
					<th>ID - CDB</th>
					<th>ID - Second CDB </th>
					<th>Coût total</th>
				</tr>';
			$total_vol = 0.00;
		foreach($resul as $row)
			{
			$prix=GetMontantRemorqueurEtPlanneur($row['id_vol']);
			$total = $prix['remq_montant'] + $prix['plan_montant'];
			$total_vol = $total_vol + $total; 
			$tab.='<tr>
					<td>'.dateUS2FR($row['date_vol']).'</td> 
					<td>'.$row['planneur'].'</td> 
					<td>'.$row["heure_depart"].'</td> 
					<td>'.$row['heure_arrivee'].'</td> 
					<td>'.dureeVol($row['date_depart'],$row['date_arrivee']).'</td>
					<td>'.$prix['plan_montant'].' '.SIGLE_MONETAIRE.'</td> 
					<td>'.$row['remorqueur'].'</td> 
					<td>'.$row['duree_vol_remq'].'</td>
					<td>'.$prix['remq_montant'].' '.SIGLE_MONETAIRE.'</td> 
					<td>'.$row['type_vol'].'</td> 
					<td>'.$row['id_pilote'].' - '.$row['cdb'].' </td> 
					<td>'.$row['id_passager'].' - '.$row['secondcdb'].'</td>
					<td>'.$total.' '.SIGLE_MONETAIRE.'</td>
				</tr>' ;	
			};
		$tab.='</table>';
		$tab.= '<p>Coût des vols de la saison actuelle : '.$total_vol.' '.SIGLE_MONETAIRE.'</p>';
		}
	return $tab;
}

function TotalRemorquage($id_cvvfr_demande){
	$bdd=ConnectBddUser();
	$annee = date('Y');
	$req=$bdd->prepare("SELECT SUM(duree_vol_remq) as centieme FROM vol WHERE (id_pilote=:id OR id_passager=:id) AND YEAR(date_vol)= :an ");
	$req -> bindValue(':id', $id_cvvfr_demande, PDO::PARAM_STR);
	$req -> bindValue(':an', $annee, PDO::PARAM_STR);
	$req->execute();
	$res = $req->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor();
	if($res['centieme'] === 0) return 0;
	else return $res['centieme'];
	}

function CalculeSoldeCompte($id) {
	
	$bdd=ConnectBddUser();
	$req=$bdd->prepare('SELECT SUM(montant) FROM mouvement WHERE id_client=:id AND type_mouv="debit"');
	$req->bindValue(':id',$id, PDO::PARAM_STR);
	$req->execute();
	$debit=$req->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($debit);}
	$req->CloseCursor();
	
	$req=$bdd->prepare('SELECT SUM(montant) FROM mouvement WHERE id_client=:id AND type_mouv="credit"');
	$req->bindValue(':id',$id, PDO::PARAM_STR);
	$req->execute();
	$credit=$req->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($debit);}
	$req->CloseCursor();
	
	$valeur = $credit[0] - $debit[0];
	
	$valeur = round($valeur, 2,PHP_ROUND_HALF_DOWN);
	
	return $valeur;
	}
	
//Calculer la somme (moyenne_vol) de tous les vols planés effectué depuis la création du compte
$reqMoyVolPlanneurByIdId=$bdd->prepare("SELECT SUM(TIMESTAMPDIFF(MINUTE,`date_depart`,`date_arrivee`)) AS moyenne_vol 
FROM vol 
INNER JOIN aeronefs ON vol.id_planeur=aeronefs.num_aeronef WHERE id_pilote=:id OR id_passager=:id AND aeronefs.type=0 ");

//Rechercher et trier par date (date_mouv) les mouvements avec les informations du gestionnaire  selon l'id du client
$reqMouvementUserById=$bdd->prepare("SELECT *, DATE(`date_heure_mouv`) AS date_mouv 
FROM mouvement 
INNER JOIN compte_utilisateur ON mouvement.id_gestionnaire = compte_utilisateur.id_util  INNER JOIN tarif ON mouvement.type_tarif=tarif.id_tarif  
WHERE id_client=:id ORDER BY date_mouv DESC");

//Rechercher et trier par date (date_mouv) les 5 derniers mouvements selon l'id
$reqMouvementUser5ById=$bdd->prepare("SELECT *, DATE(`date_heure_mouv`) AS date_mouv 
	FROM mouvement 
	INNER JOIN compte_utilisateur ON mouvement.id_gestionnaire = compte_utilisateur.id_util  INNER JOIN tarif ON mouvement.type_tarif=tarif.id_tarif  
	WHERE id_client=:id ORDER BY date_mouv DESC LIMIT 0,5");

//Calculer le temps de vol total pour chaque planneur utilisé
$reqMoyVolForEachPlanneurByIdId=$bdd->prepare("SELECT modele, SUM(TIMESTAMPDIFF(MINUTE,`date_depart`,`date_arrivee`)) AS moyenne_planneur 
FROM vol 
INNER JOIN aeronefs ON vol.id_planeur=aeronefs.num_aeronef 
WHERE id_pilote=:id OR id_passager=:id AND aeronefs.type=0 GROUP BY aeronefs.modele");

// Savoir si des modifications sont en attentes.
$reqModifWaitUserById=$bdd->prepare("SELECT champ_modif, valeur FROM operation WHERE id_utilisateur=:id AND demande_valid=1");

function CalculResteForfait($id_mouv)
	{
	$bdd=ConnectBddUser();
	$req = $bdd->prepare("SELECT TIMESTAMPDIFF(MINUTE,`date_depart`,`date_arrivee`) as duree_vol, id_vol, type_vol FROM vol WHERE date_vol BETWEEN (SELECT DATE(date_heure_mouv) as date_forfait FROM mouvement WHERE id_mouv=:id_mv) and DATE(NOW()) ORDER BY id_vol DESC");
	$req -> bindValue(':id_mv', $id_mouv, PDO::PARAM_INT);
	$req->execute();
	$res = $req ->fetchAll(PDO::FETCH_NAMED);
	$req->CloseCursor();
	
	$heures_gratuites = SEUIL_HEURES_GRATUITES * 60;
	$total_volgratuit= 0;
	$total_vol = 0;
	
	$req2 = $bdd->prepare("SELECT TIME_TO_SEC(heure_forfait)/60 as duree_forfait FROM config_forfait WHERE tarif_associe = (SELECT type_tarif FROM mouvement WHERE id_mouv = :id_mv) GROUP BY duree_forfait");
	$req2 -> bindValue(':id_mv', $id_mouv, PDO::PARAM_STR);
	$req2->execute();
	$res2 = $req ->fetch(PDO::FETCH_NAMED);
	$req2->CloseCursor();
	
	foreach($res as $row) {	echo 	'<br/>Valeur :'.$row['duree_vol'].'<br/>' ;
		if($row['duree_vol'] > $heures_gratuites ) {
			$dif_volgratuit = $row['duree_vol'] - $heures_gratuites;
			$total_volgratuit = $total_volgratuit + $dif_volgratuit;
			$total_vol = $total_vol + $heures_gratuites ;
			//echo $row['id_vol'].'-'.$row['duree_vol'].' - duree du vol gratuit : '.$dif_volgratuit.'<br/>';	
			}
		else {
			echo $row['id_vol'].'-'.$row['duree_vol'].'<br/>';
			$total_vol = $total_vol + $row['duree_vol'] ;
			}
		}
	 if (DEBUG) echo '<p>Somme de tous les vols : '.$total_vol.' minutes -  Temps de vol gratuit :'.$total_volgratuit.' min</p>';
	 $total_reste = $res2['duree_forfait'] -  $total_vol ;
	return $total_reste;
	}
	
	
function GetForfaitDescription ($id_mouv) {
	$bdd=ConnectBddUser();
	$req=$bdd->prepare('SELECT description FROM tarif WHERE id_tarif = (SELECT type_tarif FROM mouvement WHERE id_mouv = :id_mv)');
	$req->bindValue(':id_mv',$id_mouv, PDO::PARAM_INT);
	$req->execute();
	$res=$req->fetch(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor();
	return $res;
}
/*--------------------------------------------------------------------
---------------------------------------------------------------------- */
// AJOUT LE 26/05

/*-------------------------------------------
|											|
|		REQUETES SQL ESPACE GESTIONNAIRE	|
|											|
 --------------------------------------------*/
 
function InfoUsers() {	 
	$bdd=ConnectBddUser();
	$req= $bdd->prepare('SELECT id_util, id_club, nom, prenom, date_naissance FROM compte_utilisateur WHERE 1 ORDER BY id_club ASC');
	$req->execute();
	$res = $req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor(); 
	return $res;
	}
 
function GetUserByNom($nom_saisie) {
	$bdd=ConnectBddGestionnaire();
	$req = $bdd->prepare(' SELECT * FROM compte_utilisateur WHERE nom=:nom_entre ');
	$req-> bindValue(':nom_entre', $nom_saisie, PDO::PARAM_STR);
	$req->execute();
	$res=$req->fetchAll();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor();
	return $res;
	};

function VerificationIdClub($id_soumis) {
//AJOUT LE 08/06/15	- Vérifier si l'identifiant Club est disponible
	
	$bdd=ConnectBddGestionnaire();
	$req = $bdd->prepare('SELECT nom FROM compte_utilisateur WHERE id_club=:id');
	$req-> bindValue(':id', $id_soumis, PDO::PARAM_STR);
	try {
	$req->execute();
	} catch ( PDOException $erreur) {
		if (($erreur->getCode()) === '23000')
			{
			echo "<p>Les identifiants pilotes ou les immatriculations référencées dans la planche n'ont pas été trouvé dans la base de donnée, 
			l'ajout de la planche a échoué...\n</p>";
			echo $erreur->getMessage();	
			exit();
			} 
	} finally {
	$res=$req->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor();
	}
	
	if(empty($res['nom'])) return true;
	else return false;
	}
	
function SelectUsers() {
 //AJOUT LE 8/06/15 - Retourne un type select en html permetant de sélectionner un membre
	  
	$tab = InfoUsers();
	
	$select='<select id="sel_usr" name="select_user"> <option value=""> Aucune sélection</option>';
	
	foreach ($tab as $valeur)
		{	
		$annee = date('Y');
		$age_user = Age($valeur['date_naissance']);
		if ( $age_user == $annee ) $age_user='non renseigné - ';
		$select.= '<option value="'.$valeur['id_util'].'"> '.$valeur['id_club'].' - '.$valeur['nom'].' '.$valeur['prenom'].' - '.$age_user.' ans</option>';
		}	
	$select.='</select>';
	return $select;
	}

function MembreSelonAgeSeuil($signe) {
	$bdd=ConnectBddGestionnaire();
	switch($signe) {
		case '+' : $req = $bdd->prepare('SELECT id_util, id_club, nom, prenom FROM compte_utilisateur WHERE FLOOR(DATEDIFF(CURRENT_DATE(),date_naissance ) / 365.25) > :cst '); 
					break;
		case '-' : $req = $bdd->prepare('SELECT id_util, id_club, nom, prenom FROM compte_utilisateur WHERE FLOOR(DATEDIFF(CURRENT_DATE(),date_naissance ) / 365.25) < :cst '); 
					break;
		default : $req = $bdd->prepare('SELECT id_util, id_club, nom, prenom FROM compte_utilisateur WHERE FLOOR(DATEDIFF(CURRENT_DATE(),date_naissance ) / 365.25) > :cst '); 
					break;
		}
	
	$req-> bindValue(':cst', SEUIL_AGE, PDO::PARAM_STR);
	$req->execute();
	$res=$req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor();
	
	return $res;
	}
	
function MembreDesinscrit() {
	$bdd=ConnectBddGestionnaire();
	$req = $bdd -> prepare('SELECT id_util, id_club, nom, prenom FROM compte_utilisateur WHERE id_statut= :cst');
	$req-> bindValue(':cst', MB_DESINSCRIT, PDO::PARAM_STR);
	$req->execute();
	$res=$req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);} 
	$req->CloseCursor();
	
	return $res;
	}
	
function SelectAeronefs() {
 //AJOUT LE 12/06/15 - Retourne un type select en html permetant de sélectionner un aeronef
	  
	$bdd=ConnectBddGestionnaire();
	$req= $bdd->prepare('SELECT num_aeronef, modele FROM aeronefs WHERE 1');
	$req->execute();
	$tab=$req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$req->CloseCursor();
	
	$select='<select name="select_aeronef" selected> <option value=""> Aucune sélection</option>';
	foreach ($tab as $valeur)
		{	
		$select.= '<option value="'.$valeur['num_aeronef'].'">'.$valeur['modele'].'</option>';
		}	
	$select.='</select>';
	return $select;
	}
	
function SelectPlanchesVol() {
// AJOUT LE 14/06/15
	$bdd=ConnectBddGestionnaire();
	$req= $bdd->prepare('SELECT date_vol FROM vol WHERE 1 GROUP BY date_vol ORDER BY date_vol DESC');
	$req->execute();
	$tab=$req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$req->CloseCursor();
	
	$select='<select name="select_date_vol" > <option value=""> Aucune sélection</option>';
	foreach ($tab as $valeur)
		{	
		$select.= '<option value="'.$valeur['date_vol'].'">'.dateUS2FR($valeur['date_vol']).'</option>';
		}	
	$select.='</select>';
	return $select;
	}
	
function VoirPlancheVol($date)	{
	$bdd=ConnectBddGestionnaire();
	$req= $bdd->prepare('SELECT id_vol, date_depart, date_arrivee, date_vol,duree_vol_remq, type_vol, id_pilote, id_passager, id_planeur, 
	aeronefs.immat as planneur, id_remorqueur, remorq.immat as remorqueur, compte_utilisateur.nom as cdb, passenger.nom as secondcdb,  
	TIME(`date_depart`) AS heure_depart ,TIME(`date_arrivee`) AS heure_arrivee 
	FROM vol, compte_utilisateur, compte_utilisateur passenger, aeronefs, aeronefs remorq 
	WHERE vol.date_vol = :date_vol 
	AND compte_utilisateur.id_club=`id_pilote` 
	AND passenger.id_club=`id_passager` 
	AND aeronefs.num_aeronef=`id_planeur` 
	AND remorq.num_aeronef=`id_remorqueur`
	ORDER BY vol.date_depart DESC');
	$req->bindValue(':date_vol', $date, PDO::PARAM_STR);
	$req->execute();
	$resul=$req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($resul);}
	$req->CloseCursor();
	
	if (count($resul) == 0) $tab='Aucune information disponible';
	else
		{
		$tab ='<table border="1" style="text-align:center; font-size:14px;" align="center" > <tr>
		<th>Date de vol</th>
		<th>Planeur</th> 
		<th>Départ</th> 
		<th>Arrivée</th> 
		<th>Durée du vol</th>
		<th>Prix du vol</th> 
		<th>Remorqueur</th>
		<th>Durée du remorquage</th> 
		<th>Prix du remorquage</th> 
		<th>Facturation</th> 
		<th>ID - CDB</th> 
		<th>ID - Second CDB </th> 
		<th>Coût total</th>
		</tr>';
		
		foreach($resul as $row)
			{
			$prix=GetMontantRemorqueurEtPlanneur($row['id_vol']);
			$total = $prix['remq_montant'] + $prix['plan_montant'];
			$tab.='<tr>
			<td style="white-space:normal;">'.dateUS2FR($row['date_vol']).'</td> 
			<td style="white-space:normal;">'.$row['planneur'].'</td> 
			<td style="white-space:normal;">'.$row["heure_depart"].'</td> 
			<td style="white-space:normal;">'.$row['heure_arrivee'].'</td> 
			<td style="white-space:normal;">'.dureeVol($row['date_depart'],$row['date_arrivee']).'</td>
			<td style="white-space:normal;">'.$prix['plan_montant'].' '.SIGLE_MONETAIRE.'</td> <td>'.$row['remorqueur'].'</td> 
			<td style="white-space:normal;">'.$row['duree_vol_remq'].'</td> 
			<td style="white-space:normal;">'.$prix['remq_montant'].' '.SIGLE_MONETAIRE.'</td> 
			<td style="white-space:normal;">'.$row['type_vol'].'</td> 
			<td style="white-space:normal;">'.$row['id_pilote'].' - '.$row['cdb'].' </td> 
			<td style="white-space:normal;">'.$row['id_passager'].' - '.$row['secondcdb'].'</td>
			<td style="white-space:normal;">'.$total.' '.SIGLE_MONETAIRE.'</td>
			</tr>' ;	
			}
			
		$tab.='</table>';
		}
	return $tab;
	}
	
function GetMontantRemorqueurEtPlanneur($id_vol)	{
	$bdd=ConnectBddUser();
	$req=$bdd-> prepare('SELECT mouvement.montant as plan_montant, rmq.montant as remq_montant
			FROM vol,mouvement, aeronefs av_plan, aeronefs av_rmq, mouvement rmq
			WHERE vol.id_vol = :id
			AND mouvement.n_vol = vol.id_vol
			AND rmq.n_vol = vol.id_vol
			AND av_plan.num_aeronef = vol.id_planeur
			AND av_rmq.num_aeronef = vol.id_remorqueur
			AND mouvement.type_tarif = av_plan.tarif_associe
			AND rmq.type_tarif = av_rmq.tarif_associe');
	$req->bindValue(':id', $id_vol, PDO::PARAM_INT);
	$req->execute();
	$resul=$req->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($resul);}
	$req->CloseCursor();
	return $resul;
	}
	
function Information1Utilisateur($id_soumis) {
 //AJOUT LE 8/06/15 - Obtenir les infos des utilisateurs
	
	$bdd=ConnectBddUser();
	$req= $bdd->prepare('SELECT id_club, nom, prenom FROM compte_utilisateur WHERE id_util=:id');
	$req->bindValue(':id', $id_soumis, PDO::PARAM_INT);
	$req->execute();
	$tab=$req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$req->CloseCursor();
	return $tab;
	}
	
function JournalDesMouvements() {
//Afficher le journal des mouvements
// Retourne un tableau selon les valeurs spécifié dans la requête
// AJOUT LE 27/05	
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare("SELECT id_mouv, type_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv, mode_paie, montant, id_client,compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE YEAR(date_heure_mouv)= (SELECT YEAR(NOW())) AND mouvement.id_client=compte_utilisateur.id_util AND mouvement.id_gestionnaire=compte.id_util AND mouvement.type_tarif=tarif.id_tarif ORDER BY id_mouv DESC") ; // Sélectionne les mouvements de l'année civile en cours
	$requete->execute();
	$tab=$requete->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$requete->CloseCursor();
	return $tab;
	}

function RechercheMouvementParNom($nom) {
// Retourne un tableau listant les mouvements de l'utilisateur spécifié nom
// Sur l'année civile actuelle
	
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare(" SELECT id_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv, type_mouv, mode_paie, montant, id_client,compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE mouvement.id_client= (SELECT id_util FROM compte_utilisateur WHERE nom = :nom_saisie) AND YEAR(date_heure_mouv)= (SELECT YEAR(NOW())) AND mouvement.id_gestionnaire=compte.id_util AND mouvement.type_tarif=tarif.id_tarif AND compte_utilisateur.id_util = mouvement.id_client ORDER BY id_mouv DESC") ; // Sélectionne les mouvements de l'année civile en cours
	$requete->bindValue(':nom_saisie',$nom,PDO::PARAM_STR);
	$requete->execute();
	$tab=$requete->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$requete->CloseCursor();
	return $tab;		
	}
	
function RechercheMouvementParIdMouvement($id) {
// Retourne un tableau listant les mouvements de l'utilisateur spécifié nom
// Sur l'année civile actuelle
	
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare(" SELECT id_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv, type_mouv, mode_paie, montant, id_client, compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE id_mouv=:id AND id_gestionnaire=compte.id_util AND id_client=compte_utilisateur.id_util AND type_tarif=id_tarif ORDER BY id_mouv DESC") ; // Sélectionne les mouvements de l'année civile en cours
	$requete->bindValue(':id',$id,PDO::PARAM_STR);
	$requete->execute();
	$tab=$requete->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$requete->CloseCursor();
	return $tab;		
	}
	
function RechercheMouvementParID($id, $date = NULL) {
// $date doit etre nulle ($date=NULL)
// Retourne un tableau listant les mouvements de l'utilisateur par ID
// Sur une date donnée, sinon sur l'année
	
	$bdd=ConnectBddGestionnaire();
	if ( is_null($date))
		{
		$date=date('Y');
		$requete = $bdd->prepare("SELECT id_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv, type_mouv, mode_paie, montant, id_client,compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE mouvement.id_client= :id_saisie AND YEAR(date_heure_mouv)=:annee AND mouvement.id_gestionnaire=compte.id_util AND mouvement.type_tarif=tarif.id_tarif AND compte_utilisateur.id_util = mouvement.id_client ORDER BY id_mouv DESC") ; // Sélectionne les mouvements de l'année civile en cours
		$requete->bindValue(':id_saisie',$id,PDO::PARAM_STR);
		$requete->bindValue(':annee',$date,PDO::PARAM_STR);
		}
	else
		{
		$requete = $bdd->prepare("SELECT id_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv, type_mouv, mode_paie, montant, id_client,compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE mouvement.id_client= :id_saisie AND DATE(date_heure_mouv)=:date AND mouvement.id_gestionnaire=compte.id_util AND mouvement.type_tarif=tarif.id_tarif AND compte_utilisateur.id_util = mouvement.id_client ORDER BY id_mouv DESC ") ; // Sélectionne les mouvements de l'année civile en cours
		$requete->bindValue(':id_saisie',$id,PDO::PARAM_STR);
		$requete->bindValue(':date',$date,PDO::PARAM_STR);	
		}
	$requete->execute();
	$tab=$requete->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$requete->CloseCursor();
	return $tab;		
}

function RechercheMouvementParClubID($id, $date = NULL) {
// $date doit etre nulle ($date=NULL)
// Retourne un tableau listant les mouvements de l'utilisateur par l'id du club
// Sur une date donnée, sinon sur l'année
	
	$bdd=ConnectBddGestionnaire();
	if ( is_null($date))
		{
		$date=date('Y');
		$requete = $bdd->prepare("SELECT id_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv, type_mouv, mode_paie, montant, id_client,compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE compte_utilisateur.id_club = :id_saisie AND YEAR(date_heure_mouv)= :annee AND mouvement.id_gestionnaire=compte.id_util AND mouvement.type_tarif=tarif.id_tarif AND compte_utilisateur.id_util = mouvement.id_client ORDER BY id_mouv DESC") ; // Sélectionne les mouvements de l'année civile en cours
		$requete->bindValue(':id_saisie',$id,PDO::PARAM_STR);
		$requete->bindValue(':annee',$date,PDO::PARAM_STR);
		}
	else
		{
		$requete = $bdd->prepare("SELECT id_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv,  type_mouv, mode_paie, montant, id_client,compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE compte_utilisateur.id_club = :id_saisie AND DATE(date_heure_mouv)=:date AND mouvement.id_gestionnaire=compte.id_util AND mouvement.type_tarif=tarif.id_tarif AND compte_utilisateur.id_util = mouvement.id_client ORDER BY id_mouv DESC") ; // Sélectionne les mouvements de l'année civile en cours
		$requete->bindValue(':id_saisie',$id,PDO::PARAM_STR);
		$requete->bindValue(':date',$date,PDO::PARAM_STR);	
		}
	$requete->execute();
	$tab=$requete->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$requete->CloseCursor();
	return $tab;		
}

function SelectionnerMouvement($id_mouv) {
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare(" SELECT * FROM mouvement WHERE id_mouv=:mouv") ; // Sélectionne un mouvement selon son identifiant
	$requete->bindValue(':mouv',$id_mouv, PDO::PARAM_STR);	
	$requete->execute();
	$tab=$requete->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$requete->CloseCursor();
	return $tab;
}

function RechercheMouvementParJournee($jour){
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare(" SELECT id_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv, type_mouv, mode_paie, montant, id_client,compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE DATE(date_heure_mouv)=:date AND mouvement.id_gestionnaire=compte.id_util AND mouvement.type_tarif=tarif.id_tarif AND compte_utilisateur.id_util = mouvement.id_client ORDER BY id_mouv DESC") ; // Sélectionne les mouvements de l'année civile en cours
	$requete->bindValue(':date',$jour,PDO::PARAM_STR);	
	$requete->execute();
	$tab=$requete->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$requete->CloseCursor();
	return $tab;
}

function RechercheMouvementParAn($an){
	$bdd=ConnectBddGestionnaire();
	$requete = $bdd->prepare(" SELECT id_mouv, DATE(date_heure_mouv) as date_mouv, TIME(date_heure_mouv) as heure_mouv, type_mouv, mode_paie, montant, id_client,compte_utilisateur.nom as client, id_gestionnaire, compte.nom as gestionnaire, type_tarif, description, n_vol, comm FROM mouvement, compte_utilisateur, compte_utilisateur compte, tarif WHERE YEAR(date_heure_mouv)=:annee AND mouvement.id_gestionnaire=compte.id_util AND mouvement.type_tarif=tarif.id_tarif AND compte_utilisateur.id_util = mouvement.id_client ORDER BY id_mouv DESC") ; // Sélectionne les mouvements de l'année civile en cours
	$requete->bindValue(':annee',$an,PDO::PARAM_STR);	
	$requete->execute();
	$tab=$requete->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$requete->CloseCursor();
	return $tab;	
	}

function RechercheMembreAValider() {
	$bdd=ConnectBddGestionnaire();
	$reqalerte=$bdd->prepare('SELECT DATE(date_inscription) as date_inscription, TIME(date_inscription) as heure_inscription, id_util, nom, prenom FROM compte_utilisateur WHERE id_statut=1');
	$reqalerte->execute();
	$res=$reqalerte->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$reqalerte->CloseCursor();
	return $res;
	}
	
function RechercheInformationAValider() {
	$bdd=ConnectBddGestionnaire();
	$reqalerte=$bdd->prepare('SELECT * FROM operation INNER JOIN compte_utilisateur ON id_util = id_utilisateur WHERE demande_valid=1 ');
	$reqalerte->execute();
	$res=$reqalerte->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$reqalerte->CloseCursor();
	return $res;
	}

function ListerTarifs() {
	$bdd=ConnectBddGestionnaire();
	$req = $bdd -> prepare('SELECT * FROM tarif');
	$req->execute();
	$res=$req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor();
	return $res;
}

function AjouterMouvement($id_utilisateur, $id_gestionnaire, $type_mouvement, $id_tarif, $mode, $comm, $montant=NULL) {
	$bdd=ConnectBddGestionnaire();
	$requete=$bdd->prepare('INSERT INTO mouvement (date_heure_mouv, type_mouv, mode_paie, montant, id_client, id_gestionnaire, type_tarif, comm) 
	VALUES( now(), :type, :mode_paiement, :somme , :idc, :idg, :idtarif, :commentaire )');
	
	$requete->bindValue(':mode_paiement',$mode,PDO::PARAM_STR);	
	$requete->bindValue(':type',$type_mouvement,PDO::PARAM_STR);	
	$requete->bindValue(':somme',$montant,PDO::PARAM_STR);	
	$requete->bindValue(':idc',$id_utilisateur,PDO::PARAM_STR);
	$requete->bindValue(':idg',$id_gestionnaire,PDO::PARAM_STR);
	$requete->bindValue(':idtarif',$id_tarif,PDO::PARAM_STR);	
	$requete->bindValue(':commentaire',$comm,PDO::PARAM_STR);
	
	if(($requete->execute()) == TRUE ){return TRUE ;} else {return FALSE; }
}

function AjouterMouvementVol($id_utilisateur, $id_gestionnaire, $type_mouvement, $id_tarif, $mode, $comm, $id_vol, $montant) {
	$bdd=ConnectBddGestionnaire();
	$requete=$bdd->prepare('INSERT INTO mouvement (date_heure_mouv, type_mouv, mode_paie, montant, id_client, id_gestionnaire, type_tarif, n_vol, comm) 
	VALUES( now(), :type, :mode_paiement, :somme , :idc, :idg, :idtarif, :idvol, :commentaire )');
	
	$requete->bindValue(':mode_paiement',$mode,PDO::PARAM_STR);	
	$requete->bindValue(':type',$type_mouvement,PDO::PARAM_STR);	
	$requete->bindValue(':somme',$montant,PDO::PARAM_STR);	
	$requete->bindValue(':idc',$id_utilisateur,PDO::PARAM_STR);
	$requete->bindValue(':idg',$id_gestionnaire,PDO::PARAM_STR);
	$requete->bindValue(':idtarif',$id_tarif,PDO::PARAM_INT);
	$requete->bindValue(':idvol',$id_vol,PDO::PARAM_INT);	
	$requete->bindValue(':commentaire',$comm,PDO::PARAM_STR);
	
	if(($requete->execute()) == TRUE ){return TRUE ;} else {return FALSE; }
	}

function MajMouvement(int $id_gestionnaire,string $type_mouv,int $id_tarif,string $mode,string $comm,string $mouv,float $montant=NULL) {
	$bdd=ConnectBddGestionnaire();
	$requete=$bdd->prepare('UPDATE mouvement SET date_heure_mouv=now(), mode_paie=:mode_paiement, type_mouv=:mouv_typ, montant=:somme, id_gestionnaire=:idg, type_tarif=:idtarif, comm=:commentaire WHERE id_mouv=:mouvement');
	
	$requete->bindValue(':mode_paiement',$mode,PDO::PARAM_STR);		
	$requete->bindValue(':mouv_typ',$type_mouv,PDO::PARAM_STR);	
	$requete->bindValue(':somme',$montant,PDO::PARAM_FLOAT);	
	$requete->bindValue(':idg',$id_gestionnaire,PDO::PARAM_INT);
	$requete->bindValue(':idtarif',$id_tarif,PDO::PARAM_INT);	
	$requete->bindValue(':commentaire',$comm,PDO::PARAM_STR);
	$requete->bindValue(':mouvement',$mouv,PDO::PARAM_INT);	
	
	if(($requete->execute()) == TRUE ){ return TRUE ;} else {return FALSE; }
}

function MAJForfaitUser($id_utilisateur,$mouvement) {
	$bdd=ConnectBddGestionnaire();
	$requete=$bdd->prepare('UPDATE compte_utilisateur SET mouvement_forfait =:id_mouv WHERE id_util=:id_client');
	
	$requete->bindValue(':id_mouv',$mouvement,PDO::PARAM_INT);	
	$requete->bindValue(':id_client',$id_utilisateur,PDO::PARAM_INT);		
	
	if(($requete->execute()) == TRUE ){ return TRUE ;} else {return FALSE; }
	
}

function SupprimerMouvement($id_mouv) {
	$bdd=ConnectBddGestionnaire();
	$requete=$bdd->prepare('DELETE FROM mouvement WHERE id_mouv=:mouvement');
	$requete->bindValue(':mouvement',$id_mouv,PDO::PARAM_INT);	
	try {
	$requete->execute();
	} catch ( PDOException $erreur) {
		if (($erreur->getCode()) === '23000')
			{
			echo "<p>Le mouvement que vous essayer de supprimer est actuellement un forfait ou un vol effectué associé au pilote... </p>";
			//echo $erreur->getMessage();	
			return FALSE;
			}
		}
	return true;
	}
	
function GetIDPlaneur($immat) {
	$bdd = ConnectBddGestionnaire();
	$req = $bdd -> prepare('SELECT num_aeronef FROM aeronefs WHERE immat = :immat_saisi');
	$req->bindValue(':immat_saisi', $immat, PDO::PARAM_STR);
	$req->execute();
	$res = $req->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req -> CloseCursor();
	return $res;
}

function GetConfigForfait($mouvement, $planeur)	{
	$bdd=ConnectBddGestionnaire();
	$req= $bdd -> prepare('SELECT TIME_TO_SEC(heure_forfait)/60 as duree_forfait, tarif_planeur_jeune, tarif_planeur_adulte, supplement, DATE(date_heure_mouv) AS debut_forfait
		FROM config_forfait, mouvement
		WHERE planeur_concerne = :id_planeur 
		AND id_mouv= :id_mv
		AND tarif_associe = (SELECT type_tarif FROM mouvement WHERE id_mouv= :id_mv ) ');
	$req->bindValue(':id_mv',$mouvement,PDO::PARAM_INT);	
	$req->bindValue(':id_planeur',$planeur,PDO::PARAM_INT);	
	$req -> execute();
	$res = $req->fetch(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor();
	return $res;
	
	}

function MAJCMP($id_util, $cmp) {
	$bdd=ConnectBddGestionnaire();
	$req = $bdd -> prepare('UPDATE compte_utilisateur SET cmp_regle = :cmp WHERE id_util = :id');
	$req -> bindValue(':cmp', $cmp, PDO::PARAM_STR);
	$req -> bindValue(':id', $id_util, PDO::PARAM_INT);
	$req -> execute();
	if (DEBUG_SQL) {echo '<br/> RQ '. __function__ .' <br/>' ; }
	$req->CloseCursor();
	return $req;
	}
	
/*-------------------------------------------
|											|
|		REQUETES SQL Administrateur			|
|											|
 --------------------------------------------*/
 
 
$reqAffTarifs=$bdd->prepare("SELECT `code_barre`,`description`,`tarif_jeune`,`tarif_adulte`FROM `tarif` ");
$reqModTarifs=$bdd->prepare("SELECT * FROM tarif where code_barre =:id");
$reqMiseJourTarifs=$bdd->prepare ('UPDATE `tarif` set `tarif_jeune`=:val where code_barre=:id');
$reqMiseJourTarifs2=$bdd->prepare ('UPDATE `tarif` set `tarif_adulte`=:val where code_barre=:id');
$reqSuppresion=$bdd->prepare('DELETE FROM `compte_utilisateur` WHERE id_club=:id');
$reqNewMembre=$bdd->prepare("select * from compte_utilisateur where id_statut= 1") ;

$reqinfo=$bdd->prepare("select * from compte_utilisateur where id_club=:id");

function GetInfoForfaitTarif($id_tarif){
	$bdd = ConnectBddGestionnaire();
	$req = $bdd->prepare('SELECT * FROM tarif WHERE id_tarif = :id');
	$req -> bindValue(':id', $id_tarif, PDO::PARAM_INT);
	$req -> execute();
	$res = $req->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' '.$id_tarif.' <br/>' ; print_r($res);}
	$req->CloseCursor();
	return $res;
	}
	
function GetInfoForfaitConfig($id_tarif_cf){
	$bdd = ConnectBddGestionnaire();
	$req = $bdd->prepare('SELECT id, heure_forfait AS heure, config_forfait.tarif_associe, tarif_planeur_jeune, tarif_planeur_adulte, supplement, aeronefs.modele, planeur_concerne 
	FROM config_forfait, aeronefs 
	WHERE config_forfait.tarif_associe = :id AND aeronefs.num_aeronef = config_forfait.planeur_concerne ');
	$req -> bindValue(':id', $id_tarif_cf, PDO::PARAM_INT);
	$req -> execute();
	$res = $req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' '.$id_tarif_cf.' <br/>' ; print_r($res);}
	$req->CloseCursor();
	return $res;
	}
	


/*-------------------------------------------
|											|
|	   REQUETES SQL Super_Administrateur	|
|											|
 --------------------------------------------*/
 
function SelectStatut(){
		$bdd=ConnectBddGestionnaire();
		$req= $bdd->prepare('SELECT * FROM statut where num_statut < :statut ');
		$req -> bindValue(':statut', STATUT_ACCES_SA, PDO::PARAM_INT);
		$req->execute();
		$tab=$req->fetchAll(PDO::FETCH_NAMED);
		if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
		$req->CloseCursor();
		
		$selectS='<select name="select_stat"> <option value=""> Aucune sélection</option>';
		foreach ($tab as $valeur)
			{	
			$selectS.= '<option value="'.$valeur['num_statut'].'"> '.$valeur['nom_statut'].'</option>';
			}	
		$selectS.='</select>';
		return $selectS;
		}
		
		
function AfficherOpe($table, $date=NULL)	{
	$bdd = ConnectBddGestionnaire();
	
	if(is_null($date)) {
			$req = $bdd -> prepare('SELECT id_operation, id_utilisateur, client.nom as cli_nom, client.prenom as cli_pre, id_gestionnaire, gestionnaire.nom as gest_nom, gestionnaire.prenom as gest_prenom, demande_valid, table_modif, champ_modif, valeur, typ_op, DATE(date_op) as date_operation 
		FROM operation, compte_utilisateur client, compte_utilisateur gestionnaire 
		WHERE table_modif =:table
		AND client.id_util = id_utilisateur
		AND gestionnaire.id_util = id_gestionnaire 
		ORDER by id_operation DESC ');
		$req -> bindValue(':table', $table, PDO::PARAM_STR);
	}
	elseif(strlen($date) == 4 ) {
	
		if (DEBUG_SQL) {echo $date.'<br/>';}
		$req = $bdd -> prepare('SELECT id_operation, id_utilisateur, client.nom as cli_nom, client.prenom as cli_pre, id_gestionnaire, gestionnaire.nom as gest_nom, gestionnaire.prenom as gest_prenom, demande_valid, table_modif, champ_modif, valeur, typ_op, DATE(date_op) as date_operation 
		FROM operation, compte_utilisateur client, compte_utilisateur gestionnaire 
		WHERE table_modif =:table
		AND client.id_util = id_utilisateur
		AND gestionnaire.id_util = id_gestionnaire 
		AND YEAR(date_op) = :date 
		ORDER by id_operation DESC ');
		$req -> bindValue(':table', $table, PDO::PARAM_STR);
		$req -> bindValue(':date', $date, PDO::PARAM_STR);			
		}
	else	{
		$us_date = dateFR2US($date);
		if (DEBUG_SQL) {echo $us_date.'<br/>';}
		$req = $bdd -> prepare('SELECT id_operation, id_utilisateur, client.nom as cli_nom, client.prenom as cli_pre, id_gestionnaire, gestionnaire.nom as gest_nom, gestionnaire.prenom as gest_prenom, demande_valid, table_modif, champ_modif, valeur, typ_op, DATE(date_op) as date_operation 
		FROM operation, compte_utilisateur client, compte_utilisateur gestionnaire 
		WHERE table_modif =:table
		AND client.id_util = id_utilisateur
		AND gestionnaire.id_util = id_gestionnaire 
		AND DATE(date_op) = :date 
		ORDER by id_operation DESC ');
		$req -> bindValue(':table', $table, PDO::PARAM_STR);
		$req -> bindValue(':date', $us_date, PDO::PARAM_STR);	
	}
	
	$req->execute();
	$res = $req->fetchAll(PDO::FETCH_NAMED);
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($res);}
	$req->CloseCursor();

	return $res;
	}
	
function AeronefDejaCF($id_forfait, $id_planeur) {
	$bdd=ConnectBddGestionnaire();
	$req= $bdd->prepare('SELECT id FROM config_forfait WHERE planeur_concerne =:planeur AND tarif_associe = :tarif');
	$req -> bindValue(':planeur', $id_planeur, PDO::PARAM_INT);
	$req -> bindValue(':tarif', $id_forfait, PDO::PARAM_INT);
	$req->execute();
	$tab=$req->fetch();
	if (DEBUG_SQL) {echo 'RQ '. __function__ .' <br/>' ; print_r($tab);}
	$req->CloseCursor();
	
	if (empty($tab)) return FALSE; else return TRUE;
	
	}
	
?>



