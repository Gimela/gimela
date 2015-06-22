<?php
session_start();
/*
Kean de Souza
18/06/2015
parametrage_sys_cfg.php
	Objectif : Modifier en ligne le fichier conf_gimela

*/

	$fichier="./conf_gimela.php";
	//ouverture en lecture et modification
	$text=fopen($fichier,'r') or die("Fichier manquant");
	$contenu=file_get_contents($fichier);
	
	fclose($text);
	
	$tab=explode('"',$contenu);
	
	//print_r($tab);
	
	$i=0;
	echo '<br/><br/>';
	for ($i=0; $i < 20; $i++) {
		echo $tab[$i];
		}
	
	//ouverture en écriture
	// $text2=fopen($fichier,'w+') or die("Fichier manquant");
	// fwrite($text2,$contenuMod);
	// fclose($text2);


?>