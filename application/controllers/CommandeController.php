<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * CommandeController.php
 *
 */

class CommandeController extends BaseController {

	public static function index(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur		
			
			$numcoFilter 		= parent::$_request->getVar('numcoFilter');
			$clicoFilter 		= parent::$_request->getVar('clicoFilter');
			$etatcoFilter 		= parent::$_request->getVar('etatcoFilter');
			$datedebcoFilter 	= parent::$_request->getVar('datedebcoFilter');
			$datefincoFilter 	= parent::$_request->getVar('datefincoFilter');
			$datedebliFilter 	= parent::$_request->getVar('datedebliFilter');
			$datefinliFilter 	= parent::$_request->getVar('datefinliFilter');
			$archivecoFilter 	= parent::$_request->getVar('archivecoFilter');
			$pageFilter		 	= parent::$_request->getVar('pageFilter');
				
			$array_commandes 	= array();
			$array_clients		= array();
			$nouvelle_commande 	= 0;
			$page				= 1;  
			
			if(parent::$_request->getVar('submitFilter')) {	// Clic sur le bouton filter 	
			
				$page = $pageFilter;
				
				$nb_commandes 		= count(Commande::loadAll(parent::$_pdo,true,null,$numcoFilter,$clicoFilter,$etatcoFilter,unixtime($datedebcoFilter),unixtime($datefincoFilter),unixtime($datedebliFilter),unixtime($datefinliFilter),null,$archivecoFilter));
				$nombre_de_pages 	= (ceil($nb_commandes/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_commandes/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$commandes 			= Commande::loadAll(parent::$_pdo,true,''.$first,$numcoFilter,$clicoFilter,$etatcoFilter,unixtime($datedebcoFilter),unixtime($datefincoFilter),unixtime($datedebliFilter),unixtime($datefinliFilter),null,$archivecoFilter);			
				
				
				parent::$_response->addVar('form_numcoFilter'		, $numcoFilter);
				parent::$_response->addVar('form_clicoFilter'		, $clicoFilter);
				parent::$_response->addVar('form_etatcoFilter'		, $etatcoFilter);
				parent::$_response->addVar('form_datedebcoFilter'	, $datedebcoFilter);
				parent::$_response->addVar('form_datefincoFilter'	, $datefincoFilter);
				parent::$_response->addVar('form_datedebliFilter'	, $datedebliFilter);
				parent::$_response->addVar('form_datefinliFilter'	, $datefinliFilter);
				parent::$_response->addVar('form_archivecoFilter'	, $archivecoFilter);
				parent::$_response->addVar('form_page'				, $page);
			}
			else {
				if(parent::$_request->getVar('submitArchiver')) {	// Clic sur le bouton Archiver	
					$arrayArchiver = parent::$_request->getVar('archive');
					foreach($arrayArchiver as $id =>$value){ 
						$commandeArchive = Commande::setArchiveCommande(parent::$_pdo,$id,$value);
					} 
				} 
			
				$nb_commandes		= count(Commande::loadAll(parent::$_pdo));
				$nombre_de_pages 	= (ceil($nb_commandes/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_commandes/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$commandes 			= Commande::loadAll(parent::$_pdo,true,''.$first);
				parent::$_response->addVar('form_numcoFilter'		, '');
				parent::$_response->addVar('form_clicoFilter'		, '');
				parent::$_response->addVar('form_etatcoFilter'		, '');
				parent::$_response->addVar('form_datedebcoFilter'	, '');
				parent::$_response->addVar('form_datefincoFilter'	, '');
				parent::$_response->addVar('form_datedebliFilter'	, '');
				parent::$_response->addVar('form_datefinliFilter'	, '');
				parent::$_response->addVar('form_archivecoFilter'	, '');
				parent::$_response->addVar('form_page'				, $page);
			
			}
			
			
			foreach($commandes as $commande){ 
				
				if($commande->getEtatCommande() == 0)
					$nouvelle_commande++ ;
				
				$probleme = false;
				
				$nonAffectes = 0;
				$lignes = $commande->selectLigne_commandes();
				foreach($lignes as $ligne){
					
					if ($ligne->getPreparation() == null) 
						$nonAffectes++;

					if(($produit = Produit::load(parent::$_pdo,$ligne->getProduit()->getIdProduit(),true)) == null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
						
					if (!$probleme && !Geolocalisation::isGeolocalized(parent::$_pdo, $produit->getIdProduit())){
						$probleme =	true;
						// break;
					}
				}
				$array_commandes[] = array($commande,count($lignes),Client::load(parent::$_pdo,$commande->getClient()->getIdClient()),$probleme,$nonAffectes);
			}
			
			parent::$_response->addVar('nb_resultats'		, $nb_commandes);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('array_commandes'	, $array_commandes);
			parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
			parent::$_response->addVar('page'				, $page);
			parent::$_response->addVar('txt_page'			, 'Page(s)');
			parent::$_response->addVar('array_clients'		, array_unique(Client::loadAll(parent::$_pdo)));
			parent::$_response->addVar('nouvelle_commande'	, $nouvelle_commande);
			parent::$_response->addVar('txt_titre'			, gettext('Gestion des commandes'));
			parent::$_response->addVar('txt_recup_com'		, gettext('R&eacute;cup&eacute;rer les commandes du back-office'));
			parent::$_response->addVar('txt_num_com'		, gettext('N&deg; commande'));
			parent::$_response->addVar('txt_cli_com'		, gettext('Client (Soci&eacute;t&eacute;)'));
			parent::$_response->addVar('txt_eta_com'		, gettext('Etat'));
			parent::$_response->addVar('txt_date_co_com'	, gettext('Date de commande'));
			parent::$_response->addVar('txt_date_li_com'	, gettext('Date de livraison'));
			parent::$_response->addVar('txt_com_archive'	, gettext('Archivée ?'));
			parent::$_response->addVar('txt_details'		, gettext('D&eacute;tails'));
			parent::$_response->addVar('txt_com_attente'	, gettext('Commande(s) en attente d\'affectation'));
			parent::$_response->addVar('txt_no_com'			, gettext('Aucune commande trouv&eacute;e'));
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));
			parent::$_response->addVar('txt_au'				, gettext('au')); 
			parent::$_response->addVar('txt_du'				, gettext('du')); 
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));
			parent::$_response->addVar('txt_afficherChemin'	, gettext('Afficher le chemin de pr&eacute;paration de cette commande'));
			parent::$_response->addVar('txt_nonAffectes'	, gettext('R&eacute;f&eacute;rences<br>&agrave; affecter'));
			parent::$_response->addVar('txt_archiver'		, gettext('Archiver'));
		}	
	}
	
	
	public static function importer(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur
			require_once '../flux/in/commandes/commandes_recup.php';
			parent::$_response->addVar('txt_importer'	, gettext('L\'import des commandes s\'est d&eacute;roul&eacute; correctement.'));
			parent::$_response->addVar('txt_retour'		, gettext('Retour aux commandes'));
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Importation des commandes par ') .$_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-commande.log';
			writeLog($log, $logFile);
		}
	}
	
	
	public static function afficher(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_PREPARATEUR)){		
			
			$id_commande = parent::$_request->getVar('id');
			if(!isset($id_commande))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);		
		
			/* Chargement de la commande */
			if(($commande = Commande::load(parent::$_pdo, $id_commande,true)) == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5); 
				
			if(($client = Client::load(parent::$_pdo, $commande->getClient()->getIdClient(),true)) == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);	
			
			$probleme 			= false;
			$lignes_commande 	= array();
			foreach($commande->selectLigne_commandes() as $ligne){  
				if(($produit = Produit::load(parent::$_pdo,$ligne->getProduit()->getIdProduit(),true)) == null)
					throw new Exception(gettext('Param&egrave;tre invalide'),5);
					
				$lignes_commande[] = array($ligne,$produit,Geolocalisation::isGeolocalized(parent::$_pdo, $produit->getIdProduit()));
				
			}	
			
			parent::$_response->addVar('txt_commande'		, gettext('Commande'));
			parent::$_response->addVar('txt_retour'			, gettext('Retour'));
			parent::$_response->addVar('commande'			, $commande);
			parent::$_response->addVar('client'				, $client);
			parent::$_response->addVar('lignes_commande'	, $lignes_commande);
			parent::$_response->addVar('txt_info_com'		, gettext('Informations sur la commande'));
			parent::$_response->addVar('txt_compo_com'		, gettext('Composition de la commande'));
			parent::$_response->addVar('txt_etat'			, gettext('Etat de la commande'));
			parent::$_response->addVar('txt_date_co_com'	, gettext('Date de commande'));
			parent::$_response->addVar('txt_date_li_com'	, gettext('Date de livraison'));
			parent::$_response->addVar('txt_commande_archive'	, gettext('Archiv&eacute;e ?'));
			parent::$_response->addVar('txt_cli_com'		, gettext('Client (Soci&eacute;t&eacute;)'));
			parent::$_response->addVar('txt_cli_fidel'		, gettext('Carte de fid&eacute;lit&eacute;'));
			parent::$_response->addVar('txt_cli_comment'	, gettext('Commentaire du client'));
			parent::$_response->addVar('txt_cli_tel'		, gettext('T&eacute;l&eacute;phone'));
			parent::$_response->addVar('txt_adr_fact'		, gettext('Adresse de facturation'));
			parent::$_response->addVar('txt_adr_liv'		, gettext('Adresse de livraison'));
			parent::$_response->addVar('txt_ligne_produit'	, gettext('Produit'));
			parent::$_response->addVar('txt_ligne_commandee', gettext('Quantit&eacute; command&eacute;e'));
			parent::$_response->addVar('txt_ligne_prixU'	, gettext('Prix unitaire TTC'));
			parent::$_response->addVar('txt_ligne_prix'		, gettext('Prix TTC'));
			parent::$_response->addVar('txt_no_ligne'		, gettext('Aucun produit trouv&eacute;'));
			parent::$_response->addVar('txt_details'		, gettext('D&eacute;tails'));
			parent::$_response->addVar('txt_prix_total'		, gettext('Prix total TTC (hors frais compl&eacute;mentaires)'));
			parent::$_response->addVar('txt_afficher_chemin', gettext('Afficher le chemin de pr&eacute;paration de cette commande'));
			
		}	
	}
	
	public static function chemin(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_PREPARATEUR)){
			
			/* Récupération de type : commande ou préparation */
			$type 		= parent::$_request->getVar('type');
			$idEtage 	= parent::$_request->getVar('idetage');
			
			$lignes 	= array(); // Lignes de commande
			
			/* Récupération des lignes de commandes */
			if ($type == 'commande') {
				/* Récupération de l'id de la commande */
				$id_commande = parent::$_request->getVar('id');
				if(($id_commande) == '')
					throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);		
			
				/* Chargement de la commande */
				if(($commande = Commande::load(parent::$_pdo, $id_commande,true)) == null)
					throw new Exception(gettext('Param&egrave;tre invalide : idCommande'),5);
					
				$etagesCommande = Commande::getEtagesCommande(parent::$_pdo, $commande->getIdcommande());
				if (($idEtage != '')) {
					$etageSelectionne = $idEtage;
				}
				else{
					$etageSelectionne = $etagesCommande[0]->getIdetage();
				}
				
				$currentEtage 		= Etage::load(parent::$_pdo,$etageSelectionne,true);
				if ($currentEtage == null)
					throw new Exception(gettext('Param&egrave;tre invalide : idEtage'),5);
				
				$allLignes 	= Ligne_Commande::selectByCommande(parent::$_pdo, $commande);
				/* Chargement des produits se trouvant dans deux endroits différents */
				$excepts		= array();
				$arrayNoGeoloc	= array();
				foreach($allLignes as $ligne){
					$produit  	= $ligne->getProduit(); 
					$etageres 	= $produit->selectEtageres();
					$nbGeoloc	= count($etageres);
					if($nbGeoloc > 1){
						$zone		= $etageres[0]->getZone();
						$excepts[] = array($produit, $zone->getIdzone());
					}
					else if ($nbGeoloc == 0){
						$arrayNoGeoloc[] = $ligne;
					}
				}
				
				$lignes 	= Ligne_Commande::selectByCommandeAndEtage(parent::$_pdo, $commande->getIdcommande(), $currentEtage->getIdetage(), $excepts);
				$lignes 	= array_merge($lignes, $arrayNoGeoloc);	// Ajouter les produits non géolocalisés
				
				parent::$_response->addVar('etagesCommande'			, $etagesCommande);
				parent::$_response->addVar('commande'				, $commande);
				parent::$_response->addVar('txt_titre'				, gettext('Chemin de pr&eacute;paration de la commande : ') . $commande->getCodeCommande());
			}
			else if ($type == 'preparation'){
				/* Récupération de l'id de la préparation */
				$id_preparation = parent::$_request->getVar('id');
				if(!isset($id_preparation))
					throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);		
			
				/* Chargement de la préparation */
				if(($preparation = Preparation::load(parent::$_pdo, $id_preparation,true)) == null)
					throw new Exception(gettext('Param&egrave;tre invalide : idPreparation'),5);
					
				$etagesPreparation 	= Etage::getEtageByPreparation(parent::$_pdo, $preparation->getIdpreparation());
				$currentEtage		= $etagesPreparation[0];
				if ($currentEtage == null)
					throw new Exception(gettext('Param&egrave;tre invalide : idEtage'),5);
					
				$etageSelectionne	= $currentEtage->getIdetage();
				$lignes				= Ligne_Commande::selectByPreparation(parent::$_pdo, $preparation);
				parent::$_response->addVar('txt_titre'				, gettext('Chemin de pr&eacute;paration de la pr&eacute;paration : ') . $preparation->getIdpreparation());
			}
			else{
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
			}
			
			$lignes_commande = $lignes;
		
			
			parent::$_response->addVar('currentEtage'			, $currentEtage);
			
			parent::$_response->addVar('type'					, $type);
			parent::$_response->addVar('pt_depart_top'			, $currentEtage->getPtDepartTop());
			parent::$_response->addVar('pt_depart_left'			, $currentEtage->getPtDepartLeft());
			parent::$_response->addVar('pt_arrive_top'			, $currentEtage->getPtArriveTop());
			parent::$_response->addVar('pt_arrive_left'			, $currentEtage->getPtArriveLeft());
			
			parent::$_response->addVar('distancePixel'			, '');
			parent::$_response->addVar('distanceMetre'			, '');
			parent::$_response->addVar('tempsParcours'			, '');					
			parent::$_response->addVar('listeDePointsARelier'	, '');	
			
			$arrayRayons 	= array();
			$arrayObstacles = array();
			//Chargement des rayons et des zones POUR affichage
			$arrayZones 	= Zone::loadAllEtage(parent::$_pdo, $etageSelectionne,true);
			parent::$_response->addVar('arrayZones', $arrayZones);
			
			
			// pour chaque zone de l'étage on récupère les rayons
			foreach($arrayZones as $zone){ 
				foreach(Rayon::loadAllZone(parent::$_pdo,$zone->getIdzone(),true) as $rayon){
					$count = Segment::count(parent::$_pdo, $rayon->getIdrayon());
					array_push($arrayRayons, array($rayon,$count,$zone->getCouleur())); 
				}
			}	
			parent::$_response->addVar('arrayRayons', $arrayRayons);
			
			//chargement des obstacles
			$arrayObstacles = Obstacle::loadAllEtage(parent::$_pdo, $etageSelectionne,true);
			parent::$_response->addVar('arrayObstacles', $arrayObstacles);
			
			
			
			/* Liste de points à relier */
			$matrice 			= getMatricePath();
			$matrice_distance 	= $matrice[0];
			$matrice_route 		= $matrice[1];
		
			//récupération des segments à ordonner
			
			
			
			$keys 					= array();
			$arraySegmentAPasser	= array();
			$inaccessibles 			= array();	// Tableau contenant les produits inaccessibles
			$nonModelises			= array();	// Tableau contenant les produits se trouvant dans des rayons non modélisés
			$indice1				= 0;		// Le premier élément des priorités normal
			$indice2				= 0;		// Le premier élément des priorités fin
			
			if($matrice_distance != null){
				$retour 				= getSegmentsAPasser($lignes_commande,false,$matrice_distance,$matrice_route,$etageSelectionne);
				$arraySegmentAPasser 	= $retour[0];
				$keys 					= $retour[1];
				$qteTotale				= $retour[2];
				$nonGeoloc				= $retour[3];
				$lesProduits			= $retour[6];
				
				
				foreach($nonGeoloc as $pdtNonGeoloc){
					$nonModelises[]			= $pdtNonGeoloc[0];
				}
				
				
				$inaccessibles			= $retour[4];
				$indice1 				= $retour[5];
				$indice2				= $retour[6];
			}
			else{
				/* Le fichier des chemins est vide ou n'existe pas */
				foreach($lignes_commande as $ligne)
					$nonModelises[] = $ligne->getProduit();
			}
			
			

			parent::$_response->addVar('inaccessibles', $inaccessibles);
			parent::$_response->addVar('nonModelises', $nonModelises);
			
			$distancePixel 			= 0;
			$distanceMetre 			= 0;
			$temps					= 0;
			$usePvc					= true;
			
			$retourListeDePoints 	= getListePoints($arraySegmentAPasser,$matrice_distance,$matrice_route,$keys,$indice1,$indice2,$usePvc);	

			
			
			
			$distancePixel 			= getDistancePx($retourListeDePoints[0]);
			$distanceMetre			= getDistanceM($distancePixel);
			$tempsSeconde 			= getTempsByPx($distancePixel,count($retourListeDePoints[1]));
			$temps 					= (int)($tempsSeconde / 3600) . ':'.(int)(($tempsSeconde % 3600) / 60).':'.(int)((($tempsSeconde % 3600) % 60));
			
			// echo '<pre>';
			// print_r($retourListeDePoints);
			// echo '</pre>';
			
			
			
			
			parent::$_response->addVar('distancePixel'				, $distancePixel);
			parent::$_response->addVar('distanceMetre'				, $distanceMetre);
			parent::$_response->addVar('tempsParcours'				, $temps);
			parent::$_response->addVar('listeDePointsARelier'		, $retourListeDePoints[0]);	
			
			
			parent::$_response->addVar('txt_alert'					, gettext('Attention !') . '<br />' . gettext('Cette commande est sur plusieurs &eacute;tages'));
			parent::$_response->addVar('txt_legende'				, gettext('L&eacute;gende'));
			parent::$_response->addVar('txt_obstacle'				, gettext('Obstacle'));
			parent::$_response->addVar('txt_caisse'					, gettext('Caisse'));
			parent::$_response->addVar('txt_rayon'					, gettext('Rayon'));
			parent::$_response->addVar('txt_zoneDepart'				, gettext('Zone de d&eacute;part'));
			parent::$_response->addVar('txt_infos'					, gettext('Informations'));
			parent::$_response->addVar('txt_distanceParcourue'		, gettext('Distance parcourue'));
			parent::$_response->addVar('txt_soit'					, gettext('soit'));
			parent::$_response->addVar('txt_tempsEstime'			, gettext('Temps estim&eacute;'));
			parent::$_response->addVar('txt_produitsInaccessibles'	, gettext('Les produits inaccessibles'));
			parent::$_response->addVar('txt_produitsNonModelises'	, gettext('Les produits non g&eacute;olocalis&eacute;s ou se trouvant dans des rayons non encore mod&eacute;lis&eacute;s'));
			parent::$_response->addVar('txt_total'					, gettext('Total'));
			parent::$_response->addVar('txt_produit'				, gettext('produit'));
			parent::$_response->addVar('txt_produits'				, gettext('produits'));
			parent::$_response->addVar('txt_aucun'					, gettext('Aucun'));
			parent::$_response->addVar('txt_chemin_original'		, gettext('Chemin original'));
			parent::$_response->addVar('txt_chemin_optimise'		, gettext('Chemin optimis&eacute;'));
			parent::$_response->addVar('txt_retour'					, gettext('Retour'));
			
		}
	}
	
	 
	
}
?>