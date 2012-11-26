<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
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
	 * Créer un client
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
		// Ajouter le client dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO CLIENT (ID_CLIENT,CLI_NOM,CLI_PRENOM,CLI_CIVILITE,CLI_NOM_ENTREPRISE,CLI_TELEPHONE,CLI_CODE_PAYS_FACTURATION,CLI_CODE_POSTAL_FACTURATION,CLI_CODE_INSEE_FACTURATION,CLI_REGION_FACTURATION,CLI_MUNICIPALITE_FACTURATION,CLI_LIGNE_ADRESSE_FACTURATION,CLI_NOM_RUE_FACTURATION,CLI_NUMERO_BATIMENT_FACTURATION,CLI_UNITE_FACTURATION,CLI_BOITE_POSTALE_FACTURATION,CLI_DESTINATAIRE_FACTURATION) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($idClient,$nom,$prenom,$civilite,$nomEntreprise,$telephone,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation))) {
			throw new Exception('Erreur durant l\'insertion d\'un client dans la base de données');
		}
		
		// Construire le client
		return new Client($pdo,$idClient,$nom,$prenom,$civilite,$nomEntreprise,$telephone,$codePaysFacturation,$codePostalFacturation,$codeInseeFacturation,$regionFacturation,
					$municipaliteFacturation,$ligneAdresseFacturation,$nomRueFacturation,$numeroBatimentFacturation,$uniteFacturation,$boitePostaleFacturation,$destinataireFacturation,$easyload);
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
		// Déjà chargé ?
		if (isset(Client::$easyload[$idClient])) {
			return Client::$easyload[$idClient];
		}
		
		// Charger le client
		$pdoStatement = Client::_select($pdo,'c.ID_CLIENT = ?');
		if (!$pdoStatement->execute(array($idClient))) {
			throw new Exception('Erreur lors du chargement d\'un client depuis la base de données');
		}
		
		// Récupérer le client depuis le jeu de résultats
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
				throw new Exception('Erreur lors du chargement de tous les clients depuis la base de données');
		}
		else{
			if($limit != '')
				$pdoStatement = Client::_select($pdo,null,'CLI_PRENOM',$limit);
			else
				$pdoStatement = Client::_select($pdo,null,'CLI_PRENOM'); 
			if (!$pdoStatement->execute())
				throw new Exception('Erreur lors du chargement de tous les clients depuis la base de données');	
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
	 * Sélectionner tous les clients
	 * @param $pdo ownPDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(ownPDO $pdo)
	{
		$pdoStatement = Client::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous les clients depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
	 * Récupère le client suivant d'un jeu de résultats
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
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
		// Supprimer les commandes associées
		$select = $this->selectCommandes();
		while ($commande = Commande::fetch($this->pdo,$select)) {
			if (!$commande->setClient(null)) { return false; }
		}
		
		// Supprimer le client
		$pdoStatement = $this->pdo->prepare('DELETE FROM CLIENT WHERE ID_CLIENT = ?');
		if (!$pdoStatement->execute(array($this->getIdClient()))) {
			throw new Exception('Erreur lors de la supression d\'un client dans la base de données');
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
		$pdoStatement = $this->pdo->prepare('UPDATE CLIENT SET '.implode(', ', $updates).' WHERE ID_CLIENT = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdClient())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'un client dans la base de données');
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
		return $this->_set(array('ID_CLIENT','CLI_NOM','CLI_PRENOM','CLI_CIVILITE','CLI_NOM_ENTREPRISE','CLI_TELEPHONE','CLI_CODE_PAYS_FACTURATION','CLI_CODE_POSTAL_FACTURATION','CLI_CODE_INSEE_FACTURATION','CLI_REGION_FACTURATION','CLI_MUNICIPALITE_FACTURATION','CLI_LIGNE_ADRESSE_FACTURATION','CLI_NOM_RUE_FACTURATION','CLI_NUMERO_BATIMENT_FACTURATION','CLI_UNITE_FACTURATION','CLI_BOITE_POSTALE_FACTURATION','CLI_DESTINATAIRE_FACTURATION'),
			   array($this->idClient,$this->nom,$this->prenom,$this->civilite,$this->nomEntreprise,$this->telephone,$this->codePaysFacturation,$this->codePostalFacturation,$this->codeInseeFacturation,$this->regionFacturation,$this->municipaliteFacturation,$this->ligneAdresseFacturation,$this->nomRueFacturation,$this->numeroBatimentFacturation,$this->uniteFacturation,$this->boitePostaleFacturation,$this->destinataireFacturation));
	}
	
	
	public function __toString(){
		return '[' . $this->getIdClient() . ']';
	}
	
	/**
	 * Récupérer le idClient
	 * @return int 
	 */
	public function getIdClient()
	{
		return $this->idClient;
	}
	
	/**
	 * Définir le idClient
	 * @param $idClient string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setIdClient($idClient,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idClient = $idClient;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_CLIENT'),array($idClient)) : true;
	}
	
	/**
	 * Récupérer le nom
	 * @return string 
	 */
	public function getNom()
	{
		return $this->nom;
	}
	
	/**
	 * Définir le nom
	 * @param $nom string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setNom($nom,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->nom = $nom;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_NOM'),array($nom)) : true;
	}
	
	/**
	 * Récupérer le prenom
	 * @return string 
	 */
	public function getPrenom()
	{
		return $this->prenom;
	}
	
	/**
	 * Définir le prenom
	 * @param $prenom string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setPrenom($prenom,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->prenom = $prenom;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_PRENOM'),array($prenom)) : true;
	}
	
	/**
	 * Récupérer le civilite
	 * @return string 
	 */
	public function getCivilite()
	{
		return $this->civilite;
	}
	
	/**
	 * Définir le civilite
	 * @param $civilite string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCivilite($civilite,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->civilite = $civilite;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_CIVILITE'),array($civilite)) : true;
	}
	
	/**
	 * Récupérer le nom de l'entreprise
	 * @return string 
	 */
	public function getNomEntreprise()
	{
		return $this->nomEntreprise;
	}
	
	/**
	 * Définir le nom de l'entreprise
	 * @param $nomEntreprise string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setNomEntreprise($nomEntreprise,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->nomEntreprise = $nomEntreprise;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_NOM_ENTREPRISE'),array($nomEntreprise)) : true;
	}
	
	/**
	 * Récupérer le telephone
	 * @return string 
	 */
	public function getTelephone()
	{
		return $this->telephone;
	}
	
	/**
	 * Définir le telephone
	 * @param $telephone string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setTelephone($telephone,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->telephone = $telephone;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_TELEPHONE'),array($telephone)) : true;
	}
	
	/**
	 * Récupérer le codePaysFacturation
	 * @return string 
	 */
	public function getCodePaysFacturation()
	{
		return $this->codePaysFacturation;
	}
	
	/**
	 * Définir le codePaysFacturation
	 * @param $codePaysFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCodePaysFacturation($codePaysFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codePaysFacturation = $codePaysFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_CODE_PAYS_FACTURATION'),array($codePaysFacturation)) : true;
	}
	
	/**
	 * Récupérer le codePostalFacturation
	 * @return string 
	 */
	public function getCodePostalFacturation()
	{
		return $this->codePostalFacturation;
	}
	
	/**
	 * Définir le codePostalFacturation
	 * @param $codePostalFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCodePostalFacturation($codePostalFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codePostalFacturation = $codePostalFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_CODE_POSTAL_FACTURATION'),array($codePostalFacturation)) : true;
	}
	
	/**
	 * Récupérer le codeInseeFacturation
	 * @return string 
	 */
	public function getCodeInseeFacturation()
	{
		return $this->codeInseeFacturation;
	}
	
	/**
	 * Définir le codeInseeFacturation
	 * @param $codeInseeFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCodeInseeFacturation($codeInseeFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codeInseeFacturation = $codeInseeFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_CODE_INSEE_FACTURATION'),array($codeInseeFacturation)) : true;
	}
	
	/**
	 * Récupérer la regionFacturation
	 * @return string 
	 */
	public function getRegionFacturation()
	{
		return $this->regionFacturation;
	}
	
	/**
	 * Définir la regionFacturation
	 * @param $regionFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setRegionFacturation($regionFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->regionFacturation = $regionFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_REGION_FACTURATION'),array($regionFacturation)) : true;
	}
	
	/**
	 * Récupérer la municpaliteFacturation
	 * @return string 
	 */
	public function getMunicipaliteFacturation()
	{
		return $this->municpaliteFacturation;
	}
	
	/**
	 * Définir la municipaliteFacturation
	 * @param $munipaliteFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setMunicipaliteFacturation($municipaliteFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->municipaliteFacturation = $municipaliteFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_MUNICIPALITE_FACTURATION'),array($municipaliteFacturation)) : true;
	}
	
	/**
	 * Récupérer la ligneAdresseFacturation
	 * @return string 
	 */
	public function getLigneAdresseFacturation()
	{
		return $this->ligneAdresseFacturation;
	}
	
	/**
	 * Définir la ligneAdresseFacturation
	 * @param $ligneAdresseFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setLigneAdresseFacturation($ligneAdresseFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->ligneAdresseFacturation = $ligneAdresseFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_LIGNE_ADRESSE_FACTURATION'),array($ligneAdresseFacturation)) : true;
	}
	
	/**
	 * Récupérer le nomRueFacturation
	 * @return string 
	 */
	public function getNomRueFacturation()
	{
		return $this->nomRueFacturation;
	}
	
	/**
	 * Définir le nomRueFacturation
	 * @param $nomRueFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setNomRueFacturation($nomRueFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->nomRueFacturation = $nomRueFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_NOM_RUE_FACTURATION'),array($nomRueFacturation)) : true;
	}
	
	/**
	 * Récupérer le numeroBatimentFacturation
	 * @return string 
	 */
	public function getNumeroBatimentFacturation()
	{
		return $this->numeroBatimentFacturation;
	}
	
	/**
	 * Définir le numeroBatimentFacturation
	 * @param $numeroBatimentFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setNumeroBatimentFacturation($numeroBatimentFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->numeroBatimentFacturation = $numeroBatimentFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_NUMERO_BATIMENT_FACTURATION'),array($numeroBatimentFacturation)) : true;
	}
	
	/**
	 * Récupérer la uniteFacturation
	 * @return string 
	 */
	public function getUniteFacturation()
	{
		return $this->uniteFacturation;
	}
	
	/**
	 * Définir la uniteFacturation
	 * @param $uniteFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setUniteFacturation($uniteFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->uniteFacturation = $uniteFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_UNITE_FACTURATION'),array($uniteFacturation)) : true;
	}
	
	/**
	 * Récupérer la boitePostaleFacturation
	 * @return string 
	 */
	public function getBoitePostaleFacturation()
	{
		return $this->boitePostaleFacturation;
	}
	
	/**
	 * Définir la boitePostaleFacturation
	 * @param $boitePostaleFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setBoitePostaleFacturation($boitePostaleFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->boitePostaleFacturation = $boitePostaleFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_BOITE_POSTALE_FACTURATION'),array($boitePostaleFacturation)) : true;
	}
	
	/**
	 * Récupérer le destinataireFacturation
	 * @return string 
	 */
	public function getDestinataireFacturation()
	{
		return $this->destinataireFacturation;
	}
	
	/**
	 * Définir le destinataireFacturation
	 * @param $destinataireFacturation string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setDestinataireFacturation($destinataireFacturation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->destinataireFacturation = $destinataireFacturation;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CLI_DESTINATAIRE_FACTURATION'),array($destinataireFacturation)) : true;
	}
	
	
	
	/**
	 * Sélectionner les commandes
	 * @return PDOStatement 
	 */
	public function selectCommandes()
	{
		return Commande::selectByClient($this->pdo,$this);
	}
}

?>