<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * BOOTSTRAP.PHP
 *
 * Fichier de configuration. On y trouve les accès la DB, la configuration de smarty
 * et la déclaration de d'autres variables.
 */

//conf file
  

ob_start(); 
session_start();
// setlocale(LC_TIME, "fr_FR");
ini_set('display_errors', 1); // activation des erreurs
//error_reporting(E_ALL ^ E_NOTICE); // affichage de toutes les erreurs sans les notices
//error_reporting(E_ALL); // affichage de toutes les erreurs
// error_reporting(-1);
// ob_implicit_flush();

set_time_limit(-1);  
ini_set("max_execution_time", "-1");
ini_set('memory_limit','4G');

// date_default_timezone_set("Europe/Paris");

if(!defined('applicationDir')):
define ('applicationDir', '../');
endif;

 
/*
 * 
 * GetText configuration
 *
 */
// define('LANGUAGE'			, 'fr_FR');		// On définie une constante qui contient le code
// define('ABREVIATION'		, 'fra');
// define('LANGUAGE_FILE_NAME'	, 'messages');	// Le nom de nos fichiers .mo

// putenv("LANG=" . LANGUAGE . '.UTF8'); // On modifie la variable d'environnement
// setlocale(LC_ALL, LANGUAGE . '.UTF8'); // On modifie les informations de localisation en fonction de la langue
// setlocale (LC_TIME,  LANGUAGE, ABREVIATION);
// bindtextdomain(LANGUAGE_FILE_NAME, '../application/lang'); // On indique le chemin vers les fichiers .mo
// bind_textdomain_codeset ( LANGUAGE_FILE_NAME , "UTF-8" );
// textdomain(LANGUAGE_FILE_NAME); // Le nom du domaine par défaut
// $appliConfFile = "../application/config/conf/pp.ini";


/*
 *
 * Autoload magic function
 *
 */
function auto_require($class_name) { 
    $possibilities = array( 
        applicationDir.'application/kernel/'.$class_name . '.php', 
        applicationDir.'application/controllers/'.$class_name . '.php',   
        applicationDir.'application/models/'.$class_name . '.php',
        applicationDir.'libraries/smarty/'.$class_name.'.class.php', 
        applicationDir.'libraries/smarty/sysplugins/'.strtolower($class_name).'.php'
    ); 
    foreach ($possibilities as $file) { 
        if (file_exists($file)) { 
            require_once($file); 
            return true; 
        } 
    } 
    return false; 
};


spl_autoload_register('auto_require'); 



/*
 *
 * Others functions
 *
 */
require_once(applicationDir.'application/kernel/Common.php');

 
/*
 * 
 * Smarty configuration
 * 
 */
$smarty = new Smarty();	    
$smarty->template_dir	= applicationDir.'application/tpls/templates/';
$smarty->compile_dir 	= applicationDir.'application/tpls/templates_c/';
$smarty->config_dir 	= applicationDir.'application/tpls/configs/';
$smarty->cache_dir 		= applicationDir.'application/tpls/cache/'; 
$smarty->plugins_dir 	= applicationDir.'libraries/smarty/plugins/'; 

 
/*
 * 
 * Définition des différents profils et constantes utilisés dans l'application
 * 
 */
define ('DEFAULT_TEMPLATE' 		, 'navigateur1024');

// define ('RESULTAT_PAR_PAGE'		, 50);
// define ('DELAI_AVANT_LIVRAISON'	, 10800);/* DELAI AVANT LA LIVRAISON */
// define ('DEVISE'				, '€');

/*choix de la finesse utilisee, il faut changer le define finesse utilisee */
define ('FINESSE_RAYON'			, 1);
define ('FINESSE_SEGMENT'		, 2);
define ('FINESSE_ETAGERE'		, 3);
// define ('FINESSE_UTILISEE'		, FINESSE_ETAGERE);

