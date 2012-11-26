<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright � E-Pick ***
***
 * Utilisateur.php
 *
 * Gestion des utilisateurs
 *
 */


class Utilisateur {
    
    /// @var ownPDO 
    private $pdo;  
    /// @var array tableau pour le chargement rapide
    private static $easyLoad;    
    /// @var int 
    private $idUtilisateur;  
    /// @var string 
    private $login;      
    /// @var string 
    private $password; 	
    /// @var string 
    private $prenom;    
    /// @var string 
    private $nom;  
    /// @var string 
    private $email;      
    /// @var int 
    private $user_level;    
	/// @var string
	private $template;
	/// @var string
    private $photo;    
	/// @var int
	private $derniereConnexion;	
	/// @var boolean
	private $actif;
	
    
    /**
     * Construire un Utilisateur
     * @param $pdo ownPDO 
     * @param $idUtilisateur int 
     * @param $prenom string 
     * @param $nom string 
     * @param $email string
     * @param $login string
     * @param $password string
     * @param $user_level int
	 * @param $template string
	 * @param $photo string
	 * @param $derniereConnexion
	 * @param $actif
	 * @return Utilisateur 
     */
    public function __construct(ownPDO $pdo,$idUtilisateur,$login,$password,$nom,$prenom,$email,$user_level,$template,$photo,$derniereConnexion,$actif) {
        // Sauvegarder pdo
        $this->pdo = $pdo;
        
        // Sauvegarder les attributs
        $this->idUtilisateur 		= $idUtilisateur;
        $this->prenom 				= $prenom;
        $this->nom 					= $nom;
        $this->email 				= $email;
        $this->login 				= $login;
		$this->password 			= $password;
		$this->user_level 			= $user_level; 
		$this->template 			= $template;
		$this->photo 				= $photo;
		$this->derniereConnexion 	= $derniereConnexion;
		$this->actif 				= $actif;
		
    }
    
    /**
     * Construire un Utilisateur
     * @param $pdo ownPDO 
     * @param $prenom string 
     * @param $nom string 
     * @param $email string 
     * @param $login string
     * @param $password string
     * @param $user_level int
	 * @param $template string
	 * @param $photo string
	 * @param $derniereConnexion
	 * @param $actif
     * @return Utilisateur 
     */
    public static function create(ownPDO $pdo,$login,$password,$nom,$prenom,$email,$user_level,$template,$photo,$derniereConnexion,$actif) {
        // Ajouter un Utilisateur into database
        $pdoStatement = $pdo->prepare('INSERT INTO UTILISATEUR (UTI_LOGIN,UTI_PASSWORD,UTI_NOM,UTI_PRENOM,UTI_EMAIL,UTI_USER_LEVEL,UTI_TEMPLATE,UTI_PHOTO,UTI_DERNIERE_CONNEXION,UTI_ACTIF) VALUES (?,?,?,?,?,?,?,?,?,?)');
        if(!$pdoStatement->execute(array($login,md5($password),$nom,$prenom,$email,$user_level,$template,$photo,$derniereConnexion,$actif))) {
            throw new Exception('Erreur durant l\'insertion d\'un Utilisateur dans la base de donn�es');
        }
        
        // Construire une Utilisateur
        return new Utilisateur($pdo,$pdo->lastInsertId(),$login,$password,$nom,$prenom,$email,$user_level,$template,$photo,$derniereConnexion,$actif);
    }
    
