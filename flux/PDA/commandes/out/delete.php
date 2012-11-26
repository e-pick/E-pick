<?php
ini_set('display_errors', 1);
error_reporting(E_ALL); 


 

// Changement statut du fichier
$applicationDir = "/var/www/restricted/ssh/dev/www/E-Pick/";
define ('applicationDir', '../../../../../E-Pick/');
require_once(applicationDir."application/config/bootstrap.php");
require_once(applicationDir."application/kernel/PDO.php");

		$commande = $_GET['commande'];
	

	
	if (isset($_POST['commande'])) $commande = $_POST['commande']; else { 
	   				echo "ER - pas de numéro de commande !"; 
					die;
					}		// Le nom de la commande (du fichier)


	if (isset($_POST['key'])) $key = $_POST['key']; else {echo "ER-Clé incorrecte !"; die;}						// La clé de contrôle
	
	// Contrôle de la clé
	if ($key == md5("<K>" . $commande . "<K>"))
	{
		
		
		
		if (unlink($commande) == TRUE){
		$preparation = explode( '_', $commande );
		$preparationId = $preparation[3];
		$pdo 	= DB :: getInstance();
		$preparation = Preparation::load($pdo,$preparationId);
		$preparation->setEtat(2);

			echo "OK";
		}
		else
			echo "ER-Problème de suppression du fichier !";
	}
	else
	{
		echo "ER-Problème d'intégrité du fichier de commande !";
	}
?>

	