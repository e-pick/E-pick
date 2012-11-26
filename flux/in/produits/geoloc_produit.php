<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Script de géolocalisation d'un produit.
 *
 * Etapes :
 *			1) Sauvegarde de la base de données
 *			2) Lecture du fichier produits.xml et enregistrement dans la base de données
 *			3) Création d'un zip contenant la sauvegarde de la base de données et le fichier produits.xml. Ces fichiers seront supprimés du répertoire /flux/in/produits
 *			   Le fichier .zip sera placé dans le répertoire /flux/in/produits/save
 *			4) Ecriture dans le fichier log situé dans /flux/in/produits/logs. Un fichier log est créé pour chaque mois.
 *
 * /!\ ATTENTION /!\ : Ce script ne fonctionnera pas s'il est déplacé de cet emplacement.
 *
 */
error_reporting(E_ALL ^ E_NOTICE);

define ('applicationDir', '../../../../E-Pick/');
require_once(applicationDir."application/config/bootstrap.php");
require_once(applicationDir."application/kernel/PDO.php");
require_once(applicationDir."application/kernel/Request.php");
require_once(applicationDir."application/models/Produit.php");


class MonCodeEan {

$CodeEan = $produit::$_request->getVar('ean'); 

}

//$pdo = DB :: getInstance();


if (isset($_GET['ean'])) $ean = $_GET['ean']; else { 
	   				echo "ER - Ean !". $commande; 
					die;
					}		// Le nom de la commande (du fichier)



//On recupere le code EAN de l'application

echo $ean;

//$eans 	= $_request('ean');
//on localise les informations du fichier
//$pdo = DB :: getInstance();

// on renvoi les informations au PDA


?>