    /**
     * Charger un Utilisateur
     * @param $pdo ownPDO 
     * @param $idUtilisateur int 
     * @param $easyLoad bool activer le chargement rapide 
     * @return Utilisateur 
     */
    public static function load(ownPDO $pdo,$idUtilisateur,$actifOnly=true,$easyLoad=false) {
        // D�j� charg� ?
        if(isset(Utilisateur::$easyLoad[$idUtilisateur])) {
            return Utilisateur::$easyLoad[$idUtilisateur];
        }
        
        // Pr�parer et executer la requ�te
		
		if($actifOnly)
			$pdoStatement = $pdo->prepare(Utilisateur::_select('u.ID_UTILISATEUR = ? AND u.UTI_ACTIF = 1 '));
		else
			$pdoStatement = $pdo->prepare(Utilisateur::_select('u.ID_UTILISATEUR = ?'));
		
        if(!$pdoStatement->execute(array($idUtilisateur))) {
            throw new Exception('Erreur lors du chargement d\'un Utilisateur depuis la base de donn�es');
        }
        
        // R�cup�rer l'Utilisateur depuis le jeu de r�sultats
        return Utilisateur::fetch($pdo,$pdoStatement,$easyLoad);
    }
	

	
	/**
    * Verifie si un utilisateur est connect� et poss�de les droits suffisants pour continuer
    * @param $pdo ownPDO 
	* @param $level_min int 
	*/
	public static function isConnected(){
		return (!isset($_SESSION['user_id']) || (isset($_SESSION['user_id']) && empty($_SESSION['user_id']))) ? false : true;
	}
	
	/**
	 * V�rifie les droits d'un utilisateur
	 * @param $pdo ownPDO
	 * @param $level_min, le niveau minimum requis
	 * @return boolean, true si l'utilisateur a les droits, false sinon
	 */
	public static function checkRights(ownPDO $pdo,$level_min){
	
		if(self::isConnected()){
				if(intval($_SESSION['user_id']) >0 ){
					$user = self::load($pdo,$_SESSION['user_id']);
					if($user != null){
						if($user->getUserLevel() >= $level_min){						
							return true;
						}
						else{	 
							return false;
						}
					}
					else{
						return false;
					}
				}
				else{
					return false;				
				}	
		}
		else{
			return false;
		}
	}
	
	/**
	 * V�rifie si un utilisateur est autoris� pour l'action demand�e
	 * @param $pdo ownPDO
	 * @param $level_min le niveau minimum requis pour l'action demand�e
	 * @return boolean, true si l'utilisateur est autoris� pour l'action, false sinon
	 */
	public static function isAllowed(ownPDO $pdo, $level_min){
		if(!self::isConnected())
			throw new Exception('Vous devez �tre connect� pour acc�der � votre demande',1);
		else{
			if(!self::checkRights($pdo,$level_min))
				throw new Exception('Vous ne disposez pas des droits suffisants pour acc�der � votre demande',2);
			else{			
				return true;
			}		
		}	
	}
	 
	/**
	 * Renvoi true si $file est contenu dans $dir
	 * @param $file string
	 * @param *dir  string
	 * @return boolean, true si le fichier est contenu dans le r�pertoire, false sinon
	 */
	public static function isInDir($file, $dir){
		$contenu = array();			// Tableau contenant les r�pertoires du dossier $dir
		$dossier = opendir($dir);
		while ($fichier = readdir($dossier)) {
			if (substr($fichier, 0, 1) != ".") {	// S'il ne s'agit pas d'un fichier cach�
				array_push($contenu, $fichier);
			}
		}
		closedir($dossier);
		
		return in_array($file, $contenu);
	}
	
