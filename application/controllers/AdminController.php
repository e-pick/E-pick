<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * AdminController.php
 *
 */

class AdminController extends BaseController {


	public static function index(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil administrateur		
			
		}	
	}
	
	public static function stats(){		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){	// Les droits minimums pour la gestion des utilisateurs sont celui du profil administrateur		
			/* Récupération des valeurs du filter */
			$libelleFilter 	= parent::$_request->getVar('libelleFilter');
			$codeFilter 	= parent::$_request->getVar('codeFilter');
			$pageFilter 	= parent::$_request->getVar('pageFilter');
			$filtre 		= false;
			
			$produits 		= array();
			$page			= 1;  
			
			if (parent::$_request->getVar('submitFilter')){	// Clic sur le bouton filtrer
				$filtre = true;
				$page 	= $pageFilter;	// Pagination
				$nb_produits		= count(Produit::loadAll(parent::$_pdo,null,null,null,null,null,null, $libelleFilter, $codeFilter, null,null, 'p.PRO_TEMPS_MOYEN_ACCESS'));	
				$nombre_de_pages 	= (ceil($nb_produits/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_produits/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);	 
				$produits = Produit::loadAll(parent::$_pdo,''.$first,null,null,null,null,null, $libelleFilter, $codeFilter, null,null, 'p.PRO_TEMPS_MOYEN_ACCESS');	

				parent::$_response->addVar('form_libelleFilter'	, $libelleFilter);
				parent::$_response->addVar('form_codeFilter'	, $codeFilter);
				parent::$_response->addVar('form_page'			, $page);
			}
			else{ 
				$nb_produits		= count(Produit::loadAll(parent::$_pdo, null, null, null, null, null, null, null, null, null, null, 'p.PRO_TEMPS_MOYEN_ACCESS'));
				$nombre_de_pages 	= (ceil($nb_produits/RESULTAT_PAR_PAGE) == 0) ? 1 : ceil($nb_produits/RESULTAT_PAR_PAGE);
				if($page > $nombre_de_pages) 
					$page = $nombre_de_pages;
					
				$first 	  = ((($page-1)*RESULTAT_PAR_PAGE) < 0) ? 0 : (($page-1)*RESULTAT_PAR_PAGE);	
				$produits = Produit::loadAll(parent::$_pdo, ''.$first, null, null, null, null, null, null, null, null, null, 'p.PRO_TEMPS_MOYEN_ACCESS');
				parent::$_response->addVar('form_etage'	, '');
				parent::$_response->addVar('form_page'	, $page);
				
			}
			
			
			
			parent::$_response->addVar('produits'			,$produits); 
			parent::$_response->addVar('txt_ligne_codeProduit',gettext('Code produit'));
			parent::$_response->addVar('txt_ligne_libelleProduit',gettext('Libelle'));
			parent::$_response->addVar('txt_ligne_tempsMoyen',gettext('Temps moyen d\'acc&egrave;s'));
			parent::$_response->addVar('txt_no_ligne'		,gettext('Aucun produit trouv&eacute;'));
			parent::$_response->addVar('txt_effacer'		, gettext('Effacer'));
			parent::$_response->addVar('txt_filtre'			, gettext('Filtre'));			
			parent::$_response->addVar('txt_filtrer'		, gettext('Filtrer'));	
			parent::$_response->addVar('txt_code_produit'	, gettext('Code produit'));	 	
			parent::$_response->addVar('txt_libelle_produit', gettext('Libell&eacute; du produit'));	
			parent::$_response->addVar('nb_resultats'		, $nb_produits);
			parent::$_response->addVar('nb_resultats_par_page', RESULTAT_PAR_PAGE);
			parent::$_response->addVar('txt_nb_resultats'	, gettext('r&eacute;sultats sur')); 
			parent::$_response->addVar('txt_pages'			, gettext('pages')); 
			parent::$_response->addVar('nombre_de_pages'	, $nombre_de_pages);
			parent::$_response->addVar('page'				, $page);
			parent::$_response->addVar('txt_page'			, gettext('Page'));

		}	
	}
	
	public static function vider(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
			dumpDB(null, '../application/backups/', 'backup-'.time().'.sql', true);

			Commande::vider(parent::$_pdo);
			//Produit::vider(parent::$_pdo);
			
			parent::$_response->addVar('txt_vidage', gettext('La base a &eacute;t&eacute; bien vid&eacute;e'));
			parent::$_response->addVar('txt_retour', gettext('Retour &agrave; l\'accueil'));
			
			$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Vidage de la base produit/commande par ') .$_SESSION['user_login']."\r\n";
			$logFile = '../application/logs/'.date('m-Y').'-admin.log';
			if(!writeLog($log, $logFile)) die("Erreur écriture de log");
		}	
	}
	
	
	
	
		
	public static function langue(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){		
			if(parent::$_request->getVar('editer') != ''){
				$langue = parent::$_request->getVar('langue');
				header('Location:' . APPLICATION_PATH .'admin/traduction/'.$langue);
			}
			else if(parent::$_request->getVar('ajouter') != ''){
				$langue = parent::$_request->getVar('new_langue');
				mkdir("../application/lang/".$langue, 0776);
				mkdir("../application/lang/".$langue."/LC_MESSAGES", 0776);
				copy("../application/lang/.messages.pot", "../application/lang/".$langue."/LC_MESSAGES/messages.po");
				header('Location:' . APPLICATION_PATH .'admin/traduction/'.$langue);
			}
			else{
				$langues = scandir("../application/lang/");
				foreach($langues as $langue){
					if(substr($langue,0,1) != '.'){
						$arrayLangues[] = $langue;
					}
				}
				parent::$_response->addVar('arrayLangues', $arrayLangues);
				parent::$_response->addVar('txt_langue', gettext('Langue'));
				parent::$_response->addVar('txt_choix_langue', gettext('Choisir la langue &agrave; &eacute;diter'));
				parent::$_response->addVar('txt_editer', gettext('Editer'));
				parent::$_response->addVar('txt_ajouter', gettext('Ajouter'));
				parent::$_response->addVar('txt_new', gettext('Ajouter une langue'));
			}
		}
	}
	
	public static function traduction(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
			$langue = parent::$_request->getVar('lang');
			if(!isset($langue))
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);
			
			$po_file = '../application/lang/'. $langue . '/LC_MESSAGES/messages.po';
			
			if(parent::$_request->getVar('enregistrer') != ''){
				$arrayMessages = parent::$_request->getVar('messages');
			 	$po_lines = file($po_file);
				// skip header
				do {
					$line = array_shift($po_lines);
					$outlines[] = $line;
				} while ($line && !preg_match('/^#: (.*)$/', $line, $matches));
				
				$msgstr_lines = array();
				$msgid_lines = array();
				
				while ($line = array_shift($po_lines)) {
					// multi lignes
					if (preg_match('/^"(.*)"$/', $line, $matches)) {
						if (!empty($msgstr_lines)) {
							$msgstr_lines[] = $line;
							continue;
						} elseif (!empty($msgid_lines)) {
							$msgid .= $matches[1];
							$msgid_lines[] = $line;
							continue;
						}
					}
					// ligne blanche, séparateur
					elseif (preg_match('/^$/', $line)) {
							$outlines = array_merge($outlines, $msgid_lines);
							$outlines[] = 'msgstr "'.htmlentities($arrayMessages["'".$msgid."'"]).'"'."\n";
						$msgstr_lines = array();
						$msgid_lines = array();
					}
					// debut msgid
					elseif (preg_match('/^msgid "(.*)"$/', $line, $matches)) {
					  $msgid = $matches[1];
					  $msgid_lines = array($line);
					  continue;
					}
					// debut msgstr
					elseif (preg_match('/^msgstr "(.*)"$/', $line, $matches)) {
					  $msgstr = $matches[1];
					  $msgstr_lines = array($line);
					  continue;
					}
					// commentaire
					elseif (preg_match('/^# (.*)$/', $line, $matches)) {
					  continue;
					}
					$outlines[] = $line;
				  
				 }
					$outlines = array_merge($outlines, $msgid_lines);
					$outlines[] = 'msgstr "'.htmlentities($arrayMessages["'".$msgid."'"]).'"'."\n";
					$outlines[] = $line;


				  file_put_contents($po_file, $outlines);
				  //Compilation en mo
				  $output = substr($po_file, 0, -2).'mo'; // Remplace .po par .mo
				  $command = 'msgfmt '.
				  '-o '.$output.' '.
				  $po_file;
				  exec($command);
			}
			$messages = array();
			$msgid='';
			$msgstr = '';
			$references = '';
			
			$po_lines = file($po_file);
			// skip header
			do {
				$line = array_shift($po_lines);
			} while ($line && !preg_match('/^#: (.*)$/', $line, $matches));
			
			
			$references[] = isset($matches[1])?$matches[1]:'';
			while ($line = array_shift($po_lines)) {
				// multi lignes
				if (preg_match('/^"(.*)"$/', $line, $matches)) {
					$last_item .= $matches[1];
					continue;
				}
				// ligne blanche, séparateur
				if (preg_match('/^$/', $line) && $msgid) {
					$messages[$msgid] = array(
						'msgstr' => $msgstr,
						'references' => $references,
					);
					$msgid='';
					$msgstr = '';
					$references = '';
					continue;
				}
				if (preg_match('/^#: (.*)$/', $line, $matches)) {
				  $references[] = $matches[1];
				  continue;
				}
				if (preg_match('/^# (.*)$/', $line, $matches)) {
				  //commentaire
				  continue;
				}
				if (preg_match('/^msgid "(.*)"$/', $line, $matches)) {
				  $msgid = $matches[1];
				  $last_item = &$msgid;
				  continue;
				}
				if (preg_match('/^msgstr "(.*)"$/', $line, $matches)) {
				  $msgstr = $matches[1];
				  $last_item = &$msgstr;
				  continue;
				}
			}
			if ($msgid)
				$messages[$msgid] = array(
					'msgstr' => $msgstr,
					'references' => $references,
				);
			
			parent::$_response->addVar('messages', $messages);
			parent::$_response->addVar('txt_messages', gettext('Messages &agrave; traduire'));
			parent::$_response->addVar('txt_traduction', gettext('Traduction'));
			parent::$_response->addVar('txt_references', gettext('R&eacute;f&eacute;rences'));
			parent::$_response->addVar('txt_enregistrer', gettext('Enregistrer'));
			
		}
	}
	
	/* Action utilisateurscreneau
	 * Profil requis : >= Profil administrateur  
	 */	
	public static function config(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
			parent::$_response->addVar('txt_configApplication'			,gettext('Configuration de l\'application')); 		 
			parent::$_response->addVar('txt_boutonEnregistrer'			,gettext('Enregistrer')); 		 
			parent::$_response->addVar('txt_Parametres_generaux'		,gettext('Param&egrave;tres g&eacute;n&eacute;raux')); 		 
			parent::$_response->addVar('txt_path'						,gettext('Chemin de l\'application')); 		 
			parent::$_response->addVar('txt_prefix'						,gettext('Pr&eacute;fix de l\'application')); 	
			parent::$_response->addVar('txt_fuseau'						,gettext('Fuseau horaire'));
			parent::$_response->addVar('txt_language'					,gettext('Langue'));
			parent::$_response->addVar('txt_abbreviation_language'		,gettext('Abbr&eacute;viation langue'));
			parent::$_response->addVar('txt_devise'						,gettext('Devise'));
			parent::$_response->addVar('txt_resultat_page'				,gettext('Nombre de r&eacute;sultats par page'));
			parent::$_response->addVar('txt_email_rapport'				,gettext('Emails pour rapport (S&eacute;parer avec  ",")'));
			parent::$_response->addVar('txt_Parametres_geolocalisation'	,gettext('Param&egrave;tres g&eacute;olocalisation')); 		 
			parent::$_response->addVar('txt_nombre_etages'				,gettext('Nombre d\'&eacute;tage au magasin')); 		 
			parent::$_response->addVar('txt_finesse'					,gettext('Finesse utilis&eacute;e pour la g&eacute;olocalisation')); 		
			parent::$_response->addVar('txt_largeur_etagere'			,gettext('Largeur des &eacute;tag&egrave;res')); 		 
			parent::$_response->addVar('txt_delai_livraison'			,gettext('D&eacute;lai avant la livraison')); 		 	 
			parent::$_response->addVar('txt_tempsMoyenAccesProduit'		,gettext('Temps moyen d\'acces aux produits')); 	

			$langues = scandir("../application/lang/");
				foreach($langues as $langue){
					if(substr($langue,0,1) != '.'){
						$arrayLangues[] = $langue;
					}
				}
			parent::$_response->addVar('arrayLangues', $arrayLangues);
				
			$finesse = array('',gettext('Rayon'),gettext('Segment'),gettext('Etag&egrave;re'));			
			parent::$_response->addVar('arrayFinesse',$finesse); 
			
			if(parent::$_request->getVar('submit')){	 // Clic sur le bouton Enregistrer
				$error_message = '';
				
				$appliPath 	= parent::$_request->getVar('appliPath');
				if (!Application::testIntegrite('appliPath', $appliPath)){
					$error_message .= gettext('Veuillez renseigner un chemin valide pour l\'application') . '<br />';
				}
				$appliPrefixe 				= parent::$_request->getVar('appliPrefixe');
				if (!Application::testIntegrite('appliPrefixe', $appliPrefixe)){
					$error_message .= gettext('Veuillez renseigner un pr&eacute;fix valide pour l\'application') . '<br />';
				}
				$fuseau 					= parent::$_request->getVar('fuseau');
				if (!Application::testIntegrite('fuseau', $fuseau)){
					$error_message .= gettext('Veuillez renseigner un fuseau horaire valide pour l\'application') . '<br />';
				}
				$language 					= parent::$_request->getVar('language');
				$abbreviation_language 		= parent::$_request->getVar('abbreviation_language');
				if (!Application::testIntegrite('abbreviation_language', $abbreviation_language)){
					$error_message .= gettext('Veuillez renseigner abbr&eacute;viation valide pour la langue') . '<br />';
				}
				$devise 					= parent::$_request->getVar('devise');
				if (!Application::testIntegrite('devise', $devise)){
					$error_message .= gettext('Veuillez renseigner une devise existante') . '<br />';
				}
				$resultatParPage 			= (int) parent::$_request->getVar('resultatParPage');
				if (!Application::testIntegrite('resultatParPage', $resultatParPage)){
					$error_message .= gettext('Veuillez renseigner un nombre valide de resultat par page') . '<br />';
				}
				$emailRapport	 			= parent::$_request->getVar('emailRapport');
				if (!Application::testIntegrite('emailRapport', $emailRapport)){
					$error_message .= gettext('Veuillez renseigner au moins un mail pour recevoir les rapports de l\'application') . '<br />';
				}
				$nombreEtages 				= (int) parent::$_request->getVar('nombreEtages');
				if (!Application::testIntegrite('nombreEtages', $nombreEtages)){
					$error_message .= gettext('Veuillez renseigner un nombre valide d\'&eacute;tage au magasin') . '<br />';
				}
				$finesse 					= (int) parent::$_request->getVar('finesse');
				$largeurEtagere 			= (float) parent::$_request->getVar('largeurEtagere');
				if (!Application::testIntegrite('largeurEtagere', $largeurEtagere)){
					$error_message .= gettext('Veuillez renseigner une largeur valide d\'&eacute;tag&egrave;re') . '<br />';
				}
				$delaiAvantLivraison 		= (int) parent::$_request->getVar('delaiAvantLivraison');
				if (!Application::testIntegrite('delaiAvantLivraison', $delaiAvantLivraison)){
					$error_message .= gettext('Veuillez renseigner un d&eacute;lai de livraison valide') . '<br />';
				}
				$tempsMoyenAccesProduit 	= (int) parent::$_request->getVar('tempsMoyenAccesProduit');
				if (!Application::testIntegrite('tempsMoyenAccesProduit', $tempsMoyenAccesProduit)){
					$error_message .= gettext('Veuillez renseigner un temps moyen valide d\'acces aux produits') . '<br />';
				}
				 
				if($error_message == ''){
					
					if (Application::updateConfig(parent::$_pdo, $appliPath, $appliPrefixe, $fuseau, $language, $abbreviation_language, $devise, $resultatParPage, $emailRapport, $nombreEtages, $finesse, $largeurEtagere, $delaiAvantLivraison, $tempsMoyenAccesProduit)) {
						
						$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Modification de la configuration des param&egrave;tres application par ') . $_SESSION['user_login']."\r\n";
						$logFile = '../application/logs/'.date('m-Y').'-application.log';
						writeLog($log, $logFile);
						
						header('Location: ' . APPLICATION_PATH . 'admin/config');
					}
					
				}
				else{
					/* Assigner les différentes varibales pour le template */
					parent::$_response->addVar('form_errors'					, $error_message);
					parent::$_response->addVar('form_appliPath'					, $appliPath);
					parent::$_response->addVar('form_appliPrefixe'				, $appliPrefixe);
					parent::$_response->addVar('form_fuseau'					, $fuseau);
					parent::$_response->addVar('form_language'					, $language);
					parent::$_response->addVar('form_abbreviation_language'		, $abbreviation_language);
					parent::$_response->addVar('form_devise'					, $devise);
					parent::$_response->addVar('form_resultatParPage'			, $resultatParPage);
					parent::$_response->addVar('form_emailRapport'				, $emailRapport);
					parent::$_response->addVar('form_nombreEtages'				, $nombreEtages);
					parent::$_response->addVar('form_finesse'					, $finesse);
					parent::$_response->addVar('form_largeurEtagere'			, $largeurEtagere);
					parent::$_response->addVar('form_delaiAvantLivraison'		, $delaiAvantLivraison);
					parent::$_response->addVar('form_tempsMoyenAccesProduit'	, $tempsMoyenAccesProduit);
				}
			}
			else{
				
				parent::$_response->addVar('form_appliPath'					, APPLICATION_PATH);
				parent::$_response->addVar('form_appliPrefixe'				, APPLICATION_PREFIXE);
				parent::$_response->addVar('form_fuseau'					, APPLICATION_FUSEAU);
				parent::$_response->addVar('form_language'					, LANGUAGE);
				parent::$_response->addVar('form_abbreviation_language'		, ABBREVIATION_LANGUAGE);
				parent::$_response->addVar('form_devise'					, DEVISE);
				parent::$_response->addVar('form_resultatParPage'			, RESULTAT_PAR_PAGE);
				parent::$_response->addVar('form_emailRapport'				, EMAIL_RAPPORT);
				parent::$_response->addVar('form_nombreEtages'				, NOMBRE_ETAGES);
				parent::$_response->addVar('form_finesse'					, FINESSE_UTILISEE);
				parent::$_response->addVar('form_largeurEtagere'			, LARGEUR_ETAGERE);
				parent::$_response->addVar('form_delaiAvantLivraison'		, DELAI_AVANT_LIVRAISON);
				parent::$_response->addVar('form_tempsMoyenAccesProduit'	, TEMPS_MOYEN_ACCES_PRODUIT);

				
			}
		}
	}
}
?>