<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * UtilisateurController.php
 *
 * Cette classe permet de gérer toutes les actions permettant la gestion des utilisateurs de l'application.
 *
 */

class UtilisateurController extends BaseController {

 
	public static function index(){	 
		header('Location: '. APPLICATION_PATH .'utilisateur/gerer');
	}	 
	
	
	/*
	 *
	 * Connexion de l'utilisateur à  l'application
	 *
	 */
	public static function connexion(){ 
	
		if(Utilisateur::isConnected()){
			header('Location: '. APPLICATION_PATH);
		}
		/* Assigner les différents messages avec GetText */
		parent::$_response->addVar('txt_connexion'			, gettext('Connexion'));
		parent::$_response->addVar('txt_saisieIdentifiants'	, gettext('Merci de vous identifier'));
		parent::$_response->addVar('txt_login'				, gettext('Identifiant'));
		parent::$_response->addVar('txt_password' 			, gettext('Mot de passe'));
		parent::$_response->addVar('txt_seConnecter'		, gettext('Se connecter'));
		parent::$_response->addVar('txt_recup'				, gettext('R&eacute;cup&eacute;rer le mot de passe'));

		if(parent::$_request->getVar('submit')){	// Clic sur le bouton se connecter
			if(($login = htmlentities(parent::$_request->getVar('login'))) != null){ 
				if (($password = htmlentities(parent::$_request->getVar('password'))) != null){  
					$user = Utilisateur::load_after_login(parent::$_pdo,$login,$password); // Chargement de l'objet utilisateur					
				
					if($user != null){	
						/* Si l'utilisateur a été trouvé */
						$_SESSION['user_id'] 			= $user->getIdUtilisateur(); 
						$_SESSION['user_login'] 		= $user->getLogin();
						$_SESSION['user_template'] 	= $user->getTemplate(); 
						
						/* Mettre à  jour la dernière connexion de l'utilisateur */
						$user->setDerniereConnexion(mktime());
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Connexion de ') .$login ."\r\n";
						$logFile = '../application/logs/'.date('m-Y').'-user.log';
						writeLog($log, $logFile);
						
						header('Location: '. APPLICATION_PATH);	// Redirection vers la page d'accueil
					}
					else {				
						/* Utilisateur non trouvé */
						parent::$_response->addVar('form_errors', gettext('Aucun utilisateur trouv&eacute;')); 
					}
				}
				else{
					/* Pas de mot de passe saisi */
					parent::$_response->addVar('form_errors'	, gettext('Veuillez saisir votre mot de passe')); 
					parent::$_response->addVar('form_login'		, $login);
				}
			}
			else{
				/* Pas de login saisi */
				parent::$_response->addVar('form_errors', gettext('Veuillez saisir votre identifiant')); 
			}
		}
	}
	
	
	
