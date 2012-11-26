<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * ProduitController.php
 *
 * Cette classe permet de récupérer des informations sur les produits.
 *
 * Actions : 
 *
 *		Index 			: L'action par défaut du controller.
 *		Afficher		: Affiche les informations du produit.
 *		Editer			: Modifie les informations du produit.
 *		Priorite 		: Gestion des priorite de picking.
 *		Nongeolocalise	: Affiche les produits qui ne sont pas encore géolocalisés
 *		Inconnu			: Affiche les produits géolocalisés mais non présent dans la base de données.
 *		Sanscodeean		: Affiche les produits sans code ean. 
 *
 */

class ProduitController extends BaseController {

	public static function index(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
						
			parent::$_response->addVar('txt_filtre'				, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'			, gettext('Filtrer'));	
			parent::$_response->addVar('txt_code_produit'		, gettext('Code produit'));			
			parent::$_response->addVar('txt_code_ean'			, gettext('Code Ean'));			
			parent::$_response->addVar('txt_libelle_produit'	, gettext('Libell&eacute; du produit'));			
			parent::$_response->addVar('txt_etage'				, gettext('Etage'));	
			parent::$_response->addVar('txt_zone'				, gettext('Zone'));	
			parent::$_response->addVar('txt_rayon'				, gettext('Rayon'));	
			parent::$_response->addVar('txt_segment'			, gettext('Segment'));	
			parent::$_response->addVar('txt_etagere'			, gettext('Etag&egrave;re'));	
			parent::$_response->addVar('txt_debut'				, gettext('D&eacute;but'));	
			parent::$_response->addVar('txt_normal'				, gettext('Normal'));	
			parent::$_response->addVar('txt_fin'				, gettext('Fin'));	
			parent::$_response->addVar('txt_effacer'			, gettext('Effacer'));
			parent::$_response->addVar('txt_noResults'			, gettext('Aucun produit n\'a &eacute;t&eacute; trouv&eacute;'));
			parent::$_response->addVar('txt_consulter_produit'	, gettext('Consulter le produit'));
			
			/* Récupération des valeurs du filter */
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			$codeFilter 	= parent::$_request->getVar('codeFilter');
			$eanFilter 		= parent::$_request->getVar('eanFilter');
			$etageFilter 	= parent::$_request->getVar('etageFilter');
			$zoneFilter 	= parent::$_request->getVar('zoneFilter');
			$rayonFilter 	= parent::$_request->getVar('rayonFilter');
			$segmentFilter 	= parent::$_request->getVar('segmentFilter');
			$etagereFilter 	= parent::$_request->getVar('etagereFilter');
			$pageFilter 	= parent::$_request->getVar('pageFilter');
			$filtre 		= false;
			
			$produits 		= array();
			$page			= 1;  
			
			if (parent::$_request->getVar('submitFilter')){	// Clic sur le bouton filtrer
				$filtre = true;
				$page 	= $pageFilter;	// Pagination
				
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
										$Segments = Segment::loadAllRayon(parent::$_pdo, $rayonFilter);
										foreach($Segments as $seg){
											/* arraySegments : [0] => l'objet segment, [1] => La position du segment dans le rayon (numérique) */
											$arraySegments[] = array($seg, $seg->getPosition(parent::$_pdo, $seg->getIdsegment(), $rayon->getIdrayon()));
										}
										
										if($segmentFilter != null){
											$segment = Segment::load(parent::$_pdo, $segmentFilter);
											if($segment != null){
												$Etageres = Etagere::loadAllSegment(parent::$_pdo, $segmentFilter);
												foreach($Etageres as $eta){
													/* arrayEtageres : [0] => l'objet étagère, [1] => La position de l'étagère dans le segment (alphabétique) */
													$arrayEtageres[] = array($eta, $eta->getPosition(parent::$_pdo, $eta->getIdetagere(), $segment->getIdsegment()));
												}
												
												if($etagereFilter != null){
													$etagere	= Etagere::load(parent::$_pdo, $etagereFilter);
													if($etagere == null){
														throw new Exception(gettext('Param&egrave;tre invalide'),5);
													}
												}
												parent::$_response->addVar('arrayEtageres' , $arrayEtageres);
											}
											else{
												throw new Exception(gettext('Param&egrave;tre invalide'),5);
											}
										}
										parent::$_response->addVar('arraySegments' , $arraySegments);
									}
									else{
										throw new Exception(gettext('Param&egrave;tre invalide'),5);
									}
								}
								parent::$_response->addVar('arrayRayons' , $arrayRayons);
							}
							else{
								throw new Exception(gettext('Param&egrave;tre invalide'),5);
							}
						}
						parent::$_response->addVar('arrayZones'	, $arrayZones);
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
				}
				$nb_produits		= count(Produit::loadAll(parent::$_pdo,null,$etageFilter,$zoneFilter,$rayonFilter,$segmentFilter,$etagereFilter, $libelleFilter, $codeFilter, $eanFilter));
				$nombre_de_pages 	= (ceil($nb_produits/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_produits/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);	 
				$produits = Produit::loadAll(parent::$_pdo,''.$first,$etageFilter,$zoneFilter,$rayonFilter,$segmentFilter,$etagereFilter, $libelleFilter, $codeFilter, $eanFilter);	

				parent::$_response->addVar('form_libelleFilter'	, $libelleFilter);
				parent::$_response->addVar('form_codeFilter'	, $codeFilter);
				parent::$_response->addVar('form_eanFilter'		, $eanFilter);
				parent::$_response->addVar('form_etage'			, $etageFilter);
				parent::$_response->addVar('form_zone'			, $zoneFilter);
				parent::$_response->addVar('form_rayon'			, $rayonFilter);
				parent::$_response->addVar('form_segment'		, $segmentFilter);
				parent::$_response->addVar('form_etagere'		, $etagereFilter);
				parent::$_response->addVar('form_page'			, $page);
			}
			else{
				$nb_produits		= count(Produit::loadAll(parent::$_pdo));
				$nombre_de_pages 	= (ceil($nb_produits/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_produits/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);	
				$produits = Produit::loadAll(parent::$_pdo, ''.$first);
				parent::$_response->addVar('form_etage'	, '');
				parent::$_response->addVar('form_page'	, $page);
				
			}
			
			$listeProduits = array();
			
			foreach($produits as $produit){
				$etageres			= $produit->selectEtageres();
				if($etageres != null){
					/* Produit géolocalisé */
					foreach($etageres as $etagere){
						$segment			= $etagere->getSegment();
						$rayon 				= $segment->getRayon();
						$seg				= $segment->getPosition(parent::$_pdo, $segment->getIdsegment(), $rayon->getIdrayon());
						$eta				= $etagere->getPosition(parent::$_pdo, $etagere->getIdetagere(), $segment->getIdsegment());
						$zone				= $rayon->getZone();
						$etage				= $zone->getEtage();
						
						$ok = true;
						
						if($etageFilter != null && $etageFilter != $etage->getIdetage())
							$ok = false;
						
						else if($zoneFilter != null && $zoneFilter != $zone->getIdzone())
							$ok = false;
									
						else if($rayonFilter != null && $rayonFilter != $rayon->getIdrayon())
							$ok = false;										
											
						else if($segmentFilter != null && $segmentFilter != $segment->getIdsegment())
							$ok = false;
							
						else if($etagereFilter != null && $etagereFilter != $etagere->getIdetagere())
							$ok = false;							
						
						if($ok)
							$listeProduits[] 	= array($produit,$eta,$seg,$rayon->getLibelle(),$zone->getLibelle(),$etage->getLibelle());
					}
				}
				else{
					/* Produit non géolocalisé */
					$listeProduits[] = array($produit,gettext('non d&eacute;fini'), gettext('non d&eacute;fini'), gettext('non d&eacute;fini'), gettext('non d&eacute;fini') , gettext('non d&eacute;fini'));
				}
			}
			
			parent::$_response->addVar('nb_resultats'		, $nb_produits);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
			parent::$_response->addVar('page'				, $page);
			parent::$_response->addVar('txt_page'			, gettext('Page'));
			parent::$_response->addVar('txt_titre'			, gettext('Liste des produits'));	
			parent::$_response->addVar('arrayEtages'		, Etage::loadAll(parent::$_pdo,true));			
			parent::$_response->addVar('arrayProduits'		, $listeProduits);
		}
	}	
	
	
		
	/* Action importer
	 * 
	 * Importe les produits
	 *
	 */
	 
	public static function importer(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil administrateur		
			require_once "../flux/in/produits/produits_recup.php";
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Importation des produits par ') .$_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-produit.log';
			writeLog($log, $logFile);
			
			parent::$_response->addVar('txt_importer'	, gettext('L\'import des produits s\'est d&eacute;roul&eacute; correctement.'));
			parent::$_response->addVar('txt_retour'		, gettext('Retour aux produits'));
		}
	}
	
	
	
	
		
	
	
	
	
	
	/* Action afficher
	 * 
	 * Récupère et affiche les informations d'un produit
	 *
	 */
	 
	public static function afficher(){ 
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_PREPARATEUR)){
			$idProduit= parent::$_request->getVar('id');
			if(!isset($idProduit))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
			
			/* Chargement du produit */
			$produit = Produit::load(parent::$_pdo, parent::$_request->getVar('id'));	
			if($produit == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
				
			parent::$_response->addVar('produit'					, $produit);	
			parent::$_response->addVar('arrayEans'					, $produit->selectEans());
			parent::$_response->addVar('txt_produit'				, gettext('Produit'));		
			parent::$_response->addVar('txt_libelle'				, gettext('Libelle'));	
			parent::$_response->addVar('txt_codeProduit'			, gettext('Code du produit'));	
			parent::$_response->addVar('txt_largeur'				, gettext('Largeur'));	
			parent::$_response->addVar('txt_hauteur'				, gettext('Hauteur'));	
			parent::$_response->addVar('txt_profondeur'				, gettext('Profondeur'));	
			parent::$_response->addVar('txt_uniteMesure'			, gettext('Unite de mesure'));	
			parent::$_response->addVar('txt_quantiteParUniteMesure'	, gettext('Quantit&eacute; par unit&eacute; de mesure'));	
			parent::$_response->addVar('txt_poidsBrut'				, gettext('Poids brut'));	
			parent::$_response->addVar('txt_poidsNet'				, gettext('Poids net'));	
			parent::$_response->addVar('txt_estPoidsVariable'		, gettext('Est un poids variable'));	
			parent::$_response->addVar('txt_priorite'				, gettext('Priorit&eacute;'));	
			parent::$_response->addVar('txt_stock'					, gettext('Stock'));	
			parent::$_response->addVar('txt_aucun'					, gettext('Aucun'));	
			parent::$_response->addVar('txt_provenance'				, gettext('Provenance'));	
			parent::$_response->addVar('txt_photo'					, gettext('Photo'));	
			parent::$_response->addVar('txt_non'					, gettext('Non'));	
			parent::$_response->addVar('txt_oui'					, gettext('Oui'));	
			parent::$_response->addVar('txt_retour'					, gettext('Retour'));	
			parent::$_response->addVar('txt_infos'					, gettext('Informations du produit'));	
			parent::$_response->addVar('txt_eans'					, gettext('Les codes Eans correspondant au produit'));	
			parent::$_response->addVar('txt_modifGeoloc'			, gettext('Modifier la g&eacute;olocalisation du produit'));	
			parent::$_response->addVar('txt_modifGeolocPreparateur'	, gettext('Afficher la g&eacute;olocalisation du produit'));	
			parent::$_response->addVar('txt_null'					, gettext('Sans priorit&eacute;'));	
			parent::$_response->addVar('txt_debut'					, gettext('D&eacute;but'));	
			parent::$_response->addVar('txt_normal'					, gettext('Normal'));	
			parent::$_response->addVar('txt_fin'					, gettext('Fin'));
		}	
	}


	/* Action editer
	 * 
	 * Change les informations d'un produit
	 *
	 */	
	
	public static function editer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
			parent::$_response->addVar('txt_editerProduit'			, gettext('Editer un produit'));	
			parent::$_response->addVar('txt_retour'					, gettext('Retour'));	
			parent::$_response->addVar('txt_code_produit'			, gettext('Code du produit'));	
			parent::$_response->addVar('txt_libelle'				, gettext('Libelle'));	
			parent::$_response->addVar('txt_largeur'				, gettext('Largeur'));	
			parent::$_response->addVar('txt_hauteur'				, gettext('Hauteur'));	
			parent::$_response->addVar('txt_profondeur'				, gettext('Profondeur'));	
			parent::$_response->addVar('txt_uniteMesure'			, gettext('Unite de mesure'));	
			parent::$_response->addVar('txt_quantiteParUniteMesure'	, gettext('Quantit&eacute; par unit&eacute; de mesure'));	
			parent::$_response->addVar('txt_poidsBrut'				, gettext('Poids brut'));	
			parent::$_response->addVar('txt_poidsNet'				, gettext('Poids net'));	
			parent::$_response->addVar('txt_estPoidsVariable'		, gettext('Est un poids variable'));	
			parent::$_response->addVar('txt_priorite'				, gettext('Priorit&eacute;'));	
			parent::$_response->addVar('txt_stock'					, gettext('Stock'));	
			parent::$_response->addVar('txt_photo'					, gettext('Photo'));	
			parent::$_response->addVar('txt_supprimerEan'			, gettext('Supprimer ce code EAN'));	
			parent::$_response->addVar('txt_non'					, gettext('Non'));	
			parent::$_response->addVar('txt_oui'					, gettext('Oui'));	
			parent::$_response->addVar('txt_boutonEnregistrer'		, gettext('Enregistrer'));	
			parent::$_response->addVar('txt_infosProduit'			, gettext('Informations du produit'));
			parent::$_response->addVar('txt_eans'					, gettext('Les codes Eans correspondant au produit'));	
			parent::$_response->addVar('txt_ajouterEan'				, gettext('Ajouter un code EAN pour ce produit'));	
			parent::$_response->addVar('txt_null'					, gettext('Sans priorit&eacute;'));	
			parent::$_response->addVar('txt_debut'					, gettext('D&eacute;but'));	
			parent::$_response->addVar('txt_normal'					, gettext('Normal'));	
			parent::$_response->addVar('txt_fin'					, gettext('Fin'));	
			parent::$_response->addVar('txt_confirmSuppression'		, gettext('Etes-vous s&ucirc;r de vouloir supprimer ce produit?'));	
			parent::$_response->addVar('txt_lienSupprimer'			, gettext('Supprimer le produit'));
			parent::$_response->addVar('txt_confirmSuppressionEan'	, gettext('Etes-vous s&ucirc;r de vouloir supprimer ce code EAN?'));	
			
			if(parent::$_request->getVar('submit')){
				$error_message = '';
				
				$product = Produit::load(parent::$_pdo, parent::$_request->getVar('id'));	// Chargement de l'objet produit
				if($product == null)
					throw new Exception(gettext('Param&egrave;tre invalide'),5);
	
				$codeProduit = parent::$_request->getVar('codeProduit');
				if ($codeProduit != $product->getCodeProduit()){
					if(!Produit::testIntegrite('Code produit', $codeProduit)){
						$error_message .= gettext('Veuillez renseigner un code produit valide') . '<br />';
					}
				}
				
				$libelle = parent::$_request->getVar('libelle');
				if(!Produit::testIntegrite('Libelle', $libelle)){
					$error_message .= gettext('Veuillez renseigner un libelle valide') . '<br />';
				}
				
				$largeur = parent::$_request->getVar('largeur');
				if(!Produit::testIntegrite('Largeur', $largeur)){
					$error_message .= gettext('Veuillez renseigner une largeur valide') . '<br />';
				}
				
				$hauteur = parent::$_request->getVar('hauteur');
				if(!Produit::testIntegrite('Hauteur', $hauteur)){
					$error_message .= gettext('Veuillez renseigner une hauteur valide') . '<br />';
				}
				
				$profondeur = parent::$_request->getVar('profondeur');
				if(!Produit::testIntegrite('Profondeur', $profondeur)){
					$error_message .= gettext('Veuillez renseigner une profondeur valide') . '<br />';
				}
				
				$uniteMesure = parent::$_request->getVar('uniteMesure');
				if(!Produit::testIntegrite('Unite de mesure', $uniteMesure)){
					$error_message .= gettext('Veuillez renseigner une unit&eacute; de mesure valide') . '<br />';
				}
				
				$quantiteParUniteMesure = parent::$_request->getVar('quantiteParUniteMesure');
				if(!Produit::testIntegrite('Quantite par unite de mesure', $quantiteParUniteMesure)){
					$error_message .= gettext('Veuillez renseigner une quantit&eacute; par unit&eacute; de mesure valide') . '<br />';
				}
				
				$poidsBrut = parent::$_request->getVar('poidsBrut');
				if(!Produit::testIntegrite('Poids brut', $poidsBrut)){
					$error_message .= gettext('Veuillez renseigner un poids brut valide') . '<br />';
				}
				
				$poidsNet = parent::$_request->getVar('poidsNet');
				if(!Produit::testIntegrite('Poids net', $poidsNet)){
					$error_message .= gettext('Veuillez renseigner un poids net valide') . '<br />';
				}
				
				$estPoidsVariable = (int) parent::$_request->getVar('estPoidsVariable');
				if(!Produit::testIntegrite('Est poids variable', $estPoidsVariable)){
					$error_message .= gettext('Veuillez renseigner un estPoidsVariable valide') . '<br />';
				}
				
				$priorite = (int) parent::$_request->getVar('priorite');
				if(!Produit::testIntegrite('Priorite', $priorite)){
					$error_message .= gettext('Veuillez renseigner une priorit&eacute; valide') . '<br />';
				}
				if ($priorite == '') $priorite = null;
				
				$stock = (int) parent::$_request->getVar('stock');
				if(!Produit::testIntegrite('Stock', $stock)){
					$error_message .= gettext('Veuillez renseigner une valeur de stock valide') . '<br />';
				}
				if ($stock == '') $stock = null;
				
				$eanToDelete 	= (int) parent::$_request->getVar('eanToDelete');
				if ($eanToDelete != null) {
					$ean =  Ean::load(parent::$_pdo, $eanToDelete, true);
					if ($ean != null){
						if(!$ean->delete()){
							throw new Exception(gettext('Impossible de supprimmer le code EAN'),4);
						}	
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
					
					$eans = array();
					foreach($product->selectEans() as $ean){
						$eans[] = array($ean->getIdEan(), $ean->getEan());
					}
				}
				else{
					$eans = parent::$_request->getVar('ean');
					if ($eans != null){
						foreach($eans as $key=>$ean){
							$code = (int) $ean;
							if ($ean != '' && $ean != null){
								$testEan = Ean::loadByCodeEan(parent::$_pdo, $ean, $product->getIdProduit());
								if($testEan != null){
									unset($eans[$key]);
									$error_message .= gettext('Ce code EAN existe d&eacute;j&agrave; pour un autre produit. ') . gettext('Veuillez renseigner un autre code EAN.') . '<br />';
								}
								else if ($code == null ){ //|| strlen($ean) != 13){
									$error_message .= gettext('Veuillez renseigner un code EAN valide') . '<br />';
								}
							}
						}
					}
					
					$items 	= array_merge($eans);
					$eans 	= array();
					foreach($items as $item){
						$ean = Ean::loadByCodeEan(parent::$_pdo, $item);
						if ($ean != null)
							$eans[] = array($ean->getIdEan(), $ean->getEan());
						else
							$eans[] = array(0, $item);
					}
				}
				
				/* Récupérer la photo du produit */
				$photo = parent::$_request->getVar('photo');
				
				if($photo == null)
					$photo = APPLICATION_PATH . 'images/default.png';
				
				if($error_message == ''){	
					if ($product != null) {
						/* Mise à jour des informartions de l'utilisateur */
						$product->setCodeProduit($codeProduit,false);
						$product->setLibelle($libelle,false);
						if($largeur != null) $product->setLargeur($largeur,false);
						if($hauteur != null) $product->setHauteur($hauteur,false);
						if($profondeur != null) $product->setProfondeur($profondeur,false);
						if($uniteMesure != null) $product->setUniteMesure($uniteMesure,false);
						if($quantiteParUniteMesure != null) $product->setQuantiteParUniteMesure($quantiteParUniteMesure,false);
						if($poidsBrut != null) $product->setPoidsBrut($poidsBrut,false);
						if($poidsNet != null) $product->setPoidsNet($poidsNet,false);
						$product->setEstPoidsVariable($estPoidsVariable,false);
						$product->setPriorite($priorite,false);
						$product->setStock($stock,false);
						if ($photo != null)	$product->setPhoto($photo,false);
						$product->update();								// Ecriture dans la base de données 
						
						if ($eanToDelete == null) {
							/* Mis à jour des codes EAN */
							
							$liste 	= $product->selectEans();	// Liste des eans déjà présents dans la bases de données
							$listeEan = array();
							foreach($liste as $elem){
								$listeEan[] = $elem->getEan();
							}
							
							foreach($eans as $ean) {
								if(!in_array($ean[1], $listeEan)) {
									/* Si, pour un produit donné, l'ean n'est pas présent dans la table => Ajouter */
									if ($ean[1] != null && $ean[1] != ''){
										$itemEan = Ean::create(parent::$_pdo,$product->getIdProduit(),$ean[1]);
									}
								}
							}
							
							$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Edition du produit '). $product->getIdProduit() . gettext(' par ') . $_SESSION['user_login']."\r\n";
							$logFile = '../application/logs/'.date('m-Y').'-produit.log';
							writeLog($log, $logFile);
							
							header('Location: ' . APPLICATION_PATH . 'produit/afficher/' . $product->getIdProduit());	// Redirection vers la page du produit
						}
						else{
							parent::$_response->addVar('form_errors'				, $error_message);
							parent::$_response->addVar('form_idProduit'				, parent::$_request->getVar('id'));
							parent::$_response->addVar('form_codeProduit'			, $codeProduit);
							parent::$_response->addVar('form_libelle'				, $libelle);
							parent::$_response->addVar('form_hauteur'				, $hauteur);
							parent::$_response->addVar('form_largeur'				, $largeur);
							parent::$_response->addVar('form_profondeur'			, $profondeur);
							parent::$_response->addVar('form_uniteMesure'			, $uniteMesure);
							parent::$_response->addVar('form_quantiteParUniteMesure', $quantiteParUniteMesure);
							parent::$_response->addVar('form_poidsBrut'				, $poidsBrut);
							parent::$_response->addVar('form_poidsNet'				, $poidsNet);
							parent::$_response->addVar('form_estPoidsVariable'		, $estPoidsVariable);
							parent::$_response->addVar('form_photo'					, $photo);
							parent::$_response->addVar('form_priorite'				, $priorite);
							parent::$_response->addVar('form_stock'					, $stock);
							parent::$_response->addVar('form_arrayEans'				, $eans);
							parent::$_response->addVar('form_noEan'					, gettext('Ce produit ne poss&egrave;de pas de code EAN !'));
						}
					}
					else {
						parent::$_response->addVar('form_errors',gettext('aucun produit trouv&eacute;')); 
					}
				}
				else{
					parent::$_response->addVar('form_errors'				, $error_message);
					parent::$_response->addVar('form_idProduit'				, parent::$_request->getVar('id'));
					parent::$_response->addVar('form_codeProduit'			, $codeProduit);
					parent::$_response->addVar('form_libelle'				, $libelle);
					parent::$_response->addVar('form_hauteur'				, $hauteur);
					parent::$_response->addVar('form_largeur'				, $largeur);
					parent::$_response->addVar('form_profondeur'			, $profondeur);
					parent::$_response->addVar('form_uniteMesure'			, $uniteMesure);
					parent::$_response->addVar('form_quantiteParUniteMesure', $quantiteParUniteMesure);
					parent::$_response->addVar('form_poidsBrut'				, $poidsBrut);
					parent::$_response->addVar('form_poidsNet'				, $poidsNet);
					parent::$_response->addVar('form_estPoidsVariable'		, $estPoidsVariable);
					parent::$_response->addVar('form_photo'					, $photo);
					parent::$_response->addVar('form_priorite'				, $priorite);
					parent::$_response->addVar('form_stock'					, $stock);
					parent::$_response->addVar('form_arrayEans'				, $eans);
					parent::$_response->addVar('form_noEan'					, gettext('Ce produit ne poss&egrave;de pas de code EAN !'));
				}
			}
			else {
				$idProduit= parent::$_request->getVar('id');
				if(!isset($idProduit))
					throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
			
				$produit = Produit::load(parent::$_pdo, $idProduit,true);	
				if($produit == null)
					throw new Exception(gettext('Param&egrave;tre invalide'),5);	
				
				$eans = array();
				foreach($produit->selectEans() as $item){
					$eans[] = array($item->getIdEan(), $item->getEan());
				}
				
				
				parent::$_response->addVar('form_idProduit'				, $idProduit);
				parent::$_response->addVar('form_codeProduit'			, $produit->getCodeProduit());
				parent::$_response->addVar('form_libelle'				, $produit->getLibelle());
				parent::$_response->addVar('form_hauteur'				, $produit->getHauteur());
				parent::$_response->addVar('form_largeur'				, $produit->getLargeur());
				parent::$_response->addVar('form_profondeur'			, $produit->getProfondeur());
				parent::$_response->addVar('form_uniteMesure'			, $produit->getUniteMesure());
				parent::$_response->addVar('form_quantiteParUniteMesure', $produit->getQuantiteParUniteMesure());
				parent::$_response->addVar('form_poidsBrut'				, $produit->getPoidsBrut());
				parent::$_response->addVar('form_poidsNet'				, $produit->getPoidsNet());
				parent::$_response->addVar('form_estPoidsVariable'		, $produit->getEstPoidsVariable());
				parent::$_response->addVar('form_photo'					, $produit->getPhoto());
				parent::$_response->addVar('form_priorite'				, $produit->getPriorite(true));
				parent::$_response->addVar('form_stock'					, $produit->getStock());
				parent::$_response->addVar('form_arrayEans'				, $eans);
				parent::$_response->addVar('form_noEan'					, gettext('Ce produit ne poss&egrave;de pas de code EAN !'));
				
			}			
		}
	}
	
	
	
	/* Action ajouter
	 * 
	 * Ajouter manuellement un produit
	 *
	 */	
	
	public static function ajouter(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
			parent::$_response->addVar('txt_ajouterProduit'			, gettext('Ajouter un produit'));	
			parent::$_response->addVar('txt_retour'					, gettext('Retour'));	
			parent::$_response->addVar('txt_code_produit'			, gettext('Code du produit'));	
			parent::$_response->addVar('txt_libelle'				, gettext('Libelle'));	
			parent::$_response->addVar('txt_largeur'				, gettext('Largeur'));	
			parent::$_response->addVar('txt_hauteur'				, gettext('Hauteur'));	
			parent::$_response->addVar('txt_profondeur'				, gettext('Profondeur'));	
			parent::$_response->addVar('txt_uniteMesure'			, gettext('Unite de mesure'));	
			parent::$_response->addVar('txt_quantiteParUniteMesure'	, gettext('Quantit&eacute; par unit&eacute; de mesure'));	
			parent::$_response->addVar('txt_poidsBrut'				, gettext('Poids brut'));	
			parent::$_response->addVar('txt_poidsNet'				, gettext('Poids net'));	
			parent::$_response->addVar('txt_estPoidsVariable'		, gettext('Est un poids variable'));	
			parent::$_response->addVar('txt_priorite'				, gettext('Priorit&eacute;'));	
			parent::$_response->addVar('txt_stock'					, gettext('Stock'));	
			parent::$_response->addVar('txt_photo'					, gettext('Photo'));	
			parent::$_response->addVar('txt_non'					, gettext('Non'));	
			parent::$_response->addVar('txt_oui'					, gettext('Oui'));	
			parent::$_response->addVar('txt_boutonEnregistrer'		, gettext('Enregistrer'));	
			parent::$_response->addVar('txt_infosProduit'			, gettext('Informations du produit'));
			parent::$_response->addVar('txt_eans'					, gettext('Les codes Eans correspondant au produit'));	
			parent::$_response->addVar('txt_ajouterEan'				, gettext('Ajouter un code EAN pour ce produit'));	
			parent::$_response->addVar('txt_null'					, gettext('Sans priorit&eacute;'));	
			parent::$_response->addVar('txt_debut'					, gettext('D&eacute;but'));	
			parent::$_response->addVar('txt_normal'					, gettext('Normal'));	
			parent::$_response->addVar('txt_fin'					, gettext('Fin'));	
			
			if(parent::$_request->getVar('submit')){
				$error_message = '';
	
				$codeProduit = parent::$_request->getVar('codeProduit');
				if(!Produit::testIntegrite('Code produit', $codeProduit)){
					$error_message .= gettext('Veuillez renseigner un code produit valide') . '<br />';
				}
				
				$libelle = parent::$_request->getVar('libelle');
				if(!Produit::testIntegrite('Libelle', $libelle)){
					$error_message .= gettext('Veuillez renseigner un libelle valide') . '<br />';
				}
				
				$largeur = parent::$_request->getVar('largeur');
				if(!Produit::testIntegrite('Largeur', $largeur)){
					$error_message .= gettext('Veuillez renseigner une largeur valide') . '<br />';
				}
				
				$hauteur = parent::$_request->getVar('hauteur');
				if(!Produit::testIntegrite('Hauteur', $hauteur)){
					$error_message .= gettext('Veuillez renseigner une hauteur valide') . '<br />';
				}
				
				$profondeur = parent::$_request->getVar('profondeur');
				if(!Produit::testIntegrite('Profondeur', $profondeur)){
					$error_message .= gettext('Veuillez renseigner une profondeur valide') . '<br />';
				}
				
				$uniteMesure = parent::$_request->getVar('uniteMesure');
				if(!Produit::testIntegrite('Unite de mesure', $uniteMesure)){
					$error_message .= gettext('Veuillez renseigner une unit&eacute; de mesure valide') . '<br />';
				}
				
				$quantiteParUniteMesure = parent::$_request->getVar('quantiteParUniteMesure');
				if(!Produit::testIntegrite('Quantite par unite de mesure', $quantiteParUniteMesure)){
					$error_message .= gettext('Veuillez renseigner une quantit&eacute; par unit&eacute; de mesure valide') . '<br />';
				}
				
				$poidsBrut = parent::$_request->getVar('poidsBrut');
				if(!Produit::testIntegrite('Poids brut', $poidsBrut)){
					$error_message .= gettext('Veuillez renseigner un poids brut valide') . '<br />';
				}
				
				$poidsNet = parent::$_request->getVar('poidsNet');
				if(!Produit::testIntegrite('Poids net', $poidsNet)){
					$error_message .= gettext('Veuillez renseigner un poids net valide') . '<br />';
				}
				
				$estPoidsVariable = (int) parent::$_request->getVar('estPoidsVariable');
				if(!Produit::testIntegrite('Est poids variable', $estPoidsVariable)){
					$error_message .= gettext('Veuillez renseigner un estPoidsVariable valide') . '<br />';
				}
				
				$priorite = (int) parent::$_request->getVar('priorite');
				if(!Produit::testIntegrite('Priorite', $priorite)){
					$error_message .= gettext('Veuillez renseigner une priorit&eacute; valide') . '<br />';
				}
				if ($priorite == '') $priorite = null;
				
				$stock = (int) parent::$_request->getVar('stock');
				if(!Produit::testIntegrite('Stock', $stock)){
					$error_message .= gettext('Veuillez renseigner une valeur de stock valide') . '<br />';
				}
				if ($stock == '') $stock = null;
				
				$eans = parent::$_request->getVar('ean');
				if ($eans != null){
					foreach($eans as $key=>$ean){
						$code = (int) $ean;
						if ($ean != '' && $ean != null){
							$testEan = Ean::loadByCodeEan(parent::$_pdo, $ean);
							if($testEan != null){
								unset($eans[$key]);
								$error_message .= gettext('Ce code EAN existe d&eacute;j&agrave; pour un autre produit. ') . gettext('Veuillez renseigner un autre code EAN.') . '<br />';
							}
							else if ($code == null ){ //|| strlen($ean) != 13){
								$error_message .= gettext('Veuillez renseigner un code EAN valide') . '<br />';
							}
						}
					}
				}
				else
					$eans = array();
					
				$items 	= array_merge($eans);
				$eans 	= array();
				foreach($items as $item){
					$ean = Ean::loadByCodeEan(parent::$_pdo, $item);
					if ($ean != null)
						$eans[] = array($ean->getIdEan(), $ean->getEan());
					else
						$eans[] = array(0, $item);
				}
				
				/* Récupérer la photo du produit */
				$photo = parent::$_request->getVar('photo');
				
				if($photo == null)
					$photo = APPLICATION_PATH . 'images/default.png';
				
				
				if($error_message == ''){	
					$product = Produit::create(parent::$_pdo, $codeProduit, $libelle, $photo, $largeur, $hauteur, $profondeur, $uniteMesure, $quantiteParUniteMesure, $poidsBrut, $poidsNet, $estPoidsVariable, $priorite, $stock);
					
					foreach($eans as $ean) {
						if(!in_array($ean[1], $listeEan)) {
							/* Si, pour un produit donné, l'ean n'est pas présent dans la table => Ajouter */
							if ($ean[1] != null && $ean[1] != ''){
								$itemEan = Ean::create(parent::$_pdo,$product->getIdProduit(),$ean[1]);
							}
						}
					}
					
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Ajout du produit '). $product->getIdProduit() . gettext(' par ') . $_SESSION['user_login']."\r\n";
					$logFile = '../application/logs/'.date('m-Y').'-produit.log';
					writeLog($log, $logFile);
					
					header('Location: ' . APPLICATION_PATH . 'produit/afficher/' . $product->getIdProduit());	// Redirection vers la page du produit
				
					

				}
				else{
					parent::$_response->addVar('form_errors'				, $error_message);
					parent::$_response->addVar('form_codeProduit'			, $codeProduit);
					parent::$_response->addVar('form_libelle'				, $libelle);
					parent::$_response->addVar('form_hauteur'				, $hauteur);
					parent::$_response->addVar('form_largeur'				, $largeur);
					parent::$_response->addVar('form_profondeur'			, $profondeur);
					parent::$_response->addVar('form_uniteMesure'			, $uniteMesure);
					parent::$_response->addVar('form_quantiteParUniteMesure', $quantiteParUniteMesure);
					parent::$_response->addVar('form_poidsBrut'				, $poidsBrut);
					parent::$_response->addVar('form_poidsNet'				, $poidsNet);
					parent::$_response->addVar('form_estPoidsVariable'		, $estPoidsVariable);
					parent::$_response->addVar('form_photo'					, $photo);
					parent::$_response->addVar('form_priorite'				, $priorite);
					parent::$_response->addVar('form_stock'					, $stock);
					parent::$_response->addVar('form_arrayEans'				, $eans);
					parent::$_response->addVar('form_noEan'					, gettext('Ce produit ne poss&egrave;de pas de code EAN !'));
				}
			}
			else {				
				parent::$_response->addVar('form_codeProduit'			, '');
				parent::$_response->addVar('form_libelle'				, '');
				parent::$_response->addVar('form_hauteur'				, '');
				parent::$_response->addVar('form_largeur'				, '');
				parent::$_response->addVar('form_profondeur'			, '');
				parent::$_response->addVar('form_uniteMesure'			, '');
				parent::$_response->addVar('form_quantiteParUniteMesure', '');
				parent::$_response->addVar('form_poidsBrut'				, '');
				parent::$_response->addVar('form_poidsNet'				, '');
				parent::$_response->addVar('form_estPoidsVariable'		, '');
				parent::$_response->addVar('form_photo'					, '');
				parent::$_response->addVar('form_priorite'				, '');				
				parent::$_response->addVar('form_stock'					, '');				
			}			
		}
	}
	
	
	
	
	public static function priorite() {
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			parent::$_response->addVar('txt_titre'				, gettext('Gestion des priorit&eacute;s de passage'));			
			parent::$_response->addVar('txt_filtre'				, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'			, gettext('Filtrer'));	
			parent::$_response->addVar('txt_code_produit'		, gettext('Code produit'));			
			parent::$_response->addVar('txt_code_ean'			, gettext('Code Ean'));			
			parent::$_response->addVar('txt_libelle_produit'	, gettext('Libell&eacute; du produit'));			
			parent::$_response->addVar('txt_etage'				, gettext('Etage'));	
			parent::$_response->addVar('txt_zone'				, gettext('Zone'));	
			parent::$_response->addVar('txt_rayon'				, gettext('Rayon'));	
			parent::$_response->addVar('txt_segment'			, gettext('Segment'));	
			parent::$_response->addVar('txt_etagere'			, gettext('Etag&egrave;re'));	
			parent::$_response->addVar('txt_affectPriorite'		, gettext('Affectation des priorit&eacute;s'));	
			parent::$_response->addVar('txt_priorite'			, gettext('Priorit&eacute;'));	
			parent::$_response->addVar('txt_provenance'			, gettext('Provenance'));	
			parent::$_response->addVar('txt_null'				, gettext('Sans priorit&eacute;'));	
			parent::$_response->addVar('txt_debut'				, gettext('D&eacute;but'));	
			parent::$_response->addVar('txt_normal'				, gettext('Normal'));	
			parent::$_response->addVar('txt_fin'				, gettext('Fin'));	
			parent::$_response->addVar('txt_confirm'			, gettext('Etes vous s&ucirc;r ?'));	
			parent::$_response->addVar('txt_effacer'			, gettext('Effacer'));
			parent::$_response->addVar('txt_noResults'			, gettext('Aucun produit g&eacute;olocalis&eacute; n\'a &eacute;t&eacute; trouv&eacute;'));
			parent::$_response->addVar('txt_consulter_produit'	, gettext('Consulter la fiche du produit'));
			parent::$_response->addVar('txt_consulter_rayon'	, gettext('Consulter le rayon'));

			
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			$codeFilter 	= parent::$_request->getVar('codeFilter');
			$eanFilter 		= parent::$_request->getVar('eanFilter');
			$etageFilter 	= parent::$_request->getVar('etageFilter');
			$zoneFilter 	= parent::$_request->getVar('zoneFilter');
			$rayonFilter 	= parent::$_request->getVar('rayonFilter');
			$segmentFilter 	= parent::$_request->getVar('segmentFilter');
			$etagereFilter 	= parent::$_request->getVar('etagereFilter');
			$pageFilter 	= parent::$_request->getVar('pageFilter');
			
			$produits		= array();
			$page			= 1;  
			
			parent::$_response->addVar('txt_finessePriorite'	, gettext('Affecter cette priorit&eacute; &agrave; tous les produits du magasin'));
			parent::$_response->addVar('choixFinessePriorite'	, 'all');
			parent::$_response->addVar('idFinessePriorite'		, 'all');
			parent::$_response->addVar('txt_helpFinessePriorite', gettext('En cochant cette option, vous affectez la priorit&eacute; &agrave; tous les produits pr&eacute;sents dans le magasin.'));
						
			if (parent::$_request->getVar('submitFilter')){	// Clic sur le bouton filtrer
				$page = $pageFilter;
				
				if($etageFilter != null){
					$etage = Etage::load(parent::$_pdo, $etageFilter);
					if ($etage != null){
					
						parent::$_response->addVar('txt_finessePriorite'	, gettext('Affecter cette priorit&eacute; &agrave; l\'&eacute;tage choisi'));
						parent::$_response->addVar('choixFinessePriorite'	, 'etage');
						parent::$_response->addVar('idFinessePriorite'		, $etageFilter);
						parent::$_response->addVar('txt_helpFinessePriorite', gettext('En cochant cette option, vous affectez la priorit&eacute; &agrave; l\'&eacute;tage choisi dans le filtre et non pas &agrave; chaque produit. Ainsi, tous les produits qui se trouvent dans cet &eacute;tage auront cette priorit&eacute;.'));
						
						$arrayZones = Zone::loadAllEtage(parent::$_pdo, $etageFilter);
						if($zoneFilter != null){
							$zone = Zone::load(parent::$_pdo, $zoneFilter);
							if ($zone != null){
								
								parent::$_response->addVar('txt_finessePriorite'	, gettext('Affecter cette priorit&eacute; &agrave; la zone choisie'));
								parent::$_response->addVar('choixFinessePriorite'	, 'zone');
								parent::$_response->addVar('idFinessePriorite'		, $zoneFilter);
								parent::$_response->addVar('txt_helpFinessePriorite', gettext('En cochant cette option, vous affectez la priorit&eacute; &agrave; la zone choisie dans le filtre et non pas &agrave; chaque produit. Ainsi, tous les produits qui se trouvent dans cette zone auront cette priorit&eacute;.'));
								
								$arrayRayons = Rayon::loadAllZone(parent::$_pdo, $zoneFilter);
								if($rayonFilter != null){
									$rayon = Rayon::load(parent::$_pdo, $rayonFilter);
									if ($rayon != null){
										
										parent::$_response->addVar('txt_finessePriorite'	, gettext('Affecter cette priorit&eacute; au rayon choisi'));
										parent::$_response->addVar('choixFinessePriorite'	, 'rayon');
										parent::$_response->addVar('idFinessePriorite'		, $rayonFilter);
										parent::$_response->addVar('txt_helpFinessePriorite', gettext('En cochant cette option, vous affectez la priorit&eacute; au rayon choisi dans le filtre et non pas &agrave; chaque produit. Ainsi, tous les produits qui se trouvent dans ce rayon auront cette priorit&eacute;.'));
										
										$Segments = Segment::loadAllRayon(parent::$_pdo, $rayonFilter);
										foreach($Segments as $seg){
											$arraySegments[] = array($seg, $seg->getPosition(parent::$_pdo, $seg->getIdsegment(), $rayon->getIdrayon()));
										}
										
										if($segmentFilter != null){
											$segment = Segment::load(parent::$_pdo, $segmentFilter);
											if($segment != null){
												
												parent::$_response->addVar('txt_finessePriorite'	, gettext('Affecter cette priorit&eacute; au segment choisi'));
												parent::$_response->addVar('choixFinessePriorite'	, 'segment');
												parent::$_response->addVar('idFinessePriorite'		, $segmentFilter);
												parent::$_response->addVar('txt_helpFinessePriorite', gettext('En cochant cette option, vous affectez la priorit&eacute; au segment choisi dans le filtre et non pas &agrave; chaque produit. Ainsi, tous les produits qui se trouvent dans ce segment auront cette priorit&eacute;.'));
												
												$Etageres = Etagere::loadAllSegment(parent::$_pdo, $segmentFilter);
												foreach($Etageres as $eta){
													$arrayEtageres[] = array($eta, $eta->getPosition(parent::$_pdo, $eta->getIdetagere(), $segment->getIdsegment()));
												}
												
												if($etagereFilter != null){
													$etagere	= Etagere::load(parent::$_pdo, $etagereFilter);
													if($etagere != null){
														parent::$_response->addVar('txt_finessePriorite'	, gettext('Affecter cette priorit&eacute; &agrave; l\'&eacute;tag&egrave;re choisie'));
														parent::$_response->addVar('choixFinessePriorite'	, 'etagere');
														parent::$_response->addVar('idFinessePriorite'		, $rayonFilter);
														parent::$_response->addVar('txt_helpFinessePriorite', gettext('En cochant cette option, vous affectez la priorit&eacute; &agrave; l\'&eacute;tag&egrave;re choisi dans le filtre et non pas &agrave; chaque produit. Ainsi, tous les produits qui se trouvent dans cette &eacute;tag&egrave;re auront cette priorit&eacute;.'));
													}
													else{
														throw new Exception(gettext('Param&egrave;tre invalide'),5);
													}
												}
												parent::$_response->addVar('arrayEtageres' , $arrayEtageres);
											}
											else{
												throw new Exception(gettext('Param&egrave;tre invalide'),5);
											}
										}
										parent::$_response->addVar('arraySegments' , $arraySegments);
									}
									else{
										throw new Exception(gettext('Param&egrave;tre invalide'),5);
									}
								}
								parent::$_response->addVar('arrayRayons' , $arrayRayons);
							}
							else{
								throw new Exception(gettext('Param&egrave;tre invalide'),5);
							}
						}
						parent::$_response->addVar('arrayZones'	, $arrayZones);	
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
				}
				$nb					= count(Produit::loadAll(parent::$_pdo,null, $etageFilter,$zoneFilter,$rayonFilter,$segmentFilter,$etagereFilter, $libelleFilter, $codeFilter, $eanFilter, 'geoloc'));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);	
				$produits = Produit::loadAll(parent::$_pdo,''.$first, $etageFilter,$zoneFilter,$rayonFilter,$segmentFilter,$etagereFilter, $libelleFilter, $codeFilter, $eanFilter, 'geoloc');	
				
				parent::$_response->addVar('form_libelleFilter'	, $libelleFilter);
				parent::$_response->addVar('form_codeFilter'	, $codeFilter);
				parent::$_response->addVar('form_eanFilter'		, $eanFilter);
				parent::$_response->addVar('form_etage'			, $etageFilter);
				parent::$_response->addVar('form_zone'			, $zoneFilter);
				parent::$_response->addVar('form_rayon'			, $rayonFilter);
				parent::$_response->addVar('form_segment'		, $segmentFilter);
				parent::$_response->addVar('form_etagere'		, $etagereFilter);
				parent::$_response->addVar('form_page'			, $page);
			}
			else{ 
				$nb					= count(Produit::loadAll(parent::$_pdo,null, null, null, null, null, null, null, null, null, 'geoloc'));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$produits = Produit::loadAll(parent::$_pdo,''.$first, null, null, null, null, null, null, null, null, 'geoloc');
				parent::$_response->addVar('form_etage'	, '');
				parent::$_response->addVar('form_page' 	, $page);
			}
			
			$listeProduits = array();
			
			foreach($produits as $produit){
				$etageres			= $produit->selectEtageres();
				if($etageres != null){
					foreach($etageres as $etagere){
						$segment			= $etagere->getSegment();
						$rayon 				= $segment->getRayon();
						$seg				= $segment->getPosition(parent::$_pdo, $segment->getIdsegment(), $rayon->getIdrayon());
						$eta				= $etagere->getPosition(parent::$_pdo, $etagere->getIdetagere(), $segment->getIdsegment());
						$zone				= $rayon->getZone();
						$etage				= $zone->getEtage();
						$listeProduits[] 	= array($produit,$eta,$seg,$rayon,$zone->getLibelle(),$etage->getLibelle());
					}
				}
			}
			
			/* Affectation des priorités */
			if(parent::$_request->getVar('submitPriorite')){	// Clic sur le bouton affecter
				
				$produitsAffectes 		= parent::$_request->getVar('Produit');					// Tableau des idProduit à affecter
				$priorite   			= (int) parent::$_request->getVar('choixPriorite');		// La priorité à affecter
				if ($priorite == '') $priorite = null;
				$choixFinessePriorite	= parent::$_request->getVar('choixFinessePriorite');	// Existe si on choisi de ne pas affecter la priorité aux produits mais à la finesse choisie dans le filtre
				$idFinessePriorite		= parent::$_request->getVar('idFinessePriorite');
				$log = '';
				if (isset($idFinessePriorite)) {
					if(isset($choixFinessePriorite)){
						switch($choixFinessePriorite){
							case 'all':
								Produit::setAllPriorite(parent::$_pdo, $priorite);
								$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la priorit&eacute; '). gettext('de tous les produits') .gettext(' par ') . $_SESSION['user_login']."\r\n";
								break;
								
							case 'etage':
								$etage 	= Etage::load(parent::$_pdo, $idFinessePriorite);
								if ($etage != null){
									$etage->setPriorite($priorite);
									$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la priorit&eacute; '). gettext('de l\'&eacute;tage ') . $etage->getIdetage() . ' - "' . $etage->getLibelle() . '"' .gettext(' par ') . $_SESSION['user_login']."\r\n";

								}
								else{
									throw new Exception(gettext('Param&egrave;tre invalide : choix finesse -> etage'),5);
								}
								break;
								
							case 'zone':
								$zone 	= Zone::load(parent::$_pdo, $idFinessePriorite);
								if ($zone != null){
									$zone->setPriorite($priorite);
									$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la priorit&eacute; '). gettext('de la zone ') . $zone->getIdzone() . ' - "' . $zone->getLibelle() . '"' .gettext(' par ') . $_SESSION['user_login']."\r\n";
								}
								else{
									throw new Exception(gettext('Param&egrave;tre invalide : choix finesse -> zone'),5);
								}
								break;
								
							case 'rayon':
								$rayon	= Rayon::load(parent::$_pdo, $idFinessePriorite);
								if ($rayon != null){
									$rayon->setPriorite($priorite);
									$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la priorit&eacute; '). gettext('du rayon ') . $rayon->getIdrayon() . ' - "' . $rayon->getLibelle() . '"' .gettext(' par ') . $_SESSION['user_login']."\r\n";
								}
								else{
									throw new Exception(gettext('Param&egrave;tre invalide : choix finesse -> rayon'),5);
								}
								break;
								
							case 'segment':
								$segment = Segment::load(parent::$_pdo, $idFinessePriorite);
								if ($segment != null){
									$segment->setPriorite($priorite);
									$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la priorit&eacute; '). gettext('du segment ') . $segment->getIdsegment() . gettext(' par ') . $_SESSION['user_login']."\r\n";
								}
								else{
									throw new Exception(gettext('Param&egrave;tre invalide : choix finesse -> segment'),5);
								}
								break;
								
							case 'etagere':
								$etagere = Etagere::load(parent::$_pdo, $idFinessePriorite);
								if ($etagere != null){
									$etagere->setPriorite($priorite);
									$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la priorit&eacute; '). gettext('de l\'etagere ') . $etagere->getIdetagere() . gettext(' par ') . $_SESSION['user_login']."\r\n";
								}
								else{
									throw new Exception(gettext('Param&egrave;tre invalide : choix finesse -> etagere'),5);
								}
								break;
								
							default:
								break;
						}
						$logFile = '../application/logs/'.date('m-Y').'-produit.log';
						writeLog($log, $logFile);
					}
				}
				else{
					if ($produitsAffectes != null) {
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la priorit&eacute; '). gettext('des produits ');
						foreach($produitsAffectes as $produit){
							$produitAffecte = Produit::load(parent::$_pdo, $produit);					
							if($produitAffecte == null){
								throw new Exception(gettext('Param&egrave;tre invalide'),5);	
							}
							if (Produit::testIntegrite('Priorite', $priorite)){
								$produitAffecte->setPriorite($priorite,true);
								$log .= $produitAffecte->getIdProduit() . ', ';
							}
						}
						$log = substr($log, 0, -2); 
						$log .= gettext(' par ') . $_SESSION['user_login']."\r\n";	
						$logFile = '../application/logs/'.date('m-Y').'-produit.log';
						writeLog($log, $logFile);
					}
				}
								
				header('Location: '. APPLICATION_PATH . 'produit/priorite');
			}
			
			
			parent::$_response->addVar('nb_resultats'		, $nb);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('arrayEtages'	, Etage::loadAll(parent::$_pdo,true)); 
			parent::$_response->addVar('arrayProduits'	, $listeProduits);
			parent::$_response->addVar('nombre_de_pages', $nombre_de_pages);
			parent::$_response->addVar('page'			, $page);
			parent::$_response->addVar('txt_page'		, gettext('Page'));
		}
	}
	
	public static function nongeolocalise(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			parent::$_response->addVar('txt_produitsNonGeoloc'	, gettext('Liste des produits non g&eacute;olocalis&eacute;s'));
			parent::$_response->addVar('txt_filtre'				, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'			, gettext('Filtrer'));	
			parent::$_response->addVar('txt_code_produit'		, gettext('Code produit'));			
			parent::$_response->addVar('txt_code_ean'			, gettext('Code Ean'));			
			parent::$_response->addVar('txt_libelle'			, gettext('Libell&eacute; du produit'));	
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));
			parent::$_response->addVar('txt_effacer'			, gettext('Effacer'));
			parent::$_response->addVar('txt_noResults'			, gettext('Aucun produit non g&eacute;olocalis&eacute; n\'a &eacute;t&eacute; trouv&eacute;'));
			parent::$_response->addVar('txt_consulter_produit'	, gettext('Consulter le produit'));
			parent::$_response->addVar('txt_page'				, gettext('Page'));
			
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			$codeFilter 	= parent::$_request->getVar('codeFilter');
			$eanFilter 		= parent::$_request->getVar('eanFilter');
			$pageFilter		= parent::$_request->getVar('pageFilter');
			
			$page 			= 1;
			
			if (parent::$_request->getVar('submitFilter')){	// Clic sur le bouton filtrer
				$page = $pageFilter; 
			
				parent::$_response->addVar('form_libelleFilter'	, $libelleFilter);
				parent::$_response->addVar('form_codeFilter'	, $codeFilter);
				parent::$_response->addVar('form_eanFilter'		, $eanFilter);
				parent::$_response->addVar('form_page'			, $page);
				
				$nb					= count(Produit::loadAll(parent::$_pdo, null, null, null, null, null, null, $libelleFilter, $codeFilter, $eanFilter, 'nonGeoloc'));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$produits = Produit::loadAll(parent::$_pdo, '' . $first , null, null, null, null, null, $libelleFilter, $codeFilter, $eanFilter, 'nonGeoloc');
			}
			else{
				parent::$_response->addVar('form_page'	, $page);
				$nb					= count(Produit::loadAll(parent::$_pdo, null, null, null, null, null, null, null, null, null, 'nonGeoloc'));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$produits = Produit::loadAll(parent::$_pdo, '' . $first, null, null, null, null, null, null, null, null, 'nonGeoloc');
			}
			
			
			$listeProduits 	= array();
			
			foreach($produits as $produit){
				if (!Geolocalisation::isGeolocalized(parent::$_pdo,$produit->getIdProduit()))
					$listeProduits[] = $produit;
			}
			parent::$_response->addVar('nb_resultats'		, $nb);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('arrayProduits'	, $listeProduits);
			parent::$_response->addVar('nombre_de_pages', $nombre_de_pages);
		}
	}
	
	public static function inconnu(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			parent::$_response->addVar('txt_titre'				, gettext('Listes des produits g&eacute;olocalis&eacute;s mais inconnus'));	
			parent::$_response->addVar('txt_filtre'				, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'			, gettext('Filtrer'));				
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));	
			parent::$_response->addVar('txt_effacer'			, gettext('Effacer'));	
			parent::$_response->addVar('txt_code_produit'		, gettext('Code produit'));			
			parent::$_response->addVar('txt_code_ean'			, gettext('Code Ean'));			
			parent::$_response->addVar('txt_libelle'			, gettext('Libell&eacute; du produit'));			
			parent::$_response->addVar('txt_etage'				, gettext('Etage'));	
			parent::$_response->addVar('txt_zone'				, gettext('Zone'));	
			parent::$_response->addVar('txt_rayon'				, gettext('Rayon'));
			parent::$_response->addVar('txt_segment'			, gettext('Segment'));
			parent::$_response->addVar('txt_etagere'			, gettext('Etag&egrave;re'));
			parent::$_response->addVar('txt_noResults'			, gettext('Aucun produit inconnu n\'a &eacute;t&eacute; trouv&eacute;'));
			parent::$_response->addVar('txt_consulter_produit'	, gettext('Consulter le produit'));
			parent::$_response->addVar('txt_page'				, gettext('Page'));
		
			
			$libelleFilter	= 'Produit inconnu';
			$codeFilter 	= parent::$_request->getVar('codeFilter');
			$eanFilter 		= parent::$_request->getVar('eanFilter');
			$etageFilter 	= parent::$_request->getVar('etageFilter');
			$zoneFilter 	= parent::$_request->getVar('zoneFilter');
			$rayonFilter 	= parent::$_request->getVar('rayonFilter');
			$segmentFilter 	= parent::$_request->getVar('segmentFilter');
			$etagereFilter 	= parent::$_request->getVar('etagereFilter');
			$pageFilter 	= parent::$_request->getVar('pageFilter');
			
			$page 			= 1;
			
			if (parent::$_request->getVar('submitFilter')){	// Clic sur le bouton filtrer
				
				$page = $pageFilter;
				
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
										$Segments = Segment::loadAllRayon(parent::$_pdo, $rayonFilter);
										foreach($Segments as $seg){
											$arraySegments[] = array($seg, $seg->getPosition(parent::$_pdo, $seg->getIdsegment(), $rayon->getIdrayon()));
										}
										
										if($segmentFilter != null){
											$segment = Segment::load(parent::$_pdo, $segmentFilter);
											if($segment != null){
												$Etageres = Etagere::loadAllSegment(parent::$_pdo, $segmentFilter);
												foreach($Etageres as $eta){
													$arrayEtageres[] = array($eta, $eta->getPosition(parent::$_pdo, $eta->getIdetagere(), $segment->getIdsegment()));
												}
												
												if($etagereFilter != null){
													$etagere	= Etagere::load(parent::$_pdo, $etagereFilter);
													if($etagere == null){
														throw new Exception(gettext('Param&egrave;tre invalide'),5);
													}
												}
												parent::$_response->addVar('arrayEtageres' , $arrayEtageres);
											}
											else{
												throw new Exception(gettext('Param&egrave;tre invalide'),5);
											}
										}
										parent::$_response->addVar('arraySegments' , $arraySegments);
									}
									else{
										throw new Exception(gettext('Param&egrave;tre invalide'),5);
									}
								}
								parent::$_response->addVar('arrayRayons' , $arrayRayons);
							}
							else{
								throw new Exception(gettext('Param&egrave;tre invalide'),5);
							}
						}
						parent::$_response->addVar('arrayZones'	, $arrayZones);
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
				}
				$nb					= count(Produit::loadAll(parent::$_pdo, null, $etageFilter,$zoneFilter,$rayonFilter,$segmentFilter,$etagereFilter, $libelleFilter, $codeFilter, $eanFilter, 'inconnu'));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$produits = Produit::loadAll(parent::$_pdo, '' . $first, $etageFilter,$zoneFilter,$rayonFilter,$segmentFilter,$etagereFilter, $libelleFilter, $codeFilter, $eanFilter, 'inconnu');
				
				parent::$_response->addVar('form_codeFilter'	, $codeFilter);
				parent::$_response->addVar('form_eanFilter'		, $eanFilter);
				parent::$_response->addVar('form_etage'			, $etageFilter);
				parent::$_response->addVar('form_zone'			, $zoneFilter);
				parent::$_response->addVar('form_rayon'			, $rayonFilter);
				parent::$_response->addVar('form_segment'		, $segmentFilter);
				parent::$_response->addVar('form_etagere'		, $etagereFilter);
				parent::$_response->addVar('form_page'			, $page);
			}
			else{
			
				$nb					= count(Produit::loadAll(parent::$_pdo, null, null, null, null, null, null, null, null, null, 'inconnu'));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$produits = Produit::loadAll(parent::$_pdo, '' . $first, null, null, null, null, null, null, null, null, 'inconnu');
				
				parent::$_response->addVar('form_etage'	, '');
				parent::$_response->addVar('form_page'	, $page);
			}

				
			$listeProduits 	= array();
			foreach($produits as $produit){
				$etageres	= $produit->selectEtageres();
				if($etageres != null){
					foreach($etageres as $etagere){
						$segment			= $etagere->getSegment();
						$rayon 				= $segment->getRayon();
						$seg				= $segment->getPosition(parent::$_pdo, $segment->getIdsegment(), $rayon->getIdrayon());
						$eta				= $etagere->getPosition(parent::$_pdo, $etagere->getIdetagere(), $segment->getIdsegment());
						$zone				= $rayon->getZone();
						$etage				= $zone->getEtage();
						
						$ok = true;
						
						if($etageFilter != null && $etageFilter != $etage->getIdetage())
							$ok = false;
						
						else if($zoneFilter != null && $zoneFilter != $zone->getIdzone())
							$ok = false;
									
						else if($rayonFilter != null && $rayonFilter != $rayon->getIdrayon())
							$ok = false;										
											
						else if($segmentFilter != null && $segmentFilter != $segment->getIdsegment())
							$ok = false;
							
						else if($etagereFilter != null && $etagereFilter != $etagere->getIdetagere())
							$ok = false;							
						
						if($ok)
							$listeProduits[] 	= array($produit,$eta,$seg,$rayon->getLibelle(),$zone->getLibelle(),$etage->getLibelle());
					}
				}
				else{
					$listeProduits[] = array($produit,gettext('non d&eacute;fini'), gettext('non d&eacute;fini'), gettext('non d&eacute;fini'), gettext('non d&eacute;fini') , gettext('non d&eacute;fini'));
				}
			}
			parent::$_response->addVar('nb_resultats'		, $nb);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('arrayEtages'	, Etage::loadAll(parent::$_pdo));			
			parent::$_response->addVar('arrayProduits'	, $listeProduits);
			parent::$_response->addVar('nombre_de_pages', $nombre_de_pages);
		}
	}
	
	public static function sanscodeean(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			parent::$_response->addVar('txt_titre'				, gettext('Listes des produits sans code Ean'));			
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));			
			parent::$_response->addVar('txt_filtre'				, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'			, gettext('Filtrer'));	
			parent::$_response->addVar('txt_effacer'			, gettext('Effacer'));	
			parent::$_response->addVar('txt_libelle'			, gettext('Libelle du produit'));			
			parent::$_response->addVar('txt_code_produit'		, gettext('Code produit'));			
			parent::$_response->addVar('txt_code_ean'			, gettext('Code Ean'));			
			parent::$_response->addVar('txt_libelle'			, gettext('Libell&eacute; du produit'));			
			parent::$_response->addVar('txt_etage'				, gettext('Etage'));	
			parent::$_response->addVar('txt_zone'				, gettext('Zone'));	
			parent::$_response->addVar('txt_rayon'				, gettext('Rayon'));
			parent::$_response->addVar('txt_segment'			, gettext('Segment'));
			parent::$_response->addVar('txt_etagere'			, gettext('Etag&egrave;re'));
			parent::$_response->addVar('txt_noResults'			, gettext('Aucun produit sans code Ean n\'a &eacute;t&eacute; trouv&eacute;'));
			parent::$_response->addVar('txt_consulter_produit'	, gettext('Consulter le produit'));
			parent::$_response->addVar('txt_page'				, gettext('Page'));
			
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			$codeFilter 	= parent::$_request->getVar('codeFilter');
			$eanFilter 		= parent::$_request->getVar('eanFilter');
			$etageFilter 	= parent::$_request->getVar('etageFilter');
			$zoneFilter 	= parent::$_request->getVar('zoneFilter');
			$rayonFilter 	= parent::$_request->getVar('rayonFilter');
			$segmentFilter 	= parent::$_request->getVar('segmentFilter');
			$etagereFilter 	= parent::$_request->getVar('etagereFilter');
			$pageFilter 	= parent::$_request->getVar('pageFilter');
			
			$page 			= 1;
			
			if (parent::$_request->getVar('submitFilter')){	// Clic sur le bouton filtrer
				
				$page = $pageFilter;
				
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
										$Segments = Segment::loadAllRayon(parent::$_pdo, $rayonFilter);
										foreach($Segments as $seg){
											$arraySegments[] = array($seg, $seg->getPosition(parent::$_pdo, $seg->getIdsegment(), $rayon->getIdrayon()));
										}
										
										if($segmentFilter != null){
											$segment = Segment::load(parent::$_pdo, $segmentFilter);
											if($segment != null){
												$Etageres = Etagere::loadAllSegment(parent::$_pdo, $segmentFilter);
												foreach($Etageres as $eta){
													$arrayEtageres[] = array($eta, $eta->getPosition(parent::$_pdo, $eta->getIdetagere(), $segment->getIdsegment()));
												}
												
												if($etagereFilter != null){
													$etagere	= Etagere::load(parent::$_pdo, $etagereFilter);
													if($etagere == null){
														throw new Exception(gettext('Param&egrave;tre invalide'),5);
													}
												}
												parent::$_response->addVar('arrayEtageres' , $arrayEtageres);
											}
											else{
												throw new Exception(gettext('Param&egrave;tre invalide'),5);
											}
										}
										parent::$_response->addVar('arraySegments' , $arraySegments);
									}
									else{
										throw new Exception(gettext('Param&egrave;tre invalide'),5);
									}
								}
								parent::$_response->addVar('arrayRayons' , $arrayRayons);
							}
							else{
								throw new Exception(gettext('Param&egrave;tre invalide'),5);
							}
						}
						parent::$_response->addVar('arrayZones'	, $arrayZones);	
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
				}
				
				$nb					= count(Produit::loadAll(parent::$_pdo, null, $etageFilter,$zoneFilter,$rayonFilter,$segmentFilter,$etagereFilter, $libelleFilter, $codeFilter, $eanFilter, 'sansean'));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1) * RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1) * RESULTAT_PAR_PAGE);
				$produits = Produit::loadAll(parent::$_pdo, '' . $first, $etageFilter,$zoneFilter,$rayonFilter,$segmentFilter,$etagereFilter, $libelleFilter, $codeFilter, $eanFilter, 'sansean');
				
				parent::$_response->addVar('form_libelleFilter'	, $libelleFilter);
				parent::$_response->addVar('form_codeFilter'	, $codeFilter);
				parent::$_response->addVar('form_eanFilter'		, $eanFilter);
				parent::$_response->addVar('form_etage'			, $etageFilter);
				parent::$_response->addVar('form_zone'			, $zoneFilter);
				parent::$_response->addVar('form_rayon'			, $rayonFilter);
				parent::$_response->addVar('form_segment'		, $segmentFilter);
				parent::$_response->addVar('form_etagere'		, $etagereFilter);
				parent::$_response->addVar('form_page'			, $page);
			}
			else{
				$nb					= count(Produit::loadAll(parent::$_pdo, null, null, null, null, null, null, null, null, null, 'sansean'));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$produits = Produit::loadAll(parent::$_pdo, '' . $first, null, null, null, null, null, null, null, null, 'sansean');
				
				parent::$_response->addVar('form_etage'	, '');
				parent::$_response->addVar('form_page'	, $page);
			}
			
			$listeProduits = array();
			foreach($produits as $produit){

				$etageres	= $produit->selectEtageres();
				if($etageres != null){
					foreach($etageres as $etagere){
						$segment			= $etagere->getSegment();
						$rayon 				= $segment->getRayon();
						$seg				= $segment->getPosition(parent::$_pdo, $segment->getIdsegment(), $rayon->getIdrayon());
						$eta				= $etagere->getPosition(parent::$_pdo, $etagere->getIdetagere(), $segment->getIdsegment());
						$zone				= $rayon->getZone();
						$etage				= $zone->getEtage();
						$listeProduits[] 	= array($produit,$eta,$seg,$rayon->getLibelle(),$zone->getLibelle(),$etage->getLibelle());
					}
				}
				else{
					$listeProduits[] = array($produit, gettext('non d&eacute;fini'), gettext('non d&eacute;fini'), gettext('non d&eacute;fini'), gettext('non d&eacute;fini') , gettext('non d&eacute;fini'));
				}
				
			}
			parent::$_response->addVar('nb_resultats'		, $nb);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('arrayEtages'	, Etage::loadAll(parent::$_pdo));
			parent::$_response->addVar('arrayProduits'	, $listeProduits);
			parent::$_response->addVar('nombre_de_pages', $nombre_de_pages);
		}
	}
	
	
	public static function supprimer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			$idProduit = parent::$_request->getVar('id');
			$produit = Produit::load(parent::$_pdo, $idProduit);	// Chargement de l'objet produit
			if($produit == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
			$ligne_commandes = $produit->selectLigne_commandes();
			if(count($ligne_commandes) > 0)
				parent::$_response->addVar('txt_erreur', gettext('Le produit apparait au moins dans une commande, il n\'est donc pas possible de le supprimer.'));
			else{
				if(Geolocalisation::isGeolocalized(parent::$_pdo,$idProduit))
					Divers::setChangementLocalisation(parent::$_pdo,1);
				$produit->delete();
				parent::$_response->addVar('txt_supprime', gettext('Le produit a bien &eacute;t&eacute; supprim&eacute;'));
				
				$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Suppression du produit '). $idProduit .gettext(' par ') .$_SESSION['user_login']."\r\n";
				$logFile = '../application/logs/'.date('m-Y').'-produit.log';
				writeLog($log, $logFile);
			}
			parent::$_response->addVar('txt_retour', gettext('Retour &agrave; la liste des produits'));

		}	
	}
		
}



