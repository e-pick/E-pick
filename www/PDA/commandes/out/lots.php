<?php
	// �l�ment pass� en param�tre
	$pattern = '#^[a-zA-Z0-9@._-]*$#'; 			//D�finition de l'ensemble des caract�res accept�s
	if (isset($_POST['dh'])) $dh = $_POST['dh']; else { echo "ER-N� de dh incorrect !"; die;}		// Date heure de dende
	if (isset($_POST['key'])) $key = $_POST['key']; else {echo "ER-Cl� incorrecte !"; die;}			// La cl� de contr�le
	
	// Contr�le de la cl�
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
		echo "ER-Probl�me d'int�grit� de la demande !";
	}
?>

	