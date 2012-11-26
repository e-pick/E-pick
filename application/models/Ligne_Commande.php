<?php
class Ligne_Commande
{
	/// @var ownPDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idLigne;
	
	/// @var int id de produit
	private $idProduit;
	
	/// @var int id de commande
	private $idCommande;
	
	/// @var int id de preparation
	private $idPreparation;
		
	/// @var int 
	private $quantiteCommandee;
	
	/// @var int 
	private $estDansUnLot;
	
	/// @var int 
	private $idLot;
	
	/// @var string
	private $libelleLot;
	
	/// @var int 
	private $codeEanLot;
	
	/// @var double
	private $prixUnitaireTTC;
	
	
	
	/**
	 * Construire une ligne_commande
	 * @param $pdo ownPDO 
	 * @param $idLigne int
	 * @param $idProduit int id de produit
	 * @param $idPreparation int id de preparation
	 * @param $idCommande int id de commande
	 * @param $quantiteCommandee int
	 * @param $estDansUnLot int
	 * @param $idLot int
	 * @param $libelleLot string
	 * @param $codeEanLot int
	 * @param $prixUnitaireTTC 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ligne_Commande 
	 */
	protected function __construct(ownPDO $pdo,$idLigne,$idProduit,$idCommande,$idPreparation=null,$quantiteCommandee,$estDansUnLot,$idLot,$libelleLot,$codeEanLot,$prixUnitaireTTC,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idLigne = $idLigne;
		$this->idProduit = $idProduit;
		$this->idCommande = $idCommande;
		$this->quantiteCommandee = $quantiteCommandee;
		$this->estDansUnLot = $estDansUnLot;
		$this->idLot = $idLot;
		$this->libelleLot = $libelleLot;
		$this->codeEanLot = $codeEanLot;
		$this->prixUnitaireTTC = $prixUnitaireTTC;
		$this->idPreparation = $idPreparation;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Ligne_Commande::$easyload[$idLigne] = $this;
		}
	}
	
	/**
	 * Créer une ligne_commande
	 * @param $pdo ownPDO 
	 * @param $quantite int 
	 * @param $idProduit Produit 
	 * @param $idPreparation Preparation 
	 * @param $idCommande Commande 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ligne_Commande 
	 */
	public static function create(ownPDO $pdo,$idProduit,$idCommande,$idPreparation=null,$quantiteCommandee,$estDansUnLot,$idLot,$libelleLot,$codeEanLot,$prixUnitaireTTC,$easyload=true)
	{
		// Ajouter la ligne_commande dans la base de données
		$pdoStatement = $pdo->prepare('INSERT INTO LIGNE_COMMANDE (ID_PRODUIT,ID_COMMANDE,ID_PREPARATION,LC_QUANTITE_COMMANDEE,LC_EST_DANS_UN_LOT,LC_ID_LOT,LC_LIBELLE_LOT,LC_CODE_EAN_LOT,LC_PRIX_UNITAIRE_TTC) VALUES (?,?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($idProduit,$idCommande,$idPreparation,$quantiteCommandee,$estDansUnLot,$idLot,$libelleLot,$codeEanLot,$prixUnitaireTTC))) {
			throw new Exception('Erreur durant l\'insertion d\'une ligne_commande dans la base de données');
		}
		
		// Construire la ligne_commande
		return new Ligne_Commande($pdo,$pdo->lastInsertId(),$idProduit,$idCommande,$idPreparation,$quantiteCommandee,$estDansUnLot,$idLot,$libelleLot,$codeEanLot,$prixUnitaireTTC,$easyload);
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
		$req = 'SELECT * FROM LIGNE_COMMANDE l '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDER BY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : '');
		return $pdo->prepare($req);
	}
	
	/**
	 * Charger une ligne_commande
	 * @param $pdo ownPDO 
	 * @param $idLigne int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ligne_Commande 
	 */
	public static function load(ownPDO $pdo,$idLigne,$easyload=true)
	{
		// Déjà chargée ?
		if (isset(Ligne_Commande::$easyload[$idLigne])) {
			return Ligne_Commande::$easyload[$idLigne];
		}
		
		// Charger la ligne_commande
		$pdoStatement = Ligne_Commande::_select($pdo,'l.ID_LIGNE = ?');
		if (!$pdoStatement->execute(array($idLigne))) {
			throw new Exception('Erreur lors du chargement d\'une ligne_commande depuis la base de données');
		}
		
		// Récupérer la ligne_commande depuis le jeu de résultats
		return Ligne_Commande::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger toutes les ligne_commandes
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ligne_Commande[] tableau de ligne_commandes
	 */
	public static function loadAll(ownPDO $pdo,$easyload=false)
	{
		// Sélectionner toutes les ligne_commandes
		$pdoStatement = Ligne_Commande::selectAll($pdo);
		
		// Mettre chaque ligne_commande dans un tableau
		$ligne_commandes = array();
		while ($ligne_commande = Ligne_Commande::fetch($pdo,$pdoStatement,$easyload)) {
			$ligne_commandes[] = $ligne_commande;
		}
		
		// Retourner le tableau
		return $ligne_commandes;
	}
	
	
	/**
	 * Charger toutes les ligne_commandes sans preparation
	 * @param $pdo ownPDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ligne_Commande[] tableau de ligne_commandes
	 */
	public static function selectNullByCommande(ownPDO $pdo,$commande,$easyload=false)
	{
		// Sélectionner toutes les ligne_commandes
		$pdoStatement = Ligne_Commande::_select($pdo,'ID_COMMANDE = ' . $commande->getIdCommande() . ' AND ID_PREPARATION IS NULL ');
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement d\'une ligne_commande depuis la base de données');
		}
		// Mettre chaque ligne_commande dans un tableau
		$ligne_commandes = array();
		while ($ligne_commande = Ligne_Commande::fetch($pdo,$pdoStatement,$easyload)) {
			$ligne_commandes[] = $ligne_commande;
		}
		
		// Retourner le tableau
		return $ligne_commandes;
	}
	
	/**
	 * Sélectionner tous/toutes les ligne_commandes
	 * @param $pdo ownPDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(ownPDO $pdo)
	{
		$pdoStatement = Ligne_Commande::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les ligne_commandes depuis la base de données');
		}
		return $pdoStatement;
	}
	
	/**
	 * Récupère la ligne_commande suivante d'un jeu de résultats
	 * @param $pdo ownPDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Ligne_Commande 
	 */
	public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idLigne,$idProduit,$idCommande,$idPreparation,$quantiteCommandee,$estDansUnLot,$idLot,$libelleLot,$codeEanLot,$prixUnitaireTTC) = $values;
		
		// Construire la ligne_commande
		return isset(Ligne_Commande::$easyload[$idLigne]) ? Ligne_Commande::$easyload[$idLigne] :
		       new Ligne_Commande($pdo,$idLigne,$idProduit,$idCommande,$idPreparation,$quantiteCommandee,$estDansUnLot,$idLot,$libelleLot,$codeEanLot,$prixUnitaireTTC,$easyload);
	}
 
	/**
	 * Supprimer la ligne_commande
	 * @return bool opération réussie ?
	 */
	public function delete()
	{
		// Supprimer la ligne_commande
		$pdoStatement = $this->pdo->prepare('DELETE FROM LIGNE_COMMANDE WHERE ID_LIGNE = ?');
		if (!$pdoStatement->execute(array($this->getIdLigne()))) {
			throw new Exception('Erreur lors de la supression d\'une ligne_commande dans la base de données');
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
		$pdoStatement = $this->pdo->prepare('UPDATE LIGNE_COMMANDE SET '.implode(', ', $updates).' WHERE ID_LIGNE = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdLigne())))) {
			throw new Exception('Erreur lors de la mise à jour d\'un champ d\'une ligne_commande dans la base de données');
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
		return $this->_set(array('ID_PRODUIT','ID_COMMANDE','LC_QUANTITE_COMMANDEE','LC_EST_DANS_UN_LOT','LC_ID_LOT','LC_LIBELLE_LOT','LC_CODE_EAN_LOT','LC_PRIX_UNITAIRE_TTC','ID_PREPARATION'),
						   array($this->idProduit,$this->quantiteCommandee,$this->estDansUnLot,$this->idLot,$this->libelleLot,$this->codeEanLot,$this->prixUnitaireTTC,$this->idCommande,$this->idPreparation));
	}
	
	public function __toString(){
		return '[' . $this->idLigne . ']';
	}
	
	/**
	 * Récupérer le idLigne
	 * @return int 
	 */
	public function getIdLigne()
	{
		return $this->idLigne;
	}
	
	/**
	 * Récupérer le produit
	 * @return Produit 
	 */
	public function getProduit()
	{
		// Retourner null si nécéssaire
		if ($this->idProduit == null) { return null; }
		
		// Charger et retourner produit
		return Produit::load($this->pdo,$this->idProduit);
	}
	
	/**
	 * Définir le produit
	 * @param $idProduit Produit 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setIdProduit($idProduit=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idProduit = $idProduit == null ? null : $idProduit->getIdProduit();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_PRODUIT'),array($idProduit == null ? null : $idProduit->getIdProduit())) : true;
	}
	
	/**
	 * Sélectionner les ligne_commandes par produit
	 * @param $pdo ownPDO 
	 * @param $idProduit Produit 
	 * @return PDOStatement 
	 */
	public static function selectByProduit(ownPDO $pdo,Produit $produit)
	{
		$pdoStatement = $pdo->prepare('SELECT * FROM LIGNE_COMMANDE l WHERE l.ID_PRODUIT = ?');
		if (!$pdoStatement->execute(array($produit->getIdProduit()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les ligne_commandes par produit depuis la base de données');
		}
		// Mettre chaque ligne_commande dans un tableau
		$ligne_commandes = array();
		while ($ligne_commande = Ligne_Commande::fetch($pdo,$pdoStatement)) {
			$ligne_commandes[] = $ligne_commande;
		}
		
		// Retourner le tableau
		return $ligne_commandes;
	}
	
 
	
	/**
	 * Récupérer la commande
	 * @return Commande 
	 */
	public function getCommande()
	{
		// Retourner null si nécéssaire
		if ($this->idCommande == null) { return null; }
		
		// Charger et retourner commande
		return Commande::load($this->pdo,$this->idCommande);
	}
	
	/**
	 * Définir la commande
	 * @param $idCommande Commande 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCommande($idCommande=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idCommande = $idCommande == null ? null : $idCommande->getIdCommande();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_COMMANDE'),array($idCommande == null ? null : $idCommande->getIdCommande())) : true;
	}
	
	/**
	 * Sélectionner les ligne_commandes par commande
	 * @param $pdo ownPDO 
	 * @param $idCommande Commande 
	 * @return PDOStatement 
	 */
	public static function selectByCommande(ownPDO $pdo,Commande $idCommande, Etage $etage=null)
	{
		if($etage != null){
			$pdoStatement = $pdo->prepare('
			SELECT lc.*
				FROM LIGNE_COMMANDE lc, PRODUIT p, EST_GEOLOCALISE_DANS egd, ETAGERE e, SEGMENT s, RAYON r, ZONE z, ETAGE eta
				WHERE lc.ID_COMMANDE = ?
				AND p.ID_PRODUIT = lc.ID_PRODUIT
				AND egd.ID_PRODUIT = p.ID_PRODUIT
				AND e.ID_ETAGERE = egd.ID_ETAGERE
				AND s.ID_SEGMENT = e.ID_SEGMENT
				AND r.ID_RAYON = s.ID_RAYON
				AND z.ID_ZONE = r.ID_ZONE
				AND eta.ID_ETAGE = z.ID_ETAGE
				AND eta.ID_ETAGE = ?
			');
			if (!$pdoStatement->execute(array($idCommande->getIdCommande(),$etage->getIdetage()))) {
				throw new Exception('Erreur lors du chargement de tous/toutes les ligne_commandes par commande depuis la base de données');
			}
		}
		else{
			$pdoStatement = $pdo->prepare('SELECT * FROM LIGNE_COMMANDE l WHERE l.ID_COMMANDE = ?');
			if (!$pdoStatement->execute(array($idCommande->getIdCommande()))) {
				throw new Exception('Erreur lors du chargement de tous/toutes les ligne_commandes par commande depuis la base de données');
			}
		}
		// Mettre chaque ligne_commande dans un tableau
		$ligne_commandes = array();
		while ($ligne_commande = Ligne_Commande::fetch($pdo,$pdoStatement)) {
			$ligne_commandes[] = $ligne_commande;
		}
		
		// Retourner le tableau
		return $ligne_commandes;
	}
	
	/**
	 * Récupérer la preparation
	 * @return Preparation 
	 */
	public function getPreparation()
	{
		// Retourner null si nécéssaire
		if ($this->idPreparation == null) { return null; }
		
		// Charger et retourner preparation
		return Preparation::load($this->pdo,$this->idPreparation);
	}
	
	/**
	 * Définir la preparation
	 * @param $idPreparation Preparation 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setPreparation($preparation=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idPreparation = ($preparation == null) ? null : $preparation->getIdPreparation();
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('ID_PREPARATION'),array($preparation == null ? null : $preparation->getIdPreparation())) : true;
	}
	
	/**
	 * Sélectionner les ligne_commandes par preparation
	 * @param $pdo ownPDO 
	 * @param $preparation Preparation 
	 * @return lsite de ligne de commande 
	 */
	public static function selectByPreparation(ownPDO $pdo,Preparation $preparation)
	{
		$pdoStatement = $pdo->prepare('SELECT * FROM LIGNE_COMMANDE l WHERE l.ID_PREPARATION = ?');
		if (!$pdoStatement->execute(array($preparation->getIdPreparation()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les ligne_commandes par preparation depuis la base de données');
		}
		// Mettre chaque ligne_commande dans un tableau
		$ligne_commandes = array();
		while ($ligne_commande = Ligne_Commande::fetch($pdo,$pdoStatement)) {
			$ligne_commandes[] = $ligne_commande;
		}
		
		// Retourner le tableau
		return $ligne_commandes;
	}
	
	public static function selectByCommandeAndEtage(ownPDO $pdo, $idCommande, $idEtage, $excepts){
	
		$pdoStatement = $pdo->prepare('	SELECT lc.ID_LIGNE, lc.ID_PRODUIT,lc.ID_COMMANDE,lc.ID_PREPARATION,lc.LC_QUANTITE_COMMANDEE,lc.LC_EST_DANS_UN_LOT,lc.LC_ID_LOT,lc.LC_LIBELLE_LOT,lc.LC_CODE_EAN_LOT,lc.LC_PRIX_UNITAIRE_TTC
										FROM LIGNE_COMMANDE lc, PRODUIT p, EST_GEOLOCALISE_DANS egd, ETAGERE e, SEGMENT s, RAYON r, ZONE z, ETAGE eta 
										WHERE lc.ID_COMMANDE = ?
										AND lc.ID_PRODUIT = p.ID_PRODUIT
										AND p.ID_PRODUIT = egd.ID_PRODUIT
										AND egd.ID_ETAGERE = e.ID_ETAGERE
										AND e.ID_SEGMENT = s.ID_SEGMENT
										AND s.ID_RAYON = r.ID_RAYON
										AND r.ID_ZONE = z.ID_ZONE
										AND z.ID_ETAGE = eta.ID_ETAGE
										AND eta.ID_ETAGE = ?');
		
		if (!$pdoStatement->execute(array($idCommande, $idEtage))) {
			throw new Exception('Erreur lors du chargement de toutes les ligne_commandes par commande et étage depuis la base de données');
		}
		// Mettre chaque ligne_commande dans un tableau
		$ligne_commandes = array();
		while ($ligne_commande = Ligne_Commande::fetch($pdo,$pdoStatement)) {
			if ($excepts != null){
				$traite = false;
				foreach($excepts as $except){
					if ($ligne_commande->getProduit()->equals($except[0])){
						$traite = true;
						if ($except[1] == $idEtage){
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
	 * Récupérer la quantiteCommandee
	 * @return int 
	 */
	public function getQuantiteCommandee()
	{
		return $this->quantiteCommandee;
	}
	
	/**
	 * Définir la quantiteCommandee
	 * @param $quantiteCommandee int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setQuantiteCommandee($quantiteCommandee,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->quantiteCommandee = $quantiteCommandee;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('LC_QUANTITE_COMMANDEE'),array($quantiteCommandee)) : true;
	}
	
	
	
 
	/**
	 * Récupérer le estDansUnLot
	 * @return int 
	 */
	public function getEstDansUnLot()
	{
		return $this->estDansUnLot;
	}
	
	/**
	 * Définir le estDansUnLot
	 * @param $estDansUnLot int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setEstDansUnLot($estDansUnLot,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->estDansUnLot = $estDansUnLot;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('LC_EST_DANS_UN_LOT'),array($estDansUnLot)) : true;
	}
	
	/**
	 * Récupérer le idLot
	 * @return int 
	 */
	public function getIdLot()
	{
		return $this->idLot;
	}
	
	/**
	 * Définir le idLot
	 * @param $idLot int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setIdLot($idLot,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->idLot = $idLot;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('LC_ID_LOT'),array($idLot)) : true;
	}
	
	/**
	 * Récupérer le libelleLot
	 * @return int 
	 */
	public function getLibelleLot()
	{
		return $this->libelleLot;
	}
	
	/**
	 * Définir le libelleLot
	 * @param $libelleLot int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setLibelleLot($libelleLot,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->libelleLot = $libelleLot;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('LC_LIBELLE_LOT'),array($libelleLot)) : true;
	}
	
	/**
	 * Récupérer le codeEanLot
	 * @return int 
	 */
	public function getCodeEanLot()
	{
		return $this->codeEanLot;
	}
	
	/**
	 * Définir le codeEanLot
	 * @param $codeEanLot int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setCodeEanLot($codeEanLot,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->codeEanLot = $codeEanLot;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('LC_CODE_EAN_LOT'),array($codeEanLot)) : true;
	}
	
	/**
	 * Récupérer le prixUnitaireTTC
	 * @return int 
	 */
	public function getPrixUnitaireTTC()
	{
		return $this->prixUnitaireTTC;
	}
	
	/**
	 * Définir le prixUnitaireTTC
	 * @param $prixUnitaireTTC int 
	 * @param $execute bool exécuter la requête update ?
	 * @return bool opération réussie ?
	 */
	public function setPrixUnitaireTTC($prixUnitaireTTC,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->prixUnitaireTTC = $prixUnitaireTTC;
		
		// Sauvegarder dans la base de données (ou pas)
		return $execute ? $this->_set(array('LC_PRIX_UNITAIRE_TTC'),array($prixUnitaireTTC)) : true;
	}
	
}

?>