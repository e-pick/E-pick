<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * PlanningController.php
 *
 */

class PlanningController extends BaseController {

	/* Action par d�faut
	 * Profil requis : >= Profil superviseur
	 * 
	 * Action permettant de visualiser le planning et affiche le nombre d'utilisateurs
	 * qui travaillent pendant les cr�neaux
	 */	
	public static function index(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur	
			//on r�cup�re les param�tres pass�s dans l'url
			$currentYear  = parent::$_request->getVar('annee');
			$currentMonth = parent::$_request->getVar('mois');
			$currentDay   = parent::$_request->getVar('jour');
			//s'ils n'existent pas, on prend la date actuelle comme r�f�rence
			if(($currentYear == '') || ($currentMonth == '') || ($currentDay == '')){
				$currentYear 	= date("Y");
				$currentMonth 	= date("m");
				$currentDay  	= date("d");
			}
			//on calcul diff�rentes variables qui nous permettrons de faire des calculs par la suite
			$currentTimestamp 	= mktime(0,0,0,(int)$currentMonth,(int)$currentDay,(int)$currentYear); 
			$currentWeek 		= date('W',$currentTimestamp); 
			$monthName			= date('F',$currentTimestamp);
			$nextWeek 			= strtotime('+1 week',$currentTimestamp);  
			$previousWeek 		= strtotime('-1 week',$currentTimestamp);  
			$week 				= self::week_dates(($currentWeek-1),$currentYear);
			
			$jour_semaine 		= array();  //contient le timestamp de chaque jour de la semaine
			$affectation		= array();	//contient le nombre d'utilisateurs qui travaillent dans un cr�neau

			//on parcourt la semaine de PLANNING_JOURNEE_DEBUT � PLANNING_JOURNEE_FIN, ces param�tres
			//sont d�finis dans un fichier de configuration lu dans bootstrap.php
			for($i = PLANNING_JOURNEE_DEBUT; $i <= PLANNING_JOURNEE_FIN; $i++){
				$jour				= mktime(0,0,0,date('m',$week[$i]),date('d',$week[$i]),date('Y',$week[$i])); // on calcul le timestamp du d�but du jour
				$jour_semaine[$i] 	= $jour;  
		 
					//pour toutes les heures de la journ�e, on va calculer le nombre d'utilisateurs qui travaillent
					for($j = PLANNING_HEURE_DEBUT; $j < PLANNING_HEURE_FIN; $j++){ 
					
						if(PLANNING_MODE_CRENEAU  == 0){ //cr�neau � l'heure							
							$timestamp_deb 				= mktime($j,0,0,date('m',$jour),date('d',$jour),date('Y',$jour)); 		
							$timestamp_fin				= mktime(($j+1),0,0,date('m',$jour),date('d',$jour),date('Y',$jour)); 	 						
							$affectation[$jour][$j][0]	= count(array_unique(Planning::selectByTimestamp(parent::$_pdo,$timestamp_deb,$timestamp_fin))); 
							
						}
						// dans le cas du cr�neau � la demi heure, il faut calculer pour la premi�re demi heure et la deuxi�me demi heure
						else{ //creneau � la demi heure
							$timestamp_deb 				= mktime($j,0,0,date('m',$jour),date('d',$jour),date('Y',$jour)); 		
							$timestamp_fin				= mktime($j,30,0,date('m',$jour),date('d',$jour),date('Y',$jour)); 											
							$affectation[$jour][$j][1]	= count(array_unique(Planning::selectByTimestamp(parent::$_pdo,$timestamp_deb,$timestamp_fin))); 
							$timestamp_deb 				= mktime($j,30,0,date('m',$jour),date('d',$jour),date('Y',$jour)); 		
							$timestamp_fin				= mktime(($j+1),0,0,date('m',$jour),date('d',$jour),date('Y',$jour)); 			
							$affectation[$jour][$j][2]	= count(array_unique(Planning::selectByTimestamp(parent::$_pdo,$timestamp_deb,$timestamp_fin))); 
						} 
				}
			}
			//on construit l'objet r�ponse
			parent::$_response->addVar('txt_planning'			, gettext('Planning des utilisateurs pour'));
			parent::$_response->addVar('txt_conf'				, gettext('Configurer les param&egrave;tres'));
			parent::$_response->addVar('txt_dupliquer'			, gettext('Dupliquer la journ&eacute;e'));
			parent::$_response->addVar('txt_dupliquer_semaine'	, gettext('Dupliquer la semaine &agrave; la semaine d\'apr&egrave;s'));
			parent::$_response->addVar('txt_confirm_duplication', gettext('Etes-vous s&ucirc;r de vouloir dupliquer la semaine en cours &agrave; la semaine suivante?'));
			parent::$_response->addVar('txt_semaine'			, gettext('Semaine'));
			parent::$_response->addVar('txt_semaine_suivante'	, gettext('Semaine suivante'));
			parent::$_response->addVar('txt_semaine_precedente'	, gettext('Semaine pr&eacute;c&eacute;dente'));
			parent::$_response->addVar('txt_aujourdhui'			, gettext('Retourner &agrave; aujourd\'hui'));
			parent::$_response->addVar('txt_utilisateur'		, gettext('utilisateur'));
			parent::$_response->addVar('txt_utilisateurs'		, gettext('utilisateurs'));
			parent::$_response->addVar('semaine_suivante'		, date("d-m-Y",$nextWeek));
			parent::$_response->addVar('semaine_precedente'		, date("d-m-Y",$previousWeek));
			parent::$_response->addVar('aujourdhui'				, date("d-m-Y", time()));
			parent::$_response->addVar('aujourdhui_timestamp'	, mktime (0,0,0,date("m",time()),date("d",time()),date("Y",time())));
			parent::$_response->addVar('aujourdhui_semaine'		, date('W',mktime (0,0,0,date("m",time()),date("d",time()),date("Y",time()))));
			parent::$_response->addVar('jour_en_cours'			, $currentDay);
			parent::$_response->addVar('semaine_en_cours'		, $currentWeek);
			parent::$_response->addVar('mois_en_cours'			, $currentMonth);
			parent::$_response->addVar('timestamp_en_cours'		, $currentTimestamp);
			parent::$_response->addVar('annee_en_cours'			, $currentYear);
			parent::$_response->addVar('jour_semaine'			, $jour_semaine); 
			parent::$_response->addVar('utilisateurs'			, $affectation); 
			// variable pour le pop-in d'aide qui est affichable si on clique sur l'icone d'aide en haut � gauche
			parent::$_response->addVar('txt_help'				, gettext('Aide'));
			parent::$_response->addVar('txt_help_titre'			, gettext('Aide planning'));
			parent::$_response->addVar('txt_help_contenu'		, gettext('Gr&acirc;ce au planning, vous pouvez affecter vos ressources disponibles par cr&eacute;neau (soit des cr&eacute;neaux de 30minutes soit de 1 heure).'));
		
		}	
	}
	
	/* Action afficher
	 * Profil requis : >= Profil superviseur
	 * Param�tre obligatoire : id qui contient la date s�lectionn�e, l'heure s�lectionn�e ainsi que la demi heure s�lectionn�e 
	 * Affiche un cr�neau, permet l'ajout d'utilisateur pour ce cr�neau ainsi que la suppression des utilisateurs d�j� affect�s
	 */	
	public static function afficher(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur	
		
			$id = parent::$_request->getVar('id');
			if(!isset($id))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
			
			//on r�cup�re les infos de id
			$dateSelect 				= substr($id,0,-3);
			$heureSelect 				= substr($id,-3,-1);
			$demiHeureSelect			= substr($id,-1);		
			$currentTimestamp 			= time();
			
			//on va cr�er nos variables selon le mode cr�neau dans lequel on se trouve
			if(PLANNING_MODE_CRENEAU == 0){ //cr�neau � l'heure
				$creneauDebutSelect 		= mktime($heureSelect,0,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
				$creneauFinSelect 			= mktime(($heureSelect+1),0,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
				$minutesDeb					= '00';
				$minutesFin					= '00';
				$heureFin					=  ($heureSelect+1);
			}
			else{ //cr�neau � la demi heure
				if($demiHeureSelect == 1){
					$creneauDebutSelect 		= mktime($heureSelect,0,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
					$creneauFinSelect 			= mktime($heureSelect,30,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
					$minutesDeb					= '00';
					$minutesFin					= '30';
					$heureFin					= $heureSelect;
				}
				else{
					$creneauDebutSelect 		= mktime($heureSelect,30,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
					$creneauFinSelect 			= mktime(($heureSelect+1),0,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));				
					$minutesDeb					= '30';
					$minutesFin					= '00';
					$heureFin					=  ($heureSelect+1);
				}
			}
			
			//on r�cup�re dans un premier temps, tout les utilisateurs qui travaillent dans ce cr�neau
			$utilsateurs_travaillent 	= Planning::selectByTimestamp(parent::$_pdo,$creneauDebutSelect,$creneauFinSelect); 
			//et on r�cup�re tous les autres utilisateurs
			$utilisateurs 				= Utilisateur::loadAll(parent::$_pdo,true,true);
			 
			$users						= array(); // on va placer dans ce tableau, tous les utilisateurs qui peuvent �tre �ventuellement affect�s
			foreach($utilisateurs as $current){
				$bool = true;
				foreach($utilsateurs_travaillent as $user){
					if($current->getIdUtilisateur() == $user->getIdUtilisateur()){				
						$bool = false;
						break;
					}
				}
				if($bool)
					$users[] = $current;
			} 
			
			$pas 		= (PLANNING_MODE_CRENEAU == 0) ? 3600 : 1800 ; 
			$bool 		= true;			
			$listeHeure = array(); // on va lister la fin des autres cr�neaux possibles
			for($i= ($creneauDebutSelect+$pas); $i <= mktime(0,0,0,date('m',$dateSelect),date('d',($dateSelect+ (3600*24))),date('Y',$dateSelect)); $i = ($i + $pas)){
				$listeHeure[] = $i;
			} 
			

			//on construit l'objet r�ponse
			parent::$_response->addVar('utilisateur_travaillent', array_unique($utilsateurs_travaillent));
			parent::$_response->addVar('utilisateur_a_affecter'	, $users);
			parent::$_response->addVar('jourSelect'				, date('d',$dateSelect));
			parent::$_response->addVar('moisSelect'				, date('m',$dateSelect));
			parent::$_response->addVar('anneeSelect'			, date('Y',$dateSelect));
			parent::$_response->addVar('heureSelect'			, $heureSelect);
			parent::$_response->addVar('creneauDebutSelect'		, $creneauDebutSelect);
			parent::$_response->addVar('creneauFinSelect'		, $creneauFinSelect);
			parent::$_response->addVar('currentTimestamp'		, $currentTimestamp);
			parent::$_response->addVar('minutesDeb'				, $minutesDeb);
			parent::$_response->addVar('minutesFin'				, $minutesFin);
			parent::$_response->addVar('heureFin'				, $heureFin);
			parent::$_response->addVar('listeHeure'				, $listeHeure);
			parent::$_response->addVar('txt_liste_personnes'	, gettext('Liste des personnes qui travaillent'));
			parent::$_response->addVar('txt_plus_user_dispo'	, utf8_encode(gettext('Tous les utilisateurs ont &eacute;t&eacute; affect&eacute;s.')));
			parent::$_response->addVar('txt_trop_tard'			, utf8_encode( gettext('Impossible d\'ajouter des utilisateurs pour ce cr&eacute;neau car il est d&eacute;j&agrave; termin&eacute;.')));
			parent::$_response->addVar('txt_ajouter_creneau' 	, gettext('Ajouter un utilisateur'));
			parent::$_response->addVar('txt_utilisateur'		, gettext('Utilisateur'));
			parent::$_response->addVar('txt_de'					, gettext('de'));
			parent::$_response->addVar('txt_a'					, utf8_encode( gettext('&agrave;')));
			parent::$_response->addVar('txt_ajouter'			, gettext('Ajouter'));
			parent::$_response->addVar('txt_help'				, gettext('Aide'));
				
			//�tant donn� que la requ�te est de type ajax, on retournera le template afficher.ajax.tpl qui est correctement format�
			//ne pas oublier d'utilisateur utf8_encode() lorsqu'on veut passer des variables textes dans un template ajax
			if(parent::$_request->getVar('appel_ajax')){  
				parent::$_response->setType('ajax');
				return true;
			}
		}
	}
	
	
	/* Action dupliquer jour
	 * Profil requis : >= Profil superviseur
	 * Param�tre obligatoire : nbjours qui contient le nombre de jours
	 * Param�tre obligatoire : id qui contient le timestamp du jours
	 * Permet de dupliquer un jour sur un nombre de jours pass� en param
	 */	
	public static function dupliquerjour(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur	
		
			$id = parent::$_request->getVar('id');
			if(!isset($id))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
			
			//on r�cup�re le param
			$nbJours = parent::$_request->getVar('nbjours');
			$nb = $nbJours;
		
			if(($nbJours) != ''){ 
				$jourDebutSelect 	= $id; 					//d�but du jour � dupliquer
				$jourFinSelect 		= ($id + (3600*24));	//fin du jour � dupliquer
				$arrayPlanning 		= Planning::loadByTimestamp(parent::$_pdo,$jourDebutSelect,$jourFinSelect);	//tous les plannings pr�vu dans ce cr�neau
		
				$pas 				= (PLANNING_MODE_CRENEAU == 0) ? 3600 : 1800 ; 
				$i = 1;
				//pour chaque jours, on va cr�er les cr�neaux s'ils n'existent d�j� pas
				while($i <= $nbJours){
					$jour_fr 		= array(7,1,2,3,4,5,6);
					$wd 			= date("w", ($jourDebutSelect+($i*3600*24)));
							
					if($jour_fr[$wd] >= PLANNING_JOURNEE_DEBUT && $jour_fr[$wd] <= PLANNING_JOURNEE_FIN){ // on v�rifie qu'on duplique bien entre les jours du planning
						//on supprime tous les plannings du jour
						$array_planning = Planning::loadByTimestamp(parent::$_pdo,($jourDebutSelect+($i*3600*24)),($jourFinSelect+($i*3600*24)));
						foreach($array_planning as $planning)
							$planning->delete();
							
						//on duplique le jour
						foreach($arrayPlanning as $planning)
								Planning::create(parent::$_pdo,$planning->getUtilisateur(),($planning->getTimestamp()+($i*3600*24)),$planning->getDuree());
					}
					else{
						$nbJours++;
					}
					$i++;
				}
				$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Duplication du jour ') . date('d-m-Y',$jourDebutSelect) . gettext(' sur ') . $nb . gettext(' jours') . gettext(' par ') . $_SESSION['user_login']."\r\n";
				$logFile = '../application/logs/'.date('m-Y').'-planning.log';
				writeLog($log, $logFile);
				
				//on affiche le jour qui a �t� dupliqu�
				header('Location: ' . APPLICATION_PATH .'planning/'.date('d-m-Y',$id));
			}
			
			//on construit l'objet r�ponse pour l'affichage
			parent::$_response->addVar('txt_dupliquer_sur'	, utf8_encode(gettext('Dupliquer la journ&eacute;e sur')));
			parent::$_response->addVar('txt_jours'			, gettext('jour(s)'));
			parent::$_response->addVar('txt_valider'		, gettext('Valider'));	
		
			if(parent::$_request->getVar('appel_ajax')){  
				parent::$_response->setType('ajax');
				return true;	
			} 
		}
	}
	/* Action dupliquer semaine
	 * Profil requis : >= Profil superviseur 
	 * Param�tre obligatoire : id qui contient le timestamp du premier jour de la semaine
	 * Permet de dupliquer une semaine sur la semaine suivante
	 */	
	public static function dupliquersemaine(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur	
			$id = parent::$_request->getVar('id');
			if(!isset($id))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
			
			
			$jourDebutSelect 	= $id;
			$jourFinSelect 		= ($id + (3600*24*7));
			$arrayPlanning 		= Planning::loadByTimestamp(parent::$_pdo,$jourDebutSelect,$jourFinSelect);
			$pas 				= (PLANNING_MODE_CRENEAU == 0) ? 3600 : 1800 ; 
			
			//on supprime tous les plannings de la semaine
			$array_planning = Planning::loadByTimestamp(parent::$_pdo,($jourDebutSelect+(7*3600*24)),($jourFinSelect+(14*3600*24)));
			foreach($array_planning as $planning)
				$planning->delete();
			// on duplique la semaine	
			foreach($arrayPlanning as $planning){			
				 Planning::create(parent::$_pdo,$planning->getUtilisateur(),($planning->getTimestamp()+(7*3600*24)),$planning->getDuree());
			}	
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Duplication de la semaine ') . date('W',$jourDebutSelect) . gettext(' par ') . $_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-planning.log';
			writeLog($log, $logFile);

			header('Location: ' . APPLICATION_PATH .'planning/'.date('d-m-Y',$id));
		}
	}
 
	
	/* Action affecter
	 * Profil requis : >= Profil superviseur 
	 * Param�tre obligatoire : id qui contient le timestamp du jour
	 * Param�tre obligatoire : idutilisateur qui contient l'id de l'utilisateur s�lectionn�
	 * Param�tre obligatoire : heure_debut
	 * Param�tre obligatoire : heure_fin
	 * Permet de dupliquer une semaine sur la semaine suivante
	 */	
	public static function affecter(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur	
		
			$dateSelect = parent::$_request->getVar('id');
			$iduser 	= parent::$_request->getVar('idutilisateur');
			$dateDebut	= parent::$_request->getVar('heure_debut');
			$dateFin	= parent::$_request->getVar('heure_fin');

			$debut = $dateDebut;
			$fin = $dateFin;
			
			
			if(($dateSelect == '') ||($iduser == '') || ($dateFin == '')||($dateDebut==''))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
			
			$user = Utilisateur::load(parent::$_pdo,$iduser);
			
			$timestamp_deb = mktime(0,0,0,date('m',$dateDebut),date('d',$dateDebut),date('Y',$dateDebut));
			$timestamp_fin = mktime(0,0,0,date('m',$dateDebut),intval(date('d',$dateDebut))+1,date('Y',$dateDebut));
			$plannings_day = Planning::loadByTimestamp(parent::$_pdo,$timestamp_deb,$timestamp_fin,$user);
			 
			$duree_sup = 0;
			if(count($plannings_day) >= 0){
				foreach($plannings_day as $planning){
					if(($planning->getTimestamp() >= $dateDebut)){
						if(($planning->getTimestamp() + $planning->getDuree()) < $dateFin){					
							$planning->delete();
						}
						else if($planning->getTimestamp() == $dateFin){
							$duree_sup += $planning->getDuree();
							$planning->delete();
						}
						else if((($planning->getTimestamp() + $planning->getDuree()) > $dateFin) && ($planning->getTimestamp() < $dateFin)){
							$duree_sup += ($planning->getTimestamp() + $planning->getDuree()) -  $dateFin;
							$planning->delete();
						}			
					}
					else{
						if(($planning->getTimestamp() + $planning->getDuree()) == $dateDebut){
							$dateDebut -= $planning->getDuree();
							$planning->delete();
						}
					
					}
				}		
			} 
			Planning::create(parent::$_pdo,Utilisateur::load(parent::$_pdo,$iduser),$dateDebut,($dateFin-$dateDebut)+$duree_sup);
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Ajout du cr&eacute;neau ') . date('d/m/Y H:i',$debut) . '-' . date('H:i',$fin) . gettext(' pour l\'utilisateur ') . $user->getLogin() . gettext(' par ') . $_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-planning.log';
			writeLog($log, $logFile);
			
			if(parent::$_request->getVar('appel_ajax')){  
				parent::$_response->setType('ajax');
				return true;
			}
		}
	}
	/* Action supprimer
	 * Profil requis : >= Profil superviseur 
	 * Param�tre obligatoire : id qui contient le timestamp du jour
	 * Param�tre obligatoire : idutilisateur qui contient l'id de l'utilisateur s�lectionn�
	 * Param�tre obligatoire : heure_debut
	 * Param�tre obligatoire : heure_fin 
	 */	
	public static function supprimer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur	
		
			$id 		= parent::$_request->getVar('id');
			$iduser 	= parent::$_request->getVar('idutilisateur');
			$dateDebut	= parent::$_request->getVar('heure_debut');
			$dateFin	= parent::$_request->getVar('heure_fin'); 
			 
			if(($id == '') ||($iduser == '') || ($dateFin == '')||($dateDebut==''))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);			
			
			
			//on r�cup�re le planning qui contient ce cr�neau
			$planning 	= Planning::selectPlanningByUserAndTimestamp(parent::$_pdo,$dateDebut,$dateFin,$iduser);
			$deb_origin = $planning->getTimestamp();
			$fin_origin = $planning->getTimestamp() + $planning->getDuree(); 
			
			//dans un premier temps, on r�duit le cr�neau jusqu'au d�but du cr�neau supprim�
			if(($dateDebut-$deb_origin) <= 0 ) // si le cr�neau � supprimer est au d�but du planning, on supprime se planning
				$planning->delete(); 
			else{ //sinon on met � jour la dur�e
				$planning->setDuree(($dateDebut-$deb_origin));
			}
			
			// on cr�e un planning de la fin du cr�neau supprim� � la fin du cr�neau original
			if($fin_origin > $dateFin){
				$user = Utilisateur::load(parent::$_pdo,$iduser);
				Planning::create(parent::$_pdo,$user,$dateFin,($fin_origin - $dateFin));		
			}
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Suppression du cr&eacute;neau ') . date('d/m/Y H:i',$dateDebut) . '-' . date('H:i',$dateFin) . gettext(' pour l\'utilisateur ') . $user->getLogin() . gettext(' par ') . $_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-planning.log';
			writeLog($log, $logFile);
					
			if(parent::$_request->getVar('appel_ajax')){  
				parent::$_response->setType('ajax');
				return true;
			}
		}
	}
	
	/* Action utilisateurscreneau
	 * Profil requis : >= Profil superviseur 
	 * Param�tre obligatoire : id qui contient le timestamp du cr�neau 
	 */	
	public static function utilisateurscreneau(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur	
		
			$id = parent::$_request->getVar('id');			
			if(($id == ''))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
	 
			$dateSelect 				= substr($id,0,-3);
			$heureSelect 				= substr($id,-3,-1);
			$demiHeureSelect			= substr($id,-1);
			
			if(PLANNING_MODE_CRENEAU == 0){ //cr�neau � l'heure
				$creneauDebutSelect 		= mktime($heureSelect,0,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
				$creneauFinSelect 			= mktime(($heureSelect+1),0,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
			}
			else{ //cr�neau � la demi heure
				if($demiHeureSelect == 1){
					$creneauDebutSelect 		= mktime($heureSelect,0,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
					$creneauFinSelect 			= mktime($heureSelect,30,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
				}
				else{
					$creneauDebutSelect 		= mktime($heureSelect,30,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));
					$creneauFinSelect 			= mktime(($heureSelect+1),0,0,date('m',$dateSelect),date('d',$dateSelect),date('Y',$dateSelect));				
				}
			}
			//on calcul le nombre d'utilisateurs affect�s � ce cr�neau
			parent::$_response->addVar('users',array_unique(Planning::selectByTimestamp(parent::$_pdo,$creneauDebutSelect,$creneauFinSelect))); 		 
			parent::$_response->addVar('txt_liste_utilisateurs',gettext('Liste des utilisateurs')); 		 
			if(parent::$_request->getVar('appel_ajax')){  
				parent::$_response->setType('ajax');
				return true;
			}
		}
	}
	
	
	private static function week_dates($week,$year) {
		$week_dates = array();
		// Get timestamp of first week of the year
		$first_day = mktime(12,0,0,1,1,$year);
		$first_week = date("W",$first_day);
		if ($first_week > 1) {
			$first_day = strtotime("+1 week",$first_day); // skip to next if year does not begin with week 1
		}
		// Get timestamp of the week
		$timestamp = strtotime("+$week week",$first_day);
		// Adjust to Monday of that week
		$what_day = date("w",$timestamp); 
		if ($what_day==0) {
		   // actually Sunday, last day of the week. FIX;
		   $timestamp = strtotime("-6 days",$timestamp);
		} elseif ($what_day > 1) {
		   $what_day--;
		   $timestamp = strtotime("-$what_day days",$timestamp);
		}
		$week_dates[1] = $timestamp; // Monday
		$week_dates[2] = strtotime("+1 day",$timestamp); // Tuesday
		$week_dates[3] = strtotime("+2 day",$timestamp); // Wednesday
		$week_dates[4] = strtotime("+3 day",$timestamp); // Thursday
		$week_dates[5] = strtotime("+4 day",$timestamp); // Friday
		$week_dates[6] = strtotime("+5 day",$timestamp); // Saturday
		$week_dates[7] = strtotime("+6 day",$timestamp); // Sunday
		
		return($week_dates);
	}
	
	/* Action utilisateurscreneau
	 * Profil requis : >= Profil administrateur  
	 */	
	public static function config(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
			parent::$_response->addVar('txt_configPlanning'			,gettext('Configuration du planning')); 		 
			parent::$_response->addVar('txt_boutonEnregistrer'		,gettext('Enregistrer')); 		 
			parent::$_response->addVar('txt_Plage_horaire'			,gettext('Plage horaire')); 		 
			parent::$_response->addVar('txt_heureDebut'				,gettext('Heure de d&eacute;but')); 		 
			parent::$_response->addVar('txt_heureFin'				,gettext('Heure de fin')); 	
			parent::$_response->addVar('txt_Vue_semaine'			,gettext('Vue semaine')); 		 
			parent::$_response->addVar('txt_jour_debut'				,gettext('Journ&eacute;e de d&eacute;but')); 		 
			parent::$_response->addVar('txt_jour_fin'				,gettext('Journ&eacute;e de fin')); 		
			parent::$_response->addVar('txt_Creneau'				,gettext('Cr&eacute;neau')); 		 
			parent::$_response->addVar('txt_modification_creneau'	,''); 		 	 
			parent::$_response->addVar('txt_creneau_par'			,gettext('Cr&eacute;neau par')); 	

			$semaine = array(gettext('Lundi'),gettext('Mardi'),gettext('Mercredi'),gettext('Jeudi'),gettext('Vendredi'),gettext('Samedi'),gettext('Dimanche'));			
			parent::$_response->addVar('semaine',$semaine); 
			
			$creneau = array(gettext('Heure'),gettext('Demi heure'));			
			parent::$_response->addVar('creneau',$creneau); 
			
			if(parent::$_request->getVar('submit')){	 // Clic sur le bouton Enregistrer
				$error_message = '';
				
				$heure_debut 	= (int) parent::$_request->getVar('heure_debut');
				if (!Planning::testIntegrite('heure_debut', $heure_debut)){
					$error_message .= gettext('Veuillez renseigner une heure de d&eacute;but valide') . '<br />';
				}
				
				$heure_fin 		= (int) parent::$_request->getVar('heure_fin');
				if (!Planning::testIntegrite('heure_fin', $heure_fin) || $heure_fin < $heure_debut){
					$error_message .= gettext('Veuillez renseigner une heure de fin valide') . '<br />';
				}
				
				$jour_debut		= (int) parent::$_request->getVar('jour_debut');
				if (!Planning::testIntegrite('jour_debut', $jour_debut)){
					$error_message .= gettext('Veuillez renseigner une journ&eacute;e de d&eacute;but valide') . '<br />';
				}
				
				$jour_fin		= (int) parent::$_request->getVar('jour_fin');
				if (!Planning::testIntegrite('jour_fin', $jour_fin) || $jour_fin < $jour_debut){
					$error_message .= gettext('Veuillez renseigner une journ&eacute;e de fin valide') . '<br />';
				}
				
				$creneau		= parent::$_request->getVar('creneau');
				if (!Planning::testIntegrite('creneau', $creneau)){
					$error_message .= gettext('Veuillez renseigner un cr&eacute;neau valide') . '<br />';
				}
				else{  
					if((int) parent::$_request->getVar('creneau_ancien') != $creneau){ //on vide le planning					
						dumpDB(array('UTILISATEUR','PLANNING'), '../application/backups/', 'backup-'.time().'.sql');
					}
				} 
				if($error_message == ''){
					
					if (Planning::updateConfig(parent::$_pdo, $heure_debut, $heure_fin, $jour_debut, $jour_fin, $creneau)) {
						
						/* Ecrire dans le fichier 
						$fileName 	= "../application/config/conf/planning.ini";
						$fichier 	= fopen($fileName, 'w');
						
						$texte 		= ";Planning configuration file \r\n\r\n";
						$texte 	   .= 'HEURE_DEBUT = ' . $heure_debut . "\r\n";
						$texte 	   .= 'HEURE_FIN = ' . $heure_fin . "\r\n";		
						$texte 	   .= 'JOURNEE_DEBUT = ' . $jour_debut . "\r\n";		
						$texte 	   .= 'JOURNEE_FIN = ' . $jour_fin . "\r\n";		
						$texte 	   .= 'MODE_CRENEAU = ' . $creneau . "\r\n";		
						
						fputs($fichier, $texte);
						fclose($fichier);
						*/
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la configuration du planning par ') . $_SESSION['user_login']."\r\n";
						$logFile = '../application/logs/'.date('m-Y').'-planning.log';
						writeLog($log, $logFile);
						
						header('Location: ' . APPLICATION_PATH . 'planning/config');
					}
					
				}
				else{
					/* Assigner les diff�rentes varibales pour le template */
					parent::$_response->addVar('form_errors'		, $error_message);
					parent::$_response->addVar('form_heure_debut'	, $heure_debut);
					parent::$_response->addVar('form_heure_fin'		, $heure_fin);
					parent::$_response->addVar('form_jour_debut'	, $jour_debut);
					parent::$_response->addVar('form_jour_fin'		, $jour_fin);
					parent::$_response->addVar('form_creneau'		, $creneau);
				}
			}
			else{
				
				parent::$_response->addVar('form_heure_debut'	, PLANNING_HEURE_DEBUT);
				parent::$_response->addVar('form_heure_fin'		, PLANNING_HEURE_FIN);
				parent::$_response->addVar('form_jour_debut'	, PLANNING_JOURNEE_DEBUT);
				parent::$_response->addVar('form_jour_fin'		, PLANNING_JOURNEE_FIN);
				parent::$_response->addVar('form_creneau'		, PLANNING_MODE_CRENEAU);
				
			}
		}
	}
}
?>