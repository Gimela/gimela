<?php
/*
Kean de Souza, Lyes Boudjedaimi
Fichier de configuration de la valorisation des vols
Créé le 06/06/15
Modifié le 
Algo_forfait_valorisation
Objecif : - Calculer un vol remorqué
		  - Calculer un vol plané
		  - Déduire du temps d'un forfait
*/
require('/cfg/functions.php');
require('/cfg/requetesPDO.php');
require('/cfg/conf_gimela.php');
?>

<html>
	<head>
	<meta charset="UTF-8">
	<TITLE>Test Valorisation</TITLE>
	</head>
<body>
<!--LYES-->
<h1>Valorisation</h1>
<form method="POST" action='#'/>
<p> Indiquer l'ID du vol <input type="text" placeholder="test" <?php if (isset($_POST['id_vol'])) echo ('value="'.$_POST['id_vol'].'"'); ?> name="id_vol"/>
<input type="submit" value="Calculer"/>
<?php  
// Déclaration des variable 

if (!isset($_POST['id_vol'])) echo ('Spécifiez un ID !!!');
else 
	{
	$res=GetInformationsVol($_POST['id_vol']);
	if (empty($res)) echo 'Aucune information';
	else
		{
		//print_r($res);
		$bdd=ConnectBdd();
		$cu=$bdd->prepare('SELECT id_util, date_naissance, cmp_regle , mouvement_forfait FROM compte_utilisateur WHERE id_club = :idclub');
		$cu->bindValue(':idclub', $res['id_pilote'], PDO::PARAM_INT);
		$cu->execute();
		$res_cu = $cu->fetch();
		//print_r ($res_cu);
		if (empty($res_cu['mouvement_forfait']))  echo '<p>Aucun forfait associé ou forfait épuisé, débit du compte pilote !!!</p>' ;
		else
			{
			//echo $res_cu['mouvement_forfait'];
			$mouv=$bdd->prepare('SELECT DATE(date_heure_mouv) as debut_forfait, type_tarif FROM mouvement WHERE id_mouv=:idmouv');
			$mouv->bindValue(':idmouv', $res_cu['mouvement_forfait'], PDO::PARAM_INT);
			$mouv->execute();
			$res_mouv = $mouv->fetch();
			$mouv->CloseCursor();
			//print_r($res_mouv);
			list($cptvol, $cptvol_gratuit) = CalculSommeVol($res_mouv['debut_forfait']);
			echo '<p>Durée de vol cumulé : '.$cptvol.' minutes et durée de vol gratuit '.$cptvol_gratuit.' minutes</p>' ;
			
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
					
					if (Age($res_cu['date_naissance']) <= SEUIL_AGE ) $tarif_remq_selon_age = $res['remq_age_moins'];
					else $tarif_remq_selon_age = $res['remq_age_plus'];

					if ($cptvol <= $heure_forfait ) {
						$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
						$prix_planeur = $cptvol * $res_info_forfait['supplement'];
						$cmp = CMP($res['minute_duree'], $res_cu['cmp_regle']);
						$prix_vol = $prix_planeur + $cmp + $remorquage;
							echo '<p>Forfait valide : Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
						}
					
					if( ($cptvol > $heure_forfait) && (Age($res_cu['date_naissance']) <= SEUIL_AGE )) {
						$remorquage = CalculRemorquage($res['duree_vol_remq'], $res['remq_age_moins']); 
						$duree_exces = $cptvol - $heure_forfait;
						$prix_planeur = $duree_exces * ( $res_info_forfait['tarif_planeur_jeune'] + $res_info_forfait['supplement'] );
						$cmp = CMP($res['minute_duree'], $res_cu['cmp_regle']);
						$prix_vol = $prix_planeur + $cmp + $remorquage;
						
						echo '<p>Forfait en dépassement de '.$duree_exces.' minutes pour un jeune : Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
						}
					
					if( ($cptvol > $heure_forfait) && (Age($res_cu['date_naissance']) > SEUIL_AGE )) {
						$remorquage = CalculRemorquage($res['duree_vol_remq'], $res['remq_age_plus']); 
						$duree_exces = $cptvol - $heure_forfait;
						$prix_planeur = $duree_exces * ( $res_info_forfait['tarif_planeur_jeune'] + $res_info_forfait['supplement'] );
						$cmp = CMP($res['minute_duree'], $res_cu['cmp_regle']);
						$prix_vol = $prix_planeur + $cmp + $remorquage;
						
						echo '<p>Forfait en dépassement de '.$duree_exces.' minutes pour un adulte : Prix du vol : '.$prix_vol.' € = Planeur : '. $prix_planeur.' + CMP : '.$cmp.' + Prix remorquage = '.$remorquage.' €</p>';
						}
					
				}
			else echo '<p> Le forfait renseigné n\'est pas paramétré dans la configuration, veuillez transmettre l\'information suivante au responsable du système : '.$res_mouv['type_tarif'] ;	
			}
		}
	}	
?>

<?php
// Retourne la somme de la durée des vols et la somme de la durée des heures de vols gratuites
function CalculSommeVol($date_achat_forfait)
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
?>