/******************************/
/* NE PLUS TOUCHER EN DESSOUS */
/******************************/ 
define ('PROFIL_PREPARATEUR'	, 1);
define ('PROFIL_SUPERVISEUR'	, 2);
define ('PROFIL_ADMINISTRATEUR'	, 3);


/* Priorités de passage */
define ('PRIORITE_DEBUT'		, 3);
define ('PRIORITE_NORMAL'		, 2);
define ('PRIORITE_FIN'			, 1);
/* Etat des préparations */
define ('FIN_PREPARATION'		, 3);
define ('EN_COURS_PREPARATION'	, 2);
define ('EN_ATTENTE_PREPARATION', 1);


$ARRAY_ETAT_COMMANDE	= array(gettext('En attente d\'affectation'),gettext('En attente de pr&eacute;paration'),gettext('En cours de pr&eacute;paration'),gettext('Termin&eacute;e'),gettext('Annul&eacute;e'));
$ARRAY_ETAT_PREPARATION	= array(gettext('En attente de pr&eacute;paration'),gettext('En attente de saisie PDA'),gettext('En cours de saisie PDA'),gettext('Termin&eacute;e'));
$ARRAY_COMMANDE_ARCHIVEE = array(gettext('Non'),gettext('Oui'));

// $smarty->assign('application_path'				, APPLICATION_PATH);
$smarty->assign('finesse_rayon'					, FINESSE_RAYON);
$smarty->assign('finesse_segment'				, FINESSE_SEGMENT);
$smarty->assign('finesse_etagere'				, FINESSE_ETAGERE);
// $smarty->assign('finesse_utilisee'				, FINESSE_UTILISEE);

$smarty->assign('array_etat_commande'			, $ARRAY_ETAT_COMMANDE);
$smarty->assign('array_etat_preparation'		, $ARRAY_ETAT_PREPARATION);
$smarty->assign('array_commande_archivee'		, $ARRAY_COMMANDE_ARCHIVEE);
// $smarty->assign('devise'						, DEVISE);

/* Messages du menu haut */
$smarty->assign('menu_accueil'					, gettext('Accueil'));
$smarty->assign('menu_commandes'				, gettext('Les commandes'));
$smarty->assign('menu_produits'					, gettext('Les produits'));
$smarty->assign('menu_utilisateurs'				, gettext('Utilisateurs'));
$smarty->assign('menu_planning'					, gettext('Le planning'));
$smarty->assign('menu_gerer_les_utilisateurs'	, gettext('Gerer les utilisateurs'));
$smarty->assign('menu_reinit_modelisation'		, gettext('R&eacute;initialiser la mod&eacute;lisation'));
$smarty->assign('menu_admin'					, gettext('Administration'));
$smarty->assign('menu_modelisation'				, gettext('La mod&eacute;lisation'));
$smarty->assign('menu_magasin'					, gettext('Mod&eacute;liser le magasin'));
$smarty->assign('menu_stats'					, gettext('Les statistiques'));
$smarty->assign('menu_messages'					, gettext('G&eacute;rer les messages'));
$smarty->assign('menu_config_application'		, gettext('Configuration de l\'application'));
$smarty->assign('menu_config_planning'			, gettext('Configuration du planning'));
$smarty->assign('menu_config_affectation'		, gettext('Configuration de l\'affectation'));
$smarty->assign('menu_demo'						, gettext('Simulation de parcours'));
$smarty->assign('menu_vider_geolocalisation'	, gettext('Vider la geolocalisation'));
$smarty->assign('menu_vider_produit_commande'	, gettext('Vider la base produit/commande'));

