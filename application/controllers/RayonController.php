<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * RayonController.php
 *
 * Cette classe permet de récupérer des informations sur les rayons
 *
 */

class RayonController extends BaseController {

/* 
	public static function trime(){
		$i = 0;
		foreach(Rayon::loadAll(parent::$_pdo) as $rayon){
			$i++;
			$rayon->setLibelle(trim($rayon->getLibelle()));
		}
		
		die($i . ' rayon(s) modifié(s)');
	} */

	public static function index(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
				
			parent::$_response->addVar('txt_titre'			, gettext('Rayons'));
			parent::$_response->addVar('txt_rayons'			, gettext('rayons'));		
			parent::$_response->addVar('txt_rayon'			, gettext('rayon'));		
			parent::$_response->addVar('txt_segments'		, gettext('segments'));		
			parent::$_response->addVar('txt_segment'		, gettext('segment'));			
			parent::$_response->addVar('txt_libelle_rayon'	, gettext('Libell&eacute; du rayon'));			
			parent::$_response->addVar('txt_etage'			, gettext('Etage'));			
			parent::$_response->addVar('txt_zone'			, gettext('Zone'));			
			parent::$_response->addVar('txt_consulter_rayon', gettext('Consulter le rayon'));			
			parent::$_response->addVar('txt_nb_segments'	, gettext('Nombre de segments'));			
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));			
			parent::$_response->addVar('txt_editer'			, gettext('Editer la zone'));
			parent::$_response->addVar('txt_importer'		, gettext('Importer des rayons'));
			parent::$_response->addVar('txt_exporter'		, gettext('Exporter les rayons'));
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));	
			
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			$etageFilter 	= parent::$_request->getVar('etageFilter');
			$zoneFilter 	= parent::$_request->getVar('zoneFilter');
			$pageFilter 	= parent::$_request->getVar('pageFilter');
			$arrayZones 	= array();
			$page			= 1;  
 
				 
			if (parent::$_request->getVar('submitFilter')){	// Clic sur le bouton filtrer
				$page = $pageFilter;
				
				if($etageFilter != null){
					$etage = Etage::load(parent::$_pdo, $etageFilter);
					if ($etage != null){
						$arrayZones = Zone::loadAllEtage(parent::$_pdo, $etageFilter);
						if($zoneFilter != null){
							$zone = Zone::load(parent::$_pdo, $zoneFilter);
							if ($zone == null){
								throw new Exception(gettext('Param&egrave;tre invalide'),5);
							}
						}
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
				}
				 
				$nb					= count(Rayon::loadAll(parent::$_pdo,null, $etageFilter, $zoneFilter, $libelleFilter));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);	
				$rayons = Rayon::loadAll(parent::$_pdo,''.$first, $etageFilter, $zoneFilter, $libelleFilter);
				
				parent::$_response->addVar('form_libelleFilter'	, $libelleFilter);
				parent::$_response->addVar('form_etage'			, $etageFilter);
				parent::$_response->addVar('form_zone'			, $zoneFilter);
				parent::$_response->addVar('form_page'			, $page);
			}
			else{
				parent::$_response->addVar('form_libelleFilter'	, '');
				parent::$_response->addVar('form_etage'			, '');
				parent::$_response->addVar('form_zone'			, '');
				parent::$_response->addVar('form_page'			, $page);
				
				$nb					= count(Rayon::loadAll(parent::$_pdo));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$rayons = Rayon::loadAll(parent::$_pdo,''.$first);
				
			}
			
			$arrayRayons 	= array();
			foreach($rayons as $rayon){
				$arraySegments 	= Segment::selectByRayon(parent::$_pdo,$rayon);	
				$zone 			= $rayon->getZone();
				$etage			= $zone->getEtage();
				$arrayRayons[] 	= array($rayon,$etage,$zone,count($arraySegments)); 
			}
			
			parent::$_response->addVar('nb_resultats'		, $nb);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('arrayEtages'		, Etage::loadAll(parent::$_pdo,true)); 			
			parent::$_response->addVar('arrayZones'			, $arrayZones); 			
			parent::$_response->addVar('arrayRayons'		, $arrayRayons);
			parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
			parent::$_response->addVar('page'				, $page);
			parent::$_response->addVar('txt_page'			, gettext('Page'));
		}	
	}	

	/* Action afficher
	 * 
	 * Récupère et affiche les informations d'un rayon
	 *
	 */
	public static function afficher(){
	
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	
			parent::$_response->addVar('txt_retour'			, gettext('Retour'));
			parent::$_response->addVar('txt_Libelle'			, gettext('Libelle'));
			parent::$_response->addVar('txt_codeEan'			, gettext('Code EAN'));
			parent::$_response->addVar('txt_consulter_produit'	, gettext('Consulter le produit'));
			parent::$_response->addVar('txt_help'				, gettext('Aide'));
			parent::$_response->addVar('txt_help_titre'			, gettext('Aide affichage du rayon'));
			parent::$_response->addVar('txt_help_contenu'		, gettext('Aide affichage du rayon'));
			
			$idrayon 		= (int) parent::$_request->getVar('rayon');
			$idsegment 		= (int) parent::$_request->getVar('segment');
			$idetagere 		= (int) parent::$_request->getVar('etagere');
			$idRayonAjax 	= (int) parent::$_request->getVar('id');
			
			if (parent::$_request->getVar('appel_ajax')){
				/* Si l'appel de l'action provient d'un appel ajax (bouton info rayon dans la modélisation */
				$rayon					= Rayon::load(parent::$_pdo, $idRayonAjax);
				$listeProduits 			= Produit::loadAll(parent::$_pdo, null, null, null, $idRayonAjax, null, null, null, null, null, 'connu', null, true);
				$listeProduitsInconnus 	= Produit::loadAll(parent::$_pdo, null, null, null, $idRayonAjax, null, null, null, null, null, 'inconnu', null, true);
				
				/* Génération de 5 produits au hasard se trouvant dans le  rayon (pour l'appel ajax) */
				$nb = count($listeProduits);
				$arrayRandom = array();	
				if($nb > 0) {			
					if($nb < 5)
						$nbProduits = $nb;
					else
						$nbProduits = 5;
					
					$RandomKeys = array_rand($listeProduits, $nbProduits);
					if(count($RandomKeys) > 1)		
						foreach ($RandomKeys as $produit){			
							$arrayRandom[] = $listeProduits[$produit];			
						}
					else
						$arrayRandom[] = $listeProduits[0];		
				}
				
				parent::$_response->addVar('txt_rayon'		, gettext('Rayon'));
				parent::$_response->addVar('txt_1'			, utf8_encode(gettext('Ce produit appartient &agrave; la zone ')));
				parent::$_response->addVar('txt_2'			, utf8_encode(gettext('et poss&egrave;de ')));
				parent::$_response->addVar('txt_segment'	, gettext('segment'));
				parent::$_response->addVar('txt_segments'	, gettext('segments'));
				parent::$_response->addVar('txt_3'			, gettext('Il contient'));
				parent::$_response->addVar('txt_produit'	, gettext('produit'));
				parent::$_response->addVar('txt_produits'	, gettext('produits'));
				parent::$_response->addVar('txt_dont'		, gettext('dont'));
				parent::$_response->addVar('txt_sont'		, gettext('sont'));
				parent::$_response->addVar('txt_est'		, gettext('est'));
				parent::$_response->addVar('txt_inconnu'	, gettext('inconnu'));
				parent::$_response->addVar('txt_inconnus'	, gettext('inconnus'));
				parent::$_response->addVar('txt_4'			, utf8_encode(gettext('Quelques produits pr&eacute;sents dans ce rayon : ')));
				parent::$_response->addVar('txt_annuler'	, utf8_encode(gettext('Annuler la mod&eacute;lisation de ce rayon')));
				parent::$_response->addVar('txt_fermer'		, gettext('Fermer'));
				
				parent::$_response->addVar('rayon'			, $rayon);
				parent::$_response->addVar('arrayRandom'	, $arrayRandom);
				parent::$_response->addVar('nbSegments'		, count($rayon->selectSegments()));
				parent::$_response->addVar('nbPdts'			, count($listeProduitsInconnus) + count($listeProduits));
				parent::$_response->addVar('nbPdtsInconnus'	, count($listeProduitsInconnus));
				parent::$_response->setType('ajax');
				return true;
			}
			else{
				$rayon			= Rayon::load(parent::$_pdo, $idrayon);	// Le rayon à afficher 
				if($rayon == null)
					throw new Exception(gettext('Param&egrave;tre invalide'),5);
				$listeSegments 	= $rayon->selectSegments();				// Liste des segments que contient ce rayon
				$arraySegments  = array();								// Tableau contenant les infos des segments
				$listeProduits	= array();								// Liste des produits à afficher
				foreach($listeSegments as $segment){
					/* Converstion du numéro de segment en lettre */
					$lettre = $segment->getPosition(parent::$_pdo, $segment->getIdsegment(), $rayon->getIdrayon());
					/* Liste des étagères que contient ce segment */
					$listeEtageres = $segment->selectEtageres();
					/* Tableau contenant dans chaque case : [0] -> objet Segment; [1] -> la lettre correspondante au segment; [2] -> tableau contenant les étagères que contient ce segment */
					$arraySegments[] = array($segment, $lettre, $listeEtageres);
				}
				
				/* Récupération des produits */
				if ($idsegment != 0){
					if ($idetagere !=0){
						/* Afficher les produits d'une étagère */
						$etagere	= Etagere::load(parent::$_pdo, $idetagere);				
						if($etagere == null)
							throw new Exception(gettext('Param&egrave;tre invalide'),5);
						$produits	= $etagere->selectProduits();
						foreach($produits as $produit){
							$eans			 	= Ean::loadByProduit(parent::$_pdo, $produit->getIdProduit());	
							$listeProduits[] 	= array($produit,$eans);
						}
					}
					else{
						/* Afficher les produits d'un segment */
						$segment 		= Segment::load(parent::$_pdo, $idsegment);			
						if($segment == null)
							throw new Exception(gettext('Param&egrave;tre invalide'),5);
						$listeEtageres	= $segment->selectEtageres();
						foreach($listeEtageres as $etagere){
							$produits	= $etagere->selectProduits();
							foreach($produits as $produit){
								$eans				= Ean::loadByProduit(parent::$_pdo, $produit->getIdProduit());	
								$listeProduits[] 	= array($produit,$eans);
							}
						}
					}
				}
				else{
					/* Afficher les produits d'un rayon */
					foreach($listeSegments as $segment){
						$listeEtageres = $segment->selectEtageres();
						foreach($listeEtageres as $etagere){
							$produits	= $etagere->selectProduits();
							foreach($produits as $produit){
								$eans				= Ean::loadByProduit(parent::$_pdo, $produit->getIdProduit());	
								$listeProduits[] 	= array($produit,$eans);
							}
						}
					}
				}
				
				parent::$_response->addVar('txt_titre'				, gettext('D&eacute;tails du rayon : ') . $rayon->getLibelle());
				
				parent::$_response->addVar('arrayProduits'		, $listeProduits);
				parent::$_response->addVar('rayon'				, $rayon);
				parent::$_response->addVar('arraySegments'		, $arraySegments);
				parent::$_response->addVar('nbSegments'			, count($arraySegments));
				
				parent::$_response->addVar('idrayonchoisi'		, $idrayon);
				parent::$_response->addVar('idsegmentchoisi'	, $idsegment);
				parent::$_response->addVar('idetagerechoisie'	, $idetagere);
			}
		}
	}
	
	/* Action editer
	 * 
	 * Change les informations d'un rayon
	 *
	 */	
	public static function editer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			parent::$_response->addVar('txt_Libelle'			, gettext('Libelle'));
			parent::$_response->addVar('txt_localisation'		, gettext('Localisation'));
			parent::$_response->addVar('txt_priorite'			, gettext('Priorit&eacute;'));
			parent::$_response->addVar('txt_Edition'			, gettext('Edition du rayon'));
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));
			parent::$_response->addVar('txt_actuel'				, gettext('actuel'));
			parent::$_response->addVar('txt_null'				, gettext('Sans priorit&eacute;'));
			parent::$_response->addVar('txt_debut'				, gettext('D&eacute;but'));
			parent::$_response->addVar('txt_normal'				, gettext('Normal'));
			parent::$_response->addVar('txt_fin'				, gettext('Fin'));
			parent::$_response->addVar('txt_infosRayon'			, gettext('Informations du rayon'));
			parent::$_response->addVar('txt_confirmSuppression'	, gettext('Voulez vous vraiment supprimer le rayon'));
			parent::$_response->addVar('txt_lienSupprimer'		, gettext('Supprimer le rayon'));
			
			$idRayon = parent::$_request->getVar('id');
			if(isset($idRayon))
				$rayonSelectionne = $idRayon;
			else
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
			if (parent::$_request->getVar('valider')){
					$idRayon = parent::$_request->getVar('idrayon');
					$libelle = parent::$_request->getVar('libelle');
					$position_x = parent::$_request->getVar('position_x');
					$position_y = parent::$_request->getVar('position_y');
					$largeur = parent::$_request->getVar('largeur');
					$hauteur = parent::$_request->getVar('hauteur');
					$rayon = Rayon::load(parent::$_pdo,$idRayon,true);
					
					$error_message = '';
					
					if(!Rayon::testIntegrite('libelle', $libelle)){
						$error_message .= gettext('Veuillez renseigner un libell&eacute; valide') . '<br />';
					}
					else {
						if(Rayon::libelleUsed(parent::$_pdo, $libelle,$rayon->getIdrayon()))
							/* Saisie d'un libellé déja utilisé */
							$error_message .= gettext('Ce libell&eacute; est d&eacute;j&agrave; utilis&eacute;, veuillez en choisir un autre') . '<br />';
					}
					
					if($error_message == ''){
						$rayon->setLibelle(html_entities($libelle),false);
						$rayon->setPosition_left($position_x,false);
						$rayon->setPosition_top($position_y,false);
						$rayon->setLargeur($largeur,false);
						$rayon->setHauteur($hauteur,false);
						$rayon->update();
						parent::$_response->addVar('form_libelle'			, $libelle);
						parent::$_response->addVar('form_position_x'		, $position_x); 
						parent::$_response->addVar('form_position_y'		, $position_y);
						parent::$_response->addVar('form_largeur'			, $largeur);
						parent::$_response->addVar('form_hauteur'			, $hauteur);
						
					}
					else{	
						/* Assigner les différentes varibales pour le template */
						parent::$_response->addVar('form_errors'			, $error_message); 
						parent::$_response->addVar('form_idRayon'			, $rayon->getIdrayon()); 
						parent::$_response->addVar('form_libelle'			, $libelle); 
						parent::$_response->addVar('form_position_x'		, $position_x); 
						parent::$_response->addVar('form_position_y'		, $position_y); 
						parent::$_response->addVar('form_largeur'			, $largeur);
						parent::$_response->addVar('form_hauteur'			, $hauteur);
					}
					parent::$_response->setType('ajax');
					return true;

			}	
				
			if (parent::$_request->getVar('appel_ajax')){
				
					$rayon = Rayon::load(parent::$_pdo,$rayonSelectionne,true);
					if($rayon == null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					
					$error_message = '';
						
					parent::$_response->addVar('txt_rayon'				, gettext('Rayon'));
					parent::$_response->addVar('txt_modifier'			, gettext('Modifier'));
					parent::$_response->addVar('txt_fermer'				, gettext('Fermer'));
					parent::$_response->addVar('txt_positionx'			, gettext('Position Left'));
					parent::$_response->addVar('txt_positiony'			, gettext('Position Top'));
					parent::$_response->addVar('txt_width'				, gettext('Largeur'));
					parent::$_response->addVar('txt_height'				, gettext('Hauteur'));
					
					parent::$_response->addVar('rayon', $rayon);
					parent::$_response->addVar('form_errors'			, $error_message); 
					parent::$_response->addVar('form_idRayon'			, $rayon->getIdrayon()); 
					parent::$_response->addVar('form_libelle'			, $rayon->getLibelle());
					parent::$_response->addVar('form_position_x'		, $rayon->getPosition_left());
					parent::$_response->addVar('form_position_y'		, $rayon->getPosition_top());
					parent::$_response->addVar('form_largeur'			, $rayon->getLargeur());
					parent::$_response->addVar('form_hauteur'			, $rayon->getHauteur());
					parent::$_response->setType('ajax');
					return true;
			}	
			else {
				$rayon = Rayon::load(parent::$_pdo,$rayonSelectionne,true);
				if($rayon == null)
					throw new Exception(gettext('Param&egrave;tre invalide'),5);
				parent::$_response->addVar('rayon', $rayon);
				
				$error_message = '';
					
				if(parent::$_request->getVar('submit')){	 // Clic sur le bouton modifier 
						
					$libelle = parent::$_request->getVar('libelle');
					if(!Rayon::testIntegrite('libelle', $libelle)){
						$error_message .= gettext('Veuillez renseigner un libell&eacute; valide') . '<br />';
					}
					else {
						if(Rayon::libelleUsed(parent::$_pdo, $libelle,$rayon->getIdrayon()))
							/* Saisie d'un libellé déja utilisé */
							$error_message .= gettext('Ce libell&eacute; est d&eacute;j&agrave; utilis&eacute;, veuillez en choisir un autre') . '<br />';
					}
					
					$localisation = parent::$_request->getVar('localisation');
					if($localisation != '' && $localisation != null && !Rayon::testIntegrite('Localisation', $localisation)){
						$error_message .= gettext('Veuillez renseigner une localisation valide') . '<br />';
					}
					
					$priorite = (int) parent::$_request->getVar('priorite');
					if(!Rayon::testIntegrite('Priorite', $priorite)){
						$error_message .= gettext('Veuillez renseigner une priorit&eacute; valide') . '<br />';
					}
					if ($priorite == '') $priorite = null;
					 
					if($error_message == ''){	
						$rayon->setLibelle(htmlentities($libelle),false);
						$rayon->setLocalisation($localisation, false);
						$rayon->setPriorite($priorite, false);
						$rayon->update();
						
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Edition du rayon '). $rayon->getIdrayon() . gettext(' par ') . $_SESSION['user_login']."\r\n";
						$logFile = '../application/logs/'.date('m-Y').'-rayon.log';
						writeLog($log, $logFile);
						
						header('Location: ' . APPLICATION_PATH . 'rayon/afficher/' . $rayon->getIdrayon() . '/0/0');	
					}
					else{	
						/* Assigner les différentes varibales pour le template */
						parent::$_response->addVar('form_errors'		, $error_message); 
						parent::$_response->addVar('form_idRayon'		, $rayon->getIdrayon()); 
						parent::$_response->addVar('form_libelle'		, $libelle); 
						parent::$_response->addVar('form_localisation'	, $localisation); 
						parent::$_response->addVar('form_priorite'		, $priorite); 
					}
				}
				else{
				
					parent::$_response->addVar('form_errors'		, $error_message); 
					parent::$_response->addVar('form_idRayon'		, $rayon->getIdrayon()); 
					parent::$_response->addVar('form_libelle'		, $rayon->getLibelle()); 
					parent::$_response->addVar('form_localisation'	, $rayon->getLocalisation()); 
					parent::$_response->addVar('form_priorite'		, $rayon->getPriorite()); 
				}
			}
		}
	}
	
	public static function creer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			parent::$_response->addVar('txt_titre'					, gettext('Cr&eacute;er un rayon'));
			parent::$_response->addVar('txt_etage'					, gettext('Etage'));
			parent::$_response->addVar('txt_libelle'				, gettext('Libell&eacute;')); 
			parent::$_response->addVar('txt_localisation'			, gettext('Localisation')); 
			parent::$_response->addVar('txt_type'					, gettext('Type du rayon')); 
			parent::$_response->addVar('txt_boutonCreer'			, gettext('Cr&eacute;er'));
			parent::$_response->addVar('txt_retour'					, gettext('Retour'));
			parent::$_response->addVar('txt_hauteur'				, gettext('Longueur')); 
			parent::$_response->addVar('txt_largeur'				, gettext('Profondeur')); 
			parent::$_response->addVar('arrayTypes'					, array(gettext('classique'),gettext('vrac'))); 
			
			parent::$_response->addVar('arrayEtages'				, Etage::loadAll(parent::$_pdo,true));	
 
			if(parent::$_request->getVar('submit')){ 
			 	
				$error_message = '';
				
				$idEtage = (int) parent::$_request->getVar('etage');
				if (!Rayon::testIntegrite('idEtage', $idEtage)){
					$error_message .= gettext('Etage n\'existe pas') . '<br />';
				}
				
				$type = html_entities(parent::$_request->getVar('type'));
				if(!Rayon::testIntegrite('type', $type)){
					$error_message .= gettext('Veuillez renseigner un type valide') . '<br />';
				}
				
				$libelle = html_entities(parent::$_request->getVar('libelle'),false);
				if(!Rayon::testIntegrite('libelle', $libelle)){
					$error_message .= gettext('Veuillez renseigner un libell&eacute; valide') . '<br />';
				}
				else {
					if(Rayon::libelleUsed(parent::$_pdo, $libelle))
						/* Saisie d'un libellé déja utilisé */
						$error_message .= gettext('Ce libell&eacute; est d&eacute;j&agrave; utilis&eacute;, veuillez en choisir un autre') . '<br />';
				}
				
				
				$hauteur = parent::$_request->getVar('hauteur');
				$largeur = parent::$_request->getVar('largeur');
				
				
				
				$localisation = parent::$_request->getVar('localisation');
				if($localisation != '' && $localisation != null && !Rayon::testIntegrite('Localisation', $localisation)){
					$error_message .= gettext('Veuillez renseigner une localisation valide') . '<br />';
				}
				
				if($error_message == ''){
						$zone 		= Etage::load(parent::$_pdo,$idEtage)->getZoneMagasin(); 
						$rayon 		= Rayon::create(parent::$_pdo,$zone,$libelle,'-1','-1',0,$hauteur,$largeur,$type,null,$localisation);
						$segment 	= Segment::create(parent::$_pdo,$rayon);
						$etagere	= Etagere::create(parent::$_pdo,$segment);
						
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Cr&eacute;ation du rayon '). $rayon->getIdrayon() . ' - "' . $libelle . '"'. gettext(' par ') . $_SESSION['user_login']."\r\n";
						$logFile = '../application/logs/'.date('m-Y').'-rayon.log';
						writeLog($log, $logFile);
					
						header('Location: '. APPLICATION_PATH . 'rayon');														// Redirection vers la page gestion des rayons
					
				}
				else{	
					/* Assigner les différentes varibales pour le template */
					parent::$_response->addVar('form_errors'		, $error_message);
					parent::$_response->addVar('form_etage'			, $idEtage);
					parent::$_response->addVar('form_libelle'		, $libelle); 
					parent::$_response->addVar('form_localisation'	, $localisation); 
					parent::$_response->addVar('form_type'			, $type); 
				}
			}
			else{
				
				parent::$_response->addVar('form_etage'	, Etage::getFirstId(parent::$_pdo));
				parent::$_response->addVar('form_libelle'		, ''); 
				parent::$_response->addVar('form_localisation'	, ''); 
				parent::$_response->addVar('form_type'			, ''); 
			}
		}
	}	
	
	public static function importer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			parent::$_response->addVar('txt_titre'					, gettext('Importer des rayons'));
			parent::$_response->addVar('txt_etage'					, gettext('Etage'));
			parent::$_response->addVar('txt_fichier'				, gettext('Fichier &agrave; importer'));
			parent::$_response->addVar('txt_boutonValider'			, gettext('Valider'));
			parent::$_response->addVar('txt_retour'					, gettext('Retour')); 
		
			parent::$_response->addVar('arrayEtages'				, Etage::loadAll(parent::$_pdo,true));
			
			if(parent::$_request->getVar('submit')){
				$error_message = '';
					
				$idEtage = (int) parent::$_request->getVar('etage');
				if (!Rayon::testIntegrite('idEtage', $idEtage)){
					$error_message .= gettext('Etage n\'existe pas') . '<br />';
				}
				
				if($_FILES['fichier']['error'] != 0){
					$error_message .= gettext('Le fichier sp&eacute;cifi&eacute; n\'est pas correct') . '<br />';
				}
				
				if($error_message == ''){
				
					$arrayRayon = array();
					$arrayLignes = file($_FILES['fichier']['tmp_name']);
					
					foreach ($arrayLignes as $ligne){
						$error = false;
						$arrayRayon = explode(';', $ligne);
						if(isset($arrayRayon[0]) && isset($arrayRayon[1])){
							$libelle = trim($arrayRayon[0]);
							$type = trim($arrayRayon[1]);
							if(isset($arrayRayon[2]))
								$localisation = trim($arrayRayon[2]);
							else
								$localisation = '';
								
							if(!Rayon::testIntegrite('type', $type))
								$error = true;
								
							if(!Rayon::testIntegrite('libelle', $libelle))
								$error = true;
							
							if(Rayon::libelleUsed(parent::$_pdo, $libelle))
								$error = true;
							
							if($localisation != '' && $localisation != null && !Rayon::testIntegrite('Localisation', $localisation))
								$error = true;
							
							if(!$error){	
								$zone 		= Etage::load(parent::$_pdo,$idEtage)->getZoneMagasin(); 
								$rayon 		= Rayon::create(parent::$_pdo,$zone,$libelle,'-1','-1',0,13,31,$type,null,$localisation);
								$segment 	= Segment::create(parent::$_pdo,$rayon);
								$etagere	= Etagere::create(parent::$_pdo,$segment);
							}
						}
					
					
					}
					
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Import des rayons par ') .$_SESSION['user_login']."\r\n";
					$logFile = '../application/logs/'.date('m-Y').'-rayon.log';
					writeLog($log, $logFile);
					
					parent::$_response->addVar('form_ok', gettext('L\'import s\'est d&eacute;roul&eacute;e correctement.'));
					parent::$_response->addVar('form_etage'			, $idEtage);
									
				}
				else{	
					/* Assigner les différentes varibales pour le template */
					parent::$_response->addVar('form_errors'		, $error_message);
					parent::$_response->addVar('form_etage'			, $idEtage);
				}
				
			}
			else{			
				parent::$_response->addVar('form_etage'	, Etage::getFirstId(parent::$_pdo));
			}
		}
	}
	
	public static function exporter(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			parent::$_response->addVar('form_etage'	, 				Etage::getFirstId(parent::$_pdo));
			parent::$_response->addVar('txt_titre'					, gettext('Exporter les rayons'));
			parent::$_response->addVar('txt_etage'					, gettext('Etage'));
			parent::$_response->addVar('txt_boutonValider'			, gettext('Valider'));
			parent::$_response->addVar('txt_retour'					, gettext('Retour')); 
		
			parent::$_response->addVar('arrayEtages'				, Etage::loadAll(parent::$_pdo,true));
		
			if(parent::$_request->getVar('submit')){
				$idEtage = (int) parent::$_request->getVar('etage');
				$rayons = Rayon::loadAll(parent::$_pdo, null, $idEtage);
				$fichier = '';
				foreach($rayons as $rayon){
					$fichier .= trim($rayon -> getLibelle()) . ';' .trim($rayon -> getType()) . ';' . trim($rayon -> getLocalisation()) . ';' ."\r\n";
				}

				$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export des rayons par ') .$_SESSION['user_login']."\r\n";
				$logFile = '../application/logs/'.date('m-Y').'-rayon.log';
				writeLog($log, $logFile);
				
				ob_clean();			
				header("Content-Type: text/csv; name=export_rayons.csv");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ". strlen($fichier));
				header("Content-Disposition: attachment; filename=export_rayons.csv");
				header("Expires: 0");
				header("Cache-Control: no-cache, must-revalidate");
				header("Pragma: no-cache");

				echo $fichier;
				
			}
		}
	}
	
	
	public static function supprimer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
			$idRayon = parent::$_request->getVar('id');
			
			if($idRayon != null){
			
				$rayonToDelete  = Rayon::load(parent::$_pdo, $idRayon);
				if($rayonToDelete != null){
					$rayonToDelete -> delete();
				}
				else{
					throw new Exception(gettext('Param&egrave;tre invalide'),5);
				}
			}
			else{
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
			}
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Suppression du rayon '). $idRayon . gettext(' par ') . $_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-rayon.log';
			writeLog($log, $logFile);
			
			Divers::setChangementLocalisation(parent::$_pdo,1);
			header('Location: '. APPLICATION_PATH . 'rayon');
		}
	}
	
}