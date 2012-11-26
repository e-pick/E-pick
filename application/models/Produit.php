<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Produit.php
 *
 */
 
class Produit
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idProduit;
	
	/// @var string
	private $codeProduit;
	
	/// @var string 
	private $libelle;
	
	/// @var string 
	private $photo;
	
	/// @var int 
	private $largeur;
	
	/// @var int 
	private $hauteur;
	
	/// @var int 
	private $profondeur;
	
	/// @var string 
	private $uniteMesure;
	
	/// @var string 
	private $quantiteParUniteMesure;
	
	/// @var string 
	private $poidsBrut;
	
	/// @var string 
	private $poidsNet;
	
	/// @var int
	private $estPoidsVariable;
	
	/// @var int
	private $priorite;
	
	/// @var int
	private $stock;
	
	/// @var int
	private $tempsMoyenAccess;
	

	
	/**
	 * Construire un produit
	 * @param $pdo ownPDO 
	 * @param $idProduit int  
	 * @param $codeProduit string  
	 * @param $libelle string 
	 * @param $photo string 
	 * @param $largeur int 
	 * @param $hauteur int 
	 * @param $profondeur int 
	 * @param $uniteMesure string 
	 * @param $quantiteParUniteMesure string 
	 * @param $poidsBrut string
	 * @param $poidsNet string
	 * @param $estPoidsVariable int
	 * @param $priorite int
	 * @param $stock int
	 * @param $tempsMoyenAccess int
	 * @return Produit 
	 */
	public function __construct(ownPDO $pdo,$idProduit,$codeProduit,$libelle,$photo,$largeur,$hauteur,$profondeur,$uniteMesure,$quantiteParUniteMesure,$poidsBrut,$poidsNet,$estPoidsVariable,$priorite,$stock,$tempsMoyenAccess,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idProduit 				= $idProduit;
		$this->codeProduit 				= $codeProduit;
		$this->libelle 					= $libelle;
		$this->photo 					= $photo;
		$this->largeur 					= $largeur;
		$this->hauteur 					= $hauteur;
		$this->profondeur 				= $profondeur;
		$this->uniteMesure 				= $uniteMesure;
		$this->quantiteParUniteMesure 	= $quantiteParUniteMesure;
		$this->poidsBrut 				= $poidsBrut;
		$this->poidsNet 				= $poidsNet;
		$this->estPoidsVariable 		= $estPoidsVariable;
		$this->priorite					= $priorite;
		$this->stock					= $stock;
		$this->tempsMoyenAccess			= $tempsMoyenAccess;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Produit::$easyload[$idProduit] = $this;
		}
	}
	
	/**
	 * Cr�er un produit
	 * @param $pdo ownPDO 
	 * @param $idProduit int 
	 * @param $codeProduit string 
	 * @param $libelle string 
	 * @param $photo string 
	 * @param $largeur int 
	 * @param $hauteur int 
	 * @param $profondeur int 
	 * @param $uniteMesure string 
	 * @param $quantiteParUniteMesure string 
	 * @param $poidsBrut string
	 * @param $poidsNet string
	 * @param $estPoidsVariable int
	 * @param $priorite int
	 * @param $stock int
	 * @param $tempsMoyenAccess int
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Produit 
	 */
	public static function create(ownPDO $pdo,$codeProduit,$libelle,$photo,$largeur,$hauteur,$profondeur,$uniteMesure,$quantiteParUniteMesure,$poidsBrut,$poidsNet,$estPoidsVariable,$priorite,$stock=null,$tempsMoyenAccess=30,$easyload=true)
	{
		// Ajouter le produit dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO PRODUIT (PRO_CODE_PRODUIT,PRO_LIBELLE,PRO_PHOTO,PRO_LARGEUR,PRO_HAUTEUR,PRO_PROFONDEUR,PRO_UNITE_MESURE,PRO_QTE_PAR_UNITE_DE_MESURE,PRO_POIDS_BRUT,PRO_POIDS_NET,PRO_EST_POIDS_VARIABLE,PRO_PRIORITE,PRO_STOCK,PRO_TEMPS_MOYEN_ACCESS) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($codeProduit,$libelle,$photo,$largeur,$hauteur,$profondeur,$uniteMesure,$quantiteParUniteMesure,$poidsBrut,$poidsNet,$estPoidsVariable,$priorite,$stock,$tempsMoyenAccess))) {
			throw new Exception('Erreur durant l\'insertion d\'un produit dans la base de donn�es');
		}
		
		// Construire le produit
		return new Produit($pdo,$pdo->lastInsertId(),$codeProduit,$libelle,$photo,$largeur,$hauteur,$profondeur,$uniteMesure,$quantiteParUniteMesure,$poidsBrut,$poidsNet,$estPoidsVariable,$priorite,$stock,$tempsMoyenAccess,$easyload);
	}
	
	/**
	 *
	 * Teste l'int�grit� des attributs de l'objet Produit
	 * @param $attribute le nom de l'attribut
	 * @param $value la valeur � tester
	 * @return boolean => true si ok, false sinon
	 *
	 */
	public static function testIntegrite($attribute, $value){
		switch ($attribute) {
			case 'Code produit':
				$pdo = DB :: getInstance();
				/* Test l'existance du code produit choisi dans la base de donn�es */
				$pdoStatement = Produit::_select($pdo, 'PRO_CODE_PRODUIT = ?');
				if (!$pdoStatement->execute(array($value))) {
					throw new Exception('Erreur lors de la v�rification de l\'existance du code produit depuis la base de donn�es');
				}
				
				return ($value != null && $value != '' && Produit::fetch($pdo,$pdoStatement) == null);
				break;
				
			case 'Libelle' :
				return (is_string($value) && $value != null && $value != '');
				break;
				
			case 'Largeur' :
			case 'Hauteur' :
			case 'Profondeur' :
			case 'Quantite par unite de mesure' :
			case 'Poids brut' :
			case 'Poids net' :
			case 'Stock' :
				if($value != null || $value != ''){
					$valeur = floatval($value);
					return (preg_match("/^([0-9]*[\.0-9]*)$/",$value) && is_float($valeur) && $valeur >= 0);
					}
				else 
					return true;
				break;
				
			case 'Unite de mesure' :
				if($value != null)
					return (is_string($value));
				else 
					return true;
				break;
				
			case 'Est poids variable' :
				return (!is_string($value) && in_array($value, array(0,1)));
				break;
				
			case 'Priorite' :
				return (!is_string($value) && in_array($value, array(0,1,2,3)));
				break;
			case 'Temps Moyen Access':
				return (is_int($value));
				break;				
			default:
				throw new Exception(gettext('L\'attribut ') . $attribute . gettext(' ne fait pas partie de l\'objet ') . gettext('Utilisateur'),3);
		}
	}
	
	/**
	 * Requ�te de s�l�ction
	 * @param $pdo ownPDO 
	 * @param $where string 
	 * @param $orderby string 
	 * @param $limit string 
	 * @return PDOStatement 
	 */
	private static function _select(ownPDO $pdo,$where=null,$orderby=null,$limit=null,$groupby=null)
	{
		return $pdo->prepare('SELECT * FROM PRODUIT p '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($groupby != null ? ' GROUP BY '.$groupby : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un produit
	 * @param $pdo ownPDO 
	 * @param $idProduit string 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Produit 
	 */
	public static function load(ownPDO $pdo,$idProduit,$easyload=true)
	{ 
		// die(($idProduit));
		// D�j� charg� ?
		// if (isset(Produit::$easyload[$idProduit])) {
			// return Produit::$easyload[$idProduit];
		// }
		
		// Charger le produit
		$pdoStatement = Produit::_select($pdo,'ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($idProduit))) {
			throw new Exception('Erreur lors du chargement d\'un produit depuis la base de donn�es');
		}
		
		// R�cup�rer le produit depuis le jeu de r�sultats
		return Produit::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger un produit par codeProduit
	 * @param $pdo ownPDO 
	 * @param $codeProduit string 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Produit 
	 */
	public static function loadByCodeProduit(ownPDO $pdo,$codeProduit,$easyload=true)
	{
		 
		// Charger le produit
		$pdoStatement = Produit::_select($pdo,'p.PRO_CODE_PRODUIT = ?');
		if (!$pdoStatement->execute(array($codeProduit))) {
			throw new Exception('Erreur lors du chargement d\'un produit depuis la base de donn�es');
		}
		
		// R�cup�rer le produit depuis le jeu de r�sultats
		return Produit::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous les produits
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Produit[] tableau de produits
	 */
	public static function loadAll(ownPDO $pdo,$first=null, $etageFilter=null, $zoneFilter=null, $rayonFilter=null, $segmentFilter=null, $etagereFilter=null, $libelleFilter=null, $codeFilter=null, $eanFilter=null, $type=null, $orderby=null, $easyload=false)
	{
		$select		= 'SELECT p.ID_PRODUIT,p.PRO_CODE_PRODUIT,p.PRO_LIBELLE,p.PRO_PHOTO,p.PRO_LARGEUR,p.PRO_HAUTEUR,p.PRO_PROFONDEUR,p.PRO_UNITE_MESURE,p.PRO_QTE_PAR_UNITE_DE_MESURE,p.PRO_POIDS_BRUT,p.PRO_POIDS_NET,p.PRO_EST_POIDS_VARIABLE,p.PRO_PRIORITE,p.PRO_STOCK,p.PRO_TEMPS_MOYEN_ACCESS FROM ';
		$tables 	= array();
		$where 		= '';
		$limit		= '';
		$array_attr = array();
		
		if($etageFilter != null){
			$tables 	= array('PRODUIT p', 'EST_GEOLOCALISE_DANS egd', 'ETAGERE e', 'SEGMENT s', 'RAYON r', 'ZONE z');
			$where 		= 'p.ID_PRODUIT = egd.ID_PRODUIT AND egd.ID_ETAGERE = e.ID_ETAGERE AND e.ID_SEGMENT = s.ID_SEGMENT AND s.ID_RAYON = r.ID_RAYON AND r.ID_ZONE = z.ID_ZONE AND z.ID_ETAGE = ? AND ';
			$array_attr	= array($etageFilter);
		} 
		
		if($zoneFilter != null){
			$tables 	= array('PRODUIT p', 'EST_GEOLOCALISE_DANS egd', 'ETAGERE e', 'SEGMENT s', 'RAYON r');
			$where 		= 'p.ID_PRODUIT = egd.ID_PRODUIT AND egd.ID_ETAGERE = e.ID_ETAGERE AND e.ID_SEGMENT = s.ID_SEGMENT AND s.ID_RAYON = r.ID_RAYON AND r.ID_ZONE = ? AND ';
			$array_attr	= array($zoneFilter);
		}
		
		if($rayonFilter != null){
			$tables 	= array('PRODUIT p', 'EST_GEOLOCALISE_DANS egd', 'ETAGERE e', 'SEGMENT s');
			$where 		= 'p.ID_PRODUIT = egd.ID_PRODUIT AND egd.ID_ETAGERE = e.ID_ETAGERE AND e.ID_SEGMENT = s.ID_SEGMENT AND s.ID_RAYON = ? AND ';
			$array_attr	= array($rayonFilter) ;
		}
		
		if($segmentFilter != null){
			$tables		= array('PRODUIT p', 'EST_GEOLOCALISE_DANS egd','ETAGERE e ');
			$where 		= 'p.ID_PRODUIT = egd.ID_PRODUIT AND egd.ID_ETAGERE = e.ID_ETAGERE AND e.ID_SEGMENT = ? AND ';
			$array_attr	= array($segmentFilter) ;
		}
		
		if($etagereFilter != null){
			$tables		= array('PRODUIT p','EST_GEOLOCALISE_DANS egd');
			$where 		= 'p.ID_PRODUIT = egd.ID_PRODUIT AND egd.ID_ETAGERE = ? AND ';
			$array_attr	= array($etagereFilter) ;
		}
		
		if($libelleFilter != null){
			if (!in_array('PRODUIT p', $tables))
				$tables = array('PRODUIT p');
			$where 		   .= 'p.PRO_LIBELLE LIKE ? AND ';
			$array_attr[]	= '%' . html_entities($libelleFilter,false) . '%'; 
		} 
		
		if($codeFilter != null){
			if (!in_array('PRODUIT p', $tables))
				$tables = array('PRODUIT p');
			$where 		   .= 'p.PRO_CODE_PRODUIT LIKE ? AND ';
			$array_attr[]	= '%' . $codeFilter . '%';
		}
		
		if($eanFilter != null){
			if (in_array('PRODUIT p', $tables))
				$tables[] = 'EAN ea';
			else 
				$tables = array('PRODUIT p', 'EAN ea');
			$where 		   .= 'p.ID_PRODUIT = ea.ID_PRODUIT AND ea.EAN_EAN LIKE ? AND ';
			$array_attr[]	= '%' . $eanFilter . '%';
		}
		
		if ($type != null){
			switch($type){
				case 'nonGeoloc':
					if (!in_array('PRODUIT p', $tables))
						$tables = array('PRODUIT p');
					$where .= 'NOT EXISTS (SELECT * FROM EST_GEOLOCALISE_DANS egd WHERE p.ID_PRODUIT = egd.ID_PRODUIT) AND ';
 					break;
				case 'geoloc':
					if (!in_array('PRODUIT p', $tables))
						$tables = array('PRODUIT p');
					$where .= ' EXISTS (SELECT * FROM EST_GEOLOCALISE_DANS egd WHERE p.ID_PRODUIT = egd.ID_PRODUIT) AND ';
 					break;
					
				case 'inconnu':
					if (!in_array('PRODUIT p', $tables))
						$tables = array('PRODUIT p');
					$where 		   .= 'p.PRO_LIBELLE LIKE ? AND ';
					$array_attr[]	= '%Produit inconnu%';
					break;
					
				case 'connu':
					if (!in_array('PRODUIT p', $tables))
						$tables = array('PRODUIT p');
					$where 		   .= 'p.PRO_LIBELLE != ? AND ';
					$array_attr[]	= 'Produit inconnu';
					break;
				
				case 'sansean':
					if (!in_array('PRODUIT p', $tables))
						$tables = array('PRODUIT p');
					$where .= 'NOT EXISTS (SELECT * FROM EAN e WHERE p.ID_PRODUIT = e.ID_PRODUIT) AND ';
					break;
				
				default:
					break;
			}
		}
		
		if($first != null){
			$limit =  $first.','.RESULTAT_PAR_PAGE;
		}
		
			
		if($where != ''){
			$where .= ' 1=1 ';
			
			if($limit != '')
				$pdoStatement = $pdo->prepare($select . implode(', ', $tables) . ' WHERE ' . $where . ' GROUP BY p.ID_PRODUIT LIMIT ' . $limit);
			else
				$pdoStatement = $pdo->prepare($select . implode(', ', $tables) . ' WHERE ' . $where . ' GROUP BY p.ID_PRODUIT');
			if (!$pdoStatement->execute($array_attr))
				throw new Exception('Erreur lors du chargement de tous les produits depuis la base de donn�es');
		}
		else{
			if($limit != '')
				$pdoStatement = Produit::_select($pdo,null,$orderby,$limit,'p.ID_PRODUIT');
			else
				$pdoStatement = Produit::_select($pdo,null,$orderby,null,'p.ID_PRODUIT');
			if (!$pdoStatement->execute())
				throw new Exception('Erreur lors du chargement de tous les produits depuis la base de donn�es');	
		}
		
		// Mettre chaque produit dans un tableau
		$produits = array();
		while ($produit = Produit::fetch($pdo,$pdoStatement,$easyload)) {
			$produits[] = $produit;
		}
		
		// Retourner le tableau
		return $produits;
	}
	
	/**
	 * S�lectionner tous les produits
	 * @param $pdo ownPDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(ownPDO $pdo)
	{
		$pdoStatement = Produit::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous les produits depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * R�cup�re le produit suivant d'un jeu de r�sultats
	 * @param $pdo ownPDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Produit 
	 */
	public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idProduit,$codeProduit,$libelle,$photo,$largeur,$hauteur,$profondeur,$uniteMesure,$quantiteParUniteMesure,$poidsBrut,$poidsNet,$estPoidsVariable,$priorite,$stock,$tempsMoyenAccess) = $values;
		
		// Construire le/la produit
		return isset(Produit::$easyload[$idProduit]) ? Produit::$easyload[$idProduit] :
		       new Produit($pdo,$idProduit,$codeProduit,$libelle,$photo,$largeur,$hauteur,$profondeur,$uniteMesure,$quantiteParUniteMesure,$poidsBrut,$poidsNet,$estPoidsVariable,$priorite,$stock,$tempsMoyenAccess,$easyload);
	}
	
	/**
	 * Supprimer le produit
	 * @return bool op�ration r�ussie ?
	 */
	public function delete()
	{
		// Supprimer les etageres associ�es
		$pdoStatement = $this->pdo->prepare('DELETE FROM EST_GEOLOCALISE_DANS WHERE ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($this->getIdProduit()))) { return false; }
		
		// Supprimer les eans associ�es
		$eans = $this->selectEans();
		foreach ($eans as $ean) {
			if (!$ean->delete()) { return false; }
		}
		
		// Supprimer le produit
		$pdoStatement = $this->pdo->prepare('DELETE FROM PRODUIT WHERE ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($this->getIdProduit()))) {
			throw new Exception('Erreur lors de la supression d\'un	 produit dans la base de donn�es');
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
		$pdoStatement = $this->pdo->prepare('UPDATE PRODUIT SET '.implode(', ', $updates).' WHERE ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdProduit())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'un produit dans la base de donn�es');
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
		return $this->_set(array('PRO_CODE_PRODUIT','PRO_LIBELLE','PRO_PHOTO','PRO_LARGEUR','PRO_HAUTEUR','PRO_PROFONDEUR','PRO_UNITE_MESURE','PRO_QTE_PAR_UNITE_DE_MESURE','PRO_POIDS_BRUT','PRO_POIDS_NET','PRO_EST_POIDS_VARIABLE','PRO_PRIORITE','PRO_STOCK','PRO_TEMPS_MOYEN_ACCESS'),
						   array($this->codeProduit,$this->libelle,$this->photo,$this->largeur,$this->hauteur,$this->profondeur,$this->uniteMesure,$this->quantiteParUniteMesure,$this->poidsBrut,$this->poidsNet,$this->estPoidsVariable,$this->priorite,$this->stock,$this->tempsMoyenAccess));
	}
	
	public function __toString(){
		return '[' . $this->getIdProduit() . ']';
	}
	
	public function equals(Produit $produit){
		return ($this->__toString() == $produit->__toString());
	}
	
	/**
	 * R�cup�rer le idProduit
	 * @return int 
	 */
	public function getIdProduit()
	{
		return $this->idProduit;
	}
	
	/**
	 * R�cup�rer le codeProduit
	 * @return int 
	 */
	public function getCodeProduit()
	{
		return $this->codeProduit;
	}
	
	/**
	 * D�finir le codeProduit
	 * @param $codeProduit string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setCodeProduit($codeProduit,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codeProduit = $codeProduit;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_CODE_PRODUIT'),array($codeProduit)) : true;
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
		return $execute ? $this->_set(array('PRO_LIBELLE'),array($libelle)) : true;
	}
	
	/**
	 * R�cup�rer la photo
	 * @return string 
	 */
	public function getPhoto()
	{
		return $this->photo;
	}
	
	/**
	 * D�finir la photo
	 * @param $photo string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPhoto($photo,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->photo = $photo;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_PHOTO'),array($photo)) : true;
	}
	
	/**
	 * R�cup�rer la largeur
	 * @return string 
	 */
	public function getLargeur()
	{
		return $this->largeur;
	}
	
	/**
	 * D�finir la largeur
	 * @param $largeur string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setLargeur($largeur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->largeur = $largeur;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_LARGEUR'),array($largeur)) : true;
	}
	
	/**
	 * R�cup�rer la hauteur
	 * @return string 
	 */
	public function getHauteur()
	{
		return $this->hauteur;
	}
	
	/**
	 * D�finir la hauteur
	 * @param $hauteur string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setHauteur($hauteur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->hauteur = $hauteur;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_HAUTEUR'),array($hauteur)) : true;
	}
	
	/**
	 * R�cup�rer la profondeur
	 * @return string 
	 */
	public function getProfondeur()
	{
		return $this->profondeur;
	}
	
	/**
	 * D�finir la profondeur
	 * @param $profondeur string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setProfondeur($profondeur,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->profondeur = $profondeur;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_PROFONDEUR'),array($profondeur)) : true;
	}
	
	/**
	 * R�cup�rer l'unit� de mesure
	 * @return string 
	 */
	public function getUniteMesure()
	{
		return $this->uniteMesure;
	}
	
	/**
	 * D�finir l'unit� de mesure
	 * @param $uniteMesure string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setUniteMesure($uniteMesure,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->uniteMesure = $uniteMesure;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_UNITE_MESURE'),array($uniteMesure)) : true;
	}
	
	/**
	 * R�cup�rer la qte_par_unite_de_mesure
	 * @return string 
	 */
	public function getQuantiteParUniteMesure()
	{
		return $this->quantiteParUniteMesure;
	}
	
	/**
	 * D�finir la qte_par_unite_de_mesure
	 * @param $qte_par_unite_de_mesure string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setQuantiteParUniteMesure($quantiteParUniteMesure,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->quantiteParUniteMesure = $quantiteParUniteMesure;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_QTE_PAR_UNITE_DE_MESURE'),array($quantiteParUniteMesure)) : true;
	}
	
	/**
	 * R�cup�rer le poids brut
	 * @return string 
	 */
	public function getPoidsBrut()
	{
		return $this->poidsBrut;
	}
	
	/**
	 * D�finir le poids brut
	 * @param $poidsBrut string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPoidsBrut($poidsBrut,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->poidsBrut = $poidsBrut;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_POIDS_BRUT'),array($poidsBrut)) : true;
	}
	
	/**
	 * R�cup�rer le poids net
	 * @return string 
	 */
	public function getPoidsNet()
	{
		return $this->poidsNet;
	}
	
	/**
	 * D�finir le poids net
	 * @param $poidsNEt string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPoidsNet($poidsNet,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->poidsNet = $poidsNet;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_POIDS_NET'),array($poidsNet)) : true;
	}
	
	/**
	 * R�cup�rer le est un poids variable
	 * @return string 
	 */
	public function getEstPoidsVariable()
	{
		return $this->estPoidsVariable;
	}
	
	/**
	 * D�finir le est un poids variable
	 * @param $estPoidsVariable string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setEstPoidsVariable($estPoidsVariable,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->estPoidsVariable = $estPoidsVariable;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_EST_POIDS_VARIABLE'),array($estPoidsVariable)) : true;
	}
	
	/**
	 * R�cup�rer la priorit� de passage du produit
	 * @return string 
	 */
	public function getPriorite($auProduit=false)
	{
		if($auProduit){
			return $this->priorite;
		}
		else{
			if ($this->priorite != null){
				return array($this->priorite, gettext('Produit'));
			}
			else{	
				/* R�cup�rer la priorit� de l'�tag�re */
				$etageres 	= $this->selectEtageres();
				if ($etageres != null)
					$prio = $etageres[0]->getPriorite();
				else
					return array(PRIORITE_NORMAL, gettext('Par d&eacute;faut'));
				
				/* Si produit g�olocalis� dans plusieurs �tag�res */
				for($i=1; $i<count($etageres); $i++){
					$item = $etageres[$i]->getPriorite();
					if ($item != null && $min > $item)
						$prio = $item;
				}
				
				if ($prio != null){
					return array($prio, gettext('Etagere'));
				}
				else{
					/* R�cup�rer la priorit� du segment */
					$segment 	= $etageres[0]->getSegment();
					$prio 		= $segment->getPriorite();
					if($prio != null){
						return array($prio, gettext('Segment'));
					}
					else{
						/* R�cup�rer la priorit� du rayon */
						$rayon 	= $segment->getRayon();
						$prio	= $rayon->getPriorite();
						if($prio != null){
							return array($prio, gettext('Rayon'));
						}
						else{
							/* R�cup�rer la priorit� de la zone */
							$zone 	= $rayon->getZone();
							$prio	= $zone->getPriorite();
							if($prio != null){
								return array($prio, gettext('Zone'));
							}
							else{
								/* R�cup�rer la priorit� de l'�tage */
								$etage 	= $zone->getEtage();
								$prio	= $etage->getPriorite();
								if($prio != null){
									return array($prio, gettext('Etage'));
								}
								else{
									/* Retourner la priorit� par d�faut (Normale) */
									return array(PRIORITE_NORMAL, gettext('Par d&eacute;faut'));
								}
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * D�finir la priorit� de passage du produit
	 * @param $priorite string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPriorite($priorite,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->priorite = $priorite;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_PRIORITE'),array($priorite)) : true;
	}
	
	/**
	 * R�cup�rer le temps moyen d'acc�s au produit
	 * @return string 
	 */
	public function getTempsMoyenAccess()
	{
		return $this->tempsMoyenAccess;
	}
	
	/**
	 * D�finir le temps moyen d'acc�s au produit
	 * @param $tempsMoyenAccess int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setTempsMoyenAccess($tempsMoyenAccess,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->tempsMoyenAccess = $tempsMoyenAccess;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_TEMPS_MOYEN_ACCESS'),array($tempsMoyenAccess)) : true;
	}
	
	
	/**
	 * R�cup�rer le stock du produit
	 * @return int 
	 */
	public function getStock()
	{
		return $this->stock;
	}
	
	/**
	 * D�finir le stock du produit
	 * @param $stock int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setStock($stock,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->stock = $stock;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PRO_STOCK'),array($stock)) : true;
	}
	
	
	
	
	
	public function moyenneTempsAcces(){
		$pdoStatement = $this->pdo->prepare('	SELECT AVG(pr.PREL_TEMPS) as moyenne
										FROM LIGNE_COMMANDE lc, PRELEVEMENT_REALISE pr
										WHERE lc.ID_PRODUIT = ?
										AND lc.ID_LIGNE = pr.ID_LIGNE');
										
		if (!$pdoStatement->execute(array($this->idProduit))) {
			throw new Exception('Erreur lors de la mise � jour de la priorit� de tous les produits dans la base de donn�es');
		}
		
		if (($new = $pdoStatement->fetchColumn()) != NULL){
			return $new;
		}
		else{
			return $this->tempsMoyenAccess;
		}
	}
	
	/**
	 * D�finir la priorit� de passage de tous les produits
	 * @param $priorite string 
	 * @return bool op�ration r�ussie ?
	 */
	public static function setAllPriorite($pdo, $priorite){
		$pdoStatement = $pdo->prepare('UPDATE PRODUIT SET PRO_PRIORITE = ?');
		
		if (!$pdoStatement->execute(array($priorite))) {
			throw new Exception('Erreur lors de la mise � jour de la priorit� de tous les produits dans la base de donn�es');
		}
		
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * Ajouter une etagere
	 * @param $etagere Etagere 
	 * @return bool op�ration r�ussie ?
	 */
	public function addEtagere(Etagere $etagere)
	{
		$pdoStatement = $this->pdo->prepare('INSERT INTO EST_GEOLOCALISE_DANS (ID_PRODUIT,ID_ETAGERE) VALUES (?,?)');
		if (!$pdoStatement->execute(array($this->getIdProduit(),$etagere->getIdetagere()))) {
			throw new Exception('Erreur lors de l\'ajout d\'une etagere � un produit dans la base de donn�es');
		}
		
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * Supprimer une etagere
	 * @param $etagere Etagere 
	 * @return bool op�ration r�ussie ?
	 */
	public function delEtagere(Etagere $etagere)
	{
		$pdoStatement = $this->pdo->prepare('DELETE FROM EST_GEOLOCALISE_DANS WHERE ID_PRODUIT = ? AND ID_ETAGERE = ?');
		if (!$pdoStatement->execute(array($this->getIdProduit(),$etagere->getIdetagere()))) {
			throw new Exception('Erreur lors de la suppression d\'une etagere � un produit dans la base de donn�es');
		}
		return $pdoStatement->rowCount() == 1;
	}
	
	/**
	 * S�lectionner les etageres
	 * @return PDOStatement 
	 */
	public function selectEtageres()
	{
		return Etagere::selectByProduit($this->pdo,$this);
	}
	
	/**
	 * S�lectionner les produits par etagere
	 * @param $pdo ownPDO 
	 * @param $etagere Etagere 
	 * @return PDOStatement 
	 */
	public static function selectByEtagere(ownPDO $pdo,Etagere $etagere)
	{
		$pdoStatement = $pdo->prepare('SELECT * FROM PRODUIT p, EST_GEOLOCALISE_DANS a WHERE p.ID_PRODUIT = a.ID_PRODUIT AND a.ID_ETAGERE = ?');
		if (!$pdoStatement->execute(array($etagere->getIdetagere()))) {
			throw new Exception('Erreur lors du chargement de tous les produits par etagere depuis la base de donn�es');
		}
		$produits = array();
		while ($produit = Produit::fetch($pdo,$pdoStatement,true)) {
			$produits[] = $produit;
		}
		
		// Retourner le tableau
		return $produits; 
	}
	
	/**
	 * S�lectionner les produits par etagere et libelle 
	 * @param $pdo ownPDO 
	 * @param $etagere Etagere 
	 * @return PDOStatement 
	 */
	public static function selectByLibelleAndEtagere(ownPDO $pdo, $libelle,Etagere $etagere)
	{
		$pdoStatement = $pdo->prepare('SELECT * FROM PRODUIT p, EST_GEOLOCALISE_DANS a WHERE p.ID_PRODUIT = a.ID_PRODUIT AND a.ID_ETAGERE = ? AND p.PRO_LIBELLE like ?');
		if (!$pdoStatement->execute(array($etagere->getIdetagere(), '%' . $libelle . '%'))) {
			throw new Exception('Erreur lors du chargement de tous les produits par etagere depuis la base de donn�es');
		}
		$produits = array();
		while ($produit = Produit::fetch($pdo,$pdoStatement,true)) {
			$produits[] = $produit;
		}
		
		// Retourner le tableau
		return $produits; 
	}
	
	/**
	 * S�lectionner les produits par code ean
	 * @param $pdo ownPDO 
	 * @param $ean
	 * @return PDOStatement 
	 */
	public static function selectByEan(ownPDO $pdo,$ean)
	{
		$pdoStatement = $pdo->prepare('SELECT * FROM PRODUIT p, EAN e WHERE p.ID_PRODUIT = e.ID_PRODUIT AND e.EAN_EAN = ?');
		if (!$pdoStatement->execute(array($ean))) {
			throw new Exception('Erreur lors du chargement de tous les produits par etagere depuis la base de donn�es');
		}
		
		// Retourner le tableau
		return Produit::fetch($pdo,$pdoStatement,true); 
	}
	
	/**
	 * S�lectionner les produits qui ne sont pas g�olocalis�s
	 * @param $pdo ownPDO
	 * @return produitsNonGeolocalises
	 */
	public static function selectProductsNotGeolocalized(ownPDO $pdo){
		return self::loadAll($pdo,null, null, null, null, null, null, null, null, null, 'nonGeoloc', true);
	}
	
	/**
	 * S�lectionner les eans
	 * @return PDOStatement 
	 */
	public function selectEans()
	{
		return Ean::loadByProduit($this->pdo,$this->getIdProduit());
	}
	
	/**
	 * S�lectionner les ligne_commandes
	 * @return PDOStatement 
	 */
	public function selectLigne_commandes()
	{
		return Ligne_Commande::selectByProduit($this->pdo,$this);
	}
	
	public function getNbGeolocalisation(){
		
		$pdoStatement = $this->pdo->prepare('SELECT * FROM EST_GEOLOCALISE_DANS egd WHERE egd.ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($this->getIdProduit()))) {
			throw new Exception('Erreur lors du chargement du nombre de g�olocalisation du produit depuis la base de donn�es');
		}
		
		return $pdoStatement->rowCount();
	}
	
	public static function getProduitByLibelleAndRayon(ownPDO $pdo, $libelle, $rayon){
		$produits = array();
		
		$segments = $rayon->selectSegments();
		foreach ($segments as $segment){
			$etageres = $segment->selectEtageres();
			foreach($etageres as $etagere){
				$produits = array_merge($produits,self::selectByLibelleAndEtagere($pdo,$libelle,$etagere));
			}
		}
		return $produits;
	}
	
	
	public static function vider(PDO $pdo){
		$pdoStatement = $pdo->prepare('DELETE FROM EAN');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des produits dans la base de donn�es');
		}
		
		$pdoStatement = $pdo->prepare('DELETE FROM EST_GEOLOCALISE_DANS');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des produits dans la base de donn�es');
		}
		
		$pdoStatement = $pdo->prepare('DELETE FROM PRODUIT');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des produits dans la base de donn�es');
		}
		return true;
	}
	
}
?>