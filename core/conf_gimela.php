<?php
/**
|	Fichier de configuration du Système de Vol à Voile CVVFR
|	- Connexion base de donnée
|	- Choix du langage( à développer)


Crée le O1/04/15 par Kean de Souza (kean.desouza@gmail.com)
Dernière modification le : 01/04/15 par Kean de Souza 
Edité avec Notepad++ 10/01/15 - 17:20:21 ( Je suis Charlie Edition)

*/

//  SAUVEGARDE_OPE Autorise le système à sauvergarder tous les changements sur les tables
// Les seules sauvegardes effectué seront l'import des planches de vol

define("SAUVEGARDE_OPE", FALSE); 

define("NOM_CLUB", 'CVVFR');
define("SIGLE_MONETAIRE", '€');
define ("SEUIL_AGE", 25 );
define ("CMR", 1); // CMR en € par vol
define ("SEUIL_HEURES_GRATUITES", 5);
define ("CMP", 1); // Prix de la Contribution Spéciale Maintenance et Prévention Casse
define('CMP_MAX_SAISON', 80); // Heure de CMP réglé par saison

// Id de l'utilisateur système : Nécessite d'etre inscrit dans la base de donnée !
//define("ID_SYS", 150152015);
define("ID_SYS", 3);

define ("MB_DESINSCRIT", 3);

define ("STATUT_ACCES_MEMBRE", 2 );
define ("STATUT_ACCES_GESTION", 4 );
define ("STATUT_ACCES_ADMINISTRATION", 5 );
define ("STATUT_ACCES_SA", 6 );

/*---------------------------------------------

		Mode Développeur

-----------------------------------------------*/

define("DEBUG", FALSE);
define("DEBUG_SQL", FALSE); // Visualiser le résultat des requetes effectué

/*---------------------------------------*/


// Tableau des forfaits avec leurs ID issu la table des tarif et leurs valeurs en heures affecté 
//----> $forfait['id_table_tarif'] = heure_affecte ;

global $forfait;

$forfait[37] = 0;
$forfait[38] = 0;
$forfait[39] = 0; // Formule Vélivole 150 H
$forfait[40] = 0;	// Formule Vélivole 200 H
$forfait[41] = 0;	// Formule Vélivole 40 H
$forfait[42] = 0;	// Formule Vélivole 80 H
//----------------------------------------------

function use_forfait() 	{
	
	var_dump($forfait);
}

/*----------------------------------------------------------------------
|																		|
|	Tableau des pages accessibles par les différents utilisateurs		|
|																		|
------------------------------------------------------------------------*/

$pagesOK['accueil'] = '/var/www/gimela/include/accueil.php';
$pagesOK['auth'] = '/var/www/gimela/include/authentification.php';
$pagesOK['inscription'] = '/var/www/gimela/include/formulaire_inscription.php';
$pagesOK['verification_inscription'] = '/var/www/gimela/include/verification.php';
$pagesOK['motdepasseperdu'] = '/var/www/gimela/include/mot_de_passe.php';

$pagesOK['membre'] = '/var/www/gimela/models/espace_membre.php';

$pagesOK['gestionnaire'] = '/var/www/gimela/models/espace_gestionnaire.php';
$pagesOK['alerte']='/var/www/gimela/include/espace_gestionnaire/alerte_user.php';
$pagesOK['journal_des_mouvements']='/var/www/gimela/include//espace_gestionnaire/module_journal_mouvement.php';
$pagesOK['operations_mouvements']='/var/www/gimela/include//espace_gestionnaire/operation_compte.php';
$pagesOK['import']='/var/www/gimela/include/espace_gestionnaire/import.php';
$pagesOK['planche_vol']='/var/www/gimela/include/espace_gestionnaire/consulter_planche_vol.php';
$pagesOK['consultation_membre'] = '/var/www/gimela/include/espace_gestionnaire/consultation.php';

$pagesOK['administrateur']='/var/www/gimela/models/espace_administrateur.php';
$pagesOK['tarif'] = '/var/www/gimela/include/espace_gestionnaire/affichage_tarifs.php';
$pagesOK['gestion_tarif'] = '/var/www/gimela/include/espace_administrateur/tarifs.php';
$pagesOK['gestion_forfait'] = '/var/www/gimela/include/espace_administrateur/modification_forfait.php';
$pagesOK['operation_systeme'] = '/var/www/gimela/include/espace_administrateur/operation.php';
$pagesOK['suppression_membre'] = '/var/www/gimela/include/espace_administrateur/suppression.php';
$pagesOK['supprimer_membre'] = '/var/www/gimela/include/espace_administrateur/action_supprimer.php';
$pagesOK['restauration_membre'] = '/var/www/gimela/include/espace_administrateur/restauration.php';
$pagesOK['modification_tarif'] = '/var/www/gimela/include/espace_administrateur/modification_tarif.php';
$pagesOK['journal_operation'] = '/var/www/gimela/core/espace_sa/module_journal_op.php';
$pagesOK['gestion_aeronef'] = '/var/www/gimela/core/espace_administrateur/gestion_planeur.php';
$pagesOK['editer_compte_pilote'] = '/var/www/gimela/include/espace_gestionnaire/editer_compte.php';

$pagesOK['kjazh42tgh41'] = '/var/www/gimela/core/espace_sa/espace_supadmin.php';
$pagesOK['ez4gz6gaf1eh'] = '/var/www/gimela/core/espace_sa/option_su.php';
$pagesOK['creation_utilisateur'] = '/var/www/gimela/core/espace_sa/creation_utilisateur_su.php';
$pagesOK['promotion_utilisateur']='/var/www/gimela/core/espace_sa/promotion_user.php';

$pagesOK['menu']='/var/www/gimela/models/espace_generale.php';

?>
