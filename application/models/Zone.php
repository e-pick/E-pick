<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Zone.php
 *
 */
 
class Zone
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idZone;
		
	/// @var 
	private $idEtage;
	
	/// @var string 
	private $libelle;
	
	/// @vat string
	private $couleur;

	/// @var int
	private $priorite;
	
	/**
	 * Construire une zone
	 * @param $pdo ownPDO 
	 * @param $idZone int 
	 * @param $libelle string 
	 * @param $couleur string
	 * @param $priorite int
	 * @param $idEtage int id de idEtage
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Zone 
	 */
	protected function __construct(ownPDO $pdo,$idZone,$idEtage,$libelle,$couleur,$priorite=null,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idZone 			= $idZone;
		$this->libelle 			= $libelle;
		$this->couleur 			= $couleur;
		$this->idEtage 			= $idEtage;
		$this->priorite 		= $priorite;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Zone::$easyload[$idZone] = $this;
		}
	}
	
	/**
	 *
	 * Teste l'int�grit� des attributs de l'objet Zone
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
				return (is_string($libelle) && $libelle != null && $libelle != '' && $libelle != 'magasin');
				break;
				
			case 'couleur' :
				$couleur = $value;
				return (is_string($couleur) && (strlen($couleur)==6));
				break;
			
			case 'Priorite' :
				return (!is_string($value) && in_array($value, array(0,1,2,3)));
				break;
			
			case 'idEtage' :
				$idEtage = $value;
				return (!is_string($idEtage) && (Etage::load($pdo, $idEtage) != null));
				break;
				
			default:
				throw new Exception(gettext('L\'attribut ') . $attribut . gettext(' ne fait pas partie de l\'objet ') . gettext('Zone'),3);
				break;
		}
	}
	
	/**
     * Teste si le libelle est d�j� utilis�
     * @param $pdo ownPDO 
     * @param $libelle le libelle � tester
	 * @param $idEtage l'�tage dans lequel la zone se trouve
	 * @param $id l'idZone utilis� dans le cas d'une �dition 
     * @return true si le login est d�j� utilis�, false sinon
     */
	public static function libelleUsed(ownPDO $pdo, $libelle, $idEtage, $id=0){ 
 		
		if ($id == 0){
			$pdoStatement = $pdo->prepare(Zone::_select('z.ZON_LIBELLE = ? AND ID_ETAGE = ?'));
			if(!$pdoStatement->execute(array($libelle,$idEtage))) {
				throw new Exception('Erreur lors du test du login depuis la base de donn�es');
			} 
		}
		else {
			$pdoStatement = $pdo->prepare(Zone::_select('z.ZON_LIBELLE = ? AND ID_ETAGE = ? AND z.ID_ZONE != ?'));
			if(!$pdoStatement->execute(array($libelle,$idEtage, $id))) {
				throw new Exception('Erreur lors du test du login depuis la base de donn�es');
			} 
		} 
		
        return ($pdoStatement->rowCount() == 0) ? false : true;	
	}
	
	/**
	 * Cr�er une zone
	 * @param $pdo ownPDO 
	 * @param $idEtage Etage 
	 * @param $libelle string 
	 * @param $couleur string
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Zone 
	 */
	public static function create(ownPDO $pdo,$idEtage,$libelle,$couleur='000000',$priorite=null,$easyload=true)
	{
		// Ajouter la zone dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO ZONE (ID_ETAGE,ZON_LIBELLE,ZON_COULEUR,ZON_PRIORITE) VALUES (?,?,?,?)');
		if (!$pdoStatement->execute(array($idEtage,$libelle,$couleur,$priorite))) {
			throw new Exception('Erreur durant l\'insertion d\'une zone dans la base de donn�es');
		}
		
		// Construire la zone
		return new Zone($pdo,$pdo->lastInsertId(),$idEtage,$libelle,$couleur,$priorite,$easyload);
	}
	
	/**
	 * Requ�te de s�l�ction
	 * @param $pdo ownPDO 
	 * @param $where string 
	 * @param $orderby string 
	 * @param $limit string 
	 * @return PDOStatement 
	 */
	private static function _select($where=null,$orderby=null,$limit=null)
	{
		return 	'SELECT * FROM ZONE z '.
		        ($where != null ? ' WHERE '.$where : '').
		        ($orderby != null ? ' ORDER BY '.$orderby : '').
		        ($limit != null ? ' LIMIT '.$limit : '');
	}
	
	/**
	 * Charger une zone
	 * @param $pdo ownPDO 
	 * @param $idZone int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Zone 
	 */
	public static function load(ownPDO $pdo,$idZone,$easyload=true)
	{
		// D�j� charg�(e) ?
		if (isset(Zone::$easyload[$idZone])) {
			return Zone::$easyload[$idZone];
		}
		
		// Charger la zone
		$pdoStatement = $pdo->prepare(Zone::_select('z.ID_ZONE = ?'));
		if (!$pdoStatement->execute(array($idZone))) {
			throw new Exception('Erreur lors du chargement d\'une zone depuis la base de donn�es');
		}
		
		// R�cup�rer la zone depuis le jeu de r�sultats
		return Zone::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger toutes les zones
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Zone[] tableau de zones
	 */
	public static function loadAll(PDO $pdo,$easyload=false)
	{
		// S�lectionner toutes les zones
		$pdoStatement = Zone::selectAll($pdo);
		
		// Mettre chaque zone dans un tableau
		$zones = array();
		while ($zone = Zone::fetch($pdo,$pdoStatement,$easyload)) {
			$zones[] = $zone;
		}
		
		// Retourner le tableau
		return $zones;
	}
	
	/**
	 * Charger toutes les zones d'un �tage
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @param $idEtage int
	 * @return Zone[] tableau de zones de l'�tage
	 */
	public static function loadAllEtage(ownPDO $pdo, $idEtage,$easyload=false)
	{
		// S�lectionner toutes les zones de l'�tage
		$pdoStatement = $pdo->prepare(Zone::_select('z.ID_ETAGE = ?'));
		if (!$pdoStatement->execute(array($idEtage))) {
			throw new Exception('Erreur lors du chargement de toutes les zones de l\'�tage depuis la base de donn�es');
		}
		
		// Mettre chaque zone dans un tableau
		$zones = array();
		while ($zone = Zone::fetch($pdo,$pdoStatement,$easyload)) {
			$zones[] = $zone;
		}
		
		// Retourner le tableau
		return $zones;
	}
	
	/**
	 * Charger une zone d'un �tage
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @param $idEtage int
	 * @param $idZone int
	 * @return Zone[] tableau de zones de l'�tage
	 */
	public static function loadZoneMagasinByEtage(ownPDO $pdo, $idEtage, $easyload=false)
	{
		// S�lectionner toutes les zones de l'�tage
		$pdoStatement = $pdo->prepare(Zone::_select('z.ID_ETAGE = ? AND z.ZON_LIBELLE =\'magasin\''));
		if (!$pdoStatement->execute(array($idEtage))) {
			throw new Exception('Erreur lors du chargement de toutes les zones de l\'�tage depuis la base de donn�es');
		}

		// Retourner la zone
		return Zone::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * S�lectionner toutes les zones
	 * @param $pdo ownPDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = $pdo->prepare(Zone::_select());
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de toutes les zones depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * R�cup�re la zone suivante d'un jeu de r�sultats
	 * @param $pdo ownPDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Zone 
	 */
	public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idZone,$idEtage,$libelle,$couleur,$priorite) = $values;
		
		// Construire la zone
		return 	isset(Zone::$easyload[$idZone]) ? Zone::$easyload[$idZone] :
				new Zone($pdo,$idZone,$idEtage,$libelle,$couleur,$priorite,$easyload);
	}
	
	/**
	 * Supprimer la zone
	 * @return bool op�ration r�ussie ?
	 */
	public function delete($all = false)
	{
		$select = $this->selectRayons();
		if ($all){
			foreach($select as $rayon){
				$rayon->delete();
			}	
		}
		else{		
			// Affecte les rayons associ�s � la zone par d�faut		
			foreach($select as $rayon){
				$rayon->setZone(self::loadZoneMagasinByEtage($this->pdo, $this->getEtage()->getIdetage()));
			}
		}		
		// Suppression de la table des temps de pr�paration qu'il faudra recreer
		$pdoStatement = $this->pdo->prepare('DELETE FROM TEMPS_PREPARATION');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des TEMPS_PREPARATION dans la base de donn�es');
		}
				
		// Supprimer la zone
		$pdoStatement = $this->pdo->prepare('DELETE FROM ZONE WHERE ID_ZONE = ?');
		if (!$pdoStatement->execute(array($this->getIdzone()))) {
			throw new Exception('Erreur lors de la supression d\'une zone dans la base de donn�es');
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
		$pdoStatement = $this->pdo->prepare('UPDATE ZONE SET '.implode(', ', $updates).' WHERE ID_ZONE = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdzone())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'une zone dans la base de donn�es');
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
		return $this->_set(array('ID_ETAGE','ZON_LIBELLE','ZON_COULEUR','ZON_PRIORITE'),array($this->idEtage,$this->libelle,$this->couleur,$this->priorite));
	}
	
	/**
	 * R�cup�rer la idZone
	 * @return int 
	 */
	public function getIdzone()
	{
		return $this->idZone;
	}
	
	/**
	 * S�lectionner les rayons
	 * @return le tableau de rayons de la zone 
	 */
	public function selectRayons()
	{
		return Rayon::selectByZone($this->pdo,$this);
	}
	
	/**
	 * R�cup�rer la libelle
	 * @return string 
	 */
	public function getLibelle()
	{
		return $this->libelle;
	}
	
	/**
	 * D�finir la libelle
	 * @param $libelle string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setLibelle($libelle,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->libelle = $libelle;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ZON_LIBELLE'),array($libelle)) : true;
	}
	
	
	/**
	 * R�cup�rer la couleur
	 * @return Couleur
	 */
	public function getCouleur()
	{
		return $this->couleur;
	}
	
	/**
	 * D�finir la couleur
	 * @param $couleur string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setCouleur($couleur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->couleur = $couleur;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ZON_COULEUR'),array($couleur)) : true;
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
		return $execute ? $this->_set(array('ZON_PRIORITE'),array($priorite)) : true;
	}
	
	/**
	 * R�cup�rer la idEtage
	 * @return Etage 
	 */
	public function getEtage()
	{
		// Retourner null si n�c�ssaire
		if ($this->idEtage == null) { return null; }
		
		// Charger et retourner idEtage
		return Etage::load($this->pdo,$this->idEtage);
	}
	
	/**
	 * D�finir la idEtage
	 * @param $idEtage Etage 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setEtage($idEtage=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idEtage = $idEtage == null ? null : $idEtage->getIdidEtage();
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_ETAGE'),array($idEtage == null ? null : $idEtage->getIdidEtage())) : true;
	}
	
	/**
	 * S�lectionner les zones par idEtage
	 * @param $pdo ownPDO 
	 * @param $idEtage Etage 
	 * @return PDOStatement 
	 */
	public static function selectByEtage(ownPDO $pdo,Etage $idEtage)
	{
		$pdoStatement = $pdo->prepare('SELECT z.ID_ZONE, z.ID_ETAGE, z.ZON_LIBELLE, z.ZON_COULEUR, z.ZON_PRIORITE FROM ZONE z WHERE z.ID_ETAGE = ?');
		if (!$pdoStatement->execute(array($idEtage->getIdetage()))) {
			throw new Exception('Erreur lors du chargement de toutes les zones par idEtage depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * Retourner les zones par libelle
	 * @param $pdo PDO
	 * @param $libelle string
	 * @param $idEtage string
	 * @return $zones array
	 */
	public static function getZoneByLibelleAndEtage(ownPDO $pdo, $libelle, $idEtage){
		
		$pdoStatement = $pdo->prepare('SELECT * FROM ZONE z WHERE ID_ETAGE = ? AND z.ZON_LIBELLE like ?');
		if (!$pdoStatement->execute(array($idEtage, '%' . $libelle . '%'))) {
			throw new Exception('Erreur lors du chargement de tous les rayons par libelle depuis la base de donn�es');
		}
		
		// Mettre chaque rayon dans un tableau
		$zones = array();
		while ($zone = Zone::fetch($pdo,$pdoStatement)) {
			$zones[] = $zone;
		}
		
		// Retourner le tableau
		return $zones;
	}
	
	public function __toString(){
	
		return $this -> idZone;
	
	}
	
}
?>