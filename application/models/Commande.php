<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Commande.php
 *
 */
 
class Commande
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idCommande;
	
	/// @var int id de idClient
	private $idClient;
	
	/// @var int 
	private $codeCommande;
	
	/// @var int 
	private $dateCommande;
	
	/// @var int 
	private $dateLivraison;
	
	/// @var string 
	private $etatCommande;
	
	/// @var string 
	private $codePaysLivraison;
	
	/// @var string 
	private $codePostalLivraison;
	
	/// @var string 
	private $codeInseeLivraison;
	
	/// @var string 
	private $regionLivraison;
	
	/// @var string 
	private $municipaliteLivraison;
	
	/// @var string 
	private $ligneAdresseLivraison;
	
	/// @var string 
	private $nomRueLivraison;
	
	/// @var string 
	private $numeroBatimentLivraison;
	
	/// @var string 
	private $uniteLivraison;
	
	/// @var string 
	private $boitePostaleLivraison;
	
	/// @var string 
	private $destinataireLivraison;
	
	/// @var string 
	private $carteFidelite;
	
	/// @var string 
	private $commentaireClient;
	
	/// @var string 
	private $modeLivraison;
	
	/// @var string 
	private $archiveCommande;
	
	/**
	 * Construire une commande
	 * @param $pdo ownPDO 
	 * @param $idCommande int 
	 * @param $idClient int 
	 * @param $codeCommande int 
	 * @param $dateCommande int 
	 * @param $dateLivraison int 
	 * @param $etatCommande string 
	 * @param $codePaysLivraison string 
	 * @param $codePostalLivraison string 
	 * @param $codeInseeLivraison string 
	 * @param $regionLivraison string 
	 * @param $municipaliteLivraison string 
	 * @param $ligneAdresseLivraison string 
	 * @param $nomRueLivraison string 
	 * @param $numeroBatimentLivraison string 
	 * @param $uniteLivraison string 
	 * @param $boitePostaleLivraison string 
	 * @param $destinataireLivraison string 
	 * @param $carteFidelite string 
	 * @param $commentaireClient string 
	 * @param $modeLivraison string 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Client 
	 */
	public function __construct(ownPDO $pdo,$idCommande,$idClient,$codeCommande,$dateCommande,$dateLivraison,$etatCommande,$codePaysLivraison,$codePostalLivraison,$codeInseeLivraison,$regionLivraison,
					$municipaliteLivraison,$ligneAdresseLivraison,$nomRueLivraison,$numeroBatimentLivraison,$uniteLivraison,$boitePostaleLivraison,$destinataireLivraison,$carteFidelite,$commentaireClient,$modeLivraison,$archiveCommande,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idCommande = $idCommande;
		$this->codeCommande = $codeCommande;
		$this->dateCommande = $dateCommande;
		$this->dateLivraison = $dateLivraison;
		$this->etatCommande = $etatCommande;
		$this->codePaysLivraison = $codePaysLivraison;
		$this->codePostalLivraison = $codePostalLivraison;
		$this->codeInseeLivraison = $codeInseeLivraison;
		$this->regionLivraison = $regionLivraison;
		$this->municpaliteLivraison = $municipaliteLivraison;
		$this->ligneAdresseLivraison = $ligneAdresseLivraison;
		$this->nomRueLivraison = $nomRueLivraison;
		$this->numeroBatimentLivraison = $numeroBatimentLivraison;
		$this->uniteLivraison = $uniteLivraison;
		$this->boitePostaleLivraison = $boitePostaleLivraison;
		$this->destinataireLivraison = $destinataireLivraison;
		$this->idClient = $idClient;
		$this->carteFidelite = $carteFidelite;
		$this->commentaireClient = $commentaireClient;
		$this->modeLivraison = $modeLivraison;
		$this->archiveCommande = $archiveCommande;
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Commande::$easyload[$idCommande] = $this;
		}
	}
	
	/**
	 * Créer une commande
	 * @param $pdo ownPDO 
	 * @param $codeCommande int 
	 * @param $dateCommande int 
	 * @param $dateLivraison int 
	 * @param $etatCommande string 
	 * @param $codePaysLivraison string 
	 * @param $codePostalLivraison string 
	 * @param $codeInseeLivraison string 
	 * @param $regionLivraison string 
	 * @param $municipaliteLivraison string 
	 * @param $ligneAdresseLivraison string 
	 * @param $nomRueLivraison string 
	 * @param $numeroBatimentLivraison string 
	 * @param $uniteLivraison string 
	 * @param $boitePostaleLivraison string 
	 * @param $destinataireLivraison string 
	 * @param $modeLivraison string 
	 * @param $idClient int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Commande 
	 */
	public static function create(ownPDO $pdo,$idClient,$codeCommande,$dateCommande,$dateLivraison,$etatCommande,$codePaysLivraison,$codePostalLivraison,$codeInseeLivraison,$regionLivraison,
					$municipaliteLivraison,$ligneAdresseLivraison,$nomRueLivraison,$numeroBatimentLivraison,$uniteLivraison,$boitePostaleLivraison,$destinataireLivraison,$carteFidelite,$commentaireClient,$modeLivraison,$archiveCommande,$easyload=true)
	{
		// Ajouter la commande dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO COMMANDE (ID_CLIENT,CMD_CODE_COMMANDE,CMD_DATE_COMMANDE,CMD_DATE_LIVRAISON,CMD_ETAT_COMMANDE,CMD_CODE_PAYS_LIVRAISON,CMD_CODE_POSTAL_LIVRAISON,CMD_CODE_INSEE_LIVRAISON,CMD_REGION_LIVRAISON,CMD_MUNICIPALITE_LIVRAISON,CMD_LIGNE_ADRESSE_LIVRAISON,CMD_NOM_RUE_LIVRAISON,CMD_NUMERO_BATIMENT_LIVRAISON,CMD_UNITE_LIVRAISON,CMD_BOITE_POSTALE_LIVRAISON,CMD_DESTINATAIRE_LIVRAISON,CMD_CARTE_FIDELITE,CMD_COMMENTAIRE_CLIENT,CMD_MODE_LIVRAISON,CMD_ARCHIVEE) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($idClient,$codeCommande,$dateCommande,$dateLivraison,$etatCommande,$codePaysLivraison,$codePostalLivraison,$codeInseeLivraison,$regionLivraison,
					$municipaliteLivraison,$ligneAdresseLivraison,$nomRueLivraison,$numeroBatimentLivraison,$uniteLivraison,$boitePostaleLivraison,$destinataireLivraison,$carteFidelite,$commentaireClient,$modeLivraison,$archiveCommande))) {
			throw new Exception('Erreur durant l\'insertion d\'une commande dans la base de données');
		}
		
		// Construire la commande
		return new Commande($pdo,$pdo->lastInsertId(),$idClient,$codeCommande,$dateCommande,$dateLivraison,$etatCommande,$codePaysLivraison,$codePostalLivraison,$codeInseeLivraison,$regionLivraison,
					$municipaliteLivraison,$ligneAdresseLivraison,$nomRueLivraison,$numeroBatimentLivraison,$uniteLivraison,$boitePostaleLivraison,$destinataireLivraison,$carteFidelite,$commentaireClient,$modeLivraison,$archiveCommande,$easyload);
	}
	
	/**
	 * Requête de séléction
	 * @param $pdo ownPDO 
	 * @param $where string 
	 * @param $orderby string 
	 * @param $limit string 
	 * @return PDOStatement 
	 */
	private static function _select(ownPDO $pdo,$where=null,$orderby=null,$limit=null,$join=null)
	{
		$req ='SELECT c.* FROM COMMANDE c '.
							 ($join != null ? ' '.$join : '').
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby.' DESC' : '').
		                     ($limit != null ? ' LIMIT '.$limit : '');

		return $pdo->prepare($req);
	}
	
	/**
	 * Charger une commande
	 * @param $pdo ownPDO 
	 * @param $idCommande int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Commande 
	 */
	public static function load(ownPDO $pdo,$idCommande,$easyload=true)
	{
		// Déjà chargé(e) ?
		if ($easyload && isset(Commande::$easyload[$idCommande])) {
			return Commande::$easyload[$idCommande];
		}
		
		// Charger la commande
		$pdoStatement = Commande::_select($pdo,'c.ID_COMMANDE = ?');
		if (!$pdoStatement->execute(array($idCommande))) {
			throw new Exception('Erreur lors du chargement d\'une commande depuis la base de données');
		}
		
		// Récupérer la commande depuis le jeu de résultats
		return Commande::fetch($pdo,$pdoStatement,$easyload);
	}

	/**
	 * Charger une commande
	 * @param $pdo ownPDO 
	 * @param $idCommande int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Commande 
	 */
	public static function loadByCode(ownPDO $pdo,$codeCommande,$easyload=true)
	{
 
		// Charger la commande
		$pdoStatement = Commande::_select($pdo,'c.CMD_CODE_COMMANDE = ?');
		if (!$pdoStatement->execute(array($codeCommande))) {
			throw new Exception('Erreur lors du chargement d\'une commande depuis la base de données');
		}
		
		// Récupérer la commande depuis le jeu de résultats
		return Commande::fetch($pdo,$pdoStatement,$easyload);
	}
	

	/**
	 * Charger toutes les commandes
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Commande[] tableau de commandes
	 */
	public static function loadAll(ownPDO $pdo,$easyload=false,$first=null,$numcoFilter=null,$clicoFilter=null,$etatcoFilter=null,$datedebcoFilter=null,$datefincoFilter=null,$datedebliFilter=null,$datefinliFilter=null,$modeLivraisonFilter=null,$archivecoFilter=0)
	{
		$orderby	= 'c.CMD_CODE_COMMANDE';
		$join		= '';
		$where 		= '';
		$limit		= '';
		$array_attr = array();
		if($numcoFilter != null){
			$where 			.= 'c.CMD_CODE_COMMANDE LIKE ? AND ';
			$array_attr[]	 =  '%' . $numcoFilter . '%';
		}
		if($clicoFilter != null){
			$join			.= 'INNER JOIN CLIENT cli ON c.ID_CLIENT = cli.ID_CLIENT';
			$where 			.= 'cli.CLI_NOM LIKE ? AND ';
			$array_attr[]	 = '%' . $clicoFilter . '%';
		}
		if($etatcoFilter != null){
			$where 			.= 'c.CMD_ETAT_COMMANDE LIKE ? AND ';
			$array_attr[]	 =  '%' . $etatcoFilter . '%';
		}
		if($modeLivraisonFilter != null){
			$where 			.= 'c.CMD_MODE_LIVRAISON LIKE ? AND ';
			$array_attr[]	 =  '%' . $modeLivraisonFilter . '%';
		}
		if($datedebcoFilter != null){
			$where 			.= 'c.CMD_DATE_COMMANDE >= ? AND ';
			$array_attr[]	 =  $datedebcoFilter ;
		}
		if($datefincoFilter != null){
			$where 			.= 'c.CMD_DATE_COMMANDE <= ? AND ';
			$array_attr[]	 =  ($datefincoFilter + 3600*24);
		}
		if($datedebliFilter != null){
			$where 			.= 'c.CMD_DATE_LIVRAISON >= ? AND ';
			$array_attr[]	 =  $datedebliFilter ;
		}
		if($datefinliFilter != null){
			$where 			.= 'c.CMD_DATE_LIVRAISON <= ? AND ';
			$array_attr[]	 = ($datefinliFilter  + 3600*24);
		}
		
		$where 			.= 'c.CMD_ARCHIVEE = ? AND ';
		$array_attr[]	 = $archivecoFilter ;

		if($first != null){
			$limit =  $first.','.RESULTAT_PAR_PAGE;
		}
		 
		if($where != ''){
			$where .= ' 1=1 ';
			if($limit != '')
				$pdoStatement = Commande::_select($pdo,$where,$orderby,$limit,$join);
			else
				$pdoStatement = Commande::_select($pdo,$where,$orderby,null,$join);
			if (!$pdoStatement->execute($array_attr))
				throw new Exception('Erreur lors du chargement de toutes les commmandes depuis la base de données');
		}
		else{
			if($limit != '')
				$pdoStatement = Commande::_select($pdo,null,$orderby,$limit,$join);
			else
				$pdoStatement = Commande::_select($pdo,null,$orderby,null,$join); 
			if (!$pdoStatement->execute())
				throw new Exception('Erreur lors du chargement de toutes les commandes depuis la base de données');	
		}
		
		// Mettre chaque commande dans un tableau
		$commandes = array();
		while ($commande = Commande::fetch($pdo,$pdoStatement,$easyload)) {
			$commandes[] = $commande;
		}
		
		// Retourner le tableau
		return $commandes;
	}
	
	/**
	 * Sélectionner toutes les commandes
	 * @param $pdo ownPDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(ownPDO $pdo)
	{
		$pdoStatement = Commande::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de toutes les commandes depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
	 * Récupère la commande suivant(e) d'un jeu de résultats
	 * @param $pdo ownPDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Commande 
	 */
	public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idCommande,$idClient,$codeCommande,$dateCommande,$dateLivraison,$etatCommande,$codePaysLivraison,$codePostalLivraison,$codeInseeLivraison,$regionLivraison,
					$municipaliteLivraison,$ligneAdresseLivraison,$nomRueLivraison,$numeroBatimentLivraison,$uniteLivraison,$boitePostaleLivraison,$destinataireLivraison,$carteFidelite,$commentaireClient,$modeLivraison,$archiveCommande) = $values;
		
		// Construire la commande
		return isset(Commande::$easyload[$idCommande]) ? Commande::$easyload[$idCommande] :
		       new Commande($pdo,$idCommande,$idClient,$codeCommande,$dateCommande,$dateLivraison,$etatCommande,$codePaysLivraison,$codePostalLivraison,$codeInseeLivraison,$regionLivraison,
					$municipaliteLivraison,$ligneAdresseLivraison,$nomRueLivraison,$numeroBatimentLivraison,$uniteLivraison,$boitePostaleLivraison,$destinataireLivraison,$carteFidelite,$commentaireClient,$modeLivraison,$archiveCommande,$easyload);
	}
	
	/**
	 * Supprimer la commande
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
		// Supprimer les ligne_commandes associé(e)s
		$select = $this->selectLigne_commandes();
		while ($ligne_commande = Ligne_Commande::fetch($this->pdo,$select)) {
			if (!$ligne_commande->setCommande(null)) { return false; }
		}
		
		// Supprimer la commande
		$pdoStatement = $this->pdo->prepare('DELETE FROM COMMANDE WHERE ID_COMMANDE = ?');
		if (!$pdoStatement->execute(array($this->getIdCommande()))) {
			throw new Exception('Erreur lors de la supression d\'une commande dans la base de données');
		}
		
		// Opération réussie ?
		return $pdoStatement->rowCount() == 1;
	}
	
	public function __toString(){
		return $this->idCommande;
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
		$pdoStatement = $this->pdo->prepare('UPDATE COMMANDE SET '.implode(', ', $updates).' WHERE ID_COMMANDE = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdCommande())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'une commande dans la base de données');
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
		return $this->_set(array('ID_COMMANDE','CMD_CODE_COMMANDE','CMD_DATE_COMMANDE','CMD_DATE_LIVRAISON','CMD_ETAT_COMMANDE','CMD_CODE_PAYS_LIVRAISON','CMD_CODE_POSTAL_LIVRAISON','CMD_CODE_INSEE_LIVRAISON','CMD_REGION_LIVRAISON','CMD_MUNICIPALITE_LIVRAISON','CMD_LIGNE_ADRESSE_LIVRAISON','CMD_NOM_RUE_LIVRAISON','CMD_NUMERO_BATIMENT_LIVRAISON','CMD_UNITE_LIVRAISON','CMD_BOITE_POSTALE_LIVRAISON','CMD_DESTINATAIRE_LIVRAISON','ID_CLIENT','CMD_CARTE_FIDELITE','CMD_COMMENTAIRE_CLIENT','CMD_MODE_LIVRAISON','CMD_ARCHIVEE'),
			   array($this->idCommande,$this->codeCommande,$this->dateCommande,$this->dateLivraison,$this->etatCommande,$this->codePaysLivraison,$this->codePostalLivraison,$this->codeInseeLivraison,$this->regionLivraison,$this->municipaliteLivraison,$this->ligneAdresseLivraison,$this->nomRueLivraison,$this->numeroBatimentLivraison,$this->uniteLivraison,$this->boitePostaleLivraison,$this->destinataireLivraison,$this->idClient,$this->carteFidelite,$this->commentaireClient,$this->modeLivraison,$this->archiveCommande));
	}
	
	/**
	 * Récupérer l'idCommande
	 * @return int 
	 */
	public function getIdCommande()
	{
		return $this->idCommande;
	}
	
	/**
	 * Définir l'idCommande
	 * @param $idCommande int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setIdCommande($idCommande,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idCommande = $idCommande;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_COMMANDE'),array($idCommande)) : true;
	}
	/**
	 * Récupérer l'codeCommande
	 * @return int 
	 */
	public function getCodeCommande()
	{
		return $this->codeCommande;
	}
	
	/**
	 * Définir l'idCommande
	 * @param $idCommande int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCodeCommande($codeCommande,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codeCommande = $codeCommande;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_CODE_COMMANDE'),array($codeCommande)) : true;
	}
	
	/**
	 * Récupérer la dateCommande
	 * @return int 
	 */
	public function getDateCommande()
	{
		return $this->dateCommande;
	}
	
	/**
	 * Définir la dateCommande
	 * @param $dateCommande int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setDateCommande($dateCommande,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->dateCommande = $dateCommande;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_DATE_COMMANDE'),array($dateCommande)) : true;
	}
	
	/**
	 * Récupérer la dateLivraison
	 * @return int 
	 */
	public function getDateLivraison()
	{
		return $this->dateLivraison;
	}
	
	/**
	 * Définir la dateLivraison
	 * @param $dateLivraison int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setDateLivraison($dateLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->dateLivraison = $dateLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_DATE_LIVRAISON'),array($dateLivraison)) : true;
	}
	
	/**
	 * Récupérer la etatCommande
	 * @return string 
	 */
	public function getEtatCommande()
	{
		return $this->etatCommande;
	}
	
	/**
	 * Définir la etatCommande
	 * @param $etatCommande string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setEtatCommande($etatCommande,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->etatCommande = $etatCommande;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_ETAT_COMMANDE'),array($etatCommande)) : true;
	}
	
	/**
	 * Récupérer le codePaysLivraison
	 * @return string 
	 */
	public function getCodePaysLivraison()
	{
		return $this->codePaysLivraison;
	}
	
	/**
	 * Définir le codePaysLivraison
	 * @param $codePaysLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCodePaysLivraison($codePaysLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codePaysLivraison = $codePaysLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_CODE_PAYS_LIVRAISON'),array($codePaysLivraison)) : true;
	}
	
	/**
	 * Récupérer le codePostalLivraison
	 * @return string 
	 */
	public function getCodePostalLivraison()
	{
		return $this->codePostalLivraison;
	}
	
	/**
	 * Définir le codePostalLivraison
	 * @param $codePostalLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCodePostalLivraison($codePostalLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codePostalLivraison = $codePostalLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_CODE_POSTAL_LIVRAISON'),array($codePostalLivraison)) : true;
	}
	
	/**
	 * Récupérer le codeInseeLivraison
	 * @return string 
	 */
	public function getCodeInseeLivraison()
	{
		return $this->codeInseeLivraison;
	}
	
	/**
	 * Définir le codeInseeLivraison
	 * @param $codeInseeLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCodeInseeLivraison($codeInseeLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codeInseeLivraison = $codeInseeLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_CODE_INSEE_LIVRAISON'),array($codeInseeLivraison)) : true;
	}
	
	/**
	 * Récupérer la regionLivraison
	 * @return string 
	 */
	public function getRegionLivraison()
	{
		return $this->regionLivraison;
	}
	
	/**
	 * Définir la regionLivraison
	 * @param $regionLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setRegionLivraison($regionLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->regionLivraison = $regionLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_REGION_LIVRAISON'),array($regionLivraison)) : true;
	}
	
	/**
	 * Récupérer la municpaliteLivraison
	 * @return string 
	 */
	public function getMunicipaliteLivraison()
	{
		return $this->municpaliteLivraison;
	}
	
	/**
	 * Définir la municipaliteLivraison
	 * @param $munipaliteLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setMunicipaliteLivraison($municipaliteLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->municipaliteLivraison = $municipaliteLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_MUNICIPALITE_LIVRAISON'),array($municipaliteLivraison)) : true;
	}
	
	/**
	 * Récupérer la ligneAdresseLivraison
	 * @return string 
	 */
	public function getLigneAdresseLivraison()
	{
		return $this->ligneAdresseLivraison;
	}
	
	/**
	 * Définir la ligneAdresseLivraison
	 * @param $ligneAdresseLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setLigneAdresseLivraison($ligneAdresseLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->ligneAdresseLivraison = $ligenAdresseLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_LIGNE_ADRESSE_LIVRAISON'),array($ligneAdresseLivraison)) : true;
	}
	
	/**
	 * Récupérer le nomRueLivraison
	 * @return string 
	 */
	public function getNomRueLivraison()
	{
		return $this->nomRueLivraison;
	}
	
	/**
	 * Définir le nomRueLivraison
	 * @param $nomRueLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setNomRueLivraison($nomRueLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->nomRueLivraison = $nomRueLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_NOM_RUE_LIVRAISON'),array($nomRueLivraison)) : true;
	}
	
	/**
	 * Récupérer le numeroBatimentLivraison
	 * @return string 
	 */
	public function getNumeroBatimentLivraison()
	{
		return $this->numeroBatimentLivraison;
	}
	
	/**
	 * Définir le numeroBatimentLivraison
	 * @param $numeroBatimentLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setNumeroBatimentLivraison($numeroBatimentLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->numeroBatimentLivraison = $numeroBatimentLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_NUMERO_BATIMENT_LIVRAISON'),array($numeroBatimentLivraison)) : true;
	}
	
	/**
	 * Récupérer la uniteLivraison
	 * @return string 
	 */
	public function getUniteLivraison()
	{
		return $this->uniteLivraison;
	}
	
	/**
	 * Définir la uniteLivraison
	 * @param $uniteLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setUniteLivraison($uniteLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->uniteLivraison = $uniteLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_UNITE_LIVRAISON'),array($uniteLivraison)) : true;
	}
	
	/**
	 * Récupérer la boitePostaleLivraison
	 * @return string 
	 */
	public function getBoitePostaleLivraison()
	{
		return $this->boitePostaleLivraison;
	}
	
	/**
	 * Définir la boitePostaleLivraison
	 * @param $boitePostaleLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setBoitePostaleLivraison($boitePostaleLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->boitePostaleLivraison = $boitePostaleLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_BOITE_POSTALE_LIVRAISON'),array($boitePostaleLivraison)) : true;
	}
	
	/**
	 * Récupérer le destinataireLivraison
	 * @return string 
	 */
	public function getDestinataireLivraison()
	{
		return $this->destinataireLivraison;
	}
	
	/**
	 * Définir le destinataireLivraison
	 * @param $destinataireLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setDestinataireLivraison($destinataireLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->destinataireLivraison = $destinataireLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_DESTINATAIRE_LIVRAISON'),array($destinataireLivraison)) : true;
	}
	
	
	/**
	 * Récupérer le modeLivraison
	 * @return string 
	 */
	public function getModeLivraison()
	{
		return $this->modeLivraison;
	}
	
	/**
	 * Définir le modeLivraison
	 * @param $destinataireLivraison string 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setModeLivraison($modeLivraison,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->modeLivraison = $modeLivraison;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_MODE_LIVRAISON'),array($modeLivraison)) : true;
	}
	
	/**
	 * Récupérer la idClient
	 * @return Client 
	 */
	public function getClient()
	{
		// Retourner null si nécéssaire
		if ($this->idClient == null) { return null; }
		
		// Charger et retourner idClient
		return Client::load($this->pdo,$this->idClient);
	}
	
	/**
	 * Définir la idClient
	 * @param $idClient Client 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setClient($idClient=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idClient = $idClient == null ? null : $idClient->getIdClient();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_CLIENT'),array($idClient == null ? null : $idClient->getIdClient())) : true;
	}
	
	
	/**
	 * Sélectionner les ligne_commandes
	 * @return tableau de ligne_commande
	 */
	public function selectLigne_commandes($etage=null)
	{
		return Ligne_Commande::selectByCommande($this->pdo,$this,$etage);			
 
	}	
	
	/**
	 * Sélectionner les ligne_commandes pour une zone
	 * @return tableau de ligne_commande
	 */
	public function selectLigne_commandesByZone($zone,$excepts=array())
	{
		$pdoStatement = $this->pdo->prepare('	SELECT DISTINCT lc.*
												FROM COMMANDE c, LIGNE_COMMANDE lc, PRODUIT p, EST_GEOLOCALISE_DANS egd, ETAGERE e, SEGMENT s, RAYON r, ZONE z
												WHERE c.ID_COMMANDE = lc.ID_COMMANDE
												AND lc.ID_PRODUIT = p.ID_PRODUIT
												AND p.ID_PRODUIT = egd.ID_PRODUIT
												AND egd.ID_ETAGERE = e.ID_ETAGERE
												AND e.ID_SEGMENT = s.ID_SEGMENT
												AND s.ID_RAYON = r.ID_RAYON
												AND r.ID_ZONE = z.ID_ZONE
												AND c.ID_COMMANDE = ?
												AND z.ID_ZONE = ?');
												
		if (!$pdoStatement->execute(array($this->getIdCommande(),$zone->getIdzone()))) {
			throw new Exception('Erreur lors du chargement de toutes les commandes par client depuis la base de données');
		} 
		
		// Mettre chaque ligne_commande dans un tableau
		$ligne_commandes = array();
		while ($ligne_commande = Ligne_Commande::fetch($this->pdo,$pdoStatement)) {
			if ($excepts != null){
				$traite = false;
				foreach($excepts as $except){
					if ($ligne_commande->getProduit()->equals($except[0])){
						$traite = true;
						if ($except[1] == $zone->getIdzone()){
							$ligne_commandes[] = $ligne_commande;
							// echo 'Exception : ' . $ligne_commande->getProduit()->getLibelle() . '<br />';
						}
					}
				}
				
				if (!$traite){
					$traite = true;
					$ligne_commandes[] = $ligne_commande;
					// echo 'No Exception : ' . $ligne_commande->getProduit()->getIdProduit() . ' - ' . $ligne_commande->getProduit()->getLibelle() . '<br />';
				}	
			}
			else{
				$ligne_commandes[] = $ligne_commande;
				// echo 'Excepts is empty : ' . $ligne_commande->getProduit()->getIdProduit() . ' - ' . $ligne_commande->getProduit()->getLibelle() . '<br />';
			}
		}
	
		// Retourner le tableau
		return $ligne_commandes;
	}	
	
	/**
	 * Sélectionner les commandes en attente d'affectation
	 * @return tableau de commande
	 */
	public static function selectByState($pdo,$state)
	{
		$pdoStatement = Commande::_select($pdo,' CMD_ETAT_COMMANDE = ? ');				
		if (!$pdoStatement->execute(array($state))) {
			throw new Exception('Erreur lors du chargement d\'une commande depuis la base de données');
		}
		 
		$commandes = array();
		while ($commande = Commande::fetch($pdo,$pdoStatement)) {
			$commandes[] = $commande;
		}
		
		// Retourner le tableau
		return $commandes;
	}
	
	
	
	/**
	 * Sélectionner les commandes par client
	 * @param $pdo ownPDO 
	 * @param $client Client 
	 * @return PDOStatement 
	 */
	public static function selectByClient(ownPDO $pdo,Client $client)
	{
		$pdoStatement = $pdo->prepare('SELECT * FROM COMMANDE c WHERE c.ID_CLIENT = ?');
		if (!$pdoStatement->execute(array($client->getIdClient()))) {
			throw new Exception('Erreur lors du chargement de toutes les commandes par client depuis la base de données');
		}
		return $pdoStatement;
	}
	
	
	/**
	 * Définir la carteFidelite
	 * @param $carteFidelite int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCarteFidelite($carteFidelite,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->carteFidelite = $carteFidelite;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_CARTE_FIDELITE'),array($carteFidelite)) : true;
	}
	
	/**
	 * Récupérer la carteFidelite
	 * @return int 
	 */
	public function getCarteFidelite()
	{
		return $this->carteFidelite;
	}	
	/**
	 * Définir la commentaireClient
	 * @param $commentaireClient int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCommentaireClient($commentaireClient,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->commentaireClient = $commentaireClient;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('CMD_COMMENTAIRE_CLIENT'),array($commentaireClient)) : true;
	}
	
	/**
	 * Récupérer la commentaireClient
	 * @return int 
	 */
	public function getCommentaireClient()
	{
		return $this->commentaireClient;
	}
	
	public static function getEtagesCommande($pdo, $idCommande){
		$req = 'SELECT eta.ID_ETAGE, eta.ETA_LIBELLE, eta.ETA_HAUTEUR, eta.ETA_LARGEUR, eta.ETA_PT_DEPART_TOP, eta.ETA_PT_DEPART_LEFT, eta.ETA_PT_ARRIVE_TOP, eta.ETA_PT_ARRIVE_LEFT, eta.ETA_PRIORITE
				FROM COMMANDE c, LIGNE_COMMANDE lc, PRODUIT p, EST_GEOLOCALISE_DANS egd, ETAGERE e, SEGMENT s, RAYON r, ZONE z, ETAGE eta
				WHERE c.ID_COMMANDE = ?
				AND lc.ID_COMMANDE = c.ID_COMMANDE
				AND p.ID_PRODUIT = lc.ID_PRODUIT
				AND egd.ID_PRODUIT = p.ID_PRODUIT
				AND e.ID_ETAGERE = egd.ID_ETAGERE
				AND s.ID_SEGMENT = e.ID_SEGMENT
				AND r.ID_RAYON = s.ID_RAYON
				AND z.ID_ZONE = r.ID_ZONE
				AND eta.ID_ETAGE = z.ID_ETAGE
				GROUP BY eta.ID_ETAGE';
		$pdoStatement = $pdo->prepare($req);
		if (!$pdoStatement->execute(array($idCommande))) {
			throw new Exception('Erreur lors du chargement d\'une commande depuis la base de données');
		}
		
		// Mettre chaque etage dans un tableau
		$etages = array();
		while ($etage = Etage::fetch($pdo,$pdoStatement)) {
			$etages[] = $etage;
		}
		
		// Retourner le tableau
		return $etages;
	}
	
	public static function getZonesCommande($pdo, $idCommande,$idetage=0){
				
		$req = 'SELECT z.ID_ZONE, z.ID_ETAGE, z.ZON_LIBELLE, z.ZON_COULEUR, z.ZON_PRIORITE
				FROM COMMANDE c, LIGNE_COMMANDE lc, PRODUIT p, EST_GEOLOCALISE_DANS egd, ETAGERE e, SEGMENT s, RAYON r, ZONE z
				WHERE c.ID_COMMANDE = ?
				AND lc.ID_COMMANDE = c.ID_COMMANDE
				AND p.ID_PRODUIT = lc.ID_PRODUIT
				AND egd.ID_PRODUIT = p.ID_PRODUIT
				AND e.ID_ETAGERE = egd.ID_ETAGERE
				AND s.ID_SEGMENT = e.ID_SEGMENT
				AND r.ID_RAYON = s.ID_RAYON
				AND z.ID_ZONE = r.ID_ZONE';
		if($idetage != 0)
			$req .= ' AND z.ID_ETAGE = ? ';
		
			$req .=	' GROUP BY z.ID_ZONE';
		$pdoStatement = $pdo->prepare($req);
		
		if($idetage == 0){
			if (!$pdoStatement->execute(array($idCommande))) {
				throw new Exception('Erreur lors du chargement d\'une commande depuis la base de données');
			}
		}
		else{
			if (!$pdoStatement->execute(array($idCommande,$idetage))) {
				throw new Exception('Erreur lors du chargement d\'une commande depuis la base de données');
			}
		}
		$zones = array();

		// Mettre chaque etage dans un tableau
		while ($zone = Zone::fetch($pdo,$pdoStatement)) {
			$zones[] = $zone;
		}
		
		$commande = self::load($pdo,$idCommande,false);
		$addZone = false; 
		foreach($commande -> selectLigne_commandes() as $ligne){
			$produit 	= $ligne->getProduit();
			if(count($produit->selectEtageres()) == 0){
				$addZone = true;
				break;
			}
		}
		if($addZone){
			$zones[] = Zone::loadZoneMagasinByEtage($pdo, $idetage);
		}
		// Retourner le tableau
		return $zones;
	}
	
	public static function getMinDateLivraison($pdo, $arrayCommandes){
		$req =' SELECT MIN(c.CMD_DATE_LIVRAISON) as min
				FROM COMMANDE c
				WHERE  (';
			foreach($arrayCommandes as $commande){
					$req .= ' c.ID_COMMANDE = ' . $commande . ' OR';
			}
			$req = substr($req, 0, -2); //pour enlever le OR supplémentaire
			$req .= ')';
			
			
		$pdoStatement = $pdo->prepare($req);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement d\'une commande depuis la base de données');
		}
		
		return $pdoStatement->fetch(PDO::FETCH_ASSOC); 
	}
	
	
 
	public function isPrepared(){
		$pdoStatement = $this->pdo->prepare('	SELECT c.ID_COMMANDE, lc.ID_PREPARATION FROM COMMANDE c, LIGNE_COMMANDE lc, PREPARATION p 
												WHERE c.ID_COMMANDE = ?
												AND c.ID_COMMANDE = lc.ID_COMMANDE
												AND ((lc.ID_PREPARATION = p.ID_PREPARATION
												AND p.PREPA_ETAT != 2) OR (lc.ID_PREPARATION IS NULL))												
												GROUP BY c.ID_COMMANDE');
										
		if (!$pdoStatement->execute(array($this->getIdcommande()))) {
			throw new Exception('Erreur lors du chargement de toutes les commandes par client depuis la base de données');
		}
		
		return $pdoStatement->rowCount() == 0;
	}
	
	public function isNotWaiting(){
		$pdoStatement = $this->pdo->prepare('	SELECT c.ID_COMMANDE, lc.ID_PREPARATION FROM COMMANDE c, LIGNE_COMMANDE lc, PREPARATION p 
												WHERE c.ID_COMMANDE = ?
												AND c.ID_COMMANDE = lc.ID_COMMANDE
												AND lc.ID_PREPARATION = p.ID_PREPARATION
												AND p.PREPA_ETAT = 0
												GROUP BY c.ID_COMMANDE');
										
		if (!$pdoStatement->execute(array($this->getIdcommande()))) {
			throw new Exception('Erreur lors du chargement de toutes les commandes par client depuis la base de données');
		}
		
		return $pdoStatement->rowCount() == 0;
	}
	
	public function isAllAffected(){
		$pdoStatement = $this->pdo->prepare('	SELECT c.ID_COMMANDE FROM COMMANDE c, LIGNE_COMMANDE lc 
												WHERE c.ID_COMMANDE = ?
												AND c.ID_COMMANDE = lc.ID_COMMANDE
												AND lc.ID_PREPARATION is NULL
												GROUP BY c.ID_COMMANDE');
										
		if (!$pdoStatement->execute(array($this->getIdcommande()))) {
			throw new Exception('Erreur lors du chargement de toutes les commandes par client depuis la base de données');
		}
		
		return $pdoStatement->rowCount() == 0;
	}
	
	public function getNbArticles(){
		$pdoStatement = $this->pdo->prepare('	SELECT SUM(lc.LC_QUANTITE_COMMANDEE) as nbArticles
												FROM LIGNE_COMMANDE lc
												WHERE ID_COMMANDE = ?');
		
		if (!$pdoStatement->execute(array($this->getIdcommande()))) {
			throw new Exception('Erreur lors du chargement de toutes les commandes par client depuis la base de données');
		}
		
		$nbArticles = $pdoStatement->fetch(PDO::FETCH_ASSOC); 
		
		return $nbArticles['NBARTICLES'];
	}
	
	public static function getListeTypeLivraison($pdo){
		$req = 'SELECT DISTINCT CMD_MODE_LIVRAISON 
				FROM COMMANDE';
		$pdoStatement = $pdo->prepare($req);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement d\'une commande depuis la base de données');
		} 
		
		$array_TypeLivraison = array();
		while ($type = $pdoStatement->fetch(PDO::FETCH_NUM)) {
			$array_TypeLivraison[] = $type[0];
		}
		return $array_TypeLivraison; 
	}
	
	
	public static function vider(PDO $pdo){
		$pdoStatement = $pdo->prepare('DELETE FROM TEMPS_PREPARATION');
 		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des commandes dans la base de données');
		}
		
		$pdoStatement = $pdo->prepare('UPDATE PRELEVEMENT_REALISE SET ID_PRELEVEMENT_PRECEDENT = NULL');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des commandes dans la base de données');
		}
		
		$pdoStatement = $pdo->prepare('DELETE FROM PRELEVEMENT_REALISE');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des commandes dans la base de données');
		}
		
		$pdoStatement = $pdo->prepare('DELETE FROM LIGNE_COMMANDE');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des commandes dans la base de données');
		}
		
		$pdoStatement = $pdo->prepare('DELETE FROM PREPARATION');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des commandes dans la base de données');
		}
		$pdoStatement = $pdo->prepare('DELETE FROM COMMANDE');
		if (!$pdoStatement->execute()){
			throw new Exception('Erreur lors du vidage des commandes dans la base de données');
		}
		return true;
	}
		
	/**
	 * Récupérer Archive Commande
	 * @return int 
	 */
	public function getArchiveCommande()
	{
		return $this->archiveCommande;
	}
	
	/**
	 * Définir l'archiveCommande
	 * @param $archiveCommande var 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setArchiveCommande(ownPDO $pdo,$idCommande,$archiveCommande,$execute=true)
	{
		$pdoStatement = $pdo->prepare('UPDATE COMMANDE SET CMD_ARCHIVEE= ? WHERE ID_COMMANDE= ?;');
					
		if (!$pdoStatement->execute(array($archiveCommande, $idCommande))) {
			throw new Exception('Erreur lors de la mise &agrave; jour du statut Archivage');
		}			
		
		return true;
	}
}

?>