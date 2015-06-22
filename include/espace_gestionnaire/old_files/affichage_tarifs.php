<?php
/*
Kean de Souza
affichage_tarifs.php
Objectif : 
	- Voir les tarifs présent sur le site
*/

if(empty($_SESSION)){
	header('refresh: 1; URL=../../index.html');
	exit('Veuillez vous identifiez');
	}
else{
echo ('
	<script type="text/javascript">$(document).ready(function() { document.title = \'Grille tarifaires CVVFR\';});</script>
	<h1>Tarifs CVVFR</h1>
	<p> Liste des tarifs applicables en euros </p>
	<table border="1" align="center"/>
	');

	$liste_tarif=ListerTarifs();
	$tableau = '<th>ID Tarifs</th><th>Description</th><th>Tarifs - '.SEUIL_AGE.'</th><th>Tarifs + '.SEUIL_AGE.'</th>';
	
	foreach($liste_tarif as $value)
		{
		$tableau.='<tr><td>'.$value['id_tarif'].'</td> <td>'.$value['description'].'</td> <td>'.$value['tarif_jeune'].'</td> <td>'.$value['tarif_adulte'].'</td> </tr>';
		}
	echo $tableau;
	
	echo '</table> </form>';

}
?>