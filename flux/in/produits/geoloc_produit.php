<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Script de g�olocalisation d'un produit.
 *
 * Etapes :
 *			1) Sauvegarde de la base de donn�es
 *			2) Lecture du fichier produits.xml et enregistrement dans la base de donn�es
 *			3) Cr�ation d'un zip contenant la sauvegarde de la base de donn�es et le fichier produits.xml. Ces fichiers seront supprim�s du r�pertoire /flux/in/produits
 *			   Le fichier .zip sera plac� dans le r�pertoire /flux/in/produits/save
 *			4) Ecriture dans le fichier log situ� dans /flux/in/produits/logs. Un fichier log est cr�� pour chaque mois.
 *
 * /!\ ATTENTION /!\ : Ce script ne fonctionnera pas s'il est d�plac� de cet emplacement.
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