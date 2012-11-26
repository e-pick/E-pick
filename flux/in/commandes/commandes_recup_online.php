<?php
//error_reporting(E_ALL ^ E_NOTICE);

$Chemin_Fichier="./";//Avec le / à la fin
$File=$Chemin_Fichier."Demo_".date("dmYHi").".in.xml";
$datetime = Date(now);
$timestamp = strtotime(date("Y-m-d H:i:s"));


// foreach($_POST as $key => $val) echo $val;
// Exit;

if ($_POST["Saisie"]== "Ranger")
{
   	$Nom = $_POST["Nom_Prep"];
	$Prenom = "Préparateur : ".$_POST["Prenom_Prep"];
	$Adresse = "";
	$Ville = "";
	$CP = "";
	}
 	Else { 
	 $Nom = $_POST["Nom"];
	 $Prenom = $_POST["Prenom"];
	 $Adresse = $_POST["Adresse"];
	 $Ville = $_POST["Ville"];
	 $CP = $_POST["CP"];
}



$file= fopen($File, "w+");	
	$xml="";
	$xml.= "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";	 	
	$xml.="<commande> \n";
		$xml.="<idCommande>Demo_".date("dmYHi")."</idCommande> \n";
		$xml.="<dateCommande>".$timestamp."</dateCommande> \n";
		$xml.="<modeLivraison>Au Drive</modeLivraison> \n";
		$xml.="<dateLivraison></dateLivraison> \n";
		$xml.="<destinataireLivraisonNom>".$Nom."</destinataireLivraisonNom> \n";
		$xml.="<destinataireLivraisonPrenom>".$Prenom."</destinataireLivraisonPrenom> \n";
		$xml.="<codePaysLivraison>FR</codePaysLivraison> \n";
		$xml.="<codePostalLivraison>".$CP."</codePostalLivraison> \n";
		$xml.="<codeInseeLivraison></codeInseeLivraison>\n";
		$xml.="<regionLivraison></regionLivraison> \n ";
		$xml.="<municipaliteLivraison>".$Ville."</municipaliteLivraison> \n";
		$xml.="<ligneAdresseLivraison>".$Adresse."</ligneAdresseLivraison> \n";
		$xml.="<nomRueLivraison></nomRueLivraison> \n";
		$xml.="<numeroBatimentLivraison></numeroBatimentLivraison> \n";
		$xml.="<uniteLivraison></uniteLivraison>\n";
		$xml.="<boitePostaleLivraison></boitePostaleLivraison> \n";
		
		$xml.="<idClient>999999999</idClient> \n";
		$xml.="<nomClient>".$Nom."</nomClient> \n";
		$xml.="<prenomClient>".$Prenom."</prenomClient> \n";
		$xml.="<civiliteClient></civiliteClient> \n";
		$xml.="<nomEntreprise></nomEntreprise> \n";
		$xml.="<telephoneClient></telephoneClient> \n";
		$xml.="<codePaysFacturation>FR</codePaysFacturation>\n";
		$xml.="<codePostalFacturation>".$CP."</codePostalFacturation>\n";
		$xml.="<codeInseeFacturation></codeInseeFacturation>\n";
		$xml.="<regionFacturation></regionFacturation> \n";
		$xml.="<municipaliteFacturation>".$Ville."</municipaliteFacturation>\n";
		$xml.="<ligneAdresseFacturation>".$Adresse."</ligneAdresseFacturation>\n";
		$xml.="<nomRueFacturation> </nomRueFacturation> \n";
		$xml.="<numeroBatimentFacturation> </numeroBatimentFacturation>\n";
		$xml.="<uniteFacturation></uniteFacturation> \n";
		$xml.="<boitePostaleFacturation> </boitePostaleFacturation> \n";
		$xml.="<destinataireFacturation>".$Nom."</destinataireFacturation> \n";
		$xml.="<carteFidelite></carteFidelite> \n";
		$xml.="<commentaireClient></commentaireClient> \n";
		
		 
		
		$xml.="<lignesCommandes>\n";

		for($i=0;$i<count($_POST["CodeProd"]);$i++)
							{  
							
							$cod_prod = str_replace("\r\n","",$_POST["CodeProd"][$i]);
							
			$xml.="<ligneCommande>\n";
			$xml.="<idProduit>".$cod_prod."</idProduit>\n";
			$xml.="<quantiteCommandee>1</quantiteCommandee>\n";
			//if(isset($rowTemps['PRO_ESTUNLOT'])) {
				//$xml.="<estDansUnLot>".$rowTemps['PRO_ESTUNLOT'] ."  </estDansUnLot>\n";
				//$xml.="<idLot>".$rowTemps['ID_LOTPRODUIT'] ." </idLot> \n";
				//$xml.="<libelleLot>  </libelleLot> \n";
				//$xml.="<codeEanLot>  </codeEanLot>\n";
				
			//} else {
				$xml.="<estDansUnLot>0</estDansUnLot>\n";
				$xml.="<idLot></idLot> \n";
				$xml.="<libelleLot></libelleLot> \n";
				$xml.="<codeEanLot></codeEanLot>\n";
			//}
			
			 
			$xml.="<prixUnitaireTTC></prixUnitaireTTC>\n";
			$xml.="<codeEanScanne>".$_POST["myInputs"][$i]."</codeEanScanne>\n";
			$xml.="<quantitePreparee></quantitePreparee>\n";
			$xml.="<prixUnitaireTTCPrepare> </prixUnitaireTTCPrepare> \n";
			$xml.="</ligneCommande>\n";
		}  

		
	 	$xml.="</lignesCommandes> \n";
	$xml.="</commande> \n";
	
	