	/*
	 *
	 * Récupération de mot de passe
	 *
	 */
	public static function recup(){ 
		
		parent::$_response->addVar('txt_recup'				, gettext('R&eacute;cup&eacute;rer le mot de passe'));
		parent::$_response->addVar('txt_saisieLogin'		, gettext('Merci de saisir votre identifiant'));
		parent::$_response->addVar('txt_login'				, gettext('Identifiant'));
		parent::$_response->addVar('txt_valider'			, gettext('Valider'));
		parent::$_response->addVar('txt_retour'				, gettext('Retour'));
		parent::$_response->addVar('txt_info'				, gettext('Un email va &ecirc;tre envoy&eacute; aux administrateurs pour signaler votre demande de mot de passe.'));
		
		if(parent::$_request->getVar('submit')){	// Clic sur le bouton se connecter
			if(($login = parent::$_request->getVar('login')) != null){ 
					if(Utilisateur::loginUsed(parent::$_pdo,$login)){
						$users = Utilisateur::loadAll(parent::$_pdo);
						$destinataire = '';
						foreach($users as $user){
							if($user -> getUserLevel() == 3){
								$email = $user -> getEmail();
								if( $email != NULL || $email != '')
									$destinataire .= $email . ',';
								}
						}
						if($destinataire == '')
							parent::$_response->addVar('form_errors', gettext('Aucun administrateur n\'a configur&eacute; d\'adresse email. Merci de contacter un administrateur par un autre moyen.'));
						else{
							$destinataire = substr($destinataire, 0, -1);
							$objet = gettext('Recuperation de mot de passe');
							$message = gettext('Une demande de r&eacute;cup&eacute;ration de mot de passe a &eacute;t&eacute; effectu&eacute;e par l\'utilisateur : ') . $login . ".\r\n";
							// On envoi l’email
							if (mail($destinataire, $objet, $message) ){
								$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('R&eacute;cup&eacute;ration de mot de passe de ') .$login ."\r\n";
								$logFile = '../application/logs/'.date('m-Y').'-user.log';
								writeLog($log, $logFile);
								header('Location: '. APPLICATION_PATH);	// Redirection vers la page d'accueil
							}
							else 
								throw new Exception("Erreur lors de l'envoi du mail.");
						}
					}
					else
						parent::$_response->addVar('form_errors', gettext('Aucun utilisateur trouv&eacute;'));
			}
			else{
				/* Pas de login saisi */
				parent::$_response->addVar('form_errors', gettext('Veuillez saisir votre identifiant')); 
			}
		}
		
	}
	
	
	/*
	 *
	 * Gestion des utilisateurs
	 *
	 */
	public static function gerer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil superviseur	
			/* Assigner les différents messages avec GetText */
			$users 			= Utilisateur::loadAll(parent::$_pdo);				// Chargement de l'objet utilisateur
			
			parent::$_response->addVar('users'					, $users); 				// Assigner l'objet dans le template correspondant	
			parent::$_response->addVar('txt_listeUtilisateurs'	, gettext('Liste des utilisateurs')); 
			parent::$_response->addVar('txt_ajouter'			, gettext('Ajouter un utilisateur'));	
			parent::$_response->addVar('txt_editer'				, gettext('Editer'));	
			parent::$_response->addVar('txt_confirmSuppression'	, gettext('Voulez vous vraiment supprimer'));
			parent::$_response->addVar('txt_lienSupprimer'		, gettext('Supprimer'));	
			
