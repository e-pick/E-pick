<?php

/**
 * @class Etagere
 * @date 01/03/2011 (dd/mm/yyyy)
 * @generator WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
class Etagere
{
	/// @var PDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idetagere;

	/// @var int id de segment
	private $segment;
	
	/// @var int
	private $priorite;
	
	/**
	 * Construire une etagere
	 * @param $pdo PDO 
	 * @param $idetagere int 
	 * @param $libelle string 
	 * @param $segment int id de segment
	 * @param $priorite int
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etagere 
	 */
	protected function __construct(PDO $pdo,$idetagere,$segment=null,$priorite=null,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idetagere 	= $idetagere; 
		$this->segment 		= $segment;
		$this->priorite		= $priorite;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Etagere::$easyload[$idetagere] = $this;
		}
	}
	
	/**
	 * Créer une etagere
	 * @param $pdo PDO  
	 * @param $segment Segment 
	 * @param $priorite int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etagere 
	 */
	public static function create(PDO $pdo ,$segment,$priorite=null, $easyload=true)
	{
		// Ajouter l'etagere dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO ETAGERE (ID_SEGMENT,ETAGR_PRIORITE) VALUES (?,?)');
		if (!$pdoStatement->execute(array($segment->getIdsegment(),$priorite))) {
			throw new Exception('Erreur durant l\'insertion d\'une etagere dans la base de données');
		}
		
		// Construire l'etagere
		return new Etagere($pdo,$pdo->lastInsertId(),$segment->getIdsegment(),$priorite,$easyload);
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
		return $pdo->prepare('SELECT * FROM ETAGERE e '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger une etagere
	 * @param $pdo PDO 
	 * @param $idetagere int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etagere 
	 */
	public static function load(PDO $pdo,$idetagere,$easyload=true)
	{
		// Déjà chargée ?
		if (isset(Etagere::$easyload[$idetagere])) {
			return Etagere::$easyload[$idetagere];
		}
		
		// Charger l'etagere
		$pdoStatement = Etagere::_select($pdo,'e.ID_ETAGERE = ?');
		if (!$pdoStatement->execute(array($idetagere))) {
			throw new Exception('Erreur lors du chargement d\'un(e) etagere depuis la base de données');
		}
		
		// Récupérer l'etagere depuis le jeu de résultats
		return Etagere::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger toutes les etageres
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etagere[] tableau de etageres
	 */
	public static function loadAll(PDO $pdo,$easyload=false)
	{
		// Sélectionner tous/toutes les etageres
		$pdoStatement = Etagere::selectAll($pdo);
		
		// Mettre chaque etagere dans un tableau
		$etageres = array();
		while ($etagere = Etagere::fetch($pdo,$pdoStatement,$easyload)) {
			$etageres[] = $etagere;
		}
		
		// Retourner le tableau
		return $etageres;
	}
	
	/**
	 * Charger toutes les étagères d'un segment
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @param $idSegment int 
	 * @return Etagere[] tableau des étagères d'un segment
	 */
	public static function loadAllSegment(PDO $pdo,$idSegment,$easyload=false)
	{
		// Sélectionner tous les segments du rayon
		$pdoStatement = Etagere::_select($pdo, 'e.ID_SEGMENT = ?');
		if (!$pdoStatement->execute(array($idSegment))) {
			throw new Exception('Erreur lors du chargement de toutess les étagères d\'un segment depuis la base de données');
		}
		
		// Mettre chaque étagère dans un tableau
		$etageres = array();
		while ($etagere = Etagere::fetch($pdo,$pdoStatement,$easyload)) {
			$etageres[] = $etagere;
		}
		
		// Retourner le tableau
		return $etageres;
	}
	
	/**
	 * Sélectionner toutes les etageres
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Etagere::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les etageres depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
	 * Récupère l'etagere suivante d'un jeu de résultats
	 * @param $pdo PDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etagere 
	 */
	public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idetagere,$segment,$priorite) = $values;
		
		// Construire l'etagere
		return isset(Etagere::$easyload[$idetagere]) ? Etagere::$easyload[$idetagere] :
		       new Etagere($pdo,$idetagere,$segment,$priorite,$easyload);
	}
	
	/**
	 * Supprimer l'etagere
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
	
		// Supprimer les géolocalisations des produits associés
		$pdoStatement = $this->pdo->prepare('DELETE FROM EST_GEOLOCALISE_DANS WHERE ID_ETAGERE = ?');
		if (!$pdoStatement->execute(array($this->getIdetagere()))) { return false; }
		
		
		// Supprimer l'etagere
		$pdoStatement = $this->pdo->prepare('DELETE FROM ETAGERE WHERE ID_ETAGERE = ?');
		if (!$pdoStatement->execute(array($this->getIdetagere()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) etagere dans la base de données');
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
		$pdoStatement = $this->pdo->prepare('UPDATE ETAGERE SET '.implode(', ', $updates).' WHERE ID_ETAGERE = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdetagere())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'un(e) etagere dans la base de données');
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
		return $this->_set(array('ID_SEGMENT','ETAGR_PRIORITE'),array($this->segment,$this->priorite));
	}
	
	/**
	 * Récupérer le/ idetagere
	 * @return int 
	 */
	public function getIdetagere()
	{
		return $this->idetagere;
	}
	
	/**
	 * Récupérer la priorité
	 * @return int 
	 */
	public function getPriorite()
	{
		return $this->priorite;
	}
	
	/**
	 * Définir la priorité
	 * @param $priorite int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setPriorite($priorite,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->priorite = $priorite;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ETAGR_PRIORITE'),array($priorite)) : true;
	}
	
	/**
	 * Récupérer le segment
	 * @return Segment 
	 */
	public function getSegment()
	{
		// Retourner null si nécéssaire
		if ($this->segment == null) { return null; }
		
		// Charger et retourner segment
		return Segment::load($this->pdo,$this->segment);
	}
	
	/**
	 * Définir le segment
	 * @param $segment Segment 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setSegment($segment=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->segment = $segment == null ? null : $segment->getIdsegment();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_SEGMENT'),array($segment == null ? null : $segment->getIdsegment())) : true;
	}
	
	/**
	 * Sélectionner les etageres par segment
	 * @param $pdo PDO 
	 * @param $segment Segment 
	 * @return un tableau d'etagere 
	 */
	public static function selectBySegment(PDO $pdo,Segment $segment)
	{
		$pdoStatement = $pdo->prepare('SELECT * FROM ETAGERE e WHERE e.ID_SEGMENT = ?');
		if (!$pdoStatement->execute(array($segment->getIdsegment()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les etageres par segment depuis la base de données');
		} 
		
		$etageres = array();
		while ($etagere = Etagere::fetch($pdo,$pdoStatement,true)) {
			$etageres[] = $etagere;
		}
		
		// Retourner le tableau
		return $etageres; 
	}
	
	
	/**
	 * Sélectionner les geoloc
	 * @return PDOStatement 
	 */
	public function selectGeoloc()
	{
		return Geolocalisation::selectByEtagere($this->pdo,$this);
	}
	
	/**
	 * Ajouter une a un produit produit
	 * @param $produit Produit 
	 * @return bool opération réussie ?
	 */
	public function addProduit(Produit $produit)
	{
		$pdoStatement = $this->pdo->prepare('INSERT INTO EST_GEOLOCALISE_DANS (ID_ETAGERE,ID_PRODUIT) VALUES (?,?)');
		if (!$pdoStatement->execute(array($this->getIdetagere(),$produit->getIdproduit()))) {
			throw new Exception('Erreur lors de l\'ajout d\'un(e) géolocalisation d\'un produit à un(e) etagere dans la base de données');
		}
		
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * Supprimer une produit
	 * @param $produit Produit 
	 * @return bool opération réussie ?
	 */
	public function delProduit(Produit $produit)
	{
		$pdoStatement = $this->pdo->prepare('DELETE FROM EST_GEOLOCALISE_DANS WHERE ID_ETAGERE = ? AND ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($this->getIdetagere(),$produit->getIdproduit()))) {
			throw new Exception('Erreur lors de la suppression d\'un(e) produit à un(e) etagere dans la base de données');
		}
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * Sélectionner les produits
	 * @return tableau de produit
	 */
	public function selectProduits()
	{
		return Produit::selectByEtagere($this->pdo,$this);
	}
	
	/**
	 * Récupérer la position de l'étagère dans le segment
	 */
	public static function getPosition(ownPDO $pdo, $idetagere, $idSegment){
		
		/* Récupérer les étagères du segment */
		$etageres = self::loadAllSegment($pdo, $idSegment);
		
		for($position = 0; $position < count($etageres); $position++){
			if ($etageres[$position]->getIdetagere() == $idetagere){
				return ($position + 1);
			}
		}
	}
	
	/**
	 * Sélectionner les etageres par produit
	 * @param $pdo PDO 
	 * @param $produit Produit 
	 * @return PDOStatement 
	 */
	public static function selectByProduit(PDO $pdo,Produit $produit)
	{
		$pdoStatement = $pdo->prepare('SELECT * FROM ETAGERE e, EST_GEOLOCALISE_DANS a WHERE e.ID_ETAGERE = a.ID_ETAGERE AND a.ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($produit->getIdProduit()))) {
			throw new Exception('Erreur lors du chargement de toutes les etageres par produit depuis la base de données');
		}  
		$etageres = array();
		while ($etagere = Etagere::fetch($pdo,$pdoStatement,true)) {
			$etageres[] = $etagere;
		}
		
		// Retourner le tableau
		return $etageres; 
	}
	
	public function getEtage()	{
		$pdoStatement = $this->pdo->prepare('	SELECT et.*
												FROM ETAGERE e, SEGMENT s, RAYON r, ZONE z, ETAGE et
												WHERE e.ID_SEGMENT = s.ID_SEGMENT
												AND s.ID_RAYON = r.ID_RAYON
												AND r.ID_ZONE = z.ID_ZONE
												AND z.ID_ETAGE = et.ID_ETAGE
												AND	e.ID_ETAGERE = ?
												');
												
		if (!$pdoStatement->execute(array($this->idetagere))) {
			throw new Exception('Erreur lors du chargement de toutes les etageres par produit depuis la base de données');
		}  
		
		return Etage::fetch($this->pdo,$pdoStatement,true);
		
	}
	
	public function getZone()	{
		$pdoStatement = $this->pdo->prepare('	SELECT z.*
												FROM ETAGERE e, SEGMENT s, RAYON r, ZONE z
												WHERE e.ID_SEGMENT = s.ID_SEGMENT
												AND s.ID_RAYON = r.ID_RAYON
												AND r.ID_ZONE = z.ID_ZONE
												AND	e.ID_ETAGERE = ?
												');
												
		if (!$pdoStatement->execute(array($this->idetagere))) {
			throw new Exception('Erreur lors du chargement de toutes les etageres par produit depuis la base de données');
		}  
		
		return Zone::fetch($this->pdo,$pdoStatement,true);
		
	}

	
	
}
?>