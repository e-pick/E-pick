<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Script de r�cup�ration des flux produits d�pos�s dans le dossier /flux/in/produits.
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

require_once("../application/config/bootstrap.php");
require_once("../application/kernel/PDO.php");

define('productDir'	,'../flux/in/produits');						// Le r�pertoire contenant les fichiers .xml 
define('backupFile'	,'backup_produits_' . date('d-m-Y') . '.sql'); 	// Le nom du fichier de backup de la base de donn�es
define('zipName'	,'pro_' . date('d-m-Y') . '.in.bak.zip');		// Le nom du fichier zip 
	

	/* R�cup�rer les fichiers produits.xml */
	$listeProduits 	= array();			// Tableau contenant les r�pertoires du dossier /flux/in/produits.
	$dossier 		= opendir('../flux/in/produits');
	while ($fichier = readdir($dossier)) {
		if (substr($fichier, -7) == ".in.xml") {
			array_push($listeProduits, productDir . '/' . $fichier);
		}
	}
	closedir($dossier); 
		
	if (!array_empty($listeProduits)) {
		
		$log = '';	// Le texte � ajouter dans le fichier log
		
		/* Si les fichiers produits.xml existent */
	
		/************************************
		*									*
		*	Sauvegarder la base de donn�es	* 
		*									*
		************************************/

		$tables = array('PRODUIT', 'EAN');								// Le nom des tables � sauvegarder
		$backup = dumpDB($tables, '../flux/in/produits/backup/' , backupFile);	// Sauvegarde des tables de la base de donn�es (cf. /application/kernel/Common.php)
		$log   .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des produits") . " -- " . gettext("Sauvegarde des tables : ") . implode(", ", $tables) . ' ' . gettext("dans le fichier ") . $backup . "\n";

		/************************************************************************************************ 
		* 																								*
		*	Lire le fichier produits.xml et sauvegarder les diff�rents champs dans la base de donn�es	*
		*																								*
		************************************************************************************************/

		$pdo = DB :: getInstance();
	
		foreach ($listeProduits as $fichier) {
			$XmlFile = new DomDocument();
			$XmlFile->load($fichier);	// On r�cup�re le document produits.xml
			
			/* On r�cup�re l'ensemble des produits */
			$produits = $XmlFile->getElementsByTagName('produit');
			
			$nbInsertions 	= 0;
			$nbMiseAJour	= 0;
			$nbProduits		= 0;
			
			foreach($produits as $produit) {
				$nbProduits++;
				
				/* Pour chaque produit, on r�cup�re les diff�rentes informations le concernant */
				$codeProduit			= $produit->getElementsByTagName('idProduit')->item(0)->nodeValue;
				$libelleProduit 		= html_entities($produit->getElementsByTagName('libelleProduit')->item(0)->nodeValue);
				$photoProduit 			= html_entities($produit->getElementsByTagName('photoProduit')->item(0)->nodeValue);
				if ($photoProduit == null) $photoProduit = 'default.png';
				$largeurProduit 		= $produit->getElementsByTagName('largeurProduit')->item(0)->nodeValue;
				$hauteurProduit 		= $produit->getElementsByTagName('hauteurProduit')->item(0)->nodeValue;
				$profondeurProduit 		= $produit->getElementsByTagName('profondeurProduit')->item(0)->nodeValue;
				$uniteMesure 			= html_entities($produit->getElementsByTagName('uniteMesure')->item(0)->nodeValue);
				$quantiteParUniteMesure = $produit->getElementsByTagName('quantiteParUniteMesure')->item(0)->nodeValue;
				$poidsBrutProduit 		= $produit->getElementsByTagName('poidsBrutProduit')->item(0)->nodeValue;
				$poidsNetProduit 		= $produit->getElementsByTagName('poidsNetProduit')->item(0)->nodeValue;
				$estPoidsVariable 		= $produit->getElementsByTagName('estPoidsVariable')->item(0)->nodeValue;
				$eans 					= $produit->getElementsByTagName('ean');

				/* Mise � jour de la base de donn�es */
				if (($itemProduit = Produit::loadByCodeProduit($pdo, $codeProduit)) == null){
					/* Si le produit n'existe pas */
					$nouveauProduit = true; 
					 
					/* Tester s'il n'existe pas un produit inconnu correspondant � l'ean */
					foreach($eans as $ean){
						$produitInconnu = Produit::selectByEan($pdo, $ean->nodeValue);
						if($produitInconnu != null){ 
							if(substr($produitInconnu->getCodeProduit(),0,7) == 'unknown'){
								$nbInsertions++;
								
								/* Il existe un produit inconnu => Mettre � jour ses informations */
								$produit  = Produit::loadByCodeProduit($pdo, $produitInconnu->getCodeProduit());
								
								$produit -> setCodeProduit($codeProduit,false);
								$produit -> setLibelle($libelleProduit,false);
								$produit -> setPhoto($photoProduit,false);
								$produit -> setLargeur($largeurProduit,false);
								$produit -> setHauteur($hauteurProduit,false);
								$produit -> setProfondeur($profondeurProduit,false);
								$produit -> setUniteMesure($uniteMesure,false);
								$produit -> setQuantiteParUniteMesure($quantiteParUniteMesure,false);
								$produit -> setPoidsBrut($poidsBrutProduit,false);
								$produit -> setPoidsNet($poidsNetProduit,false);
								$produit -> setEstPoidsVariable($estPoidsVariable,false);
								
								$produit->update(); 
								
								$nouveauProduit = false;
							}
						}
					}
					
					/* Le produit n'existe pas dans la base de donn�es */  
					if($nouveauProduit){
						$nbInsertions++;
						// echo 'nouveau' .'<br />';
						$priorite 	= NULL; // Par d�faut
						$stock		= NULL; // Par d�faut
						$tempsAcces = TEMPS_MOYEN_ACCES_PRODUIT; // Par d�faut
						$itemProduit = Produit::create($pdo,$codeProduit,$libelleProduit, $photoProduit, $largeurProduit,$hauteurProduit,$profondeurProduit,$uniteMesure,$quantiteParUniteMesure,$poidsBrutProduit,$poidsNetProduit,$estPoidsVariable,$priorite,$stock,$tempsAcces);
					}
				}
				else {
					/* Si le produit existe */ 
					$nbMiseAJour++;
					
					$itemProduit->setLibelle($libelleProduit,false);
					$itemProduit->setPhoto($photoProduit,false);
					$itemProduit->setLargeur($largeurProduit,false);
					$itemProduit->setHauteur($hauteurProduit,false);
					$itemProduit->setProfondeur($profondeurProduit,false);
					$itemProduit->setUniteMesure($uniteMesure,false);
					$itemProduit->setQuantiteParUniteMesure($quantiteParUniteMesure,false);
					$itemProduit->setPoidsBrut($poidsBrutProduit,false);
					$itemProduit->setPoidsNet($poidsNetProduit,false);
					$itemProduit->setEstPoidsVariable($estPoidsVariable,false);
					$itemProduit->update();
				}
			
				/* Sauvegarde des codes eans */
				$produitk 	= Produit::loadByCodeProduit($pdo, $codeProduit);					// Chargement du produit
				$listeEan 	= Ean::selectByProduit($pdo, $produitk->getIdProduit());	// Liste des eans d�j� pr�sents dans la bases de donn�es pour chaque produit
				foreach($eans as $ean) {
					$codeEan = $ean->nodeValue;
					if(!in_array($codeEan, $listeEan)) {
						/* Si, pour un produit donn�, l'ean n'est pas pr�sent dans la table => Ajouter */
						if ($codeEan != null && $codeEan != ''){
							$eanTest	= Ean::loadByCodeEan($pdo, $codeEan);
							if ($eanTest == null){
								/* Si le code EAN n'est pas utilis� par un autre produit, on ins�re */
								$itemEan 	= Ean::create($pdo, $produitk->getIdProduit(), $codeEan);
							}
							else{
								/* Ecrire un log */
								$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des produits") . " -- " . gettext("Erreur ajout code EAN ") . $codeEan . gettext(' pour le produit : ') .$libelleProduit . '. ' . gettext("Le code EAN est utilis&eacute; par un autre produit.") . "\n";
							}
						}
					}
				}
			} 
			$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des produits") . " -- " . gettext("R&eacute;cup&eacute;ration du fichier : ") . $fichier . gettext(" contenant ") . $nbProduits . gettext(" produit(s).") . "\n";
			$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des produits") . " -- " . gettext("Nombre d'insertions : ") . $nbInsertions . '. ' . gettext("Nombre de mise &agrave; jour : ") . $nbMiseAJour . "\n";
		}
	 
		/********************************************************************************************************************************
		* 																																*
		*	Zipper le fichier produits.xml & backup.sql et les sauvegarder dans /produits/save et les supprimer de l'emplacement actuel	* 
		*																																*
		********************************************************************************************************************************/
		
		/* liste des fichiers � compresser */
		$files = array ($backup);				
		foreach($listeProduits as $fichier){
			array_push($files, $fichier);
		}
		$zip   = zipper($files, '../flux/in/produits/save', zipName);// Cr�ation d'un zip avec les fichiers pass�s en param�tres (cf. /application/kernel/Common.php)
		
		/* On supprime les fichiers du r�pertoire courant */
		foreach($files as $file) {
			if (file_exists($file)) unlink($file);
		}

		/****************************
		 * 							*
		 * 		Ecrire le log 		*
		 *							*
		 ****************************/
		
		$logFile = '../flux/in/produits/logs/PRO_' . date('d-m-Y') . '.log';
		if(!writeLog($log, $logFile)) die("Erreur �criture de log");		// Ecrire le log dans le fichier pass� en param�tre (cf. /application/kernel/Common.php)
	}	
?>