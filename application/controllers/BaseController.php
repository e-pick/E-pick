<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * BaseController.php
 *
 * Cette classe contient tous les attributs et méthodes basiques et communs à tous les autres controleurs.
 *
 */

abstract class BaseController {
 
  protected static $_request;
  protected static $_response;
  protected static $_pdo;
 
  abstract public static function index (); // action par défaut
 
public static function _init(Request $_request, Response $_response, ownPDO $_pdo){
		self::setRequest($_request);
		self::setResponse($_response);
		self::setPdo($_pdo);
	
		$isConnected = Utilisateur::isConnected(); 
		self::$_response->addVar('isConnected',$isConnected); 
		if($isConnected) { 
			$user = Utilisateur::load(self::$_pdo,$_SESSION['user_id']);
			self::$_response->addVar('userLevel'	, $user->getUserLevel());
			self::$_response->addVar('userLogin'	, $user->getLogin());
			self::$_response->addVar('userPrenom'	, $user->getPrenom());
			self::$_response->addVar('userNom'		, $user->getNom());
			self::$_response->addVar('userId'		, $_SESSION['user_id']);
		}
		
		if(isset($_SESSION['user_feeedback']) && !empty($_SESSION['user_feeedback'])){
			if($_SESSION['user_feeedback'][2] == 0)
				unset($_SESSION['user_feeedback']);
			else {
				$_SESSION['user_feeedback'][2]--;     
				self::$_response->addVar('user_feeedback',$_SESSION['user_feeedback']);    
			}
		}
	} 
 
  final public static function setRequest (Request &$request) {
    self::$_request = $request;
  }
 
  final public static function setResponse (Response &$response) {
    self::$_response = $response;
  }
  final public static function setPdo (ownPDO &$pdo) {
    self::$_pdo = $pdo;
  }
} 
 
?>