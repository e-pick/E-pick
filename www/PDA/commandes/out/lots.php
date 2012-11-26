<?php
	// élément passé en paramètre
	$pattern = '#^[a-zA-Z0-9@._-]*$#'; 			//Définition de l'ensemble des caractères acceptés
	if (isset($_POST['dh'])) $dh = $_POST['dh']; else { echo "ER-N° de dh incorrect !"; die;}		// Date heure de dende
	if (isset($_POST['key'])) $key = $_POST['key']; else {echo "ER-Clé incorrecte !"; die;}			// La clé de contrôle
	
	// Contrôle de la clé
	if ($key == md5("<DH>" . $dh . "<DH>"))
	{
		$d = dir("./");
		$entry = $d->path;
		if ((strncmp(strtoupper($entry), "LOT", 3) == 0) && (strstr(strtoupper($entry), ".TXT") != null))
			echo $entry . "#";
		while (false !== ($entry = $d->read())) {
			if ((strncmp(strtoupper($entry), "LOT", 3) == 0) && (strstr(strtoupper($entry), ".TXT") != null))
				echo $entry . "#";
		}
		$d->close();
	}
	else
	{
		echo "ER-Problème d'intégrité de la demande !";
	}
?>

	