<?php
class DB {
	private static $dbh = null;
	
	public static function getInstance() {
		if (self :: $dbh == null) {
			try {
				self::parseIni();
				
				self :: $dbh = new ownPDO(DB_DSN, DB_USER, DB_PASSWORD);
				self :: $dbh->setAttribute(PDO :: ATTR_CASE, PDO :: CASE_UPPER);
				self :: $dbh->setAttribute(PDO :: ATTR_ERRMODE, PDO :: ERRMODE_EXCEPTION);
				
				// Ajout Cache MySQL
				self :: $dbh->setAttribute(PDO :: ATTR_EMULATE_PREPARES, true);
				self :: $dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
			}
			catch (Exception $e) {  
				header('Location: '. APPLICATION_PATH .'error.php?type=http500&message='.$e->getMessage().'&code='.$e->getCode().'&url='.APPLICATION_PATH); 	
			}
		}
		return self :: $dbh;
	}
	
	private static function parseIni(){
		$DBConfFile = applicationDir.'application/config/conf/database.ini';
		
		if (file_exists($DBConfFile)){
			if (!($conf_array = parse_ini_file($DBConfFile))){
				throw new Exception(gettext('Erreur lors de la lecture du fichier ') . $DBConfFile);
			}
			else{
				if (count($conf_array) != 4) {throw new Exception(gettext('Param&egrave;tre manquant dans le fichier ') . $DBConfFile);}
				if (($DB_NAME = $conf_array['DB_NAME']) == ''){throw new Exception(gettext('Param&egrave;tre non renseign&eacute; dans le fichier ') . $DBConfFile . gettext(' : DB_NAME'));}
				if (($DB_DSN	= $conf_array['DB_DSN']) == ''){throw new Exception(gettext('Param&egrave;tre non renseign&eacute; dans le fichier ') . $DBConfFile . gettext(' : DB_DSN'));}
				if (($DB_USER = $conf_array['DB_USER']) == ''){throw new Exception(gettext('Param&egrave;tre non renseign&eacute; dans le fichier ') . $DBConfFile . gettext(' : DB_USER'));}
				// if (($DB_PASSWORD = $conf_array['DB_PASSWORD']) == ''){throw new Exception(gettext('Param&egrave;tre non renseign&eacute; dans le fichier ') . $DBConfFile . gettext(' : DB_PASSWORD'));}
				
				define('DB_NAME', $conf_array['DB_NAME']);
				define('DB_DSN', $conf_array['DB_DSN']);
				define('DB_USER', $conf_array['DB_USER']);
				define('DB_PASSWORD', $conf_array['DB_PASSWORD']);
			}
		}
		else{
			throw new Exception(gettext('Le fichier ') . $DBConfFile . gettext(' n\'existe pas !'));
		}
	}
}


class ownPDO extends PDO {

} 

?>