	/**
	 *
	 * Teste l'int�grit� des attributs de l'objet Utilisateur
	 * @param $attribute le nom de l'attribut
	 * @param $value la valeur � tester
	 * @return boolean => true si ok, false sinon
	 *
	 */
	public static function testIntegrite($attribute, $value){
		switch ($attribute) {
			case 'prenom' :
			case 'nom' :
				return (is_string($value) && $value != null && $value != '' && preg_match("/^([a-zA-Z'�����������������[:blank:]-]*)$/",$value));
				break;
				
			case 'login' :
				$login = $value;
				return (is_string($login) && $login != null && $login != '' && preg_match("/^([a-zA-Z-._]*)$/",$login));
				break;
				
			case 'email' :
				$email = $value;
				return (is_string($email) && $email != null && $email != '' && preg_match("/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/",$email));
				break;
				
			case 'password' :
				$password = $value;
				return (is_string($password) && $password != null && $password != '' && strlen($password) == 32);
				break;
				
			case 'user_level' :
				$user_level = $value;
				return (!is_string($user_level) && $user_level != null && $user_level != '' && in_array($user_level, array(1,2,3)));
				break;
				
			case 'template' :
				$template = $value;
				// return (is_string($template) && $template != null && $template != '' && self::isInDir($template, dirname(__DIR__) . '/tpls/templates/'));
				return (is_string($template) && $template != null && $template != '' && self::isInDir($template, '../application/tpls/templates/'));
				break;
				
			case 'photo' :
				$photo = $value;
				// return (is_string($photo) && $photo != null && $photo != '' && self::isInDir($photo, dirname(dirname(__DIR__)) . '/www/images/photos/'));
				return (is_string($photo) && $photo != null && $photo != '' && self::isInDir($photo, './images/photos/'));
				break;
				
			case 'derniereConnexion' :
				$derniereConnexion = $value;
				return (!is_string($derniereConnexion) && $derniereConnexion != null && $derniereConnexion != '');
				break;
					
			case 'actif' :
				$actif = $value;
				return (!is_string($actif) && $actif != null && $actif != '' && in_array($actif, array(0,1)));
				break;
				
			default:
				throw new Exception(gettext('L\'attribut ') . $attribute . gettext(' ne fait pas partie de l\'objet ') . gettext('Utilisateur'),3);
		}
	}
	
	 /**
     * Charger un Utilisateur apr�s connexion
     * @param $pdo ownPDO 
     * @param $login string
     * @param $password string 
     * @param $easyLoad bool activer le chargement rapide 
     * @return Utilisateur 
     */
	public static function load_after_login(ownPDO $pdo,$login,$password,$easyLoad=true){
	    // Pr�parer et executer la requ�te
        $pdoStatement = $pdo->prepare(Utilisateur::_select('u.UTI_LOGIN = ? AND u.UTI_PASSWORD = ? AND u.UTI_ACTIF = 1 ')); 
        if(!$pdoStatement->execute(array($login,md5($password)))) {
            throw new Exception('Erreur lors du chargement d\'un Utilisateur depuis la base de donn�es');
        }
		
        // R�cup�rer l'Utilisateur depuis le jeu de r�sultats
        return Utilisateur::fetch($pdo,$pdoStatement,$easyLoad);
	}
    
    /**
     * Charger tous les Utilisateurs
     * @param $pdo ownPDO 
     * @param $easyLoad bool activer le chargement rapide 
     * @return Utilisateur[] tableau de Utilisateurs
     */
    public static function loadAll(ownPDO $pdo, $actifOnly=true, $easyLoad=false) {
        // S�lectionner tous les Utilisateurs
        $pdoStatement = Utilisateur::selectAll($pdo, $actifOnly);
        
        // Mettre chaque Utilisateur dans un tableau
        $Utilisateurs = array();
        while($Utilisateur = Utilisateur::fetch($pdo,$pdoStatement,$easyLoad)) {
            $Utilisateurs[] = $Utilisateur;
        }
        
        // Retourner le tableau
        return $Utilisateurs;
    }

    /**
     * Teste si le login est d�j� utilis�
     * @param $pdo ownPDO 
     * @param $login le login � tester
	 * @param $id l'idUtilisateur utilis� dans le cas d'une �dition 
     * @return true si le login est d�j� utilis�, false sinon
     */
	public static function loginUsed(ownPDO $pdo, $login, $id=0){ 
 		
		if ($id == 0){
			$pdoStatement = $pdo->prepare(Utilisateur::_select('u.UTI_LOGIN = ?'));
			if(!$pdoStatement->execute(array($login))) {
				throw new Exception('Erreur lors du test du login depuis la base de donn�es');
			} 
		}
		else {
			$pdoStatement = $pdo->prepare(Utilisateur::_select('u.UTI_LOGIN = ? AND u.ID_UTILISATEUR != ?'));
			if(!$pdoStatement->execute(array($login, $id))) {
				throw new Exception('Erreur lors du test du login depuis la base de donn�es');
			} 
		} 
		 
        return ($pdoStatement->rowCount() == 0) ? false : true;	
	}
    
