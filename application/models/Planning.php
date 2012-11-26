<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Planning.php
 *
 * Gestion du planning des utilisateurs
 *
 */

class Planning
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idplanning;
		
	/// @var int id de utilisateur
	private $utilisateur;

	/// @var int 
	private $timestamp;
	
	/// @var int 
	private $duree;

	
	/**
	 * Construire un(e) planning
	 * @param $pdo ownPDO 
	 * @param $idplanning int 
	 * @param $timestamp int 
	 * @param $utilisateur int id de utilisateur
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Planning 
	 */
	protected function __construct(ownPDO $pdo,$idplanning,$utilisateur=null,$timestamp,$duree,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idplanning 	= $idplanning;
		$this->timestamp 	= $timestamp;
		$this->utilisateur 	= $utilisateur;
		$this->duree	 	= $duree;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Planning::$easyload[$idplanning] = $this;
		}
	}
	
	/**
	 * Cr�er un(e) planning
	 * @param $pdo ownPDO 
	 * @param $timestamp int 
	 * @param $utilisateur Utilisateur 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Planning 
	 */
	public static function create(ownPDO $pdo,$utilisateur=null,$timestamp,$duree,$easyload=true)
	{
		// Ajouter le/la planning dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO PLANNING (ID_UTILISATEUR,PLA_TIMESTAMP,PLA_DUREE) VALUES (?,?,?)');
		if (!$pdoStatement->execute(array($utilisateur == null ? null : $utilisateur->getIdutilisateur(),$timestamp,$duree))) {
			throw new Exception('Erreur durant l\'insertion d\'un(e) planning dans la base de donn�es');
		}
		
		// Construire le/la planning
		return new Planning($pdo,$pdo->lastInsertId(),$utilisateur == null ? null : $utilisateur->getIdutilisateur(),$timestamp,$duree,$easyload);
	}
	
	/**
	 *
	 * Teste l'int�grit� des attributs de l'objet Planning
	 * @param $attribute le nom de l'attribut
	 * @param $value la valeur � tester
	 * @return boolean => true si ok, false sinon
	 *
	 */
	public static function testIntegrite($attribute, $value){
		switch ($attribute) {
			case 'heure_debut' :
			case 'heure_fin' :
				return (!is_string($value) && $value >= 0 && $value <= 24);
				break;
				
			case 'jour_debut' :
			case 'jour_fin' :
				return (!is_string($value) && $value >= 1 && $value <= 7);
				break;
			
			case 'creneau' :
				return ($value == '0' || $value == '1');
				break;
				
			default:
				throw new Exception(gettext('L\'attribut ') . $attribut . gettext(' ne fait pas partie de l\'objet ') . gettext('Planning'),3);
		}
	}
	
	/**
	 * Requ�te de s�l�ction
	 * @param $pdo ownPDO 
	 * @param $where string 
	 * @param $orderby string 
	 * @param $limit string 
	 * @return ownPDOStatement 
	 */
	private static function _select(ownPDO $pdo,$where=null,$orderby=null,$limit=null)
	{
		return $pdo->prepare('SELECT p.ID_PLANNING, p.ID_UTILISATEUR, p.PLA_TIMESTAMP, p.PLA_DUREE FROM PLANNING p '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDERBY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	
	
	public static function truncate(ownPDO $pdo){
		$pdoStatement = $pdo->prepare('TRUNCATE TABLE PLANNING');
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors de la suppression des plannings depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * Charger un(e) planning
	 * @param $pdo ownPDO 
	 * @param $idplanning int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Planning 
	 */
	public static function load(ownPDO $pdo,$idplanning,$easyload=true)
	{
		// D�j� charg�(e) ?
		if (isset(Planning::$easyload[$idplanning])) {
			return Planning::$easyload[$idplanning];
		}
		
		// Charger le/la planning
		$pdoStatement = Planning::_select($pdo,'p.ID_PLANNING = ?');
		if (!$pdoStatement->execute(array($idplanning))) {
			throw new Exception('Erreur lors du chargement d\'un(e) planning depuis la base de donn�es');
		}
		
		// R�cup�rer le/la planning depuis le jeu de r�sultats
		return Planning::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous/toutes les plannings
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Planning[] tableau de plannings
	 */
	public static function loadAll(ownPDO $pdo,$easyload=false)
	{
		// S�lectionner tous/toutes les plannings
		$pdoStatement = Planning::selectAll($pdo);
		
		// Mettre chaque planning dans un tableau
		$plannings = array();
		while ($planning = Planning::fetch($pdo,$pdoStatement,$easyload)) {
			$plannings[] = $planning;
		}
		
		// Retourner le tableau
		return $plannings;
	}
	
	/**
	 * S�lectionner tous/toutes les plannings
	 * @param $pdo ownPDO 
	 * @return ownPDOStatement 
	 */
	public static function selectAll(ownPDO $pdo)
	{
		$pdoStatement = Planning::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les plannings depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * R�cup�re le/la planning suivant(e) d'un jeu de r�sultats
	 * @param $pdo ownPDO 
	 * @param $pdoStatement ownPDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Planning 
	 */
	public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idplanning,$utilisateur,$timestamp,$duree) = $values;
		
		// Construire le/la planning
		return isset(Planning::$easyload[$idplanning]) ? Planning::$easyload[$idplanning] :
		       new Planning($pdo,$idplanning,$utilisateur,$timestamp,$duree,$easyload);
	}
	
	/**
	 * Supprimer le/la planning
	 * @return bool op�ration r�ussie ?
	 */
	public function delete()
	{
		// Supprimer le/la planning
		$pdoStatement = $this->pdo->prepare('DELETE FROM PLANNING WHERE ID_PLANNING = ?');
		if (!$pdoStatement->execute(array($this->getIdplanning()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) planning dans la base de donn�es');
		}
		
		// Op�ration r�ussie ?
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * Mettre � jour un champ dans la base de donn�es
	 * @param $fields array 
	 * @param $values array 
	 * @return bool op�ration r�ussie ?
	 */
	private function _set($fields,$values)
	{
		// Pr�parer la mise � jour
		$updates = array();
		foreach ($fields as $field) {
			$updates[] = $field.' = ?';
		}
		
		// Mettre � jour le champ
		$pdoStatement = $this->pdo->prepare('UPDATE PLANNING SET '.implode(', ', $updates).' WHERE ID_PLANNING = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdplanning())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'un(e) planning dans la base de donn�es');
		}
		
		// Op�ration r�ussie ?
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * Mettre � jour tous les champs dans la base de donn�es
	 * @return bool op�ration r�ussie ?
	 */
	public function update()
	{
		return $this->_set(array('PLA_TIMESTAMP','PLA_DUREE','ID_UTILISATEUR'),array($this->timestamp,$this->duree,$this->utilisateur));
	}
	
	/**
	 * R�cup�rer le/la idplanning
	 * @return int 
	 */
	public function getIdplanning()
	{
		return $this->idplanning;
	}
	
	/**
	 * R�cup�rer le/la timestamp
	 * @return int 
	 */
	public function getTimestamp()
	{
		return $this->timestamp;
	}
	
	/**
	 * D�finir le/la duree
	 * @param $duree int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setDuree($duree,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->duree = $duree;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PLA_DUREE'),array($duree)) : true;
	}
	/**
	 * R�cup�rer le/la duree
	 * @return int 
	 */
	public function getDuree()
	{
		return $this->duree;
	}
	
	/**
	 * D�finir le/la timestamp
	 * @param $timestamp int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setTimestamp($timestamp,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->timestamp = $timestamp;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PLA_TIMESTAMP'),array($timestamp)) : true;
	}
	
	/**
	 * R�cup�rer le/la utilisateur
	 * @return Utilisateur 
	 */
	public function getUtilisateur()
	{
		// Retourner null si n�c�ssaire
		if ($this->utilisateur == null) { return null; }
		
		// Charger et retourner utilisateur
		return Utilisateur::load($this->pdo,$this->utilisateur);
	}
	
	/**
	 * D�finir le/la utilisateur
	 * @param $utilisateur Utilisateur 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setUtilisateur($utilisateur=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->utilisateur = $utilisateur == null ? null : $utilisateur->getIdutilisateur();
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_UTILISATEUR'),array($utilisateur == null ? null : $utilisateur->getIdutilisateur())) : true;
	}
	
	/**
	 * S�lectionner les plannings par utilisateur
	 * @param $pdo ownPDO 
	 * @param $utilisateur Utilisateur 
	 * @return tableau de planning
	 */
	public static function selectByUtilisateur(ownPDO $pdo,Utilisateur $utilisateur)
	{
		$pdoStatement = $pdo->prepare('SELECT p.ID_PLANNING, p.ID_UTILISATEUR, p.PLA_TIMESTAMP, p.PLA_DUREE FROM PLANNING p WHERE p.ID_UTILISATEUR = ?');
		if (!$pdoStatement->execute(array($utilisateur->getIdutilisateur()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les plannings par utilisateur depuis la base de donn�es');
		}
		
		$plannings= array();
		while ($planning = Planning::fetch($pdo,$pdoStatement)) {
			$plannings[] = $planning; 
		}
		
		// Retourner le tableau		
		return $plannings;
	}
	
	/**
	 * S�lectionner les utilisateurs qui travaillent dans le cr�neau horaire
	 * @param $pdo ownPDO 
	 * @param $timestamp_inf
	 * @param $timestamp_sup
	 * @return tableau d'utilisateur
	 */
	public static function selectByTimestamp(ownPDO $pdo, $timestamp_inf, $timestamp_sup){
	 
		$pdoStatement = $pdo->prepare('SELECT  p.ID_PLANNING, p.ID_UTILISATEUR, p.PLA_TIMESTAMP, p.PLA_DUREE  FROM PLANNING p WHERE  (p.PLA_TIMESTAMP <= ? AND p.PLA_TIMESTAMP + p.PLA_DUREE > ?) OR ((p.PLA_TIMESTAMP > ? AND p.PLA_TIMESTAMP < ?) OR (p.PLA_TIMESTAMP + p.PLA_DUREE > ? AND p.PLA_TIMESTAMP + p.PLA_DUREE < ? ))');
			if (!$pdoStatement->execute(array($timestamp_inf,$timestamp_inf,$timestamp_inf,$timestamp_sup,$timestamp_inf,$timestamp_sup))) {
			throw new Exception('Erreur lors du chargement de tous les utilsateurs pour ce cr�neau depuis la base de donn�es');
		}			
		 
		$utilisateurs= array();
		while ($planning = Planning::fetch($pdo,$pdoStatement)) {
			$utilisateurs[] = $planning->getUtilisateur(); 
		}
		
		// Retourner le tableau		
		return $utilisateurs;
	
	}
	/**
	 * S�lectionner les objets planning dans le cr�neau horaire
	 * @param $pdo ownPDO 
	 * @param $timestamp_inf
	 * @param $timestamp_sup
	 * @return tableau de planning
	 */
	public static function loadByTimestamp(ownPDO $pdo, $timestamp_inf, $timestamp_sup, $user = null){
	
		if($user == null){
			$pdoStatement = $pdo->prepare('SELECT  p.ID_PLANNING, p.ID_UTILISATEUR, p.PLA_TIMESTAMP, p.PLA_DUREE FROM PLANNING p WHERE  (p.PLA_TIMESTAMP <= ? AND p.PLA_TIMESTAMP + p.PLA_DUREE > ?) OR ((p.PLA_TIMESTAMP > ? AND p.PLA_TIMESTAMP < ?) OR (p.PLA_TIMESTAMP + p.PLA_DUREE > ? AND p.PLA_TIMESTAMP + p.PLA_DUREE < ? ))');
			if (!$pdoStatement->execute(array($timestamp_inf,$timestamp_inf,$timestamp_inf,$timestamp_sup,$timestamp_inf,$timestamp_sup))) {
				throw new Exception('Erreur lors du chargement de tous les utilsateurs pour ce cr�neau depuis la base de donn�es');
			}			
		}
		else {
			$pdoStatement = $pdo->prepare('SELECT  p.ID_PLANNING, p.ID_UTILISATEUR, p.PLA_TIMESTAMP, p.PLA_DUREE FROM PLANNING p WHERE   ((p.PLA_TIMESTAMP <= ? AND p.PLA_TIMESTAMP + p.PLA_DUREE > ?) OR ((p.PLA_TIMESTAMP > ? AND p.PLA_TIMESTAMP < ?) OR (p.PLA_TIMESTAMP + p.PLA_DUREE > ? AND p.PLA_TIMESTAMP + p.PLA_DUREE < ? ))) AND p.ID_UTILISATEUR = ? ORDER BY p.PLA_TIMESTAMP');
			if (!$pdoStatement->execute(array($timestamp_inf,$timestamp_inf,$timestamp_inf,$timestamp_sup,$timestamp_inf,$timestamp_sup,$user->getIdutilisateur()))) {
				throw new Exception('Erreur lors du chargement de tous les utilsateurs pour ce cr�neau depuis la base de donn�es');
			}
		}
		$plannings= array();
		while ($planning = Planning::fetch($pdo,$pdoStatement)) {
			$plannings[] = $planning; 
		}
		
		// Retourner le tableau		
		return $plannings;
	
	}
	/*
	 * S�lectionner l'objet planning correspondant � un utilisateur dans un timestamp donn�
	 * @param $pdo ownPDO
	 * @param $timestamp_inf le d�but du cr�nau
	 * @param $timestamp_sup la fin du cr�nau
	 * @param $iduser identifiant de l'utilisateur
	 * @return planning
	 */
	public static function selectPlanningByUserAndTimestamp(ownPDO $pdo, $timestamp_inf, $timestamp_sup, $iduser){ 
		$pdoStatement = $pdo->prepare('SELECT  p.ID_PLANNING, p.ID_UTILISATEUR, p.PLA_TIMESTAMP, p.PLA_DUREE FROM PLANNING p WHERE ((p.PLA_TIMESTAMP <= ? AND p.PLA_TIMESTAMP + p.PLA_DUREE > ?) OR ((p.PLA_TIMESTAMP > ? AND p.PLA_TIMESTAMP < ?) OR (p.PLA_TIMESTAMP + p.PLA_DUREE > ? AND p.PLA_TIMESTAMP + p.PLA_DUREE < ? ))) AND p.ID_UTILISATEUR = ? ORDER BY p.PLA_TIMESTAMP');
		if (!$pdoStatement->execute(array($timestamp_inf,$timestamp_inf,$timestamp_inf,$timestamp_sup,$timestamp_inf,$timestamp_sup, $iduser))) {
			throw new Exception('Erreur lors du chargement de tous les utilsateurs pour ce cr�neau depuis la base de donn�es');
		}			
		
		return Planning::fetch($pdo,$pdoStatement);
	}
	
	/*
	 * Mettre � jour la Config Planning Dans la base de donn�es
	 * @param $pdo ownPDO
	 * @param $heure_debut l'heure de debut du planning
	 * @param $heure_fin l'heure de fin du planning
	 * @param $jour_debut le jour de debut de semaine
	 * @param $jour_fin le jour de fin de semaine
	 * @param $creneau le mode de creneau par demi-heure ou heure
	 * @return true si pas d'erreur
	 */
	public static function updateConfig(ownPDO $pdo, $heure_debut, $heure_fin, $jour_debut, $jour_fin, $creneau){ 
		$pdoStatement = $pdo->prepare('UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="PLANNING_HEURE_DEBUT";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="PLANNING_HEURE_FIN";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="PLANNING_JOURNEE_DEBUT";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="PLANNING_JOURNEE_FIN";
					UPDATE CONFIG SET VALUE_CONFIG= ? WHERE NAME_CONFIG="PLANNING_MODE_CRENEAU";');
					
		if (!$pdoStatement->execute(array($heure_debut, $heure_fin, $jour_debut, $jour_fin, $creneau))) {
			throw new Exception('Erreur lors de la mise &agrave; jour des confs planning dans la base de donn�es');
		}			
		
		return true;
	}
	
	public function __toString(){
		return $this->getIdplanning();
	}
}
?>