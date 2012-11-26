<?php

class Prelevement_Realise
{
	/// @var PDO 
	private $pdo;
	
	/// @var array tableau pour le chargement rapide
	private static $easyload;
	
	/// @var int 
	private $idPrelevement_realise;
	
	/// @var int id de ligne_commande
	private $ligne_commande;
	
	/// @var int id de ancien
	private $ancien;
	
	/// @var int 
	private $temps;
	
	/// @var int 
	private $distance;
	
	/// @var int 
	private $quantite_prelevee;
	
	/// @var int 
	private $ean_lu;
	
	/// @var float 
	private $prix_unitaire_ttc_lu;
	

	private $modePreparation;
	
	/**
	 * Construire un(e) prelevement_realise
	 * @param $pdo PDO 
	 * @param $idPrelevement_realise int 
	 * @param $temps int 
	 * @param $quantite_prelevee int 
	 * @param $ean_lu int 
	 * @param $prix_unitaire_ttc_lu float 
	 * @param $ligne_commande int id de ligne_commande
	 * @param $ancien int id de ancien
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Prelevement_Realise 
	 */
	protected function __construct(PDO $pdo,$idPrelevement_realise,$ligne_commande,$ancien=null,$temps,$distance,$quantite_prelevee,$ean_lu,$prix_unitaire_ttc_lu,$modePreparation,$easyload=false)
	{
		// Sauvegarder pdo
		$this->pdo = $pdo;
		
		// Sauvegarder les attributs
		$this->idPrelevement_realise = $idPrelevement_realise;
		$this->temps = $temps;
		$this->distance = $distance;
		$this->quantite_prelevee = $quantite_prelevee;
		$this->ean_lu = $ean_lu;
		$this->prix_unitaire_ttc_lu = $prix_unitaire_ttc_lu;
		$this->ligne_commande = $ligne_commande;
		$this->ancien = $ancien;
		$this->modePreparation = $modePreparation;
		
		// Sauvegarder pour le chargement rapide
		if ($easyload) {
			Prelevement_Realise::$easyload[$idPrelevement_realise] = $this;
		}
	}
	
	/**
	 * Cr�er un(e) prelevement_realise
	 * @param $pdo PDO 
	 * @param $temps int 
	 * @param $quantite_prelevee int 
	 * @param $ean_lu int 
	 * @param $prix_unitaire_ttc_lu float 
	 * @param $ligne_commande Ligne_Commande 
	 * @param $ancien Prelevement_Realise 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Prelevement_Realise 
	 */
	public static function create(PDO $pdo,Ligne_Commande $ligne_commande,$ancien,$temps,$distance,$quantite_prelevee,$ean_lu,$prix_unitaire_ttc_lu,$modePreparation,$easyload=true)
	{
		// Ajouter le/la prelevement_realise dans la base de donn�es
		$pdoStatement = $pdo->prepare('INSERT INTO PRELEVEMENT_REALISE (ID_LIGNE,ID_PRELEVEMENT_PRECEDENT,PREL_TEMPS,PREL_DISTANCE,PREL_QUANTITE_PRELEVEE,PREL_EAN_LU,PREL_PRIX_UNITAIRE_TTC_LU,PREL_MODE_PREPARATION) VALUES (?,?,?,?,?,?,?,?)');
		if (!$pdoStatement->execute(array($ligne_commande->getIdLigne(),$ancien == null ? null : $ancien->getIdprelevement_realise(),$temps,$distance,$quantite_prelevee,$ean_lu,$prix_unitaire_ttc_lu,$modePreparation))) {
			throw new Exception('Erreur durant l\'insertion d\'un(e) prelevement_realise dans la base de donn�es');
		}
		
		// Construire le/la prelevement_realise
		return new Prelevement_Realise($pdo,$pdo->lastInsertId(),$ligne_commande->getIdLigne(),$ancien == null ? null : $ancien->getIdprelevement_realise(),$temps,$distance,$quantite_prelevee,$ean_lu,$prix_unitaire_ttc_lu,$modePreparation,$easyload);
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
		return $pdo->prepare('SELECT p.ID_PRELEVEMENT_REALISE, p.ID_LIGNE, p.ID_PRELEVEMENT_PRECEDENT, p.PREL_TEMPS, p.PREL_DISTANCE, p.PREL_QUANTITE_PRELEVEE, p.PREL_EAN_LU, p.PREL_PRIX_UNITAIRE_TTC_LU, p.PREL_MODE_PREPARATION FROM PRELEVEMENT_REALISE p '.
		                     ($where != null ? ' WHERE '.$where : '').
		                     ($orderby != null ? ' ORDERBY '.$orderby : '').
		                     ($limit != null ? ' LIMIT '.$limit : ''));
	}
	
