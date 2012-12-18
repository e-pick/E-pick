<?php

/**
 *** Logiciel E-Pick ***
 *** Read license joint in text file for more information ***
 *** Copyright © E-Pick ***
 *
 * AffectationController.php
 *
 * Actions :
 *
 *			index		: Gestion du menu Mes préprations de l'utilisateur connecté
 *			manuelle 	: Gestion de l'affectation des commandes (en mode manuelle)
 * 							-> Choix des commandes à préparer
 *							-> Choix du mode de préparation
 *				groupeCommandes 	: Groupement des commandes en mode Multi commandes
 *				groupeMonoZone		: Groupement des commandes en mode Mono zone
 *				groupeZones			: Groupement des commandes en mode Multi zones
 *				testGroupementZone	: Test du groupement d'un ensemble de préparations en mode Multi zones
 *				testGroupement		: Test du groupement d'un ensemble de préparations en mode Mono/Multi commandes et Mono zone
 *				isBetterZone		: Test le meilleur groupement entre deux préparations en mode Multi zones
 *				isBetter			: Test le meilleur groupement entre deux préparations en mode Mono/Multi commandes et Mono zone
 *				getTempsPreparation : Retourne le temps de préparations pour un ensemble de lignes de commandes
 *			choixutilisateur 	: Choix de l'utilisateur pour la préparation des commandes choisies
 *			all 				: Affichage de l'ensemble des préparations
 *			details 			: Affichage du détail la préparation (Lignes de commande, chemin de préparation, saisie manuelle ...)
 *			supprimer 			: Suppression d'un préparation
 *			reinitialiser 		: Reinitialisation d'une préparation (de l'état en cours de préparation vers en attente de préparation)
 *			config 				: Configuration des paramètres pour l'affectation
 *
 */

class AffectationController extends BaseController {

 
	
