<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Preparation.php
 *
 */

class Preparation
{
	/// @var PDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idPreparation;
	
	/// @var int id de utilisateur
	private $utilisateur;
	
	/// @var int 
	private $duree;
	
	/// @var int 
	private $date_preparation;
	
	/// @var int 
	private $etat;
	
	/// @var int 
	private $prioritaire;

	
	private $modePreparation;
	
	private $typePreparation;
	
	/**
	 * Construire un(e) preparation
	 * @param $pdo PDO 
	 * @param $idPreparation int 
	 * @param $duree int 
	 * @param $date_preparation int 
	 * @param $etat int 
	 * @param $prioritaire int 
	 * @param $utilisateur int id de utilisateur
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Preparation 
	 */
	protected function __construct(PDO $pdo,$idPreparation,$utilisateur,$duree,$date_preparation,$etat,$prioritaire,$modePreparation,$typePreparation,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idPreparation = $idPreparation;
		$this->duree = $duree;
		$this->date_preparation = $date_preparation;
		$this->etat = $etat;
		$this->prioritaire = $prioritaire;
		$this->utilisateur = $utilisateur;
		$this->modePreparation = $modePreparation;
		$this->typePreparation = $typePreparation;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Preparation::$easyload[$idPreparation] = $this;
		}
	}
	
	/**
	 * Créer un(e) preparation
	 * @param $pdo PDO 
	 * @param $duree int 
	 * @param $date_preparation int 
	 * @param $etat int 
	 * @param $prioritaire int 
	 * @param $utilisateur Utilisateur 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Preparation 
	 */
	public static function create(PDO $pdo,$utilisateur=null,$duree,$date_preparation,$etat=0,$prioritaire=0,$modePreparation,$typePreparation,$easyload=true)
	{
		// Ajouter le/la preparation dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO PREPARATION (ID_UTILISATEUR,PREPA_DUREE,PREPA_DATE_PREPARATION,PREPA_ETAT,PREPA_PRIORITAIRE,PREPA_MODE_PREPARATION,PREPA_TYPE_PREPARATION) VALUES (?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($utilisateur == null ? null : $utilisateur->getIdUtilisateur(),$duree,$date_preparation,$etat,$prioritaire,$modePreparation,$typePreparation))) {
			throw new Exception('Erreur durant l\'insertion d\'un(e) preparation dans la base de données');
		}
		
		// Construire le/la preparation
		return new Preparation($pdo,$pdo->lastInsertId(),$utilisateur == null ? null : $utilisateur->getIdUtilisateur(),$duree,$date_preparation,$etat,$prioritaire,$modePreparation,$typePreparation,$easyload);
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
		return $pdo->prepare('SELECT p.ID_PREPARATION, p.ID_UTILISATEUR, p.PREPA_DUREE, p.PREPA_DATE_PREPARATION, p.PREPA_ETAT, p.PREPA_PRIORITAIRE,p.PREPA_MODE_PREPARATION,p.PREPA_TYPE_PREPARATION FROM PREPARATION p '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un(e) preparation
	 * @param $pdo PDO 
	 * @param $idPreparation int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Preparation 
	 */
	public static function load(PDO $pdo,$idPreparation,$easyload=true)
	{
		// Déjà chargé(e) ?
		if (isset(Preparation::$easyload[$idPreparation])) {
			return Preparation::$easyload[$idPreparation];
		}
		
		// Charger le/la preparation
		$pdoStatement = Preparation::_select($pdo,'p.ID_PREPARATION = ?');
		if (!$pdoStatement->execute(array($idPreparation))) {
			throw new Exception('Erreur lors du chargement d\'un(e) preparation depuis la base de données');
		}
		
		// Récupérer le/la preparation depuis le jeu de résultats
		return Preparation::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous/toutes les preparations
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Preparation[] tableau de preparations
	 */
	public static function loadAll(PDO $pdo,$first=null,$numcoFilter=null,$etatcoFilter=null,$preparateurFilter=null,$datedebcoFilter=null,$datefincoFilter=null,$easyload=false)
	{
		$select		= 'SELECT p.* FROM ';
		$tables 	= array();
		$where 		= '';
		$limit		= '';
		$array_attr = array();
	
		if($numcoFilter != null){
			if (!in_array('PREPARATION p', $tables))
				$tables[] = 'PREPARATION p';
				
			$where 		   .= 'p.ID_PREPARATION LIKE ? AND ';
			$array_attr[]	= '%' . $numcoFilter . '%'; 
		} 
		
		if($etatcoFilter != null){
			if(!in_array('PREPARATION p', $tables))
				$tables[] = 'PREPARATION p';
				
			$where 		   .= 'p.PREPA_ETAT = ? AND ';
			$array_attr[]	= $etatcoFilter; 
		}
	
		if($preparateurFilter != null){
			if (!in_array('PREPARATION p', $tables))
				$tables[] = 'PREPARATION p';
				
			$where 		   .= 'p.ID_UTILISATEUR = ? AND ';
			$array_attr[]	= $preparateurFilter ; 
		} 
		
		
		if($datedebcoFilter != null){
			if (!in_array('PREPARATION p', $tables))
				$tables[] = 'PREPARATION p';
			$where 			.= 'PREPA_DATE_PREPARATION >= ? AND ';
			$array_attr[]	 =  $datedebcoFilter ;
		}
		if($datefincoFilter != null){
			if (!in_array('PREPARATION p', $tables))
				$tables[] = 'PREPARATION p';
			$where 			.= 'PREPA_DATE_PREPARATION <= ? AND ';
			$array_attr[]	 =  ($datefincoFilter + 3600*24);
		}
	
		// Sélectionner tous/toutes les preparations
		if($first != null){
			$limit =  $first.','.RESULTAT_PAR_PAGE;
		}
		
			
		if($where != ''){
			$where .= ' 1=1 ';
			
			if($limit != '')
				$pdoStatement = $pdo->prepare($select . implode(', ', $tables) . ' WHERE ' . $where . ' ORDER BY p.PREPA_ETAT LIMIT ' . $limit);
			else
				$pdoStatement = $pdo->prepare($select . implode(', ', $tables) . ' WHERE ' . $where .' ORDER BY p.PREPA_ETAT');
			if (!$pdoStatement->execute($array_attr))
				throw new Exception('Erreur lors du chargement de tous les préparations depuis la base de données');
		}
		else{
			if($limit != '')
				$pdoStatement = Preparation::_select($pdo,null,' p.PREPA_ETAT',$limit);
			else
				$pdoStatement = Preparation::_select($pdo,null,'p.PREPA_ETAT');
			if (!$pdoStatement->execute())
				throw new Exception('Erreur lors du chargement de tous les préparations depuis la base de données');	
		}
		
		// Mettre chaque preparation dans un tableau
		$preparations = array();
		while ($preparation = Preparation::fetch($pdo,$pdoStatement,$easyload)) {
			$preparations[] = $preparation;
		}
		
		// Retourner le tableau
		return $preparations;
	}
	
	/**
	 * Sélectionner tous/toutes les preparations
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Preparation::_select($pdo,null,' PREPA_DATE_PREPARATION, PREPA_ETAT');
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les preparations depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
	 * Récupère le/la preparation suivant(e) d'un jeu de résultats
	 * @param $pdo PDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Preparation 
	 */
	public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idPreparation,$utilisateur,$duree,$date_preparation,$etat,$prioritaire,$modePreparation,$typePreparation) = $values;
		
		// Construire le/la preparation
		return isset(Preparation::$easyload[$idPreparation]) ? Preparation::$easyload[$idPreparation] :
		       new Preparation($pdo,$idPreparation,$utilisateur,$duree,$date_preparation,$etat,$prioritaire,$modePreparation,$typePreparation,$easyload);
	}
	
	/**
	 * Supprimer le/la preparation
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
		// Supprimer les ligne_commandes associé(e)s
		$lignes = $this->selectLigne_commandes();
		foreach($lignes as $ligne){
			if ($ligne->setPreparation(null)){
				$commande = Commande::load($this->pdo, $ligne->getCommande()->getIdCommande());
				$commande->setEtatCommande(0);
			}	
			else{ 
				return false; 
			}
		}
		
		// Supprimer la preparation
		$pdoStatement = $this->pdo->prepare('DELETE FROM PREPARATION WHERE ID_PREPARATION = ?');
		if (!$pdoStatement->execute(array($this->getIdPreparation()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) preparation dans la base de données');
		}
		
		// Opération réussie ?
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * Mettre à jour un champ dans la base de données
	 * @param $fields array 
	 * @param $values array 
	 * @return bool opération réussie ?
	 */
	private function _set($fields,$values)
	{
		// Préparer la mise à jour
		$updates = array();
		foreach ($fields as $field) {
			$updates[] = $field.' = ?';
		}
		
		// Mettre à jour le champ
		$pdoStatement = $this->pdo->prepare('UPDATE PREPARATION SET '.implode(', ', $updates).' WHERE ID_PREPARATION = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdPreparation())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'un(e) preparation dans la base de données');
		}
		
		// Opération réussie ?
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * Mettre à jour tous les champs dans la base de données
	 * @return bool opération réussie ?
	 */
	public function update()
	{
		return $this->_set(array('PREPA_DUREE','PREPA_DATE_PREPARATION','PREPA_ETAT','PREPA_PRIORITAIRE','ID_UTILISATEUR','PREPA_MODE_PREPARATION','PREPA_TYPE_PREPARATION'),array($this->duree,$this->date_preparation,$this->etat,$this->prioritaire,$this->utilisateur,$this->modePreparation,$this->typePreparation));
	}
	
	/**
	 * Récupérer le/la idPreparation
	 * @return int 
	 */
	public function getIdPreparation()
	{
		return $this->idPreparation;
	}
	
	/**
	 * Sélectionner les ligne_commandes
	 * @return liste de ligne de commande
	 */
	public function selectLigne_commandes()
	{
		return Ligne_Commande::selectByPreparation($this->pdo,$this);
	}
	
	/**
	 * Récupérer le/la duree
	 * @return int 
	 */
	public function getDuree()
	{
		return $this->duree;
	}
	
	/**
	 * Définir le/la duree
	 * @param $duree int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setDuree($duree,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->duree = $duree;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('PREPA_DUREE'),array($duree)) : true;
	}
	
	/**
	 * Récupérer le/la date_preparation
	 * @return int 
	 */
	public function getDate_preparation()
	{
		return $this->date_preparation;
	}
	
	/**
	 * Définir le/la date_preparation
	 * @param $date_preparation int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setDate_preparation($date_preparation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->date_preparation = $date_preparation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('PREPA_DATE_PREPARATION'),array($date_preparation)) : true;
	}
	
	/**
	 * Récupérer le/la etat
	 * @return int 
	 */
	public function getEtat()
	{
		return $this->etat;
	}
	
	/**
	 * Définir le/la etat
	 * @param $etat int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setEtat($etat,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->etat = $etat;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('PREPA_ETAT'),array($etat)) : true;
	}
	
	/**
	 * Récupérer le/la prioritaire
	 * @return int 
	 */
	public function getPrioritaire()
	{
		return $this->prioritaire;
	}
	
	/**
	 * Définir le/la prioritaire
	 * @param $prioritaire int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setPrioritaire($prioritaire,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->prioritaire = $prioritaire;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('PREPA_PRIORITAIRE'),array($prioritaire)) : true;
	}
	/**
	 * Récupérer le/la prioritaire
	 * @return int 
	 */
	public function getModePreparation()
	{
		return $this->modePreparation;
	}
	
	/**
	 * Définir le/la prioritaire
	 * @param $prioritaire int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setModePreparation($modePreparation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->modePreparation = $modePreparation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('PREPA_MODE_PREPARATION'),array($modePreparation)) : true;
	}
	/**
	 * Récupérer le/la prioritaire
	 * @return int 
	 */
	public function getTypePreparation()
	{
		return $this->typePreparation;
	}
	
	/**
	 * Définir le/la prioritaire
	 * @param $prioritaire int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setTypePreparation($typePreparation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->typePreparation = $typePreparation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('PREPA_TYPE_PREPARATION'),array($typePreparation)) : true;
	}
	
	/**
	 * Récupérer le/la utilisateur
	 * @return Utilisateur 
	 */
	public function getUtilisateur()
	{
		// Retourner null si nécéssaire
		if ($this->utilisateur == null) { return null; }
		
		// Charger et retourner utilisateur
		return Utilisateur::load($this->pdo,$this->utilisateur,false);
	}
	
	/**
	 * Définir le/la utilisateur
	 * @param $utilisateur Utilisateur 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setUtilisateur($utilisateur=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->utilisateur = $utilisateur == null ? null : $utilisateur->getIdUtilisateur();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_UTILISATEUR'),array($utilisateur == null ? null : $utilisateur->getIdUtilisateur())) : true;
	}
	
	/**
	 * Sélectionner les preparations par utilisateur
	 * @param $pdo PDO 
	 * @param $utilisateur Utilisateur 
	 * @return PDOStatement 
	 */
	public static function selectByUtilisateur(PDO $pdo,Utilisateur $utilisateur)
	{
		$pdoStatement = $pdo->prepare('SELECT p.ID_PREPARATION, p.ID_UTILISATEUR, p.PREPA_DUREE, p.PREPA_DATE_PREPARATION, p.PREPA_ETAT, p.PREPA_PRIORITAIRE, p.PREPA_MODE_PREPARATION, p.PREPA_TYPE_PREPARATION FROM PREPARATION p WHERE p.ID_UTILISATEUR = ? ORDER BY PREPA_DATE_PREPARATION, p.PREPA_ETAT');
		if (!$pdoStatement->execute(array($utilisateur->getIdUtilisateur()))) {
			throw new Exception('Erreur lors du chargement de toutes les preparations par utilisateur depuis la base de données');
		}
		
		// Mettre chaque preparation dans un tableau
		$preparations = array();
		while ($preparation = Preparation::fetch($pdo,$pdoStatement)) {
			$preparations[] = $preparation;
		}
		
		// Retourner le tableau
		return $preparations;
		// return $pdoStatement;
	}
	/*
	 * Retourne les idCommande qui ont au moins une ligne de commande dans la préparation
	 */
	public function getCommandes(){
		$pdoStatement = $this->pdo->prepare('	SELECT c.* 
												FROM COMMANDE c, LIGNE_COMMANDE lc, PREPARATION p 
												WHERE p.ID_PREPARATION = ?
												AND p.ID_PREPARATION = lc.ID_PREPARATION
												AND lc.ID_COMMANDE = c.ID_COMMANDE
												GROUP BY c.ID_COMMANDE');
										
		if (!$pdoStatement->execute(array($this->getIdPreparation()))) {
			throw new Exception('Erreur lors du chargement de toutes les preparations par utilisateur depuis la base de données');
		}
		
		// Mettre chaque commande dans un tableau
		$commandes = array(); 
		while ($commande = Commande::fetch($this->pdo,$pdoStatement)) { 
			$commandes[] = $commande;
		}
		 
		// Retourner le tableau
		return $commandes;
	}
	
	/*
	 * Mettre à jour la Config Affectation Dans la base de données
	 * @param $pdo ownPDO
	 * @param $nbCommandesMax Nombre max des commandes à regrouper
	 * @param $tempsMaxPrepa Temps max de préparation (Minute) 
	 * @param $nbRefsMax Nombre max des références à préparer
	 * @param $nbArticlesMax Nombre max des articles à préparer
	 * @param $poidsMax Poids total max des articles à préparer (Kg) 
	 * @return true si pas d'erreur
	 */
	public static function updateConfig(ownPDO $pdo, $nbCommandesMax, $tempsMaxPrepa, $nbRefsMax, $nbArticlesMax, $poidsMax){ 
		$pdoStatement = $pdo->prepare('UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="AFFECTATION_NB_COMMANDES_MAX";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="AFFECTATION_TEMPS_PREPA_MAX";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="AFFECTATION_NB_REFERENCES_MAX";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="AFFECTATION_NB_ARTICLES_MAX";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="AFFECTATION_POIDS_MAX";');
					
		if (!$pdoStatement->execute(array($nbCommandesMax, ($tempsMaxPrepa * 60), $nbRefsMax, $nbArticlesMax, $poidsMax))) {
			throw new Exception('Erreur lors de la mise &agrave; jour des confs affectation dans la base de données');
		}			
		
		return true;
	}

}

?>