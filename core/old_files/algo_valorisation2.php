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
require('/cfg/conf_sysvv.php');
require('/cfg/functions.php');
require('/cfg/conf_valorisation.php');
?>

<html>
	<head>
	<meta charset="UTF-8">
	<TITLE>Test Valorisation</TITLE>
	<style type="text/css">
		input[type="range"] {
		position: relative;
		margin-left: 1em;
	}
	input[type="range"]:after,
	input[type="range"]:before {
		position: absolute;
		top: 1em;
		color: #aaa;
	}
	input[type="range"]:before {
		left:0em;
		content: attr(min);
	}
	input[type="range"]:after {
		right: 0em;
		content: attr(max);
	}
	</style>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	<script type="text/javascript">
	$(function(){
		$("#partage_pourcent").change(function() {
		$("#pourcentage_selection").val(($("#partage_pourcent").val()));
		})
		});
</script>
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
	$connexion=ConnectBdd();
	
	$req = $connexion->prepare('SELECT date_vol, id_planneur, date_depart, date_arrivee, TIMESTAMPDIFF(MINUTE,`date_depart`,`date_arrivee`) AS minute_duree, id_remorqueur, duree_vol_remq, type_vol, id_pilote, id_passager, tarif.tarif_inf, tarif.tarif_sup, remq_tarif.tarif_inf as remq_age_moins, remq_tarif.tarif_sup as remq_age_plus FROM vol, aeronefs, aeronefs aero_remq, tarif, tarif remq_tarif WHERE id_vol=:id AND id_planneur = aeronefs.num_aeronef AND id_remorqueur = aero_remq.num_aeronef AND aeronefs.tarif_associe = tarif.id_tarif AND aero_remq.tarif_associe = remq_tarif.id_tarif  ');
	$req->bindValue(':id', $_POST['id_vol'],PDO::PARAM_STR);
	$req->execute();
	$res = $req->fetchAll();
	$req->CloseCursor();
	
	
			
	if (empty($res)) echo 'Aucune information';
	else
		{
		$remorquage = 0.00;
		$vol_plane = 0.00;
		$tarif_cmp = 0.00;
		
		foreach ($res as $info)
			{
				// Premier CDB
				$req2 = $connexion->prepare('SELECT * FROM compte_utilisateur WHERE id_util=:cdb');
				$req2->bindValue(':cdb', $info['id_pilote']);
				$req2->execute();
				$res2 = $req2->fetch();
				$req2->CloseCursor();
				
				//Second CDB
				$req3 = $connexion->prepare('SELECT * FROM compte_utilisateur WHERE id_util=:second_cdb');
				$req3->bindValue(':second_cdb', $info['id_passager']);
				$req3->execute();
				$res3 = $req3->fetch();
				$req3->CloseCursor();
				
				//Tarifs pour les pilotes selon leurs âges
				if (Age($res2['date_naissance']) <= SEUIL_AGE ) 
					{
					echo ('<p>Jeune pilote : '.Age($res2['date_naissance']).' ans</p>');
					$tarif_remq_selon_age = $info['remq_age_moins'];
					$tarif_planneur_selon_age = $info['tarif_inf'];
					}
				else 
					{
					echo ('<p>Pilote agé : '.Age($res2['date_naissance']).' ans</p>');
					$tarif_remq_selon_age = $info['remq_age_plus'];
					$tarif_planneur_selon_age = $info['tarif_sup'];	
					}
				
				// Si présence d'un forfait 
				if(!empty($res2['heure_forfait'])) 
					{
					echo ('<p>CDB : Débiter heure du forfait ?<input type="radio" name="choix_debit" value="forfait" /> 
					OU compte pilote ?<input type="radio" name="choix_debit" value="compte"/> </p>');	
					}
				
				//Information pour le second CDB
				if($res3['id_util']!= 0) // Si il n'y a pas de second pilote
					{
					if (Age($res3['date_naissance']) <= SEUIL_AGE ) 
						{
						echo ('<p>Second Jeune pilote : '.Age($res3['date_naissance']).' ans</p>');
						$tarif_remq_selon_age2 = $info['remq_age_moins'];
						$tarif_planneur_selon_age2 = $info['tarif_inf'];
						}
					else 
						{
						echo ('<p>Second Pilote agé : '.Age($res3['date_naissance']).' ans</p>');
						$tarif_remq_selon_age2 = $info['remq_age_plus'];
						$tarif_planneur_selon_age2 = $info['tarif_sup'];	
						}
					
					// Si présence d'un forfait - second CDB
					if(!empty($res3['heure_forfait'])) 
						{
						echo ('<p>Second CDB : Débiter heure du forfait ?<input type="radio" name="choix_debit_2" value="forfait" /> OU compte pilote ?<input type="radio" name="choix_debit_2" value="compte"/> </p>');	
						}
					}
				
				switch ($info['type_vol'])
					{
						case 'partage' :  echo ('<p>Prise en charge du vol à combien de pourcent pour le CDB ? : 
												<input type="range" min="0" max="100" value="50" step="5" id="partage_pourcent" />
												<input type="text" id="pourcentage_selection" value="50" name="pourcentage"/>%</p>
												<input type="submit" name="calcul_pourcentage" value="Calcul du partage"/> ');
											break;
						
						case '1er_payeur' : echo ('<p> Prise en charge entière par le CDB</p>');
											break;
						
						case 'vi_30' : 
											break;
						
						case 'vi_60' : 
											break;
						
						case 'vi_initiation' : 
											break;
						
						case 'solo' : 		echo ('<p> Prise en charge entière par le CDB </p>');
											break;
						
					}
								
				// CALCUL DU REMORQUAGE
					echo ('<p> Tarif du remorquage : '.$tarif_remq_selon_age.' €');
					
					echo ('<p> Durée du vol remorquée : '.$info['duree_vol_remq'].'');
					
					$remorquage = $info['duree_vol_remq'] * $tarif_remq_selon_age + CMR ; 
					
					echo ('<p>Prix du vol remorqué : '.$remorquage.' €</p>');
				//--------------------------------------------
				
				// CALCUL DU VOL PLANE
					echo  ('<p> Tarif de l\'heure de vol : '.$tarif_planneur_selon_age.'</p>');
					$duree=$info['minute_duree'];
					
					if ($duree <= SEUIL_HEURES_GRATUITES * 60) 
						{	
						echo ('<p> Vol de moins de '.SEUIL_HEURES_GRATUITES.' heures : '.$duree.' minutes</p>');
						$tarif_cmp = CMP * floor($duree/60) ; 
						echo ('<p> Prix de la CMP : '.$tarif_cmp.' €</p>');
						$vol_plane = ($duree/60) * $tarif_planneur_selon_age;
						$vol_plane = round($vol_plane, 2);
						echo ('Prix du vol plane : '.$vol_plane.' €');
						}
					else
						{
						echo ('Vol de plus de '.SEUIL_HEURES_GRATUITES.' heures : '.$duree.' minutes');
						
						//Si il y'a 80 heures de CMP accumulé, voir champ utilisateur BDD
						$tarif_cmp = CMP * SEUIL_HEURES_GRATUITES ;
						echo ('<p> Prix de la CMP : '.$tarif_cmp.'</p>');
						
						$vol_plane = 5 * $tarif_planneur_selon_age;
						
						$vol_plane = round($vol_plane, 2,PHP_ROUND_HALF_DOWN);
						echo ('Prix du vol plane : '.$vol_plane.' €');
						}
						
					$vol_plane = $vol_plane + $tarif_cmp + $remorquage;
					
					if(isset($_POST['calcul_pourcentage'])) 
						{
							$partage_vol_cdb = round($_POST['pourcentage'], 2,PHP_ROUND_HALF_UP);
							$partage_vol_2_cdb = 100 - $partage_vol_cdb;
							
							$cout_cdb = $vol_plane * ($partage_vol_cdb/100);
							$cout_2_cdb = $vol_plane - $cout_cdb;
							
							$cout_cdb = round($cout_cdb, 2, PHP_ROUND_HALF_UP);
							$cout_2_cdb = round($cout_2_cdb, 2, PHP_ROUND_HALF_DOWN);
							
							echo ('<p>Partage occasionnée : CDB : '.$partage_vol_cdb.' soit '.$cout_cdb.' €; Second CDB : '.$partage_vol_2_cdb.' soit '.$cout_2_cdb.' €</p>');
						}
					
					
					echo ('<p>Prix du vol complet : '.$vol_plane.'€</p>');

			}
		}
	}

 ?>
 </form>
</body>
</html>
<?php

?>
 
 