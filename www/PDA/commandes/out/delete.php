<?php
	
	
	define ('applicationDir', '../../../../../E-Pick/');
	require_once(applicationDir."application/config/bootstrap.php");
	require_once(applicationDir."application/kernel/PDO.php");

	
	
	if (isset($_POST['commande'])) $commande = $_POST['commande']; else { 
	   				echo "ER - pas de num�ro de commande !"; 
					die;
					}		// Le nom de la commande (du fichier)


 	
		
	if (isset($_POST['key'])) $key = $_POST['key']; else {echo "ER-Cl� incorrecte !"; die;}						// La cl� de contr�le
	
		

	
	// Contr�le de la cl�
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
			echo "ER-Probl�me de suppression du fichier !";
	}
	else
	{
		echo "ER-Probl�me d'int�grit� du fichier de commande !";
	}
?>

	