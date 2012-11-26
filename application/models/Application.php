<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Application.php
 *
 * Gestion des paramètres d'application
 *
 */
 
 class Application
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idapplication;
	
	/**
	 * Construire un application
	 * @param $pdo ownPDO 
	 * @param $idapplication int
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Application 
	 */
	protected function __construct(ownPDO $pdo,$idapplication,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idapplication 	= $idapplication;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Application::$easyload[$idapplication] = $this;
		}
	}
	
	/*
	 * Mettre à jour la Config Application Dans la base de données
	 * @param $pdo ownPDO
	 * @param $heure_debut l'heure de debut du planning
	 * @param $heure_fin l'heure de fin du planning
	 * @param $jour_debut le jour de debut de semaine
	 * @param $jour_fin le jour de fin de semaine
	 * @param $creneau le mode de creneau par demi-heure ou heure
	 * @return true si pas d'erreur
	 */
	public static function updateConfig(ownPDO $pdo, $appliPath, $appliPrefixe, $fuseau, $language, $abbreviation_language, $devise, $resultatParPage, $emailRapport, $nombreEtages, $finesse, $largeurEtagere, $delaiAvantLivraison, $tempsMoyenAccesProduit){ 
		$pdoStatement = $pdo->prepare('UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_PATH";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_PREFIXE";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_FUSEAU_HORAIRE";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_LANGUAGE";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_ABBREVIATION_LANGUAGE";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_DEVISE";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_RESULTAT_PAR_PAGE";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_EMAIL_RAPPORT";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_NOMBRE_ETAGES";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_FINESSE_UTILISEE";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_LARGEUR_ETAGERE";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_DELAI_AVANT_LIVRAISON";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="APPLICATION_TEMPS_MOYEN_ACCES_PRODUIT";');
					
		if (!$pdoStatement->execute(array($appliPath, $appliPrefixe, $fuseau, $language, $abbreviation_language, $devise, $resultatParPage, $emailRapport, $nombreEtages, $finesse, $largeurEtagere, $delaiAvantLivraison, $tempsMoyenAccesProduit))) {
			throw new Exception('Erreur lors de la mise &agrave; jour des confs planning dans la base de données');
		}			
		
		return true;
	}
	
	/**
	 *
	 * Teste l'intégrité des attributs de l'objet Application
	 * @param $attribute le nom de l'attribut
	 * @param $value la valeur à tester
	 * @return boolean => true si ok, false sinon
	 *
	 */
	public static function testIntegrite($attribute, $value){
		switch ($attribute) {
			case 'appliPath' :
			case 'appliPrefixe' :
			case 'fuseau' :
			case 'abbreviation_language' :
			case 'devise' :
			case 'emailRapport' :
				return (is_string($value) && $value != '' && !empty($value));
				break;
				
			case 'resultatParPage' :
			case 'nombreEtages' :
			case 'delaiAvantLivraison' :
			case 'tempsMoyenAccesProduit' :
				return (!is_string($value) && is_int($value) && $value > 0);
				break;
				
			case 'largeurEtagere' :
				return (!is_string($value) && is_numeric($value) && $value > 0);
				break;
				
			default:
				throw new Exception(gettext('L\'attribut ') . $attribut . gettext(' ne fait pas partie de l\'objet ') . gettext('Application'),3);
		}
	}
	
	/**
	 * Requête de séléction
	 * @param $pdo PDO 
	 * @param $where string 
	 * @param $orderby string 
	 * @param $limit string 
	 * @return PDOStatement 
	 */
	private static function _select(PDO $pdo,$where=null,$orderby=null,$limit=null)
	{
		return $pdo->prepare('SELECT * FROM CONFIG c '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Loader les emails rapport
	 * @param $pdo PDO 
	 * @param $idetage int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etage 
	 */
	public static function getEmails(PDO $pdo)
	{
		// Charger les emails
		$pdoStatement = self::_select($pdo,'c.NAME_CONFIG = "APPLICATION_EMAIL_RAPPORT"');
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement d\'un(e) etage depuis la base de données');
		}
		
		return $pdoStatement->fetchAll();
	}
}
?>