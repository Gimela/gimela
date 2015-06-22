<?php
// Christeddy Milapie
//Modification par Kean de Souza le 18/06/15

if(empty($_SESSION)){
	header('refresh: 1; URL=index.php');
	exit('Veuillez vous identifiez');
	}
elseif($_SESSION['id_statut'] < STATUT_ACCES_ADMINISTRATION)
	{
	header('refresh: 1; URL=index.php?page='.$_SESSION['page_defaut'].'');
	exit('Vous n\'avez pas les droit nécessaires pour consulter ce fichier. Vous serez redirigé.');
	}
elseif($_SESSION['id_statut'] >= STATUT_ACCES_ADMINISTRATION)
	{	
	$resultat = $reqUserStatutById->execute(array(':id'=>$_SESSION["id"]));
	$resultat = $reqUserStatutById->fetch(); // Récuperer les infos concernant l'utilisateur
	$reqUserStatutById->CloseCursor();
	
	// requete: affichage des tarifs
	$resu = $reqAffTarifs->execute();
	$resu = $reqAffTarifs->fetchAll(PDO::FETCH_NAMED);
	$tabTarif = '<tr><th>CODE BARRE</th><th>TARIFS</th><th>- '.SEUIL_AGE.' ans</th><th> +'.SEUIL_AGE.' ans</th></tr>';
	
	foreach($resu as $row){
			$tabTarif.='<tr><td align="center"><a href="index.php?page=modification_tarif&amp;id='.$row['code_barre'].'" >'.$row['code_barre'].'</a></td><td>'.$row['description'].'</td><td align="center"  nowrap>'.$row['tarif_jeune'].' '.SIGLE_MONETAIRE.'</td><td align="center" nowrap>'.$row['tarif_adulte'].' '.SIGLE_MONETAIRE.'</td></tr>';
				}
   
		echo('	<h1>Tarifs CVVFR</h1>
		<script type="text/javascript">$(document).ready(function() { document.title = \'Grille Tarifaires CVVFR\';});</script>
				<table border="4" align="center">
				'.$tabTarif.'
				</table>
				<p><a href="index.php?page=administrateur">Retourner au menu</a></p>');
	}
?>