<?php
/**************************************
Kean de Souza - kean.desouza@gmail.com
GIMELA - Projet 8 ERD - UFRST Evry Val d'Essonne
Fichier: valorisation.php
Crée le 15/06/15
Objectif : Fonction permettant la valorisation des vols effectué par les pilotes 
	
********************************************/

function ValoriserVol($id_vol) {	// En paramètre l'ID d'un vol inscrit dans la base de donnée

	//Déclaration de variable à grande portée dans le code
	$remorquage = 0.00;
	$vol_plane = 0.00;
	$tarif_cmp = 0.00;
	
	$res=GetInformationsVol($id_vol); // Rechercher la ligne de la planche de vol concerné et les informations nécessaire (id remorqueur, planeur ...)
	
	if (empty($res)) echo 'Aucune information';
	else
		{
				// Si il y a un un passager
		if(!is_null($res['id_passager'])) $res_cu2=GetInformationsUser($res['id_passager']);
		
			$res_cu=GetInformationsUser($res['id_pilote']); // Obtenir les informations concernant le pilote
		
			if (DEBUG) {
				print_r ($res_cu);
				print_r($res); 
				}
			
			if (Age($res_cu['date_naissance']) <= SEUIL_AGE )	{ // Configuration de la tarification des vols
				$tarif_remq_selon_age = $res['remq_age_moins'];
				$tarif_planneur_selon_age =  $res['tarif_jeune'];
				}
			else {
				$tarif_remq_selon_age = $res['remq_age_plus'];
				$tarif_planneur_selon_age = $res['tarif_adulte'];
				}
			
			$duree = $res['minute_duree'];
			if ($res['type_vol'] == 'partage') {
				if ($duree > SEUIL_HEURES_GRATUITES * 60) { $duree = 2.5 * 60 ; $duree_rem = $res['duree_vol_remq']; }
				else {  $duree = $duree/2 ; $duree_rem = $res['duree_vol_remq']; }
				} 
			
			// Si le pilote n'a pa acheté de forfait ou la date de son forfait est superieur a la date du vol
			if((empty($res_cu['mouvement_forfait'])))
				{
				switch($res['type_vol']) // Selon le type de vol ---------- Voir les fonctions en fin de fichiers !!
					{
					case 'solo'	: 			if (!empty($res_cu['mouvement_forfait'])) CalculForfait($res_cu, $res);
											else {
											$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
											$prix_planeur = CalculVolPlaneur($duree, $tarif_planneur_selon_age);
											$cmp =CMP($duree, $res_cu['cmp_regle'], FALSE);
											$prix_vol = $prix_planeur + $cmp;
											$commentaire = 'Vol solo facturé' ;
											$prix_planeur = $prix_planeur + $cmp;
											$valorisation = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											$cmp_bdd = $cmp + $res_cu['cmp_regle'];
											 if (DEBUG) echo '<p>Valeur de CMP'.$cmp_bdd;
											// MAJ CMP
											MAJCMP($res_cu['id_util'], $cmp_bdd);
											// Mouvement remorqueur	
											AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $res['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, $remorquage);
											// Mouvement planeur
											AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $res['id_tarif_planeur'], 'virement', $commentaire , $id_vol, $prix_vol);
											
											if (DEBUG) echo ('<p>Vol Solo | Prix: '.$prix_vol.'€ = '.$remorquage.' € de remorquage+ '.$prix_planeur.'  € de vol plane </p>');
											}
											break;
											
									
					case '1er_payeur' : 	if (!empty($res_cu['mouvement_forfait'])) CalculForfait($res_cu, $res);
											else {
											$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
											$prix_planeur = CalculVolPlaneur($duree, $tarif_planneur_selon_age);
											$cmp=CMP($duree, $res_cu['cmp_regle'], FALSE);
											$prix_vol = $prix_planeur + $cmp;
											$commentaire ='Charge du prix du vol incombé au commandant de bord';
											
											// Mouvement remorqueur	
											AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $res['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, $remorquage);
											// Mouvement planeur
											AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $res['id_tarif_planeur'], 'virement', $commentaire , $id_vol, $prix_vol);
											// MAJ CMP
											$cmp_bdd = $cmp + $res_cu['cmp_regle'];
											MAJCMP($res_cu['id_util'], $cmp_bdd);
											if (DEBUG) echo ('<p>Vol 1er_payeur | Prix: '.$prix_vol.'€ = '.$remorquage.' € de remorquage+ '.$prix_planeur.'  € de vol plane</p>');
											$valorisation = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											}
											break;
					
					case 'instruction' :	if (!empty($res_cu['mouvement_forfait'])) CalculForfait($res_cu, $res);
											else {
											$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age);
											$prix_planeur= CalculVolPlaneur($duree, $tarif_planneur_selon_age);
											$cmp=CMP($duree, $res_cu['cmp_regle'], FALSE);
											$prix_vol = $prix_planeur + $cmp;
											$commentaire='Vol d\'instruction';
											
											// Mouvement remorqueur	
											AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $res['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, $remorquage);
											AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $res['id_tarif_planeur'], 'virement', $commentaire , $id_vol, $prix_vol); 
											
											// Mouvement planeur
											AjouterMouvementVol($res_cu2['id_util'], ID_SYS, 'debit', $res['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, 0);
											AjouterMouvementVol($res_cu2['id_util'], ID_SYS, 'debit', $res['id_tarif_planeur'], 'virement', $commentaire , $id_vol, 0);
											
											// MAJ CMP
											$cmp_bdd = $cmp + $res_cu['cmp_regle'];
											MAJCMP($res_cu['id_util'], $cmp_bdd);
											if (DEBUG) echo ('<p>Vol d\'instruction, Elève qui régale | Prix: '.$prix_vol.'€ = '.$remorquage.' € de remorquage+ '.$prix_planeur.'  € de vol plane</p>');
											$valorisation = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire ,$res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											}
											
											break;
											
											
					case 'partage'		: 	$remorquage = CalculRemorquage($duree_rem, $tarif_remq_selon_age, true); 
											$prix_planeur = CalculVolPlaneur($duree, $tarif_planneur_selon_age);
											$prix_planeur = round($prix_planeur, 2, PHP_ROUND_HALF_UP);
											$cmp=CMP($duree, $res_cu['cmp_regle'], TRUE);
											$cmp2 = CMP($duree, $res_cu2['cmp_regle'], TRUE);
											$prix_vol1 = $prix_planeur + $cmp;
											$prix_vol2 = $prix_planeur + $cmp2;
										
											$commentaire='Vol partagé';
											
											$cmp2_bdd = $cmp2 + $res_cu2['cmp_regle'];
											$cmp_bdd = $cmp + $res_cu['cmp_regle'];
											
											if (!empty($res_cu['mouvement_forfait'])) CalculForfait($res_cu, $res);
											else {
												AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $res['id_tarif_planeur'], 'virement', $commentaire , $id_vol, $prix_vol1);
												AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $res['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, $remorquage);
												MAJCMP($res_cu['id_util'], $cmp_bdd);
												}
											
											if (!empty($res_cu['mouvement_forfait'])) CalculForfait($res_cu2, $res);
											else {
												AjouterMouvementVol($res_cu2['id_util'], ID_SYS, 'debit', $res['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, $remorquage);
												AjouterMouvementVol($res_cu2['id_util'], ID_SYS, 'debit', $res['id_tarif_planeur'], 'virement', $commentaire , $id_vol, $prix_vol2);
												MAJCMP($res_cu2['id_util'], $cmp2_bdd);
												}
												
											if (DEBUG) echo ('<p>Vol partagé | Prix: '.$prix_vol.'€ = '.$remorquage.' € de remorquage+ '.$prix_planeur.'  € de vol plane </p>');
											$valorisation[0] = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire ,$res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											$valorisation[1] = array($id_vol, $res_cu2['id_util'], $remorquage, $prix_planeur, $cmp2, $prix_vol, $commentaire ,$res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											
					case 'vi_30' : 		echo ('<p>Vol d\'initiation 30 minutes, remorquage compris</p>');
										break;
										
					case 'vi_60' : 		echo ('<p>Vol d\'initiation 60 minutes, remorquage compris</p>');
										break;
					
					default : 	echo ('<p>En attente</p>'); 
								break;	
					}
				return $valorisation;
				}			
		}
	}	
?>

<?php
// Retourne la somme de la durée des vols et la somme de la durée des heures de vols gratuites
function CalculSommeVol($date_achat_forfait)
	{
	$bdd=ConnectBddGestionnaire();
	$req = $bdd->prepare("SELECT TIMESTAMPDIFF(MINUTE,`date_depart`,`date_arrivee`) as duree_vol, id_vol, type_vol FROM vol WHERE date_vol BETWEEN :date_forfait and DATE(NOW()) ORDER BY id_vol DESC");
	$req -> bindValue(':date_forfait', $date_achat_forfait, PDO::PARAM_STR);
	$req->execute();
	$res = $req ->fetchAll(PDO::FETCH_NAMED);
	$req->CloseCursor();
	
	$heures_gratuites = SEUIL_HEURES_GRATUITES * 60;
	$total_volgratuit= 0;
	$total_vol = 0;
	
	foreach($res as $row) {	echo 	'<br/>Valeur :'.$row['duree_vol'].'<br/>' ;
		if($row['duree_vol'] > $heures_gratuites ) {
			$dif_volgratuit = $row['duree_vol'] - $heures_gratuites;
			$total_volgratuit = $total_volgratuit + $dif_volgratuit;
			$total_vol = $total_vol + $heures_gratuites ;
			echo $row['id_vol'].'-'.$row['duree_vol'].' - duree du vol gratuit : '.$dif_volgratuit.'<br/>';	
			}
		else {
			echo $row['id_vol'].'-'.$row['duree_vol'].'<br/>';
			$total_vol = $total_vol + $row['duree_vol'] ;
			}
		}
	 if (DEBUG) echo '<p>Somme de tous les vols : '.$total_vol.' minutes -  Temps de vol gratuit :'.$total_volgratuit.' min</p>';
	return $total_vol;
	}
	
function CalculRemorquage($duree_remorquage, $cout_remorquage, $partage=FALSE) {
	if ($partage) $remorquage = $duree_remorquage * 0.5 * $cout_remorquage + CMR * 0.5 ; 
	else $remorquage = $duree_remorquage * $cout_remorquage + CMR ; 
	return $remorquage;
	};
	
function CMP($duree_vol, $cmp_deja_paye, $partage=NULL) {
	
	if($partage == TRUE) $duree_vol = $duree_vol * 0.5;
	
	if ($duree_vol <= SEUIL_HEURES_GRATUITES * 60) 
		{	
		if ($cmp_deja_paye < CMP_MAX_SAISON ) {
			$tarif_cmp = CMP * floor($duree_vol/60); 
			$tarif_cmp= round($tarif_cmp, 1, PHP_ROUND_HALF_UP);
			}
		else 
			$tarif_cmp = 0;
		}
	else
		{
		if ($cmp_deja_paye < CMP_MAX_SAISON ) { $tarif_cmp = CMP * SEUIL_HEURES_GRATUITES ; }
		else 
		$tarif_cmp = 0;
		}
		
	$verification_cmp = $tarif_cmp + $cmp_deja_paye;
	
	if ($verification_cmp > CMP_MAX_SAISON) {
		$ajust = CMP_MAX_SAISON - $verification_cmp;
		$tarif_cmp = $tarif_cmp - $ajust;
		$tarif_cmp= round($tarif_cmp, 1, PHP_ROUND_HALF_UP);
		return $tarif_cmp;
		}
	else return $tarif_cmp;		
	}

function CalculVolPlaneur($duree_vol, $cout_vol_horaire, $supplement=NULL){
		
		if ($duree_vol <= SEUIL_HEURES_GRATUITES * 60) 
			$vol_plane = $duree_vol * ($cout_vol_horaire/60) + $supplement;
		else
			$vol_plane = 5 * ($cout_vol_horaire/60) + $supplement;
		$vol_plane = round($vol_plane, 2,PHP_ROUND_HALF_DOWN);	
		
		return $vol_plane ;
	}
	
function CalculForfait($res_cu, $vol) {
	// Obtenir les infos des aeronefs pris en forfait et les tarifs
	$info_forfait = GetConfigForfait($res_cu['mouvement_forfait'], $vol['id_planeur']);
	if (DEBUG) print_r($info_forfait);	
	
	//On calcule la somme des durées de vol effectué depuis le jour d'achat du forfait
	$cptvol = CalculSommeVol($info_forfait['debut_forfait'], $vol['type_vol']);
	if(DEBUG) echo '<p>Durée de vol cumulé : '.$cptvol.' minutes et durée de vol gratuit '.$cptvol_gratuit.' minutes</p>' ;
		
	if ($cptvol <= $info_forfait['duree_forfait']/60 ) 
		{
		$remorquage = CalculRemorquage($vol['duree_vol_remq'], $tarif_remq_selon_age); 
		$supplement = $info_forfait['supplement'];
		if ($duree > SEUIL_HEURES_GRATUITES * 60) $prix_planeur = SEUIL_HEURES_GRATUITES * $supplement;
		else $prix_planeur = $duree/60 * $supplement;
		//echo $duree; echo $supplement;
		$cmp = CMP( $duree, $res_cu['cmp_regle']);
		$prix_vol = $prix_planeur + $cmp;
		
		$type_forfait = $info_forfait['duree_forfait']/60 ; // Pour affichage
		$commentaire = 'Forfait '.$type_forfait.' H en cours';
		$valorisation = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $vol['id_tarif_remorqueur'], $vol['id_tarif_planeur']);

		$cmp_bdd = $res_cu['cmp_regle'] + $cmp;
		// MAJ CMP
		MAJCMP($res_cu['id_util'], $cmp_bdd);
		
		if ($cptvol == $info_forfait['duree_forfait']/60) { $commentaire = 'Forfait '.$type_forfait.' terminé';  MAJForfaitUser($res_cu['id_util'], NULL); //MAJ Forfait }
		// Mouvement remorqueur	
		AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $vol['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, $remorquage);
		// Mouvement planeur
		AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $vol['id_tarif_planeur'], 'virement', $commentaire , $id_vol, $prix_vol);					
		if(DEBUG) echo '<p>Forfait valide : vol : '.$id_vol.': Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
		return $valorisation;
		}
		
	if (( $cptvol > $info_forfait['duree_forfait']/60 ) && (Age($res_cu['date_naissance']) <= SEUIL_AGE )) 
		{
		$remorquage = CalculRemorquage($vol['duree_vol_remq'], $tarif_remq_selon_age); 
		$duree_exces = $cptvol - $info_forfait['duree_forfait']; // temps en minutes
		$supplement = $info_forfait['supplement'];
		//echo $duree_exces ; echo $info_forfait['duree_forfait'];
		$prix_planeur = $duree_exces/60 * ($info_forfait['tarif_planeur_jeune'] + $info_forfait['supplement'] ) ;
		$cmp = CMP($duree_exces, $res_cu['cmp_regle']);
		$prix_vol = $prix_planeur + $cmp;
		$type_forfait = $info_forfait['duree_forfait']/60 ; // Pour affichage
		$commentaire = 'Forfait '.$type_forfait.' H terminé';
		$prix_planeur = $prix_planeur + $cmp;
		$valorisation= array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $vol['id_tarif_remorqueur'], $vol['id_tarif_planeur']);
		
		MAJForfaitUser($res_cu['id_util'], NULL); //MAJ Forfait 
		$cmp_bdd= $cmp + $res_cu['cmp_regle']; //MAJ CMP
		MAJCMP($res_cu['id_util'], $cmp_bdd);
		// Mouvement remorqueur	
		AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $vol['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, $remorquage);
		// Mouvement planeur
		AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $vol['id_tarif_planeur'], 'virement', $commentaire , $id_vol, $prix_vol);
		$res_cu['mouvement_forfait']=NULL;
		//echo '<p>Forfait en dépassement de  : vol : '.$id_vol.' -'.$duree_exces.' minutes pour un jeune : Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
		return $valorisation;
		}
		
	if (( $cptvol > $info_forfait['duree_forfait']/60 ) && (Age($res_cu['date_naissance']) > SEUIL_AGE )) 
		{
		$remorquage = CalculRemorquage($vol['duree_vol_remq'], $tarif_remq_selon_age); 
		$duree_exces = $cptvol - $info_forfait['duree_forfait']; // temps en minutes
		$supplement = $info_forfait['supplement'];
		//echo $duree_exces ; echo $info_forfait['duree_forfait'];
		$prix_planeur = $duree_exces/60 * ($info_forfait['tarif_planeur_adulte'] + $info_forfait['supplement'] ) ;
		$cmp = CMP($duree_exces, $res_cu['cmp_regle']);
		$prix_vol = $prix_planeur + $cmp;
		$type_forfait = $info_forfait['duree_forfait']/60 ; // Pour affichage
		$commentaire = 'Forfait '.$type_forfait.' H terminé';
		$prix_planeur = $prix_planeur + $cmp;
		$valorisation= array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $vol['id_tarif_remorqueur'], $vol['id_tarif_planeur']);
		
		MAJForfaitUser($res_cu['id_util'], NULL); //MAJ Forfait 
		$cmp_bdd= $cmp + $res_cu['cmp_regle']; //MAJ CMP
		MAJCMP($res_cu['id_util'], $cmp_bdd);
		// Mouvement remorqueur	
		AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $vol['id_tarif_remorqueur'] , 'virement', $commentaire , $id_vol, $remorquage);
		// Mouvement planeur
		AjouterMouvementVol($res_cu['id_util'], ID_SYS, 'debit', $vol['id_tarif_planeur'], 'virement', $commentaire , $id_vol, $prix_vol);
		$res_cu['mouvement_forfait']=NULL;
		//echo '<p>Forfait en dépassement de  : vol : '.$id_vol.' -'.$duree_exces.' minutes pour un adulte : Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
		return $valorisation;
		}
	}
}
?>