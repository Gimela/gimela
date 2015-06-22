<?php
/*
Kean de Souza
import.php 
Crée le 07/05/15

Objectif : 1 - Script PHP permettant d'importer les fichiers uploadé dans la base de données

Dernière modification le : 29/05/15

Edité avec Notepad++ 10/01/15 - 17:20:21 ( Je suis Charlie Edition)

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
	include('core/valorisation.php');
	
	if(isset($_POST['auth']))	{
	echo '<h1>Planche de vol importé </h1>';

	$extensionsAuth = array(".csv", ".html"); // tableau des extensions autorisées
	$taille_maxi = 100000; // Taille maximal du fichier
	global $erreur; // Variable d'errer globale

	if(isset($_FILES['imported'])) // Si le champ file n'est pas vide
		{
		$extension = strrchr($_FILES['imported']['name'],'.');
		$numeroplanche = explode(".",$_FILES['imported']['name']);
		//echo $numeroplanche[0];
		$date_planche_fichier=date( "Y-m-d",strtotime($numeroplanche[0]));
		
		

		if(!in_array($extension, $extensionsAuth)) //Si l'extension n'est pas dans le tableau
			{
			$erreur = 'Vous devez uploader un fichier de type csv ou html';
			goto error;
			}
		
		// Vérification de la taille du fichier
		$taille = filesize($_FILES['imported']['tmp_name']);
		if($taille>$taille_maxi)
			{
			$erreur = 'Le fichier est trop gros...';
			goto error;
			}
		//Permet de ne pas montrer à l'utilisateur le fichier soumi à la base de donnée	

		switch($extension)  
		{
		
		case ".csv": 	if(($data = get2DArrayFromCsv($_FILES['imported']['tmp_name'],';')) !== FALSE)
							{
							// Traitement des informations avant insertion dans la base de donnée
							
							$planche_date = new DateTime ($date_planche_fichier); // Récupérer la date de planche et CAST en DateTime
							$planche_date2 = new DateTime ($date_planche_fichier); 
							while(list($key, $ligne) = each($data)) 
								{ // Pour chaque ligne du tableau 2D
								
								$id_planeur=GetIDPlaneur($ligne[0]); // Recuperer ID du planeur selon l'immatriculation
								$horaire_depart= explode(":", $ligne[1]);							
								$date_depart = $planche_date->setTime($horaire_depart[0], $horaire_depart[1]); 
								
								$horaire_arrivee= explode(":", $ligne[2]);	
								$date_arrivee = $planche_date2->setTime($horaire_arrivee[0], $horaire_arrivee[1]); 
								$date_arrivee->format('Y-m-d H:i:s');
							
								$id_remorqueur=GetIDPlaneur($ligne[4]);
								$duree_remorquage = $ligne[5];
								
								$type_facture = $ligne[8];
								
								$id_pilote = $ligne[9];
								
								if  ( (empty($ligne[10])) OR $ligne[10] == '0' ) $id_pilote2 = 0;
								else $id_pilote2 =$ligne[10];
								
								//echo 'Planeur:'.$id_planeur['num_aeronef'].' - Remorqueur:'.$id_remorqueur['num_aeronef'];
								
								$req = $bdd -> prepare('INSERT INTO `vol`
								(date_vol, id_planeur, date_depart, date_arrivee, id_remorqueur, duree_vol_remq, type_vol, id_pilote, id_passager) 
								VALUES (:date_vol,:planeur,:date_depart,:date_arrivee,:remorqueur,:duree_rem,:type_vol,:id_pilote,:id_2_pilote) ');
								$req->bindValue(':date_vol', $planche_date->format('Y-m-d') , PDO::PARAM_STR);
								$req->bindValue(':planeur', $id_planeur['num_aeronef'], PDO::PARAM_INT);
								$req->bindValue(':date_depart',$date_depart->format('Y-m-d H:i:s'), PDO::PARAM_STR);
								$req->bindValue(':date_arrivee',$date_arrivee->format('Y-m-d H:i:s'), PDO::PARAM_STR);
								$req->bindValue(':remorqueur',$id_remorqueur['num_aeronef'], PDO::PARAM_INT);
								$req->bindValue(':duree_rem',$duree_remorquage, PDO::PARAM_STR);
								$req->bindValue(':type_vol',$type_facture, PDO::PARAM_STR);
								$req->bindValue(':id_pilote',$id_pilote, PDO::PARAM_STR);	
								$req->bindValue(':id_2_pilote',$id_pilote2, PDO::PARAM_INT);
								
								try {
									$req->execute();
								} catch (PDOException $erreur) 
										{
									if (($erreur->getCode()) === '23000')
										{
										echo "<p>Les identifiants pilotes ou les immatriculations référencées dans la planche n'ont pas été trouvé dans la base de donnée, 
										l'ajout de la planche a échoué...\n</p>";
										echo $erreur->getMessage();	
										exit();
										}
									} 
								}
								
								echo '<h2>Facturation de la planche</h2>';
								$req_planche = $bdd ->prepare('SELECT id_vol, type_vol, id_pilote, id_passager FROM vol WHERE date_vol = :datevol ');
								$req_planche->bindValue(':datevol', $planche_date->format('Y-m-d') , PDO::PARAM_STR);
								$req_planche->execute();
								static $res_planche = array();
								$res_planche = $req_planche -> fetchAll(PDO::FETCH_NAMED);
								if (DEBUG) print_r($res_planche);
								$i = 0;
								foreach ($res_planche as $vol) {

										$resultat_valorisation[$i++] = ValoriserVol($vol['id_vol']); 
										//array(0-$id_vol, 1-$res_cu['id_util'], 2-$remorquage, 3-$prix_planeur, 4-$cmp, 5-$prix_vol, 
										//6-$commentaire, 7-$res['id_tarif_remorqueur'], 8- $res['id_tarif_planeur']);
								
									} 	
									if (DEBUG) {echo 'Resultat valorisation'; print_r($resultat_valorisation);}
								$table_facture = '<table border="1" style="text-align:center;" align="center"> <tr> <th>NUMERO VOL</th> <th>ID</th> <th>NOM - PRENOM</th>  <th>FACTURATION</th> <th>PRIX VOL</th> <th>PRIX REMORQUAGE</th> <th>CMP</th> <th>PRIX PLANEUR</th> </tr>';
								 foreach ( $resultat_valorisation as $informer) {
									$info_membre =  GetInformationsUserIdSYS($informer[1]);
									
									// Mouvement Planneur 
									$prix_total_planeur = 0.00;
									$prix_total_planeur = $informer[3] + $informer[4];
									
									// ----------------------
									
									$table_facture.='<tr> <td>'.$informer[0].'</td> <td>'.$info_membre['id_club'].'</td> <td>'.$info_membre['nom'].' '.$info_membre['prenom'].'</td> <td>'.$informer[6].'</td> <td>'.$informer[5].' € </td> <td>'.$informer[2].' €</td> <td>'.$informer[4].' € </td> <td>'.$informer[3].' € </td></tr>';
									 }
								$table_facture.='</table>';
								
								echo $table_facture;
								echo ('<p>La planche à bien été ajouté dans la base, veuillez cliquer sur le lien : <a href="index.php?page=planche_vol&date_planche_vol='.$planche_date->format('Y-m-d').'">Planche du '.$planche_date->format('Y-m-d').'</a></p>');
							}
							BREAK;

	 
		case ".html" : echo ("Fonction non disponible");
						BREAK;
		
		default : echo ("N'a pas pu être traité");
		}
		 
		error : echo $erreur;
		
		if (!isset($erreur))
			{
echo ('
<i><a href="index.php?page=gestionnaire">Retour au menu</a></i>  ');
			}
		
		}
	}
	
	else {
		
echo ('<form method="post" action="#" enctype="multipart/form-data">
<h1>Planche</h1>
<p><label>Fichier (CSV uniquement):<input type="file"  name="imported" id="file_imported"/></label></p>
<input type="hidden" name="MAX_FILE_SIZE" value="100000"/>
<input type = "submit" name="auth" value = "Envoyer la planche et valoriser les vols" style="width:34%; padding:5px"/>
</form>
<p><i><a href="index.php?page=gestionnaire"> <= Retour au menu</a></i></p> ');
	}
}
?>

<?php
function get2DArrayFromCsv($file,$delimiter) {
        if (($handle = fopen($file, "r")) !== FALSE) {
            $i = 0;
            while (($lineArray = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                for ($j=0; $j<count($lineArray); $j++) {
                    $data2DArray[$i][$j] = $lineArray[$j];
                }
                $i++;
            }
            fclose($handle);
        }
        return $data2DArray;
    } 
?>

