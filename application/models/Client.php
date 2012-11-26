<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Client.php
 *
 */
 
class Client
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idClient;
	
	/// @var string 
	private $nom;
	
	/// @var string 
	private $prenom;
	
	/// @var string 
	private $civilite;
	
	/// @var string 
	private $nomEntreprise;
	
	/// @var string 
	private $telephone;
	
	/// @var string 
	private $codePaysFacturation;
	
	/// @var string 
	private $codePostalFacturation;
	
	/// @var string 
	private $codeInseeFacturation;
	
	/// @var string 
	private $regionFacturation;
	
	/// @var string 
	private $municipaliteFacturation;
	
	/// @var string 
	private $ligneAdresseFacturation;
	
	/// @var string 
	private $nomRueFacturation;
	
	/// @var string 
	private $numeroBatimentFacturation;
	
	/// @var string 
	private $uniteFacturation;
	
	/// @var string 
	private $boitePostaleFacturation;
	
	/// @var string 
	private $destinataireFacturation;
	
	/**
	 * Construire un client
	 * @param $pdo ownPDO 
	 * @param $idClient int 
	 * @param $nom string 
	 * @param $prenom string 
	 * @param $civilite string 
	 * @param $nomEntreprise string 
	 * @param $telephone string 
	 * @param $codePaysFacturation string 
	 * @param $codePostalFacturation string 
	 * @param $codeInseeFacturation string 
	 * @param $regionFacturation string 
	 * @param $municipaliteFacturation string 
	 * @param $ligneAdresseFacturation string 
	 * @param $nomRueFacturation string 
	 * @param $numeroBatimentFacturation string 
	 * @param $uniteFacturation string 
	 * @param $boitePostaleFacturation string 
	 * @param $destinataireFacturation string 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Client 
	 */
	public function __construct(ownPDO $pdo,$idClient,$nom,$prenom,$civilite,$nomEntreprise,$telephone,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idClient = $idClient;
		$this->nom = $nom;
		$this->prenom = $prenom;
		$this->civilite = $civilite;
		$this->nomEntreprise = $nomEntreprise;
		$this->telephone = $telephone;
		$this->codePaysFacturation = $codePaysFacturation;
		$this->codePostalFacturation = $codePostalFacturation;
		$this->codeInseeFacturation = $codeInseeFacturation;
		$this->regionFacturation = $regionFacturation;
		$this->municpaliteFacturation = $municipaliteFacturation;
		$this->ligneAdresseFacturation = $ligneAdresseFacturation;
		$this->nomRueFacturation = $nomRueFacturation;
		$this->numeroBatimentFacturation = $numeroBatimentFacturation;
		$this->uniteFacturation = $uniteFacturation;
		$this->boitePostaleFacturation = $boitePostaleFacturation;
		$this->destinataireFacturation = $destinataireFacturation;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Client::$easyload[$idClient] = $this;
		}
	}
	
	/**
	 * Cr�er un client
	 * @param $pdo ownPDO 
	 * @param $code_client string 
	 * @param $nom string 
	 * @param $prenom string 
	 * @param $civilite string 
	 * @param $telephone string 
	 * @param $adresse_facturation string 
	 * @param $ville_facturation string 
	 * @param $code_postal_facturation string 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Client 
	 */
	public static function create(ownPDO $pdo,$idClient,$nom,$prenom,$civilite,$nomEntreprise,$telephone,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation,$easyload=true)
	{
		// Ajouter le client dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO CLIENT (ID_CLIENT,CLI_NOM,CLI_PRENOM,CLI_CIVILITE,CLI_NOM_ENTREPRISE,CLI_TELEPHONE,CLI_CODE_PAYS_FACTURATION,CLI_CODE_POSTAL_FACTURATION,CLI_CODE_INSEE_FACTURATION,CLI_REGION_FACTURATION,CLI_MUNICIPALITE_FACTURATION,CLI_LIGNE_ADRESSE_FACTURATION,CLI_NOM_RUE_FACTURATION,CLI_NUMERO_BATIMENT_FACTURATION,CLI_UNITE_FACTURATION,CLI_BOITE_POSTALE_FACTURATION,CLI_DESTINATAIRE_FACTURATION) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($idClient,$nom,$prenom,$civilite,$nomEntreprise,$telephone,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation))) {
			throw new Exception('Erreur durant l\'insertion d\'un client dans la base de donn�es');
		}
		
		// Construire le client
		return new Client($pdo,$idClient,$nom,$prenom,$civilite,$nomEntreprise,$telephone,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation,$easyload);
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
		return $pdo->prepare('SELECT * FROM CLIENT c '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un client
	 * @param $pdo ownPDO 
	 * @param $idClient int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Client 
	 */
	public static function load(ownPDO $pdo,$idClient,$easyload=true)
	{
		// D�j� charg� ?
		if (isset(Client::$easyload[$idClient])) {
			return Client::$easyload[$idClient];
		}
		
		// Charger le client
		$pdoStatement = Client::_select($pdo,'c.ID_CLIENT = ?');
		if (!$pdoStatement->execute(array($idClient))) {
			throw new Exception('Erreur lors du chargement d\'un client depuis la base de donn�es');
		}
		
		// R�cup�rer le client depuis le jeu de r�sultats
		return Client::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous les clients
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Client[] tableau de clients
	 */
	public static function loadAll(ownPDO $pdo,$first=null,$prenomFilter=null,$nomFilter=null,$societeFilter=null,$codepFilter=null,$municipaliteFilter=null,$easyload=false)
	{ 
		$where 		= '';
		$limit		= '';
		$array_attr = array();
		if($prenomFilter != null){
			$where 			.= 'CLI_PRENOM LIKE ? AND ';
			$array_attr[]	 =  '%' . $prenomFilter . '%';
		}
		if($nomFilter != null){
			$where 			.= 'CLI_NOM LIKE ? AND ';
			$array_attr[]	 =  '%' . $nomFilter . '%';
		}
		if($societeFilter != null){
			$where 			.= 'CLI_NOM_ENTREPRISE LIKE ? AND ';
			$array_attr[]	 =  '%' . $societeFilter . '%';
		}
		if($codepFilter != null){
			$where 			.= 'CLI_CODE_POSTAL_FACTURATION LIKE ? AND ';
			$array_attr[]	 =  '%' . $codepFilter . '%';
		}
		if($municipaliteFilter != null){
			$where 			.= 'CLI_MUNICIPALITE_FACTURATION LIKE ? AND ';
			$array_attr[]	 =  '%' . $municipaliteFilter . '%';
		}
		
		if($first != null){
			$limit =  $first.','.RESULTAT_PAR_PAGE;
		}
		
		
		if($where != ''){
			$where .= ' 1=1 ';
			if($limit != '')
				$pdoStatement = Client::_select($pdo,$where,'CLI_PRENOM',$limit);
			else
				$pdoStatement = Client::_select($pdo,$where,'CLI_PRENOM');
			if (!$pdoStatement->execute($array_attr))
				throw new Exception('Erreur lors du chargement de tous les clients depuis la base de donn�es');
		}
		else{
			if($limit != '')
				$pdoStatement = Client::_select($pdo,null,'CLI_PRENOM',$limit);
			else
				$pdoStatement = Client::_select($pdo,null,'CLI_PRENOM'); 
			if (!$pdoStatement->execute())
				throw new Exception('Erreur lors du chargement de tous les clients depuis la base de donn�es');	
		}
		
		// Mettre chaque client dans un tableau
		$clients = array();
		while ($client = Client::fetch($pdo,$pdoStatement,$easyload)) {
			$clients[] = $client;
		} 
		// Retourner le tableau
		return $clients;
	}
	
	/**
	 * S�lectionner tous les clients
	 * @param $pdo ownPDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(ownPDO $pdo)
	{
		$pdoStatement = Client::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous les clients depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * R�cup�re le client suivant d'un jeu de r�sultats
	 * @param $pdo ownPDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Client 
	 */
	public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idClient,$nom,$prenom,$civilite,$nomEntreprise,$telephone,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation) = $values;
		
		// Construire le client
		return isset(Client::$easyload[$idClient]) ? Client::$easyload[$idClient] :
		       new Client($pdo,$idClient,$nom,$prenom,$civilite,$nomEntreprise,$telephone,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation,$easyload);
	}
	
	/**
	 * Supprimer le client
	 * @return bool op�ration r�ussie ?
	 */
	public function delete()
	{
		// Supprimer les commandes associ�es
		$select = $this->selectCommandes();
		while ($commande = Commande::fetch($this->pdo,$select)) {
			if (!$commande->setClient(null)) { return false; }
		}
		
		// Supprimer le client
		$pdoStatement = $this->pdo->prepare('DELETE FROM CLIENT WHERE ID_CLIENT = ?');
		if (!$pdoStatement->execute(array($this->getIdClient()))) {
			throw new Exception('Erreur lors de la supression d\'un client dans la base de donn�es');
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
		$pdoStatement = $this->pdo->prepare('UPDATE CLIENT SET '.implode(', ', $updates).' WHERE ID_CLIENT = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdClient())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'un client dans la base de donn�es');
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
		return $this->_set(array('ID_CLIENT','CLI_NOM','CLI_PRENOM','CLI_CIVILITE','CLI_NOM_ENTREPRISE','CLI_TELEPHONE','CLI_CODE_PAYS_FACTURATION','CLI_CODE_POSTAL_FACTURATION','CLI_CODE_INSEE_FACTURATION','CLI_REGION_FACTURATION','CLI_MUNICIPALITE_FACTURATION','CLI_LIGNE_ADRESSE_FACTURATION','CLI_NOM_RUE_FACTURATION','CLI_NUMERO_BATIMENT_FACTURATION','CLI_UNITE_FACTURATION','CLI_BOITE_POSTALE_FACTURATION','CLI_DESTINATAIRE_FACTURATION'),
			   array($this->idClient,$this->nom,$this->prenom,$this->civilite,$this->nomEntreprise,$this->telephone,$this->codePaysFacturation,$this->codePostalFacturation,$this->codeInseeFacturation,$this->regionFacturation,$this->municipaliteFacturation,$this->ligneAdresseFacturation,$this->nomRueFacturation,$this->numeroBatimentFacturation,$this->uniteFacturation,$this->boitePostaleFacturation,$this->destinataireFacturation));
	}
	
	
	public function __toString(){
		return '[' . $this->getIdClient() . ']';
	}
	
	/**
	 * R�cup�rer le idClient
	 * @return int 
	 */
	public function getIdClient()
	{
		return $this->idClient;
	}
	
	/**
	 * D�finir le idClient
	 * @param $idClient string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setIdClient($idClient,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idClient = $idClient;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_CLIENT'),array($idClient)) : true;
	}
	
	/**
	 * R�cup�rer le nom
	 * @return string 
	 */
	public function getNom()
	{
		return $this->nom;
	}
	
	/**
	 * D�finir le nom
	 * @param $nom string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setNom($nom,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->nom = $nom;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_NOM'),array($nom)) : true;
	}
	
	/**
	 * R�cup�rer le prenom
	 * @return string 
	 */
	public function getPrenom()
	{
		return $this->prenom;
	}
	
	/**
	 * D�finir le prenom
	 * @param $prenom string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPrenom($prenom,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->prenom = $prenom;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_PRENOM'),array($prenom)) : true;
	}
	
	/**
	 * R�cup�rer le civilite
	 * @return string 
	 */
	public function getCivilite()
	{
		return $this->civilite;
	}
	
	/**
	 * D�finir le civilite
	 * @param $civilite string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setCivilite($civilite,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->civilite = $civilite;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_CIVILITE'),array($civilite)) : true;
	}
	
	/**
	 * R�cup�rer le nom de l'entreprise
	 * @return string 
	 */
	public function getNomEntreprise()
	{
		return $this->nomEntreprise;
	}
	
	/**
	 * D�finir le nom de l'entreprise
	 * @param $nomEntreprise string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setNomEntreprise($nomEntreprise,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->nomEntreprise = $nomEntreprise;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_NOM_ENTREPRISE'),array($nomEntreprise)) : true;
	}
	
	/**
	 * R�cup�rer le telephone
	 * @return string 
	 */
	public function getTelephone()
	{
		return $this->telephone;
	}
	
	/**
	 * D�finir le telephone
	 * @param $telephone string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setTelephone($telephone,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->telephone = $telephone;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_TELEPHONE'),array($telephone)) : true;
	}
	
	/**
	 * R�cup�rer le codePaysFacturation
	 * @return string 
	 */
	public function getCodePaysFacturation()
	{
		return $this->codePaysFacturation;
	}
	
	/**
	 * D�finir le codePaysFacturation
	 * @param $codePaysFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setCodePaysFacturation($codePaysFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codePaysFacturation = $codePaysFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_CODE_PAYS_FACTURATION'),array($codePaysFacturation)) : true;
	}
	
	/**
	 * R�cup�rer le codePostalFacturation
	 * @return string 
	 */
	public function getCodePostalFacturation()
	{
		return $this->codePostalFacturation;
	}
	
	/**
	 * D�finir le codePostalFacturation
	 * @param $codePostalFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setCodePostalFacturation($codePostalFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codePostalFacturation = $codePostalFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_CODE_POSTAL_FACTURATION'),array($codePostalFacturation)) : true;
	}
	
	/**
	 * R�cup�rer le codeInseeFacturation
	 * @return string 
	 */
	public function getCodeInseeFacturation()
	{
		return $this->codeInseeFacturation;
	}
	
	/**
	 * D�finir le codeInseeFacturation
	 * @param $codeInseeFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setCodeInseeFacturation($codeInseeFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codeInseeFacturation = $codeInseeFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_CODE_INSEE_FACTURATION'),array($codeInseeFacturation)) : true;
	}
	
	/**
	 * R�cup�rer la regionFacturation
	 * @return string 
	 */
	public function getRegionFacturation()
	{
		return $this->regionFacturation;
	}
	
	/**
	 * D�finir la regionFacturation
	 * @param $regionFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setRegionFacturation($regionFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->regionFacturation = $regionFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_REGION_FACTURATION'),array($regionFacturation)) : true;
	}
	
	/**
	 * R�cup�rer la municpaliteFacturation
	 * @return string 
	 */
	public function getMunicipaliteFacturation()
	{
		return $this->municpaliteFacturation;
	}
	
	/**
	 * D�finir la municipaliteFacturation
	 * @param $munipaliteFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setMunicipaliteFacturation($municipaliteFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->municipaliteFacturation = $municipaliteFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_MUNICIPALITE_FACTURATION'),array($municipaliteFacturation)) : true;
	}
	
	/**
	 * R�cup�rer la ligneAdresseFacturation
	 * @return string 
	 */
	public function getLigneAdresseFacturation()
	{
		return $this->ligneAdresseFacturation;
	}
	
	/**
	 * D�finir la ligneAdresseFacturation
	 * @param $ligneAdresseFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setLigneAdresseFacturation($ligneAdresseFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->ligneAdresseFacturation = $ligneAdresseFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_LIGNE_ADRESSE_FACTURATION'),array($ligneAdresseFacturation)) : true;
	}
	
	/**
	 * R�cup�rer le nomRueFacturation
	 * @return string 
	 */
	public function getNomRueFacturation()
	{
		return $this->nomRueFacturation;
	}
	
	/**
	 * D�finir le nomRueFacturation
	 * @param $nomRueFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setNomRueFacturation($nomRueFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->nomRueFacturation = $nomRueFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_NOM_RUE_FACTURATION'),array($nomRueFacturation)) : true;
	}
	
	/**
	 * R�cup�rer le numeroBatimentFacturation
	 * @return string 
	 */
	public function getNumeroBatimentFacturation()
	{
		return $this->numeroBatimentFacturation;
	}
	
	/**
	 * D�finir le numeroBatimentFacturation
	 * @param $numeroBatimentFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setNumeroBatimentFacturation($numeroBatimentFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->numeroBatimentFacturation = $numeroBatimentFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_NUMERO_BATIMENT_FACTURATION'),array($numeroBatimentFacturation)) : true;
	}
	
	/**
	 * R�cup�rer la uniteFacturation
	 * @return string 
	 */
	public function getUniteFacturation()
	{
		return $this->uniteFacturation;
	}
	
	/**
	 * D�finir la uniteFacturation
	 * @param $uniteFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setUniteFacturation($uniteFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->uniteFacturation = $uniteFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_UNITE_FACTURATION'),array($uniteFacturation)) : true;
	}
	
	/**
	 * R�cup�rer la boitePostaleFacturation
	 * @return string 
	 */
	public function getBoitePostaleFacturation()
	{
		return $this->boitePostaleFacturation;
	}
	
	/**
	 * D�finir la boitePostaleFacturation
	 * @param $boitePostaleFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setBoitePostaleFacturation($boitePostaleFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->boitePostaleFacturation = $boitePostaleFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_BOITE_POSTALE_FACTURATION'),array($boitePostaleFacturation)) : true;
	}
	
	/**
	 * R�cup�rer le destinataireFacturation
	 * @return string 
	 */
	public function getDestinataireFacturation()
	{
		return $this->destinataireFacturation;
	}
	
	/**
	 * D�finir le destinataireFacturation
	 * @param $destinataireFacturation string 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setDestinataireFacturation($destinataireFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->destinataireFacturation = $destinataireFacturation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('CLI_DESTINATAIRE_FACTURATION'),array($destinataireFacturation)) : true;
	}
	
	
	
	/**
	 * S�lectionner les commandes
	 * @return PDOStatement 
	 */
	public function selectCommandes()
	{
		return Commande::selectByClient($this->pdo,$this);
	}
}

?>