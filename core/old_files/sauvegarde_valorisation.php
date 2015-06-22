<?php
/**************************************
Kean de Souza - kean.desouza@gmail.com
GIMELA - Projet 8 ERD - UFRST Evry Val d'Essonne
Fichier: valorisation.php
Crée le 15/06/15
Objectif : Fonction permettant la valorisation des vols effectué par les pilotes 
	
********************************************/

global $forfait; // Défini dans conf_gimela.php
function ValoriserVol($id_vol) {	// En paramètre l'ID d'un vol inscrit dans la base de donnée
	
	//Déclaration de variable à grande portée dans le code
	$remorquage = 0.00;
	$vol_plane = 0.00;
	$tarif_cmp = 0.00;
	
	$res=GetInformationsVol($id_vol); // Rechercher la ligne de la planche de vol concerné et les informations nécessaire (id remorqueur, planeur ...)
	if (empty($res)) echo 'Aucune information';
	else
		{
		
		//print_r($res); 
		
		$res_cu=GetInformationsUser($res['id_pilote']); // Obtenir les informations concernant le pilote
		
		//print_r ($res_cu);
		
		if (Age($res_cu['date_naissance']) <= SEUIL_AGE )	{ // Configuration de la tarification des vols
			$tarif_remq_selon_age = $res['remq_age_moins'];
			$tarif_planneur_selon_age =  $res['tarif_jeune'];
			}
		else  {
			$tarif_remq_selon_age = $res['remq_age_plus'];
			$tarif_planneur_selon_age = $res['tarif_adulte'];
			}
			
		//if($res['id_passager'] != 0) echo '<p>Second pilote !</p>'; // Si la ligne contient un second pilote
		
		// Si le pilote possède un forfait
		if (!empty($res_cu['mouvement_forfait'])) {
			$res_mouv = GetForfait($res_cu['mouvement_forfait']);
			$dateduforfait = new Datetime($res_mouv['debut_forfait']);
			$dateplanche = new Datetime($res['date_vol']);
			}
			
		// Si le pilote n'a pa acheté de forfait ou la date de son forfait est superieur a la date du vol
		if((empty($res_cu['mouvement_forfait'])) OR (($dateduforfait > $dateplanche)) )
			{
			$duree = $res['minute_duree'];
			switch($res['type_vol']) // Selon le type de vol ---------- Voir les fonctions en fin de fichiers !!
					{
					case 'solo'	: 			$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
											$prix_planeur = CalculVolPlaneur($duree, $tarif_planneur_selon_age);
											$cmp=CMP($duree, $res_cu['cmp_regle']);
											$prix_vol = $prix_planeur + $cmp + $remorquage;
											$commentaire = 'Vol solo facturé' ;
											
											$res = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											break;
											//echo ('<p>Vol Solo | Prix: '.$vol_complet.'€ = '.$remorquage.' € de remorquage+ '.$vol_plane.'  € de vol plane +  '.$tarif_cmp.' € de CMP</p>');
											
									
					case '1er_payeur' : 	$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
											$prix_planeur = CalculVolPlaneur($duree, $tarif_planneur_selon_age);
											$cmp=CMP($duree, $res_cu['cmp_regle']);
											$prix_vol = $prix_planeur + $remorquage + $cmp;
											$commentaire ='Charge du prix du vol incombé au commandant de bord';
											
											$res = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											break;											//echo ('<p>Vol 1er_payeur | Prix: '.$vol_complet.'€ = '.$remorquage.' € de remorquage+ '.$vol_plane.'  € de vol plane</p>');
										
					
					case 'instruction' :	$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age);
											$prix_planeur= CalculVolPlaneur($duree, $tarif_planneur_selon_age, $res_cu['cmp_regle']);
											$prix_vol = $prix_planeur + $cmp + $remorquage;
											$commentaire='Vol d\'instruction';
											$res = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire ,$res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											break;
											//echo ('<p>Vol d\'instruction, Elève qui régale | Prix: '.$vol_complet.'€ = '.$remorquage.' € de remorquage+ '.$vol_plane.'  € de vol plane</p>');
											
											
					case 'partage'		: 	$res['duree_vol_remq'] = $res['duree_vol_remq'] / 2;
											$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
											$duree = $duree/2;
											$prix_planeur = CalculVolPlaneur($duree, $tarif_planneur_selon_age, $res_cu['cmp_regle']);
											$prix_planeur = round($prix_planeur, 2, PHP_ROUND_HALF_UP);
											$prix_vol = $prix_planeur + $remorquage + $cmp;
											$cmp=CMP($duree, $res_cu['cmp_regle']);
											$res = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire ,$res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
											break;
											
					case 'vi_30' : 		echo ('<p>Vol d\'initiation 30 minutes, remorquage compris</p>');
										break;
										
					case 'vi_60' : 		echo ('<p>Vol d\'initiation 60 minutes, remorquage compris</p>');
										break;
					
					default : 	echo ('<p>En attente</p>'); 
								break;	
					}
			
			}
		
		else //Le pilote possède un forfait, on analyse et on en déduit le temps de vol effectuer depuis son achat à aujourd'hui
			{
			//echo $res_cu['mouvement_forfait'];
			global $forfait; // Rappel nécessaire
		
			//print_r($res_mouv);
			list($cptvol, $cptvol_gratuit) = CalculSommeVol($res_mouv['debut_forfait'], $res['type_vol']);
			//echo '<p>Durée de vol cumulé : '.$cptvol.' minutes et durée de vol gratuit '.$cptvol_gratuit.' minutes</p>' ;
			
			//Trouver l'heure de forfait associé au mouvement dans le tableau spécifié dans le paramètrage du système
			if(array_key_exists($res_mouv['type_tarif'], $forfait))
				{	
					$key = $res_mouv['type_tarif'];
					
					//Obtenir les tarifs associés au forfait
					$rq_info_forfait=$bdd->prepare('SELECT * FROM config_forfait WHERE planeur_concerne=:idplaneur AND tarif_associe=:idtarif');
					
					//Vol : Planeur qui a provoqué le calcul du forfait
					$rq_info_forfait->bindValue(':idplaneur',$res['id_planeur'], PDO::PARAM_INT);
					$rq_info_forfait->bindValue(':idtarif', $res_mouv['type_tarif'], PDO::PARAM_INT);
					$rq_info_forfait->execute();
					$res_info_forfait = $rq_info_forfait->fetch();
					$rq_info_forfait->CloseCursor();
					
					//print_r($res_info_forfait);
					
					$heure_forfait = $forfait[$key]; 
					//echo 'Forfait d\'une durée de : '.$heure_forfait.'H';
					
					$heure_forfait = $heure_forfait * 60;
					$cmp = 0;
					//print_r($res);
					if ($cptvol <= $heure_forfait ) {
						$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
						$prix_planeur = $cptvol * $res_info_forfait['supplement'];
						$cmp = CMP($res['minute_duree'], $res_cu['cmp_regle']);
						$prix_vol = $prix_planeur + $cmp + $remorquage;
						$commentaire = 'Forfait '.$forfait[$key].' H en cours';
						$res = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
						//echo '<p>Forfait valide : vol : '.$id_vol.': Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
						}
					
					if( ($cptvol > $heure_forfait) && (Age($res_cu['date_naissance']) <= SEUIL_AGE )) {
						$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
						$duree_exces = $cptvol - $heure_forfait;
						$prix_planeur = $duree_exces * ( $res_info_forfait['tarif_planeur_jeune'] + $res_info_forfait['supplement'] );
						$cmp = CMP($res['minute_duree'], $res_cu['cmp_regle']);
						$prix_vol = $prix_planeur + $cmp + $remorquage;
						$commentaire = 'Forfait '.$forfait[$key].' H terminé';
						$res = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
						
						MAJForfaitUser($res_cu['id_util'], NULL);
						//echo '<p>Forfait en dépassement de  : vol : '.$id_vol.' '.$duree_exces.' minutes pour un jeune : Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
						}
					
					if( ($cptvol > $heure_forfait) && (Age($res_cu['date_naissance']) > SEUIL_AGE )) {
						$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
						$duree_exces = $cptvol - $heure_forfait;
						$prix_planeur = $duree_exces * ( $res_info_forfait['tarif_planeur_jeune'] + $res_info_forfait['supplement'] );
						$cmp = CMP($res['minute_duree'], $res_cu['cmp_regle']);
						$prix_vol = $prix_planeur + $cmp + $remorquage;
						$commentaire = 'Forfait '.$forfait[$key].' H terminé';
						$res = array($id_vol, $res_cu['id_util'], $remorquage, $prix_planeur, $cmp, $prix_vol, $commentaire, $res['id_tarif_remorqueur'], $res['id_tarif_planeur']);
						
						MAJForfaitUser($res_cu['id_util'], NULL);
						//echo '<p>Forfait en dépassement  : vol : '.$id_vol.' de '.$duree_exces.' minutes pour un adulte : Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
						}
				}
			else {
				echo '<p> Le forfait renseigné n\'est pas paramétré dans la configuration, veuillez transmettre l\'information suivante au responsable du système : '.$res_mouv['type_tarif'] ;	
				}
			}
		}
	return $res;
	}	