fwrite($file, $xml);
fclose($file);



/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Script de récupération des flux commandes déposés dans le dossier /flux/in/commandes.
 *
 * Etapes :
 *			1) Sauvegarde de la base de données.
 *			2) Lecture du fichier commandes.xml et enregistrement dans la base de données.
 *			3) Création d'un zip contenant la sauvegarde de la base de données et le fichier commandes.xml. Ces fichiers seront supprimés du répertoire /flux/in/commandes.
 *			   Le fichier .zip sera placé dans le répertoire /flux/in/commandes/save.
 *			4) Ecriture dans le fichier log situé dans /flux/in/commandes/logs. Un fichier log est créé pour chaque mois.
 *
 * /!\ ATTENTION /!\ : Ce script ne fonctionnera pas s'il est déplacé de cet emplacement.
 *
 */


define('applicationDir', '../../../../E-Pick/');
require_once(applicationDir.'application/config/bootstrap.php');
require_once(applicationDir.'application/kernel/PDO.php');


define(applicationDir,'flux/in/commandes');							// Le répertoire contenant les fichiers .xml 
define('backupFile', 'backup_commandes_' . date('Y-m-d') . '.sql'); 	// Le nom du fichier de backup de la base de données
define('zipName','magasin_' . date('d-m-Y_H-i-s') . '.in.bak.zip');		// Le nom du fichier zip 

	

	/* Récupérer les fichiers commandes.xml */
	$listeCommandes = array();			// Tableau contenant les fichiers .xml du répertoire /flux/in/commandes.
	$dossier 		= opendir(applicationDir.'flux/in/commandes/');
	while ($fichier = readdir($dossier)) {
		if (substr($fichier, -7) == ".in.xml") {
			array_push($listeCommandes, $fichier);
		}
	}
	closedir($dossier);
	 
	if (!array_empty($listeCommandes)) {
	
		$log = '';	// Le texte à ajouter dans le fichier log
	
		/* Si les fichiers commandes.xml existent */
	
		/************************************
		*									*
		*	Sauvegarder la base de données	* 
		*									*
		************************************/

		$tables = array('CLIENT', 'COMMANDE');							// Le nom des tables à sauvegarder (en majuscule)							
		$backup = dumpDB($tables, applicationDir.'/flux/in/commandes' , backupFile);	// Sauvegarde des tables de la base de données (cf. /application/kernel/Common.php)
		$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des commandes") . " -- " . gettext("Sauvegarde des tables : ") . implode(", ", $tables) . ' ' . gettext("dans le fichier ") . $backup . "\n";
		
		/************************************************************************************************ 
		* 																								*
		*	Lire les fichier commandes.xml et sauvegarder les différents champs dans la base de données	*
		*																								*
		************************************************************************************************/
		
		$pdo = DB :: getInstance();

		foreach ($listeCommandes as $fichier) {

		
			$XmlFile = new DomDocument();
			$XmlFile->load($fichier);	// On récupère le document commandes.xml
			$log 	.= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des commandes") . " -- " . gettext("R&eacute;cup&eacute;ration du fichier : ") . $fichier . "\n";
			
			/* On récupère la commande */
			$commandes 	= $XmlFile->getElementsByTagName('commande');
			$commande	= $commandes->item(0);
			

			/* On récupère les différentes informations concernant la commande */
			$codeCommande = $commande->getElementsByTagName('idCommande')->item(0)->nodeValue;
			if(Commande::loadByCode($pdo,$codeCommande) != null) {
				$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des commandes") . " -- " . gettext("Le fichier") . ' : '. $fichier . ' ' . gettext("contenant la commande") . ' : ' . $codeCommande . ' ' . gettext("existe d&eacute;j&agrave; dans la base de donn&eacute;es et a &eacute;t&eacute; rejet&eacute;") . "\n";
				break;
			}
			$dateCommande = $commande->getElementsByTagName('dateCommande')->item(0)->nodeValue;
			$etatCommande = '0';
			
			/* On récupère l'adresse de livraison */
			$modeLivraison 				= html_entities($commande->getElementsByTagName('modeLivraison')->item(0)->nodeValue);
			$dateLivraison 				= $commande->getElementsByTagName('dateLivraison')->item(0)->nodeValue;
			$destinataireLivraison 		= html_entities($commande->getElementsByTagName('destinataireLivraisonPrenom')->item(0)->nodeValue);
			$destinataireLivraison 		.= ' ' . html_entities($commande->getElementsByTagName('destinataireLivraisonNom')->item(0)->nodeValue);
			$codePaysLivraison 			= html_entities($commande->getElementsByTagName('codePaysLivraison')->item(0)->nodeValue);
			$codePostalLivraison 		= html_entities($commande->getElementsByTagName('codePostalLivraison')->item(0)->nodeValue);
			$codeInseeLivraison 		= html_entities($commande->getElementsByTagName('codeInseeLivraison')->item(0)->nodeValue);
			$regionLivraison 			= html_entities($commande->getElementsByTagName('regionLivraison')->item(0)->nodeValue);
			$municipaliteLivraison 		= html_entities($commande->getElementsByTagName('municipaliteLivraison')->item(0)->nodeValue);
			$ligneAdresseLivraison 		= html_entities($commande->getElementsByTagName('ligneAdresseLivraison')->item(0)->nodeValue);
			$nomRueLivraison 			= html_entities($commande->getElementsByTagName('nomRueLivraison')->item(0)->nodeValue);
			$numeroBatimentLivraison 	= html_entities($commande->getElementsByTagName('numeroBatimentLivraison')->item(0)->nodeValue);
			$uniteLivraison 			= html_entities($commande->getElementsByTagName('uniteLivraison')->item(0)->nodeValue);
			$boitePostaleLivraison 		= html_entities($commande->getElementsByTagName('boitePostaleLivraison')->item(0)->nodeValue);
			
			/* On récupère les différentes informations concernant le client */
			$idClient 					= $commande->getElementsByTagName('idClient')->item(0)->nodeValue;
			$nomClient 					= html_entities($commande->getElementsByTagName('nomClient')->item(0)->nodeValue);
			$prenomClient 				= html_entities($commande->getElementsByTagName('prenomClient')->item(0)->nodeValue);
			$civiliteClient 			= html_entities($commande->getElementsByTagName('civiliteClient')->item(0)->nodeValue);
			$nomEntreprise 				= html_entities($commande->getElementsByTagName('nomEntreprise')->item(0)->nodeValue);
			$telephoneClient 			= html_entities($commande->getElementsByTagName('telephoneClient')->item(0)->nodeValue);
			$codePaysFacturation 		= html_entities($commande->getElementsByTagName('codePaysFacturation')->item(0)->nodeValue);
			$codePostalFacturation 		= html_entities($commande->getElementsByTagName('codePostalFacturation')->item(0)->nodeValue);
			$codeInseeFacturation 		= html_entities($commande->getElementsByTagName('codeInseeFacturation')->item(0)->nodeValue);
			$regionFacturation 			= html_entities($commande->getElementsByTagName('regionFacturation')->item(0)->nodeValue);
			$municipaliteFacturation 	= html_entities($commande->getElementsByTagName('municipaliteFacturation')->item(0)->nodeValue);
			$ligneAdresseFacturation 	= html_entities($commande->getElementsByTagName('ligneAdresseFacturation')->item(0)->nodeValue);
			$nomRueFacturation 			= html_entities($commande->getElementsByTagName('nomRueFacturation')->item(0)->nodeValue);
			$numeroBatimentFacturation 	= html_entities($commande->getElementsByTagName('numeroBatimentFacturation')->item(0)->nodeValue);
			$uniteFacturation 			= html_entities($commande->getElementsByTagName('uniteFacturation')->item(0)->nodeValue);
			$boitePostaleFacturation 	= html_entities($commande->getElementsByTagName('boitePostaleFacturation')->item(0)->nodeValue);
			$destinataireFacturation 	= html_entities($commande->getElementsByTagName('destinataireFacturation')->item(0)->nodeValue);
			$carteFidelite			 	= html_entities($commande->getElementsByTagName('carteFidelite')->item(0)->nodeValue);
			$commentaireClient 			= html_entities($commande->getElementsByTagName('commentaireClient')->item(0)->nodeValue); 
			
			/* Insérer les informations sur le client dans la base de données */
			if(($itemClient = Client::load($pdo, $idClient)) == null) { 
				/* Si le client n'existe pas dans la base de données, on l'ajoute */
				$itemClient = Client::create($pdo,$idClient,$nomClient,$prenomClient,$civiliteClient,$nomEntreprise,$telephoneClient,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation);
				$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des commandes") . " -- " . gettext("Enregistrement du client : ") . $prenomClient . ' ' . $nomClient . ' ' . gettext("dans la base de donn&eacute;es") . "\n";
			}
			else { 
				/* Si le client existe dans la base de données, on met à jour ses informations */
				$itemClient->setIdClient($idClient, false);
				$itemClient->setNom($nomClient, false);
				$itemClient->setPrenom($prenomClient, false);
				$itemClient->setCivilite($civiliteClient, false);
				$itemClient->setNomEntreprise($nomEntreprise, false);
				$itemClient->setTelephone($telephoneClient, false);
				$itemClient->setCodePaysFacturation($codePaysFacturation, false);
				$itemClient->setCodePostalFacturation($codePostalFacturation, false);
				$itemClient->setCodeInseeFacturation($codeInseeFacturation, false);
				$itemClient->setRegionFacturation($regionFacturation, false);
				$itemClient->setMunicipaliteFacturation($municipaliteFacturation, false);
				$itemClient->setLigneAdresseFacturation($ligneAdresseFacturation, false);
				$itemClient->setNomRueFacturation($nomRueFacturation, false);
				$itemClient->setNumeroBatimentFacturation($numeroBatimentFacturation, false);
				$itemClient->setUniteFacturation($uniteFacturation, false);
				$itemClient->setBoitePostaleFacturation($boitePostaleFacturation, false);
				$itemClient->setDestinataireFacturation($destinataireFacturation, false);
				$itemClient->update();
			}
			
			/* Insérer les informations sur la commande dans la base de données */
			$itemCommande = Commande::create($pdo,$idClient,$codeCommande,$dateCommande,$dateLivraison,$etatCommande,$codePaysLivraison,$codePostalLivraison,$codeInseeLivraison,$regionLivraison,
											$municipaliteLivraison,$ligneAdresseLivraison,$nomRueLivraison,$numeroBatimentLivraison,$uniteLivraison,$boitePostaleLivraison,$destinataireLivraison,
											$carteFidelite,$commentaireClient,$modeLivraison,0);
			$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des commandes") . " -- " . gettext("Enregistrement de la commande : ") . $prenomClient . ' ' . $nomClient . ':' . $codeCommande . ' ' . gettext("dans la base de donn&eacute;es") . "\n";
			
			$idCommande 	= $pdo->lastInsertId();
			$ligneCommande 	= $commande->getElementsByTagName('ligneCommande');
			
			foreach($ligneCommande as $ligne) {
				/* Pour chaque commande on récupère les lignes de commande */
				$codeProduit = $ligne->getElementsByTagName('idProduit')->item(0)->nodeValue;
				/* On vérifie si le produit est présent dans la base de données */
				if (($produit = Produit::loadByCodeProduit($pdo, $codeProduit)) == null){
					/* Non trouvé, généré un log d'erreur */
					$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des commandes") . " -- " . gettext("Le produit") . ' id : ' . $codeProduit . ' ' . gettext("n'est pas pr&eacute;sent dans la base de donn&eacute;es") . "\n";
				}
				else {
					/* Produit présent dans la base de données => on prend en compte la ligne de commandes */
					$quantiteCommandee 	= $ligne->getElementsByTagName('quantiteCommandee')->item(0)->nodeValue;
					$estDansUnLot 		= $ligne->getElementsByTagName('estDansUnLot')->item(0)->nodeValue;
					$idLot 				= $ligne->getElementsByTagName('idLot')->item(0)->nodeValue;
					$libelleLot 		= html_entities($ligne->getElementsByTagName('libelleLot')->item(0)->nodeValue);
					$codeEanLot 		= $ligne->getElementsByTagName('codeEanLot')->item(0)->nodeValue;
					$prixUnitaireTTC 	= $ligne->getElementsByTagName('prixUnitaireTTC')->item(0)->nodeValue;
					
					/* Insérer les lignes de commandes */ 
					$itemLignes = Ligne_Commande::create($pdo,$produit->getIdProduit(),$idCommande,null,$quantiteCommandee,$estDansUnLot,$idLot,$libelleLot,$codeEanLot,$prixUnitaireTTC);	// idPreparation = null, commande non encore affectée
				}
			}
			
			foreach(Zone::loadAll($pdo) as $zone){
				$temps = getTemps($itemCommande->selectLigne_commandesByZone($zone));
				Temps_Preparation::create($pdo,$itemCommande,$zone,$temps);
				$TempsTotal = $TempsTotal+$temps;
			}
			
			
			
			 header("Location: http://dev.e-pick.com/E-Pick/www/demo/chemin/commande-". $idCommande);

			
			
		}
	 
		/************************************************************************************************************************************
		* 																																	*
		*	Zipper le fichier commandes.xml & backup.sql et les sauvegarder dans /commandes/save et les supprimer de l'emplacement actuel	* 
		*																																	*
		************************************************************************************************************************************/
		
		/* liste des fichiers à compresser */
		$files = array ($backup);				
		foreach($listeCommandes as $fichier){
			array_push($files, $fichier);
		}
		
		// $zip   = zipper($files, __DIR__ . "/save", zipName);// Création d'un zip avec les fichiers passés en paramètres (cf. /application/kernel/Common.php)
		$zip   = zipper($files, applicationDir."flux/in/commandes/save", zipName);// Création d'un zip avec les fichiers passés en paramètres (cf. /application/kernel/Common.php)
		$log  .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des commandes") . " -- " . gettext("Compression des fichiers : ") . implode(', ', $files) . ' ' . gettext("dans le fichier ") . $zip . "\n";
		
		/* On supprime les fichiers du répertoire courant */
		foreach($files as $file) {
			if (file_exists($file)) unlink($file);
		}
		$log .= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext("Par : Script r&eacute;cup&eacute;ration des commandes") . " -- " . gettext("Suppression des fichiers : ") . implode(', ', $files) . ' ' . gettext("depuis l'emplacement ") . __DIR__ . "\n";
		
		/****************************
		 * 							*
		 * 		Ecrire le log 		*
		 *							*
		 ****************************/

		$logFile = applicationDir.'flux/in/commandes/logs/COM_' . date('d-m-Y') . '.log';
		if(!writeLog($log, $logFile)) die("Erreur écriture de log");		// Ecrire le log dans le fichier passé en paramètre (cf. /application/kernel/Common.php)
	}
?>