			parent::$_response->addVar('txt_prenom'				, gettext('Pr&eacute;nom'));
			parent::$_response->addVar('txt_nom'				, gettext('Nom'));
			parent::$_response->addVar('txt_email'				, gettext('Email'));
			parent::$_response->addVar('txt_login'				, gettext('Identifiant'));
			parent::$_response->addVar('txt_templateUtilise'	, gettext('Template utilis&eacute;'));
			parent::$_response->addVar('txt_preparateur'		, gettext('Pr&eacute;parateur'));
			parent::$_response->addVar('txt_superviseur'		, gettext('Superviseur'));
			parent::$_response->addVar('txt_administrateur'		, gettext('Administrateur'));
			parent::$_response->addVar('txt_fonction'			, gettext('Fonction'));
			parent::$_response->addVar('txt_derniereConnexion'	, gettext('Derni&egrave;re connexion'));

		}	
	}
	
	/*
	 *
	 * Création d'un utilisateur 
	 *
	 */	
	public static function creer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la création des utilisateurs sont celui du profil superviseur
			
			/* Assigner les variables pour GetText */
			parent::$_response->addVar('txt_retour'				, gettext('Retour'));
			parent::$_response->addVar('txt_prenom'				, gettext('Pr&eacute;nom'));
			parent::$_response->addVar('txt_nom'				, gettext('Nom'));
			parent::$_response->addVar('txt_email'				, gettext('Email'));
			parent::$_response->addVar('txt_login'				, gettext('Identifiant'));
			parent::$_response->addVar('txt_photo'				, gettext('Photo'));
			parent::$_response->addVar('txt_ajouterUtilisateurs', gettext('Ajouter un utilisateur'));
			parent::$_response->addVar('txt_fonction'			, gettext('Fonction'));
			parent::$_response->addVar('txt_templateUtilise'	, gettext('Template utilis&eacute;'));
			parent::$_response->addVar('txt_password'			, gettext('Mot de passe'));
			parent::$_response->addVar('txt_confirmPassword'	, gettext('Confirmation de mot de passe'));
			parent::$_response->addVar('txt_boutonEnregistrer'	, gettext('Enregistrer'));
			parent::$_response->addVar('txt_preparateur'		, gettext('Pr&eacute;parateur'));
			parent::$_response->addVar('txt_superviseur'		, gettext('Superviseur'));
			parent::$_response->addVar('txt_administrateur'		, gettext('Administrateur'));
			parent::$_response->addVar('txt_navigateur'			, gettext('Navigateur'));
			parent::$_response->addVar('txt_tablette'			, gettext('Tablette'));
			parent::$_response->addVar('txt_borne'				, gettext('Borne'));
				
			$myDir			= opendir('../application/tpls/templates/') or die('Erreur');
			$arrayTemplate 	= array();
			while($entry = @readdir($myDir)) {
				if (substr($entry, 0, 1) != ".") 
					array_push($arrayTemplate, $entry);
			}
			closedir($myDir);
			parent::$_response->addVar('arrayTemplate'			, $arrayTemplate);
			
		  
			if(parent::$_request->getVar('submit')){	 // Clic sur le bouton valider
			   
				$error_message = '';	
				
				/* Récupérer le prénom */
				$prenom = parent::$_request->getVar('prenom');
				if(!Utilisateur::testIntegrite('prenom', $prenom)){
					$error_message .= gettext('Veuillez renseigner un pr&eacute;nom valide') . '<br />';
				}
				
				/* Récupérer le nom */
				$nom = parent::$_request->getVar('nom');
				if(!Utilisateur::testIntegrite('nom', $nom)){
					$error_message .= gettext('Veuillez renseigner un nom valide') . '<br />';
				}
				
				/* Récupérer l'email */
				$email = parent::$_request->getVar('email');
				if(!Utilisateur::testIntegrite('email', $email)){
					$error_message .= gettext('Veuillez renseigner un email valide') . '<br />';
				}
				
				/* Récupérer le login */
				$login = parent::$_request->getVar('login');
				if(!Utilisateur::testIntegrite('login', $login)){
					$error_message .= gettext('Veuillez renseigner un identifiant valide') . '<br />';
				}
				else {
					if(Utilisateur::loginUsed(parent::$_pdo, $login))
						/* Saisie d'un login déja utilisé */
						$error_message .= gettext('Votre identifiant est d&eacute;j&agrave; utilis&eacute;. Merci d\'en choisir un autre') . '<br />';
				}
						
				/* Récupérer le mot de passe */
				$password 		= parent::$_request->getVar('password');
				$passwordLength = strlen($password);
				if($passwordLength < 6){
					/* Mot de passe de moins de 6 caractères */
					$error_message .= gettext('Veuillez saisir un mot de passe de plus de 6 caract&egrave;res') . '<br />';
				}
				else if($password == null){
					/* Mot de passe non renseigné */
					$error_message .= gettext('Veuillez renseigner un mot de passe') . '<br />';
				}
				else if (($password_conf = parent::$_request->getVar('password_conf')) == null) {
					/* Mot de passe non confirmé */
					$error_message .= gettext('Veuillez confirmer votre mot de passe') . '<br />';
				}
				else if($password != $password_conf) {
					/* Mots de passe non identiques */
					$error_message .= gettext('Les mots de passes renseign&eacute;s ne sont pas identiques') . '<br />';
				}
				
				/* Récupérer la fonction de l'utilisateur */
				$user_level = (int) parent::$_request->getVar('level');
				if(!Utilisateur::testIntegrite('user_level', $user_level)){
					$error_message .= gettext('La fonction que vous avez choisi n\'existe pas') . '<br />';
				}
				
				/* Récupérer le jeu de template choisi */
				$template = parent::$_request->getVar('template');
				if(!Utilisateur::testIntegrite('template', $template)){
					$error_message .= gettext('Le template que vous avez choisi n\'existe pas') . '<br />';
				}
				
				/* Récupérer la photo de l'utilisateur */
				// $photosDir  	= dirname(dirname(__DIR__)) . '/www/images/photos/';
				$photosDir  	= './images/photos/';
				$photo 			= null;
				$arrayPhoto 	= $_FILES['photo'];
				if ($arrayPhoto != null){
					if ($arrayPhoto['error']) {    
						switch ($arrayPhoto['error']){    
								case 1: // UPLOAD_ERR_INI_SIZE    
									$error_message .= 'Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !' . '<br />';    
								break;    
								
								case 2: // UPLOAD_ERR_FORM_SIZE    
									$error_message .= 'Le fichier dépasse la limite autorisée dans le formulaire HTML !' . '<br />';
								break;    
							   
								case 3: // UPLOAD_ERR_PARTIAL    
									$error_message .= 'L\'envoi du fichier a été interrompu pendant le transfert !' . '<br />';
								break;    
							   
								case 4: // UPLOAD_ERR_NO_FILE    
									/* Aucune photo n'a été choisie, on prend celle par défaut */
									$error_message .= '';
									$photo = 'default.png';
								break;    
						}    
					}    
					else {    
						if (isset($arrayPhoto)) {
							$photo = 'img_profile' . mktime() . '.jpg';    
							if (!move_uploaded_file($arrayPhoto['tmp_name'], $photosDir . $photo)){ 	// Déplacer la photo dans le dossier /www/images/photos/
								throw new Exception(gettext('Erreur lors du d&eacute;placement du fichier') . ' ' . $arrayPhoto['name'] . ' ' . gettext('de') . ' ' . $arrayPhoto['tmp_name'] . ' ' . gettext('vers') . ' ' . $photo);
							}
						}
					} 
				}
					
				if($error_message == ''){					
					$user = Utilisateur::create(parent::$_pdo,$login,$password,$nom,$prenom,$email,$user_level,$template,$photo,null,1);	// Ajout de l'utilisateur dans la base de données
					resize_image($photo,'profil',128,128);																		// Reddimensionner la taille de l'image
					$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Cr&eacute;ation de l\'utilisateur ') .$login .gettext(' par ').$_SESSION['user_login']."\r\n";
					$logFile = '../application/logs/'.date('m-Y').'-user.log';
					writeLog($log, $logFile);
					header('Location: '. APPLICATION_PATH . 'utilisateur/gerer');												// Redirection vers la page gestion des utilisateurs
				}
				else{	
					/* Assigner les différentes varibales pour le template */
					parent::$_response->addVar('form_errors'	, $error_message);
					parent::$_response->addVar('form_prenom'	, $prenom);
					parent::$_response->addVar('form_nom'		, $nom);
					parent::$_response->addVar('form_email'		, $email);
					parent::$_response->addVar('form_login'		, $login);
					parent::$_response->addVar('form_level'		, $user_level);
					parent::$_response->addVar('form_template'	, $template);
				}			
			}
			else{			
				//affichage du formulaire de creation		

					parent::$_response->addVar('form_errors'	, '');
					parent::$_response->addVar('form_prenom'	, '');
					parent::$_response->addVar('form_nom'		, '');
					parent::$_response->addVar('form_email'		, '');
					parent::$_response->addVar('form_login'		, '');
					parent::$_response->addVar('form_level'		, '');
					parent::$_response->addVar('form_template'	, '');				
			}
		}	
	}

	/*
	 *
	 * Edition d'un utilisateur
	 *
	 */	
	public static function editer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_PREPARATEUR)){
			$id = parent::$_request->getVar('id');	// Récupération de l'identifiant de l'utilisateur à modifier
			if(isset($id)){					
				$user 			= Utilisateur::load(parent::$_pdo, $id); 	// Chargement de l'objet utilisateur				
				if ($user != null) {
					if(Utilisateur::isAllowed(parent::$_pdo,$user->getUserLevel())){
					
						/* Assigner les variables pour GetText */
						parent::$_response->addVar('txt_confirmSuppression'	, gettext('Voulez vous vraiment supprimer'));
						parent::$_response->addVar('txt_lienSupprimer'		, gettext('Supprimer l\'utilisateur'));
						parent::$_response->addVar('txt_retour'				, gettext('Retour'));
						parent::$_response->addVar('txt_prenom'				, gettext('Pr&eacute;nom'));
						parent::$_response->addVar('txt_nom'				, gettext('Nom'));
						parent::$_response->addVar('txt_email'				, gettext('Email'));
						parent::$_response->addVar('txt_login'				, gettext('Identifiant'));
						parent::$_response->addVar('txt_fonction'			, gettext('Fonction'));
						parent::$_response->addVar('txt_templateUtilise'	, gettext('Template utilis&eacute;'));
						parent::$_response->addVar('txt_photo'				, gettext('Photo'));
						parent::$_response->addVar('txt_supprimerPhoto'		, gettext('Supprimer la photo'));
						parent::$_response->addVar('txt_editerUtilisateurs'	, gettext('Editer un utilisateur'));
						parent::$_response->addVar('txt_password'			, gettext('Mot de passe'));
						parent::$_response->addVar('txt_confirmPassword'	, gettext('Confirmation de mot de passe'));
						parent::$_response->addVar('txt_boutonEnregistrer'	, gettext('Enregistrer'));
						parent::$_response->addVar('txt_preparateur'		, gettext('Pr&eacute;parateur'));
						parent::$_response->addVar('txt_superviseur'		, gettext('Superviseur'));
						parent::$_response->addVar('txt_administrateur'		, gettext('Administrateur'));
						parent::$_response->addVar('txt_navigateur'			, gettext('Navigateur'));
						parent::$_response->addVar('txt_tablette'			, gettext('Tablette'));
						parent::$_response->addVar('txt_borne'				, gettext('Borne'));
						
						$myDir			= opendir('../application/tpls/templates/') or die('Erreur');
						$arrayTemplate 	= array();
						while($entry = @readdir($myDir)) {
							if (substr($entry, 0, 1) != ".")	
								array_push($arrayTemplate, $entry);
						}
						closedir($myDir);
						parent::$_response->addVar('arrayTemplate'			, $arrayTemplate);
						
						if(parent::$_request->getVar('submit')){	 // Clic sur le bouton modifier
							$error_message = '';
							
							$prenom = parent::$_request->getVar('prenom');
							if(!Utilisateur::testIntegrite('prenom', $prenom)){
								$error_message .= gettext('Veuillez renseigner un pr&eacute;nom valide') . '<br />';
							}
							
							$nom = parent::$_request->getVar('nom');
							if(!Utilisateur::testIntegrite('nom', $nom)){
								$error_message .= gettext('Veuillez renseigner un nom valide') . '<br />';
							}
							
							$email = parent::$_request->getVar('email');
							if(!Utilisateur::testIntegrite('email', $email)){
								$error_message .= gettext('Veuillez renseigner un email valide') . '<br />';
							}
							
							$login = parent::$_request->getVar('login');
							if(!Utilisateur::testIntegrite('login', $login)){
								$error_message .= gettext('Veuillez renseigner un identifiant valide') . '<br />';
							}
							else {
								if(Utilisateur::loginUsed(parent::$_pdo, $login, parent::$_request->getVar('id')))
									/* Saisie d'un login déja utilisé */
									$error_message .= gettext('Votre identifiant est d&eacute;j&agrave; utilis&eacute;. Merci d\'en choisir un autre') . '<br />';
							}
							
							$password 		= parent::$_request->getVar('password');
							$passwordLength = strlen($password);
							if($passwordLength > 0 && $passwordLength < 6){
								$error_message .= gettext('Veuillez saisir un mot de passe de plus de 6 caract&egrave;res') . '<br />';
							}
							else if($password != null){
								if(($password_conf = parent::$_request->getVar('password_conf')) == null){
									$error_message .= gettext('Veuillez confirmer votre mot de passe') . '<br />';
								}
								else if ($password != $password_conf) {
									$error_message .= gettext('Les mots de passes renseign&eacute;s ne sont pas identiques') . '<br />';
								}
							} 
							$user_level = (int) parent::$_request->getVar('level');
							if(Utilisateur::checkRights(parent::$_pdo,PROFIL_SUPERVISEUR)){
								if(!Utilisateur::testIntegrite('user_level', $user_level)){
									$error_message .= gettext('La fonction que vous avez choisi n\'existe pas') . '<br />';
								}
							} 
							
							$template = parent::$_request->getVar('template');
							if(!Utilisateur::testIntegrite('template', $template)){
								$error_message .= gettext('Le template que vous avez choisi n\'existe pas') . '<br />';
							}
							
							/* Récupérer la photo de l'utilisateur */
							$photoDefault 	= parent::$_request->getVar('supprimerPhoto');
							$photosDir  	= './images/photos/';
							$photo 			= null;
							if (!$photoDefault){
								$arrayPhoto = $_FILES['photo'];
								if ($arrayPhoto != null){
									if ($arrayPhoto['error']) {    
										switch ($arrayPhoto['error']){    
												case 1: // UPLOAD_ERR_INI_SIZE    
													$error_message .= 'Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !' . '<br />';    
												break;    
												
												case 2: // UPLOAD_ERR_FORM_SIZE    
													$error_message .= 'Le fichier dépasse la limite autorisée dans le formulaire HTML !' . '<br />';
												break;    
											   
												case 3: // UPLOAD_ERR_PARTIAL    
													$error_message .= 'L\'envoi du fichier a été interrompu pendant le transfert !' . '<br />';
												break;    
											   
												case 4: // UPLOAD_ERR_NO_FILE    
													/* Aucune photo n'a été choisie, on garde l'ancienne */
													$error_message .= '';
												break;    
										}    
									}    
									else {    
										if (isset($arrayPhoto)) {
											$photo = 'img_profile' . mktime() . '.jpg';    
											if (!move_uploaded_file($arrayPhoto['tmp_name'], $photosDir . $photo)){ 	// Déplacer la photo dans le dossier /www/images/photos/
												throw new Exception(gettext('Erreur lors du d&eacute;placement du fichier') . ' ' . $arrayPhoto['name'] . ' ' . gettext('de') . ' ' . $arrayPhoto['tmp_name'] . ' ' . gettext('vers') . ' ' . $photosDir . $photo);
											}
										}
									} 
								}
							}
							else {
								$photo = 'default.png';
							}
							
							if($error_message == ''){					
								$user = Utilisateur::load(parent::$_pdo, parent::$_request->getVar('id'));	// Chargement de l'objet utilisateur
								if ($user != null) {
									/* Mise à jour des informartions de l'utilisateur */
									$user->setPrenom($prenom,false);
									$user->setNom($nom,false);
									$user->setEmail($email,false);
									$user->setLogin($login,false);
									if($password != null) $user->setPassword(md5($password),false);
									if(Utilisateur::checkRights(parent::$_pdo,PROFIL_SUPERVISEUR)) if($user_level != null) $user->setUserLevel($user_level,false);
									if($id == $_SESSION['user_id'])
										$_SESSION['user_template'] = $template; // on met à jour le jeu de template utilisé
									$user->setTemplate($template,false);
									if ($photo != null) {
										if ($user->getPhoto() != 'default.png'){
											unlink($photosDir . $user->getPhoto());		// Supprimer l'ancienne photo
										}
										resize_image($photo,'profil',128,128);
										$user->setPhoto($photo,false);	
									}
									$user->write();								// Ecriture dans la base de données 
									$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Edition de l\'utilisateur ') .$login .gettext(' par ').$_SESSION['user_login']."\r\n";
									$logFile = '../application/logs/'.date('m-Y').'-user.log';
									writeLog($log, $logFile);
									if(Utilisateur::load(parent::$_pdo,$_SESSION['user_id'])->getUserLevel() == PROFIL_PREPARATEUR)									
										header('Location: ' . APPLICATION_PATH);							// Redirection vers la page d'accueil pour le profil préparateur
									else
										header('Location: ' . APPLICATION_PATH . 'utilisateur/gerer');	// Redirection vers la page gestion des utilisateurs
								}
								else {
									throw new Exception(gettext('Param&egrave;tre invalide : Identifiant de l\'utilisateur inconnu'),5);
								}	
							}
							else{
								/* Assigner les différentes variables pour le template */
								parent::$_response->addVar('form_id'		, parent::$_request->getVar('id'));  
								parent::$_response->addVar('form_errors'	, $error_message);
								parent::$_response->addVar('form_prenom'	, $prenom);
								parent::$_response->addVar('form_nom'		, $nom);
								parent::$_response->addVar('form_email'		, $email);
								parent::$_response->addVar('form_login'		, $login);
								parent::$_response->addVar('form_level'		, intval($user_level));
								parent::$_response->addVar('form_template'	, $template);
								parent::$_response->addVar('form_photo'		, $user->getPhoto());
							}		
						}
						else{
							/* Chargement de la page pour la première fois */
							/* Assigner les différentes variables pour le template */
							parent::$_response->addVar('form_id'			, $user->getIdUtilisateur());					
							parent::$_response->addVar('form_login'			, $user->getLogin()); 
							parent::$_response->addVar('form_prenom'		, $user->getPrenom());
							parent::$_response->addVar('form_nom'			, $user->getNom());
							parent::$_response->addVar('form_email'			, $user->getEmail());
							parent::$_response->addVar('form_password'		, $user->getPassword());
							parent::$_response->addVar('form_password_conf'	, $user->getPassword());
							parent::$_response->addVar('form_level'			, $user->getUserLevel());
							parent::$_response->addVar('form_template'		, $user->getTemplate());
							parent::$_response->addVar('form_photo'			, $user->getPhoto());							
						}
					}
				}
				else {
					throw new Exception(gettext('Param&egrave;tre invalide : Identifiant de l\'utilisateur inconnu'),5);
				}
				
			}
			else{
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e : identifiant de l\'utilisateur'),3);
			}
		}
	}

	/*
	 *
	 * Suppression d'un utilisateur
	 *
	 */	
	public static function supprimer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Les droits minimums pour la suppression des utilisateurs sont celui du profil superviseur
			$id = parent::$_request->getVar('id');	// Récupération de l'identifiant de l'utilisateur à supprimer
			if(isset($id)){
				if($_SESSION['user_id'] != $id){					
					$user = Utilisateur::load(parent::$_pdo, $id);	// Chargement de l'objet utilisateur
					if ($user != null) {
						if(Utilisateur::isAllowed(parent::$_pdo,$user->getUserLevel())){
							/* Vérification de l'absence de preparation en attente ou en cours */
							$erreur = false;
							$arrayPrepas = Preparation::selectByUtilisateur(parent::$_pdo, $user);
							foreach($arrayPrepas as $prepa){
								if ($prepa -> getEtat() < 2)
									$erreur = true;
							}
							
							if($erreur){
								parent::$_response->addVar('txt_erreur'	, gettext('Impossible de supprimer cet utilisateur car il poss&egrave;de des pr&eacute;parations en attente ou en cours.'));
								parent::$_response->addVar('txt_retour'	, gettext('Retour'));
							}
							else{						
								/* Suppression de l'utilisateur */
								if ($user->delete()) {
									$photosDir  = './images/photos/';
									$photo 		= $user->getPhoto();
									if ($photo != 'default.png')
										unlink($photosDir . $photo);	// Supprimer la photo de profil de l'utilisateur
									$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Suppression de l\'utilisateur ') .$login .gettext(' par ').$_SESSION['user_login']."\r\n";
									$logFile = '../application/logs/'.date('m-Y').'-user.log';
									writeLog($log, $logFile);
									/* Redirection vers la page gestion des utilisateurs */
									header('Location: '. APPLICATION_PATH . 'utilisateur/gerer') or die('deleting problem');
								}
								else {
									throw new Exception(gettext('Impossible de supprimmer un utilisateur'),4);
								} 
							}
						}
					}
					else {
						/* Redirection vers la page gestion des utilisateurs */
						header('Location: '. APPLICATION_PATH . 'utilisateur/gerer') or die('deleting problem');
					}
				}
				else{
					/* Redirection vers la page gestion des utilisateurs */
					header('Location: '. APPLICATION_PATH . 'utilisateur/gerer') or die('deleting problem');
				}
			}
			else{
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
			}
		}
	}
	
	/*
	 *
	 * Déconnexion de l'utilisateur de l'application
	 *
	 */	
	public static function deconnexion(){	
		$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Deconnexion de ').$_SESSION['user_login']."\r\n";
		$logFile = '../application/logs/'.date('m-Y').'-user.log';
		writeLog($log, $logFile);	
		session_unset();
		session_destroy();
		header('Location: ' . APPLICATION_PATH);
	}
}
?>