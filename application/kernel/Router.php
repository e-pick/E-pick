<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * ROUTER.PHP
 * 
 * La classe routeur permet de lancer une action dans un controlleur en r�cup�rant ces 
 * informations dans la requ�te qui a la forme /controller/action. Les erreurs sont intercept�es
 * ici pour �tre redirig�es vers la page d'erreur.
 */
 
 
class Router{ 

	/* @param object Request : contient la requ�te de l'utilisateur */
	protected static $_request;
	/* @param object Response : contient les variables � transmettre au template */
	protected static $_response;
	/* @param object Smarty : instance du moteur de template */
	protected static $smarty;
	/* @param object ownPDO : instance de la classe ownPDO qui h�rite de la classe PDO */
	protected static $_pdo;
 
	/**
	 * M�thode run, lance soit une action soit l'affichage d'une erreur
	 * @param $type contient le type du run � effectuer, soit normal pour lancer l'action, soit error pour afficher une erreur attrap�e
	 * @param $redirect contient le type d'erreur que l'on peut avoir : http404,http500
	 * @param $exception contient l'exception lev�e, pour l'afficher
	 */ 
	 
	 	
	 
	 
	public static function run($type = 'normal', $redirect = null, $exception = null){ 
		switch($type){
			case 'error' :
			//echo $_SERVER['HTTP_REFERER'] . "<br>".$_SERVER['REQUEST_URI'];
			//exit;

				switch($redirect){
					case 'http404' :						
					case 'http500' :
					default :						  			
						header('Location: '. APPLICATION_PATH .'error.php?type='.$redirect.'&message='.$exception->getMessage().'&code='.$exception->getCode().'&url='.APPLICATION_PATH); 							 
				}
				break;			
			case 'normal' :
			default :			
				/* cr�ation des objets request et response */
				self::$_request 	= new Request($_REQUEST); 
				self::$_response 	= new Response(); 				
				$controller 		= self::$_request->getController();  
				$action				= self::$_request->getAction();

				if ($controller != 'affectation' && ($action != 'manuelle' || $action != 'choixutilisateur' || $action != 'config')){
					unset($_SESSION['type']);
					unset($_SESSION['idEtage']);
					unset($_SESSION['array_preparations']);
					unset($_SESSION['retour']);
				}
				self::load($controller,$action);		
		} 
	}
	
 	/**
	 * M�thode load, ex�cute une action d'un controller
	 * @param $controller string
	 * @param $action string par d�faut l'action ex�cut�e est "index"
	 */
	public static function load ($controller, $action = null) {
		
		//lancement de l'action dans le controller
		
        if (empty($action)) $action = "index";
        try { 
			self::$_pdo = DB :: getInstance();	
			parseApplicationConfigFile(self::$_pdo, self::$smarty);	
			parsePlanningFileConf(self::$_pdo, self::$smarty);
			parseAffectationFileConf(self::$_pdo);

			$controller = ucfirst($controller).'Controller'; 
			if(!method_exists($controller,'_init') || !method_exists($controller,$action)) 
				throw new BadMethodCallException(gettext('L\'action demand&eacute;e n\'existe pas.'),404);
            // $controller::_init(self::$_request, self::$_response, self::$_pdo); 
			call_user_func_array(array($controller,'_init') , array(self::$_request, self::$_response, self::$_pdo));
			call_user_func(array($controller,$action));
		
			// $controller::$action();
        }
        catch (BadMethodCallException $e) { // si une des m�thodes ex�cut�es n'existe pas
			return self::run("error", "http404", $e);
        }
        catch (Exception $e) { // pour toutes les exceptions lev�es dans les actions
			if($e->getCode() == 1) // le code 0x001 correspond � un utilisateur non connect�, on le redirige directement vers le formulaire de connexion
				header('Location: '. APPLICATION_PATH .'utilisateur/connexion');
			else //sinon on l�ve une erreur 500
				return self::run("error", "http500", $e);
        }
        
		//chargement et affichage du template qui correspond � l'action effectu�e
	
		if(isset($_SESSION['user_template']) && is_dir(self::$smarty->template_dir.$_SESSION['user_template'])){
			$view_path =  self::$smarty->template_dir . $_SESSION['user_template'] . '/' .strtolower(str_replace('Controller', '', $controller)) . '/' . strtolower($action) . '.' . self::$_response->getType() . '.tpl';
			self::$smarty->assign('user_template'			, $_SESSION['user_template']);
			}
		else{
			$view_path =  self::$smarty->template_dir . DEFAULT_TEMPLATE .'/' .strtolower(str_replace('Controller', '', $controller)) . '/' . strtolower($action) . '.' . self::$_response->getType() . '.tpl';
			self::$smarty->assign('user_template'			, DEFAULT_TEMPLATE);
		}
			
		try {  
			ViewManager::_init(self::$smarty); 
			ViewManager::setHeader(self::$_response->getType());
			ViewManager::setVars(self::$_response->getVars());
			ViewManager::load($view_path);
		}
		catch (Exception $e) { //si le template n'est pas trouv�
			return self::run("error", "http500", $e); 
		}
	 
    }
	
	/**
	 * SetSmarty
	 * @param $smarty object smarty
	 * @return true si ok, false sinon
	 */
	public static function setSmarty($smarty){
		if(is_a($smarty,'Smarty')){
			self::$smarty = $smarty;	
			return true;
		}
		else
			return false;
	}
	 
	
	/**
	 * SetPDO
	 * @param $pdo object ownPDO
	 * @return true si ok, false sinon
	 */
	public static function setPdo($pdo){
		if(is_a($pdo,'ownPDO')){
			self::$_pdo = $pdo;	
			return true;
		}
		else
			return false;
	}
 
}
?>