<?php
/**
 * @class Etage
 * @date 01/03/2011 (dd/mm/yyyy)
 * @generator WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
class Etage
{
	/// @var PDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idetage;
	
	/// @var string 
	private $libelle;
	
	/// @var int 
	private $hauteur;
	
	/// @var int 
	private $largeur;
	
	/// @var int 
	private $pt_depart_top;
	
	/// @var int 
	private $pt_depart_left;	
	
	/// @var int 
	private $pt_arrive_top;
	
	/// @var int 
	private $pt_arrive_left;
	
	/// @var int
	private $priorite;
	
	/**
	 * Construire un(e) etage
	 * @param $pdo PDO 
	 * @param $idetage int 
	 * @param $libelle string 
	 * @param $hauteur int 
	 * @param $largeur int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etage 
	 */
	protected function __construct(PDO $pdo,$idetage,$libelle,$hauteur,$largeur,$pt_depart_top,$pt_depart_left,$pt_arrive_top,$pt_arrive_left,$priorite=null,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idetage 			= $idetage;
		$this->libelle 			= $libelle;
		$this->hauteur 			= $hauteur;
		$this->largeur 			= $largeur;
		$this->pt_depart_top 	= $pt_depart_top;
		$this->pt_depart_left 	= $pt_depart_left;
		$this->pt_arrive_top 	= $pt_arrive_top;
		$this->pt_arrive_left 	= $pt_arrive_left;
		$this->priorite 		= $priorite; 
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Etage::$easyload[$idetage] = $this;
		}
	}
	
	
	/**
	 *
	 * Teste l'int�grit� des attributs de l'objet Etage
	 * @param $attribute le nom de l'attribut
	 * @param $value la valeur � tester
	 * @return boolean => true si ok, false sinon
	 *
	 */	
	public static function testIntegrite($attribute, $value){
		$pdo = DB :: getInstance();
		
		switch ($attribute) {
			case 'libelle' :
				$libelle = $value;
				return (is_string($libelle) && $libelle != null && $libelle != '');
				break;
				
			default:
				throw new Exception(gettext('L\'attribut ') . $attribut . gettext(' ne fait pas partie de l\'objet ') . gettext('Etage'),3);
				break;
		}
	}
	
	/**
     * Teste si le libelle est d�j� utilis�
     * @param $pdo ownPDO 
     * @param $libelle le libelle � tester
	 * @param $idEtage l'�tage dans le cas d'une �dition 
     * @return true si le login est d�j� utilis�, false sinon
     */
	public static function libelleUsed(ownPDO $pdo, $libelle, $idEtage=0){ 
 		
		if ($idEtage == 0){
			$pdoStatement = Etage::_select($pdo, 'e.ETA_LIBELLE = ?');
			if(!$pdoStatement->execute(array($libelle))) {
				throw new Exception('Erreur lors du test du login depuis la base de donn�es');
			} 
		}
		else {
			$pdoStatement = Etage::_select($pdo,'e.ETA_LIBELLE = ? AND e.ID_ETAGE != ?');
			if(!$pdoStatement->execute(array($libelle,$idEtage))) {
				throw new Exception('Erreur lors du test du login depuis la base de donn�es');
			} 
		} 
		
        return ($pdoStatement->rowCount() == 0) ? false : true;	
	}
	
	
	
	
	/**
	 * Cr�er un etage
	 * @param $pdo PDO 
	 * @param $libelle string 
	 * @param $hauteur int 
	 * @param $largeur int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etage 
	 */
	public static function create(PDO $pdo,$libelle,$hauteur,$largeur,$pt_depart_top,$pt_depart_left,$pt_arrive_top,$pt_arrive_left,$priorite=null,$easyload=true)
	{
		// Ajouter l'etage dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO ETAGE (ETA_LIBELLE,ETA_HAUTEUR,ETA_LARGEUR,ETA_PT_DEPART_TOP,ETA_PT_DEPART_LEFT,ETA_PT_ARRIVE_TOP,ETA_PT_ARRIVE_LEFT,ETA_PRIORITE) VALUES (?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($libelle,$hauteur,$largeur,$pt_depart_top,$pt_depart_left,$pt_arrive_top,$pt_arrive_left,$priorite))) {
			throw new Exception('Erreur durant l\'insertion d\'un(e) etage dans la base de donn�es');
		}
		
		// Construire l'etage
		return new Etage($pdo,$pdo->lastInsertId(),$libelle,$hauteur,$largeur,$pt_depart_top,$pt_depart_left,$pt_arrive_top,$pt_arrive_left,$priorite,$easyload);
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
		return $pdo->prepare('SELECT * FROM ETAGE e '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un etage
	 * @param $pdo PDO 
	 * @param $idetage int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etage 
	 */
	public static function load(PDO $pdo,$idetage,$easyload=true)
	{
		// D�j� charg�(e) ?
		if (isset(Etage::$easyload[$idetage])) {
			return Etage::$easyload[$idetage];
		}
		
		// Charger l'etage
		$pdoStatement = Etage::_select($pdo,'e.ID_ETAGE = ?');
		if (!$pdoStatement->execute(array($idetage))) {
			throw new Exception('Erreur lors du chargement d\'un(e) etage depuis la base de donn�es');
		}
		
		// R�cup�rer l'etage depuis le jeu de r�sultats
		return Etage::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous les etages
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etage[] tableau de etages
	 */
	public static function loadAll(PDO $pdo,$easyload=false)
	{
		// S�lectionner tous les etages
		$pdoStatement = Etage::selectAll($pdo);
		
		// Mettre chaque etage dans un tableau
		$etages = array();
		while ($etage = Etage::fetch($pdo,$pdoStatement,$easyload)) {
			$etages[] = $etage;
		}
		
		// Retourner le tableau
		return $etages;
	}
	
	/**
	 * S�lectionner tous les etages
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Etage::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les etages depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * R�cup�re l'etage suivant d'un jeu de r�sultats
	 * @param $pdo PDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Etage 
	 */
	public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idetage,$libelle,$hauteur,$largeur,$pt_depart_top,$pt_depart_left,$pt_arrive_top,$pt_arrive_left,$priorite) = $values;
		
		// Construire l'etage
		return isset(Etage::$easyload[$idetage]) ? Etage::$easyload[$idetage] :
		       new Etage($pdo,$idetage,$libelle,$hauteur,$largeur,$pt_depart_top,$pt_depart_left,$pt_arrive_top,$pt_arrive_left,$priorite,$easyload);
	}
	
	/**
	 * Supprimer l'etage
	 * @return bool op�ration r�ussie ?
	 */
	public function delete()
	{
		// Supprimer les obstacles associ�(e)s
		$select = $this->selectObstacles();
		while ($obstacle = Obstacle::fetch($this->pdo,$select)) {
			$obstacle->delete();
		}
		
		$zones = $this->selectZones();
		foreach($zones as $zone){
			$zone->delete(true);
		}
		
		// Supprimer l'etage
		$pdoStatement = $this->pdo->prepare('DELETE FROM ETAGE WHERE ID_ETAGE = ?');
		if (!$pdoStatement->execute(array($this->getIdetage()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) etage dans la base de donn�es');
		}
		
		// Op�ration r�ussie ?
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
     * Compter le nombre de etages
     * @param $pdo PDO 
     * @return int nombre de etages
     */
    public static function count(ownPDO $pdo) {
        if(!($pdoStatement = $pdo->query('SELECT COUNT(ID_ETAGE) FROM ETAGE'))) {
            throw new Exception('Erreur lors du comptage des etages dans la base de donn�es');
        }
        return $pdoStatement->fetchColumn();
    }

	/**
     * Retourne le premier etage existant
     * @param $pdo PDO 
     * @return int id du premier etage existant
     */
	
	public static function getFirstId(ownPDO $pdo) {
		$pdoStatement = Etage::_select($pdo, null, 'e.ID_ETAGE', 1);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors de la r�cup�ration du premier �tage');
		}
        return $pdoStatement->fetchColumn();
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
		$pdoStatement = $this->pdo->prepare('UPDATE ETAGE SET '.implode(', ', $updates).' WHERE ID_ETAGE = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdetage())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'un(e) etage dans la base de donn�es');
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
		return $this->_set(array('ETA_LIBELLE','ETA_HAUTEUR','ETA_LARGEUR','ETA_PT_DEPART_TOP','ETA_PT_DEPART_LEFT','ETA_PT_ARRIVE_TOP','ETA_PT_ARRIVE_LEFT','ETA_PRIORITE'),array($this->libelle,$this->hauteur,$this->largeur,$this->pt_depart_top,$this->pt_depart_left,$this->pt_arrive_top,$this->pt_arrive_left,$this->priorite));
	}
	
	/**
	 * R�cup�rer le idetage
	 * @return int 
	 */
	public function getIdetage()
	{
		return $this->idetage;
	}
	
	/**
	 * S�lectionner les obstacles
	 * @return PDOStatement 
	 */
	public function selectObstacles()
	{
		return Obstacle::selectByEtage($this->pdo,$this);
	}
	
	/**
	 * S�lectionner les zones
	 * @return le tableau de zones 
	 */
	public function selectZones()
	{
		$pdoStatement = Zone::selectByEtage($this->pdo,$this);
		// Mettre chaque etage dans un tableau
		$zones = array();
		while ($zone = Zone::fetch($this->pdo,$pdoStatement)) {
			$zones[] = $zone;
		}		
		// Retourner le tableau
		return $zones;
	}
	
	/**
	 * R�cup�rer le libelle
	 * @return string 
	 */
	public function getLibelle()
	{
		return $this->libelle;
	}
	
	/**
	 * D�finir le libelle
	 * @param $libelle string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setLibelle($libelle,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->libelle = $libelle;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ETA_LIBELLE'),array($libelle)) : true;
	}
	
	/**
	 * R�cup�rer la hauteur
	 * @return int 
	 */
	public function getHauteur()
	{
		return $this->hauteur;
	}
	
	/**
	 * D�finir la hauteur
	 * @param $hauteur int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setHauteur($hauteur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->hauteur = $hauteur;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ETA_HAUTEUR'),array($hauteur)) : true;
	}
	
	/**
	 * R�cup�rer la largeur
	 * @return int 
	 */
	public function getLargeur()
	{
		return $this->largeur;
	}
	
	/**
	 * D�finir la largeur
	 * @param $largeur int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setLargeur($largeur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->largeur = $largeur;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ETA_LARGEUR'),array($largeur)) : true;
	}
	
	/**
	 * R�cup�rer la position du point depart top
	 * @return int 
	 */
	public function getPtDepartTop()
	{
		return $this->pt_depart_top;
	}
	
	/**
	 * D�finir la  position du point depart top
	 * @param $pt_depart_top int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPtDepartTop($pt_depart_top,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->pt_depart_top = $pt_depart_top;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ETA_PT_DEPART_TOP'),array($pt_depart_top)) : true;
	}
	/**
	 * R�cup�rer la position du point depart left
	 * @return int 
	 */
	public function getPtDepartLeft()
	{
		return $this->pt_depart_left;
	}
	
	/**
	 * D�finir la  position du point depart left
	 * @param $pt_depart_left int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPtDepartLeft($pt_depart_left,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->pt_depart_left = $pt_depart_left;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ETA_PT_DEPART_LEFT'),array($pt_depart_left)) : true;
	}

	
	/**
	 * R�cup�rer la position du point arrive top
	 * @return int 
	 */
	public function getPtArriveTop()
	{
		return $this->pt_arrive_top;
	}
	
	/**
	 * D�finir la  position du point arrive top
	 * @param $pt_arrive_top int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPtArriveTop($pt_arrive_top,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->pt_arrive_top = $pt_arrive_top;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ETA_PT_ARRIVE_TOP'),array($pt_arrive_top)) : true;
	}
	/**
	 * R�cup�rer la position du point arrive left
	 * @return int 
	 */
	public function getPtArriveLeft()
	{
		return $this->pt_arrive_left;
	}
	
	/**
	 * D�finir la  position du point arrive left
	 * @param $pt_arrive_left int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPtArriveLeft($pt_arrive_left,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->pt_arrive_left = $pt_arrive_left;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ETA_PT_ARRIVE_LEFT'),array($pt_arrive_left)) : true;
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
		return $execute ? $this->_set(array('ETA_PRIORITE'),array($priorite)) : true;
	}
	
	/**
	 * Retourner les �tages par libelle
	 * @param $pdo PDO
	 * @param $libelle string
	 * @return $etages array
	 */
	public static function getEtageByLibelle(ownPDO $pdo, $libelle){
		
		$pdoStatement = $pdo->prepare('SELECT * FROM ETAGE e WHERE e.ETA_LIBELLE like ?');
		if (!$pdoStatement->execute(array('%' . $libelle . '%'))) {
			throw new Exception('Erreur lors du chargement de tous les �tages par libelle depuis la base de donn�es');
		}
		
		// Mettre chaque �tage dans un tableau
		$etages = array();
		while ($etage = Etage::fetch($pdo,$pdoStatement)) {
			$etages[] = $etage;
		}
		
		// Retourner le tableau
		return $etages;
	}
	
	/**
	 * Retourner zone par defaut (magasin) de letage
	 * @param $pdo PDO
	 * @return $zone object
	 */
	public function getZoneMagasin(){
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM ZONE WHERE  ID_ETAGE = ? AND ZON_LIBELLE LIKE "magasin"');
		if (!$pdoStatement->execute(array($this->idetage))) {
			throw new Exception('Erreur lors du chargement depuis la base de donn�es');
		}
		
		// Mettre chaque �tage dans un tableau 
		return Zone::fetch($this->pdo,$pdoStatement);
		
	}
	
	public static function getEtageByPreparation($pdo, $idPreparation){
		$pdoStatement = $pdo->prepare('	SELECT eta.ID_ETAGE,eta.ETA_LIBELLE,eta.ETA_HAUTEUR,eta.ETA_LARGEUR,eta.ETA_PT_DEPART_TOP,eta.ETA_PT_DEPART_LEFT,eta.ETA_PT_ARRIVE_TOP,eta.ETA_PT_ARRIVE_LEFT,eta.ETA_PRIORITE
										FROM PREPARATION pre, LIGNE_COMMANDE lc, PRODUIT p, EST_GEOLOCALISE_DANS egd, ETAGERE e, SEGMENT s, RAYON r, ZONE z, ETAGE eta 
										WHERE  pre.ID_PREPARATION = ? 
										AND pre.ID_PREPARATION = lc.ID_PREPARATION
										AND lc.ID_PRODUIT = p.ID_PRODUIT
										AND p.ID_PRODUIT = egd.ID_PRODUIT
										AND egd.ID_ETAGERE = e.ID_ETAGERE
										AND e.ID_SEGMENT = s.ID_SEGMENT
										AND s.ID_RAYON = r.ID_RAYON
										AND r.ID_ZONE = z.ID_ZONE
										AND z.ID_ETAGE = eta.ID_ETAGE
										GROUP BY eta.ID_ETAGE');
										
		if (!$pdoStatement->execute(array($idPreparation))) {
			throw new Exception('Erreur lors du chargement depuis la base de donn�es');
		}
		
		// Mettre chaque �tage dans un tableau
		$etages = array();
		while ($etage = Etage::fetch($pdo,$pdoStatement)) {
			$etages[] = $etage;
		}
		
		// Retourner le tableau
		return $etages;
	}
}
?>