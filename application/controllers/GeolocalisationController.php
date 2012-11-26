<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * GeolocalisationController.php
 *
 * Cette classe permet de gérer la géolocalisation du magasin.
 *
 * Actions :
 *
 *		Index 		: L'action par défaut du controller.
 *		Importer 	: Lis les fichiers xml dans le répertoire /PDA/geolocalisation/in/ et met à jour la géolocalisation éxistante.
 *		Expoter  	: Expoter la géolocalisation éxistante dans un fichier xml déposé dans le répertoire /PDA/geolocalisation/out/.
 *		Vider 		: Vide toute la géolocalisation éxistante. Toutes les données sur les rayons, segments et étagère seront perdues.
 *		Editer		: Edite la géolocalisation d'un produit.
 *		Creer		: Crée une nouvelle géolocalisation pour un produit.
 *		Supprimer	: Supprimer une géolocalisation d'un produit.
 *
 */

class GeolocalisationController extends BaseController {
	
	public static function index(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		}	
	}	
	
	public static function importer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			$nombreProduitsGeolocalises = 0;
			$nombreProduitsInconnus 	= 0;
			$arrayProduitsInconnus 		= array();
			$nbEtages 					= array();
			$nbRayons 					= array();
			$nbSegments 				= array();
			$nbEtageres 				= array();
			$cpt						= 0;
			
			$IMPORT_DIR = "./PDA/geolocalisation/in/";
			$zipName 	= 'geoloc_' . date('d-m-Y_H.i.s') . '.in.bak.zip';		// Le nom du fichier zip 
			
			$error_message = '';
			
			/* Récupérer les fichiers geo.in.xml */
			$listeGeoloc 	= array();			// Tableau contenant les fichiers .xml du répertoire /PDA/geolocalisation/in/.
			$dossier 		= opendir($IMPORT_DIR);	// Chemin du répertoire contenant les fichiers .xml
			while ($fichier = readdir($dossier)) {
				if (substr($fichier, -7) == ".in.xml") {
					array_push($listeGeoloc, $IMPORT_DIR . $fichier);
				}
			} 
			closedir($dossier);
			
			/* Parse du fichier .xml */
			if (!array_empty($listeGeoloc)) {
				
				$tables 	= array('PRODUIT','EAN','RAYON','SEGMENT','ETAGERE','EST_GEOLOCALISE_DANS');		// Le nom des tables à sauvegarder
				$fileName 	= 'geoloc_' . date('d-m-Y_H.i.s') . '.sql'; 
				$backup 	= dumpDB($tables, './PDA/geolocalisation/in/' , $fileName);	// Sauvegarde des tables de la base de données (cf. /application/kernel/Common.php)
				
				/* Pour chaque fichier de géolocalisation ... */
				foreach ($listeGeoloc as $fichier) {
					$XmlFile = new DomDocument();
					$XmlFile->load($fichier);	// On récupère le document geolocalisation.xml
					
					/* On récupère l'étage */
					$etages = $XmlFile->getElementsByTagName('etage');
					if ($etages != null){
						foreach($etages as $etage){
							$idEtage 		= $etage->getAttribute('id');
							// if (!in_array($idEtage, $nbEtages))	
								// $nbEtages[] = $idEtage; 
							$libelleEtage 	= html_entities($etage->getElementsByTagName('libelleEtage')->item(0)->nodeValue);
							
							/* Tous les étages du magasin sont connus avant la phase de géolocalisation */
							if (($etageEnCours = Etage::load(parent::$_pdo, $idEtage)) == null){
								/* Problème, l'idEtage renseigné n'est pas présent dans la base de données */
								//throw new Exception("Erreur dans le fichier de géolocalisation : L'étage " . $idEtage . " n'existe pas");
								$error_message 	.= "Erreur dans le fichier de géolocalisation : L'étage " . $idEtage . " n'existe pas"."<br/>";
							}
							else{
								$rayons = $etage->getElementsByTagName('rayon');
								if ($rayons != null){
									foreach($rayons as $rayon){
										/* Pour chaque rayon de l'étage */
										$idRayon 		= $rayon->getAttribute('id');
										$actionRayon	= substr($rayon->getAttribute('id'),0,6);
										$typeRayon 		= $rayon->getAttribute('type');
										$libelleRayon 	= html_entities($rayon->getElementsByTagName('libelleRayon')->item(0)->nodeValue);
										$segments 		= $rayon->getElementsByTagName('segment');
										$nbSegmentss	= count($segments);
										$hauteurRayon	= ECHELLE_1M;
										$largeurRayon	= ECHELLE_1M;
										
										if ($actionRayon == "create"){
											/* Si l'id vaut 'create' c'est qu'il s'agit d'un nouveau rayon et qu'il va falloir l'ajouter dans la base de donnée */
											if ($typeRayon == 'classique'){
												$hauteurRayon 	= HAUTEURSEGMENT;
												$largeurRayon	= (LARGEURSEGMENT * $nbSegmentss);
											}
											$rayonEnCours = Rayon::create(parent::$_pdo,Zone::loadZoneMagasinByEtage(parent::$_pdo, $idEtage),$libelleRayon, -1, -1, 0,$hauteurRayon,$largeurRayon,$typeRayon);
										}
										else if ($actionRayon == "delete"){
											$rayonEnCours = Rayon::load(parent::$_pdo, substr($idRayon,7));
											if($rayonEnCours == null){
												/* Problème, l'idRayon renseigné n'est pas présent dans la base de données */
												//throw new Exception("Erreur dans le fichier de géolocalisation : Le rayon " . $idRayon . " n'existe pas");
												$error_message 	.= "Erreur dans le fichier de géolocalisation : Le rayon " . $idRayon . " n'existe pas"."<br/>";	
											}
										}
										else if(($rayonEnCours = Rayon::load(parent::$_pdo, $idRayon)) == null){
											/* Problème, le idRayon renseigné n'est pas présent dans la base de données */
											//throw new Exception("Erreur dans le fichier de géolocalisation : Le rayon " . $idRayon . " n'existe pas");
											$error_message 	.= "Erreur dans le fichier de géolocalisation : Le rayon " . $idRayon . " n'existe pas"."<br/>";	
										}
										else {
										// if (!in_array($rayonEnCours->getIdrayon(), $nbRayons))
											// $nbRayons[] = $rayonEnCours->getIdrayon();
										
											$numSegment = 0;
											if ($segments != null){
												foreach($segments as $segment){
													/* Pour chaque segment du rayon */
													$numSegment++;
													$idSegment 		= $segment->getAttribute('id');
													$actionSegment	= substr($segment->getAttribute('id'),0,6);
													
													if ($actionSegment == "create"){
														/* Si l'id vaut 'create' c'est qu'il s'agit d'un nouveau segment et qu'il va falloir l'ajouter dans la base de donnée */
														$segmentEnCours = Segment::create(parent::$_pdo, $rayonEnCours);
													}
													else if ($actionSegment == "delete"){
														$segmentEnCours = Segment::load(parent::$_pdo, substr($idSegment,7));
														if($etagereEnCours == null){
															/* Problème, l'idRayon renseigné n'est pas présent dans la base de données */
														//	throw new Exception("Erreur dans le fichier de géolocalisation : Le segment " . $idSegment . " n'existe pas");	
														$error_message 	.= "Erreur dans le fichier de géolocalisation : Le segment " . $idSegment . " n'existe pas"."<br/>";	
														}
													}
													else if (($segmentEnCours = Segment::load(parent::$_pdo, $idSegment)) == null){
														/* Problème, le idSegment renseigné n'est pas présent dans la base de données */
													//	throw new Exception("Erreur dans le fichier de géolocalisation : Le segment " . $idSegment . " n'existe pas");
													$error_message 	.= "Erreur dans le fichier de géolocalisation : Le segment " . $idSegment . " n'existe pas"."<br/>";	
													}
													
													// if (!in_array($segmentEnCours->getIdsegment(), $nbSegments))
														// $nbSegments[] = $segmentEnCours->getIdsegment();
													else {
														$etageres 	= $segment->getElementsByTagName('etagere');
														$numEtagere = 0;
														if ($etageres != null){
															foreach($etageres as $etagere){
																/* Pour chaque étagère du segment */
																$numEtagere++;
																$idEtagere 		= $etagere->getAttribute('id');
																$actionEtagere	= substr($etagere->getAttribute('id'),0,6);
																
																if ($actionEtagere == "create"){
																	/* Si l'id vaut 'create' c'est qu'il s'agit d'une nouvelle étagère et qu'il va falloir l'ajouter dans la base de donnée */
																	$etagereEnCours = Etagere::create(parent::$_pdo, $segmentEnCours);
																}
																else if ($actionEtagere == "delete"){
																	$etagereEnCours = Etagere::load(parent::$_pdo, substr($idEtagere,7));
																//	if($etagereEnCours == null){
																//		/* Problème, l'idEtagere renseigné n'est pas présent dans la base de données */
																//		throw new Exception("Erreur dans le fichier de géolocalisation  : L'étagère " . $idEtagere . " n'existe pas");	
																$error_message 	.= "Erreur dans le fichier de géolocalisation : L'étagère " . $idEtagere . " n'existe pas"."<br/>";	
																//	}
																}
																else if (($etagereEnCours = Etagere::load(parent::$_pdo, $idEtagere)) == null ){
																	/* Problème, l'idEtagere renseigné n'est pas présent dans la base de données */
																	//throw new Exception("Erreur dans le fichier de géolocalisation : L'étagère " . $idEtagere . " n'existe pas");
																	$error_message 	.= "Erreur dans le fichier de géolocalisation : L'étagère " . $idEtagere . " n'existe pas"."<br/>";	
																}
																
																// if (!in_array($etagereEnCours->getIdetagere(), $nbEtageres))
																	// $nbEtageres[] = $etagereEnCours->getIdetagere();
																else {
																	$eans = $etagere->getElementsByTagName('ean');
																	 
																	if ($eans != null){
																	 
																		foreach($eans as $ean){
																			/* Pour chaque EAN scanné dans l'étagère */
																			$actionEan 	= substr($ean->nodeValue,0,6);
																			$codeEan	= substr($ean->nodeValue,7);
																			$produit 	= Produit::selectByEan(parent::$_pdo, $codeEan);	// Récupérer le produit correspondant au code EAN
																			
																			if ($actionEan != "delete"){
																				if ($produit == null){
																				
																					/* Produit non trouvé dans la base de données */
																					$nombreProduitsInconnus++;
																					$ProduitInconnu['ean'] 		= $ean->nodeValue;
																					$ProduitInconnu['etage'] 	= $libelleEtage;
																					$ProduitInconnu['rayon'] 	= $libelleRayon;
																					$ProduitInconnu['segment'] 	= chr($numSegment + ord('A') - 1);
																					$ProduitInconnu['etagere'] 	= $numEtagere;
																					array_push($arrayProduitsInconnus, $ProduitInconnu);
																					
																					/* Insérer le produit dans la base */
																					$codeProduit 	= 'unknown' . time() . $cpt;
																					$libelleProduit = 'Produit inconnu';
																					$photoProduit 	= 'default.png';
																					$priorite		= NULL;
																					$stock			= NULL;
																					$tempsAcces		= TEMPS_MOYEN_ACCES_PRODUIT;
																					
																					$produit	  	= Produit::create(parent::$_pdo, $codeProduit,$libelleProduit, $photoProduit, '','','','','','','',0,$priorite,$stock,$tempsAcces);
																					$itemEan 		= Ean::create(parent::$_pdo,$produit->getIdProduit(),$codeEan);
																					
																					$cpt++;
																					
																				}
																				
																				if(!Geolocalisation::exists(parent::$_pdo,$produit->getIdProduit(),$etagereEnCours->getIdetagere())){
																					/* Si la géolocalisation n'existe pas dans la base de données => Ajouter le produit dans l'étagère*/
																					$etagereEnCours->addProduit(Produit::load(parent::$_pdo, $produit->getIdProduit()));
																					$nombreProduitsGeolocalises++;
																					
																					/* Nombre d'étages, rayons, segments et étagères pour le rapport */
																					if (!in_array($idEtage, $nbEtages))	
																						$nbEtages[] = $idEtage; 
																						
																					if (!in_array($rayonEnCours->getIdrayon(), $nbRayons))
																						$nbRayons[] = $rayonEnCours->getIdrayon();
																					
																					if (!in_array($segmentEnCours->getIdsegment(), $nbSegments))
																						$nbSegments[] = $segmentEnCours->getIdsegment();
																					
																					if (!in_array($etagereEnCours->getIdetagere(), $nbEtageres))
																						$nbEtageres[] = $etagereEnCours->getIdetagere();
																				}
																			}
																			else if($actionEan == "delete"){
																				if ($produit != null && $etagereEnCours!=null){
																					if (Geolocalisation::exists(parent::$_pdo, $produit->getIdProduit(), $etagereEnCours->getIdetagere())){
																						/* Supprimer la géolocalisation */
																						$produit->delEtagere($etagereEnCours);
																					}
																				}
																			} else {
																				
																				throw new Exception("Erreur de reaffectation");
																			}
																		}
																	}
																	
																	/* Supprimer l'étagère si besoin */
																	if ($actionEtagere == "delete"){
																		
																		if($etagereEnCours != null){
																			$etagereEnCours->delete();
																		}
																		
																		
																		
																	}
																}
															}
															
															/* Supprimer le segment si besoin */
															if ($actionSegment == "delete"){
																	if($segmentEnCours != null){
																			$segmentEnCours->delete();
																		}
															
																
															}
															
														}
													}
													
													/* Supprimer le rayon si besoin */
													if ($actionRayon == "delete"){
														$rayonEnCours->delete();
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				
				/* Sauvegarder les fichiers de géolocalisation et le fichier de backup */
				$files 	= $listeGeoloc;
				$files[]= $backup;
				$zip   	= zipper($files, './PDA/geolocalisation/in/save/', $zipName);
				
				/* Supprimer le fichier de backup et les fichiers de géolocalisation */
				unlink($backup);
				foreach ($listeGeoloc as $fichier) {
					unlink($fichier);
				}
				$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Import de la g&eacute;olocalisation par ') .$_SESSION['user_login']."\r\n";
				$logFile = '../application/logs/'.date('m-Y').'-geoloc.log';
				writeLog($log, $logFile);
				Divers::setChangementLocalisation(parent::$_pdo,1);
			}
			else{
				/* Aucun fichier .xml disponible */
				parent::$_response->addVar('txt_fail' , gettext('Aucun fichier de g&eacute;olocalisation n\'a &eacute;t&eacute; trouv&eacute;') );
			}
			
			/* Rechercher les produits qui n'ont pas été géolocalisés */
			$produitsNonScannes 		= Produit::selectProductsNotGeolocalized(parent::$_pdo);
			$arrayProduitsNonScannes 	= array();
			foreach($produitsNonScannes as $produitNonScanne){
				$eans = $produitNonScanne->selectEans();
				if ($eans != null)
					$ean = $eans[0]->getEan();
				else
					$ean = gettext('non d&eacute;fini');
				$arrayProduitsNonScannes[] = array($produitNonScanne,$ean);
			}
			
			parent::$_response->addVar('txt_retour'					, gettext('Retour aux produits') );
			parent::$_response->addVar('txt_succes'					, gettext('La g&eacute;olocalisation a &eacute;t&eacute; import&eacute;e avec succ&egrave;s') );
			parent::$_response->addVar('txt_rapport'				, gettext('Rapport') );
			parent::$_response->addVar('txt_codeEan'				, gettext('Code EAN') );
			parent::$_response->addVar('txt_Etage'					, gettext('Etage') );
			parent::$_response->addVar('txt_Etages'					, gettext('Etages') );
			parent::$_response->addVar('txt_Rayon'					, gettext('Rayon') );
			parent::$_response->addVar('txt_Rayons'					, gettext('Rayons'));
			parent::$_response->addVar('txt_Segment'				, gettext('Segment'));
			parent::$_response->addVar('txt_Segments'				, gettext('Segments'));
			parent::$_response->addVar('txt_Etagere'				, gettext('Etag&egrave;re'));
			parent::$_response->addVar('txt_Etageres'				, gettext('Etag&egrave;res'));
			parent::$_response->addVar('txt_produit'				, gettext('Produit'));
			parent::$_response->addVar('txt_produits'				, gettext('Produits'));
			parent::$_response->addVar('txt_produitsScannes'		, gettext('Produits scann&eacute;s'));
			parent::$_response->addVar('txt_contenant'				, gettext('contenant'));
			parent::$_response->addVar('txt_comportant'				, gettext('comportant'));
			parent::$_response->addVar('txt_dans'					, gettext('dans'));
			parent::$_response->addVar('txt_et'						, gettext('et'));
			parent::$_response->addVar('txt_libelle'				, gettext('Libelle'));
			
			parent::$_response->addVar('nbPtsGeolocalises'			, $nombreProduitsGeolocalises);
			parent::$_response->addVar('nbEtages'					, count($nbEtages));
			parent::$_response->addVar('nbRayons'					, count($nbRayons));
			parent::$_response->addVar('nbSegments'					, count($nbSegments));
			parent::$_response->addVar('nbEtageres'					, count($nbEtageres));
			
			parent::$_response->addVar('txt_produitsInconnus'		, gettext('produits n\'existant pas dans la bases de donn&eacute;es ont &eacute;t&eacute; g&eacute;olocalis&eacute;s'));
			parent::$_response->addVar('txt_produitsNonScannes'		, gettext('produits pr&eacute;sents dans la bases de donn&eacute;es n\'ont pas &eacute;t&eacute; g&eacute;olocalis&eacute;s'));
			parent::$_response->addVar('txt_produitInconnu'			, gettext('produit n\'existant pas dans la bases de donn&eacute;es a &eacute;t&eacute; g&eacute;olocalis&eacute;'));
			parent::$_response->addVar('txt_produitNonScanne'		, gettext('produit pr&eacute;sent pas dans la bases de donn&eacute;es n\'a pas &eacute;t&eacute; g&eacute;olocalis&eacute;'));
			
			parent::$_response->addVar('nombreProduitsInconnus'			, $nombreProduitsInconnus);
			parent::$_response->addVar('nombreProduitsNonScannes'	, count($arrayProduitsNonScannes));
			parent::$_response->addVar('arrayProduitsInconnus'				, $arrayProduitsInconnus);
			parent::$_response->addVar('arrayProduitsNonScannes'		, $arrayProduitsNonScannes);
			parent::$_response->addVar('form_errors'		, $error_message);

		}
	}
	
	public static function exporter(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			parent::$_response->addVar('txt_titre'			, gettext('Zones à exporter'));			
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));	
			parent::$_response->addVar('txt_libelle_zone'	, gettext('Libell&eacute; de la zone'));			
			parent::$_response->addVar('txt_etage'			, gettext('Etage'));			
			parent::$_response->addVar('txt_zone'			, gettext('Zone'));			
			parent::$_response->addVar('txt_nb_rayons'		, gettext('Nombre de rayons'));			
			parent::$_response->addVar('txt_nb_produits'	, gettext('Nombre de produits'));
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));
			parent::$_response->addVar('txt_editer_zone'	, gettext('Editer la zone'));
			parent::$_response->addVar('txt_selectionner'	, gettext('Tous'));
			parent::$_response->addVar('txt_submitzones'	, gettext('Exporter'));

			$arrayEtages = Etage::loadAll(parent::$_pdo,true);
			parent::$_response->addVar('arrayEtages', $arrayEtages);
			
			$arrayZones 	= array();
			$etages			= array();
			
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			$etageFilter 	= parent::$_request->getVar('etageFilter');
			
			if(parent::$_request->getVar('submitFilter')) {	// Clic sur le bouton filter
				if($etageFilter != null){
					$etage = Etage::load(parent::$_pdo, $etageFilter);
					if ($etage != null){
						$etages[] = $etage;
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
				}
				else{
					$etages = Etage::loadAll(parent::$_pdo,true);
				}
				
				parent::$_response->addVar('form_libelleFilter'	, $libelleFilter);
				parent::$_response->addVar('form_etage'			, $etageFilter);
				
				if ($etages != null){ 
					foreach($etages as $etage){
						if ($libelleFilter != null){
							$zones 	= Zone::getZoneByLibelleAndEtage(parent::$_pdo, $libelleFilter, $etage->getIdetage());
						}
						else{
							$zones = Zone::loadAllEtage(parent::$_pdo,$etage->getIdetage(),true);
						}
						
						foreach($zones as $zone){
							$rayons 		= Rayon::loadAllZone(parent::$_pdo, $zone->getIdzone(),true);
							$arrayProduits 	= Produit::loadAll(parent::$_pdo, null,null,$zone->getIdzone());					
							$arrayZones[] 	= array($zone,$etage,count($rayons),count($arrayProduits)); 
						}
					}
				}
				
				parent::$_response->addVar('arrayZones'		, $arrayZones);
			}
			else{
				if (parent::$_request->getVar('submitZones')){
					$arrayZones = parent::$_request->getVar('zoneList');
					$currentEtageId = "";
					
					/* Défintion du répertoire d'export de la géolocalisation */
					$EXPORT_DIR = './PDA/geolocalisation/out/';
					
					/* Creation du document xml */
					$save = new DomDocument('1.0', 'UTF-8');
					$save -> formatOutput = true;	// Pour faire jolie 
					
					/*
					 * Hierarchie : 
					 *
					 * 		Etages
					 * 		   |
					 *		   |__Zones (non présent dans le fichier .xml)
					 *			    |
					 *				|__Rayons
					 *					  |
					 *					  |__Segments
					 *							  |
					 *							  |__Etageres
					 *									  |
					 *									  |__Eans
					 *
					 */
					 
					$magasin = $save->createElement('magasin');
					$save->appendChild($magasin);
					
					/* Création d'un noeud finesse */
					$finesse = $save->createElement('finesse');
					$magasin -> appendChild($finesse);
					$txt_finesse = $save -> createTextNode(FINESSE_UTILISEE);
					$finesse -> appendChild($txt_finesse);
				
					/* Noeud racine */
					$listeEtages = $save->createElement('listeEtages');
					$magasin->appendChild($listeEtages);
					foreach($arrayZones as $idArray =>$idZone){ 
						$zone = Zone::load(parent::$_pdo,$idZone,true);
						$etageId = $zone->getEtage()->getIdetage();
						$currentEtage = Etage::load(parent::$_pdo,$etageId,true);
						
						if ($etageId != $currentEtageId){
							$currentEtageId = $etageId;	
							/* Création d'un noeud pour chaque étage du magasin */
							$etage = $save->createElement('etage');
							$listeEtages -> appendChild($etage);
							
							/* Création d'un attribut id de l'étage */
							$idEtage = $save->createAttribute('id');
							$etage -> appendChild($idEtage);
							$txt_idEtage = $save -> createTextNode($currentEtage->getIdetage());
							$idEtage -> appendChild($txt_idEtage);
							
							/* Création d'un noeud libelle de l'étage */
							$libelleEtage = $save->createElement('libelleEtage');
							$etage -> appendChild($libelleEtage);
							$txt_libelleEtage = $save -> createTextNode(html_entity_decode($currentEtage->getLibelle(), ENT_QUOTES, 'UTF-8'));
							$libelleEtage -> appendChild($txt_libelleEtage);
							
							/* Création d'un noeud listeRayons */
							$listeRayons = $save->createElement('listeRayons');
							$etage->appendChild($listeRayons);
						}
						
						/* EnCoursment de tous les rayons de l'étage */
						$rayons = Rayon::loadAllZone(parent::$_pdo,$idZone);
						
						/* Parcours de tous les rayons */
						foreach($rayons as $itemRayon){
							
							/* Création d'un noeud pour chaque rayon de l'étage */
							$rayon = $save->createElement('rayon');
							$listeRayons->appendChild($rayon);
							
							/* Création d'un attribut id du rayon */
							$idRayon = $save->createAttribute('id');
							$rayon -> appendChild($idRayon);
							$txt_idRayon = $save -> createTextNode($itemRayon->getIdrayon());
							$idRayon -> appendChild($txt_idRayon);
							
							/* Création d'un attribut type du rayon */
							$typeRayon = $save->createAttribute('type');
							$rayon -> appendChild($typeRayon);
							$txt_typeRayon = $save -> createTextNode($itemRayon->getType());
							$typeRayon -> appendChild($txt_typeRayon);
							
							/* Création d'un noeud libelle du rayon */
							$libelleRayon = $save->createElement('libelleRayon');
							$rayon -> appendChild($libelleRayon);
							$txt_libelleRayon = $save -> createTextNode(html_entity_decode($itemRayon->getLibelle(), ENT_QUOTES, 'UTF-8'));
							$libelleRayon -> appendChild($txt_libelleRayon);
							
							/* Création d'un noeud listeSegments */
							$listeSegments = $save->createElement('listeSegments');
							$rayon->appendChild($listeSegments);
							
							/* EnCoursment de tous les segments du rayon */
							$segments = Segment::selectByRayon(parent::$_pdo, $itemRayon);
							
							/* Parcours de tous les segments du rayon */
							foreach($segments as $itemSegment){
								/* Création d'un noeud pour chaque segment du rayon */
								$segment = $save->createElement('segment');
								$listeSegments->appendChild($segment);
								
								/* Création d'un attribut id du segment */
								$idSegment = $save->createAttribute('id');
								$segment -> appendChild($idSegment);
								$txt_idSegment = $save -> createTextNode($itemSegment->getIdsegment());
								$idSegment -> appendChild($txt_idSegment);
								
								/* Création d'un noeud listeEtageres */
								$listeEtageres = $save->createElement('listeEtageres');
								$segment->appendChild($listeEtageres);
								
								/* EnCoursment de toutes les étagères du segment */
								$etageres = Etagere::selectBySegment(parent::$_pdo, $itemSegment);
								
								/* Parcours de toutes les étagères */
								foreach($etageres as $itemEtagere){
									/* Création d'un noeud pour chaque étagère du segment */
									$etagere = $save->createElement('etagere');
									$listeEtageres->appendChild($etagere);
									
									/* Création d'un attribut id de l'étagère */
									$idEtagere = $save->createAttribute('id');
									$etagere -> appendChild($idEtagere);
									$txt_idEtagere = $save -> createTextNode($itemEtagere->getIdetagere());
									$idEtagere -> appendChild($txt_idEtagere);
									
									/* Création d'un noeud listeEans */
									$listeEans = $save->createElement('listeEans');
									$etagere->appendChild($listeEans);
									
									/* EnCoursment de tous les produits géolocalisés dans l'étagère */
									$produits = $itemEtagere->selectProduits();
									
									/* Parcours de tous les produits */
									foreach($produits as $itemProduit){
										/* EnCoursment de tous les codes ean du produit */
										$eans = $itemProduit->selectEans();
										
										/* Parcours de tous les eans */
										foreach($eans as $itemEan){
											
											/* Création d'un noeud ean */
											$ean = $save->createElement('ean');
											$listeEans -> appendChild($ean);
											$txt_ean = $save -> createTextNode($itemEan->getEan());
											$ean -> appendChild($txt_ean);
										}
									}
								}
							}
						}		
					} 
					/* Suppression des anciens exports s'il en existe */
					$dossier = opendir($EXPORT_DIR);	// Chemin du répertoire contenant les fichiers .xml
					while ($fichier = readdir($dossier)) {
						if (substr($fichier, -8) == '.out.xml') {
							unlink($EXPORT_DIR . $fichier);
						}
					}
					closedir($dossier);
					
					/* Sauvegarde dans une fichier .xml */
					$fileName = $EXPORT_DIR . 'geo_magasin_'.date('d-m-Y-H-i-s').'.out.xml';
					$save -> save($fileName);
					
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export de la g&eacute;olocalisation par ') .$_SESSION['user_login']."\r\n";
					$logFile = '../application/logs/'.date('m-Y').'-geoloc.log';
					writeLog($log, $logFile);
					
					parent::$_response->addVar('txt_export'	, gettext('L\'export de la g&eacute;olocalisation a &eacute;t&eacute; r&eacute;alis&eacute; avec succ&egrave;s'));
					parent::$_response->addVar('txt_retour'	, gettext('Retour aux produits'));
					parent::$_response->addVar('txt_comm'	,gettext('Un fichier contenant la g&eacute;olocalisation actuelle du magasin a &eacute;t&eacute; mis &agrave; disposition du PDA. A pr&eacute;sent, vous pouvez g&eacute;olocaliser le magasin en utilisant le logiciel embarqu&eacute; de g&eacute;olocalisation. Une fois que vous avez termin&eacute; d\'utiliser le logiciel PDA, cliquez sur '));
					parent::$_response->addVar('txt_importer'	,gettext('importer la nouvelle g&eacute;olocalisation'));
					parent::$_response->addVar('done'		,'');
				}
				else{
					parent::$_response->addVar('form_libelleFilter'	, '');
					parent::$_response->addVar('form_etage'			, '');
					$etages = Etage::loadAll(parent::$_pdo, true);
					if ($etages != null){ 
						foreach($etages as $etage){
							if ($libelleFilter != null){
								$zones 	= Zone::getZoneByLibelleAndEtage(parent::$_pdo, $libelleFilter, $etage->getIdetage());
							}
							else{
								$zones = Zone::loadAllEtage(parent::$_pdo,$etage->getIdetage(),true);
							}
							
							foreach($zones as $zone){
								$rayons 		= Rayon::loadAllZone(parent::$_pdo, $zone->getIdzone(),true);
								$arrayProduits 	= Produit::loadAll(parent::$_pdo, null,null,$zone->getIdzone());					
								$arrayZones[] 	= array($zone,$etage,count($rayons),count($arrayProduits)); 
							}
						}
					}
					
					parent::$_response->addVar('arrayZones'		, $arrayZones);
				}
			}
			
		}	
	}	 
	
	/*public static function exporter(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
			// Défintion du répertoire d'export de la géolocalisation 
			$EXPORT_DIR = './PDA/geolocalisation/out/';
			
			// Creation du document xml 
			$save = new DomDocument('1.0', 'UTF-8');
			$save -> formatOutput = true;	// Pour faire jolie 
			 
			$magasin = $save->createElement('magasin');
			$save->appendChild($magasin);
			
			// Création d'un noeud finesse 
			$finesse = $save->createElement('finesse');
			$magasin -> appendChild($finesse);
			$txt_finesse = $save -> createTextNode(FINESSE_UTILISEE);
			$finesse -> appendChild($txt_finesse);
		
			// Noeud racine 
			$listeEtages = $save->createElement('listeEtages');
			$magasin->appendChild($listeEtages);
			
			// EnCoursment de tous les étages 
			$etages = Etage::loadAll(parent::$_pdo);
			
			// Parcour de tous les étages 
			foreach($etages as $itemEtage){
			
				// Création d'un noeud pour chaque étage du magasin 
				$etage = $save->createElement('etage');
				$listeEtages -> appendChild($etage);
				
				// Création d'un attribut id de l'étage 
				$idEtage = $save->createAttribute('id');
				$etage -> appendChild($idEtage);
				$txt_idEtage = $save -> createTextNode($itemEtage->getIdetage());
				$idEtage -> appendChild($txt_idEtage);
				
				// Création d'un noeud libelle de l'étage 
				$libelleEtage = $save->createElement('libelleEtage');
				$etage -> appendChild($libelleEtage);
				$txt_libelleEtage = $save -> createTextNode(html_entity_decode($itemEtage->getLibelle(), ENT_QUOTES, 'UTF-8'));
				$libelleEtage -> appendChild($txt_libelleEtage);
				
				// Création d'un noeud listeRayons 
				$listeRayons = $save->createElement('listeRayons');
				$etage->appendChild($listeRayons);
				
				// EnCoursment de toutes les zones de l'étage 
				$zones = Zone::loadAllEtage(parent::$_pdo,$itemEtage->getIdetage());
				
				// Parcours de toutes les zones 
				foreach($zones as $zone){
				
					// EnCoursment de tous les rayons de l'étage 
					$rayons = Rayon::loadAllZone(parent::$_pdo,$zone->getIdzone());
					
					// Parcours de tous les rayons 
					foreach($rayons as $itemRayon){
						
						// Création d'un noeud pour chaque rayon de l'étage 
						$rayon = $save->createElement('rayon');
						$listeRayons->appendChild($rayon);
						
						// Création d'un attribut id du rayon 
						$idRayon = $save->createAttribute('id');
						$rayon -> appendChild($idRayon);
						$txt_idRayon = $save -> createTextNode($itemRayon->getIdrayon());
						$idRayon -> appendChild($txt_idRayon);
						
						// Création d'un attribut type du rayon 
						$typeRayon = $save->createAttribute('type');
						$rayon -> appendChild($typeRayon);
						$txt_typeRayon = $save -> createTextNode($itemRayon->getType());
						$typeRayon -> appendChild($txt_typeRayon);
						
						// Création d'un noeud libelle du rayon 
						$libelleRayon = $save->createElement('libelleRayon');
						$rayon -> appendChild($libelleRayon);
						$txt_libelleRayon = $save -> createTextNode(html_entity_decode($itemRayon->getLibelle(), ENT_QUOTES, 'UTF-8'));
						$libelleRayon -> appendChild($txt_libelleRayon);
						
						// Création d'un noeud listeSegments 
						$listeSegments = $save->createElement('listeSegments');
						$rayon->appendChild($listeSegments);
						
						// EnCoursment de tous les segments du rayon 
						$segments = Segment::selectByRayon(parent::$_pdo, $itemRayon);
						
						// Parcours de tous les segments du rayon 
						foreach($segments as $itemSegment){
							// Création d'un noeud pour chaque segment du rayon 
							$segment = $save->createElement('segment');
							$listeSegments->appendChild($segment);
							
							// Création d'un attribut id du segment 
							$idSegment = $save->createAttribute('id');
							$segment -> appendChild($idSegment);
							$txt_idSegment = $save -> createTextNode($itemSegment->getIdsegment());
							$idSegment -> appendChild($txt_idSegment);
							
							// Création d'un noeud listeEtageres 
							$listeEtageres = $save->createElement('listeEtageres');
							$segment->appendChild($listeEtageres);
							
							// EnCoursment de toutes les étagères du segment 
							$etageres = Etagere::selectBySegment(parent::$_pdo, $itemSegment);
							
							// Parcours de toutes les étagères 
							foreach($etageres as $itemEtagere){
								// Création d'un noeud pour chaque étagère du segment 
								$etagere = $save->createElement('etagere');
								$listeEtageres->appendChild($etagere);
								
								// Création d'un attribut id de l'étagère 
								$idEtagere = $save->createAttribute('id');
								$etagere -> appendChild($idEtagere);
								$txt_idEtagere = $save -> createTextNode($itemEtagere->getIdetagere());
								$idEtagere -> appendChild($txt_idEtagere);
								
								// Création d'un noeud listeEans 
								$listeEans = $save->createElement('listeEans');
								$etagere->appendChild($listeEans);
								
								// EnCoursment de tous les produits géolocalisés dans l'étagère 
								$produits = $itemEtagere->selectProduits();
								
								// Parcours de tous les produits 
								foreach($produits as $itemProduit){
									// EnCoursment de tous les codes ean du produit 
									$eans = $itemProduit->selectEans();
									
									// Parcours de tous les eans 
									foreach($eans as $itemEan){
										
										// Création d'un noeud ean 
										$ean = $save->createElement('ean');
										$listeEans -> appendChild($ean);
										$txt_ean = $save -> createTextNode($itemEan->getEan());
										$ean -> appendChild($txt_ean);
									}
								}
							}
						}
					}
				}
			}
			
			// Suppression des anciens exports s'il en existe 
			$dossier = opendir($EXPORT_DIR);	// Chemin du répertoire contenant les fichiers .xml
			while ($fichier = readdir($dossier)) {
				if (substr($fichier, -8) == '.out.xml') {
					unlink($EXPORT_DIR . $fichier);
				}
			}
			closedir($dossier);
			
			// Sauvegarde dans une fichier .xml 
			$fileName = $EXPORT_DIR . 'geo_magasin_'.date('d-m-Y-H-i-s').'.out.xml';
			$save -> save($fileName);
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export de la g&eacute;olocalisation par ') .$_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-geoloc.log';
			writeLog($log, $logFile);
			
			parent::$_response->addVar('txt_export'	, gettext('L\'export de la g&eacute;olocalisation a &eacute;t&eacute; r&eacute;alis&eacute; avec succ&egrave;s'));
			parent::$_response->addVar('txt_retour'	, gettext('Retour aux produits'));
			parent::$_response->addVar('txt_comm'	,gettext('Un fichier contenant la g&eacute;olocalisation actuelle du magasin a &eacute;t&eacute; mis &agrave; disposition du PDA. A pr&eacute;sent, vous pouvez g&eacute;olocaliser le magasin en utilisant le logiciel embarqu&eacute; de g&eacute;olocalisation. Une fois que vous avez termin&eacute; d\'utiliser le logiciel PDA, cliquez sur '));
			parent::$_response->addVar('txt_importer'	,gettext('importer la nouvelle g&eacute;olocalisation'));
		}	
	}*/
	
	public static function vider(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			$tables 	= array('PRODUIT','EAN','RAYON','SEGMENT','ETAGERE','EST_GEOLOCALISE_DANS');			// Les tables à sauvegarder
			$fileName 	= 'geoloc_' . date('d-m-Y_H.i.s') . '.sql';
			
			/* Sauvegarde de la géolocalisation actuelle dans le fichier /application/backups/$fileName */
			dumpDB($tables,'../application/backups', $fileName);
			
			/* Vidage de la géolocalisation */
			Geolocalisation::emptying(parent::$_pdo);
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Vidage de la g&eacute;olocalisation par ') .$_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-geoloc.log';
			writeLog($log, $logFile);
			Divers::setChangementLocalisation(parent::$_pdo,1);
			parent::$_response->addVar('txt_vidage', gettext('La g&eacute;olocalisation a &eacute;t&eacute; bien supprim&eacute;e'));
			parent::$_response->addVar('txt_retour', gettext('Retour &agrave; l\'accueil'));
		}
	}
	
	public static function editer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_PREPARATEUR)){
		
			parent::$_response->addVar('txt_geolocProduit'		, gettext('G&eacute;olocalisation du produit'));	
			parent::$_response->addVar('txt_etage'				, gettext('Etage'));	
			parent::$_response->addVar('txt_zone'				, gettext('Zone'));	
			parent::$_response->addVar('txt_rayon'				, gettext('Rayon'));	
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));
			parent::$_response->addVar('txt_boutonEnregistrer'	, gettext('Enregistrer'));	
			parent::$_response->addVar('txt_geoloc'				, gettext('G&eacute;olocalisation'));	
			parent::$_response->addVar('txt_ajouterGeoloc'		, gettext('Ajouter une nouvelle g&eacute;olocalisation'));	
			parent::$_response->addVar('txt_supprimer'			, gettext('Supprimer le produit de cet emplacement'));	
			parent::$_response->addVar('txt_confirmSuppression'	, gettext('Etes vous sur de vouloir supprimer le produit de cet emplacement'));	
			
			
			/* On récupère l'id du produit */
			$idProduit= parent::$_request->getVar('id');
			if(!isset($idProduit))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
		
			$produit = Produit::load(parent::$_pdo, $idProduit,true);	
			if($produit == null)
				throw new Exception(gettext('Param&egrave;tre invalide : idProduit'),5);	
			
			/* On récupère la géolocalisation à afficher/traiter */
			$geoloc 			= parent::$_request->getVar('geoloc');
			$geolocSelectionnee = '';
			if(($geoloc != '')){
				/* La géolocalisation choisie dans le menu en onglets */
				$geolocSelectionnee = (int) $geoloc;
			}
			else{
				/* Si pas de géolocalisation précisée, on prend la première présente dans la table */
				$etageres = $produit->selectEtageres();
				if ($etageres != null)
					$geolocSelectionnee = $etageres[0]->getIdetagere();
			} 
			
			$etageFilter 	= parent::$_request->getVar('etageFilter');
			$zoneFilter 	= parent::$_request->getVar('zoneFilter');
			$rayonFilter 	= parent::$_request->getVar('rayonFilter');
			$idetagere 		= parent::$_request->getVar('idEtagere');
			$rayonChoisi	= false;
			
			$arrayEtages = array();
			$arrayZone	 = array();
			$arrayRayons = array();
				
			if(FINESSE_UTILISEE == FINESSE_SEGMENT)
				parent::$_response->addVar('txt_explication', gettext('Pour modifier la g&eacute;olocalisation, cliquez sur le segment correspondant au nouvel emplacement du produit.') );
			else if(FINESSE_UTILISEE == FINESSE_ETAGERE)
				parent::$_response->addVar('txt_explication', gettext('Pour modifier la g&eacute;olocalisation, cliquez sur l\'&eacute;tag&egrave;re correspondante au nouvel emplacement du produit.') );
			
			if(parent::$_request->getVar('submitGeoloc')){
				if($etageFilter != null){
					$etage = Etage::load(parent::$_pdo, $etageFilter);
					if ($etage != null){
						$arrayZones = Zone::loadAllEtage(parent::$_pdo, $etageFilter);
						if($zoneFilter != null){
							$zone = Zone::load(parent::$_pdo, $zoneFilter);
							if ($zone != null){
								$arrayRayons = Rayon::loadAllZone(parent::$_pdo, $zoneFilter);
								if($rayonFilter != null){
									$rayon = Rayon::load(parent::$_pdo, $rayonFilter);
									if ($rayon != null){
										$rayonChoisi = true;
										
										$listeSegments 	= $rayon->selectSegments();				// Liste des segments que contient ce rayon
										$arraySegments  = array();								// Tableau contenant les infos des segments
										foreach($listeSegments as $segment){
											/* Converstion du numéro de segment en lettre */
											$lettre = $segment->getPosition(parent::$_pdo, $segment->getIdsegment(), $rayon->getIdrayon());
											/* Liste des étagères que contient ce segment */
											$listeEtageres = $segment->selectEtageres();
											/* Tableau contenant dans chaque case : [0] -> objet Segment; [1] -> la lettre correspondante au segment; [2] -> tableau contenant les étagères que contient ce segment */
											$arraySegments[] = array($segment, $lettre, $listeEtageres);
										}
										
										parent::$_response->addVar('nouveauRayon'		, $rayon);
										parent::$_response->addVar('arraySegments'		, $arraySegments);
										parent::$_response->addVar('nbSegments'			, count($arraySegments));
										parent::$_response->addVar('etagereChoisie'		, $idetagere);
										
										if (FINESSE_UTILISEE == FINESSE_RAYON){
											/* Si on est en finesse rayon, on selectionne la seule étagère se trouvant dans ce rayon */
											$idetagere = $listeEtageres[0]->getIdetagere();
										}
									}
									else{
										throw new Exception(gettext('Param&egrave;tre invalide'),5);
									}
								}
								else{
									$rayonChoisi = false;
									parent::$_response->addVar('txt_errors'		, gettext('Veuillez choisir un rayon'));
								}
							}
							else{
								throw new Exception(gettext('Param&egrave;tre invalide'),5);
							}
						}
						else{
							$rayonChoisi = false;
							parent::$_response->addVar('txt_errors'		, gettext('Veuillez choisir une zone'));
						}
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
				}
				else{
					$rayonChoisi = false;
					parent::$_response->addVar('txt_errors'		, gettext('Veuillez choisir un &eacute;tage'));
				}
					
				parent::$_response->addVar('arrayZones'		, $arrayZones);
				parent::$_response->addVar('arrayRayons'	, $arrayRayons);
				parent::$_response->addVar('form_etage'		, $etageFilter);
				parent::$_response->addVar('form_zone'		, $zoneFilter);
				parent::$_response->addVar('form_rayon'		, $rayonFilter);
				
				if(parent::$_request->getVar('save') == '1'){
					/* Sauvegarder la nouvelle géolocalisation */
					if ($rayonChoisi && $idetagere != null){

						$oldGeoloc 	= Geolocalisation::loadByEtagereAndProduit(parent::$_pdo, $geolocSelectionnee, $produit->getIdProduit());
						$oldEtagere	= Etagere::load(parent::$_pdo, $geolocSelectionnee);
						$oldZone	= $oldEtagere->getSegment()->getRayon()->getZone();
						if ($oldGeoloc != null){
							/* Modifier la géolocalisation */
							$oldGeoloc->setIdetagere(Etagere::load(parent::$_pdo, $idetagere), false);
							$oldGeoloc->setIdproduit($produit, false);
							$oldGeoloc->update();
						}
						else{
							throw new Exception(gettext('Param&egrave;tre invalide : idGeolocalisation'),5);
						}
						$newEtagere	= Etagere::load(parent::$_pdo, $idetagere);
						$newZone	= $newEtagere->getSegment()->getRayon()->getZone();
						if($oldZone != $newZone)
							Divers::setChangementLocalisation(parent::$_pdo,1);
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la g&eacute;olocalisation du produit '). $idProduit.gettext(' par ') .$_SESSION['user_login']."\r\n";
						$logFile = '../application/logs/'.date('m-Y').'-geoloc.log';
						writeLog($log, $logFile);
						header('Location: ' . APPLICATION_PATH . 'produit/afficher/' . $produit->getIdProduit());	// Redirection vers la page du produit
					}
				}
			}
			else{
				/* Récupèrer la géolocalisation du produit */
				
				if(!Geolocalisation::exists(parent::$_pdo, $produit->getIdProduit(),  $geolocSelectionnee )){
					/* Si la géolocalisation n'existe pas */
					$rayonChoisi = false;
					parent::$_response->addVar('noGeoloc'			, gettext('Ce produit n\'est g&eacute;olocalis&eacute; dans cet emplacement'));
				}
				if($geolocSelectionnee != null) {
					$etagere 	= Etagere::load(parent::$_pdo, $geolocSelectionnee);
					if($etagere != null){
						$segment	= $etagere->getSegment();
						$rayon		= $segment->getRayon();
						$zone 		= $rayon->getZone();
						$etage		= $zone->getEtage();
					
						parent::$_response->addVar('arrayZones'			, $etage->selectZones());	
						parent::$_response->addVar('arrayRayons'		, $zone->selectRayons());
						
						parent::$_response->addVar('form_rayon'			, $rayon->getIdrayon());
						parent::$_response->addVar('form_zone'			, $zone->getIdzone());
						parent::$_response->addVar('form_etage'			, $etage->getIdetage());
						
						parent::$_response->addVar('segment'			, $segment);
						parent::$_response->addVar('etagereChoisie'		, $etagere->getIdetagere());
						
						$listeSegments 	= $rayon->selectSegments();				// Liste des segments que contient ce rayon
						$arraySegments  = array();								// Tableau contenant les infos des segments
						foreach($listeSegments as $segment){
							/* Converstion du numéro de segment en lettre */
							$lettre = $segment->getPosition(parent::$_pdo, $segment->getIdsegment(), $rayon->getIdrayon());
							/* Liste des étagères que contient ce segment */
							$listeEtageres = $segment->selectEtageres();
							/* Tableau contenant dans chaque case : [0] -> objet Segment; [1] -> la lettre correspondante au segment; [2] -> tableau contenant les étagères que contient ce segment */
							$arraySegments[] = array($segment, $lettre, $listeEtageres);
						}
						
						parent::$_response->addVar('nouveauRayon'		, $rayon);
						parent::$_response->addVar('arraySegments'		, $arraySegments);
						parent::$_response->addVar('nbSegments'			, count($arraySegments));
						$rayonChoisi = true;
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide : idEtagere'),5);
					}
				}
				else{
					/* Le produit n'a pas encore été géolocalisé */
					parent::$_response->addVar('noGeoloc'			, gettext('Ce produit n\'est pas encore g&eacute;olocalis&eacute;.'));
				}
			}
			
			parent::$_response->addVar('rayonChoisi' 		, $rayonChoisi);	
			parent::$_response->addVar('produit'			, $produit);
			parent::$_response->addVar('arrayEtages'		, Etage::loadAll(parent::$_pdo,true));
			parent::$_response->addVar('arrayGeolocs'		, $produit->selectEtageres());
			parent::$_response->addVar('geolocSelectionnee'	, $geolocSelectionnee);
			
			
		}
	}
	
	public static function creer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			parent::$_response->addVar('txt_newGeolocProduit'	, gettext('Nouvelle g&eacute;olocalisation pour le produit'));	
			parent::$_response->addVar('txt_etage'				, gettext('Etage'));	
			parent::$_response->addVar('txt_zone'				, gettext('Zone'));	
			parent::$_response->addVar('txt_rayon'				, gettext('Rayon'));	
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));
			parent::$_response->addVar('txt_boutonEnregistrer'	, gettext('Enregistrer'));	
			parent::$_response->addVar('txt_geoloc'				, gettext('G&eacute;olocalisation'));	
			
			/* On récupère l'id du produit */
			$idProduit= parent::$_request->getVar('id');
			if(!isset($idProduit))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
		
			$produit = Produit::load(parent::$_pdo, $idProduit,true);	
			if($produit == null)
				throw new Exception(gettext('Param&egrave;tre invalide : idProduit'),5);	
				
			parent::$_response->addVar('produit'			, $produit);
			parent::$_response->addVar('arrayEtages'		, Etage::loadAll(parent::$_pdo,true));
			
			$eans 	= $produit->selectEans();
			$AllRayons = Rayon::loadAll(parent::$_pdo);
			
			if ($eans == null){
				/* Le produit ne possède pas de code Ean */
				parent::$_response->addVar('noEan'	, gettext('Ce produit ne poss&egrave;de pas de code Ean, il est impossible de le g&eacute;olocaliser.'));
			}
			else if ($AllRayons == null){
				/* Il n'existe aucun rayon dans la base de données */
				parent::$_response->addVar('noRayons'	, gettext('Il n\'existe aucun rayons dans la base de donn&eacute;es, il est impossible de g&eacute;olocaliser ce produit.') . '<br />' . gettext('Veuillez revoir la g&eacute;olocalisation du magasin.'));
			}
			else{
			
				$etageFilter 	= parent::$_request->getVar('etageFilter');
				$zoneFilter 	= parent::$_request->getVar('zoneFilter');
				$rayonFilter 	= parent::$_request->getVar('rayonFilter');
				$idetagere 		= parent::$_request->getVar('idEtagere');
				$rayonChoisi	= false;
				
				if(FINESSE_UTILISEE == FINESSE_SEGMENT)
					parent::$_response->addVar('txt_explication', gettext('Pour modifier la g&eacute;olocalisation, cliquez sur le segment correspondant au nouvel emplacement du produit.') );
				else if(FINESSE_UTILISEE == FINESSE_ETAGERE)
					parent::$_response->addVar('txt_explication', gettext('Pour modifier la g&eacute;olocalisation, cliquez sur l\'&eacute;tag&egrave;re correspondante au nouvel emplacement du produit.') );
				
				$arrayEtages = array();
				$arrayZone	 = array();
				$arrayRayons = array();
				
				if(parent::$_request->getVar('submitGeoloc')){
					$arrayZones 	= array();
					$arrayRayons 	= array();
					if($etageFilter != null){
						$etage = Etage::load(parent::$_pdo, $etageFilter);
						if ($etage != null){
							$arrayZones = Zone::loadAllEtage(parent::$_pdo, $etageFilter);
							if($zoneFilter != null){
								$zone = Zone::load(parent::$_pdo, $zoneFilter);
								if ($zone != null){
									$arrayRayons = Rayon::loadAllZone(parent::$_pdo, $zoneFilter);
									if($rayonFilter != null){
										$rayon = Rayon::load(parent::$_pdo, $rayonFilter);
										if ($rayon != null){
											$rayonChoisi = true;
											
											$listeSegments 	= $rayon->selectSegments();				// Liste des segments que contient ce rayon
											$arraySegments  = array();								// Tableau contenant les infos des segments
											foreach($listeSegments as $segment){
												/* Converstion du numéro de segment en lettre */
												$lettre = $segment->getPosition(parent::$_pdo, $segment->getIdsegment(), $rayon->getIdrayon());
												/* Liste des étagères que contient ce segment */
												$listeEtageres = $segment->selectEtageres();
												/* Tableau contenant dans chaque case : [0] -> objet Segment; [1] -> la lettre correspondante au segment; [2] -> tableau contenant les étagères que contient ce segment */
												$arraySegments[] = array($segment, $lettre, $listeEtageres);
											}
											
											parent::$_response->addVar('nouveauRayon'		, $rayon);
											parent::$_response->addVar('arraySegments'		, $arraySegments);
											parent::$_response->addVar('nbSegments'			, count($arraySegments));
											parent::$_response->addVar('etagereChoisie'		, $idetagere);
											
											if (FINESSE_UTILISEE == FINESSE_RAYON){
												/* Si on est en finesse rayon, on selectionne la seule étagère se trouvant dans ce rayon */
												$idetagere = $listeEtageres[0]->getIdetagere();
											}
										}
										else{
											throw new Exception(gettext('Param&egrave;tre invalide'),5);
										}
									}
									else{
										$rayonChoisi = false;
										parent::$_response->addVar('txt_errors'		, gettext('Veuillez choisir un rayon'));
									}
								}
								else{
									throw new Exception(gettext('Param&egrave;tre invalide'),5);
								}
							}
							else{
								$rayonChoisi = false;
								parent::$_response->addVar('txt_errors'		, gettext('Veuillez choisir une zone'));
							}
						}
						else{
							throw new Exception(gettext('Param&egrave;tre invalide'),5);
						}
					}
					else{
						$rayonChoisi = false;
						parent::$_response->addVar('txt_errors'		, gettext('Veuillez choisir un &eacute;tage'));
					}	
					
					parent::$_response->addVar('arrayZones'		, $arrayZones);
					parent::$_response->addVar('arrayRayons'	, $arrayRayons);
					parent::$_response->addVar('form_etage'		, $etageFilter);
					parent::$_response->addVar('form_zone'		, $zoneFilter);
					parent::$_response->addVar('form_rayon'		, $rayonFilter);
					
					if(parent::$_request->getVar('save') == '1'){
						/* Sauvegarder la nouvelle géolocalisation */
						if ($rayonChoisi && $idetagere != null){
		
							/* Ajouter la nouvelle géolocalisation si elle n'existe pas */
							if (!Geolocalisation::exists(parent::$_pdo, $produit->getIdProduit(), $idetagere)){
								$etagere = Etagere::load(parent::$_pdo, $idetagere);
								if($etagere != null)
									$produit->addEtagere($etagere);
								else
									throw new Exception(gettext('Param&egrave;tre invalide'),5);
							}
							Divers::setChangementLocalisation(parent::$_pdo,1);
							$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Ajout d\'une g&eacute;olocalisation pour le produit '). $idProduit.gettext(' par ') .$_SESSION['user_login']."\r\n";
							$logFile = '../application/logs/'.date('m-Y').'-geoloc.log';
							writeLog($log, $logFile);
							header('Location: ' . APPLICATION_PATH . 'produit/afficher/' . $produit->getIdProduit());	// Redirection vers la page du produit
						}
					}
				}
				else{ 
					parent::$_response->addVar('form_etage'		, '');
					parent::$_response->addVar('form_zone'		, '');
					parent::$_response->addVar('form_rayon'		, '');
				}
				parent::$_response->addVar('rayonChoisi' , $rayonChoisi);
			}
		}
	}
	
	public static function supprimer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
			/* On récupère l'id du produit */
			$idProduit= parent::$_request->getVar('id');
			if(!isset($idProduit))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e : idProduit'),3);			
		
			$produit = Produit::load(parent::$_pdo, $idProduit,true);	
			if($produit == null)
				throw new Exception(gettext('Param&egrave;tre invalide : idProduit'),5);	
			
			/* On récupère la géolocalisation */
			$geoloc = parent::$_request->getVar('geoloc');
			if(!isset($geoloc))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e : idEtagere'),3);
			
			$etagere = Etagere::load(parent::$_pdo, $geoloc);
			if($etagere == null)
				throw new Exception(gettext('Param&egrave;tre invalide : idEtagere'),5);
			
			/* Suppression de l'étagère */
			$produit->delEtagere($etagere);
			
			Divers::setChangementLocalisation(parent::$_pdo,1);
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Suppression d\'une g&eacute;olocalisation du produit '). $idProduit.gettext(' par ') .$_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-geoloc.log';
			writeLog($log, $logFile);
			header('Location: ' . APPLICATION_PATH . 'geolocalisation/editer/' .$idProduit);	// Redirection vers la page du produit
		}
	}
}
?>