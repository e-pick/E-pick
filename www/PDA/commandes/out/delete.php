<?php
	
	
	define ('applicationDir', '../../../../../E-Pick/');
	require_once(applicationDir."application/config/bootstrap.php");
	require_once(applicationDir."application/kernel/PDO.php");

	
	
	if (isset($_POST['commande'])) $commande = $_POST['commande']; else { 
	   				echo "ER - pas de numéro de commande !"; 
					die;
					}		// Le nom de la commande (du fichier)


 	
		
	if (isset($_POST['key'])) $key = $_POST['key']; else {echo "ER-Clé incorrecte !"; die;}						// La clé de contrôle
	
		

	
	// Contrôle de la clé
	if ($key == md5("<K>" . $commande . "<K>"))
	{
		if (unlink($commande) == TRUE){
		echo "OK";
		
		//On change le statut de la commande
		$preparation = explode( '_', $commande );
		$preparationId = $preparation[1];
		$pdo 	= DB :: getInstance();
		$preparation = Preparation::load($pdo,$preparationId);
		$preparation->setEtat(2);
	
		}
		else
			echo "ER-Problème de suppression du fichier !";
	}
	else
	{
		echo "ER-Problème d'intégrité du fichier de commande !";
	}
?>

	