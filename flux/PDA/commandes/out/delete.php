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
	   				echo "ER - pas de num�ro de commande !"; 
					die;
					}		// Le nom de la commande (du fichier)


	if (isset($_POST['key'])) $key = $_POST['key']; else {echo "ER-Cl� incorrecte !"; die;}						// La cl� de contr�le
	
	// Contr�le de la cl�
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
			echo "ER-Probl�me de suppression du fichier !";
	}
	else
	{
		echo "ER-Probl�me d'int�grit� du fichier de commande !";
	}
?>

	