    /**
     * R�cup�rer la requ�te de selection
     * @param $where string 
     * @param $orderby string 
     * @param $limit string 
     * @return string 
     */
    private static function _select($where=null,$orderby=null,$limit=null) {
        return 'SELECT * FROM UTILISATEUR u '.
                ($where != null ? 'WHERE '.$where : '').
                ($orderby != null ? ' ORDERBY '.$orderby : '').
                ($limit != null ? ' LIMIT '.$limit : '');
    }
    
    /**
     * S�lectionner tous les Utilisateurs
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(ownPDO $pdo, $actifOnly=true) {
		if($actifOnly)
			$pdoStatement = $pdo->prepare(Utilisateur::_select('u.UTI_ACTIF = 1 '));
		else
			$pdoStatement = $pdo->prepare(Utilisateur::_select());
		
        if(!$pdoStatement->execute()) {
            throw new Exception('Erreur lors du chargement de tous les Utilisateurs depuis la base de donn�es');
        }
        return $pdoStatement;
    }
    
    /**
     * R�cup�re l'Utilisateur suivant d'un jeu de r�sultats
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $easyLoad bool activer le chargement rapide ?
     * @return Utilisateur 
     */
    public static function fetch(ownPDO $pdo,PDOStatement $pdoStatement,$easyLoad=false) {  
        // Extraire les valeurs
        $values = $pdoStatement->fetch();
        if(!$values) { return null; }
        list($idUtilisateur,$login,$password,$nom,$prenom,$email,$user_level,$template,$photo,$derniereConnexion,$actif) = $values;
        
        // Construire l'Utilisateur
        $Utilisateur = new Utilisateur($pdo,$idUtilisateur,$login,$password,$nom,$prenom,$email,$user_level,$template,$photo,$derniereConnexion,$actif);
        
        // Sauvegarder pour le chargement rapide
        if($easyLoad) {
            Utilisateur::$easyLoad[$idUtilisateur] = $Utilisateur;
        }
        // Retourner Utilisateur
        return $Utilisateur;
    }
    
    /**
     * Test d'�galit�
     * @param $Utilisateur Utilisateur 
     * @return bool les objets sont ils �gaux ?
     */
    public function equals($Utilisateur) {
        // Test si null
        if($Utilisateur == null) { return false; }
        
        // Tester la classe
        if(!($Utilisateur instanceof Utilisateur)) { return false; }
        
        // Tester les ids
        return $this->idUtilisateur == $Utilisateur->idUtilisateur;
    }
    
    /**
     * Compter les Utilisateurs
     * @param $pdo PDO 
     * @return int nombre de Utilisateurs
     */
    public static function count(ownPDO $pdo) {
        if(!($pdoStatement = $pdo->query('SELECT COUNT(ID_UTILISATEUR) FROM UTILISATEUR'))) {
            throw new Exception('Erreur lors du comptage des Utilisateurs dans la base de donn�es');
        }
        return $pdoStatement->fetchColumn();
    }
    
