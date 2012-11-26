<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * AccueilController.php
 *
 * 
 */

class AccueilController extends BaseController {

	
	public static function index(){
	
		if(Utilisateur::checkRights(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Profil superviseur au minimum			
		
			// PLANNING
			$currentTimestamp 	= time();
			$beginDayTimestamp	= mktime(0,0,0,date('m',$currentTimestamp),date('d',$currentTimestamp),date('Y',$currentTimestamp));
			$endDayTimestamp	= mktime(0,0,0,date('m',$currentTimestamp),intval(date('d',$currentTimestamp))+1,date('Y',$currentTimestamp));
			$arrayUsers			= array_unique(Planning::selectByTimestamp(parent::$_pdo,$beginDayTimestamp,$endDayTimestamp));
			$pas 				= (PLANNING_MODE_CRENEAU == 0) ? 3600 : 1800;
			$jour_fr			= array(7,1,2,3,4,5,6);
			$wd 				= date("w", $beginDayTimestamp);
			if($jour_fr[$wd] >= PLANNING_JOURNEE_FIN)			
				$offset			= ((7 - $jour_fr[$wd]) + PLANNING_JOURNEE_DEBUT);
			else if($jour_fr[$wd] < PLANNING_JOURNEE_DEBUT ) 			
				$offset			= 	PLANNING_JOURNEE_DEBUT - $jour_fr[$wd];
			else 				
				$offset			= 1;	 
			
			$arrayUsersNext		= array_unique(Planning::selectByTimestamp(parent::$_pdo,$beginDayTimestamp+ ($offset*3600*24),$endDayTimestamp+ (($offset)*3600*24)));		 
			
			
			$arrayResume		= array();

			foreach($arrayUsers as $user){

				$arrayPlannnigs = Planning::loadByTimestamp(parent::$_pdo,$beginDayTimestamp,$endDayTimestamp,$user);
				$fourchette		= array();
				$nb_creneaux	= count($arrayPlannnigs);
				$finPlanningUser = $arrayPlannnigs[0]->getDuree();  // recherche la fin du planning de utilisateur
				
				if($nb_creneaux > 0){
					
					if($nb_creneaux >= 2){
						$debutFourchette = $arrayPlannnigs[0]->getTimestamp();
						$finFourchette 	 = $debutFourchette + $finPlanningUser; 
						for($i = 1; $i < $nb_creneaux; $i++){  
							
							
							if(($arrayPlannnigs[$i]->getTimestamp()) != $finFourchette){
								$fourchette[] 		= array($debutFourchette,($finFourchette));
								$debutFourchette 	= $arrayPlannnigs[$i]->getTimestamp();
								$finFourchette 	 	= $debutFourchette + $finPlanningUser; 
							}
							else{								
								$finFourchette		= $finFourchette + $finPlanningUser;	 
							}						
						} 
						$fourchette[] = array($debutFourchette,$finFourchette);						
					}
					else {
						$fourchette[] = array($arrayPlannnigs[0]->getTimestamp(),($arrayPlannnigs[0]->getTimestamp()+ $finPlanningUser));
					} 				
				}
				$arrayResume[]	= array($user,$fourchette);
			}
		
			$arrayCommandes = Commande::selectByState(parent::$_pdo,0);
		
			parent::$_response->addVar('txt_tableau_de_bord'	, gettext('Tableau de bord'));
			parent::$_response->addVar('txt_editer'				, gettext('Editer'));
			parent::$_response->addVar('txt_acceder_planning'	, gettext('Acc&eacute;der au planning'));
			parent::$_response->addVar('txt_titre_stats'		, gettext('Statistiques'));
			parent::$_response->addVar('txt_titre_users'		, gettext('Ressources humaines disponibles'));
			parent::$_response->addVar('txt_titre_commandes'	, gettext('Commandes &agrave; affecter'));
			parent::$_response->addVar('txt_num_com'			, gettext('N&deg; commande (nb ref)'));
			parent::$_response->addVar('txt_cli_com'			, gettext('Client (Soci&eacute;t&eacute;)'));
			parent::$_response->addVar('txt_eta_com'			, gettext('Etat'));
			parent::$_response->addVar('txt_date_co_com'		, gettext('Date de commande'));
			parent::$_response->addVar('txt_date_li_com'		, gettext('Date de livraison'));
			parent::$_response->addVar('txt_details'			, gettext('D&eacute;tails'));
			parent::$_response->addVar('txt_com_attente'		, gettext('Commande(s) en attente d\'affectation'));
			parent::$_response->addVar('txt_no_com'				, gettext('Aucune commande trouv&eacute;e'));
			parent::$_response->addVar('txt_lien_liste_co'		, gettext('Acc&eacute;der &agrave; la liste des commandes'));
			parent::$_response->addVar('txt_lien_affec_co'		, gettext('Acc&eacute;der &agrave; l\'interface d\'affectation des commandes'));
			parent::$_response->addVar('txt_attention_no_affect', gettext('Attention, aucun pr&eacute;parateur n\'est encore disponible pour la prochaine journ&eacute;e.'));
			parent::$_response->addVar('txt_commande'			, gettext('commande'));
			parent::$_response->addVar('txt_commandes'			, gettext('commandes'));
			parent::$_response->addVar('txt_est'				, gettext('est'));
			parent::$_response->addVar('txt_sont'				, gettext('sont'));
			parent::$_response->addVar('txt_attente'			, gettext('en attente d\'affectation.'));
			parent::$_response->addVar('currentTimestamp'		, $currentTimestamp);
			parent::$_response->addVar('beginDayTimestamp'		, $beginDayTimestamp);
			parent::$_response->addVar('endDayTimestamp'		, $endDayTimestamp);
			parent::$_response->addVar('arrayResume'			, $arrayResume);
			parent::$_response->addVar('usersNext'				, count($arrayUsersNext));
			parent::$_response->addVar('nbCommandes'			, count($arrayCommandes));
		
		}
		else if(Utilisateur::checkRights(parent::$_pdo,PROFIL_PREPARATEUR)){	// Profil superviseur au minimum
			header('Location:'. APPLICATION_PATH .'affectation');
		}
		else{			
			throw new Exception(gettext('Vous devez &ecirc;tre connect&eacute; pour acc&eacute;der &agrave; votre demande'),1);
		}
	}	
}
?>