	public static function index(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_PREPARATEUR)){
			
			/* Génération des fichiers vers le PDA au format txt */
			if(parent::$_request->getVar('submit') != '' && parent::$_request->getVar('selected') != ''){
				if(parent::$_request->getVar('exportPDA')){
				$time		= time();
				$arrayIdPreparation = parent::$_request->getVar('selected');
				$matrices	= getMatricePath();
				$nb_prepas = count($arrayIdPreparation);
				
				/* on récupère les id de chaque préparation */ 
				foreach($arrayIdPreparation as $idPreparation){
					$contenu_fichier_txt = '';
					if(($prepa = Preparation::load(parent::$_pdo,$idPreparation))==null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					$prepa->setTypePreparation('PDA');
					$commandes = $prepa->getCommandes();
					
					if (count($commandes) == 1){
						$client				= $commandes[0]->getClient();
						
						$dateHeureCommande 		= $commandes[0]->getDateCommande();
						$numClient 				= $client->getIdclient();
						$nomClient 				= $client->getNom();
						$prenomClient			= $client->getPrenom();
						$adresseFacturation		= $client->getLigneAdresseFacturation();
						$codePostalFacturation	= $client->getCodePostalFacturation();
						$villeFacturation		= $client->getMunicipaliteFacturation();
						$telephoneClient		= $client->getTelephone();
						$mailClient				= '';
						$modePaiement			= '';
						$encaisse				= '';
						$modeLivraison			= '';
						$etageLivraison			= '';
						$adresseLivraison		= $commandes[0]->getLigneAdresseLivraison();
						$codePostalLivraison	= $commandes[0]->getCodePostalLivraison();
						$villeLivraison			= $commandes[0]->getMunicipaliteLivraison();
						$commentaire			= $commandes[0]->getCommentaireClient();
						
					}
					else{
						$dateHeureCommande 		= '';
						$numClient 				= '';
						$nomClient 				= '';
						$prenomClient			= '';
						$adresseFacturation		= '';
						$codePostalFacturation	= '';
						$villeFacturation		= '';
						$telephoneClient		= '';
						$mailClient				= '';
						$modePaiement			= '';
						$encaisse				= '';
						$modeLivraison			= '';
						$etageLivraison			= '';
						$adresseLivraison		= '';
						$codePostalLivraison	= '';
						$villeLivraison			= '';
						$commentaire			= '';
					}
					
					$contenu_fichier_txt .= APPLICATION_PREFIXE . $prepa->getIdpreparation() . "\r\n";
					$contenu_fichier_txt .= $dateHeureCommande . "\r\n"; //date heure commande
					$contenu_fichier_txt .= $numClient . "\r\n"; // num client
					$contenu_fichier_txt .= $nomClient . "\r\n"; // nom
					$contenu_fichier_txt .= $prenomClient . "\r\n"; // prenom
					$contenu_fichier_txt .= $adresseFacturation . "\r\n"; // adresse facturation
					$contenu_fichier_txt .= $codePostalFacturation . "\r\n"; // CP facturation
					$contenu_fichier_txt .= $villeFacturation . "\r\n"; // ville facturation	
					$contenu_fichier_txt .= $telephoneClient . "\r\n"; // tel client
					$contenu_fichier_txt .= $mailClient . "\r\n"; // email
					$contenu_fichier_txt .= $modePaiement . "\r\n"; // mode paiement
					$contenu_fichier_txt .= $encaisse . "\r\n"; // encaissé
					$contenu_fichier_txt .= $modeLivraison . "\r\n"; // mode de livraison
					$contenu_fichier_txt .= date('d/m/Y H:i',$prepa->getDate_preparation())."\r\n"; //date livraison
					$contenu_fichier_txt .= $etageLivraison . "\r\n"; // etage livraison
					$contenu_fichier_txt .= $adresseLivraison . "\r\n"; // adresse livraison
					$contenu_fichier_txt .= $codePostalLivraison . "\r\n"; // cp livraison
					$contenu_fichier_txt .= $villeLivraison . "\r\n"; // ville livraison
					$contenu_fichier_txt .= $commentaire . "\r\n"; // commentaires client
					$contenu_fichier_txt .= '******'."\r\n";
					$cpt = 0;
					
					/* ordonne la liste des produits de la préparation */
					$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
					$erreur = false;
					$lsc = $prepa->selectLigne_commandes();
					$etage = null;
					$first = true;
					foreach($lsc as $lc){
						$produit = $lc->getProduit();
						$locs = $produit -> selectEtageres();
						if(count($locs) == 0){
							$etage 	= $etageDefaut;
						}
						else{
							$zone 	= $locs[0]->getZone();
							$etage 	= $zone->getEtage();
						}
						if($first){
							$idEtage = $etage->getIdetage();
							$first = false;
						}
						if($etage->getIdetage() != $idEtage)
							$erreur = true;
					}
					
					if(!$erreur){
									
						$retour 				= getSegmentsAPasser($lsc,false,$matrices[0],$matrices[1],$idEtage);					
						$arraySegmentAPasser 	= $retour[0];
						$keys 					= $retour[1];
						$qteTotale				= $retour[2];
						$nonGeoloc				= $retour[3];
						$indice1 				= $retour[5];
						$indice2				= $retour[6];
						$usePvc					= true;
						
						$retourListeDePoints 	= getListePoints($arraySegmentAPasser,$matrices[0],$matrices[1],$keys,$indice1,$indice2,$usePvc);
						
						/* on parcourt les lignes de commande (lc) pour extraire les informations nécessaires */ 
						foreach($retourListeDePoints[1] as $segment){ 
							 if($segment[2] != null){
								foreach($segment[2] as $lc){
									if(($produit = Produit::load(parent::$_pdo,$lc->getProduit()->getIdProduit()))==null)
										throw new Exception(gettext('Param&egrave;tre invalide'),5);
									
									$contenu_fichier_txt .= $lc->getIdLigne() . "\t";
									$contenu_fichier_txt .= $produit->getCodeProduit() . "\t";
									$contenu_fichier_txt .= unhtmlentities($produit->getLibelle()) . "\t";
									$contenu_fichier_txt .= $produit->getEstPoidsVariable() . "\t";
									$contenu_fichier_txt .= $lc->getQuantiteCommandee() . "\t";
									$cpt += $lc->getQuantiteCommandee();
									$contenu_fichier_txt .= $lc->getPrixUnitaireTTC() . "\t"; //prix unitaire ou prix unitaire * nb article
									$etageres	= $lc->getProduit()->selectEtageres();
									$segment	= $etageres[0]->getSegment();
									$rayon		= $segment->getRayon();
									$codeGeoloc = '';
									if ($rayon->getLocalisation() == null)
										$codeGeoloc .= substr($rayon->getLibelle(),0,15) . '-';
									else
									$codeGeoloc .= $rayon->getLocalisation() . '-'; 
									$codeGeoloc .= Segment::getPosition(parent::$_pdo,$segment->getIdsegment(),$rayon->getIdrayon()) . '-';
									$codeGeoloc .= Etagere::getPosition(parent::$_pdo,$etageres[0]->getIdetagere(), $segment->getIdsegment());
									$contenu_fichier_txt .= $codeGeoloc . "\t"; // code géolocalisation
									
									$eans	=	$produit->selectEans();
									if(count($eans) > 0)
										$contenu_fichier_txt .= $eans[0]->getEan() . "\t"; //ean maitre, il y en a forcément un 
									else	
										$contenu_fichier_txt .= "\t";
									
									if(count($eans) > 1){
										for($i = 1; $i < count($eans); $i++){
											$contenu_fichier_txt .= $eans[$i]->getEan() . "\t"; //ean pas maitre s'il y en a
										}
									}
									else{						
										$contenu_fichier_txt .= '' . "\t"; //liste ean vide
									}
									
									$contenu_fichier_txt .= '***'."\r\n"; //fin d'une ligne de commande								
								}
							}
						}
						
						foreach($nonGeoloc as $ligneNonGeoloc){
							$contenu_fichier_txt .= $ligneNonGeoloc[1]->getIdLigne() . "\t";
							$contenu_fichier_txt .= $ligneNonGeoloc[0]->getCodeProduit() . "\t";
							$contenu_fichier_txt .= unhtmlentities($ligneNonGeoloc[0]->getLibelle()) . "\t";
							$contenu_fichier_txt .= $ligneNonGeoloc[0]->getEstPoidsVariable() . "\t";
							$contenu_fichier_txt .= $ligneNonGeoloc[1]->getQuantiteCommandee() . "\t";
							$cpt += $ligneNonGeoloc[1]->getQuantiteCommandee();
							$contenu_fichier_txt .= $ligneNonGeoloc[1]->getPrixUnitaireTTC() . "\t";
							$contenu_fichier_txt .= 'Non géolocalisé' . "\t";
							$eans	=	$ligneNonGeoloc[0]->selectEans();
							if(count($eans) > 0)
								$contenu_fichier_txt .= $eans[0]->getEan() . "\t"; //ean maitre, il y en a forcément un 
							else	
								$contenu_fichier_txt .= "\t";
							if(count($eans) > 1){
								for($i = 1; $i < count($eans); $i++){
									$contenu_fichier_txt .= $eans[$i]->getEan() . "\t"; //ean pas maitre s'il y en a
								}
							}
							else{						
								$contenu_fichier_txt .= '' . "\t"; //liste ean vide
							}
							
							$contenu_fichier_txt .= '***'."\r\n"; //fin d'une ligne de commande	
						}
						
						/* fin des lignes commande */ 	
						$contenu_fichier_txt .= $cpt."\r\n"; // nombre de produits					
						$contenu_fichier_txt .= '******';

						/* ecriture des fichiers */
						$file_name 	= APPLICATION_PREFIXE . $prepa->getIdpreparation() . "_" . $time; 					
						$open 		= fopen('./PDA/commandes/out/' . $file_name. '.txt', "w+");
						fwrite($open, $contenu_fichier_txt);
						fclose($open);
						
						
						/* ecriture du fichier lot pour faire fonctionner l'existant */
						$preparateur = $prepa->getUtilisateur();
						$file_name2	= 'LOT_' . $preparateur->getPrenom() . '_' . $preparateur->getNom() . '_' . $prepa->getIdpreparation() . '_' . $time; 	
						$contenu_fichier_txt = $file_name2 . "\r\n";
						$contenu_fichier_txt .= $file_name . '.txt'; 
						$open 		= fopen('./PDA/commandes/out/' . $file_name2 . '.txt', "w+");
						fwrite($open, $contenu_fichier_txt);
						fclose($open);	
						
						/* Mis à jour de l'état de préparation */
						$prepa->setEtat(1);
						foreach($commandes as $commande){
							if($commande -> isAllAffected() && $commande -> isNotWaiting()){
								$commande -> setEtatCommande(2);
							}
						}
						
						/* Impression des étiquettes */
						$arrayCommandes = array();
						foreach($commandes as $commande){
							$arrayCommandes[] = array($commande , 1);
						}
						$fileName = './uploads/Etiq_' . $prepa->getIdpreparation() . '.pdf';
						etiquettePDFA4($arrayCommandes, $fileName);
					}
				}
				if(!$erreur){
				
					if($nb_prepas > 1){
						$str = gettext('Les fichiers sont &agrave; disposition pour &ecirc;tre t&eacute;l&eacute;charg&eacute;s sur le PDA');
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export des pr&eacute;parations ') . implode(', ', $arrayIdPreparation) . gettext(' sur le PDA') . gettext(' par ') . $_SESSION['user_login']."\r\n";
					}
					else{
						$str = gettext('Le fichier est &agrave; disposition pour &ecirc;tre t&eacute;l&eacute;charg&eacute; sur le PDA');	
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export de la pr&eacute;paration ') . $arrayIdPreparation[0] . gettext(' sur le PDA') . gettext(' par ') . $_SESSION['user_login']."\r\n";											
					}
					self::$_response->addVar('user_feeedback',array ('success',$str));
					
					$logFile = '../application/logs/'.date('m-Y').'-affectation.log';
					writeLog($log, $logFile);
				}
				else{
					$str = gettext('Des produits ont chang&eacute; de localisation entrainant une pr&eacute;paration sur plusieurs &eacute;tages. ');
					$str .= "\n" . gettext('La pr&eacute;paration doit &ecirc;tre annul&eacute;e et r&eacute;affect&eacute;e.');
					self::$_response->addVar('user_feeedback',array ('echec',$str));
				}
			}
			if(parent::$_request->getVar('exportPDF')){ 
				
				$arrayIdPreparation = parent::$_request->getVar('selected');
				$matrices	= getMatricePath();
				$nb_prepas = count($arrayIdPreparation);
				
				foreach($arrayIdPreparation	as $idPreparation){
					$prepa = Preparation::load(parent::$_pdo,$idPreparation);					
					$prepa->setTypePreparation('PDF');
					
					/* ordonne la liste des produits de la préparation */
					$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
					$erreur = false;
					$lsc = $prepa->selectLigne_commandes();
					$etage = null;
					$first = true;
					foreach($lsc as $lc){
						$produit = $lc->getProduit();
						$locs = $produit -> selectEtageres();
						if(count($locs) == 0){
							$etage 	= $etageDefaut;
						}
						else{
							$zone 	= $locs[0]->getZone();
							$etage 	= $zone->getEtage();
						}
						if($first){
							$idEtage = $etage->getIdetage();
							$first = false;
						}
						if($etage->getIdetage() != $idEtage)
							$erreur = true;
					}
					
					if(!$erreur){
						
						$retour 				= getSegmentsAPasser($lsc,false,$matrices[0],$matrices[1],$idEtage);					
						$arraySegmentAPasser 	= $retour[0];
						$keys 					= $retour[1];
						$qteTotale				= $retour[2];
						$nonGeoloc				= $retour[3];
						$indice1 				= $retour[5];
						$indice2				= $retour[6];
						$usePvc					= true;
									
						$retourListeDePoints 	= getListePoints($arraySegmentAPasser,$matrices[0],$matrices[1],$keys,$indice1,$indice2,$usePvc);
						
						$lignes = array();
						foreach($retourListeDePoints[1] as $segment){ 
							 if($segment[2] != null){
								foreach($segment[2] as $lc){
									$etageres	= $lc->getProduit()->selectEtageres();
									$segment	= $etageres[0]->getSegment();
									$rayon		= $segment->getRayon();
									$codeGeoloc = '';
									$codeGeoloc .= Segment::getPosition(parent::$_pdo,$segment->getIdsegment(),$rayon->getIdrayon()) . '-';
									$codeGeoloc .= Etagere::getPosition(parent::$_pdo,$etageres[0]->getIdetagere(), $segment->getIdsegment());
									$lignes[] = array($lc,$rayon->getLibelle(),$codeGeoloc);
								}
							}
						}
						
						foreach($nonGeoloc as $ligneNonGeoloc){
							$codeGeoloc = 'Non géolocalisé';
							$lignes[]	= array($ligneNonGeoloc[1], $codeGeoloc, '');
						}

						$arrayCommandes = $prepa -> getCommandes();

						$file_name 	= './PDF/' . APPLICATION_PREFIXE . $prepa->getIdpreparation() . '.pdf';
						if (preparationPDF($prepa, $lignes, $file_name)){
							/* Mis à jour de l'état de préparation */
							$prepa->setEtat(1);
							foreach($arrayCommandes as $commande){
								if($commande -> isAllAffected() && $commande -> isNotWaiting()){
									$commande -> setEtatCommande(2);
								}
							}
						}
						
						/* Impression des étiquettes */
						$commandes = array();
						foreach($arrayCommandes as $commande){
							$commandes[] = array($commande , 1);
						}
						$fileName = './uploads/Etiq_' . $prepa->getIdpreparation() . '.pdf';
						etiquettePDFA4($commandes, $fileName);
					}
				}
				if(!$erreur){
					if($nb_prepas > 1){
						$str = gettext('Les fichiers PDF sont &agrave; disposition pour &ecirc;tre t&eacute;l&eacute;charg&eacute;s');
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export de la pr&eacute;paration ') . implode(', ', $arrayIdPreparation) . gettext(' en PDF') . gettext(' par ') . $_SESSION['user_login']."\r\n";											
					}
					else{
						$str = gettext('Le fichier PDF est &agrave; disposition pour &ecirc;tre t&eacute;l&eacute;charg&eacute;');	
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export de la pr&eacute;paration ') . $arrayIdPreparation[0] . gettext(' en PDF') . gettext(' par ') . $_SESSION['user_login']."\r\n";											
					}
					self::$_response->addVar('user_feeedback',array ('success',$str));
					
				}
				else{
					$str = gettext('Des produits ont chang&eacute; de localisation entrainant une pr&eacute;paration sur plusieurs &eacute;tages. ');
					$str .= "\n" . gettext('La pr&eacute;paration doit &ecirc;tre annul&eacute;e et r&eacute;affect&eacute;e.');
					self::$_response->addVar('user_feeedback',array ('echec',$str));
				}
				}	
			}

			$arrayPrepas 		= array();
			$user 				= Utilisateur::load(parent::$_pdo,$_SESSION['user_id']);
			
			$numprepaFilter 	= parent::$_request->getVar('numprepaFilter');
			$etatprepaFilter 	= parent::$_request->getVar('etatprepaFilter');  
			$datedebcoFilter 	= parent::$_request->getVar('datedebcoFilter');
			$datefincoFilter 	= parent::$_request->getVar('datefincoFilter'); 
			$pageFilter		 	= parent::$_request->getVar('pageFilter');			
			$page				= 1;  
			
			if(parent::$_request->getVar('submitFilter')) {	// Clic sur le bouton filter 	
		
				$page = $pageFilter; 
				$nb_commandes		= count(Preparation::loadAll(parent::$_pdo,null,$numprepaFilter,$etatprepaFilter,$user->getIdUtilisateur(),unixtime($datedebcoFilter),unixtime($datefincoFilter)));
				$nombre_de_pages 	= (ceil($nb_commandes/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_commandes/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);				
				$preparations		= Preparation::loadAll(parent::$_pdo,''.$first,$numprepaFilter,$etatprepaFilter,$user->getIdUtilisateur(),unixtime($datedebcoFilter),unixtime($datefincoFilter));
				
				
				parent::$_response->addVar('form_numprepaFilter'			, $numprepaFilter);
				parent::$_response->addVar('form_etatprepaFilter'			, $etatprepaFilter); 
				parent::$_response->addVar('form_datedebcoFilter'			, $datedebcoFilter);
				parent::$_response->addVar('form_datefincoFilter'			, $datefincoFilter); 
				parent::$_response->addVar('form_page'						, $page);
			}
			else{
				$nb_commandes		= count(Preparation::loadAll(parent::$_pdo,null,null,null,$user->getIdUtilisateur(),null,null));
				
				$nombre_de_pages 	= (ceil($nb_commandes/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_commandes/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE); 		
				$preparations		=Preparation::loadAll(parent::$_pdo,''.$first,null,null,$user->getIdUtilisateur(),null,null);
				parent::$_response->addVar('form_numprepaFilter'			, '');
				parent::$_response->addVar('form_etatprepaFilter'			, '');				
				parent::$_response->addVar('form_datedebcoFilter'			, '');
				parent::$_response->addVar('form_datefincoFilter'			, ''); 
				parent::$_response->addVar('form_page'						, $page);
			}
		
			parent::$_response->addVar('nb_resultats'		, $nb_commandes);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
			parent::$_response->addVar('page'				, $page);
			parent::$_response->addVar('txt_page'			, 'Page(s)');
			parent::$_response->addVar('array_clients'		, array_unique(Client::loadAll(parent::$_pdo)));
			parent::$_response->addVar('array_typeLivraison', Commande::getListeTypeLivraison(parent::$_pdo));
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));
			parent::$_response->addVar('txt_au'				, gettext('au')); 
			parent::$_response->addVar('txt_du'				, gettext('du')); 
			parent::$_response->addVar('txt_num_prepa'		, gettext('Num&eacute;ro de la pr&eacute;paration'));  
			parent::$_response->addVar('txt_etat_prepa'		, gettext('Etat de la pr&eacute;paration'));  
			parent::$_response->addVar('txt_date_co_com'	, gettext('Date limite de fin'));  
			
			
			foreach($preparations as $preparation){
				$lignes			= Ligne_Commande::selectByPreparation(parent::$_pdo, $preparation);
				$nbReferences 	= count($lignes);
				$nbProduits		= 0;
				foreach($lignes as $ligne){
					$nbProduits += $ligne->getQuantiteCommandee();
				}
 				
				$arrayPrepas[] 	= array($preparation,$nbReferences,$nbProduits,$preparation->getDuree());
			}
			
			parent::$_response->addVar('arrayPrepas'			, $arrayPrepas);
			parent::$_response->addVar('txt_explain'			, gettext('Choisissez l\'export souhait&eacute.'));
			parent::$_response->addVar('txt_exporter'			, gettext('Exporter'));
			parent::$_response->addVar('txt_exportPDA'			, gettext('Exporter vers le PDA'));
			parent::$_response->addVar('txt_exportPDF'			, gettext('Exporter en format PDF'));
			parent::$_response->addVar('txt_modeDegrade'		, gettext('Mode d&eacute;grad&eacute;'));
			parent::$_response->addVar('txt_titre'				, gettext('Mes commandes &agrave; pr&eacute;parer'));
			parent::$_response->addVar('txt_explication'		, gettext('texte blabla'));
			parent::$_response->addVar('txt_etat_preparation'	, gettext('Etat de la pr&eacute;paration'));
			parent::$_response->addVar('txt_num_preparation'	, gettext('N°'));
			parent::$_response->addVar('txt_nb_references'		, gettext('Nb r&eacute;f&eacute;rences'));
			parent::$_response->addVar('txt_nb_produits'		, gettext('Nb produits'));
			parent::$_response->addVar('txt_duree'				, gettext('Dur&eacute;e'));
			parent::$_response->addVar('txt_afficherDetails'	, gettext('Afficher le d&eacute;tail de cette pr&eacute;paration'));
			parent::$_response->addVar('txt_afficherChemin'		, gettext('Afficher le chemin de pr&eacute;paration de cette pr&eacute;paration'));
			parent::$_response->addVar('txt_afficherPDF'		, gettext('Afficher le bon de pr&eacute;paration en format PDF'));
			parent::$_response->addVar('txt_afficherEtiq'		, gettext('Afficher les &eacute;tiquettes pour impression'));
			parent::$_response->addVar('txt_no_com'				, gettext('Aucune pr&eacute;paration &agrave; effectuer'));
			parent::$_response->addVar('txt_date_limite_debut'	, gettext('Date limite de d&eacute;but'));
			parent::$_response->addVar('txt_date_limite_fin'	, gettext('Date limite de fin'));
			parent::$_response->addVar('txt_chemin_original'	, gettext('Chemin original'));
			parent::$_response->addVar('txt_chemin_optimise'	, gettext('Chemin optimis&eacute;'));
		}
	}	
	
	public static function manuelle(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
			$affichage = array();
			$idEtage = 0; 
			
			unset($_SESSION['min']);
			unset($_SESSION['duree']);
			unset($_SESSION['lignes_co']);
			
			$commandes = array();
			/* Recupération des commandes à afficher dans tous les cas */
			/* on commence par lister les commandes que l'utilisateur a selectionné sur la pemiere page */
			//if( parent::$_request->getVar('submitListeCommande')!= ''&& parent::$_request->getVar('commandes')!= ''){ 
			if( parent::$_request->getVar('commandes')!= ''){  
				$modePreparation = parent::$_request->getVar('modePreparation');
				if($modePreparation == 'monoCommande')
					$type = 'monoCommande';
				else if($modePreparation == 'multiCommandes')
					$type = 'multiCommandes';
				else if($modePreparation == 'monoZone')
					$type = 'monoZone';
				else if($modePreparation == 'multiZones')
					$type = 'multiZones';
				else 
					$type = 'monoCommande';
				unset($_SESSION['array_preparations']);
				$strSelection = '';
				parent::$_response->addVar('selection' , implode('-', parent::$_request->getVar('commandes')));
				foreach(parent::$_request->getVar('commandes') as $selection){
					
					$data = explode('_',$selection);
					$idEtage = $data[0];
					$commandes[] = Commande::load(parent::$_pdo,$data[1]);				
				} 
				$commandes = array_unique($commandes);
				if($idEtage == 0)
					throw new Exception('Un paramètre est invalide',5);

				parent::$_response->addVar('modePreparation'				, $modePreparation);
			} /* listing des commandes lorsqu'on change de mode */
			// else if ((parent::$_request->getVar('submitMonoCommandes') || parent::$_request->getVar('submitMultiCommandes') || parent::$_request->getVar('submitMonoZones') || parent::$_request->getVar('submitMultiZones') || parent::$_request->getVar('submitBoth'))  && parent::$_request->getVar('commandes_select') || isset($_SESSION['notAffectedYet'])){
			else if ((parent::$_request->getVar('submitType') && parent::$_request->getVar('commandes_select')) || isset($_SESSION['retour'])){ // || parent::$_request->getVar('submitMultiCommandes') || parent::$_request->getVar('submitMonoZones') || parent::$_request->getVar('submitMultiZones') || parent::$_request->getVar('submitBoth'))  && parent::$_request->getVar('commandes_select')){
				$modePreparation = parent::$_request->getVar('modePreparation');
				$retour = $_SESSION['retour'];
				if (isset($_SESSION['retour']))
					$type = $_SESSION['type'];
				else if(parent::$_request->getVar('submitType') == 'submitMonoCommandes')
					$type = 'monoCommande';
				else if(parent::$_request->getVar('submitType') == 'submitMultiCommandes')
					$type = 'multiCommandes';
				else if(parent::$_request->getVar('submitType') == 'submitMonoZones')
					$type = 'monoZone';
				else if(parent::$_request->getVar('submitType') == 'submitMultiZones')
					$type = 'multiZones';
				else 
					$type = 'listing';
					
				if (!isset($_SESSION['retour'])){
					parent::$_response->addVar('selection' , parent::$_request->getVar('commandes_select'));
					$selections = parent::$_request->getVar('commandes_select');
					foreach(explode('-' , $selections) as $selection){
						$data = explode('_',$selection);
						$idEtage = $data[0];
						$commandes[] = Commande::load(parent::$_pdo,$data[1]);
					}
					$commandes = array_unique($commandes);
					if($idEtage == 0)
						throw new Exception('Un paramètre est invalide',5);
 				}
				else {
					if ($type != 'listing'){
						$idEtage	= $_SESSION['idEtage'];
						$modePreparation = $_SESSION['modePreparation'];
						if($idEtage == 0)
							throw new Exception('Un paramètre est invalide : idEtage',5);
							
						$selection = array();
						foreach($_SESSION['notAffectedYet'] as $idCommande){
							$commandes[] = Commande::load(parent::$_pdo, $idCommande);
							$selection[] = $idEtage . '_' . $idCommande . '_' . '0';
						}
						$commandes 	= array_unique($commandes);
						parent::$_response->addVar('selection' ,implode('-',$selection));
						
					}
					
					unset($_SESSION['notAffectedYet']);
					unset($_SESSION['idEtage']);
				}
				parent::$_response->addVar('modePreparation'	, $modePreparation);
			}
			else{ //liste de toutes les commandes par defaut avec l'état : en cours d'affectation 
				$type 				= 'listing';	
				unset($_SESSION['array_preparations']);
				unset($_SESSION['notAffectedYet']);
				unset($_SESSION['idEtage']);
				unset($_SESSION['retour']);
			}
			
			if($type == 'listing'){
				$_SESSION['type'] 	= $type;
				
				$numcoFilter 		= parent::$_request->getVar('numcoFilter');
				$clicoFilter 		= parent::$_request->getVar('clicoFilter'); 
				$typeLivraisonFilter= parent::$_request->getVar('typeLivraisonFilter'); 
				$datedebcoFilter 	= parent::$_request->getVar('datedebcoFilter');
				$datefincoFilter 	= parent::$_request->getVar('datefincoFilter');
				$datedebliFilter 	= parent::$_request->getVar('datedebliFilter');
				$datefinliFilter 	= parent::$_request->getVar('datefinliFilter');
				$pageFilter		 	= parent::$_request->getVar('pageFilter');
				
				$page				= 1;  
				
				if(parent::$_request->getVar('submitFilter')) {	// Clic sur le bouton filter 	
			
					$page = $pageFilter;
					
					$nb_commandes 		= count(Commande::loadAll(parent::$_pdo,true,null,$numcoFilter,$clicoFilter,'0',unixtime($datedebcoFilter),unixtime($datefincoFilter),unixtime($datedebliFilter),unixtime($datefinliFilter),htmlentities($typeLivraisonFilter)));
					$nombre_de_pages 	= (ceil($nb_commandes/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_commandes/RESULTAT_PAR_PAGE);
					if($page > $nombre_de_pages) 
						$page = $nombre_de_pages;
						
					$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
					$commandes 			= Commande::loadAll(parent::$_pdo,true,''.$first,$numcoFilter,$clicoFilter,'0',unixtime($datedebcoFilter),unixtime($datefincoFilter),unixtime($datedebliFilter),unixtime($datefinliFilter),htmlentities($typeLivraisonFilter));			
				
					parent::$_response->addVar('form_numcoFilter'			, $numcoFilter);
					parent::$_response->addVar('form_clicoFilter'			, $clicoFilter); 
					parent::$_response->addVar('form_typeLivraisonFilter'	, htmlentities($typeLivraisonFilter)); 
					parent::$_response->addVar('form_datedebcoFilter'		, $datedebcoFilter);
					parent::$_response->addVar('form_datefincoFilter'		, $datefincoFilter);
					parent::$_response->addVar('form_datedebliFilter'		, $datedebliFilter);
					parent::$_response->addVar('form_datefinliFilter'		, $datefinliFilter);
					parent::$_response->addVar('form_page'					, $page);
				}
				else{
					$nb_commandes		= count(Commande::loadAll(parent::$_pdo,true,null,null,null,'0'));
					$nombre_de_pages 	= (ceil($nb_commandes/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_commandes/RESULTAT_PAR_PAGE);
					if($page > $nombre_de_pages) 
						$page = $nombre_de_pages;
						
					$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
					$commandes= Commande::loadAll(parent::$_pdo,true,''.$first,null,null,'0');
					parent::$_response->addVar('form_numcoFilter'			, '');
					parent::$_response->addVar('form_clicoFilter'			, '');
					parent::$_response->addVar('form_typeLivraisonFilter'	, ''); 					
					parent::$_response->addVar('form_datedebcoFilter'		, '');
					parent::$_response->addVar('form_datefincoFilter'		, '');
					parent::$_response->addVar('form_datedebliFilter'		, '');
					parent::$_response->addVar('form_datefinliFilter'		, '');
					parent::$_response->addVar('form_page'					, $page);
				}
			
				parent::$_response->addVar('nb_resultats'		, $nb_commandes);
				parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
				parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
				parent::$_response->addVar('txt_pages'			, gettext('pages')); 
				parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
				parent::$_response->addVar('page'				, $page);
				parent::$_response->addVar('txt_page'			, 'Page(s)');
				parent::$_response->addVar('array_clients'		, array_unique(Client::loadAll(parent::$_pdo)));
				parent::$_response->addVar('array_typeLivraison', Commande::getListeTypeLivraison(parent::$_pdo));
			}
			
			unset($_SESSION['retour']);
			
			/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
			  // unset($_SESSION['array_preparations']);
			if($type != 'listing' && !isset($_SESSION['array_preparations'])){
				$modePreparation = parent::$_request->getVar('modePreparation');
				if ($retour == 1)
					$modePreparation = $_SESSION['modePreparation'];
				if ($modePreparation == 'monoCommande' || $modePreparation == 'all'){
					$monoCommandes =  self::groupeCommandes($commandes,1,$idEtage); 
					foreach($monoCommandes as $liste){
						$listeID = array();
						foreach($liste[0] as $commande)
							$listeID[] =  array($commande->getIdCommande(), $liste[1]);
						$_SESSION['array_preparations']['monoCommande'][] = $listeID;
					} 
 				} 
				
				if ($modePreparation == 'multiCommandes' || $modePreparation == 'all'){
					$multiCommandes =  self::groupeCommandes($commandes,NB_COMMANDES_MAX,$idEtage); 
					foreach($multiCommandes as $liste){
						$listeID = array();
						foreach($liste[0] as $commande)
							$listeID[] =  array($commande->getIdCommande(), $liste[1]);
						$_SESSION['array_preparations']['multiCommandes'][] = $listeID;
					}
				} 
				
				if ($modePreparation == 'monoZone' || $modePreparation == 'all'){
					$monoZone =  self::groupeMonoZone($commandes,NB_COMMANDES_MAX,$idEtage);
					
					foreach($monoZone as $liste){
						$listeID = array();
						foreach($liste[0] as $commande)
							$listeID[] =  array($commande->getIdCommande(), $liste[1]);
						$_SESSION['array_preparations']['monoZone'][] = $listeID;
					} 
					
					
				}
				if ($modePreparation == 'multiZones' || $modePreparation == 'all'){
					$monoZone =  self::groupeMonoZone($commandes,NB_COMMANDES_MAX,$idEtage);
					if (NB_COMMANDES_MAX == 1){
						foreach($monoZone as $liste){
							$listeID = array();
							foreach($liste[0] as $commande)
								$listeID[] =  array($commande->getIdCommande(), $liste[1]);
							$_SESSION['array_preparations']['multiZones'][] = $listeID;
						} 
					}
					else {
						$multiZones =  self::groupeZones($monoZone,$commandes,NB_COMMANDES_MAX,$idEtage);
						foreach($multiZones as $prepa){
							$listeID = array();
							foreach($prepa as $liste){
								$listeIDZone = array();
								foreach($liste[1] as $zone)
									$listeIDZone[] = $zone->getIdzone();
									
								foreach($liste[0] as $commande){
									if(isset($listeID[$commande->getIdCommande()]))
										$listeID[$commande->getIdCommande()][1] = array_merge($listeIDZone,$listeID[$commande->getIdCommande()][1]);
									else
										$listeID[$commande->getIdCommande()] =  array($commande->getIdCommande(), $listeIDZone);
								}
							}
							
							$_SESSION['array_preparations']['multiZones'][] = $listeID;
						}
					} 
				}
				parent::$_response->addVar('modePreparation'				, $modePreparation);
				
			}
			 
			if(isset($_SESSION['temps_preparations']))
				unset($_SESSION['temps_preparations']);
			else
				$_SESSION['temps_preparations']= array();
				
			/* on éclate et prépare les commandes pour l'affichage, on récupère les données dont on a besoin */
			if ($type == 'listing'){ //le listing sur la premiere page
				$_SESSION['temps_preparations_total']=0;
				$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
				$zoneMagasin  = Zone::loadZoneMagasinByEtage(parent::$_pdo, $etageDefaut->getIdetage());
				
				if(Divers::getChangementLocalisation(parent::$_pdo)){
					parent::$_response->addVar('rechargerTempsPreparation', 1);			
					parent::$_response->addVar('txt_rechargerTempsPreparation', 'La géolocalisation a été modifiée. Le calcul des temps de préparation doit être relancé.');			
					parent::$_response->addVar('txt_recalculer', 'Recalculer les temps de préparation.');			
				}
				else{
					foreach ($commandes as $commande){
						$erreur 			= 0;
						$tempsPdtsNonGeoloc	= 0;
						$idZone				= array();
						$idZones			= array();
						$client 			= $commande->getClient();
						$lsc 				= $commande -> selectLigne_commandes();
						$lscEtages 			= array();
						$nb_articles		= 0;
						foreach ($lsc as $lc){
							
							$produit = $lc->getProduit();
							$locs = $produit -> selectEtageres(); // on récupère le nombre de géolocalisation par produit et on prend la premiere
							if(count($locs) == 0){
								$erreur++;
								/* Forcer la géolocalisation des produits non géolocalisés dans la zone par défaut du premier étage */
								$etage 	= $etageDefaut;
								$zone 	= $zoneMagasin;
								$tempsPdtsNonGeoloc += $produit->getTempsMoyenAccess();
							}
							else{
								$zone 	= $locs[0] -> getZone();
								$etage 	= $zone->getEtage();
							}
							
						   //on compte le nombre de ligne de commande pour chaque étage
							if($idEtage == 0 || ($idEtage != 0 && $etage->getIdetage()== $idEtage)){
								if($lc->getPreparation() == null){
									$nb_articles += $lc->getQuantiteCommandee();
									$idZone[] = $zone->getIdzone();
									$idZones[$etage->getIdetage() . '_' . $etage->getLibelle()][] = $zone->getIdzone();
									if(isset($lscEtages[$etage->getIdetage() . '_' . $etage->getLibelle()]))
										$lscEtages[$etage->getIdetage() . '_' . $etage->getLibelle()]++;
									else
										$lscEtages[$etage->getIdetage() . '_' . $etage->getLibelle()] = 1;
								}
							}
						}
						ksort($lscEtages); 
						
						//on va construire notre tableau affichage en ajoutant des informations comme le temps, le nombre de ref, etc
						foreach($lscEtages as $etage=>$value){
							$temps_estime 	= 0; 
							foreach(array_unique($idZones[$etage]) as $id){
								if($value > 0){
									$z = Zone::load(parent::$_pdo,$id);
									$tp = Temps_Preparation::selectByCommandeAndZone(parent::$_pdo,$commande,$z);
									if ($tp !== null) {
										$temps = $tp->getDuree();
										if ($id == $zoneMagasin->getIdzone()) $temps += $tempsPdtsNonGeoloc;
										$temps_estime += $temps;
									} else {
										// TODO
									}
								}

							}
							// $affichage[substr($etage,strpos($etage,'_')+1)][] = array($commande,$client,$value,count($lsc),substr($etage,0,strpos($etage,'_')),$temps_estime,$erreur,implode('-',array_unique($idZone)),$nb_articles);
							$affichage[substr($etage,strpos($etage,'_')+1)][] = array($commande,$client,$value,count($lsc),substr($etage,0,strpos($etage,'_')),$temps_estime,$erreur,implode('-',array_unique($idZone)),$nb_articles);
							$etages[substr($etage,strpos($etage,'_')+1)]	  = substr($etage,0,strpos($etage,'_'));						
							parent::$_response->addVar('arrayEtages'				, $etages);			
							
						}
					}
				}
			}			
			else if($type == 'multiCommandes' || $type == 'monoCommande' || $type == 'monoZone' || $type == 'multiZones'){
				$_SESSION['temps_preparations_total']=0;
				$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
				$zoneMagasin  = Zone::loadZoneMagasinByEtage(parent::$_pdo, $etageDefaut->getIdetage());
				$nbPrepas = array();
				if($type == 'multiCommandes'){
					$preparations 	= $_SESSION['array_preparations']['multiCommandes'];					
				}
				else if($type == 'monoCommande'){
					$preparations 	= $_SESSION['array_preparations']['monoCommande'];
				}
				else if($type == 'monoZone'){
					$preparations 	= $_SESSION['array_preparations']['monoZone']; 
				}
				else if($type == 'multiZones'){
					$preparations 	= $_SESSION['array_preparations']['multiZones']; 			
				}
				
				$nb = count($_SESSION['array_preparations']['multiCommandes']);
				$nbPrepas['multiCommandes']	= ($nb <= 1) ? $nb .' '. gettext('bon de pr&eacute;paration') : $nb .' '. gettext('bons de pr&eacute;paration');
				$nb = count($_SESSION['array_preparations']['monoCommande']);
				$nbPrepas['monoCommande']	= ($nb <= 1) ? $nb .' '. gettext('bon de pr&eacute;paration') : $nb .' '. gettext('bons de pr&eacute;paration');
				$nb = count($_SESSION['array_preparations']['monoCommande']);
				$nbPrepas['monoCommande']	= ($nb <= 1) ? $nb .' '. gettext('bon de pr&eacute;paration') : $nb .' '. gettext('bons de pr&eacute;paration');
				$nb = count($_SESSION['array_preparations']['monoZone']);
				$nbPrepas['monoZone']	= ($nb <= 1) ? $nb .' '. gettext('bon de pr&eacute;paration') : $nb .' '. gettext('bons de pr&eacute;paration');
				$nb = count($_SESSION['array_preparations']['multiZones']);
				$nbPrepas['multiZones']	= ($nb <= 1) ? $nb .' '. gettext('bon de pr&eacute;paration') : $nb .' '. gettext('bons de pr&eacute;paration');
				
				parent::$_response->addVar('arrayNbPrepas',$nbPrepas);
				$affichage 		= array();
			$letemps=array();
				foreach($preparations as $key=>$preparation){ 
					$commandes 	= array();
					$lscTotal  	= array();
					foreach($preparation as $commande){
						$strZone	= '';
						$zones		= array();
						$tempsPdtsNonGeoloc = 0;
						$arrayPdtsNonGeoloc = array();
						$idCommande = $commande[0];
						$idZones	= $commande[1];
						
						$_SESSION['notAffectedYet'][] = $idCommande;
						
						$commande 	= Commande::load(parent::$_pdo, $idCommande);
						$allLignes 	= $commande -> selectLigne_commandes();
						$lsc		= array();
						$nb_articles = 0;
						$nb_ref_restant = 0;
						// Chargement des produits se trouvant dans deux endroits différents 
						$excepts	= array();
						
						foreach($allLignes as $ligne){
							$produit = $ligne->getProduit();
							$etageres 	= $produit->selectEtageres();
							if(count($etageres) > 1){
								// Produits géolocalisés plus d'une fois  
								$zone		= $etageres[0]->getZone(); 
								$excepts[] = array($produit, $zone->getIdzone());
							}
							
							else if (count($etageres) == 0 ){
								// Produits non géolocalisés 
								$arrayPdtsNonGeoloc[] = $ligne;
								$tempsPdtsNonGeoloc += $produit->getTempsMoyenAccess();
							}
						}
						
						 
						if(count($idZones) == 0){ 
							$strZone = gettext('Toutes zones');
							$zones = Zone::loadAllEtage(parent::$_pdo,$idEtage);
						}
						else{
							foreach($idZones as $idZone){
								$zone = Zone::load(parent::$_pdo,$idZone);
								$zones[] = $zone;
								$strZone .= $zone->getLibelle() . ' - ';
							}
							$strZone = substr($strZone,0,-2);
							if(strlen($strZone) > 30)
								$strZone = substr($strZone,0,30).'...';
						}
						foreach($zones as $zone){ 
							$lsc  = array_merge($lsc,$commande -> selectLigne_commandesByZone($zone,$excepts));
							if ($zone->getIdzone() == $zoneMagasin->getIdzone()){ $lsc = array_merge($lsc, $arrayPdtsNonGeoloc); }
						}
					 
						foreach($lsc as $lc){
							if($lc->getPreparation() == null){ 
								$nb_articles += $lc->getQuantiteCommandee();
								$nb_ref_restant ++;
							}
						}
						 
						unset($allLignes);
						$lscTotal = array_merge($lsc,$lscTotal);
						$client 	= $commande->getClient();
						$commandes[] = array($commande, $client, count($lsc),$nb_articles,$nb_ref_restant,$strZone);
					
					}
					$temps = 0;
					//if (parent::$_request->getVar('optimisation_temps') != 1){
						$temps	= self::getTempsPreparation($lscTotal);
					//}
					$_SESSION['temps_preparations'][$key] = $temps;
					$_SESSION['temps_preparations_total'] += $temps;
					$affichage[] 						 = array($commandes,$temps);
				}
				
				$_SESSION['idEtage'] = $idEtage;
				$_SESSION['type'] = $type;
			}  
			
		   
			parent::$_response->addVar('txt_titre'				, gettext('Affectation manuelle des commandes aux pr&eacute;parateurs'));
			parent::$_response->addVar('txt_conf'				, gettext('Configuration des param&egrave;tres de l\'affectation'));
			parent::$_response->addVar('txt_num_com'			, gettext('N&deg; commande'));
			parent::$_response->addVar('txt_cli_com'			, gettext('Client (Soci&eacute;t&eacute;)'));  
			parent::$_response->addVar('txt_date_co_com'		, gettext('Date de commande'));
			parent::$_response->addVar('txt_date_li_com'		, gettext('Date de livraison'));
			parent::$_response->addVar('txt_lib_zone'			, gettext('Libell&eacute;'));
			parent::$_response->addVar('txt_details'			, gettext('D&eacute;tails'));
			parent::$_response->addVar('txt_typeLivraison'		, gettext('Type de livraison'));
			parent::$_response->addVar('txt_bon_preparation'	, gettext('Bon de pr&eacute;paration'));
			parent::$_response->addVar('txt_dateMaxPrepa'		, gettext('Date max de pr&eacute;paration'));
			parent::$_response->addVar('txt_dateLivraison'		, gettext('Date de livraison'));
			parent::$_response->addVar('txt_nbRefAAffectees'	, gettext('Nb r&eacute;f&eacute;rences &agrave; affecter'));
			parent::$_response->addVar('txt_nbProdAAffectees'	, gettext('Nb articles &agrave; affecter'));
			parent::$_response->addVar('txt_nbRefTotal'			, gettext('Nb r&eacute;f&eacute;rences total'));
			parent::$_response->addVar('txt_com_attente'		, gettext('Commande(s) en attente d\'affectation'));
			parent::$_response->addVar('txt_no_com'				, gettext('Aucune commande trouv&eacute;e en attente d\'affectation'));
			parent::$_response->addVar('txt_filtrer'			, gettext('Filtrer'));
			parent::$_response->addVar('txt_filtre'				, gettext('Filtre'));
			parent::$_response->addVar('txt_mode'				, gettext('Mode d\'affectation'));
			parent::$_response->addVar('txt_effacer'			, gettext('Effacer'));
			parent::$_response->addVar('txt_produits'			, gettext('r&eacute;f&eacute;rences'));
			parent::$_response->addVar('txt_produit'			, gettext('r&eacute;f&eacute;rence'));
			parent::$_response->addVar('txt_zone'				, gettext('Zone'));
			parent::$_response->addVar('txt_commande'			, gettext('Commande'));
			parent::$_response->addVar('txt_produits_total'		, gettext('Nombre de r&eacute;f&eacute;rences total'));
			parent::$_response->addVar('txt_lien_groupe_com'	, gettext('Grouper par commandes'));
			parent::$_response->addVar('txt_lien_groupe_zon'	, gettext('Grouper par zones'));
			parent::$_response->addVar('txt_choix_etape'		, gettext('Prochaine &eacute;tape'));
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));
			parent::$_response->addVar('txt_seconde_etape'		, gettext('<b>Seconde Etape:</b> S&eacute;lection du mode de pr&eacute;paration'));
			parent::$_response->addVar('txt_etape_selection'	, gettext('Passer &agrave; l\'&eacute;tape 2'));
			parent::$_response->addVar('txt_etape_suivante'		, gettext('Confirmation et s&eacute;lection du pr&eacute;parateur'));
			parent::$_response->addVar('txt_tempsEstime'		, gettext('Temps estim&eacute;'));
			parent::$_response->addVar('txt_total_temps'		, gettext('La dur&eacute;e de pr&eacute;paration en mode mono commande pour les commandes s&eacute;lectionn&eacute;es est estim&eacute;e &agrave; '));
			parent::$_response->addVar('txt_optimisation_temps'	, gettext('pas d\'optimisation du temps de pr&eacute;paration '));
			parent::$_response->addVar('txt_calculer_options'	, gettext('Tous les modes'));
			parent::$_response->addVar('txt_mono_commandes'		, gettext('Mono commande'));
			parent::$_response->addVar('txt_multi_commandes'	, gettext('Multi commandes'));
			parent::$_response->addVar('txt_mono_zones'			, gettext('Mono zone'));
			parent::$_response->addVar('txt_multi_zones'		, gettext('Multi zones'));
			parent::$_response->addVar('txt_monomulti_both'		, gettext('Combin&eacute;'));
			parent::$_response->addVar('txt_mode_selection'		, gettext('Vous pouvez choisir un mode d\'affectation parmi les quatres disponibles'));
			parent::$_response->addVar('txt_etape_user'			, gettext('Passer &agrave; l\'&eacute;tape de s&eacute;l&eacute;ction du pr&eacute;parateur'));
			parent::$_response->addVar('txt_etape_user2'		, gettext('Vous avez s&eacute;l&eacute;ctionn&eacute;'));
			parent::$_response->addVar('txt_etape_user3'		, gettext('bon(s) de pr&eacute;paration'));
			parent::$_response->addVar('txt_mode_mono_commande'	, gettext('commande'));
			parent::$_response->addVar('txt_mode_multi_commande', gettext('commande'));
			parent::$_response->addVar('txt_mode_mono_zone'		, gettext('zone'));
			parent::$_response->addVar('txt_mode_multi_zone'	, gettext('zone'));
			parent::$_response->addVar('txt_mode_combine'		, gettext('combine'));
			parent::$_response->addVar('txt_au'					, gettext('au')); 
			parent::$_response->addVar('txt_du'					, gettext('du')); 
			parent::$_response->addVar('txt_selection_commande'	, gettext('Commencez tout d\'abord par s&eacute;lectionner une ou plusieurs commandes &agrave; affecter.'));
			parent::$_response->addVar('txt_selection'			, gettext('Tout s&eacute;lectionner'));
			parent::$_response->addVar('txt_deselection'		, gettext('Tout d&eacute;s&eacute;lectionner'));
			parent::$_response->addVar('txt_toutes_zones'		, gettext('Toutes zones'));
			parent::$_response->addVar('commandes'				, $affichage);
			parent::$_response->addVar('type'					, $type); 
			parent::$_response->addVar('idEtage'				, $idEtage); 
			parent::$_response->addVar('tpsTotal'				, $_SESSION['temps_preparations_total']); 
			
		}
	}
	
	private static function groupeCommandes($commandes,$nbMaxCommandes,$idEtage){
		$arrayPrepas 	= array();
		$arrayMulti		= array();
		$etage = Etage::load(parent::$_pdo, $idEtage);
		$zoneMagasin  = Zone::loadZoneMagasinByEtage(parent::$_pdo, $etage->getIdetage());
		/* Sélection des commandes à préparer en mono commande */
		
		 
		foreach ($commandes as $commande){
			if($nbMaxCommandes == 1){
				$arrayPrepas[] = array(array($commande),array());			
			}
			else{
				$multi = self::testGroupement(array(array($commande)),Zone::loadAllEtage(parent::$_pdo,$idEtage),$zoneMagasin,$nbMaxCommandes);
				
				if(!$multi[1]){
					if (!$multi[0]){ 
						$arrayPrepas[] = array(array($commande),array());
					}
					else{ 
						$arrayMulti[] = array($commande);
					}
				}
			}
		}
		
		/* Groupement des commandes */
		$fin = false; 
		while(!$fin && $nbMaxCommandes > 1){
			if (count($arrayMulti) == 0) $fin = true;
			else{
				$elem 	= array_pop($arrayMulti);
	 
				$indice = 0;
				$max	= null;
				if (count($elem) < $nbMaxCommandes){
					for($i=0; $i<count($arrayMulti); $i++){
						$retour = self::testGroupement(array($elem,$arrayMulti[$i]),Zone::loadAllEtage(parent::$_pdo,$idEtage),$zoneMagasin,$nbMaxCommandes);
						if(!$retour[1]){
							if ($retour[0]){
								if($max == null){
									$max = $arrayMulti[$i];
									$indice = $i;
								}
								else{
									if (self::isBetter($arrayMulti[$i],$max,Zone::loadAllEtage(parent::$_pdo,$idEtage),$zoneMagasin)){
										$max = $arrayMulti[$i];
										$indice = $i;
									}
								}
							}
						}
					}
				}
				
				if($max != null) {
					unset($arrayMulti[$indice]);
					$arrayMulti = array_merge($arrayMulti);
					$arrayMulti[] = array_merge($elem,$max);
				}
				else{
					$arrayPrepas[] = array($elem,array());
				}
			}
		}

		return $arrayPrepas;
	}
	
	private static function groupeMonoZone($commandes, $nbMaxCommandes,$idEtage){
		$arrayPrepas = array();
		$zones = array();
		$etage = Etage::load(parent::$_pdo, $idEtage);
		$zoneMagasin  = Zone::loadZoneMagasinByEtage(parent::$_pdo, $etage->getIdetage());
		
		foreach($commandes as $commande){
			$zones = array_merge($zones, Commande::getZonesCommande(parent::$_pdo, $commande,$idEtage));
		}
		$zones = array_unique($zones); 
		foreach($zones as $zone){
			$arrayMulti = array(); 
			foreach($commandes as $commande){			 
				$zonesCommande = Commande::getZonesCommande(parent::$_pdo, $commande,$idEtage); 
				if(in_array($zone,$zonesCommande)){
					if($nbMaxCommandes == 1){
						$multi = self::testGroupement(array(array($commande)), array($zone),$zoneMagasin,$nbMaxCommandes);
						
						if (!$multi[1])
							$arrayPrepas[] = array(array($commande),array($zone -> getIdzone()));			
					}
					else{
						$multi = self::testGroupement(array(array($commande)), array($zone),$zoneMagasin,$nbMaxCommandes);
						if (!$multi[1]){
							if (!$multi[0]){
								$arrayPrepas[] = array(array($commande),array($zone -> getIdzone()));
							}
							else{
								$arrayMulti[] = array($commande);
							}
						}
					}
				}
			} 
			/* Groupement des commandes */
			$fin = false; 
			while(!$fin && $nbMaxCommandes > 1){
				if (count($arrayMulti) == 0) $fin = true;
				else{
					$elem 	= array_pop($arrayMulti);
					$indice = 0;
					$max	= null;
					if (count($elem) < $nbMaxCommandes){
						for($i=0; $i<count($arrayMulti); $i++){
							$retour = self::testGroupement(array($elem,$arrayMulti[$i]), array($zone),$zoneMagasin, $nbMaxCommandes,$nbMaxCommandes);
							if (!$retour[1]){	
								if ($retour[0]){
									if($max == null){
										$max = $arrayMulti[$i];
										$indice = $i;
									}
									else{
										if (self::isBetter($arrayMulti[$i],$max, array($zone),$zoneMagasin)){
											$max = $arrayMulti[$i];
											$indice = $i;
										}
									}
								}
							}
						}
					}
					
					if($max != null) {
						unset($arrayMulti[$indice]);
						$arrayMulti = array_merge($arrayMulti);
						$arrayMulti[] = array_merge($elem,$max);
					}
					else{
						$arrayPrepas[] = array($elem,array($zone -> getIdzone()));
					}
				}
			}
				
		}
		
		return $arrayPrepas;
		
	}
	
	private static function groupeZones($preparations,$commandes, $nbMaxCommandes,$idEtage){
		$arrayPrepas 	= array();
		$arrayMulti		= array();
		$etage 			= Etage::load(parent::$_pdo, $idEtage);
		$zoneMagasin  	= Zone::loadZoneMagasinByEtage(parent::$_pdo, $etage->getIdetage());
		foreach($preparations as $key=>$preparation){ 
			$commandes 	= array();
			$zones	 	= array(); 
			$commandes	= $preparation[0];
			$idZones	= $preparation[1];  
			foreach($idZones as $idZone)
				$zones[] = Zone::load(parent::$_pdo,$idZone); 
			$arrayMulti[] = array(array($commandes,array_unique($zones)));				
		}	
		
		/* Groupement des bons de préparations entre eux*/
		$fin = false; 
		while(!$fin ){//&& $nbMaxCommandes > 1){
			if (count($arrayMulti) == 0) $fin = true;
			else{
				$elem 	= array_pop($arrayMulti);
				$indice = 0;
				$max	= null;
				
				$arrayCom = array();
				foreach($elem as $liste){
					foreach($liste[0] as $commande){
						$arrayCom[] = $commande->getIdCommande();
					}
				}
				$arrayCom = array_unique($arrayCom);
				
				if (count($arrayCom) < $nbMaxCommandes){	// Si la préparation contient moins de 4 commandes
					for($i=0; $i<count($arrayMulti); $i++){

						if (self::testGroupementZone(array($elem,$arrayMulti[$i]),$zoneMagasin, $nbMaxCommandes)){
							if($max == null){
								$max = $arrayMulti[$i];
								$indice = $i;
							}
							else{
								if (self::isBetterZone($arrayMulti[$i],$max,$zoneMagasin)){
									$max = $arrayMulti[$i];
									$indice = $i;
								}
							}
						}
					}
				}
				
				if($max != null) {
					unset($arrayMulti[$indice]);
					$arrayMulti = array_merge($arrayMulti);						 
					$arrayMulti[] = array_merge($elem, $max);
				}
				else{ 
					$arrayPrepas[] = $elem;
				}
			}
		}

		return $arrayPrepas;	
	}
	
	private static function testGroupementZone($arrayPrepas, $zoneMagasin, $nbMaxCommandes){
	 
		$tempsPrepa = 0;
		$nbArticles = 0;
		$nbRefs		= 0;
		
		$arrayCom = array();
		foreach($arrayPrepas as $elem){
			foreach($elem as $liste){
				foreach($liste[0] as $commande){
					$arrayCom[] = $commande->getIdCommande();
				}
			}
		}
		$arrayCom = array_unique($arrayCom);
		
		if (count($arrayCom) > $nbMaxCommandes) return false;
		
		
		foreach($arrayPrepas as $liste){
			foreach($liste as $listeCommande){
				foreach($listeCommande[0] as $commande){ 
					$arrayPdtsNonGeoloc = array(); 
					$lsc 		= array(); 
					$allLignes 	= $commande -> selectLigne_commandes();
					$excepts	= array();
					foreach($allLignes as $ligne){
						$produit 	= $ligne->getProduit();
						$etageres 	= $produit->selectEtageres();
						$nbGeoloc 	= count($etageres);
						if($nbGeoloc > 1){
							/* Produits géolocalisés plus d'une fois */
							$zoneExcepts 	= $etageres[0]->getZone(); 
							$excepts[] 		= array($produit, $zoneExcepts->getIdzone());
						}
						
						else if ($nbGeoloc == 0){
							/* Produits non géolocalisés */
							$arrayPdtsNonGeoloc[] = $ligne;
						}
					}
					unset($allLignes);
					foreach($listeCommande[1] as $zone){
							$lsczones = $commande -> selectLigne_commandesByZone($zone,$excepts);
							$lsc  = array_merge($lsc,$lsczones);
							if ($zone->getIdzone() == $zoneMagasin->getIdzone()) $lsc = array_merge($lsc, $arrayPdtsNonGeoloc);
						
					}
					$tempsPrepa += self::getTempsPreparation($lsc);
					foreach($lsc as $lc){
						if($lc->getPreparation() == null){
							$nbArticles += $lc->getQuantiteCommandee();
							$nbRefs	++;
						}
					}
				}
			}
		}
		
		$multi = true;
		
		if ($tempsPrepa > TEMPS_PREPA_MAX) {$multi = false;}
		if ($multi && $nbArticles > NB_ARTICLES_MAX) {$mutli = false;}
		if ($multi && $nbRefs > NB_REFERENCES_MAX) {$multi = false;}
	
		return $multi;
	}
	
	private static function testGroupement($arrayCommandes, $zones, $zoneMagasin, $nbMaxCommandes){ 
		
		if (count($arrayCommandes) > $nbMaxCommandes) return false;
		
		$tempsPrepa = 0;
		$nbArticles = 0;
		$nbRefs		= 0; 
		foreach($arrayCommandes as $liste){
			foreach($liste as $commande){
				$arrayPdtsNonGeoloc = array(); 
				$lsc 		= array(); 
				$allLignes 	= $commande -> selectLigne_commandes();
				$excepts	= array();
				foreach($allLignes as $ligne){
					$produit = $ligne->getProduit();
					$nbGeoloc = $produit->getNbGeolocalisation();
					if($nbGeoloc > 1){
						/* Produits géolocalisés plus d'une fois */
						$etageres 		= $produit->selectEtageres();
						$zoneExcepts 	= $etageres[0]->getZone(); 
						$excepts[] 		= array($produit, $zoneExcepts->getIdzone());
					}
					
					else if ($nbGeoloc == 0){
						/* Produits non géolocalisés */
						$arrayPdtsNonGeoloc[] = $ligne;
					}
				}
				unset($allLignes);
				foreach($zones as $zone){
						$lsczones = $commande -> selectLigne_commandesByZone($zone,$excepts);
						$lsc  = array_merge($lsc,$lsczones);
						if ($zone->getIdzone() == $zoneMagasin->getIdzone()) $lsc = array_merge($lsc, $arrayPdtsNonGeoloc);
					
				}
				$tempsPrepa += self::getTempsPreparation($lsc);
				foreach($lsc as $lc){
					if($lc->getPreparation() == null){
						$nbArticles += $lc->getQuantiteCommandee();
						$nbRefs	++;
					}
				}		 
			}
		}
		
		$vide = false;
		if ($nbRefs == 0) $vide = true;
		
		$multi = true;
		if ($tempsPrepa > TEMPS_PREPA_MAX) {$multi = false;}
		if ($multi && $nbArticles > NB_ARTICLES_MAX) {$mutli = false;}
		if ($multi && $nbRefs > NB_REFERENCES_MAX) {$multi = false;}
	
		return array($multi,$vide);
	} 

	private static function isBetterZone($arrayCommandes1, $arrayCommandes2, $zoneMagasin){
		$tempsPrepa1 	= 0;
		$nbArticles1 	= 0;
		$nbRefs1		= 0;
		 
		foreach($arrayCommandes1 as $listeCommande){
			foreach($listeCommande[0] as $commande){
				$arrayPdtsNonGeoloc = array();
				$allLignes 	= $commande -> selectLigne_commandes();
				$excepts	= array();
				$lsc = array();
				foreach($allLignes as $ligne){
					$produit = $ligne->getProduit();
					$nbGeoloc = $produit->getNbGeolocalisation();
					if($nbGeoloc > 1){
						/* Produits géolocalisés plus d'une fois */
						$etageres 		= $produit->selectEtageres();
						$zoneExcepts 	= $etageres[0]->getZone(); 
						$excepts[] 		= array($produit, $zoneExcepts->getIdzone());
					}
					
					else if ($nbGeoloc == 0){
						/* Produits non géolocalisés */
						$arrayPdtsNonGeoloc[] = $ligne;
					}
				}
				unset($allLignes);
				
				foreach($listeCommande[1] as $zone){
					$lsc  = array_merge($lsc,$commande -> selectLigne_commandesByZone($zone,$excepts));
					if ($zone->getIdzone() == $zoneMagasin->getIdzone()) $lsc = array_merge($lsc, $arrayPdtsNonGeoloc);
				}

				$tempsPrepa1 += self::getTempsPreparation($lsc);
				foreach($lsc as $lc){
					if($lc->getPreparation() == null){
						$nbArticles1 += $lc->getQuantiteCommandee();
					}
				}	
				// $nbArticles1 += $commande->getNbArticles();
				$nbRefs1	 += count($lsc);
			}
		}
		
		$tempsPrepa2 	= 0;
		$nbArticles2 	= 0;
		$nbRefs2		= 0;
		
		foreach($arrayCommandes2 as $listeCommande){
			foreach($listeCommande[0] as $commande){
				$arrayPdtsNonGeoloc = array();
				$allLignes 	= $commande -> selectLigne_commandes();
				$excepts	= array();
				$lsc = array();
				foreach($allLignes as $ligne){
					$produit = $ligne->getProduit();
					$nbGeoloc = $produit->getNbGeolocalisation();
					if($nbGeoloc > 1){
						/* Produits géolocalisés plus d'une fois */
						$etageres 		= $produit->selectEtageres();
						$zoneExcepts 	= $etageres[0]->getZone(); 
						$excepts[] 		= array($produit, $zoneExcepts->getIdzone());
					}
					
					else if ($nbGeoloc == 0){
						/* Produits non géolocalisés */
						$arrayPdtsNonGeoloc[] = $ligne;
					}
				}
				unset($allLignes);
				
				foreach($listeCommande[1] as $zone){
					$lsc  = array_merge($lsc,$commande -> selectLigne_commandesByZone($zone,$excepts));
					if ($zone->getIdzone() == $zoneMagasin->getIdzone()) $lsc = array_merge($lsc, $arrayPdtsNonGeoloc);
				}
				
				$tempsPrepa2 += self::getTempsPreparation($lsc);
				
				foreach($lsc as $lc){
					if($lc->getPreparation() == null){
						$nbArticles2 += $lc->getQuantiteCommandee();
					}
				}
				// $nbArticles2 += $commande->getNbArticles();
				$nbRefs2	 += count($lsc);
			}
		}
		
		$best = true;
		
		if ($tempsPrepa1 < $tempsPrepa2) $best = false;
		if ($best && $nbArticles1 < $nbArticles2) $best = false;
		if ($best && $nbRefs1 < $nbRefs2) $best = false;
		
		return $best;
	}
	
	private static function isBetter($arrayCommandes1, $arrayCommandes2, $zones, $zoneMagasin){
		$tempsPrepa1 	= 0;
		$nbArticles1 	= 0;
		$nbRefs1		= 0;
		
		foreach($arrayCommandes1 as $commande){
				$arrayPdtsNonGeoloc = array();
				$allLignes 	= $commande -> selectLigne_commandes();
				$excepts	= array();
				$lsc = array();
				foreach($allLignes as $ligne){
					$produit = $ligne->getProduit();
					$etageres = $produit->selectEtageres();
					$nbGeoloc = count($etageres);
					if($nbGeoloc > 1){
						/* Produits géolocalisés plus d'une fois */
						$zoneExcepts 	= $etageres[0]->getZone(); 
						$excepts[] 		= array($produit, $zoneExcepts->getIdzone());
					}
					
					else if ($nbGeoloc == 0){
						/* Produits non géolocalisés */
						$arrayPdtsNonGeoloc[] = $ligne;
					}
				}
				unset($allLignes);
				
				foreach($zones as $zone){
					$lsc  = array_merge($lsc,$commande -> selectLigne_commandesByZone($zone,$excepts));
					if ($zone->getIdzone() == $zoneMagasin->getIdzone()) $lsc = array_merge($lsc, $arrayPdtsNonGeoloc);
				}
			

			$tempsPrepa1 += self::getTempsPreparation($lsc);
			foreach($lsc as $lc){
				if($lc->getPreparation() == null){
					$nbArticles1 += $lc->getQuantiteCommandee();
				}
			}	 
			$nbRefs1	 += count($lsc);
		}
		
		$tempsPrepa2 	= 0;
		$nbArticles2 	= 0;
		$nbRefs2		= 0;
		
		foreach($arrayCommandes2 as $commande){
				$arrayPdtsNonGeoloc = array();
			$allLignes 	= $commande -> selectLigne_commandes();
			$excepts	= array();
			$lsc = array();
			foreach($allLignes as $ligne){
				$produit = $ligne->getProduit();
				$etageres = $produit->selectEtageres();
				$nbGeoloc = count($etageres);
				if($nbGeoloc > 1){
					/* Produits géolocalisés plus d'une fois */ 
					$zoneExcepts 	= $etageres[0]->getZone(); 
					$excepts[] 		= array($produit, $zoneExcepts->getIdzone());
				}
				
				else if ($nbGeoloc == 0){
					/* Produits non géolocalisés */
					$arrayPdtsNonGeoloc[] = $ligne;
				}
			}
			unset($allLignes);
			
			foreach($zones as $zone){
				$lsc  = array_merge($lsc,$commande -> selectLigne_commandesByZone($zone,$excepts));
				if ($zone->getIdzone() == $zoneMagasin->getIdzone()) $lsc = array_merge($lsc, $arrayPdtsNonGeoloc);
			}
			
			$tempsPrepa2 += self::getTempsPreparation($lsc);
			
			foreach($lsc as $lc){
				if($lc->getPreparation() == null){
					$nbArticles2 += $lc->getQuantiteCommandee();
				}
			} 
			$nbRefs2	 += count($lsc);
		}
		
		$best = true;
		
		if ($tempsPrepa1 < $tempsPrepa2) $best = false;
		if ($best && $nbArticles1 < $nbArticles2) $best = false;
		if ($best && $nbRefs1 < $nbRefs2) $best = false;
		
		return $best;
	}
	
	private static function getTempsPreparation($lignes_commande){
		$lsc = array();
		foreach($lignes_commande as $lc){
			if($lc->getPreparation() == null)
				$lsc[] = $lc;
		}
		return getTemps($lsc);
		
	}
	
	public static function choixutilisateur(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			
			parent::$_response->addVar('txt_titre'	, gettext('Affectation manuelle des commandes &agrave; un pr&eacute;parateur'));
			
			$_SESSION['retour'] = true;
			
			$bonsPrepas 			= parent::$_request->getVar('bons');
			$type					= parent::$_request->getVar('type');
			$idEtage				= parent::$_request->getVar('etage');
			
			$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
			$zoneMagasin  = Zone::loadZoneMagasinByEtage(parent::$_pdo, $etageDefaut->getIdetage());
			
			if ($bonsPrepas != ''){
				$preparations = array();
				foreach($bonsPrepas as $bon){
					if((isset($_SESSION['array_preparations'][$type][$bon]) && !empty($_SESSION['array_preparations'][$type][$bon])))
						$preparations[$bon] = $_SESSION['array_preparations'][$type][$bon];
				}

				$arrayIdCommandes 	= array();

				$temps_estime_total		= 0;

				foreach($preparations as $key=>$preparation){ 
					$commandes 			= array();
					$lscTotal  			= array();
					$lignes_commande	= array();
					
					foreach($preparation as $commande){
						$strZone	= '';
						$zones		= array();
						$tempsPdtsNonGeoloc = 0;
						$arrayPdtsNonGeoloc = array();
						$idCommande = $commande[0];
						$idZones	= $commande[1]; 
						
						$arrayIdCommandes[] = $idCommande;
						
						$commande 	= Commande::load(parent::$_pdo, $idCommande);
						$allLignes 	= $commande -> selectLigne_commandes();
						$lsc		= array();
						$nb_articles = 0;
						$nb_ref_restant = 0;
						/* Chargement des produits se trouvant dans deux endroits différents */
						$excepts	= array();
						foreach($allLignes as $ligne){
							$produit = $ligne->getProduit();
							$etageres 	= $produit->selectEtageres();
							if(count($etageres) > 1){
								/* Produits géolocalisés plus d'une fois */ 
								$zone		= $etageres[0]->getZone(); 
								$excepts[] = array($produit, $zone->getIdzone());
							}
							
							else if (count($etageres) == 0 ){
								/* Produits non géolocalisés */
								$arrayPdtsNonGeoloc[] = $ligne;
								$tempsPdtsNonGeoloc += $produit->getTempsMoyenAccess();
							}
						}
						if(count($idZones) == 0){ 
							$strZone = gettext('Toutes zones');
							$zones = Zone::loadAllEtage(parent::$_pdo,$idEtage);
						}
						else{
							foreach($idZones as $idZone){
								$zone = Zone::load(parent::$_pdo,$idZone);
								$zones[] = $zone;
								$strZone .= $zone->getLibelle() . ' - ';
							}
							$strZone = substr($strZone,0,-2);
							if(strlen($strZone) > 30)
								$strZone = substr($strZone,0,30).'...';
						}
						foreach($zones as $zone){ 
							$lsc  = array_merge($lsc,$commande -> selectLigne_commandesByZone($zone,$excepts));
							if ($zone->getIdzone() == $zoneMagasin->getIdzone()){ $lsc = array_merge($lsc, $arrayPdtsNonGeoloc); }
						}
					 
						foreach($lsc as $lc){
							if($lc->getPreparation() == null){ 
								$nb_articles += $lc->getQuantiteCommandee();
								$nb_ref_restant ++;
								$lignes_commande[] = $lc;
							}
						}
						 
						
						$lscTotal = array_merge($lsc,$lscTotal);
						$client 	= $commande->getClient();
						$commandes[] = array($commande, $client, $nb_ref_restant,$nb_articles,count($allLignes),$strZone);
						unset($allLignes);
					}
					
					$temps		 		= $_SESSION['temps_preparations'][$key];
					$temps_estime_total += $temps;
					
					$affichage[] 	= array($commandes,$temps);
					
					foreach($lignes_commande as $lc){
						$_SESSION['lignes_co'][$key][] = $lc->getIdLigne();
					}
					
					$min 						= Commande::getMinDateLivraison(parent::$_pdo,array_unique($arrayIdCommandes)); 				
					$date_limite				= $min['MIN'] - DELAI_AVANT_LIVRAISON;
					$_SESSION['min'][$key]		= $date_limite;
					$_SESSION['duree'][$key]	= $temps;
				}
				
				if(empty($arrayIdCommandes))
						header('Location:' . APPLICATION_PATH .'affectation/manuelle');
				else{
				
					//gestion des durées et  des utilisateurs disponibles	
					$min 				= Commande::getMinDateLivraison(parent::$_pdo,array_unique($arrayIdCommandes)); 				
					$date_limite		= $min['MIN'] - DELAI_AVANT_LIVRAISON;
					$current_time		= time();
					$users				= Utilisateur::loadAll(parent::$_pdo);
					
					$_SESSION['arrayIdCommandes']	= array_unique($arrayIdCommandes);
					
					if($current_time + $temps_estime_total > $date_limite){
						parent::$_response->addVar('affectation_valide'		, 0);
						parent::$_response->addVar('txt_error'				, gettext('La pr&eacute;paration est trop longue pour respecter les d&eacute;lais'));
					}
					else{
						$users	= Planning::selectByTimestamp(parent::$_pdo,$current_time,$date_limite);
						if(count($users) == 0){
							parent::$_response->addVar('affectation_valide'		, 0);
							parent::$_response->addVar('txt_error'				, gettext('Aucun utilisateur ne travaille dans de maintenant jusqu\'&agrave; la date limite de pr&eacute;paration'));
						}
						else{
							parent::$_response->addVar('affectation_valide'		, 1);
							parent::$_response->addVar('txt_error'				, '');
						}
					}
					
					$_SESSION['bonsPrepas'] 		= $bonsPrepas;
					$_SESSION['type']	 			= $type;
					$_SESSION['modePreparation']	= $type;
					$_SESSION['idEtage']			= $idEtage;
					
					switch($type){
						case 'monoCommande':
							$modePrepa = 'Mono commande';
							break;
						
						case 'multiCommandes':
							$modePrepa = 'Multi commandes';
							break;
							
						case 'monoZone':
							$modePrepa = 'Mono zone';
							break;
							
						case 'multiZones':
							$modePrepa = 'Multi zones';
							break;
					}
					$_SESSION['mode_de_prepa'] = $modePrepa;
					
					parent::$_response->addVar('txt_liste_prepas'		, gettext('Liste des bons de pr&eacute;paration &agrave; affecter'));
					parent::$_response->addVar('txt_mode_prepa'			, gettext('Mode de pr&eacute;paration'));
					parent::$_response->addVar('txt_num_com'			, gettext('N&deg; commande'));
					parent::$_response->addVar('txt_cli_com'			, gettext('Client (Soci&eacute;t&eacute;)'));  
					parent::$_response->addVar('txt_date_co_com'		, gettext('Date de commande'));
					parent::$_response->addVar('txt_date_li_com'		, gettext('Date de livraison'));
					parent::$_response->addVar('txt_bon_preparation'	, gettext('Bon de pr&eacute;paration'));
					parent::$_response->addVar('txt_dateMaxPrepa'		, gettext('Date max de pr&eacute;paration'));
					parent::$_response->addVar('txt_typeLivraison'		, gettext('Type de livraison'));
					parent::$_response->addVar('txt_dateLivraison'		, gettext('Date de livraison'));
					parent::$_response->addVar('txt_nbRefAAffectees'	, gettext('Nb r&eacute;f&eacute;rences &agrave; affecter'));
					parent::$_response->addVar('txt_nbProdAAffectees'	, gettext('Nb articles &agrave; affecter'));
					parent::$_response->addVar('txt_nbRefTotal'			, gettext('Nb r&eacute;f&eacute;rences total'));
					parent::$_response->addVar('txt_tempsEstime'		, gettext('Temps estim&eacute;'));
					parent::$_response->addVar('txt_choix_prep'			, gettext('Choisissez le pr&eacute;parateur'));
					parent::$_response->addVar('txt_choix'				, gettext('Choisir un pr&eacute;parateur'));
					parent::$_response->addVar('txt_preparateur'		, gettext('Pr&eacute;parateur'));
					parent::$_response->addVar('txt_affecter'			, gettext('Affecter'));
					parent::$_response->addVar('txt_num_com'			, gettext('N&deg; commande'));
					parent::$_response->addVar('txt_produits'			, gettext('r&eacute;f&eacute;rences s&eacute;lectionn&eacute;es'));
					parent::$_response->addVar('txt_produit'			, gettext('r&eacute;f&eacute;rence s&eacute;lectionn&eacute;e'));
					parent::$_response->addVar('txt_au_total'			, gettext('au total'));
					parent::$_response->addVar('txt_commande'			, gettext('Commande(s) s&eacute;lectionn&eacute;e(s)'));
					parent::$_response->addVar('txt_date_max'			, gettext('Date limite de pr&eacute;paration le'));
					parent::$_response->addVar('txt_duree_appro'		, gettext('Dur&eacute;e approximative'));
					parent::$_response->addVar('txt_zone'				, gettext('zone'));
					parent::$_response->addVar('txt_pour'				, gettext('pour'));
					parent::$_response->addVar('txt_doit_livrer_le'		, gettext('- livraison le'));
					parent::$_response->addVar('txt_etat'				, 1);
					parent::$_response->addVar('txt_retour'				, gettext('Revenir &agrave; l\'&eacute;tape du choix de mode de pr&eacute;paration'));
					parent::$_response->addVar('users'					, array_unique($users)); 
					parent::$_response->addVar('affichage'				, $affichage);
					parent::$_response->addVar('temps_prepa'			, $temps_estime_total);
					parent::$_response->addVar('date_limite'			, $date_limite);
					parent::$_response->addVar('type'					, $type);
					parent::$_response->addVar('modePrepa'				, $modePrepa);
					parent::$_response->addVar('idEtage'				, $idEtage);
					parent::$_response->addVar('txt_details'			, gettext('D&eacute;tails'));
				}

			}
			else if(parent::$_request->getVar('submit_user') != ''){ 
				//s'il y a eu au moins une zone sélectionnée et un préparateur, on crée la préparation
				if((isset($_SESSION['min']) && !empty($_SESSION['min'])) ||
					(isset($_SESSION['arrayIdCommandes']) && !empty($_SESSION['arrayIdCommandes'])) ||
					(isset($_SESSION['lignes_co']) && !empty($_SESSION['lignes_co'])) ||
					(isset($_SESSION['mode_de_prepa']) && !empty($_SESSION['mode_de_prepa'])) ||
					(isset($_SESSION['duree']) && !empty($_SESSION['duree']))
					){
					$arrayIdCommandes 	= $_SESSION['arrayIdCommandes'];
					$duree				= $_SESSION['duree'];
					$min				= $_SESSION['min'];
					$lignes_commande	= $_SESSION['lignes_co'];
					$mode_de_prepa		= $_SESSION['mode_de_prepa'];
				}
				else
					throw new Exception(gettext('Param&egrave;tre invalide'),5);
				
				//if(($user = Utilisateur::load(parent::$_pdo, parent::$_request->getVar('utilisateur'),true,true)) == null)
					//throw new Exception(gettext('Param&egrave;tre invalide'),5);
				$utilisateurs = parent::$_request->getVar('utilisateur');
				foreach($lignes_commande as $key => $lignesPrepas){
					$user = Utilisateur::load(parent::$_pdo, $utilisateurs[$key],true,true);
					$prepa = Preparation::create(parent::$_pdo,$user,$duree[$key],$min[$key],0,0,$mode_de_prepa,'');
					if($prepa == null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
				
					foreach($lignesPrepas as $lcID){
						$lc = Ligne_Commande::load(parent::$_pdo,$lcID);
						$lc->setPreparation($prepa);
					}
				}
				
				
				/* mise à jour de l'état de la COMMANDE si toutes les lignes de la commande sont affectées*/
				foreach($arrayIdCommandes as $idCommande){							
					if(($commande = Commande::load(parent::$_pdo, $idCommande)) == null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);

					if($commande -> isAllAffected())
						$commande->setEtatCommande(1);
				
				}
				unset($_SESSION['min']);
				unset($_SESSION['duree']);
				unset($_SESSION['lignes_co']);
				unset($_SESSION['mode_de_prepa']);
				
				foreach($_SESSION['bonsPrepas'] as $bon){
					unset($_SESSION['array_preparations'][$_SESSION['type']][$bon]);
				}
				unset($_SESSION['notAffectedYet']);
				
				if (empty($_SESSION['array_preparations'][$_SESSION['type']])){
					unset($_SESSION['type']);
					unset($_SESSION['modePreparation']);
					unset($_SESSION['idEtage']);
					unset($_SESSION['array_preparations']);
					unset($_SESSION['retour']);
				}
				else{ 
					foreach($_SESSION['array_preparations'][$_SESSION['type']] as $preparation){
						foreach($preparation as $commande){
							$_SESSION['notAffectedYet'][] = $commande[0];
						}
					} 
					unset($_SESSION['array_preparations']);	// On recalcule pour trouver une meilleure affectation (à commenter si on veut garder la même affectation)
				}

				$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Affectation de la pr&eacute;paration ') . $prepa->getIdpreparation() . gettext('&agrave; l\'utilisateur') . $user->getlogin() . gettext(' par ') . $_SESSION['user_login']."\r\n";
				$logFile = '../application/logs/'.date('m-Y').'-affectation.log';
				writeLog($log, $logFile);
				
				parent::$_response->addVar('txt_etat'		, 2);
				header('Location:' . APPLICATION_PATH .'affectation/manuelle');
			}
			else{
				 
				parent::$_response->addVar('txt_error'			, gettext('Aucune case n\'a &eacute;t&eacute; coch&eacute;e &agrave; l\'&eacute;tape pr&eacute;c&eacute;dente'));
				parent::$_response->addVar('txt_retour'			, gettext('Retour'));
				parent::$_response->addVar('txt_etat'			, 0);
			}
		}
	}
	
	public static function all(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			/* Génération des fichiers vers le PDA au format txt */
			if(parent::$_request->getVar('submit') && parent::$_request->getVar('selected') != ''){
					if(parent::$_request->getVar('exportPDA')){
				
					$time		= time();
					$arrayIdPreparation = parent::$_request->getVar('selected');
					$matrices	= getMatricePath();
					$nb_prepas = count($arrayIdPreparation);
					
					/* on récupère les id de chaque préparation */ 
					foreach($arrayIdPreparation as $idPreparation){
						$contenu_fichier_txt = '';
						if(($prepa = Preparation::load(parent::$_pdo,$idPreparation))==null)
							throw new Exception(gettext('Param&egrave;tre invalide'),5);
						$prepa->setTypePreparation('PDA');
						$commandes = $prepa->getCommandes();
						
						if (count($commandes) == 1){
							$client				= $commandes[0]->getClient();
							
							$dateHeureCommande 		= $commandes[0]->getDateCommande();
							$numClient 				= $client->getIdclient();
							$nomClient 				= $client->getNom();
							$prenomClient			= $client->getPrenom();
							$adresseFacturation		= $client->getLigneAdresseFacturation();
							$codePostalFacturation	= $client->getCodePostalFacturation();
							$villeFacturation		= $client->getMunicipaliteFacturation();
							$telephoneClient		= $client->getTelephone();
							$mailClient				= '';
							$modePaiement			= '';
							$encaisse				= '';
							$modeLivraison			= '';
							$etageLivraison			= '';
							$adresseLivraison		= $commandes[0]->getLigneAdresseLivraison();
							$codePostalLivraison	= $commandes[0]->getCodePostalLivraison();
							$villeLivraison			= $commandes[0]->getMunicipaliteLivraison();
							$commentaire			= $commandes[0]->getCommentaireClient();
							
						}
						else{
							$dateHeureCommande 		= '';
							$numClient 				= '';
							$nomClient 				= '';
							$prenomClient			= '';
							$adresseFacturation		= '';
							$codePostalFacturation	= '';
							$villeFacturation		= '';
							$telephoneClient		= '';
							$mailClient				= '';
							$modePaiement			= '';
							$encaisse				= '';
							$modeLivraison			= '';
							$etageLivraison			= '';
							$adresseLivraison		= '';
							$codePostalLivraison	= '';
							$villeLivraison			= '';
							$commentaire			= '';
						}
						
						$contenu_fichier_txt .= APPLICATION_PREFIXE . $prepa->getIdpreparation() . "\r\n";
						$contenu_fichier_txt .= $dateHeureCommande . "\r\n"; //date heure commande
						$contenu_fichier_txt .= $numClient . "\r\n"; // num client
						$contenu_fichier_txt .= $nomClient . "\r\n"; // nom
						$contenu_fichier_txt .= $prenomClient . "\r\n"; // prenom
						$contenu_fichier_txt .= $adresseFacturation . "\r\n"; // adresse facturation
						$contenu_fichier_txt .= $codePostalFacturation . "\r\n"; // CP facturation
						$contenu_fichier_txt .= $villeFacturation . "\r\n"; // ville facturation	
						$contenu_fichier_txt .= $telephoneClient . "\r\n"; // tel client
						$contenu_fichier_txt .= $mailClient . "\r\n"; // email
						$contenu_fichier_txt .= $modePaiement . "\r\n"; // mode paiement
						$contenu_fichier_txt .= $encaisse . "\r\n"; // encaissé
						$contenu_fichier_txt .= $modeLivraison . "\r\n"; // mode de livraison
						$contenu_fichier_txt .= date('d/m/Y H:i',$prepa->getDate_preparation())."\r\n"; //date livraison
						$contenu_fichier_txt .= $etageLivraison . "\r\n"; // etage livraison
						$contenu_fichier_txt .= $adresseLivraison . "\r\n"; // adresse livraison
						$contenu_fichier_txt .= $codePostalLivraison . "\r\n"; // cp livraison
						$contenu_fichier_txt .= $villeLivraison . "\r\n"; // ville livraison
						$contenu_fichier_txt .= $commentaire . "\r\n"; // commentaires client
						$contenu_fichier_txt .= '******'."\r\n";
						$cpt = 0;
						
						/* ordonne la liste des produits de la préparation */
						$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
						$erreur = false;
						$lsc = $prepa->selectLigne_commandes();
						$etage = null;
						$first = true;
						foreach($lsc as $lc){
							$produit = $lc->getProduit();
							$locs = $produit -> selectEtageres();
							if(count($locs) == 0){
								$etage 	= $etageDefaut;
							}
							else{
								$zone 	= $locs[0]->getZone();
								$etage 	= $zone->getEtage();
							}
							if($first){
								$idEtage = $etage->getIdetage();
								$first = false;
							}
							if($etage->getIdetage() != $idEtage)
								$erreur = true;
						}
						
						if(!$erreur){
							$retour 				= getSegmentsAPasser($lsc,false,$matrices[0],$matrices[1],$idEtage);					
							$arraySegmentAPasser 	= $retour[0];
							$keys 					= $retour[1];
							$qteTotale				= $retour[2];
							$nonGeoloc				= $retour[3];
							$indice1 				= $retour[5];
							$indice2				= $retour[6];
							$usePvc					= true;
							
							$retourListeDePoints 	= getListePoints($arraySegmentAPasser,$matrices[0],$matrices[1],$keys,$indice1,$indice2,$usePvc);
							
							/* on parcourt les lignes de commande (lc) pour extraire les informations nécessaires */ 
							foreach($retourListeDePoints[1] as $segment){ 
								 if($segment[2] != null){
									foreach($segment[2] as $lc){
										if(($produit = Produit::load(parent::$_pdo,$lc->getProduit()->getIdProduit()))==null)
											throw new Exception(gettext('Param&egrave;tre invalide'),5);
										
										$contenu_fichier_txt .= $lc->getIdLigne() . "\t";
										$contenu_fichier_txt .= $produit->getCodeProduit() . "\t";
										$contenu_fichier_txt .= unhtmlentities($produit->getLibelle()) . "\t";
										$contenu_fichier_txt .= $produit->getEstPoidsVariable() . "\t";
										$contenu_fichier_txt .= $lc->getQuantiteCommandee() . "\t";
										$cpt += $lc->getQuantiteCommandee();
										$contenu_fichier_txt .= $lc->getPrixUnitaireTTC() . "\t"; //prix unitaire ou prix unitaire * nb article
										$etageres	= $lc->getProduit()->selectEtageres();
										$segment	= $etageres[0]->getSegment();
										$rayon		= $segment->getRayon();
										$codeGeoloc = '';
										if ($rayon->getLocalisation() == null)
											$codeGeoloc .= substr($rayon->getLibelle(),0,15) . '-';
										else
											$codeGeoloc .= $rayon->getLocalisation() . '-'; 
										$codeGeoloc .= Segment::getPosition(parent::$_pdo,$segment->getIdsegment(),$rayon->getIdrayon()) . '-';
										$codeGeoloc .= Etagere::getPosition(parent::$_pdo,$etageres[0]->getIdetagere(), $segment->getIdsegment());
										$contenu_fichier_txt .= $codeGeoloc . "\t"; // code géolocalisation
										
										$eans	=	$produit->selectEans();
										if(count($eans) > 0)
											$contenu_fichier_txt .= $eans[0]->getEan() . "\t"; //ean maitre, il y en a forcément un 
										else	
											$contenu_fichier_txt .= "\t";
										
										if(count($eans) > 1){
											for($i = 1; $i < count($eans); $i++){
												$contenu_fichier_txt .= $eans[$i]->getEan() . "\t"; //ean pas maitre s'il y en a
											}
										}
										else{						
											$contenu_fichier_txt .= '' . "\t"; //liste ean vide
										}
										
										$contenu_fichier_txt .= '***'."\r\n"; //fin d'une ligne de commande								
									}
								}
							}
							
							foreach($nonGeoloc as $ligneNonGeoloc){
								$contenu_fichier_txt .= $ligneNonGeoloc[1]->getIdLigne() . "\t";
								$contenu_fichier_txt .= $ligneNonGeoloc[0]->getCodeProduit() . "\t";
								$contenu_fichier_txt .= unhtmlentities($ligneNonGeoloc[0]->getLibelle()) . "\t";
								$contenu_fichier_txt .= $ligneNonGeoloc[0]->getEstPoidsVariable() . "\t";
								$contenu_fichier_txt .= $ligneNonGeoloc[1]->getQuantiteCommandee() . "\t";
								$cpt += $ligneNonGeoloc[1]->getQuantiteCommandee();
								$contenu_fichier_txt .= $ligneNonGeoloc[1]->getPrixUnitaireTTC() . "\t";
								$contenu_fichier_txt .= 'Non géolocalisé' . "\t";
								$eans	=	$ligneNonGeoloc[0]->selectEans();
								if(count($eans) > 0)
									$contenu_fichier_txt .= $eans[0]->getEan() . "\t"; //ean maitre, il y en a forcément un 
								else	
									$contenu_fichier_txt .= "\t";
								if(count($eans) > 1){
									for($i = 1; $i < count($eans); $i++){
										$contenu_fichier_txt .= $eans[$i]->getEan() . "\t"; //ean pas maitre s'il y en a
									}
								}
								else{						
									$contenu_fichier_txt .= '' . "\t"; //liste ean vide
								}
								
								$contenu_fichier_txt .= '***'."\r\n"; //fin d'une ligne de commande	
							}
							
							/* fin des lignes commande */ 	
							$contenu_fichier_txt .= $cpt."\r\n"; // nombre de produits					
							$contenu_fichier_txt .= '******';
	
							/* ecriture des fichiers */
							$file_name 	= APPLICATION_PREFIXE . $prepa->getIdpreparation() . "_" . $time; 					
							$open 		= fopen('./PDA/commandes/out/' . $file_name. '.txt', "w+");
							fwrite($open, $contenu_fichier_txt);
							fclose($open);
							
							
							/* ecriture du fichier lot pour faire fonctionner l'existant */
							$preparateur = $prepa->getUtilisateur();
							$file_name2	= 'LOT_' . $preparateur->getPrenom() . '_' . $preparateur->getNom() . '_' . $prepa->getIdpreparation() . '_' . $time; 	
							$contenu_fichier_txt = $file_name2 . "\r\n";
							$contenu_fichier_txt .= $file_name . '.txt'; 
							$open 		= fopen('./PDA/commandes/out/' . $file_name2 . '.txt', "w+");
							fwrite($open, $contenu_fichier_txt);
							fclose($open);	
							
							/* Mis à jour de l'état de préparation */
							$prepa->setEtat(1);
							foreach($commandes as $commande){
								if($commande -> isAllAffected() && $commande -> isNotWaiting()){
									$commande -> setEtatCommande(2);
								}
							}
							
							/* Impression des étiquettes */
							$arrayCommandes = array();
							foreach($commandes as $commande){
								$arrayCommandes[] = array($commande , 1);
							}
							$fileName = './uploads/Etiq_' . $prepa->getIdpreparation() . '.pdf';
							etiquettePDFA4($arrayCommandes, $fileName);
						}
					}
					if(!$erreur){
						if($nb_prepas > 1){
							$str = gettext('Les fichiers sont &agrave; disposition pour &ecirc;tre t&eacute;l&eacute;charg&eacute;s sur le PDA');
							$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export des pr&eacute;parations ') . implode(', ', $arrayIdPreparation) . gettext(' sur le PDA') . gettext(' par ') . $_SESSION['user_login']."\r\n";
						}
						else{
							$str = gettext('Le fichier est &agrave; disposition pour &ecirc;tre t&eacute;l&eacute;charg&eacute; sur le PDA');	
							$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export de la pr&eacute;paration ') . $arrayIdPreparation[0] . gettext(' sur le PDA') . gettext(' par ') . $_SESSION['user_login']."\r\n";											
						}
						self::$_response->addVar('user_feeedback',array ('success',$str));
						
						$logFile = '../application/logs/'.date('m-Y').'-affectation.log';
						writeLog($log, $logFile);
					}
					else{
						$str = gettext('Des produits ont chang&eacute; de localisation entrainant une pr&eacute;paration sur plusieurs &eacute;tages. ');
						$str .= "\n" . gettext('La pr&eacute;paration doit &ecirc;tre annul&eacute;e et r&eacute;affect&eacute;e.');
						self::$_response->addVar('user_feeedback',array ('echec',$str));
					}
				}
				if(parent::$_request->getVar('exportPDF')){ 
					
					$arrayIdPreparation = parent::$_request->getVar('selected');
					$matrices	= getMatricePath();
					$nb_prepas = count($arrayIdPreparation);
					
					foreach($arrayIdPreparation	as $idPreparation){
						$prepa = Preparation::load(parent::$_pdo,$idPreparation);					
						$prepa->setTypePreparation('PDF');
						
						/* ordonne la liste des produits de la préparation */
						$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
						$erreur = false;
						$lsc = $prepa->selectLigne_commandes();
						$etage = null;
						$first = true;
						foreach($lsc as $lc){
							$produit = $lc->getProduit();
							$locs = $produit -> selectEtageres();
							if(count($locs) == 0){
								$etage 	= $etageDefaut;
							}
							else{
								$zone 	= $locs[0]->getZone();
								$etage 	= $zone->getEtage();
							}
							if($first){
								$idEtage = $etage->getIdetage();
								$first = false;
							}
							if($etage->getIdetage() != $idEtage)
								$erreur = true;
						}
						
						if(!$erreur){
							
							$retour 				= getSegmentsAPasser($lsc,false,$matrices[0],$matrices[1],$idEtage);			
							$arraySegmentAPasser 	= $retour[0];
							$keys 					= $retour[1];
							$qteTotale				= $retour[2];
							$nonGeoloc				= $retour[3];
							$indice1 				= $retour[5];
							$indice2				= $retour[6];
							$usePvc					= true;
										
							$retourListeDePoints 	= getListePoints($arraySegmentAPasser,$matrices[0],$matrices[1],$keys,$indice1,$indice2,$usePvc);
							
							$lignes = array();
							foreach($retourListeDePoints[1] as $segment){ 
								 if($segment[2] != null){
									foreach($segment[2] as $lc){
										$etageres	= $lc->getProduit()->selectEtageres();
										$segment	= $etageres[0]->getSegment();
										$rayon		= $segment->getRayon();
										$codeGeoloc = '';
										$codeGeoloc .= Segment::getPosition(parent::$_pdo,$segment->getIdsegment(),$rayon->getIdrayon()) . '-';
										$codeGeoloc .= Etagere::getPosition(parent::$_pdo,$etageres[0]->getIdetagere(), $segment->getIdsegment());
										$lignes[] = array($lc,$rayon->getLibelle(),$codeGeoloc);
									}
								}
							}
							
							foreach($nonGeoloc as $ligneNonGeoloc){
								$codeGeoloc = 'Non géolocalisé';
								$lignes[]	= array($ligneNonGeoloc[1], $codeGeoloc, '');
							}
							
							$arrayCommandes = $prepa -> getCommandes();
							
							$file_name 	= './PDF/' . APPLICATION_PREFIXE . $prepa->getIdpreparation() . '.pdf';
							if (preparationPDF($prepa, $lignes, $file_name)){
								/* Mis à jour de l'état de préparation */
								$prepa->setEtat(1);
								foreach($arrayCommandes as $commande){
									if($commande -> isAllAffected() && $commande -> isNotWaiting()){
										$commande -> setEtatCommande(2);
									}
								}
							}
							
							/* Impression des étiquettes */
							$commandes = array();
							foreach($arrayCommandes as $commande){
								$commandes[] = array($commande , 1);
							} 
							$fileName = './uploads/Etiq_' . $prepa->getIdpreparation() . '.pdf';
							etiquettePDFA4($commandes, $fileName);
						}
					}
					if(!$erreur){
						if($nb_prepas > 1){
							$str = gettext('Les fichiers sont &agrave; disposition pour &ecirc;tre t&eacute;l&eacute;charg&eacute;s sur le PDA');
							$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export des pr&eacute;parations ') . implode(', ', $arrayIdPreparation) . gettext(' en PDF') . gettext(' par ') . $_SESSION['user_login']."\r\n";
						}
						else{
							$str = gettext('Le fichier est &agrave; disposition pour &ecirc;tre t&eacute;l&eacute;charg&eacute; sur le PDA');	
							$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Export de la pr&eacute;paration ') . $arrayIdPreparation[0] . gettext(' en PDF') . gettext(' par ') . $_SESSION['user_login']."\r\n";											
						}
						self::$_response->addVar('user_feeedback',array ('success',$str));
						
						$logFile = '../application/logs/'.date('m-Y').'-affectation.log';
						writeLog($log, $logFile);
					}
					else{
						$str = gettext('Des produits ont chang&eacute; de localisation entrainant une pr&eacute;paration sur plusieurs &eacute;tages. ');
						$str .= "\n" . gettext('La pr&eacute;paration doit &ecirc;tre annul&eacute;e et r&eacute;affect&eacute;e.');
						self::$_response->addVar('user_feeedback',array ('echec',$str));
					}
				}
			}
		
			parent::$_response->addVar('txt_titre'			, gettext('Listes des pr&eacute;parations'));
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));
			parent::$_response->addVar('txt_num_com'		, gettext('N&deg; pr&eacute;paration'));
			parent::$_response->addVar('txt_cli_com'		, gettext('Client (Soci&eacute;t&eacute;)'));
			parent::$_response->addVar('txt_preparateur'	, gettext('Pr&eacute;parateur'));
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));
			parent::$_response->addVar('txt_page'			, gettext('Page'));
			
			parent::$_response->addVar('arrayClients'		, Client::loadAll(parent::$_pdo));
			parent::$_response->addVar('arrayPreparateurs'	, Utilisateur::loadAll(parent::$_pdo,false));
			
			
			/* Récupération des valeurs du filter */
			$numcoFilter 		= parent::$_request->getVar('numcoFilter');
			$etatcoFilter 		= parent::$_request->getVar('etatcoFilter');
			$preparateurFilter 	= parent::$_request->getVar('preparateurFilter');
			$pageFilter 		= parent::$_request->getVar('pageFilter');
			$page				= 1;  
			if(parent::$_request->getVar('submitFilter')){
				$filtre = true;
				$page 	= $pageFilter;	// Pagination
				
				$nb_preparations	= count(Preparation::loadAll(parent::$_pdo,null,$numcoFilter,$etatcoFilter,$preparateurFilter));
				$nombre_de_pages 	= (ceil($nb_preparations/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_preparations/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);	 
				$preparations = Preparation::loadAll(parent::$_pdo,''.$first,$numcoFilter,$etatcoFilter,$preparateurFilter);	

				parent::$_response->addVar('form_numcoFilter'		, $numcoFilter);
				parent::$_response->addVar('form_etatcoFilter'		, $etatcoFilter);
				parent::$_response->addVar('form_preparateurFilter'	, $preparateurFilter);
				parent::$_response->addVar('form_page'				, $page);
				
				
			}
			else{
				$nb_preparations	= count(Preparation::loadAll(parent::$_pdo));
				$nombre_de_pages 	= (ceil($nb_preparations/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_preparations/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);	
				$preparations = Preparation::loadAll(parent::$_pdo, ''.$first); 
				parent::$_response->addVar('form_page'				, $page);
				parent::$_response->addVar('form_numcoFilter'		, '');
				parent::$_response->addVar('form_etatcoFilter'		, '');
				parent::$_response->addVar('form_preparateurFilter'	, '');
			}
			
			 
			parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
			parent::$_response->addVar('page'				, $page);
			parent::$_response->addVar('txt_page'			, gettext('Page'));
			$arrayPrepas 		= array();
			
			foreach($preparations as $preparation){
				$lignes			= Ligne_Commande::selectByPreparation(parent::$_pdo, $preparation);
				$user			= $preparation->getUtilisateur(); 
				$nbReferences 	= count($lignes);
				$nbProduits		= 0;
				foreach($lignes as $ligne){
					$nbProduits += $ligne->getQuantiteCommandee();
				}
 				
				$arrayPrepas[] 	= array($preparation,$nbReferences,$nbProduits,$preparation->getDuree(),$user);
			}
			
			parent::$_response->addVar('nb_resultats'			, $nb_preparations);
			parent::$_response->addVar('nb_resultats_par_page'	, RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'		, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'				, gettext('pages'));
			parent::$_response->addVar('arrayPrepas'			, $arrayPrepas);
			parent::$_response->addVar('txt_explain'			, gettext('Choisissez l\'export souhait&eacute.'));
			parent::$_response->addVar('txt_exporter'			, gettext('Exporter'));
			parent::$_response->addVar('txt_exportPDA'			, gettext('Exporter vers le PDA'));
			parent::$_response->addVar('txt_exportPDF'			, gettext('Exporter en format PDF'));
			parent::$_response->addVar('txt_modeDegrade'		, gettext('Mode d&eacute;grad&eacute;'));
			parent::$_response->addVar('txt_preparateur'		, gettext('Pr&eacute;parateur')); 
			parent::$_response->addVar('txt_titre'				, gettext('Commandes &agrave; pr&eacute;parer')); 
			parent::$_response->addVar('txt_etat_preparation'	, gettext('Etat de la pr&eacute;paration'));
			parent::$_response->addVar('txt_num_preparation'	, gettext('N°'));
			parent::$_response->addVar('txt_nb_references'		, gettext('Nb r&eacute;f&eacute;rences'));
			parent::$_response->addVar('txt_eta_prepa'			, gettext('Etat de la pr&eacute;paration'));
			parent::$_response->addVar('txt_nb_produits'		, gettext('Nb produits'));
			parent::$_response->addVar('txt_duree'				, gettext('Dur&eacute;e'));
			parent::$_response->addVar('txt_afficherDetails'	, gettext('Afficher le d&eacute;tail de cette pr&eacute;paration'));
			parent::$_response->addVar('txt_afficherChemin'		, gettext('Afficher le chemin de pr&eacute;paration de cette pr&eacute;paration'));
			parent::$_response->addVar('txt_afficherPDF'		, gettext('Afficher le bon de pr&eacute;paration en format PDF'));
			parent::$_response->addVar('txt_afficherEtiq'		, gettext('Afficher les &eacute;tiquettes pour impression'));
			parent::$_response->addVar('txt_no_com'				, gettext('Aucune pr&eacute;paration'));
			parent::$_response->addVar('txt_date_limite_debut'	, gettext('Date limite de d&eacute;but'));
			parent::$_response->addVar('txt_date_limite_fin'	, gettext('Date limite de fin'));
			parent::$_response->addVar('txt_supprime'			, gettext('Supprim&eacute;'));
		}
	}
	
	public static function details(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_PREPARATEUR)){
			if(parent::$_request->getVar('appel_ajax')){
				$idBon 		= parent::$_request->getVar('id');
				$typeBon 	= parent::$_request->getVar('type');
				$idEtage 	= parent::$_request->getVar('etage');
				
				$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
				$zoneMagasin  = Zone::loadZoneMagasinByEtage(parent::$_pdo, $etageDefaut->getIdetage());
				
				$lignes_commande = array();
				$preparation = $_SESSION['array_preparations'][$typeBon][$idBon];
				foreach($preparation as $commande){
					$zones		= array();
					$tempsPdtsNonGeoloc = 0;
					$arrayPdtsNonGeoloc = array();
					$idCommande = $commande[0];
					$idZones	= $commande[1]; 
										
					$commande 	= Commande::load(parent::$_pdo, $idCommande);
					$allLignes 	= $commande -> selectLigne_commandes();
					$lsc		= array();
					
					/* Chargement des produits se trouvant dans deux endroits différents */
					$excepts	= array();
					foreach($allLignes as $ligne){
						$produit = $ligne->getProduit();
						$etageres 	= $produit->selectEtageres();
						if(count($etageres) > 1){
							/* Produits géolocalisés plus d'une fois */ 
							$zone		= $etageres[0]->getZone(); 
							$excepts[] = array($produit, $zone->getIdzone());
						}
						
						else if (count($etageres) == 0 ){
							/* Produits non géolocalisés */
							$arrayPdtsNonGeoloc[] = $ligne;
							$tempsPdtsNonGeoloc += $produit->getTempsMoyenAccess();
						}
					}
					if(count($idZones) == 0){ 
						$zones = Zone::loadAllEtage(parent::$_pdo,$idEtage);
					}
					else{
						foreach($idZones as $idZone){
							$zone = Zone::load(parent::$_pdo,$idZone);
							$zones[] = $zone;
						}
					}
					foreach($zones as $zone){ 
						$lsc  = array_merge($lsc,$commande -> selectLigne_commandesByZone($zone,$excepts));
						if ($zone->getIdzone() == $zoneMagasin->getIdzone()){ $lsc = array_merge($lsc, $arrayPdtsNonGeoloc); }
					}
				 
					foreach($lsc as $lc){
						if($lc->getPreparation() == null){
							$idProduit			= $lc->getProduit()->getIdProduit();
							$libelleProduit		= $lc->getProduit()->getLibelle();
							$codeCommande		= $lc->getCommande()->getCodeCommande();
							$qte				= $lc->getQuantiteCommandee();
							$prixU		 		= $lc->getPrixUnitaireTTC();
							$geoloc				= Geolocalisation::isGeolocalized(parent::$_pdo, $idProduit);
							$lignes_commande[] = array(utf8_encode($libelleProduit),utf8_encode($codeCommande),$qte,$prixU,$geoloc);
						}
					}	
					
							 
					unset($allLignes);
			
				}			
				parent::$_response->settype('ajax');
				parent::$_response->addVar('lignes_commande'				, $lignes_commande);
				parent::$_response->addVar('txt_ligne_produit'				, gettext('Produit'));
				parent::$_response->addVar('txt_ligne_commande'				, gettext('Commande'));
				parent::$_response->addVar('txt_ligne_qteCommandee'			, utf8_encode(gettext('Quantit&eacute; command&eacute;e')));
				parent::$_response->addVar('txt_ligne_prixU'				, gettext('Prix unitaire TTC'));
				parent::$_response->addVar('txt_ligne_prix'					, gettext('Prix TTC'));
				parent::$_response->addVar('txt_no_ligne'					, utf8_encode(gettext('Aucun produit trouv&eacute;')));

				return true;
			}
			else{
				parent::$_response->addVar('txt_retour'						, gettext('Retour'));
				parent::$_response->addVar('txt_preparation'				, gettext('Pr&eacute;paration'));
				parent::$_response->addVar('txt_info_pre'					, gettext('Informations sur la pr&eacute;paration'));
				parent::$_response->addVar('txt_etat'						, gettext('Etat'));
				parent::$_response->addVar('txt_modePrepa'					, gettext('Mode de pr&eacute;paration'));
				parent::$_response->addVar('txt_date_limite_debut'			, gettext('Date limite de d&eacute;but'));
				parent::$_response->addVar('txt_date_limite_fin'			, gettext('Date limite de fin'));
				parent::$_response->addVar('txt_compo_pre'					, gettext('Composition de la pr&eacute;paration'));
				parent::$_response->addVar('txt_ligne_produit'				, gettext('Produit'));
				parent::$_response->addVar('txt_ligne_commande'				, gettext('Commande'));
				parent::$_response->addVar('txt_ligne_qteCommandee'			, gettext('Quantit&eacute; command&eacute;e'));
				parent::$_response->addVar('txt_ligne_qtePrelevee'			, gettext('Quantit&eacute; pr&eacute;lev&eacute;e'));
				parent::$_response->addVar('txt_ligne_prixU'				, gettext('Prix unitaire TTC'));
				parent::$_response->addVar('txt_ligne_prix'					, gettext('Prix TTC'));
				parent::$_response->addVar('txt_no_ligne'					, gettext('Aucun produit trouv&eacute;'));
				parent::$_response->addVar('txt_details_pro'				, gettext('D&eacute;tails du produit'));
				parent::$_response->addVar('txt_details_com'				, gettext('D&eacute;tails de la commande'));
				parent::$_response->addVar('txt_details_cli'				, gettext('D&eacute;tails du client'));
				parent::$_response->addVar('txt_duree'						, gettext('Dur&eacute;e de pr&eacute;pration'));
				parent::$_response->addVar('txt_preparateur'				, gettext('Pr&eacute;prateur'));
				parent::$_response->addVar('txt_changerPreparateur'			, gettext('Changer de pr&eacute;prateur'));
				parent::$_response->addVar('txt_reinitialiserPreparation'	, gettext('Reinitialiser l\'&eacute;tat de la pr&eacute;paration'));
				parent::$_response->addVar('txt_supprimerPreparation'		, gettext('Supprimer cette pr&eacute;paration'));
				parent::$_response->addVar('txt_confirmSuppression'			, gettext('Etes vous s&ucirc;r de vouloir supprimer cette pr&eacute;paration?'));
				parent::$_response->addVar('txt_confirmReinit'				, gettext('Etes vous s&ucirc;r de vouloir r&eacute;initialiser cette pr&eacute;paration?'));
				parent::$_response->addVar('txt_afficher_chemin'			, gettext('Afficher le chemin de pr&eacute;paration de cette pr&eacute;paration'));
				parent::$_response->addVar('txt_affecter'					, gettext('Affecter'));
				parent::$_response->addVar('txt_valider_saisie'				, gettext('Terminer la pr&eacute;paration'));
				parent::$_response->addVar('txt_client'						, gettext('Client'));
				parent::$_response->addVar('txt_nb_bacs'					, gettext('Nombre de bacs'));
				parent::$_response->addVar('txt_renseignerNbBacs'			, gettext('Veuillez renseigner le nombre de bacs pour chaque commande'));
				parent::$_response->addVar('txt_contient'					, gettext('Cette pr&eacute;paration contient'));
				parent::$_response->addVar('txt_reference'					, gettext('r&eacute;f&eacute;rence(s)'));
				parent::$_response->addVar('txt_produit'					, gettext('produit(s)'));
				
				$id_prepration = parent::$_request->getVar('id');
				if(!isset($id_prepration))
					throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);	
					
				/* Chargement de la préparation */
				if(($preparation = Preparation::load(parent::$_pdo, $id_prepration,true)) == null)
					throw new Exception(gettext('Param&egrave;tre invalide'),5); 
				
				if ((Utilisateur::load(parent::$_pdo, $_SESSION['user_id'])->getUserLevel() == PROFIL_PREPARATEUR) && ($preparation->getUtilisateur()->getIdUtilisateur() != $_SESSION['user_id']))
					throw new Exception('Vous ne disposez pas des droits suffisants pour accéder à votre demande',2);
				
				$matrices = getMatricePath();
				
				$probleme 				= false;
				$lignes_commande 		= array();
				/* ordonne la liste des produits de la préparation */
				$etageDefaut = Etage::load(parent::$_pdo, Etage::getFirstId(parent::$_pdo));
				$erreur = false;
				$lsc = $preparation->selectLigne_commandes();
				$etage = null;
				$first = true;
				foreach($lsc as $lc){
					$produit = $lc->getProduit();
					$locs = $produit -> selectEtageres();
					if(count($locs) == 0){
						$etage 	= $etageDefaut;
					}
					else{
						$zone 	= $locs[0]->getZone();
						$etage 	= $zone->getEtage();
					}
					if($first){
						$idEtage = $etage->getIdetage();
						$first = false;
					}
					if($etage->getIdetage() != $idEtage)
						$erreur = true;
				}
			
				$retour 				= getSegmentsAPasser($lsc,false,$matrices[0],$matrices[1],$idEtage);					
				$arraySegmentAPasser 	= $retour[0];
				$keys 					= $retour[1];
				$qteTotale				= $retour[2];
				$nonGeoloc			= $retour[3];
				$inaccessibles		= $retour[4];
				$indice1 				= $retour[5];
				$indice2				= $retour[6];
				$retourListeDePoints 	= getListePoints($arraySegmentAPasser,$matrices[0],$matrices[1],$keys,$indice1,$indice2);
				
				/* Liste des lignes de commande ordonnée */
				foreach($retourListeDePoints[1] as $segment){  
					if($segment[2] != null){
						foreach($segment[2] as $ligne){
							if(($produit = Produit::load(parent::$_pdo,$ligne->getProduit()->getIdProduit(),true)) == null)
								throw new Exception(gettext('Param&egrave;tre invalide'),5);
							$quantite_prelevee = 0;
							if($preparation->getEtat() == 2){
								$prelevement = Prelevement_realise::selectByLigne_commande(parent::$_pdo, $ligne);
								if($prelevement != null)
									$quantite_prelevee = $prelevement -> getQuantite_prelevee();
							}
							$lignes_commande[] = array($ligne,$produit,$ligne->getCommande(),Geolocalisation::isGeolocalized(parent::$_pdo, $produit->getIdProduit()),$quantite_prelevee);
						}
					}
				}
				
				/* Produits inaccessibles en milieu de liste */
				foreach($inaccessibles as $ligneInaccessibles){
					$ligne = $ligneInaccessibles[2];
					if(($produit = Produit::load(parent::$_pdo,$ligne->getProduit()->getIdProduit(),true)) == null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					$quantite_prelevee = 0;
					if($preparation->getEtat() == 2){
						$prelevement = Prelevement_realise::selectByLigne_commande(parent::$_pdo, $ligne);
						if($prelevement != null)
							$quantite_prelevee = $prelevement -> getQuantite_prelevee();
					}
					$lignes_commande[] = array($ligne,$produit,$ligne->getCommande(),false,$quantite_prelevee,'#FF8C00');
				}
				
				/* Produits non géolocalisés en fin de liste */
				foreach($nonGeoloc as $ligneNonGeoloc){
					$ligne = $ligneNonGeoloc[1];
					if(($produit = Produit::load(parent::$_pdo,$ligne->getProduit()->getIdProduit(),true)) == null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					$quantite_prelevee = 0;
					if($preparation->getEtat() == 2){
						$prelevement = Prelevement_realise::selectByLigne_commande(parent::$_pdo, $ligne);
						if($prelevement != null)
							$quantite_prelevee = $prelevement -> getQuantite_prelevee();
					}
					$lignes_commande[] = array($ligne,$produit,$ligne->getCommande(),false,$quantite_prelevee,'#D72424');
				}
					
				$nbReferences 	= count($lignes_commande);
				$nbProduits		= 0;
				foreach($lignes_commande as $ligne_commande){
					$nbProduits += $ligne_commande[0]->getQuantiteCommandee();
				}	
				
				$duree = $preparation->getDuree();
				
				$preparateur = $preparation->getUtilisateur();
					
				$date_limite		= $preparation->getDate_preparation();
				$temps_estime		= $preparation->getDuree();
				$current_time		= time();
				$users				= array();
				
				$users	= Utilisateur::loadAll(parent::$_pdo);				
				
				$commandes = $preparation->getCommandes(); 
				$affichageCommandes = array(); 
				foreach($commandes as $commande){ 
					$affichageCommandes[] = array($commande->getIdCommande(), $commande->getCodeCommande(), $commande->getClient()->getIdclient(), $commande->getClient()->getNom() . ' ' . $commande->getClient()->getPrenom());
				} 
				parent::$_response->addVar('preparation'			, $preparation);
				parent::$_response->addVar('preparateur'			, $preparateur);
				parent::$_response->addVar('duree_pre'				, $duree);
				parent::$_response->addVar('lignes_commande'		, $lignes_commande);
				parent::$_response->addVar('nbReferences'			, $nbReferences);
				parent::$_response->addVar('nbProduits'				, $nbProduits);
				parent::$_response->addVar('users'					, array_unique($users));
				parent::$_response->addVar('arrayCommandes'			, $affichageCommandes);
				parent::$_response->addVar('userLogged'				, Utilisateur::load(parent::$_pdo, $_SESSION['user_id']));
				
				if(parent::$_request->getVar('submit')){	// Changement de préparateur
					
					$newPreparateur = parent::$_request->getVar('utilisateur');
					if ($newPreparateur != ''){
						$user = Utilisateur::load(parent::$_pdo, $newPreparateur);
						if ($user != null){
							/* Modifier le préparateur */
							if ($preparation->setUtilisateur($user))
								header('Location:' . APPLICATION_PATH . 'affectation/all');
							else
								throw new Exception(gettext('Erreur lors de la modification du pr&eacute;parateur'));
						}
						else{
							throw new Exception(gettext('Param&egrave;tre invalide'),5);
						}
					}
				}
				
				if(parent::$_request->getVar('submitSaisie')){	// Saisie manuelle
					$saisieQtesPrelevees 	= parent::$_request->getVar('saisieQtePrelevee');
					$ancien 				= array(null,'ptd_'.$idEtage); 
					foreach($saisieQtesPrelevees as $idLigne=>$qtePrelevee){
						$ligne 		= Ligne_Commande::load(parent::$_pdo, $idLigne);
						$produit 	= $ligne->getProduit();
						$etageres 	= $produit->selectEtageres();
						if ($etageres != null){
							$segment 	= $etageres[0]->getSegment();
							$idsegment 	= $segment->getIdsegment();
							$idrayon	= $segment->getRayon()->getIdrayon();
							$key		= $idrayon . '_' . $idsegment;
							
							if ($ancien[1] != 'nonGeoloc')
								$distance = $matrices[0][$key][$ancien[1]];
							else
								$distance = 0;
						}
						else{
							$key = 'nonGeoloc';
							$distance = 0;
						}
						$eanLu 			= $produit->selectEans();
						$prix			= $ligne->getPrixUnitaireTTC();
						$prelevement 	= Prelevement_realise::create(parent::$_pdo, $ligne, $ancien[0],$produit->getTempsMoyenAccess(), ''.$distance, $qtePrelevee, $eanLu[0]->getEan(), $prix, $preparation->getModePreparation());
						
						/* Prélèvement précédent */
						$ancien = array($prelevement,$key);
						
						$arrayCommandes[] = $ligne->getCommande();
					}
					
					$preparation->setEtat(2);
					
					/* Générer les fichiers Xml de retour */
					$EXPORT_DIR = "../flux/out/commandes/";
					retourCommandeXml(parent::$_pdo, array_unique($arrayCommandes), $EXPORT_DIR);
					
					/* Gestion des étiquettes */
					$nbBacs 		= parent::$_request->getVar('nbBacs');
					$arrayCommandes	= array();
					foreach($nbBacs as $idCommande => $nb){
						$arrayCommandes[] = array(Commande::load(parent::$_pdo, $idCommande), intval($nb));
					}
					
					$etiq = './uploads/Etiq_' . $preparation->getIdpreparation() . '.pdf';	
					if(file_exists($etiq)){
						unlink($etiq);
						if(file_exists($etiq))
							throw new Exception(gettext('Erreur lors de la suppression du fichier ') . $etiq);
					}
					
					$fileName = './uploads/Etiq_' . $preparation->getIdpreparation() . '.pdf';
					etiquettePDFA4($arrayCommandes, $fileName);
				
					header('Location:'. APPLICATION_PATH . 'affectation');
				}
				if(!$erreur){
					parent::$_response->addVar('erreur'			, false);
				}
				else{
					parent::$_response->addVar('erreur'			, true);
					parent::$_response->addVar('txt_erreur'		, gettext('Des produits ont chang&eacute; de localisation entrainant une pr&eacute;paration sur plusieurs &eacute;tages. ') . "\n");
					parent::$_response->addVar('txt_annuler'		,gettext('La pr&eacute;paration doit &ecirc;tre annul&eacute;e et r&eacute;affect&eacute;e.'));
				}
			}
		}
	}
	
	public static function supprimer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			$id_prepration = parent::$_request->getVar('id');
			if(!isset($id_prepration))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);	
				
			/* Chargement de la préparation */
			if(($preparation = Preparation::load(parent::$_pdo, $id_prepration,true)) == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
			else{
				if ($preparation->delete()){	
				
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Suppression de la pr&eacute;paration ') . $id_prepration . gettext(' par ') . $_SESSION['user_login']."\r\n";											
					$logFile = '../application/logs/'.date('m-Y').'-affectation.log';
					writeLog($log, $logFile);
					
					header('Location:' . APPLICATION_PATH . 'affectation');
				}
				else
					throw new Exception(gettext('Erreur lors de la suppression de la pr&eacute;paration'));
			}
			
		}
	}
		
	
	public static function reinitialiser(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
			$id_prepration = parent::$_request->getVar('id');
			if(!isset($id_prepration))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);	
				
			/* Chargement de la préparation */
			if(($prepa = Preparation::load(parent::$_pdo, $id_prepration,true)) == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
			else{
			
				$pdf = './PDF/' . APPLICATION_PREFIXE . $prepa->getIdpreparation() . '.pdf';
				if(file_exists($pdf)){
					unlink($pdf);
					if(file_exists($pdf))
						throw new Exception(gettext('Erreur lors de la reinitialisation de la pr&eacute;paration'));
				}
				
				$etiq = './uploads/Etiq_' . $prepa->getIdpreparation() . '.pdf';	
				if(file_exists($etiq)){
					unlink($etiq);
					if(file_exists($etiq))
						throw new Exception(gettext('Erreur lors de la reinitialisation de la pr&eacute;paration'));
				}			
				
				$dir = './PDA/commandes/out';
				$preparateur = $prepa->getUtilisateur();
				foreach (scandir($dir) as $file){
					if(strpos($file, APPLICATION_PREFIXE . $prepa->getIdpreparation()) === 0){
						unlink($dir . '/' . $file);
						if(file_exists($dir . '/' . $file))
							throw new Exception(gettext('Erreur lors de la reinitialisation de la pr&eacute;paration'));
					}
						
					if(strpos($file, 'LOT_' . $preparateur->getPrenom() . '_' . $preparateur->getNom() . '_' . $prepa->getIdpreparation()) === 0){
						unlink($dir . '/' . $file);
						if(file_exists($dir . '/' . $file))
							throw new Exception(gettext('Erreur lors de la reinitialisation de la pr&eacute;paration'));							
					}
				}
				if ($prepa->setEtat(0)){
					header('Location:' . APPLICATION_PATH . 'affectation');			
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('R&eacute;initialisation de la pr&eacute;paration ') . $id_prepration . gettext(' par ') . $_SESSION['user_login']."\r\n";											
					$logFile = '../application/logs/'.date('m-Y').'-affectation.log';
					writeLog($log, $logFile);
				}
				else
					throw new Exception(gettext('Erreur lors de la reinitialisation de la pr&eacute;paration'));
			}
			
		}
	}
	
	public static function recalculer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			Temps_Preparation::truncate(parent::$_pdo);
			foreach(Commande::loadAll(parent::$_pdo) as $commande){			
				foreach(Zone::loadAll(parent::$_pdo) as $zone){
					$temps = getTemps($commande->selectLigne_commandesByZone($zone));
					Temps_Preparation::create(parent::$_pdo,$commande,$zone,$temps);
				}
			}
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Recalcul des temps de pr&eacute;paration par ') . $_SESSION['user_login']."\r\n";											
			$logFile = '../application/logs/'.date('m-Y').'-affectation.log';
			writeLog($log, $logFile);
			
			Divers::setChangementLocalisation(parent::$_pdo,0);
			header('Location:' . APPLICATION_PATH . 'affectation/manuelle');			
		}
	}
	
	public static function config(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
			parent::$_response->addVar('txt_configAffectation'		,gettext('Configuration des param&egrave;tres d\'affectation')); 
			parent::$_response->addVar('txt_param_affect'			,gettext('Param&egrave;tres de l\'affectation')); 
			parent::$_response->addVar('txt_retour'					,gettext('Retour')); 
			parent::$_response->addVar('txt_boutonEnregistrer'		,gettext('Enregistrer')); 
			parent::$_response->addVar('txt_nbCommandesMax'			,gettext('Nombre max des commandes &agrave; regrouper')); 
			parent::$_response->addVar('txt_tempsMaxPrepa'			,gettext('Temps max de pr&eacute;paration (Minute)')); 
			parent::$_response->addVar('txt_nbRefsMax'				,gettext('Nombre max des r&eacute;f&eacute;rences &agrave; pr&eacute;parer')); 
			parent::$_response->addVar('txt_nbArticlesMax'			,gettext('Nombre max des articles &agrave; pr&eacute;parer')); 
			parent::$_response->addVar('txt_poidsMax'				,gettext('Poids total max des articles &agrave; pr&eacute;parer (Kg)')); 
			
			$_SESSION['retour'] = true;
			
			if(parent::$_request->getVar('submit')){	 // Clic sur le bouton Enregistrer
				$error_message = '';
				
				$nbCommandesMax 	= (int) parent::$_request->getVar('nbCommandesMax');
				if (!self::testIntegrite('nbCommandesMax', $nbCommandesMax)){
					$error_message .= gettext('Veuillez renseigner un nombre max de commandes valide') . '<br />';
				}
				
				$tempsMaxPrepa 		= (int) parent::$_request->getVar('tempsMaxPrepa');
				if (!self::testIntegrite('tempsMaxPrepa', $tempsMaxPrepa)){
					$error_message .= gettext('Veuillez renseigner un temps max de pr&eacute;paration valide') . '<br />';
				}
				
				$nbRefsMax		= (int) parent::$_request->getVar('nbRefsMax');
				if (!self::testIntegrite('nbRefsMax', $nbRefsMax)){
					$error_message .= gettext('Veuillez renseigner un nombre max de r&eacute;f&eacute;rences valide') . '<br />';
				}
				
				$nbArticlesMax		= (int) parent::$_request->getVar('nbArticlesMax');
				if (!self::testIntegrite('nbArticlesMax', $nbArticlesMax)){
					$error_message .= gettext('Veuillez renseigner un nombre max d\'articles valide') . '<br />';
				}
				
				$poidsMax		= (int) parent::$_request->getVar('poidsMax');
				if (!self::testIntegrite('poidsMax', $poidsMax)){
					$error_message .= gettext('Veuillez renseigner un poids max valide') . '<br />';
				}
				
				if($error_message == ''){
					
					if (Preparation::updateConfig(parent::$_pdo, $nbCommandesMax, $tempsMaxPrepa, $nbRefsMax, $nbArticlesMax, $poidsMax)) {
						
						/* Ecrire dans le fichier 
						$fileName 	= "../application/config/conf/affectation.ini";
						$fichier 	= fopen($fileName, 'w');
						
						$texte 		= ";Affetctaion configuration file \r\n\r\n";
						$texte 	   .= 'NB_COMMANDES_MAX = ' . $nbCommandesMax . "\r\n";
						$texte 	   .= 'TEMPS_PREPA_MAX = ' . ($tempsMaxPrepa * 60) . "\r\n";		
						$texte 	   .= 'NB_REFERENCES_MAX = ' . $nbRefsMax . "\r\n";		
						$texte 	   .= 'NB_ARTICLES_MAX = ' . $nbArticlesMax . "\r\n";		
						$texte 	   .= 'POIDS_MAX = ' . $poidsMax . "\r\n";		
						
						fputs($fichier, $texte);
						fclose($fichier);
						*/
						unset($_SESSION['array_preparations']);
						
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la configuration de l\'affectation par ') . $_SESSION['user_login']."\r\n";
						$logFile = '../application/logs/'.date('m-Y').'-affectation.log';
						writeLog($log, $logFile);					
						
						header('Location: ' . APPLICATION_PATH . 'affectation/config');
					}
				}
				else{
					/* Assigner les différentes varibales pour le template */
					parent::$_response->addVar('form_errors'		, $error_message);
					parent::$_response->addVar('form_nbCommandesMax', $nbCommandesMax);
					parent::$_response->addVar('form_tempsPrepaMax'	, $tempsMaxPrepa);
					parent::$_response->addVar('form_nbRefsMax'		, $nbRefsMax);
					parent::$_response->addVar('form_nbArticlesMax'	, $nbArticlesMax);
					parent::$_response->addVar('form_poidsMax'		, $poidsMax);
				}
			}
			else{
				parent::$_response->addVar('form_nbCommandesMax', NB_COMMANDES_MAX);
				parent::$_response->addVar('form_tempsPrepaMax'	, (TEMPS_PREPA_MAX/60));
				parent::$_response->addVar('form_nbRefsMax'		, NB_REFERENCES_MAX);
				parent::$_response->addVar('form_nbArticlesMax'	, NB_ARTICLES_MAX);
				parent::$_response->addVar('form_poidsMax'		, POIDS_MAX);
			}
		}
	}
	
	public static function testIntegrite($attribute, $value){
		switch ($attribute) {
			case 'nbCommandesMax' :
			case 'tempsMaxPrepa' :
			case 'nbRefsMax' :
			case 'nbArticlesMax' :
			case 'poidsMax' :
				return (!is_string($value) && is_int($value) && $value > 0);
				break;
				
			default:
				throw new Exception(gettext('L\'attribut ') . $attribut . gettext(' ne fait pas partie de l\'objet ') . gettext('Affectation'),3);
		}
	}
	
}

?>