/* Messages du menu gauche pour Produits */
$smarty->assign('menu_organisation'				, gettext('Organisation'));
$smarty->assign('menu_etage'					, gettext('Etage'));
$smarty->assign('menu_zone'						, gettext('Zone'));
$smarty->assign('menu_rayon'					, gettext('Rayon'));
$smarty->assign('menu_import_export'					, gettext('Importer/Exporter des rayons'));
$smarty->assign('menu_gestion_priorite'			, gettext('Gestion des priorit&eacute;s de picking'));
$smarty->assign('menu_affectation_rayons'		, gettext('Affectation rayons'));
$smarty->assign('menu_integrite'				, gettext('Int&eacute;grit&eacute;'));
$smarty->assign('menu_non_geolocalises'			, gettext('Non g&eacute;olocalis&eacute;s'));
$smarty->assign('menu_produits_inconnus'		, gettext('Produits inconnus'));
$smarty->assign('menu_codes_ean'				, gettext('Codes ean'));
$smarty->assign('menu_flux_produits'			, gettext('Flux Produits'));
$smarty->assign('menu_produits_synchro'			, gettext('Charger les produits'));
$smarty->assign('menu_geo_vers_Pda'				, gettext('Envoyer produits vers PDA pour g&eacute;oloc.'));
$smarty->assign('menu_geo_du_Pda'				, gettext('R&eacute;cup&eacute;ration de la g&eacute;olocalisation'));
$smarty->assign('txt_confirm_produits'			, gettext('Importer les produits'));
$smarty->assign('txt_confirm_geoloc_import'			, gettext('Importer la g&eacute;olocalisation'));
$smarty->assign('txt_confirm_geoloc_export'			, gettext('Exporter la g&eacute;olocalisation'));



/* Messages du menu gauche pour Commandes */
$smarty->assign('menu_gestion_commandes'		, gettext('Gestion commandes'));
$smarty->assign('menu_gestion_commandes_affect'	, gettext('Gestion pr&eacute;parations'));
$smarty->assign('menu_recup_commandes'			, gettext('Charger les commandes'));
$smarty->assign('menu_gestion_mes_preparations'	, gettext('Mes pr&eacute;parations'));
$smarty->assign('menu_gestion_affect_liste'		, gettext('Liste des pr&eacute;parations'));
$smarty->assign('menu_gestion_affect_manuelle'	, gettext('Affectation des commandes'));
$smarty->assign('menu_gestion_clientele'		, gettext('Gestion client&egrave;le'));
$smarty->assign('menu_clients'					, gettext('Clients'));
$smarty->assign('menu_gestion_planning'	, gettext('G&eacute;rer le planning'));
$smarty->assign('menu_planning'				, gettext('Le planning'));
$smarty->assign('txt_confirm_commandes'	, gettext('Importer les commandes'));

$smarty->assign('txt_se_deconnecter'			, gettext('Se d&eacute;connecter'));

define ('LARGEURSEGMENT' 	, 31);
define ('HAUTEURSEGMENT' 	, 13);
define ('ECHELLE_1M' 		, 27);
define ('VITESSE_MparS' 	, 1);
define ('TEMPS_PRELEVEMENT'	, 20);

/* Récupérer les fichiers commandes.xml */
$countCommandes = 0;			// Tableau contenant les fichiers .xml du répertoire /flux/in/commandes.
$dossier 		= opendir(applicationDir.'/flux/in/commandes/');
while ($fichier = readdir($dossier)) {
	if (substr($fichier, -7) == ".in.xml") {
		$countCommandes++;
	}
}
closedir($dossier);
define ('COUNT_COMMANDES'	, $countCommandes);
$smarty->assign('count_commandes', COUNT_COMMANDES);

//dépend de l'affichage /!\ il faut que les dimensions correspondent à la taille affichée en xhtml/css
$smarty->assign('largeursegment', LARGEURSEGMENT);
$smarty->assign('hauteursegment', HAUTEURSEGMENT);
$smarty->assign('echelle'		, ECHELLE_1M);



/*
 * 
 * Router configuration
 * 
 */
require_once (applicationDir.'application/kernel/PDO.php');
require_once (applicationDir.'application/kernel/Router.php');
Router::setSmarty($smarty);
