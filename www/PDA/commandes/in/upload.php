<?php

if (isset($_POST['commande'])) $commande = $_POST['commande']; else { echo "ER-N� de commande incorrect !"; die;}		// Le nom de la commande (du fichier)
if (isset($_POST['key'])) $key = $_POST['key']; else {echo "ER-Cl� incorrecte !"; die;}						// La cl� de contr�le
$data = $_POST['data'];	// Les donn�es

// Contr�le de la cl�
if ($key == md5("<K>" . $commande . "<K>")) {

	// Enregistrement du fichier
	
	$fw = fopen("./" . $commande . ".txt", "w");
	fwrite($fw, $data);
	fclose($fw);
	echo "OK";
	
	/* Traiter les fichiers */
	require_once('./commandes_retour.php');
}
else {
	echo "ER-Probl�me d'int�grit� du fichier de commande !";
}
?>
