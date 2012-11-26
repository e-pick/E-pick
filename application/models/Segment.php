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
	 * Cr�er un segment
	 * @param $pdo PDO 
	 * @param $libelle string 
	 * @param $rayon Rayon 
	 * @param $priorite int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Segment 
	 */
	public static function create(PDO $pdo,$rayon,$priorite=null,$easyload=true)
	{
		// Ajouter le segment dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO SEGMENT (ID_RAYON,SEG_PRIORITE) VALUES (?,?)');
		
		if (!$pdoStatement->execute(array($rayon->getIdrayon(),$priorite))) {
			throw new Exception('Erreur durant l\'insertion d\'un segment dans la base de donn�es');
		}
		// Construire le segment
		return new Segment($pdo,$pdo->lastInsertId(),$rayon == null ? null : $rayon->getIdrayon(),$priorite,$easyload);
	}
	
	/**
	 * Requ�te de s�l�ction
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
		// D�j� charg� ?
		if (isset(Segment::$easyload[$idsegment])) {
			return Segment::$easyload[$idsegment];
		}
		
		// Charger le segment
		$pdoStatement = Segment::_select($pdo,'s.ID_SEGMENT = ?');
		if (!$pdoStatement->execute(array($idsegment))) {
			throw new Exception('Erreur lors du chargement d\'un(e) segment depuis la base de donn�es');
		}
		
		// R�cup�rer le segment depuis le jeu de r�sultats
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
		// S�lectionner tous les segments
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
		// S�lectionner tous les segments du rayon
		$pdoStatement = Segment::_select($pdo, 's.ID_RAYON = ?');
		if (!$pdoStatement->execute(array($idRayon))) {
			throw new Exception('Erreur lors du chargement de tous les segments du rayons depuis la base de donn�es');
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
	 * S�lectionner tous les segments
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Segment::_select($pdo,null,'s.ID_SEGMENT');
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous les segments depuis la base de donn�es');
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
            throw new Exception('Erreur lors du comptage des segment pour le rayon dans la base de donn�es');
        }
        return $pdoStatement->fetchColumn();
    }
	
	/**
	 * R�cup�re le segment suivant d'un jeu de r�sultats
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
	 * @return bool op�ration r�ussie ?
	 */
	public function delete()
	{
		// Supprimer les etageres associ�es
		$etageres = $this->selectEtageres();
		if ($etageres != null){
			foreach($etageres as $etagere){
				if (!$etagere->delete()) return false;
			}
		}
		
		// Supprimer le segment
		$pdoStatement = $this->pdo->prepare('DELETE FROM SEGMENT WHERE ID_SEGMENT = ?');
		if (!$pdoStatement->execute(array($this->getIdsegment()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) segment dans la base de donn�es');
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
		$pdoStatement = $this->pdo->prepare('UPDATE SEGMENT SET '.implode(', ', $updates).' WHERE ID_SEGMENT = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdsegment())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'un(e) segment dans la base de donn�es');
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
		return $this->_set(array('ID_RAYON','SEG_PRIORITE'),array($this->rayon,$this->priorite));
	}
	
	/**
	 * R�cup�rer le idsegment
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
	 * Retourne l'id du segment pour une position donn�e dans un rayon donn�
	 * @param $pdo ownPDO  
	 * @param $idrayon  identifiant du rayon
	 * @param $numsegment  num�ro du segment
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
			throw new Exception('Erreur lors du chargement d\'un segment depuis la base de donn�es');
		}
		// R�cup�rer le segment depuis le jeu de r�sultats
		return Segment::fetch($pdo,$pdoStatement,true)->getIdsegment();
		 
	}
	
	/**
	 * S�lectionner les etageres
	 * @return PDOStatement 
	 */
	public function selectEtageres()
	{
		return Etagere::selectBySegment($this->pdo,$this);
	}
	

	/**
	 * R�cup�rer la priorit�
	 * @return int 
	 */
	public function getPriorite()
	{
		return $this->priorite;
	}
	
	/**
	 * D�finir la priorit�
	 * @param $priorite int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPriorite($priorite,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->priorite = $priorite;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('SEG_PRIORITE'),array($priorite)) : true;
	}

	/**
	 * R�cup�rer le rayon
	 * @return Rayon 
	 */
	public function getRayon()
	{
		// Retourner null si n�c�ssaire
		if ($this->rayon == null) { return null; }
		
		// Charger et retourner rayon
		return Rayon::load($this->pdo,$this->rayon);
	}
	
	/**
	 * D�finir le rayon
	 * @param $rayon Rayon 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setRayon($rayon=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->rayon = $rayon == null ? null : $rayon->getIdrayon();
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_RAYON'),array($rayon == null ? null : $rayon->getIdrayon())) : true;
	}
	
	public static function getPosition(ownPDO $pdo, $idsegment, $idrayon){
		
		/* R�cup�rer les segments du rayon */
		$segments = self::loadAllRayon($pdo, $idrayon);
		
		for($position = 0; $position < count($segments); $position++){
			if ($segments[$position]->getIdsegment() == $idsegment){
				return chr($position + ord('A'));
			}
		}
	}
	
	/**
	 * S�lectionner les segments par rayon
	 * @param $pdo PDO 
	 * @param $rayon Rayon 
	 * @return les segments du rayon
	 */
	public static function selectByRayon(ownPDO $pdo,Rayon $rayon)
	{  
		$pdoStatement = $pdo->prepare('SELECT s.ID_SEGMENT, s.ID_RAYON, s.SEG_PRIORITE FROM SEGMENT s WHERE s.ID_RAYON = ? ORDER BY ID_SEGMENT');
		if (!$pdoStatement->execute(array($rayon->getIdrayon()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les segments par rayon depuis la base de donn�es');
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