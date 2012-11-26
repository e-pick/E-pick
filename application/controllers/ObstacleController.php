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

class ObstacleController extends BaseController {
	
	public static function index(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil administrateur		
			
		}	
	}
	
	/* Action editer
	 * 
	 * Change les informations d'un obstacle
	 *
	 */	
	 public static function editer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
			parent::$_response->addVar('txt_infosObstacle'		, gettext('Informations de l\'obtacle'));
			parent::$_response->addVar('txt_Couleur'			, gettext('Couleur'));
			
			$idObstacle = parent::$_request->getVar('id');
			if(isset($idObstacle))
				$obstacleSelectionne = $idObstacle;
			else
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
							
			if (parent::$_request->getVar('valider')){
					$idObstacle = parent::$_request->getVar('idObstacle');
					$couleur = parent::$_request->getVar('couleur');
					$obstacle = Obstacle::load(parent::$_pdo,$idObstacle,true);
					
					$error_message = '';
					
					if($couleur == '' || empty($couleur)){
						$error_message .= gettext('Veuillez renseigner une couleur valide') . '<br />';
					}
					
					if($error_message == ''){
						
						$obstacle->setCouleur($couleur,false);
						$obstacle->update();
					
						parent::$_response->addVar('form_couleur'			, $couleur);
						
					}
					else{	
						/* Assigner les différentes varibales pour le template */
						parent::$_response->addVar('form_errors'			, $error_message); 
						parent::$_response->addVar('form_idObstacle'			, $obstacle->getIdobstacle()); 
						parent::$_response->addVar('form_couleur'			, $couleur); 
					
					}
					parent::$_response->setType('ajax');
					return true;

			}	
				
			else if (parent::$_request->getVar('appel_ajax')){
				
					$obstacle = Obstacle::load(parent::$_pdo,$obstacleSelectionne,true);
					if($obstacle == null)
						throw new Exception(gettext('Param&egrave;tre invalide'),5);
					
					$error_message = '';
						
					parent::$_response->addVar('txt_modifier'			, gettext('Modifier'));
					parent::$_response->addVar('txt_fermer'				, gettext('Fermer'));
					
					parent::$_response->addVar('obstacle', $obstacle);
					parent::$_response->addVar('form_errors'			, $error_message); 
					parent::$_response->addVar('form_idObstacle'		, $obstacle->getidobstacle()); 
					parent::$_response->addVar('form_couleur'			, $obstacle->getCouleur());
					parent::$_response->setType('ajax');
					return true;
			}	
		}
	}
	
}
