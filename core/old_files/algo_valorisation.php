<?php
require('/cfg/conf_sysvv.php');
require('/cfg/functions.php');
require('/cfg/conf_valorisation.php');
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
<p> Indiquer l'ID du vol <input type="text" name="id_vol"/>
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
	//print_r($res);
	if (empty($res)) echo 'Aucune information';
	else
		{
		$remorquage = 0.00;
		$vol_plane = 0.00;
		foreach ($res as $info){
			////Age de l'utilisateur pris en compte soit 2 branches de déroulement
			$tarif_remq_selon_age = $info['remq_age_moins'];
			$tarif_planneur_selon_age = $info['tarif_sup'];
			$tarif_cmp = 0.00;
				
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
					
					$vol_plane = round($vol_plane, 2);
					echo ('Prix du vol plane : '.$vol_plane.' €');
					}
				
				$vol_plane = $vol_plane + $tarif_cmp + $remorquage;
				
				echo ('<p>Prix du vol complet : '.$vol_plane.'€</p>');
				
			}
		}
	}
 /*
 $Cout="durée(en 1/100)*cout du 1/100";
 $Dv="(HH*60+MM)";
 $PVr="3.09";
 $PTv="4.75";
 $PTd="6.81";
 
 // on valorise d'abord le remorquage
 if (forfait>0);
 echo "pas de valorisation introhgation sur compte pilot ";
 compte=compte-Dv; 
 elseif (Dv<=5)
 {
 echo "<b> pour un montant d'achat unique <=5";
 Prix=Dv*3.09+(1*Dv ?? en entier );
 echo "Prix=??";
 }
 elseif {
 echo"Vol partagé on devise le cout par 2 et on affect le resultat aux 2 pilote  "
  Prix=Prix/2;
 echo "cout du vol plané pour chaque pilote "
 // on valorise les vol plané*/
 ?>
 </form>
</body>
</html>
 