<?php

	if(isset($_GET['recup_nom'])){
	
		/* Récuperer les fichiers geo.out.xml */
		$dossier = opendir(".");
		while ($fichier = readdir($dossier)) {
			if (substr($fichier, -8) == ".out.xml") {
				echo $fichier;
				break;
			}
		} 
		closedir($dossier);
	}
	else if(isset($_GET['delete'])){
		unlink($_GET['delete']);
	}
?>