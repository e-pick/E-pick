<?php

class Temps_Preparation
{
	/// @var PDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idtemps_preparation;
		
	/// @var int id de commande
	private $commande;
	
	/// @var int id de zone
	private $zone;
	
	/// @var int 
	private $duree;

	/**
	 * Construire un(e) temps_preparation
	 * @param $pdo PDO 
	 * @param $idtemps_preparation int 
	 * @param $duree int 
	 * @param $commande int id de commande
	 * @param $zone int id de zone
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Temps_Preparation 
	 */
	protected function __construct(PDO $pdo,$idtemps_preparation,$commande=null,$zone=null,$duree,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idtemps_preparation = $idtemps_preparation;
		$this->duree = $duree;
		$this->commande = $commande;
		$this->zone = $zone;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Temps_Preparation::$easyload[$idtemps_preparation] = $this;
		}
	}
	
	/**
	 * Créer un(e) temps_preparation
	 * @param $pdo PDO 
	 * @param $duree int 
	 * @param $commande Commande 
	 * @param $zone Zone 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Temps_Preparation 
	 */
	public static function create(PDO $pdo,$commande=null,$zone=null,$duree,$easyload=true)
	{
		// Ajouter le/la temps_preparation dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO TEMPS_PREPARATION (ID_COMMANDE,ID_ZONE,TPS_DUREE) VALUES (?,?,?)');
		if (!$pdoStatement->execute(array($commande == null ? null : $commande->getIdCommande(),$zone == null ? null : $zone->getIdzone(),$duree))) {
			throw new Exception('Erreur durant l\'insertion d\'un(e) temps_preparation dans la base de données');
		}
		
		// Construire le/la temps_preparation
		return new Temps_Preparation($pdo,$pdo->lastInsertId(),$commande == null ? null : $commande->getIdCommande(),$zone == null ? null : $zone->getIdzone(),$duree,$easyload);
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
		return $pdo->prepare('SELECT t.ID_TEMPS_PREPARATION, t.ID_COMMANDE, t.ID_ZONE, t.TPS_DUREE FROM TEMPS_PREPARATION t '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un(e) temps_preparation
	 * @param $pdo PDO 
	 * @param $idtemps_preparation int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Temps_Preparation 
	 */
	public static function load(PDO $pdo,$idtemps_preparation,$easyload=true)
	{
		// Déjà chargé(e) ?
		if (isset(Temps_Preparation::$easyload[$idtemps_preparation])) {
			return Temps_Preparation::$easyload[$idtemps_preparation];
		}
		
		// Charger le/la temps_preparation
		$pdoStatement = Temps_Preparation::_select($pdo,'t.ID_TEMPS_PREPARATION = ?');
		if (!$pdoStatement->execute(array($idtemps_preparation))) {
			throw new Exception('Erreur lors du chargement d\'un(e) temps_preparation depuis la base de données');
		}
		
		// Récupérer le/la temps_preparation depuis le jeu de résultats
		return Temps_Preparation::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous/toutes les temps_preparations
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Temps_Preparation[] tableau de temps_preparations
	 */
	public static function loadAll(PDO $pdo,$easyload=false)
	{
		// Sélectionner tous/toutes les temps_preparations
		$pdoStatement = Temps_Preparation::selectAll($pdo);
		
		// Mettre chaque temps_preparation dans un tableau
		$temps_preparations = array();
		while ($temps_preparation = Temps_Preparation::fetch($pdo,$pdoStatement,$easyload)) {
			$temps_preparations[] = $temps_preparation;
		}
		
		// Retourner le tableau
		return $temps_preparations;
	}
	
	/**
	 * Sélectionner tous/toutes les temps_preparations
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Temps_Preparation::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les temps_preparations depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
	 * Récupère le/la temps_preparation suivant(e) d'un jeu de résultats
	 * @param $pdo PDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Temps_Preparation 
	 */
	public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idtemps_preparation,$commande,$zone,$duree) = $values;
		
		// Construire le/la temps_preparation
		return isset(Temps_Preparation::$easyload[$idtemps_preparation]) ? Temps_Preparation::$easyload[$idtemps_preparation] :
		       new Temps_Preparation($pdo,$idtemps_preparation,$commande,$zone,$duree,$easyload);
	}
	
	/**
	 * Supprimer le/la temps_preparation
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
		// Supprimer le/la temps_preparation
		$pdoStatement = $this->pdo->prepare('DELETE FROM TEMPS_PREPARATION WHERE ID_TEMPS_PREPARATION = ?');
		if (!$pdoStatement->execute(array($this->getIdtemps_preparation()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) temps_preparation dans la base de données');
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
		$pdoStatement = $this->pdo->prepare('UPDATE TEMPS_PREPARATION SET '.implode(', ', $updates).' WHERE ID_TEMPS_PREPARATION = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdtemps_preparation())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'un(e) temps_preparation dans la base de données');
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
		return $this->_set(array('TPS_DUREE','ID_COMMANDE','ID_ZONE'),array($this->duree,$this->commande,$this->zone));
	}
	
	/**
	 * Récupérer le/la idtemps_preparation
	 * @return int 
	 */
	public function getIdtemps_preparation()
	{
		return $this->idtemps_preparation;
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
		return $execute ? $this->_set(array('TPS_DUREE'),array($duree)) : true;
	}
	
	/**
	 * Récupérer le/la commande
	 * @return Commande 
	 */
	public function getCommande()
	{
		// Retourner null si nécéssaire
		if ($this->commande == null) { return null; }
		
		// Charger et retourner commande
		return Commande::load($this->pdo,$this->commande);
	}
	
	/**
	 * Définir le/la commande
	 * @param $commande Commande 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCommande($commande=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->commande = $commande == null ? null : $commande->getIdCommande();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_COMMANDE'),array($commande == null ? null : $commande->getIdCommande())) : true;
	}
	
	/**
	 * Sélectionner les temps_preparations par commande
	 * @param $pdo PDO 
	 * @param $commande Commande 
	 * @return PDOStatement 
	 */
	public static function selectByCommande(PDO $pdo,Commande $commande, $idEtage=0)
	{
		if($idEtage == 0){
			$pdoStatement = $pdo->prepare('SELECT t.ID_TEMPS_PREPARATION, t.ID_COMMANDE, t.ID_ZONE, t.TPS_DUREE FROM TEMPS_PREPARATION t WHERE t.ID_COMMANDE = ?');
			if (!$pdoStatement->execute(array($commande->getIdCommande()))) {
				throw new Exception('Erreur lors du chargement de tous/toutes les temps_preparations par commande depuis la base de données');
			}
		}
		else{ //by etage
			$pdoStatement = $pdo->prepare('SELECT t.ID_TEMPS_PREPARATION, t.ID_COMMANDE, t.ID_ZONE, t.TPS_DUREE FROM TEMPS_PREPARATION t, ZONE z WHERE t.ID_COMMANDE = ? AND z.ID_ZONE = t.ID_ZONE AND z.ID_ETAGE = ?');
			if (!$pdoStatement->execute(array($commande->getIdCommande(),$idEtage))) {
				throw new Exception('Erreur lors du chargement de tous/toutes les temps_preparations par commande depuis la base de données');
			}
		}
		// Mettre chaque temps_preparation dans un tableau
		$temps_preparations = array();
		while ($temps_preparation = Temps_Preparation::fetch($pdo,$pdoStatement)) {
			$temps_preparations[] = $temps_preparation;
		}
		
		// Retourner le tableau
		return $temps_preparations;
	}
	
	
	
	/**
	 * Sélectionner les temps_preparations par commande et par zone
	 * @param $pdo PDO 
	 * @param $commande Commande 
	 * @return PDOStatement 
	 */
	public static function selectByCommandeAndZone(PDO $pdo,Commande $commande, Zone $zone)
	{
		$pdoStatement = $pdo->prepare('SELECT t.ID_TEMPS_PREPARATION, t.ID_COMMANDE, t.ID_ZONE, t.TPS_DUREE FROM TEMPS_PREPARATION t WHERE t.ID_COMMANDE = ? AND t.ID_ZONE = ?');
		if (!$pdoStatement->execute(array($commande->getIdCommande(),$zone->getIdzone()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les temps_preparations par commande depuis la base de données');
		}
		
		return  Temps_Preparation::fetch($pdo,$pdoStatement);
	} 
	/**
	 * Récupérer le/la zone
	 * @return Zone 
	 */
	public function getZone()
	{
		// Retourner null si nécéssaire
		if ($this->zone == null) { return null; }
		
		// Charger et retourner zone
		return Zone::load($this->pdo,$this->zone);
	}
	
	/**
	 * Définir le/la zone
	 * @param $zone Zone 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setZone($zone=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->zone = $zone == null ? null : $zone->getIdzone();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_ZONE'),array($zone == null ? null : $zone->getIdzone())) : true;
	}
	
	/**
	 * Sélectionner les temps_preparations par zone
	 * @param $pdo PDO 
	 * @param $zone Zone 
	 * @return PDOStatement 
	 */
	public static function selectByZone(PDO $pdo,Zone $zone)
	{
		$pdoStatement = $pdo->prepare('SELECT t.ID_TEMPS_PREPARATION, t.ID_COMMANDE, t.ID_ZONE, t.TPS_DUREE FROM TEMPS_PREPARATION t WHERE t.ID_ZONE = ?');
		if (!$pdoStatement->execute(array($zone->getIdzone()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les temps_preparations par zone depuis la base de données');
		}
		return $pdoStatement;
	}
	
		public static function truncate(ownPDO $pdo){
		$pdoStatement = $pdo->prepare('TRUNCATE TABLE TEMPS_PREPARATION');
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors de la suppression des temps_preparation depuis la base de données');
		}
		return $pdoStatement;
	}
}
?>