	/**
	 * Charger un(e) prelevement_realise
	 * @param $pdo PDO 
	 * @param $idPrelevement_realise int 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Prelevement_Realise 
	 */
	public static function load(PDO $pdo,$idPrelevement_realise,$easyload=true)
	{
		// D�j� charg�(e) ?
		if (isset(Prelevement_Realise::$easyload[$idPrelevement_realise])) {
			return Prelevement_Realise::$easyload[$idPrelevement_realise];
		}
		
		// Charger le/la prelevement_realise
		$pdoStatement = Prelevement_Realise::_select($pdo,'p.ID_PRELEVEMENT_REALISE = ?');
		if (!$pdoStatement->execute(array($idPrelevement_realise))) {
			throw new Exception('Erreur lors du chargement d\'un(e) prelevement_realise depuis la base de donn�es');
		}
		
		// R�cup�rer le/la prelevement_realise depuis le jeu de r�sultats
		return Prelevement_Realise::fetch($pdo,$pdoStatement,$easyload);
	}
	
	/**
	 * Charger tous/toutes les prelevement_realises
	 * @param $pdo PDO 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Prelevement_Realise[] tableau de prelevement_realises
	 */
	public static function loadAll(PDO $pdo,$easyload=false)
	{
		// S�lectionner tous/toutes les prelevement_realises
		$pdoStatement = Prelevement_Realise::selectAll($pdo);
		
		// Mettre chaque prelevement_realise dans un tableau
		$prelevement_realises = array();
		while ($prelevement_realise = Prelevement_Realise::fetch($pdo,$pdoStatement,$easyload)) {
			$prelevement_realises[] = $prelevement_realise;
		}
		
		// Retourner le tableau
		return $prelevement_realises;
	}
	
	/**
	 * S�lectionner tous/toutes les prelevement_realises
	 * @param $pdo PDO 
	 * @return PDOStatement 
	 */
	public static function selectAll(PDO $pdo)
	{
		$pdoStatement = Prelevement_Realise::_select($pdo);
		if (!$pdoStatement->execute()) {
			throw new Exception('Erreur lors du chargement de tous/toutes les prelevement_realises depuis la base de donn�es');
		}
		return $pdoStatement;
	}
	
