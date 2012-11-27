<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * EtageController.php
 *
 */

class EtageController extends BaseController {


	public static function index(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil administrateur		
			
			parent::$_response->addVar('txt_titre'			, gettext('Etages'));			
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));	
			parent::$_response->addVar('txt_libelle_etage'	, gettext('Libell&eacute; de l\'&eacute;tage'));	
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));
			parent::$_response->addVar('txt_etage'			, gettext('Etage'));
			parent::$_response->addVar('txt_nb_zones'		, gettext('Nombre de zones'));			
			parent::$_response->addVar('txt_nb_rayons'		, gettext('Nombre de rayons'));			
			parent::$_response->addVar('txt_nb_produits'	, gettext('Nombre de produits'));
			parent::$_response->addVar('txt_editer_etage'	, gettext('Editer l\'&eacute;tage'));
			
			$arrayEtages	= array();
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			
			
			if(parent::$_request->getVar('submitFilter')) {	// Clic sur le bouton filter
				parent::$_response->addVar('form_libelleFilter'	, $libelleFilter);
			}
			
			if ($libelleFilter != null){
				$etages	= Etage::getEtageByLibelle(parent::$_pdo, $libelleFilter);
			}
			else{
				$etages	= Etage::loadAll(parent::$_pdo, true);
			}
			
			foreach($etages as $etage){
				$zones 			= $etage->selectZones();
				$rayons 		= Rayon::loadAll(parent::$_pdo, null, $etage->getIdetage());
				$arrayProduits	= Produit::loadAll(parent::$_pdo, null, $etage->getIdetage());
				$arrayEtages[]	= array($etage,count($zones),count($rayons),count($arrayProduits)); 
			}
			
			parent::$_response->addVar('arrayEtages'	, $arrayEtages);
		}	
	}
	
	public static function creer(){	
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
		
		
			parent::$_response->addVar('txt_titre'				, gettext('Cr&eacute;er un &eacute;tage'));			
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));	
			parent::$_response->addVar('txt_libelle'			, gettext('Libelle'));
			parent::$_response->addVar('txt_boutonCreer'		, gettext('Cr&eacute;er'));

			$error_message = '';
		
			if(parent::$_request->getVar('submit')){
				$libelle = parent::$_request->getVar('libelle');
				if(!Etage::testIntegrite('libelle', $libelle)){
						$error_message .= gettext('Veuillez renseigner un libelle valide') . '<br />';
				}
				
				else if(Etage::libelleUsed(parent::$_pdo, $libelle)){
						/* Saisie d'un libellé déja utilisé */
						$error_message .= gettext('Ce libelle est d&eacute;j&agrave; utilis&eacute;, veuillez en choisir un autre') . '<br />';
				}
			
				if($error_message == ''){	
					$etage = Etage::create(parent::$_pdo, $libelle, 500, 700, 0, 0, 0, 0);
					Zone::create(parent::$_pdo, $etage -> getIdetage(), 'magasin');
					Obstacle::create(parent::$_pdo, $etage, 25, 25, 50, 80, 'caisse', 'caisse', '4B92E3');
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Cr&eacute;ation d\'un &eacute;tage par ') .$_SESSION['user_login']."\r\n";
					$logFile = '../application/logs/'.date('m-Y').'-etage.log';
					writeLog($log, $logFile);
					header('Location: ' . APPLICATION_PATH . 'etage');
				}
				else{
					parent::$_response->addVar('form_errors'		, $error_message); 
					parent::$_response->addVar('form_libelle'		, $libelle);
				}
			}
			
			else{			
				parent::$_response->addVar('form_errors'		, $error_message); 
				parent::$_response->addVar('form_libelle'		, '');
			}
		}
	}	
	
	public static function editer(){	
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
			$idEtage = parent::$_request->getVar('id');
			if(isset($idEtage))
				$etageSelectionne = $idEtage;
			else
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
		
			//chargement de l'objet
			$etage = Etage::load(parent::$_pdo,$etageSelectionne);
			if($etage == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
		
		
			parent::$_response->addVar('txt_titre'				, gettext('Editer un &eacute;tage'));			
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));	
			parent::$_response->addVar('txt_libelle'			, gettext('Libelle'));
			parent::$_response->addVar('txt_boutonEditer'		, gettext('Editer'));
			parent::$_response->addVar('txt_confirmSuppression'	, gettext('Voulez vous vraiment supprimer cet &eacute;tage (La g&eacute;olocalisation de cet &eacute;tage sera d&eacute;truite)'));
			parent::$_response->addVar('txt_lienSupprimer'		, gettext('Supprimer l\'&eacute;tage'));
			parent::$_response->addVar('form_idEtage'			, $idEtage);
			
			$error_message = '';
		
			if(parent::$_request->getVar('submit')){
				$libelle = parent::$_request->getVar('libelle');
				if(!Etage::testIntegrite('libelle', $libelle)){
						$error_message .= gettext('Veuillez renseigner un libelle valide') . '<br />';
				}
				
				else if(Etage::libelleUsed(parent::$_pdo, $libelle,$idEtage)){
						/* Saisie d'un libellé déja utilisé */
						$error_message .= gettext('Ce libelle est d&eacute;j&agrave; utilis&eacute;, veuillez en choisir un autre') . '<br />';
				}
			
				if($error_message == ''){	
					$oldLibelle = $etage -> getLibelle();
					$etage -> setLibelle($libelle);
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification du libell&eacute; de l\'&eacute;tage ').$idEtage.gettext(' par ').$_SESSION['user_login'].' : '.$oldLibelle.' --> '.$libelle."\r\n";
					$logFile = '../application/logs/'.date('m-Y').'-etage.log';
					writeLog($log, $logFile);
					header('Location: ' . APPLICATION_PATH . 'etage');
				}
				else{
					parent::$_response->addVar('form_errors'		, $error_message); 
					parent::$_response->addVar('form_libelle'		, $etage->getLibelle());
				}
			}
			
			else{			
				parent::$_response->addVar('form_errors'		, $error_message); 
				parent::$_response->addVar('form_libelle'		, $etage->getLibelle());
			}
		}
	}

	
	public static function supprimer(){	
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
			$idEtage = parent::$_request->getVar('id');	
			if(!isset($idEtage))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
			else if($idEtage == 1)
				throw new Exception(gettext('Impossible de supprimer le premier &eacute;tage.'),5);
				
			$etage = Etage::load(parent::$_pdo,$idEtage);
			if($etage == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
			
			$etage -> delete();
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Suppresion de l\'&eacute;tage ').$idEtage.gettext(' par ').$_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-etage.log';
			writeLog($log, $logFile);
			
			Divers::setChangementLocalisation(parent::$_pdo,1);
			header('Location: ' . APPLICATION_PATH . 'etage');			
		}
	}
}
?>
