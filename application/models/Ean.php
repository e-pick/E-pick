<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Ean.php
 *
 */
 
class Ean
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idEan;
	
	/// @var int id de produit
	private $idProduit;
	
	/// @var int 
	private $ean;
	

	
	/**
	 * Construire un ean
	 * @param $pdo ownPDO 
	 * @param $idEan int 
	 * @param $idProduit int id de produit
	 * @param $ean int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ean 
	 */
	protected function __construct(ownPDO $pdo,$idEan,$idProduit=null,$ean,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idEan 	= $idEan;
		$this->ean 		= $ean;
		$this->produit 	= $idProduit;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Ean::$easyload[$idEan] = $this;
		}
	}
	
	/**
	 * Cr�er un ean
	 * @param $pdo ownPDO 
	 * @param $ean int 
	 * @param $idProduit Produit 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ean 
	 */
	public static function create(ownPDO $pdo,$idProduit,$ean,$easyload=true)
	{
		// Ajouter le ean dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO EAN (ID_PRODUIT,EAN_EAN) VALUES (?,?)');
		if (!$pdoStatement->execute(array($idProduit,$ean))) {
			throw new Exception('Erreur durant l\'insertion d\'un ean dans la base de donn�es');
		}
		
		// Construire le ean
		return new Ean($pdo,$pdo->lastInsertId(),$idProduit,$ean,$easyload);
	}
	
	/**
	 * Requ�te de s�l�ction
	 * @param $pdo ownPDO 
	 * @param $where string 
	 * @param $orderby string 
	 * @param $limit string 
	 * @return PDOStatement 
	 */
	private static function _select(ownPDO $pdo,$where=null,$orderby=null,$limit=null)
	{
		return $pdo->prepare('SELECT e.ID_EAN, e.ID_PRODUIT, e.EAN_EAN FROM EAN e '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDERBY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un ean
	 * @param $pdo ownPDO 
	 * @param $idEan int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ean 
	 */
	public static function load(ownPDO $pdo,$idEan,$easyload=true)
	{
		// D�j� charg� ?
		if (isset(Ean::$easyload[$idEan])) {
			return Ean::$easyload[$idEan];
		}
		
		// Charger le ean
		$pdoStatement = Ean::_select($pdo,'e.ID_EAN = ?');
		if (!$pdoStatement->execute(array($idEan))) {
			throw new Exception('Erreur lors du chargement d\'un ean depuis la base de donn�es');
		}
		
		// R�cup�rer le ean depuis le jeu de r�sultats
		return Ean::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger un ean
	 * @param $pdo ownPDO 
	 * @param $idEan int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ean 
	 */
	public static function loadByCodeEan(ownPDO $pdo,$ean,$id=0,$easyload=true)
	{
		if ($id == 0){
			// Charger le ean
			$pdoStatement = Ean::_select($pdo,'e.EAN_EAN = ?');
			if (!$pdoStatement->execute(array($ean))) {
				throw new Exception('Erreur lors du chargement d\'un ean depuis la base de donn�es');
			}

		}
		else{
			// Charger le ean
			$pdoStatement = Ean::_select($pdo,'e.EAN_EAN = ? AND e.ID_PRODUIT != ?');
			if (!$pdoStatement->execute(array($ean,$id))) {
				throw new Exception('Erreur lors du chargement d\'un ean depuis la base de donn�es');
			}
		}
		// R�cup�rer le ean depuis le jeu de r�sultats
		return Ean::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger un ean par produit
	 * @param $pdo ownPDO 
	 * @param $idProduit int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ean 
	 */
	public static function loadByProduit(ownPDO $pdo,$idProduit,$easyload=true)
	{
		// Charger le ean
		$pdoStatement = Ean::_select($pdo,'e.ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($idProduit))) {
			throw new Exception('Erreur lors du chargement d\'un ean par produit depuis la base de donn�es');
		}
		
		// Mettre chaque ean dans un tableau
		$eans = array();
		while ($ean = Ean::fetch($pdo,$pdoStatement,$easyload)) {
			$eans[] = $ean;
		}
		
		// Retourner le tableau
		return $eans;
	}
	
	/**
	 * Charger tous les eans
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ean[] tableau de eans
	 */
	public static function loadAll(ownPDO $pdo,$easyload=false)
	{
		// S�lectionner tous les eans
		$pdoStatement = Ean::selectAll($pdo);
		
		// Mettre chaque ean dans un tableau
		$eans = array();
		while ($ean = Ean::fetch($pdo,$pdoStatement,$easyload)) {
			$eans[] = $ean;
		}
		
		// Retourner le tableau
		return $eans;
	}
	
	/**
	 * S�lectionner tous les eans
	 * @param $pdo ownPDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(ownPDO $pdo)
	{
		$pdoStatement = Ean::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les eans depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * R�cup�re le ean suivant d'un jeu de r�sultats
	 * @param $pdo ownPDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ean 
	 */
	public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idEan,$idProduit,$ean) = $values;
		
		// Construire le ean
		return isset(Ean::$easyload[$idEan]) ? Ean::$easyload[$idEan] :
		       new Ean($pdo,$idEan,$idProduit,$ean,$easyload);
	}
	
	/**
	 * Supprimer le ean
	 * @return bool op�ration r�ussie ?
	 */
	public function delete()
	{
		// Supprimer le ean
		$pdoStatement = $this->pdo->prepare('DELETE FROM EAN WHERE ID_EAN = ?');
		if (!$pdoStatement->execute(array($this->getIdEan()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) ean dans la base de donn�es');
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
		$pdoStatement = $this->pdo->prepare('UPDATE EAN SET '.implode(', ', $updates).' WHERE ID_EAN = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdEan())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'un(e) ean dans la base de donn�es');
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
		return $this->_set(array('EAN_EAN','ID_PRODUIT'),array($this->ean,$this->produit));
	}
	
	/**
	 * R�cup�rer le idEan
	 * @return int 
	 */
	public function getIdEan()
	{
		return $this->idEan;
	}
	
	/**
	 * R�cup�rer le ean
	 * @return int 
	 */
	public function getEan()
	{
		return $this->ean;
	}
	
	/**
	 * D�finir le ean
	 * @param $ean int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setEan($ean,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->ean = $ean;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('EAN_EAN'),array($ean)) : true;
	}
	
	/**
	 * R�cup�rer le produit
	 * @return Produit 
	 */
	public function getIdProduit()
	{
		// Retourner null si n�c�ssaire
		if ($this->produit == null) { return null; }
		
		// Charger et retourner produit
		return Produit::load($this->pdo,$this->produit);
	}
	
	/**
	 * D�finir le produit
	 * @param $idProduit Produit 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setIdProduit($idProduit,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->produit = $idProduit;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_PRODUIT'),array($idProduit)) : true;
	}
	
	/**
	 * S�lectionner les eans par produit
	 * @param $pdo ownPDO 
	 * @param $idProduit Produit 
	 * @return PDOStatement 
	 */
	public static function selectByProduit(ownPDO $pdo,$idProduit)
	{
		$pdoStatement = $pdo->prepare('SELECT e.EAN_EAN FROM EAN e WHERE e.ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($idProduit))) {
			throw new Exception('Erreur lors du chargement de tous les eans par produit depuis la base de donn�es');
		}
		
		return $pdoStatement->fetchAll(PDO::FETCH_COLUMN);
	}
}

?>