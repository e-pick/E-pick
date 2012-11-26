<?php

/**
 * @class Obstacle
 * @date 01/03/2011 (dd/mm/yyyy)
 * @generator WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
class Obstacle
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idobstacle;
		
	/// @var int id de etage
	private $etage;
	
	/// @var int 
	private $position_top;
	
	/// @var int 
	private $position_left;
	
	/// @var int 
	private $hauteur;
	
	/// @var int 
	private $largeur;
	
	/// @var varchar
	private $type;
	
	/// @var string
	private $libelle;
	
	/// @var string
	private $couleur;
	
	/**
	 * Construire un obstacle
	 * @param $pdo ownPDO 
	 * @param $idobstacle int 
	 * @param $position_top int 
	 * @param $position_left int 
	 * @param $hauteur int 
	 * @param $largeur int 
	 * @param $etage int id de etage
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Obstacle 
	 */
	protected function __construct(ownPDO $pdo,$idobstacle,$etage=null,$position_top,$position_left,$hauteur,$largeur,$type,$libelle,$couleur,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idobstacle = $idobstacle;
		$this->position_top = $position_top;
		$this->position_left = $position_left;
		$this->hauteur = $hauteur;
		$this->largeur = $largeur;
		$this->type = $type;		
		$this->etage = $etage;
		$this->libelle = $libelle;
		$this->couleur = $couleur;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Obstacle::$easyload[$idobstacle] = $this;
		}
	}
	
	/**
	 * Créer un obstacle
	 * @param $pdo ownPDO 
	 * @param $position_top int 
	 * @param $position_left int 
	 * @param $hauteur int 
	 * @param $largeur int 
	 * @param $etage Etage 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Obstacle 
	 */
	public static function create(ownPDO $pdo,$etage,$position_top,$position_left,$hauteur,$largeur,$type,$libelle,$couleur,$easyload=true)
	{ 
		// Ajouter l'obstacle dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO OBSTACLE (ID_ETAGE,OBS_POSITION_TOP,OBS_POSITION_LEFT,OBS_HAUTEUR,OBS_LARGEUR,OBS_TYPE,OBS_LIBELLE,OBS_COULEUR) VALUES (?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($etage == null ? null : $etage->getIdetage(),$position_top,$position_left,$hauteur,$largeur,$type,$libelle,$couleur))) {
			throw new Exception('Erreur durant l\'insertion d\'un(e) obstacle dans la base de données');
		}
		
		// Construire l'obstacle
		return new Obstacle($pdo,$pdo->lastInsertId(),$etage == null ? null : $etage->getIdetage(),$position_top,$position_left,$hauteur,$largeur,$type,$libelle,$couleur,$easyload);
	}
	
	/**
	 * Requête de séléction
	 * @param $pdo ownPDO 
	 * @param $where string 
	 * @param $orderby string 
	 * @param $limit string 
	 * @return PDOStatement 
	 */
	private static function _select(ownPDO $pdo,$where=null,$orderby=null,$limit=null)
	{
		return $pdo->prepare('SELECT o.ID_OBSTACLE, o.ID_ETAGE, o.OBS_POSITION_TOP, o.OBS_POSITION_LEFT, o.OBS_HAUTEUR, o.OBS_LARGEUR, o.OBS_TYPE, o.OBS_LIBELLE, o.OBS_COULEUR FROM OBSTACLE o '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un obstacle
	 * @param $pdo ownPDO 
	 * @param $idobstacle int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Obstacle 
	 */
	public static function load(ownPDO $pdo,$idobstacle,$easyload=true)
	{
		// Déjà chargé ?
		if (isset(Obstacle::$easyload[$idobstacle])) {
			return Obstacle::$easyload[$idobstacle];
		}
		
		// Charger l'obstacle
		$pdoStatement = Obstacle::_select($pdo,'o.ID_OBSTACLE = ?');
		if (!$pdoStatement->execute(array($idobstacle))) {
			throw new Exception('Erreur lors du chargement d\'un(e) obstacle depuis la base de données');
		}
		
		// Récupérer l'obstacle depuis le jeu de résultats
		return Obstacle::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous les obstacles
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Obstacle[] tableau de obstacles
	 */
	public static function loadAll(ownPDO $pdo,$easyload=false)
	{
		// Sélectionner tous les obstacles
		$pdoStatement = Obstacle::selectAll($pdo);
		
		// Mettre chaque obstacle dans un tableau
		$obstacles = array();
		while ($obstacle = Obstacle::fetch($pdo,$pdoStatement,$easyload)) {
			$obstacles[] = $obstacle;
		}
		
		// Retourner le tableau
		return $obstacles;
	}
	
	/**
	 * Charger tous les rayons d'un etage
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @param $idetage int 
	 * @return Obstacle[] tableau de rayons de la zone
	 */	
		public static function loadAllEtage(ownPDO $pdo,$idetage,$easyload=false)
	{
		// Sélectionner tous les rayons de la zone
		$pdoStatement = Obstacle::_select($pdo, 'o.ID_ETAGE = ?');
		if (!$pdoStatement->execute(array($idetage))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les rayons de la zone depuis la base de données');
		}
		
		// Mettre chaque rayon dans un tableau
		$obstacles = array();
		while ($obstacle = Obstacle::fetch($pdo,$pdoStatement,$easyload)) {
			$obstacles[] = $obstacle;
		}
		
		// Retourner le tableau
		return $obstacles;
	}
	
	/**
	 * Sélectionner tous les obstacles
	 * @param $pdo ownPDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(ownPDO $pdo)
	{
		$pdoStatement = Obstacle::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les obstacles depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
	 * Récupère l'obstacle suivant d'un jeu de résultats
	 * @param $pdo ownPDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Obstacle 
	 */
	public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idobstacle,$etage,$position_top,$position_left,$hauteur,$largeur,$type,$libelle,$couleur) = $values;
		
		// Construire l'obstacle
		return isset(Obstacle::$easyload[$idobstacle]) ? Obstacle::$easyload[$idobstacle] :
		       new Obstacle($pdo,$idobstacle,$etage,$position_top,$position_left,$hauteur,$largeur,$type,$libelle,$couleur,$easyload);
	}
	
	/**
	 * Supprimer l'obstacle
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
		// Supprimer l'obstacle
		$pdoStatement = $this->pdo->prepare('DELETE FROM OBSTACLE WHERE ID_OBSTACLE = ?');
		if (!$pdoStatement->execute(array($this->getIdobstacle()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) obstacle dans la base de données');
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
		$pdoStatement = $this->pdo->prepare('UPDATE OBSTACLE SET '.implode(', ', $updates).' WHERE ID_OBSTACLE = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdobstacle())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'un(e) obstacle dans la base de données');
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
		return $this->_set(array('OBS_POSITION_TOP','OBS_POSITION_LEFT','OBS_HAUTEUR','OBS_LARGEUR','OBS_TYPE','ID_ETAGE','OBS_LIBELLE','OBS_COULEUR'),array($this->position_top,$this->position_left,$this->hauteur,$this->largeur,$this->type,$this->etage,$this->libelle,$this->couleur));
	}
	
	/**
	 * Récupérer le idobstacle
	 * @return int 
	 */
	public function getIdobstacle()
	{
		return $this->idobstacle;
	}
	
	/**
	 * Récupérer la position_top
	 * @return int 
	 */
	public function getPosition_top()
	{
		return $this->position_top;
	}
	
	/**
	 * Définir la position_top
	 * @param $position_top int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setPosition_top($position_top,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->position_top = $position_top;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('OBS_POSITION_TOP'),array($position_top)) : true;
	}
	/**
	 * Récupérer le libelle
	 * @return string
	 */
	public function getLibelle()
	{
		return $this->libelle;
	}
	
	/**
	 * Définir le libelle
	 * @param $libelle string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setLibelle($libelle,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->libelle = $libelle;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('OBS_LIBELLE'),array($libelle)) : true;
	}
	/**
	 * Récupérer la couleur
	 * @return string
	 */
	public function getCouleur()
	{
		return $this->couleur;
	}
	
	/**
	 * Définir la couleur
	 * @param $couleur string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCouleur($couleur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->couleur = $couleur;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('OBS_COULEUR'),array($couleur)) : true;
	}
	/**
	 * Récupérer la position_left
	 * @return int 
	 */
	public function getPosition_left()
	{
		return $this->position_left;
	}
	
	/**
	 * Définir la position_left
	 * @param $position_left int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setPosition_left($position_left,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->position_left = $position_left;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('OBS_POSITION_LEFT'),array($position_left)) : true;
	}
	
	/**
	 * Récupérer la hauteur
	 * @return int 
	 */
	public function getHauteur()
	{
		return $this->hauteur;
	}
	
	/**
	 * Définir la hauteur
	 * @param $hauteur int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setHauteur($hauteur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->hauteur = $hauteur;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('OBS_HAUTEUR'),array($hauteur)) : true;
	}
	
	/**
	 * Récupérer la largeur
	 * @return int 
	 */
	public function getLargeur()
	{
		return $this->largeur;
	}
	
	/**
	 * Définir la largeur
	 * @param $largeur int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setLargeur($largeur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->largeur = $largeur;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('OBS_LARGEUR'),array($largeur)) : true;
	}	
	/**
	 * Récupérer le type
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * Définir le type
	 * @param $largeur string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setType($type,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->type = $type;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('OBS_TYPE'),array($type)) : true;
	}
	
	/**
	 * Récupérer l'etage
	 * @return Etage 
	 */
	public function getEtage()
	{
		// Retourner null si nécéssaire
		if ($this->etage == null) { return null; }
		
		// Charger et retourner etage
		return Etage::load($this->pdo,$this->etage);
	}
	
	/**
	 * Définir l'etage
	 * @param $etage Etage 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setEtage($etage=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->etage = $etage == null ? null : $etage->getIdetage();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_ETAGE'),array($etage == null ? null : $etage->getIdetage())) : true;
	}
	
	/**
	 * Sélectionner les obstacles par etage
	 * @param $pdo ownPDO 
	 * @param $etage Etage 
	 * @return PDOStatement 
	 */
	public static function selectByEtage(ownPDO $pdo,Etage $etage)
	{
		$pdoStatement = $pdo->prepare('SELECT o.ID_OBSTACLE, o.ID_ETAGE, o.OBS_POSITION_TOP, o.OBS_POSITION_LEFT, o.OBS_HAUTEUR, o.OBS_LARGEUR,o.OBS_TYPE FROM OBSTACLE o WHERE o.ID_ETAGE = ?');
		if (!$pdoStatement->execute(array($etage->getIdetage()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les obstacles par etage depuis la base de données');
		}
		return $pdoStatement;
	}
}

?>