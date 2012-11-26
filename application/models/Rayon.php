<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Rayon.php
 *
 */
 
class Rayon
{
	/// @var PDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idrayon;
	
	/// @var int id de zone
	private $zone;
	
	/// @var string 
	private $libelle;
	
	/// @var int 
	private $position_top;
	
	/// @var int 
	private $position_left;
	
	/// @var string 
	private $sens;
	
	/// @var int largeur du rayon
	private $hauteur;
	
	/// @var int largeur du rayon
	private $largeur;
	
	/// @var string type du rayon
	private $type;
	
	/// @var int
	private $priorite;
	
	/// @var string
	private $localisation;
	
	/**
	 * Construire un rayon
	 * @param $pdo PDO 
	 * @param $idrayon int 
	 * @param $libelle string 
	 * @param $position_top int 
	 * @param $position_left int 
	 * @param $sens string 
	 * @param $zone int id de zone
	 * @param $hauteur int 
	 * @param $largeur int 
	 * @param $type string 
	 * @param $priorite int 
	 * @param $localisation string 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Rayon 
	 */
	protected function __construct(PDO $pdo,$idrayon,$zone,$libelle,$position_top,$position_left,$sens,$hauteur,$largeur,$type,$priorite=null,$localisation=null,$easyload=false)
	{ 
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idrayon 			= $idrayon;
		$this->libelle 			= $libelle;
		$this->position_top 	= $position_top;
		$this->position_left 	= $position_left;
		$this->sens 			= $sens;
		$this->zone 			= $zone;
		$this->hauteur 			= $hauteur;
		$this->largeur 			= $largeur;
		$this->type 			= $type;
		$this->priorite 		= $priorite;
		$this->localisation		= $localisation;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Rayon::$easyload[$idrayon] = $this;
		}
	}
	
	
	
	/**
	 *
	 * Teste l'intégrité des attributs de l'objet rayon
	 * @param $attribute le nom de l'attribut
	 * @param $value la valeur à tester
	 * @return boolean => true si ok, false sinon
	 *
	 */
	public static function testIntegrite($attribute, $value){
		$pdo = DB :: getInstance();
		
		switch ($attribute) {
			case 'libelle' :
			case 'Localisation':
				$libelle = $value;
				return (is_string($libelle) && $value != null && $libelle != '');
				break;
			case 'type' :
				$type = $value;
				return (is_string($type) && $value != null && $type != '' && ($type == 'classique' || $type == 'vrac'));
				break;
			
			case 'Priorite' :
				return (!is_string($value) && in_array($value, array(0,1,2,3)));
				break;
				
			case 'idEtage' :
				$idEtage = $value;
				return (!is_string($idEtage) && (Etage::load($pdo, $idEtage) != null));
				break;
				
			default:
				throw new Exception(gettext('L\'attribut ') . $attribut . gettext(' ne fait pas partie de l\'objet ') . gettext('rayon'),3);
				break;
		}
	}
	
		/**
     * Teste si le libelle est déjà utilisé
     * @param $pdo ownPDO 
     * @param $libelle le libelle à tester 
	 * @param $id l'idRayon utilisé dans le cas d'une édition 
     * @return true si le login est déjà utilisé, false sinon
     */
	public static function libelleUsed(ownPDO $pdo, $libelle,  $id=0){ 
 		
		if ($id == 0){
			$pdoStatement = Rayon::_select($pdo,'r.RAY_LIBELLE = ? ');
			if(!$pdoStatement->execute(array($libelle))) {
				throw new Exception('Erreur lors du test du login depuis la base de données');
			} 
		}
		else {
			$pdoStatement = Rayon::_select($pdo,'r.RAY_LIBELLE = ? AND r.ID_RAYON != ?');
			if(!$pdoStatement->execute(array($libelle, $id))) {
				throw new Exception('Erreur lors du test du login depuis la base de données');
			} 
		} 
		
        return ($pdoStatement->rowCount() == 0) ? false : true;	
	}
	
	/**
	 * Créer un rayon
	 * @param $pdo PDO 
	 * @param $libelle string 
	 * @param $position_top int 
	 * @param $position_left int 
	 * @param $sens string 
	 * @param $zone Zone 
	 * @param $hauteur int 
	 * @param $largeur int 
	 * @param $type string 
	 * @param $priorite int 
	 * @param $localisation string 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Rayon 
	 */
	public static function create(PDO $pdo,$zone,$libelle,$position_top,$position_left,$sens,$hauteur,$largeur,$type,$priorite=null,$localisation=null,$easyload=true)
	{
		// Ajouter le rayon dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO RAYON (ID_ZONE,RAY_LIBELLE,RAY_POSITION_TOP,RAY_POSITION_LEFT,RAY_SENS,RAY_HAUTEUR,RAY_LARGEUR,RAY_TYPE,RAY_PRIORITE,RAY_LOCALISATION) VALUES (?,?,?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($zone->getIdzone(),$libelle,$position_top,$position_left,$sens,$hauteur,$largeur,$type,$priorite,$localisation))) {
			throw new Exception('Erreur durant l\'insertion d\'un(e) rayon dans la base de données');
		}
		
		// Construire le rayon
		return new Rayon($pdo,$pdo->lastInsertId(),$zone->getIdzone(),$libelle,$position_top,$position_left,$sens,$hauteur,$largeur,$type,$priorite,$localisation,$easyload);
	}
	
	/**
	 * Requête de séléction
	 * @param $pdo PDO 
	 * @param $where string 
	 * @param $orderby string 
	 * @param $limit string 
	 * @return PDOStatement 
	 */
	private static function _select(ownPDO $pdo,$where=null,$orderby=null,$limit=null)
	{
		return $pdo->prepare('SELECT * FROM RAYON r '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un rayon
	 * @param $pdo PDO 
	 * @param $idrayon int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Rayon 
	 */
	public static function load(ownPDO $pdo,$idrayon,$easyload=true)
	{
	
		// Déjà chargé ?
		if (isset(Rayon::$easyload[$idrayon])) {
			return Rayon::$easyload[$idrayon];
		}
		
		// Charger le rayon
		$pdoStatement = Rayon::_select($pdo,'r.ID_RAYON = ?');
		if (!$pdoStatement->execute(array($idrayon))) {
			throw new Exception('Erreur lors du chargement d\'un rayon depuis la base de données');
		} 
		// Récupérer le rayon depuis le jeu de résultats
		return Rayon::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous les rayons
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Rayon[] tableau de rayons
	 */
	public static function loadAll(PDO $pdo,$first=null, $etageFilter=null, $zoneFilter=null, $libelleFilter=null, $easyload=false)
	{
	
		$select		= 'SELECT r.* FROM ';
		$tables 	= array('RAYON r');
		$where 		= '';
		$limit 		= '';
		$array_attr = array();
		
		if($etageFilter != null){
			$tables[] 	= 'ZONE z';
			$where 		= 'r.ID_ZONE = z.ID_ZONE AND z.ID_ETAGE = ? AND ';
			$array_attr	= array($etageFilter);
		}
		
		if($zoneFilter != null){
			$tables 	= array('RAYON r');
			$where 		= 'r.ID_ZONE = ? AND ';
			$array_attr	= array($zoneFilter);
		}
		
		if($libelleFilter != null){
			$where 		   .= 'r.RAY_LIBELLE LIKE ? AND ';
			$array_attr[]	= '%' . html_entities($libelleFilter, false) . '%';
		}
		
		if($first != null){
			$limit =  $first.','.RESULTAT_PAR_PAGE;
		}
		
		if($where != ''){
			$where .= ' 1=1 ';
			// die($select . implode(', ', $tables) . ' WHERE ' . $where );
			if($limit != '')
				$pdoStatement = $pdo->prepare($select . implode(', ', $tables) . ' WHERE ' . $where . ' LIMIT ' . $limit);
			else
				$pdoStatement = $pdo->prepare($select . implode(', ', $tables) . ' WHERE ' . $where);
			if (!$pdoStatement->execute($array_attr))
				throw new Exception('Erreur lors du chargement de tous les rayons depuis la base de données');
		}
		else{		
			if($limit != '')
				$pdoStatement = Rayon::_select($pdo,null,null,$limit);
			else
				$pdoStatement = Rayon::_select($pdo); 
			if (!$pdoStatement->execute())
				throw new Exception('Erreur lors du chargement de toutes les commandes depuis la base de données');	
		} 
		// Mettre chaque rayon dans un tableau
		$rayons = array();
		while ($rayon = Rayon::fetch($pdo,$pdoStatement,$easyload)) {
			$rayons[] = $rayon;
		}
		
		// print_r($rayons);
		// Retourner le tableau
		return $rayons;
	}
	
	/**
	 * Charger tous les rayons d'une zone
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @param $idZone int 
	 * @return Rayon[] tableau de rayons de la zone
	 */
	public static function loadAllZone(PDO $pdo,$idZone,$easyload=false)
	{
		// Sélectionner tous les rayons de la zone
		$pdoStatement = Rayon::_select($pdo, 'r.ID_ZONE = ?');
		if (!$pdoStatement->execute(array($idZone))) {
			throw new Exception('Erreur lors du chargement de tous les rayons de la zone depuis la base de données');
		}
		
		// Mettre chaque rayon dans un tableau
		$rayons = array();
		while ($rayon = Rayon::fetch($pdo,$pdoStatement,$easyload)) {
			$rayons[] = $rayon;
		}
		
		// Retourner le tableau
		return $rayons;
	}
	
	/**
	 * Sélectionner tous les rayons
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Rayon::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous les rayons depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
	 * Récupère le rayon suivant d'un jeu de résultats
	 * @param $pdo PDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Rayon 
	 */
	public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idrayon,$zone,$libelle,$position_top,$position_left,$sens,$hauteur,$largeur,$type,$priorite,$localisation) = $values;
		
		// Construire le rayon
		return isset(Rayon::$easyload[$idrayon]) ? Rayon::$easyload[$idrayon] :
		       new Rayon($pdo,$idrayon,$zone,$libelle,$position_top,$position_left,$sens,$hauteur,$largeur,$type,$priorite,$localisation,$easyload);
	}
	
	/**
	 * Supprimer le rayon
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
	
		// Supprimer les segments associés
		$segments = $this->selectSegments();
		
		if($segments != null) {
			foreach($segments as $segment){
				if(!$segment->delete()) return false;
			}
		}
		
		// Supprimer le rayon
		$pdoStatement = $this->pdo->prepare('DELETE FROM RAYON WHERE ID_RAYON = ?');
		if (!$pdoStatement->execute(array($this->getIdrayon()))) {
			throw new Exception('Erreur lors de la supression d\'un rayon dans la base de données');
		}
		
		// Opération réussie ?
		return $pdoStatement->rowCount() == 1;
	}
	
	
		/**
		 * Retourne le premier rayon existant
		 * @param $pdo PDO 
		 * @return int id du premier rayon existant
		 */
	
		public static function getFirstId(ownPDO $pdo) {
		$pdoStatement = Rayon::_select($pdo, null, 'r.ID_RAYON', 1);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors de la récupération du premier rayon');
		}
        return $pdoStatement->fetchColumn();
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
		$pdoStatement = $this->pdo->prepare('UPDATE RAYON SET '.implode(', ', $updates).' WHERE ID_RAYON = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdrayon())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'un rayon dans la base de données');
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
		return $this->_set(	array('RAY_LIBELLE','RAY_POSITION_TOP','RAY_POSITION_LEFT','RAY_SENS','ID_ZONE','RAY_HAUTEUR','RAY_LARGEUR','RAY_TYPE','RAY_PRIORITE','RAY_LOCALISATION'),
							array($this->libelle,$this->position_top,$this->position_left,$this->sens,$this->zone,$this->hauteur,$this->largeur,$this->type,$this->priorite,$this->localisation));
	}
	
	/**
	 * Récupérer le idrayon
	 * @return int 
	 */
	public function getIdrayon()
	{
		return $this->idrayon;
	}
	
	/**
	 * Sélectionner les checkpoints
	 * @return PDOStatement 
	 */
	public function selectCheckpoints()
	{
		return Checkpoint::selectByRayon($this->pdo,$this);
	}
	
	/**
	 * Sélectionner les segments
	 * @return PDOStatement 
	 */
	public function selectSegments()
	{
		return Segment::selectByRayon($this->pdo,$this);
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
		return $execute ? $this->_set(array('RAY_LIBELLE'),array($libelle)) : true;
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
		return $execute ? $this->_set(array('RAY_POSITION_TOP'),array($position_top)) : true;
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
		return $execute ? $this->_set(array('RAY_POSITION_LEFT'),array($position_left)) : true;
	}
	
	/**
	 * Récupérer le sens
	 * @return string 
	 */
	public function getSens()
	{
		return $this->sens;
	}
	
	/**
	 * Définir le sens
	 * @param $sens string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setSens($sens,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->sens = $sens;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('RAY_SENS'),array($sens)) : true;
	}
	
	/**
	 * Récupérer la zone
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
	 * Définir la zone
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
	 * Récupérer la hauteur
	 * @return string 
	 */
	public function getHauteur()
	{
		return $this->hauteur;
	}
	
	/**
	 * Définir la hauteur
	 * @param $hauteur string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setHauteur($hauteur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->hauteur = $hauteur;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('RAY_HAUTEUR'),array($hauteur)) : true;
	}
	
	/**
	 * Récupérer la largeur
	 * @return string 
	 */
	public function getLargeur()
	{
		return $this->largeur;
	}
	
	/**
	 * Définir la largeur
	 * @param $largeur string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setLargeur($largeur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->largeur = $largeur;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('RAY_LARGEUR'),array($largeur)) : true;
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
	 * @param $type string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setType($type,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->type = $type;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('RAY_TYPE'),array($type)) : true;
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
		return $execute ? $this->_set(array('RAY_PRIORITE'),array($priorite)) : true;
	}
	
	/**
	 * Récupérer la localisation
	 * @return int 
	 */
	public function getLocalisation()
	{
		return $this->localisation;
	}
	
	/**
	 * Définir la localisation
	 * @param $localisation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setLocalisation($localisation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->localisation = $localisation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('RAY_LOCALISATION'),array($localisation)) : true;
	}
	
	/**
	 * Sélectionner les rayons par zone
	 * @param $pdo PDO 
	 * @param $zone Zone 
	 * @return PDOStatement 
	 */
	public static function selectByZone(PDO $pdo,Zone $zone)
	{
		$pdoStatement = $pdo->prepare('SELECT r.* FROM RAYON r WHERE r.ID_ZONE = ?');
		if (!$pdoStatement->execute(array($zone->getIdzone()))) {
			throw new Exception('Erreur lors du chargement de tous les rayons par zone depuis la base de données');
		}
		
		// Mettre chaque rayon dans un tableau
		$rayons = array();
		while ($rayon = Rayon::fetch($pdo,$pdoStatement)) {
			$rayons[] = $rayon;
		}
		
		// Retourner le tableau
		return $rayons;
	}
	
	/**
	 * Retourner les rayons par libelle
	 * @param $pdo PDO
	 * @param $libelle string
	 * @param $idZone string
	 * @return $rayons array
	 */
	public static function getRayonByLibelleAndZone(ownPDO $pdo, $libelle, $idZone){
		
		$pdoStatement = $pdo->prepare('SELECT r.* FROM RAYON r WHERE ID_ZONE = ? AND r.RAY_LIBELLE like ?');
		if (!$pdoStatement->execute(array($idZone, '%' . $libelle . '%'))) {
			throw new Exception('Erreur lors du chargement de tous les rayons par libelle depuis la base de données');
		}
		
		// Mettre chaque rayon dans un tableau
		$rayons = array();
		while ($rayon = Rayon::fetch($pdo,$pdoStatement)) {
			$rayons[] = $rayon;
		}
		
		// Retourner le tableau
		return $rayons;
	}
}
?>