	/**
	 * R�cup�re le/la prelevement_realise suivant(e) d'un jeu de r�sultats
	 * @param $pdo PDO 
	 * @param $pdoStatement PDOStatement 
	 * @param $easyload bool activer le chargement rapide ?
	 * @return Prelevement_Realise 
	 */
	public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$easyload=false)
	{
		// Extraire les valeurs
		$values = $pdoStatement->fetch();
		if (!$values) { return null; }
		list($idPrelevement_realise,$ligne_commande,$ancien,$temps,$distance,$quantite_prelevee,$ean_lu,$prix_unitaire_ttc_lu,$modePreparation) = $values;
		
		// Construire le/la prelevement_realise
		return isset(Prelevement_Realise::$easyload[$idPrelevement_realise]) ? Prelevement_Realise::$easyload[$idPrelevement_realise] :
		       new Prelevement_Realise($pdo,$idPrelevement_realise,$ligne_commande,$ancien,$temps,$distance,$quantite_prelevee,$ean_lu,$prix_unitaire_ttc_lu,$modePreparation,$easyload);
	}
	
	/**
	 * Supprimer le/la prelevement_realise
	 * @return bool op�ration r�ussie ?
	 */
	public function delete()
	{
		// Supprimer le/la prelevement_realise
		$pdoStatement = $this->pdo->prepare('DELETE FROM PRELEVEMENT_REALISE WHERE ID_PRELEVEMENT_REALISE = ?');
		if (!$pdoStatement->execute(array($this->getIdprelevement_realise()))) {
			throw new Exception('Erreur lors de la supression d\'un(e) prelevement_realise dans la base de donn�es');
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
		$pdoStatement = $this->pdo->prepare('UPDATE PRELEVEMENT_REALISE SET '.implode(', ', $updates).' WHERE ID_PRELEVEMENT_REALISE = ?');
		if (!$pdoStatement->execute(array_merge($values,array($this->getIdprelevement_realise())))) {
			throw new Exception('Erreur lors de la mise � jour d\'un champ d\'un(e) prelevement_realise dans la base de donn�es');
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
		return $this->_set(array('PREL_TEMPS','PREL_DISTANCE','PREL_QUANTITE_PRELEVEE','PREL_EAN_LU','PREL_PRIX_UNITAIRE_TTC_LU','ID_LIGNE','ID_PRELEVEMENT_PRECEDENT','PREL_MODE_PREPARATION'),array($this->temps,$this->distance,$this->quantite_prelevee,$this->ean_lu,$this->prix_unitaire_ttc_lu,$this->ligne_commande,$this->prelevement_realise,$this->modePreparation));
	}
	
	/**
	 * R�cup�rer le/la idPrelevement_realise
	 * @return int 
	 */
	public function getIdprelevement_realise()
	{
		return $this->idPrelevement_realise;
	}
	
	/**
	 * R�cup�rer le/la temps
	 * @return int 
	 */
	public function getTemps()
	{
		return $this->temps;
	}
	
	/**
	 * D�finir le/la temps
	 * @param $temps int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setTemps($temps,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->temps = $temps;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PREL_TEMPS'),array($temps)) : true;
	}

	/**
	 * R�cup�rer la distance
	 * @return int 
	 */
	public function getDistance()
	{
		return $this->distance;
	}
	
	/**
	 * D�finir la distance
	 * @param $distance int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setDistance($distance,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->distance = $distance;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PREL_DISTANCE'),array($distance)) : true;
	}
	
	/**
	 * R�cup�rer le/la quantite_prelevee
	 * @return int 
	 */
	public function getQuantite_prelevee()
	{
		return $this->quantite_prelevee;
	}
	
	/**
	 * D�finir le/la quantite_prelevee
	 * @param $quantite_prelevee int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setQuantite_prelevee($quantite_prelevee,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->quantite_prelevee = $quantite_prelevee;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PREL_QUANTITE_PRELEVEE'),array($quantite_prelevee)) : true;
	}
	
	/**
	 * R�cup�rer le/la ean_lu
	 * @return int 
	 */
	public function getEan_lu()
	{
		return $this->ean_lu;
	}
	
	/**
	 * D�finir le/la ean_lu
	 * @param $ean_lu int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setEan_lu($ean_lu,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->ean_lu = $ean_lu;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PREL_EAN_LU'),array($ean_lu)) : true;
	}
	
	/**
	 * R�cup�rer le/la prix_unitaire_ttc_lu
	 * @return float 
	 */
	public function getPrix_unitaire_ttc_lu()
	{
		return $this->prix_unitaire_ttc_lu;
	}
	
	/**
	 * D�finir le/la prix_unitaire_ttc_lu
	 * @param $prix_unitaire_ttc_lu float 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setPrix_unitaire_ttc_lu($prix_unitaire_ttc_lu,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->prix_unitaire_ttc_lu = $prix_unitaire_ttc_lu;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PREL_PRIX_UNITAIRE_TTC_LU'),array($prix_unitaire_ttc_lu)) : true;
	}
	
	/**
	 * R�cup�rer le/la ligne_commande
	 * @return Ligne_Commande 
	 */
	public function getLigne_commande()
	{
		return Ligne_Commande::load($this->pdo,$this->ligne_commande);
	}
	
	/**
	 * D�finir le/la ligne_commande
	 * @param $ligne_commande Ligne_Commande 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setLigne_commande(Ligne_Commande $ligne_commande,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->ligne_commande = $ligne_commande->getIdLigne();
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_LIGNE'),array($ligne_commande->getIdLigne())) : true;
	}
	
	/**
	 * S�lectionner les prelevement_realises par ligne_commande
	 * @param $pdo PDO 
	 * @param $ligne_commande Ligne_Commande 
	 * @return PDOStatement 
	 */
	public static function selectByLigne_commande(PDO $pdo,Ligne_Commande $ligne_commande)
	{
		$pdoStatement = $pdo->prepare('SELECT p.* FROM PRELEVEMENT_REALISE p WHERE p.ID_LIGNE = ?');
		if (!$pdoStatement->execute(array($ligne_commande->getIdLigne()))) {
			throw new Exception('Erreur lors du chargement de tous/toutes les prelevement_realises par ligne_commande depuis la base de donn�es');
		}
		
		return Prelevement_Realise::fetch($pdo,$pdoStatement);
	}
	
	/**
	 * R�cup�rer le/la ancien
	 * @return Prelevement_Realise 
	 */
	public function getAncien()
	{
		// Retourner null si n�c�ssaire
		if ($this->ancien == null) { return null; }
		
		// Charger et retourner prelevement_realise
		return Prelevement_Realise::load($this->pdo,$this->ancien);
	}
	
	/**
	 * D�finir le/la ancien
	 * @param $ancien Prelevement_Realise 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setAncien($ancien=null,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->ancien = $ancien == null ? null : $ancien->getIdprelevement_realise();
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('ID_PRELEVEMENT_REALISE'),array($ancien == null ? null : $ancien->getIdprelevement_realise())) : true;
	}
	
	
	/**
	 * R�cup�rer le/la prioritaire
	 * @return int 
	 */
	public function getModePreparation()
	{
		return $this->modePreparation;
	}
	
	/**
	 * D�finir le/la prioritaire
	 * @param $prioritaire int 
	 * @param $execute bool ex�cuter la requ�te update ?
	 * @return bool op�ration r�ussie ?
	 */
	public function setModePreparation($modePreparation,$execute=true)
	{
		// Sauvegarder dans l'objet
		$this->modePreparation = $modePreparation;
		
		// Sauvegarder dans la base de donn�es (ou pas)
		return $execute ? $this->_set(array('PREL_MODE_PREPARATION'),array($modePreparation)) : true;
	}
}

?>