    /**
     * Supprimer le Utilisateur
     * @return bool op�ration r�ussie ?
     */
    public function delete() {
        // Elements associ�s
		
		// Supprimer les plannings associ�s
		$select = $this->selectPlannings();
		// while ($planning = Planning::fetch($this->pdo,$select)) {
			// if (!$planning->delete()) { return false; }
		// }
		foreach ($select as $planning){
			if (!$planning->delete()) return false; 
		}
        
        // Supprimer le login et le mot de passe de l'utilisateur
		$pdoStatement = $this->pdo->prepare('UPDATE UTILISATEUR SET UTI_LOGIN = NULL, UTI_PASSWORD = ?, UTI_ACTIF = 0 WHERE ID_UTILISATEUR = ?');
        if(!$pdoStatement->execute(array(md5(uniqid(rand(), true)), $this->getIdUtilisateur())))
            throw new Exception('Erreur lors de la supression d\'un Utilisateur dans la base de donn�es');
        
        // Op�ration r�ussie ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * ToString
     * @return string repr�sentation de Utilisateur sous la forme d'un string
     */
    public function __toString() {
        return '[Utilisateur idUtilisateur="'.$this->getIdUtilisateur().'" prenom="'.$this->getPrenom().'" nom="'.$this->getNom().'" email="'.$this->getEmail().'" login="'.$this->getLogin().'" password="'.$this->getPassword().'" user_level="'.$this->getUserLevel().'" template="'.$this->getTemplate().'" photo="'.$this->getPhoto().'" derniere_connexion="'.$this->getDerniereConnexion().'" actif="'.$this->getActif().'"]';
    }
    
    /**
     * R�cup�rer le idUtilisateur
     * @return int 
     */
    public function getIdUtilisateur() {
        return $this->idUtilisateur;
    }
    
    
    /**
     * R�cup�rer le prenom
     * @return string 
     */
    public function getPrenom($utf8 = false) {		
        return ($utf8) ? utf8_encode($this->prenom) : $this->prenom;
    }

    /**
     * R�cup�rer le nom
     * @return string 
     */
    public function getNom($utf8 = false) {		
        return ($utf8) ? utf8_encode($this->nom) : $this->nom;
    }
	
     /**
     * R�cup�rer l'email
     * @return string 
     */
    public function getEmail($utf8 = false) {		
        return ($utf8) ? utf8_encode($this->email) : $this->email;
    }
    
    /**
     * R�cup�rer le login
     * @return int 
     */
    public function getLogin() {
        return $this->login;
    }
       
	
	 /**
     * R�cup�rer le password
     * @return int 
     */
    public function getPassword() {
        return $this->password;
    }
	
	 /**
     * R�cup�rer le user_level
     * @return int 
     */
    public function getUserLevel() {
        return $this->user_level;
    }
	
	 /**
     * R�cup�rer le template 
     * @return int 
     */
    public function getTemplate() {
        return $this->template;
    }	
	
	 /**
     * R�cup�rer la photo 
     * @return int 
     */
    public function getPhoto() {
        return $this->photo;
    }	
	
	 /**
     * R�cup�rer la derniereConnexion 
     * @return int 
     */
    public function getDerniereConnexion() {
        return $this->derniereConnexion;
    }	
	 
	 /**
     * R�cup�rer le boolean actif 
     * @return int 
     */
    public function getActif() {
        return $this->actif;
    }	
	    
    /**
     * D�finir le prenom
     * @param $prenom string 
     * @return bool op�ration r�ussie ?
     */
    public function setPrenom($prenom,$write=true) {
		if (!self::testIntegrite('prenom', $prenom)) {
			return false;
		}		
		
        // Sauvegarder dans l'objet
        $this->prenom = $prenom;
     
        
        return ($write) ? $this->write() : true;
    }
	
	
	 /**
     * D�finir le nom
     * @param $nom string 
     * @return bool op�ration r�ussie ?
     */
    public function setNom($nom,$write=true) {
		if (!self::testIntegrite('nom', $nom)) {
			return false;
		}		
        // Sauvegarder dans l'objet
        $this->nom = $nom;
		
        
        return ($write) ? $this->write() : true;
    } 
	/**
     * D�finir le nom
     * @param $nom string 
     * @return bool op�ration r�ussie ?
     */
    public function setEmail($email,$write=true) {
		if (!self::testIntegrite('email', $email)) {
			return false;
		}		
        // Sauvegarder dans l'objet
        $this->email = $email;
		
        
        return ($write) ? $this->write() : true;
    }
	
	
	 /**
     * D�finir le login
     * @param $login string 
     * @return bool op�ration r�ussie ?
     */
    public function setLogin($login,$write=true) {
        if (!self::testIntegrite('login', $login)) {
			return false;
		}	
		// Sauvegarder dans l'objet
        $this->login = $login;
		
       
        return ($write) ? $this->write() : true;
    }
	
	/**
     * D�finir le password
     * @param $password string 
     * @return bool op�ration r�ussie ?
     */
    public function setPassword($password,$write=true) {
        if (!self::testIntegrite('password', $password)) {
			return false;	}
		// Sauvegarder dans l'objet
        $this->password = $password;
		
 
        return ($write) ? $this->write() : true;
    }
	
	/**
     * D�finir le user_level
     * @param $user_level string 
     * @return bool op�ration r�ussie ?
     */
    public function setUserLevel($user_level,$write=true) {
        if (!self::testIntegrite('user_level', $user_level)) {
			return false;		}	
		// Sauvegarder dans l'objet
        $this->user_level = $user_level;
		
        return ($write) ? $this->write() : true;
    }
	
	/**
     * D�finir le template
     * @param $template string 
     * @return bool op�ration r�ussie ?
     */
    public function setTemplate($template,$write=true) {
        if (!self::testIntegrite('template', $template)) {
			return false;
		}	
		
        $this->template = $template;
		
        return ($write) ? $this->write() : true;
    }	
	
	/**
     * D�finir la photo
     * @param $template string 
     * @return bool op�ration r�ussie ?
     */
    public function setPhoto($photo,$write=true) {
        if (!self::testIntegrite('photo', $photo)) {
			return false;
		}	
		
        $this->photo = $photo;
		
        return ($write) ? $this->write() : true;
    }
	
	/**
     * D�finir la derniereConnexion
     * @param $derniereConnexion int 
     * @return bool op�ration r�ussie ?
     */
    public function setDerniereConnexion($derniereConnexion,$write=true) {
        if (!self::testIntegrite('derniereConnexion', $derniereConnexion)) {
			return false;
		}	
        $this->derniereConnexion = $derniereConnexion;
		
        return ($write) ? $this->write() : true;
    }
	
	/**
     * R�cup�rer le boolean actif 
     * @return int 
     */
    public function setActif($actif,$write=true) {
		if (!self::testIntegrite('actif', $actif)) {
			return false;
		}
        $this->actif = $actif;
		
		return ($write) ? $this->write() : true;
    }	
	  
	
	
	
	/**
	 * Ecrit dans la base de donn�es
	 */
	public function write(){
	    // Sauvegarder dans la base de donn�es
        $pdoStatement = $this->pdo->prepare('UPDATE UTILISATEUR SET UTI_NOM = ? , UTI_PRENOM = ?, UTI_EMAIL = ?, UTI_LOGIN = ?, UTI_PASSWORD = ?, UTI_USER_LEVEL = ?, UTI_TEMPLATE = ?, UTI_PHOTO = ?, UTI_DERNIERE_CONNEXION = ?, UTI_ACTIF = ? WHERE ID_UTILISATEUR = ?');
        if(!$pdoStatement->execute(array($this->getNom(),$this->getPrenom(),$this->getEmail(),$this->getLogin(),$this->getPassword(),$this->getUserLevel(),$this->getTemplate(),$this->getPhoto(),$this->getDerniereConnexion(),$this->getActif(),$this->getIdUtilisateur()))) {
            throw new Exception('Erreur lors de la mise � jour de l\'utilisateur dans la base de donn�es');
        }
        // Op�ration r�ussie ?
        return $pdoStatement->rowCount() == 1;	
	}
	
	
	/**
	 * S�lectionner les plannings
	 * @return PDOStatement 
	 */
	public function selectPlannings()
	{
		return Planning::selectByUtilisateur($this->pdo,$this);
	}
	
}
?>