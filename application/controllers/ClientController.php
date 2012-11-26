<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * ClientController.php
 *
 */

class ClientController extends BaseController {


	public static function index(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur		
			
			$prenomFilter 		= parent::$_request->getVar('prenomFilter');
			$nomFilter	 		= parent::$_request->getVar('nomFilter');
			$societeFilter 		= parent::$_request->getVar('societeFilter');
			$codepFilter 		= parent::$_request->getVar('codepFilter');
			$municipaliteFilter = parent::$_request->getVar('municipaliteFilter');
			$nbcoFilter			= parent::$_request->getVar('nbcoFilter'); 
			$pageFilter			= parent::$_request->getVar('pageFilter'); 
			
			$page				= 1;
			
			$array_clients		= array();
			
			if(parent::$_request->getVar('submitFilter')) {	// Clic sur le bouton filter 

				$page = $pageFilter;
				
				$nb					= count(Client::loadAll(parent::$_pdo, null,$prenomFilter,$nomFilter,$societeFilter,$codepFilter,$municipaliteFilter));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$clients = Client::loadAll(parent::$_pdo, '' . $first,$prenomFilter,$nomFilter,$societeFilter,$codepFilter,$municipaliteFilter);
						
				parent::$_response->addVar('form_prenomFilter'		, $prenomFilter);
				parent::$_response->addVar('form_nomFilter'			, $nomFilter);
				parent::$_response->addVar('form_societeFilter'		, $societeFilter);
				parent::$_response->addVar('form_codepFilter'		, $codepFilter);
				parent::$_response->addVar('form_municipaliteFilter', $municipaliteFilter);
				parent::$_response->addVar('form_nbco'				, $nbcoFilter);
				parent::$_response->addVar('form_page'				, $page);
			}
			else{
			
				$nb					= count(Client::loadAll(parent::$_pdo));
				$nombre_de_pages 	= (ceil($nb/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);
				$clients = Client::loadAll(parent::$_pdo, '' . $first);
				
				parent::$_response->addVar('form_prenomFilter'		, '');
				parent::$_response->addVar('form_nomFilter'			, '');
				parent::$_response->addVar('form_societeFilter'		, '');
				parent::$_response->addVar('form_codepFilter'		, '');
				parent::$_response->addVar('form_municipaliteFilter', '');
				parent::$_response->addVar('form_nbco'				, '');
				parent::$_response->addVar('form_page'				, $page);
			}
			
			foreach($clients as $client){
				$nbco 			 = $client->selectCommandes()->rowCount();
				if($nbco >= (int) $nbcoFilter){ 
					$array_clients[] = array($client,$nbco);
				}
			}
			
			parent::$_response->addVar('nb_resultats'		, $nb);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages'));
			parent::$_response->addVar('arrayNumber'		, array(0,10,50,100)); 
			parent::$_response->addVar('array_clients'		, $array_clients); 
			parent::$_response->addVar('txt_titre'			, gettext('Gestion de la client&egrave;le'));
			parent::$_response->addVar('txt_no_cli'			, gettext('Aucun client trouv&eacute;'));
			parent::$_response->addVar('txt_cli_prenom'		, gettext('Pr&eacute;nom'));
			parent::$_response->addVar('txt_cli_nom'		, gettext('Nom'));
			parent::$_response->addVar('txt_cli_soc'		, gettext('Soci&eacute;t&eacute;'));
			parent::$_response->addVar('txt_cli_codep'		, gettext('Code postal'));
			parent::$_response->addVar('txt_cli_munici'		, gettext('Municipalit&eacute;'));
			parent::$_response->addVar('txt_cli_nb_co'		, gettext('Nb. commandes'));
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));
			parent::$_response->addVar('txt_page'			, gettext('Page'));
			parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
		}	
	}
	
	
	public static function afficher(){		
			if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_PREPARATEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur		
			
			$id_client = parent::$_request->getVar('id');
			if(!isset($id_client))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);		
		
			if(($client = Client::load(parent::$_pdo, $id_client,true)) == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);	
	 
			parent::$_response->addVar('client'				, $client);
			parent::$_response->addVar('txt_info_com'		, gettext('Informations sur le client'));  
			parent::$_response->addVar('txt_cli'			, gettext('Client'));
			parent::$_response->addVar('txt_cli_tel'		, gettext('T&eacute;l&eacute;phone'));
			parent::$_response->addVar('txt_adr_fact'		, gettext('Adresse de facturation'));  
			parent::$_response->addVar('txt_retour'			, gettext('Retour'));  
			parent::$_response->addVar('txt_cli_prenom'		, gettext('Pr&eacute;nom'));
			parent::$_response->addVar('txt_cli_nom'		, gettext('Nom'));
			parent::$_response->addVar('txt_cli_soc'		, gettext('Soci&eacute;t&eacute;'));
			parent::$_response->addVar('txt_cli_codep'		, gettext('Code postal'));
			parent::$_response->addVar('txt_cli_munici'		, gettext('Municipalit&eacute;'));
			parent::$_response->addVar('txt_cli_nb_co'		, gettext('Nb. commandes'));
			
		}	
	}

}
?>