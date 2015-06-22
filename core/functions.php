<?php
/**
| 	Functions.php
|	Objectif : Fichier regroupant les fonctions utilisées par le système
|
|
|
|
|

Crée le O1/04/15 par Kean de Souza (kean.desouza@gmail.com)
Dernière modification le : 01/04/15 par Kean de Souza 

Edité avec Notepad++ 10/01/15 - 17:20:21 ( Je suis Charlie Edition)

*/

// Fonction de conversion de date du format américain (AAAA-MM-JJ) vers le format français (JJ/MM/AAAA)
function dateUS2FR($date)	{	
	$date = explode('-', $date);
	$date = array_reverse($date);
	$date = implode('/', $date);
	return $date;
	}
	
function dateFR2US($date) {
	$date = explode('/', $date);
	$date = array_reverse($date);
	$date = implode('-', $date);
	return $date;
}
	
// Calcul de la durée d'un vol entre 2 type DATETIME SQL
function dureeVol($depart,$arrivee){
	$heure_depart= new DateTime($depart);
	$heure_arrivee= new DateTime($arrivee);
	$duree=$heure_depart->diff($heure_arrivee);
	return $duree->format('%D - %H:%I');
}

function dureeVolMinute($depart, $arrivee){
	$heure_depart= new DateTime($depart);
	$heure_arrivee= new DateTime($arrivee);
	$duree=$heure_arrivee->diff($heure_depart);
	return idate('i',$duree);
}

//Fonction permettant de calculer l'age actuel 
function Age($date_naissance)
    {
	$date_n=dateUS2FR($date_naissance);
    $am = explode('/', $date_n);
    $an = explode('/', date('d/m/Y'));
 
    if(($am[1] < $an[1]) || (($am[1] == $an[1]) && ($am[0] <= $an[0])))
    return $an[2] - $am[2];
    return $an[2] - $am[2] - 1;
    }
	
//Fonction d'affichage de minutes à heure/minutes sans limitation
function MinutesAHeuresMinutes($minutes){
$heures = floor($minutes / 60);
$minutes = $minutes % 60;
if ($minutes == 0 && $heures == 1) return $heures.' heure';
elseif ($minutes == 0 && $heures > 1) return $heures.' heures';
else return $heures.':'.$minutes;
}


?>