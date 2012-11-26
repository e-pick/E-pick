<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Geolocalisation.php
 *
 */
 
class Geolocalisation
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idgeolocalisation;
		
	/// @var objet produit
	private $produit;
	
	/// @var objet etagere 
	private $etagere;




	/**
	 * Construire une geolocalisation
	 * @param $pdo PDO 
	 * @param $idetagere int 
	 * @param $idproduit int  
	 * @param $easyload bool activer le chargement rapide ?
	 * @return geolocalisation
	 */
	protected function __construct(PDO $pdo,$idgeolocalisation,$produit,$etagere,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idgeolocalisation = $idgeolocalisation; 
		$this->produit = $produit;
		$this->etagere = $etagere; 
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Geolocalisation::$easyload[$idgeolocalisation]= $this;
		}
	}
	

	/**
	 * Cr�er une g�olocalisation
	 * @param $pdo PDO  
	 * @param $etagere 
	 * @param $produit 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return geolocalisation 
	 */
	public static function create(PDO $pdo,$produit,$etagere,$easyload=true)
	{
		// Ajouter l'etagere dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO EST_GEOLOCALISE_DANS (ID_PRODUIT,ID_ETAGERE) VALUES (?,?)');
		if (!$pdoStatement->execute(array($produit->getIdProduit(),$etagere->getIdetagere()))) {
			throw new Exception('Erreur durant l\'insertion d\'un(e) geolocalisation dans la base de donn�es');
		}
		
		// Construire l'etagere
		return new Geolocalisation($pdo,$pdo->lastInsertId(),$produit->getIdProduit(),$etagere->getIdetagere(),$easyload);
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
		return $pdo->prepare('SELECT * FROM EST_GEOLOCALISE_DANS '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}

	/**
	 * Charger une geolocalisation
	 * @param $pdo PDO 
	 * @param $idgeolocalisation int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Geolocalisation
	 */
	public static function load(PDO $pdo,$idgeolocalisation,$easyload=true)
	{
		// D�j� charg�e ?
		if (isset(Geolocalisation::$easyload[$idgeolocalisation])) {
			return Geolocalisation::$easyload[$idgeolocalisation];
		}
		
		// Charger la g�olocalisation
		$pdoStatement = Geolocalisation::_select($pdo,'ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($idProduit))) {
			throw new Exception('Erreur lors du chargement d\'une geoloc depuis la base de donn�es');
		}
		
		// R�cup�rer l'etagere depuis le jeu de r�sultats
		return Geolocalisation::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger une geolocalisation par produit
	 * @param $pdo PDO 
	 * @param $idProduit int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Geolocalisation
	 */
	public static function loadByProduit(PDO $pdo,$idProduit,$easyload=true)
	{
		// Charger la g�olocalisation
		$pdoStatement = Geolocalisation::_select($pdo,'ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($idProduit))) {
			throw new Exception('Erreur lors du chargement d\'une geoloc depuis la base de donn�es');
		}
		
		// Mettre chaque geolocalisation dans un tableau
		$geolocalisations = array();
		while ($geolocalisation = Geolocalisation::fetch($pdo,$pdoStatement,$easyload)) {
			$geolocalisations[] = $geolocalisation;
		}
		
		// Retourner le tableau
		return $geolocalisations;
	}
	
	/**
	 * Charger une geolocalisation par etagere
	 * @param $pdo PDO 
	 * @param $idEtagere int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Geolocalisation
	 */
	public static function loadByEtagere(PDO $pdo,$etagere,$easyload=true)
	{
		// Charger la g�olocalisation
		$pdoStatement = Geolocalisation::_select($pdo,'ID_ETAGERE = ?');
		if (!$pdoStatement->execute(array($etagere->getIdetagere()))) {
			throw new Exception('Erreur lors du chargement d\'une geoloc depuis la base de donn�es');
		}
		
		// Mettre chaque geolocalisation dans un tableau
		$geolocalisations = array();
		while ($geolocalisation = Geolocalisation::fetch($pdo,$pdoStatement,$easyload)) {
			$geolocalisations[] = $geolocalisation;
		}
		
		// Retourner le tableau
		return $geolocalisations;
	}
	
	/**
	 * Charger toutes les geolocalisations
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return geolocalisation[] tableau de geolocalisation
	 */
	public static function loadAll(PDO $pdo,$easyload=false)
	{
		// S�lectionner tous/toutes les etageres
		$pdoStatement = Geolocalisation::selectAll($pdo);
		
		// Mettre chaque geolocalisation dans un tableau
		$geolocalisations = array();
		while ($geolocalisation = Geolocalisation::fetch($pdo,$pdoStatement,$easyload)) {
			$geolocalisations[] = $geolocalisation;
		}
		
		// Retourner le tableau
		return $geolocalisations;
	}
	
	/**
	 * Charger toutes les geolocalisations
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return geolocalisation[] tableau de geolocalisation
	 */
	public static function loadByEtagereAndProduit(PDO $pdo, $idEtagere, $idProduit, $easyload=false)
	{
		// S�lectionner tous/toutes les etageres
		$pdoStatement = Geolocalisation::_select($pdo,'ID_ETAGERE = ? AND ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($idEtagere, $idProduit))) {
			throw new Exception('Erreur lors du chargement d\'une geolocalisation par produit et �tag�re depuis la base de donn�es');
		}
		
		// Mettre chaque geolocalisation dans un tableau
		$geolocalisations = array();
		while ($geolocalisation = Geolocalisation::fetch($pdo,$pdoStatement,$easyload)) {
			$geolocalisations[] = $geolocalisation;
		}
		
		// Retourner le tableau
		return $geolocalisations[0];
	}
	
	/**
	 * S�lectionner toutes les geolocalisations
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Geolocalisation::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les g�olocalisations depuis la base de donn�es');
		}
		return $pdoStatement;
	}

	/**
	 * R�cup�re la g�olocalisation suivante d'un jeu de r�sultats
	 * @param $pdo PDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Geolocalisation
	 */
	public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idgeolocalisation,$produit,$etagere) = $values;
		
		// Construire l'etagere
		return isset(Geolocalisation::$easyload[$idgeolocalisation]) ? Geolocalisation::$easyload[$idgeolocalisation] :
		       new Geolocalisation($pdo,$idgeolocalisation,$produit,$etagere,$easyload);
	}
	
	/**
	 * Supprimer la g�olocalisation
	 * @return bool op�ration r�ussie ?
	 */
	public function delete()
	{
		$pdoStatement = $this->pdo->prepare('DELETE FROM EST_GEOLOCALISE_DANS WHERE ID_GEOLOCALISATION = ?');
		if (!$pdoStatement->execute(array($this->getIdgeolocalisation()))) {
			throw new Exception('Erreur lors de la supression d\'une g�olocalisation dans la base de donn�es');
		}
		
		// Op�ration r�ussie ?
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * V�rifie si une g�olocalisation existe dans la table est_geolocalise_dans
	 * @param $pdo PDO
	 * @param $idProduit 
	 * @param $idEtagere
	 * @return boolean, true si la g�olocalisation existe, false sinon
	 */
	public static function exists(PDO $pdo, $idProduit, $idEtagere){
		
		$pdoStatement = Geolocalisation::_select($pdo, 'ID_PRODUIT = ? AND ID_ETAGERE = ?');
		if (!$pdoStatement->execute(array($idProduit,$idEtagere))) {
			throw new Exception('Erreur lors de la v�rification de l\'existance de la g�olocalisation depuis la base de donn�es');
		}
		
		return (Geolocalisation::fetch($pdo,$pdoStatement) != null);
		
	}
	
	/**
	 * V�rifie si un produit est g�olocalis�
	 * @param $pdo PDO
	 * @param $idProduit 
	 * @return boolean, true si le produit est g�olocalis�, false sinon
	 */
	public static function isGeolocalized(PDO $pdo, $idProduit){
		
		$pdoStatement = Geolocalisation::_select($pdo, 'ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($idProduit))) {
			throw new Exception('Erreur lors de la v�rification de l\'existance de la g�olocalisation depuis la base de donn�es');
		}
		
		return (Geolocalisation::fetch($pdo,$pdoStatement) != null);
		
	}
	
	/**
	 * Supprime toute la g�olocalisation
	 * @param $pdo PDO
	 * @return boolean, true si l'op�ration a r�ussie, false sinon
	 */
	public static function emptying(PDO $pdo){
		$pdoStatement = $pdo->prepare('DELETE FROM EST_GEOLOCALISE_DANS');
 		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage de l\'existante g�olocalisation dans la base de donn�es');
		}
		
		$pdoStatement = $pdo->prepare('DELETE FROM ETAGERE');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage de l\'existante g�olocalisation dans la base de donn�es');
		}
		
		$pdoStatement = $pdo->prepare('DELETE FROM SEGMENT');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage de l\'existante g�olocalisation dans la base de donn�es');
		}
		
		$pdoStatement = $pdo->prepare('DELETE FROM RAYON');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage de l\'existante g�olocalisation dans la base de donn�es');
		}
		$pdoStatement = $pdo->prepare('DELETE FROM OBSTACLE');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage de l\'existante g�olocalisation dans la base de donn�es');
		}
		return true;
	}
	
	/**
	 * R�cup�rer le idgeolocalisation
	 * @return int 
	 */
	public function getIdgeolocalisation()
	{
		return $this->idgeolocalisation;
	}
	
	/**
	 * R�cup�rer l'�tagere
	 * @return int 
	 */
	public function getIdetagere()
	{
		return $this->etagere;
	}
	
	/**
	 * D�finir l'�tag�re
	 * @param $etagere Etagere 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setIdetagere($etagere,$execute=true)
	{	
		// Sauvegarder dans l'objet
		$this->etagere = $etagere == null ? null : $etagere->getIdetagere();
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_ETAGERE'),array($etagere == null ? null : $etagere->getIdetagere())) : true;
	}
	
	/**
	 * R�cup�rer l'�tagere
	 * @return int 
	 */
	public function getIdproduit()
	{
		return $this->produit;
	}
	
	/**
	 * D�finir le produit
	 * @param $produit Produit 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setIdproduit($produit,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->produit = $produit == null ? null : $produit->getIdProduit();
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_PRODUIT'),array($produit == null ? null : $produit->getIdProduit())) : true;
	}
	
	
	/**
	 * Mettre � jour tous les champs dans la base de donn�es
	 * @return bool op�ration r�ussie ?
	 */
	public function update()
	{
		return $this->_set(array('ID_ETAGERE','ID_PRODUIT'),array($this->etagere,$this->produit));
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
		$pdoStatement = $this->pdo->prepare('UPDATE EST_GEOLOCALISE_DANS SET '.implode(', ', $updates).' WHERE ID_GEOLOCALISATION = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdgeolocalisation())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'une geolocalisation dans la base de donn�es');
		}
		
		// Op�ration r�ussie ?
		return $pdoStatement->rowCount() == 1;
	}
}
?>