?>

<?php
// Retourne la somme de la durée des vols et la somme de la durée des heures de vols gratuites

function CalculSommeVol($date_achat_forfait, $type_vol)
	{
	$bdd=ConnectBdd();
	$req = $bdd->prepare("SELECT DISTINCT TIMESTAMPDIFF(MINUTE,`date_depart`,`date_arrivee`) as duree_vol, id_vol FROM vol WHERE date_vol BETWEEN ':date_forfait' and DATE(NOW()) ORDER BY id_vol DESC");
	$req -> bindValue(':date_forfait', $date_achat_forfait, PDO::PARAM_STR);
	$req->execute();
	$res = $req ->fetchAll(PDO::FETCH_NAMED);
	$req->CloseCursor();
	
	$heures_gratuites = SEUIL_HEURES_GRATUITES * 60;
	$total_volgratuit= 0;
	$total_vol = 0;
	foreach($res as $row) {		
		if($row['duree_vol'] > $heures_gratuites ) {
				if($type_vol == 'partage') $row['duree_vol'] = $row['duree_vol']/2;
			$duree_volgratuit = $row['duree_vol'] - $heures_gratuites;
			$total_volgratuit = $total_volgratuit + $duree_volgratuit;
			$total_vol = 	$total_vol + 5 ;
			//echo $row['id_vol'].'-'.$row['duree_vol'].' - duree du vol gratuit : '.$duree_volgratuit.'<br/>';	
			}
		else {
			//echo $row['id_vol'].'-'.$row['duree_vol'].'<br/>';
			$total_vol = $total_vol + $row['duree_vol'] ;
			}
		}
	//echo '<p>Somme de tous les vols : '.$total_vol.' min Temps gratuit :'.$total_volgratuit.' min</p>';
	return array($total_vol, $total_volgratuit);
	}
	
