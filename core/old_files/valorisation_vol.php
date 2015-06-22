<?php
/*
Kean de Souza, Lyes Boudjedaimi
Fichier de configuration de la valorisation des vols
Créé le 06/06/15
Modifié le 
algo_valorisation.php
Objecif : - Calculer un vol remorqué
		  - Calculer un vol plané
		  - Déduire du temps d'un forfait
*/
require('../functions.php');
require('../sql/requetesPDO.php');
require('../conf_gimela.php');
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
		$remorquage = 0.00;
		$vol_plane = 0.00;
		$tarif_cmp = 0.00;
		
		$res2=GetInformationsUser($res['id_pilote']);
				
					
				if (Age($res2['date_naissance']) <= SEUIL_AGE ) 
					{
					$tarif_remq_selon_age = $res['remq_age_moins'];
					$tarif_planneur_selon_age = $res['tarif_jeune'];
					
					}
				else 
					{
					$tarif_remq_selon_age = $res['remq_age_plus'];
					$tarif_planneur_selon_age = $res['tarif_adulte'];
					}
				
					
				//Second CDB si VOL PARTAGE
				if($res['type_vol'] == 'partage')
					{	
					 $res3=GetInformationsUser($res['id_passager']);
					
						
					if (Age($res3['date_naissance']) <= SEUIL_AGE ) 
						{
						$tarif_remq_selon_age2 = $res['remq_age_moins'];
						$tarif_planneur_selon_age2 = $res['tarif_jeune'];
						}
					else 
						{
						$tarif_remq_selon_age2 = $res['remq_age_plus'];
						$tarif_planneur_selon_age2 = $res['tarif_adulte'];	
						}	
					}
					
					$duree = $res['minute_duree']; // Durée du vol en minute retourné par la base de donnée
					
				// Calcul des vols sans prise en compte du forfait
					
				switch($res['type_vol'])
					{
						
					case 'solo'	: 	
											$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
											$vol_plane = CalculVolPlaneur($duree, $tarif_planneur_selon_age, $res2['cmp_regle']);
											$vol_complet = $vol_plane + $tarif_cmp + $remorquage;
											$tarif_cmp=CMP($duree, $res2['cmp_regle']);
											echo ('<p>Vol Solo | Prix: '.$vol_complet.'€ = '.$remorquage.' € de remorquage+ '.$vol_plane.'  € de vol plane +  '.$tarif_cmp.' € de CMP</p>');
										break;
									
					case '1er_payeur' : 	$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
											$vol_plane = CalculVolPlaneur($duree, $tarif_planneur_selon_age, $res2['cmp_regle']);
											$vol_complet = $vol_plane + $remorquage;
											echo ('<p>Vol 1er_payeur | Prix: '.$vol_complet.'€ = '.$remorquage.' € de remorquage+ '.$vol_plane.'  € de vol plane</p>');
											break;
					
					case 'instruction' :	$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age);
											$vol_plane= CalculVolPlaneur($duree, $tarif_planneur_selon_age, $res2['cmp_regle']);
											$vol_complet = $vol_plane + $remorquage;
											echo ('<p>Vol d\'instruction, Elève qui régale | Prix: '.$vol_complet.'€ = '.$remorquage.' € de remorquage+ '.$vol_plane.'  € de vol plane</p>');
											break;
											
					case 'partage'		: 	$remorquage = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age); 
											$vol_plane = CalculVolPlaneur($duree, $tarif_planneur_selon_age, $res2['cmp_regle']);
											
											$remorquage2 = CalculRemorquage($res['duree_vol_remq'], $tarif_remq_selon_age2);
											$vol_plane2 = CalculVolPlaneur($duree, $tarif_planneur_selon_age2, $res3['cmp_regle']);
											
											$vol_complet = $vol_plane + $remorquage;
											$vol_complet2 = $vol_plane2 + $remorquage2;
											
											$vol_complet = $vol_complet * 0.5;
											$vol_complet2 = $vol_complet2 * 0.5;
											
											$cout_cdb = round($vol_complet, 2, PHP_ROUND_HALF_UP);
											$cout_2_cdb = round($vol_complet2, 2, PHP_ROUND_HALF_DOWN);
											
											$cout_partage = $vol_complet + $vol_complet2;
											
											echo '<p>CDB 1 Cout du vol '.	$cout_cdb.' </p>'; 	
											echo '<p>CDB 2 Cout du vol '.	$cout_2_cdb.' </p>'; 
											
											break;
											
					case 'vi_30' : 		echo ('<p>Vol d\'initiation 30 minutes, remorquage compris</p>');
										break;
										
					case 'vi_60' : 		echo ('<p>Vol d\'initiation 60 minutes, remorquage compris</p>');
										break;
					
					default : 	echo ('non traité'); 
								break;
						
					}
		
				/*
					Afficher un formulaire de changement des pourcentages lors d'un partage des vols
					if(isset($_POST['calcul_pourcentage'])) 
						{
							$partage_vol_cdb = round($_POST['pourcentage'], 2,PHP_ROUND_HALF_UP);
							$partage_vol_2_cdb = 100 - $partage_vol_cdb;
							
							$cout_cdb = $vol_complet * ($partage_vol_cdb/100);
							$cout_2_cdb = $vol_complet - $cout_cdb;
							
							$cout_cdb = round($cout_cdb, 2, PHP_ROUND_HALF_UP);
							$cout_2_cdb = round($cout_2_cdb, 2, PHP_ROUND_HALF_DOWN);
			
						}
					
				*/		

		}
	}

 ?>
 </form>
</body>
</html>
<?php

// Calcul du vol pour le débiter sur le compte pilote

function CalculRemorquage($duree_remorquage, $cout_remorquage) {
	
	$remorquage = $duree_remorquage * $cout_remorquage + CMR ; 
	
	return $remorquage;
	};

function CalculVolPlaneur($duree_vol, $cout_vol_horaire, $cmp_deja_paye, $supplement=NULL){
		
		if ($duree_vol <= SEUIL_HEURES_GRATUITES * 60) 
			$vol_plane = ($duree_vol/60) * ($cout_vol_horaire + $supplement);
		else
			$vol_plane = 5 * ($cout_vol_horaire + $supplement);
		
		$vol_plane = round($vol_plane, 2,PHP_ROUND_HALF_DOWN);	
		
		return $vol_plane ;
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
 
 