<?php
/*
Kean de Souza, Lyes Boudjedaimi
PAGE DE TEST VALORISATION VOL !!!
Fichier de configuration de la valorisation des vols
Créé le 06/06/15
Modifié le 
algo_valorisation.php
Objecif : - Calculer un vol remorqué
		  - Calculer un vol plané
		  - Déduire du temps d'un forfait
*/
require('./functions.php');
require('/sql/requetesPDO.php');
require('./conf_gimela.php');
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
				
				$cdb_vol[0] =  $res['id_pilote'];
				if(!is_null($res['id_passager'])) $cdb_vol[1]=$res['id_passager'];
				
				$iteration = sizeof($cdb_vol);
				$i=0;
				
			for ($i=0; $i < $iteration; $i++)
				{
				echo ('Pilote '.$i.' <br/>');
				
				$res2=GetInformationsUser($cdb_vol[$i]);
				
					
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
					
					
				$duree = $res['minute_duree']; // Durée du vol en minute retourné par la base de donnée
					
					
					
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
											$cmp = CMP( $duree , $res2['cmp_regle'] );
											$vol_complet = $vol_plane + $remorquage;
										
											
											$vol_complet = $vol_complet * 0.5;
											
											
											$cout_cdb = round($vol_complet, 2, PHP_ROUND_HALF_UP);
											
											echo '<p> Suite à vol partage : '.$cout_cdb.' € </p>';
											
											break;
											
					case 'vi_30' : 		echo ('<p>Vol d\'initiation 30 minutes, remorquage compris</p>');
										break;
										
					case 'vi_60' : 		echo ('<p>Vol d\'initiation 60 minutes, remorquage compris</p>');
										break;
					
					default : 	echo ('non traité'); 
								break;
						
					}
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
			$vol_plane = $duree_vol * ($cout_vol_horaire/60 + $supplement);
		else
			$vol_plane = 5 * ($cout_vol_horaire/60 + $supplement);
		
		$vol_plane = round($vol_plane, 2,PHP_ROUND_HALF_DOWN);	
		echo 'Vol plane : '.$vol_plane.'<br/>';
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
		
	echo '<br/>CMP: '.$tarif_cmp;
	
	return $tarif_cmp;
		
}

?>
 
 