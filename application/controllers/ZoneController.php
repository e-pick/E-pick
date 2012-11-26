<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * ZoneController.php
 *
 * Cette classe permet de gérer une zone.
 *
 */

class ZoneController extends BaseController {

	public static function index(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			parent::$_response->addVar('txt_titre'			, gettext('Zones'));			
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));	
			parent::$_response->addVar('txt_libelle_zone'	, gettext('Libell&eacute; de la zone'));			
			parent::$_response->addVar('txt_etage'			, gettext('Etage'));			
			parent::$_response->addVar('txt_zone'			, gettext('Zone'));			
			parent::$_response->addVar('txt_nb_rayons'		, gettext('Nombre de rayons'));			
			parent::$_response->addVar('txt_nb_produits'	, gettext('Nombre de produits'));
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));
			parent::$_response->addVar('txt_editer_zone'	, gettext('Editer la zone'));

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
			}
			else{
				parent::$_response->addVar('form_libelleFilter'	, '');
				parent::$_response->addVar('form_etage'			, '');
				$etages = Etage::loadAll(parent::$_pdo, true);
			}
			
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
	
	public static function creer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			parent::$_response->addVar('txt_titre'					, gettext('Cr&eacute;er une zone'));
			parent::$_response->addVar('txt_etage'					, gettext('Etage'));
			parent::$_response->addVar('txt_libelle'				, gettext('Libelle'));
			parent::$_response->addVar('txt_couleur'				, gettext('Couleur'));
			parent::$_response->addVar('txt_boutonCreer'			, gettext('Cr&eacute;er'));
			parent::$_response->addVar('txt_retour'					, gettext('Retour'));
			parent::$_response->addVar('txt_choixAutreCouleur'		, gettext('Choisir une autre couleur'));
			

			parent::$_response->addVar('arrayEtages'	, Etage::loadAll(parent::$_pdo,true));
			
			if(parent::$_request->getVar('submit')){ 
			 	
				$error_message = '';
				
				$idEtage = (int) parent::$_request->getVar('etage');
				if (!Zone::testIntegrite('idEtage', $idEtage)){
					$error_message .= gettext('Etage n\'existe pas') . '<br />';
				}
				
				$libelle = parent::$_request->getVar('libelle');
				if(!Zone::testIntegrite('libelle', $libelle)){
					$error_message .= gettext('Veuillez renseigner un libelle valide') . '<br />';
				}
				else {
					if(Zone::libelleUsed(parent::$_pdo, $libelle,$idEtage))
						/* Saisie d'un libellé déja utilisé */
						$error_message .= gettext('Ce libelle est d&eacute;j&agrave; utilis&eacute;, veuillez en choisir un autre') . '<br />';
				}
				
				$couleur = parent::$_request->getVar('couleur');
				if($couleur != null || $couleur != ''){
					if(!Zone::testIntegrite('couleur', $couleur)){
						$error_message .= gettext('Veuillez renseigner une couleur valide') . '<br />';
					}
				}
				
				if($error_message == ''){	
						$zone = Zone::create(parent::$_pdo,$idEtage,$libelle,$couleur);	// Ajout de la zone dans la base de données
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Cr&eacute;ation de la zone '). $zone->getIdzone() . ' - "' . $libelle . '"' . gettext(' par ') .$_SESSION['user_login']."\r\n";
						$logFile = '../application/logs/'.date('m-Y').'-zone.log';
						writeLog($log, $logFile);
						header('Location: '. APPLICATION_PATH . 'zone');														// Redirection vers la page gestion des zones
					
				}
				else{	
					/* Assigner les différentes varibales pour le template */
					parent::$_response->addVar('form_errors'		, $error_message);
					parent::$_response->addVar('form_etage'			, $idEtage);
					parent::$_response->addVar('form_libelle'		, $libelle);
					parent::$_response->addVar('form_couleur'		, $couleur);
				}
			}
			else{
				parent::$_response->addVar('form_etage'	, Etage::getFirstId(parent::$_pdo));
			}
		}
	}
	
	
	public static function editer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	
				$idZone = parent::$_request->getVar('id');
			if(isset($idZone))
				$zoneSelectionne = $idZone;
			else
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
			
			//chargement de l'objet
			$zone = Zone::load(parent::$_pdo,$zoneSelectionne);
			if($zone == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
			
			parent::$_response->addVar('txt_titre'				, gettext('Editer une zone'));
			parent::$_response->addVar('txt_libelle'			, gettext('Libelle'));
			parent::$_response->addVar('txt_priorite'			, gettext('Priorit&eacute;'));
			parent::$_response->addVar('txt_couleur'			, gettext('Couleur'));
			parent::$_response->addVar('txt_null'				, gettext('Sans priorit&eacute;'));
			parent::$_response->addVar('txt_debut'				, gettext('D&eacute;but'));
			parent::$_response->addVar('txt_normal'				, gettext('Normal'));
			parent::$_response->addVar('txt_fin'				, gettext('Fin'));
			parent::$_response->addVar('txt_choixAutreCouleur'	, gettext('Choisir une autre couleur'));
			parent::$_response->addVar('txt_boutonEditer'		, gettext('Editer'));
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));
			parent::$_response->addVar('txt_confirmSuppression'	, gettext('Voulez vous vraiment supprimer la zone'));
			parent::$_response->addVar('txt_lienSupprimer'		, gettext('Supprimer la zone'));
			parent::$_response->addVar('magasin'				, gettext('magasin'));	// Le libelle de la zone par d&eacute;faut
			$error_message = '';

			if(parent::$_request->getVar('submit')){  
				
				if ($zone->getLibelle() != 'magasin'){
					$libelle = parent::$_request->getVar('libelle');
					if(!Zone::testIntegrite('libelle', $libelle)){
						$error_message .= gettext('Veuillez renseigner un libelle valide') . '<br />';
					}
					else {
						if(Zone::libelleUsed(parent::$_pdo, $libelle, $zone->getEtage()->getIdetage(), $zone->getIdzone()))
							/* Saisie d'un libellé déja utilisé */
							$error_message .= gettext('Ce libelle est d&eacute;j&agrave; utilis&eacute;, veuillez en choisir un autre') . '<br />';
					}
				}
				
				$priorite = (int) parent::$_request->getVar('priorite');
				if(!Zone::testIntegrite('Priorite', $priorite)){
					$error_message .= gettext('Veuillez renseigner une priorit&eacute; valide') . '<br />';
				}
				if ($priorite == '') $priorite = null;
			
				$couleur = parent::$_request->getVar('couleur');
				if($couleur != null || $couleur != ''){
					if(!Zone::testIntegrite('couleur', $couleur)){
						$error_message .= gettext('Veuillez renseigner une couleur valide') . '<br />';
					}
				}
				
				if($error_message == ''){	
						
					$zone  = Zone::load(parent::$_pdo,$zoneSelectionne);
					if($zone == null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);		
					if ($zone->getLibelle() != 'magasin') $zone -> setLibelle($libelle,false);
					$zone -> setPriorite($priorite,false);
					$zone -> setCouleur($couleur,false);
					$zone -> update();
					
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Edition de la zone '). $zone->getIdzone() . gettext(' par ') .$_SESSION['user_login']."\r\n";
					$logFile = '../application/logs/'.date('m-Y').'-zone.log';
					writeLog($log, $logFile);
					
					header('Location: ' . APPLICATION_PATH . 'zone');
				}
				else{	
					/* Assigner les différentes varibales pour le template */
					parent::$_response->addVar('form_errors'		, $error_message);
					parent::$_response->addVar('form_libelle'		, $libelle);
					parent::$_response->addVar('form_priorite'		, $priorite);
					parent::$_response->addVar('form_libelle_display', ($zone->getLibelle() != 'magasin'));
					parent::$_response->addVar('form_couleur'		, $couleur);
					parent::$_response->addVar('form_id'			, $zone->getIdzone());
				}
			}
			else{			
				parent::$_response->addVar('form_errors'		, $error_message); 
				parent::$_response->addVar('form_id'			, $zone->getIdzone());
				parent::$_response->addVar('form_libelle'		, $zone->getLibelle());
				parent::$_response->addVar('form_libelle_display', ($zone->getLibelle() != 'magasin'));
				parent::$_response->addVar('form_priorite'		, $zone->getPriorite());
				parent::$_response->addVar('form_couleur'		, $zone->getCouleur());
			}
		}
	}
	
	public static function affecter(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			parent::$_response->addVar('txt_titreInfosRayons'	, gettext('Informations'));
			parent::$_response->addVar('txt_boutonAffecter'		, gettext('Affecter'));
			parent::$_response->addVar('txt_boutonAfficher'		, gettext('Afficher'));
			parent::$_response->addVar('txt_selectAll' 			, gettext('S&eacute;lectionner tout'));
			parent::$_response->addVar('txt_titre'				, gettext('Affectation des rayons'));
			parent::$_response->addVar('txt_filtre'				, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'			, gettext('Filtrer'));	
			parent::$_response->addVar('txt_libelle_rayon'		, gettext('Libell&eacute; du rayon'));			
			parent::$_response->addVar('txt_etage'				, gettext('Etage'));			
			parent::$_response->addVar('txt_zone'				, gettext('Zone'));	
			parent::$_response->addVar('txt_effacer'			, gettext('Effacer'));	
			parent::$_response->addVar('txt_affecter'			, gettext('Affecter'));	
			parent::$_response->addVar('txt_affectZone'			, gettext('Affectation des zones'));	
			parent::$_response->addVar('txt_afficher_rayon'		, gettext('Afficher le rayon'));	
			parent::$_response->addVar('txt_saisir_etage'		, gettext('Merci de saisir un &eacute;tage pour obtenir la liste des zones'));	
			
			
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			$etageFilter 	= parent::$_request->getVar('etageFilter');
			$zoneFilter 	= parent::$_request->getVar('zoneFilter');
			$pageFilter 	= parent::$_request->getVar('pageFilter');
			$arrayZones 	= Zone::loadAll(parent::$_pdo);
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
			
			parent::$_response->addVar('arrayZones'			, $arrayZones); 
			
			$arrayRayons 	= array();
			foreach($rayons as $rayon){
				$arraySegments 	= Segment::selectByRayon(parent::$_pdo,$rayon);	
				$zone 			= $rayon->getZone();
				$etage			= $zone->getEtage();
				$arrayRayons[] 	= array($rayon,$etage,$zone); 
			}
			
			parent::$_response->addVar('arrayEtages'		, Etage::loadAll(parent::$_pdo,true)); 			
			parent::$_response->addVar('arrayRayons'		, $arrayRayons);
			
			/* Affectation des rayons */
			if(parent::$_request->getVar('submitAffecter')){	// Clic sur le bouton affecter
				
				$rayonsAffectes	= parent::$_request->getVar('Rayon');				// Tableau des idRayon à affecter
				$zoneAffectee  	= (int) parent::$_request->getVar('choixZone');		// La zone à affecter
				
				$error_message 	= '';
				
				if($rayonsAffectes == null){
					/* Aucun rayon n'a été choisi */
					parent::$_response->addVar('form_errors',gettext('Veuillez choisir un rayon &agrave; affecter') . '<br />');
				}
				else if($zoneAffectee == null){
					/* Aucune zone n'a été choisie */
					parent::$_response->addVar('form_errors',gettext('Veuillez choisir une zone') . '<br />');
				}
				else{
					$zone = Zone::load(parent::$_pdo, $zoneAffectee);
					
					if($zone != null){
						foreach($rayonsAffectes as $rayonAffecte){
							$rayon = Rayon::load(parent::$_pdo, $rayonAffecte);
							
							if($rayon == null){
								throw new Exception(gettext('Param&egrave;tre invalide'),5);	
							}
							else{
								/* Modifier la zone */
								$rayon->setZone($zone,true);
							}
						}
					}
					else{
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					}
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Affectation de rayons dans la zone '). $zone->getIdzone() . gettext(' par ') .$_SESSION['user_login']."\r\n";
					$logFile = '../application/logs/'.date('m-Y').'-zone.log';
					writeLog($log, $logFile);
					
					Divers::setChangementLocalisation(parent::$_pdo,1);
					header('Location: '. APPLICATION_PATH . 'zone/affecter');
				}
			}
			parent::$_response->addVar('nb_resultats'		, $nb);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
			parent::$_response->addVar('page'				, $page);
			parent::$_response->addVar('txt_page'			, 'Page(s)');
		}
	}

	public static function supprimer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
			$idZone = parent::$_request->getVar('id');
			
			if($idZone != null){
			
				$zoneToDelete  = Zone::load(parent::$_pdo, $idZone);
				if($zoneToDelete != null){
					if ($zoneToDelete->getLibelle() != 'magasin'){
						$zoneToDelete -> delete();
					}
					else{
						throw new Exception(gettext('Il est impossible de supprimer la zone par d&eacute;faut'));
					}
				}
				else{
					throw new Exception(gettext('Param&egrave;tre invalide'),5);
				}
			}
			else{
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
			}
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Suppression de la zone '). $zone->getIdzone() . gettext(' par ') .$_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-zone.log';
			writeLog($log, $logFile);
			
			Divers::setChangementLocalisation(parent::$_pdo,1);
			header('Location: '. APPLICATION_PATH . 'zone');
		}
	}
}
?>