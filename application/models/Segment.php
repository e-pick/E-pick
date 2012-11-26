<?php
/**
 * @class Segment
 * @date 01/03/2011 (dd/mm/yyyy)
 * @generator WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
class Segment
{
	/// @var PDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idsegment;
	
	/// @var int id de rayon
	private $rayon;
	
	/// @var int
	private $priorite;
	
	/**
	 * Construire un segment
	 * @param $pdo PDO 
	 * @param $idsegment int 
	 * @param $libelle string 
	 * @param $rayon int id de rayon
	 * @param $rpiorite int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Segment 
	 */
	protected function __construct(PDO $pdo,$idsegment,$rayon=null,$priorite=null,$easyload=true)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idsegment 	= $idsegment;
		$this->rayon 		= $rayon;
		$this->priorite		= $priorite;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Segment::$easyload[$idsegment] = $this;
		}
	}
	
	/**
	 * Créer un segment
	 * @param $pdo PDO 
	 * @param $libelle string 
	 * @param $rayon Rayon 
	 * @param $priorite int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Segment 
	 */
	public static function create(PDO $pdo,$rayon,$priorite=null,$easyload=true)
	{
		// Ajouter le segment dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO SEGMENT (ID_RAYON,SEG_PRIORITE) VALUES (?,?)');
		
		if (!$pdoStatement->execute(array($rayon->getIdrayon(),$priorite))) {
			throw new Exception('Erreur durant l\'insertion d\'un segment dans la base de données');
		}
		// Construire le segment
		return new Segment($pdo,$pdo->lastInsertId(),$rayon == null ? null : $rayon->getIdrayon(),$priorite,$easyload);
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
		return $pdo->prepare('SELECT * FROM SEGMENT s '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un segment
	 * @param $pdo PDO 
	 * @param $idsegment int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Segment 
	 */
	public static function load(PDO $pdo,$idsegment,$easyload=true)
	{
		// Déjà chargé ?
		if (isset(Segment::$easyload[$idsegment])) {
			return Segment::$easyload[$idsegment];
		}
		
		// Charger le segment
		$pdoStatement = Segment::_select($pdo,'s.ID_SEGMENT = ?');
		if (!$pdoStatement->execute(array($idsegment))) {
			throw new Exception('Erreur lors du chargement d\'un(e) segment depuis la base de données');
		}
		
		// Récupérer le segment depuis le jeu de résultats
		return Segment::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous les segments
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Segment[] tableau de segments
	 */
	public static function loadAll(PDO $pdo,$easyload=false)
	{
		// Sélectionner tous les segments
		$pdoStatement = Segment::selectAll($pdo);
		
		// Mettre chaque segment dans un tableau
		$segments = array();
		while ($segment = Segment::fetch($pdo,$pdoStatement,$easyload)) {
			$segments[] = $segment;
		}
		
		// Retourner le tableau
		return $segments;
	}
	
	/**
	 * Charger tous les segments d'une rayon
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @param $idRayon int 
	 * @return Segment[] tableau de segments du rayon
	 */
	public static function loadAllRayon(PDO $pdo,$idRayon,$easyload=false)
	{
		// Sélectionner tous les segments du rayon
		$pdoStatement = Segment::_select($pdo, 's.ID_RAYON = ?');
		if (!$pdoStatement->execute(array($idRayon))) {
			throw new Exception('Erreur lors du chargement de tous les segments du rayons depuis la base de données');
		}
		
		// Mettre chaque segment dans un tableau
		$segments = array();
		while ($segment = Segment::fetch($pdo,$pdoStatement,$easyload)) {
			$segments[] = $segment;
		}
		
		// Retourner le tableau
		return $segments;
	}
	
	/**
	 * Sélectionner tous les segments
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Segment::_select($pdo,null,'s.ID_SEGMENT');
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous les segments depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
     * Compter le nombre de segments pour un rayon
     * @param $pdo PDO 
     * @return int nombre de segments pour le rayon
     */
    public static function count(ownPDO $pdo, $idRayon) {
		$pdoStatement = $pdo->prepare('SELECT COUNT(s.ID_SEGMENT) FROM SEGMENT s WHERE s.ID_RAYON = ?');
        if(!($pdoStatement->execute(array($idRayon)))) {
            throw new Exception('Erreur lors du comptage des segment pour le rayon dans la base de données');
        }
        return $pdoStatement->fetchColumn();
    }
	
	/**
	 * Récupère le segment suivant d'un jeu de résultats
	 * @param $pdo PDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Segment 
	 */
	public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idsegment,$rayon,$priorite) = $values;
		
		// Construire le segment
		return isset(Segment::$easyload[$idsegment]) ? Segment::$easyload[$idsegment] :
		       new Segment($pdo,$idsegment,$rayon,$priorite,$easyload);
	}
	
	/**
	 * Supprimer le segment
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
		// Supprimer les etageres associées
		$etageres = $this->selectEtageres();
		if ($etageres != null){
			foreach($etageres as $etagere){
				if (!$etagere->delete()) return false;
			}
		}
		
		// Supprimer le segment
		$pdoStatement = $this->pdo->prepare('DELETE FROM SEGMENT WHERE ID_SEGMENT = ?');
		if (!$pdoStatement->execute(array($this->getIdsegment()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) segment dans la base de données');
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
		$pdoStatement = $this->pdo->prepare('UPDATE SEGMENT SET '.implode(', ', $updates).' WHERE ID_SEGMENT = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdsegment())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'un(e) segment dans la base de données');
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
		return $this->_set(array('ID_RAYON','SEG_PRIORITE'),array($this->rayon,$this->priorite));
	}
	
	/**
	 * Récupérer le idsegment
	 * @return int 
	 */
	public function getIdsegment()
	{
		return $this->idsegment;
	}
	
	public function __toString()
	{
		return $this->rayon.'_'.$this->idsegment.'<br />';
	}
	
	
	/**
	 * Retourne l'id du segment pour une position donnée dans un rayon donné
	 * @param $pdo ownPDO  
	 * @param $idrayon  identifiant du rayon
	 * @param $numsegment  numéro du segment
	 * @return l'id du segment
	 */
	public static function getIdSegmentByOrder(ownPDO $pdo, $idrayon,$numsegment){
		$id 	= $idrayon;
		$rayon 	= Rayon::load($pdo,$id);
		$num	= ($rayon->getType() == 'vrac') ? 0 : $numsegment-1; 
		$nb 	= 1;
		 
 
		// Charger le segment		
		$pdoStatement = Segment::_select($pdo,'s.ID_RAYON = :idRayon','ID_SEGMENT', ':debut, :nombre');
		
		$pdoStatement->bindParam(':idRayon', $id, PDO::PARAM_INT);
		$pdoStatement->bindParam(':debut', $num, PDO::PARAM_INT);
		$pdoStatement->bindParam(':nombre', $nb, PDO::PARAM_INT); 
		
 		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement d\'un segment depuis la base de données');
		}
		// Récupérer le segment depuis le jeu de résultats
		return Segment::fetch($pdo,$pdoStatement,true)->getIdsegment();
		 
	}
	
	/**
	 * Sélectionner les etageres
	 * @return PDOStatement 
	 */
	public function selectEtageres()
	{
		return Etagere::selectBySegment($this->pdo,$this);
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
		return $execute ? $this->_set(array('SEG_PRIORITE'),array($priorite)) : true;
	}

	/**
	 * Récupérer le rayon
	 * @return Rayon 
	 */
	public function getRayon()
	{
		// Retourner null si nécéssaire
		if ($this->rayon == null) { return null; }
		
		// Charger et retourner rayon
		return Rayon::load($this->pdo,$this->rayon);
	}
	
	/**
	 * Définir le rayon
	 * @param $rayon Rayon 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setRayon($rayon=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->rayon = $rayon == null ? null : $rayon->getIdrayon();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_RAYON'),array($rayon == null ? null : $rayon->getIdrayon())) : true;
	}
	
	public static function getPosition(ownPDO $pdo, $idsegment, $idrayon){
		
		/* Récupérer les segments du rayon */
		$segments = self::loadAllRayon($pdo, $idrayon);
		
		for($position = 0; $position < count($segments); $position++){
			if ($segments[$position]->getIdsegment() == $idsegment){
				return chr($position + ord('A'));
			}
		}
	}
	
	/**
	 * Sélectionner les segments par rayon
	 * @param $pdo PDO 
	 * @param $rayon Rayon 
	 * @return les segments du rayon
	 */
	public static function selectByRayon(ownPDO $pdo,Rayon $rayon)
	{  
		$pdoStatement = $pdo->prepare('SELECT s.ID_SEGMENT, s.ID_RAYON, s.SEG_PRIORITE FROM SEGMENT s WHERE s.ID_RAYON = ? ORDER BY ID_SEGMENT');
		if (!$pdoStatement->execute(array($rayon->getIdrayon()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les segments par rayon depuis la base de données');
		}
		
		$segments = array();
		while ($segment = Segment::fetch($pdo,$pdoStatement,true)) {
			$segments[] = $segment;
		}
		
		// Retourner le tableau
		return $segments; 
	}
}
?>