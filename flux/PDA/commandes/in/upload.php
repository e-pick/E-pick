<?php

if (isset($_POST['commande'])) $commande = $_POST['commande']; else { echo "ER-N° de commande incorrect !"; die;}		// Le nom de la commande (du fichier)
if (isset($_POST['key'])) $key = $_POST['key']; else {echo "ER-Clé incorrecte !"; die;}						// La clé de contrôle
$data = $_POST['data'];	// Les données

// Contrôle de la clé
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
	echo "ER-Problème d'intégrité du fichier de commande !";
}
?>
