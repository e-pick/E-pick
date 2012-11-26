<?php 

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Script de récupération des fichiers des commandes préparées déposés dans le dossier /www/PDA/commandes/in.
 *
 *
 * /!\ ATTENTION /!\ : Ce script ne fonctionnera pas s'il est déplacé de cet emplacement.
 *
 */
 
ini_set('display_errors', 1); // activation des erreurs
error_reporting(E_ALL); // affichage de toutes les erreurs

 
 
 
	// require_once("../../../../application/config/bootstrap.php");
	// require_once("../../../../application/kernel/common.php");
	
	chdir('../../../');
	
	require_once("../application/kernel/PDO.php");
	require_once("../application/kernel/Common.php");
	require_once("../application/models/Ligne_Commande.php");
	require_once("../application/models/Commande.php");
	require_once("../application/models/Client.php");
	require_once("../application/models/Prelevement_realise.php");
	require_once("../application/models/Preparation.php");
	require_once("../application/models/Produit.php");
	require_once("../application/models/Etage.php");
	require_once("../application/models/Etagere.php");
	require_once("../application/models/Segment.php");
	require_once("../application/models/Rayon.php");
	
	$IMPORT_DIR = "./PDA/commandes/in/";
	$EXPORT_DIR = "../flux/out/commandes/";
	
	/* Récupération des fichiers de retours */
	$listeFichiers 	= array();			// Tableau contenant les fichiers commandes des retour du répertoire /PDA/commandes/in/.
	$dossier 		= opendir($IMPORT_DIR);	// Chemin du répertoire contenant les fichiers
	while ($fichier = readdir($dossier)) {
		if (substr($fichier, -4) == ".txt") {
			array_push($listeFichiers, $IMPORT_DIR . $fichier);
		}
	} 
	closedir($dossier);
	
	$arrayCommandes = array();
	$pdo = DB :: getInstance();
	
 
	
	/* Liste de points à relier */
	$matrice 			= getMatricePath();
	$matrice_distance 	= $matrice[0];
	$matrice_route 		= $matrice[1];
	
	unset($matrice);		// pas besoin
	unset($matrice_route);	// non plus
	
	foreach($listeFichiers as $fichier){
		// echo "<br /><br/>***** " . $fichier . '<br/>';
		$f 				= fopen($fichier, "r");
		$numLigne 		= 0;
		$ancien 		= array(null,'ptd_0'); 
		$preparation	= null;
		
		while (!feof($f) && fgets($f, 4096) != "******\r\n") {
			// On avance jusqu'aux lignes de produits
		}

		
		while (!feof($f)) { //on parcourt toutes les lignes
			$ligne = fgets($f, 4096); // lecture du contenu de la ligne

			if (substr($ligne,-5) == "***\r\n"){
				// echo 'Ligne commande : ' . $ligne . '<br />';
				$contenu = explode("\t", $ligne);
				$idLigne 			= $contenu[0];
				$codeProduit 		= $contenu[1];
				$libelleProduit 	= $contenu[2];
				$quantiteLue 		= $contenu[3];
				$prix 				= $contenu[4];
				$codeGeoloc	 		= $contenu[5];
				$eanLu 				= $contenu[6];
				$temps 				= $contenu[7];
				$listeEanVoisins 	= $contenu[8];
				$poids 				= $contenu[9];
				
				// echo 'idLigne : ' . $idLigne . '<br />';
				// echo 'codeProduit : ' . $codeProduit . '<br />';
				// echo '$libelleProduit : ' . $libelleProduit . '<br />';
				// echo 'quantiteLue : ' . $quantiteLue . '<br />';
				// echo 'prix : ' . $prix . '<br />';
				// echo 'codeGeoloc : ' . $codeGeoloc . '<br />';
				// echo 'eanLu : ' . $eanLu . '<br />';
				// echo 'temps : ' . $temps . '<br />';
				// echo 'listeEanVoisins : ' . $listeEanVoisins . '<br />';
				// echo 'poids : ' . $poids . '<br /><br />';
				
				/* Récupérer la ligne de commande */
				
				
				$ligneCommande 	= Ligne_Commande::load($pdo, $idLigne);
				print_r($ligneCommande);
				
				if ($ligneCommande != null){
					/* Mise à jour temps moyen d'accès pour le produit */
					$produit = Produit::loadByCodeProduit($pdo, $codeProduit);
												
					$etageres 	= $produit->selectEtageres();
					if ($etageres != null){
						$segment 	= $etageres[0]->getSegment();
						$idsegment 	= $segment->getIdsegment();
						$idrayon	= $segment->getRayon()->getIdrayon();
						$key		= $idrayon . '_' . $idsegment;
					
					
							
						
						if ($ancien[1] != 'nonGeoloc')
						
							$distance = $matrice_distance[$key][$ancien[1]];
							
						else
							$distance = 0;
													
					}
					else{
						$key = 'nonGeoloc';
						$distance = 0;
					}
					
					if ($preparation == null) $preparation = $ligneCommande->getPreparation();
					
					print_r ($ligneCommande);
					
					print "=>".$ancien[1];
					/* Mise à jour table prélemevent réalisé */
					
//					$prelevement = Prelevement_realise::create($pdo, $ligneCommande, '', $temps, $distance, $quantiteLue, $eanLu, $prix, $ligneCommande, $ancien[0],$preparation->getModePreparation());
					$prelevement = Prelevement_realise::create($pdo, $ligneCommande, $ancien[0], $temps, $distance, $quantiteLue, $eanLu, $prix, $preparation->getModePreparation());
					print_r ($ancien);
					$newTime = $produit->moyenneTempsAcces();
					$produit->setTempsMoyenAccess($newTime);
					
					
					/* Prélèvement précédent */
					$ancien = array($prelevement,$key);
					
					$arrayCommandes[] = $ligneCommande->getCommande();

					
				}
			}
			else{
			
				if ($ligne != ''){
					// echo 'Derniere ligne : ' . $ligne . '<br />';
					$contenu = explode("\t", $ligne);
					$nbArticles = $contenu[0];
					$nbBacs 	= $contenu[1];
					$tempsPause	= $contenu[2];
					$tempsTotal	= $contenu[3];
				}
			}
		}
		fclose($f);
		
		/* Mettre à jour l'état de la préparation à terminée */
		if ($preparation != null) $preparation->setEtat(2);

	}
	
	
	
	
	$arrayCommandes = array_unique($arrayCommandes);
	
	/* Générer les fichier Xml de retour */
	retourCommandeXml($pdo, $arrayCommandes, $EXPORT_DIR);
		
	/* Sauvegarder les fichiers */
	zipper($listeFichiers, './PDA/commandes/in/save/', 'PDA_commandes_' .date('d-m-Y_H-i-s') . '.in.bak.zip');
	
	/* Suppression des fichiers */
	foreach($listeFichiers as $fichier){
		unlink($fichier);
	}
	
	

?>