function CalculRemorquage($duree_remorquage, $cout_remorquage) {
	
	$remorquage = $duree_remorquage * $cout_remorquage + CMR ; 
	
	return $remorquage;
	};
	
function CMP($duree_vol, $cmp_deja_paye) {
	
	if ($duree_vol <= SEUIL_HEURES_GRATUITES * 60) 
		{	
		if ($cmp_deja_paye < CMP_MAX_SAISON ) 
			$tarif_cmp = CMP * floor($duree_vol/60) ; 
		else 
			$tarif_cmp = 0;
		}
	else
		{
		if ($cmp_deja_paye < CMP_MAX_SAISON )
			$tarif_cmp = CMP * SEUIL_HEURES_GRATUITES ;
		else 
		$tarif_cmp = 0;
		}
	return $tarif_cmp;		
}

function CalculVolPlaneur($duree_vol, $cout_vol_horaire, $supplement=NULL){
		
		if ($duree_vol <= SEUIL_HEURES_GRATUITES * 60) 
			$vol_plane = $duree_vol * ($cout_vol_horaire/60) + $supplement);
		else
			$vol_plane = 5 * ($cout_vol_horaire/60) + $supplement);
		$vol_plane = round($vol_plane, 2,PHP_ROUND_HALF_DOWN);	
		
		return $vol_plane ;
	};

?>