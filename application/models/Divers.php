<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Divers.php
 *
 */
 
class Divers
{
	
	/**
	 * Récupère le flag de changement de localisation
	**/
	public static function getChangementLocalisation(PDO $pdo){
		$pdoStatement = $pdo->query('SELECT DIV_CHGMT_LOC FROM DIVERS');
		$result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
		return $result['DIV_CHGMT_LOC'];
		
	}
	
	
	/**
	 * Modifie le flag de changement de localisation
	**/
	
	public static function setChangementLocalisation(PDO $pdo, $value){
		$pdoStatement = $pdo->prepare('UPDATE DIVERS SET DIV_CHGMT_LOC = ?');
		if (!$pdoStatement->execute(array($value))) {
			throw new Exception('Erreur lors de la mise à jour du flag de changement de localisation');
		}
		
		// Opération réussie ?
		return $pdoStatement->rowCount() == 1;
	}

}

?>