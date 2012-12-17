<?php
/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
*** 
 *
 * Ce fichier contient les fonctions communes dans l'application
 *
 */
 
	require_once(applicationDir.'libraries/zip.lib.php') ; // librairie ZIP
	require_once(applicationDir.'libraries/fpdf/fpdf.php');
  
	define ('INFINITE_DISTANCE', 1000000);
		// Pour le calcul des plus courts chemins entre tous les sommets

	/**
	 *
	 * Fonction qui permet de sauvegarder les tables de la base de données
	 *
	 * @param $tables 	: array, la liste des tables à sauvegarder
	 * @param $location : string, le chemin absolu du dossier qui contiendra le fichier de sauvegarde
	 * @param $fileName : string, le nom du fichier de sauvegarde (avec .sql à la fin)
	 * @param $allDB 	: boolean, vaut 'true' si on veut sauvegarder toutes la base de données, par défaut est égal à 'false'
	 * @return 			: string, le chemin du fichier de sauvegarde
	 *
	 */
	function dumpDB($tables, $location, $fileName, $allDB=false) {
		
		$pdo 	= DB :: getInstance();
		
		
		/* Récupérer toutes les tables de la base de données */
		$query 	= "SHOW TABLES";
		$result = $pdo->query($query);
		$i 		= 0;
		$allTables = array();
		while ($Table = $result->fetch(PDO::FETCH_ASSOC)) {
			$allTables[$i] = $Table['TABLES_IN_' . strtoupper(DB_NAME)];
			// $allTables[$i] = strtoupper($Table['TABLES_IN_' . strtoupper(DB_NAME)]);
			$i++;
		}
		
		/* Test d'intégrité des paramètres */
		if($allDB){
			/* Si on a choisi de sauvegarder toute la base de données */ 
			// $tables = $allTables; 
			$tables = array('PRODUIT','EAN','ETAGE','OBSTACLE','ZONE','RAYON','SEGMENT','ETAGERE','EST_GEOLOCALISE_DANS','CLIENT','COMMANDE','TEMPS_PREPARATION','UTILISATEUR','PLANNING','PREPARATION','LIGNE_COMMANDE','PRELEVEMENT_REALISE'); 
		}
		else if (!is_array($tables)) {
			/* S'il ne s'agit pas d'un tableau */
			die("Veuillez renseigner un tableau en paramètre");
		}
		else if(array_empty($tables)) {
			/* Si aucune table n'a été renseignée et allDB = false */
			die("Veuillez rensigner des tables à sauvegarder");
		}
		else {
			/* Test si les tables renseignées existe bien dans la base de données */
			foreach ($tables as $elem){
				if (!in_array($elem, $allTables)){
					die("La table " . $elem . " n'existe pas");
				}
			}
		}
		if (!file_exists($location)) {
			/* Test si le dossier cible existe */
			die("Le dossier " . $location . " n'existe pas");
		}
		
		
		/* Sauvegarde de la base de données */
		$data  = "-- PDO SQL Dump --\n\n";	
		$data .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\"; \n\n";
		$data .= "--\n";
		$data .= "-- Database: '". DB_NAME . "'\n";
		$data .= "-- Tables: ";
		$data .= implode("; ", $tables);	// Les éléments de $tables séparés par ';'
		$data .= "\n--";
		
		foreach ($tables as $tableName) {
 
			/* Récupération des données de la table */
			$data .= "\n\n--\n-- Les données pour la table `$tableName`\n--\n\n"; 
			$query = "SELECT * FROM " . $tableName. "\n";
			$result2 = $pdo->query($query);
			$count = 0;
			$first = true;
			while ($record = $result2->fetch(PDO::FETCH_ASSOC)) {
				if($first){
					$data .= "INSERT IGNORE INTO " . $tableName . " VALUES \n";
					$first = false;
				}
				if($count == 2000){
					$data = substr($data, 0, -2);
					$data .= ";\n";
					$data .= "INSERT IGNORE INTO " . $tableName . " VALUES \n";
					$count = 0;
				}
				$enregistrement  = "(";
				foreach( $record as $value ) {
					if(is_null($value))
						$enregistrement .= "NULL,";
					else
						$enregistrement .= "'" . addslashes($value) . "',";
				}
				$data .= substr( $enregistrement, 0, -1 );
				$data .= "),\n";
				$count++;
				
			}
			
			if($first != true){ // Au moins un enregistrement
				$data = substr($data, 0, -2);
				$data .= ";\n";
			}
		}

		// /* Ecriture dans le fichier de backup */
		$fileBackup = $location . '/' . $fileName;
		$open 		= fopen( $fileBackup , "w+");
		fwrite($open, $data);
		fclose($open);
		
		return $fileBackup;
	}
	
	/**
	 *
	 * Vérifie si un tableau est vide
	 *
	 * @param $array	: array, le tableau à vérifier
	 * @return 			: boolean, true si le tableau est vide, false sinon
	 *
	 */
	function array_empty($array) {
		$etiquette = trim(implode('', $array));
		return (strlen($etiquette) == 0);
	}
	
	/**
	 *
	 * Pour créer un zip
	 *
	 * @param $files 	: array, les fichiers à mettre dans le zip
	 * @param $location : string, le chemin absolu du dossier qui contiendra le zip
	 * @param $fileName : string, le nom du fichier zip
	 * @return 			: string, le chemin du fichier zip
	 *
	 */
	function zipper($files, $location, $fileName){
		
		if (!is_array($files)) {
			/* S'il ne s'agit pas d'un tableau */
			die("Veuillez renseigner un tableau en paramètre");
		}
		else if(array_empty($files)) {
			/* Si aucune table n'a été renseignée et allDB = false */
			die("Veuillez rensigner des fichiers à zipper");
		}
		if (!file_exists($location)) {
			/* Test si le dossier cible existe */
			die("Le dossier " . $location . " n'exsiste pas");
		}
		
		$zip = new zipfile () ; // On crée une instance zip
	
		$i = 0 ;
		while ( count( $files ) > $i )   {
			
			$fo 		= fopen($files[$i],'r') ; //on ouvre le fichier
			$contenu 	= fread($fo, filesize($files[$i])) ; 	//on enregistre le contenu
			fclose($fo) ; //on ferme fichier

			$zip->addfile($contenu, $files[$i]) ; //on ajoute le fichier
			$i++; //on incrémente i

		}

		$archive = $zip->file() ; // on associe l'archive

		// on enregistre l'archive dans un fichier
		if(!file_exists($location))
			mkdir($location);
		$fileZip 	= $location . '/' . $fileName;
		$open 		= fopen($fileZip , "wb");
		fwrite($open, $archive);
		fclose($open);
		
		return $fileZip;
	}
	
	/**
	 *
	 * Pour écrire un log
	 *
	 * @param $text	: string, le texte à écrire
	 * @param $file : string, le chemin du fichier log
	 * @return 		: boolean, true si l'opération a réussi, false sinon.
	 *
	 */
	function writeLog($text, $file) {
	
		if (strlen($text) == 0) {
			/* Test si le texte est vide */
			die("Veuillez renseigner un texte à logger");
		}
		
		$fichier = fopen($file, 'a+');
		if (fwrite($fichier, $text) != false){
			fclose($fichier);
			return true;
		}
		else {
			fclose($fichier);
			return false;
		}
	}
	
	/**
	 * 
	 * Convertit une date en timestamp
	 *
	 * @param $date	: string, format : jj/mm/aaa
	 * @return 		: timestamp int
	 *
	 */
	function unixtime($date, $inclus = false) {
		$_a = explode('/', $date);
		if ($inclus) {
			$_a[0]++;
		}
		return (sizeof($_a) == 3) ? mktime(0, 0, 0, $_a[1], $_a[0], $_a[2]) : null;
	}	
	
	/**
	 * 
	 * Retourne une couleur au hasard
	 * @return 	: string, code couleur
	 *
	 */
	function random_color(){
		mt_srand((double)microtime()*1000000);
		$color = '';
		while(strlen($color)<6){
			$color .= sprintf("%02X", mt_rand(0, 200));
		}
		return $color;
	}
	
	
	/**
	 *
	 * Afficher une matrice dans une table HTML
	 * @param $matrix	: la matrice à afficher
	 * @param $keys		: les clés de la matrice
	 *
	 */
	function printMatrix($matrix,$keys){
		$texte 	= '<table border=1><tr><td></td>';
		$nbkeys = count($keys);
		for($i = 0; $i < $nbkeys; $i++){
			$texte .= "<td>".$keys[$i] . "</td>";
		}	
		$texte .="</tr>";
		
		for($i = 0; $i < $nbkeys; $i++){
				$texte .= '<tr><td>'.$keys[$i] .'</td>';
				for($j = 0; $j < $nbkeys; $j++){
					$texte .= '<td>'.$matrix[$keys[$i]][$keys[$j]].'</td>';	 
				}					
				$texte .= "</tr>";
		}
		echo $texte.'</table>';
	}
	
	/**
	 *
	 * Vérifie si un fichier se trouve dans un répertoire
	 * @param $file	: string, fichier à vérifier
	 * @param $dir	: string, répertoire à vérifier
	 * @return 		: boolean, true si le fichier est contenu dans le répertoire, false sinon
	 *
	 */
	function isInDir($file, $dir){
		$contenu = array();			// Tableau contenant les répertoires du dossier $dir
		$dossier = opendir($dir);
		while ($fichier = readdir($dossier)) {
			if (substr($fichier, 0, 1) != ".") {	// S'il ne s'agit pas d'un fichier caché
				array_push($contenu, $fichier);
			}
		}
		closedir($dossier);
		
		return in_array($file, $contenu);
	}
	
	/**
	 *
	 * Convertit les cacactères spéciaux
	 *
	 */
	function html_entities($text,$utf8=true){
		if($utf8)
			return htmlentities(trim($text), ENT_QUOTES, "UTF-8");
		else 
			return htmlentities(trim($text), ENT_QUOTES);
	}
	
	function unhtmlentities($chaineHtml) {
		$tmp = get_html_translation_table(HTML_ENTITIES);
		$tmp = array_flip ($tmp);
		$tmp['&#039;'] = '\'';
		$chaineTmp = strtr ($chaineHtml, $tmp);
		
		return $chaineTmp;
	}
	
	/**                                                                
	 * Redimensionne la taille de la photo                                                                                             
	 * @param $img_file 		: le nom de la photo à redimensionner
	 * @param $type 			: type photo de profil ou photo produit 
	 * @param $img_max_width 	: la largeur max                                                
	 * @param $img_max_height 	: la hauteur max                                        
	 */
	function resize_image($img_file, $type, $img_max_width, $img_max_height) {
        
        if ($type == 'profil')
			$dir 	= './images/photos/';			// Chemin du dossier contenant la photo de profil
		else if ($type == 'produit')
			$dir 	= './images/photosProduits/';	// Chemin du dossier contenant la photo du produit

		$file 		= realpath($dir . $img_file);	// Chemin canonique absolu de l'image
        $img_infos 	= getimagesize($file);			// Récupération des infos de l'image
        $img_width 	= $img_infos[0];				// Largeur de l'image
        $img_height = $img_infos[1];				// Hauteur de l'image
        $img_type 	= $img_infos[2];				// Type de l'image
  
        // Sélection des variables selon l'extension de l'image
        switch ($img_type) {
                case 1: $img = imagecreatefromgif ($file); 	break;
                case 2: $img = imagecreatefromjpeg($file); 	break;
                case 3: $img = imagecreatefrompng ($file); 	break;
				default: 									break;
        }

        // Création de la vignette
        $img_thumb 	= imagecreatetruecolor($img_max_width, $img_max_height);
        $color		= imagecolorallocate($img_thumb, 255, 255, 255);   // Choix de la couleur
        imagefill($img_thumb,0,0,$color);
        
        //L’image est plus petite en hauteur et en largeur :
        //On recopie l'image telle quelle, sans la redimensionner, on la centre en hauteur et en largeur
        if(($img_width<=$img_max_width) and ($img_height<=$img_max_height)) {
            imagecopyresampled($img_thumb,$img,((int)(($img_max_width-$img_width)/2)),((int)(($img_max_height-$img_height)/2)),0,0,$img_width,$img_height,$img_width,$img_height);
        }
		
        //Cas n°2 : L’image sort du cadre et est proportionnement trop grande en largeur
        //On redimentionne pour avoir la bonne largeur et la centre en hauteur
        else if((($img_width>$img_max_width) or ($img_height>$img_max_height)) and (($img_width/$img_height)>($img_max_width/$img_max_height))) {
			$new_w=$img_max_width;
			$new_h=($img_height*$new_w)/$img_width;
			imagecopyresampled($img_thumb,$img,0,((int)(($img_max_height-$new_h)/2)),0,0,$new_w,$new_h,$img_width,$img_height);
        }
		
        //Cas n°3 : L’image sort du cadre et est proportionnement trop grande en hauteur :
        //On redimentionne pour avoir la bonne hauteur et on centre en largeur
        else if((($img_width>$img_max_width) or ($img_height>$img_max_height)) and (($img_width/$img_height)<=($img_max_width/$img_max_height))) {
			$new_h = $img_max_height;
			$new_w = ($img_width*$new_h) / $img_height;
			imagecopyresampled($img_thumb, $img,((int)(($img_max_width-$new_w)/2)),0,0,0,$new_w, $new_h, $img_width, $img_height);
        }

        // Insertion de l'image de base redimensionnée
        // Sélection de la vignette créée
        switch($img_type){
			case 1 : imagegif ($img_thumb, $dir . $img_file); 	break;
			case 2 : imagejpeg($img_thumb, $dir . $img_file); 	break;
			case 3 : imagepng ($img_thumb, $dir . $img_file); 	break;
			default: imagejpeg($img_thumb, $dir . $img_file);	break;
        }
	}
	
	
	
	/**
	 *
	 * Retourne les matrices distance et route des différents étages du magasin
	 *
	 */
	function getMatricePath($idEtage = 0) {
		
		$pdo = DB :: getInstance();
		$matrice_distance 	= array();
		$matrice_route 		= array();

		/*  récupération de la matrice du fichier */
		if($idEtage == 0){
			$arrayEtages = Etage::loadAll($pdo);

			foreach($arrayEtages as $etage){
				if(file_exists(applicationDir.'application/tmp/matrice_' . $etage->getIdetage() .'.php')){			
						require(applicationDir.'application/tmp/matrice_' . $etage->getIdetage() .'.php'); 
						$matrice_distance = array_merge_recursive($matrice_distance, ${'matrice_distance_' . $etage->getIdetage()});
						$matrice_route = array_merge_recursive($matrice_route, ${'matrice_route_' . $etage->getIdetage()});

				}
				else{
					throw new Exception(gettext('Le fichier de sauvegarde n\'a pas &eacute;t&eacute; trouv&eacute;. Veuillez vous assurer que la mod&eacute;lisation a bien &eacute;t&eacute; sauvegard&eacute;e'));
				}
			} 
		}
		else{		
			if(file_exists(applicationDir.'application/tmp/matrice_' . $idEtage .'.php')){
				require_once(applicationDir.'application/tmp/matrice_' . $idEtage .'.php');
				$matrice_distance = ${'matrice_distance_' . $idEtage};
				$matrice_route = ${'matrice_route_' . $idEtage};
			}
			else
				throw new Exception(gettext('Le fichier de sauvegarde n\'a pas &eacute;t&eacute; trouv&eacute;. Veuillez vous assurer que la mod&eacute;lisation a bien &eacute;t&eacute; sauvegard&eacute;e'));
		}

		return array($matrice_distance,$matrice_route);
	}
	
	function getSegmentsAPasser($lignes_commande=array(),$chargement=true,$matrice_distance=array(),$matrice_route=array(),$idEtage=1){
		$pdo = DB::getInstance();
		
		if($chargement){
			$matrices 			= getMatricePath();
			$matrice_distance 	= $matrices[0];
			$matrice_route 		= $matrices[1]; 			 
		}
		
		/* 	calcul du temps pour la liste de lignes_commande*/
		$keys 					= array();
		$arraySegmentAPasser 	= array(); 
		$nonModelises			= array();
		$inaccessibles			= array();
		$tempsSeconde 			= 0;
		$temps 					= 0;
		$qteTotale				= 0;
		$indice1				= 0;
		$indice2				= 0;
		if(count($lignes_commande) != 0){
			$keys 					= array();
			$arraySegmentAPasser	= array(); 	
			if($matrice_distance != null){
				$keys[] = 'ptd_'.$idEtage;
				$arraySegmentAPasser['ptd_'.$idEtage] = array('ptd', $idEtage, null);
				$debut = array();
				$debutkeys = array();
				$normal = array();
				$normalkeys = array();
				$fin = array();
				$finkeys = array();
				
				
				foreach($lignes_commande as $ligne){
					$produit 	= Produit::load($pdo,$ligne->getProduit()->getIdProduit());
					$priorite	= $produit->getPriorite();
					$etageres 	= $produit->selectEtageres();
					if ($etageres != null){
						$segment 	= $etageres[0]->getSegment();
						$idsegment 	= $segment->getIdsegment();
						$rayon		= $segment->getRayon();
						$idrayon	= $rayon->getIdrayon();
						$key		= $idrayon.'_'.$idsegment;
						
						$zone = $rayon->getZone();
						$etage = $zone->getEtage();
						
						if($etage->getIdetage() == $idEtage){
						
							switch ($priorite[0]){
								case 3:
									if (in_array($key, $debutkeys)){
										/* Si le produit se trouve dans une étagère déjà traitée */
										$debut[$key][2][] = $ligne;
									}
									else {
										if (count($debutkeys) > 0){
											if (isset($matrice_distance[$debutkeys[count($debutkeys)-1]][$key])){
												if($matrice_distance[$debutkeys[count($debutkeys)-1]][$key] < 1000000){
													$debutkeys[] = $key;
													$debut[$key] = array($idrayon, $idsegment, array($ligne));
												}
												else{
													/* Produit inaccessible */
													$inaccessibles[] = array($produit,$rayon,$ligne);
												}
											}
											else{
												/* Produit se trouvant dans un rayon non encore modélisé */
												$nonModelises[] = array($produit,$ligne);
											}
										}
										else{
											if (isset($matrice_distance[$keys[0]][$key])){
												
												if($matrice_distance[$keys[0]][$key] < 1000000){
													$debutkeys[] = $key;
													$debut[$key] = array($idrayon, $idsegment, array($ligne));
												}
												else{
													/* Produit inaccessible */
													$inaccessibles[] = array($produit,$rayon,$ligne);
												}
											}
											else{
												/* Produit se trouvant dans un rayon non encore modélisé */
												$nonModelises[] = array($produit,$ligne);
											}
										}
										
									}
									break;
								case 2:
									
									if (in_array($key, $normalkeys)){
										/* Si le produit se trouve dans une étagère déjà traitée */
										$normal[$key][2][] = $ligne;
										
									}
									else {
										
										if (count($normalkeys) > 0){
		
											if(isset($matrice_distance[$normalkeys[count($normalkeys)-1]][$key])){
											
												if($matrice_distance[$normalkeys[count($normalkeys)-1]][$key] < 1000000){
													$normalkeys[] = $key;
													$normal[$key] = array($idrayon, $idsegment, array($ligne));
												}
												else{
													/* Produit inaccessible */
													$inaccessibles[] = array($produit,$rayon,$ligne);
												}
											}
											else{
												/* Produit se trouvant dans un rayon non encore modélisé */
												$nonModelises[] = array($produit,$ligne);
											}
										}
										else{
											if (isset($matrice_distance[$keys[0]][$key])){
												if($matrice_distance[$keys[0]][$key] < 1000000){
													$normalkeys[] = $key;
													$normal[$key] = array($idrayon, $idsegment, array($ligne));
												}
												else{
													/* Produit inaccessible */
													$inaccessibles[] = array($produit,$rayon,$ligne);
												}
											}
											else{
												/* Produit se trouvant dans un rayon non encore modélisé */
												$nonModelises[] = array($produit,$ligne);
											}
										}
									}
									break;
								case 1:
									if (in_array($key, $finkeys)){
										/* Si le produit se trouve dans une étagère déjà traitée */
										$fin[$key][2][] = $ligne;
									}
									else {
										if (count($finkeys) > 0){
											if (isset($matrice_distance[$finkeys[count($finkeys)-1]][$key])){
												if($matrice_distance[$finkeys[count($finkeys)-1]][$key] < 1000000){
													$finkeys[] = $key;
													$fin[$key] = array($idrayon, $idsegment, array($ligne));
												}
												else{
													/* Produit inaccessible */
													$inaccessibles[] = array($produit,$rayon,$ligne);
												}
											}
											else{
												/* Produit se trouvant dans un rayon non encore modélisé */
												$nonModelises[] = array($produit,$ligne);
											}
										}
										else{
											if (isset($matrice_distance[$keys[0]][$key])){
												if($matrice_distance[$keys[0]][$key] < 1000000){
													$finkeys[] = $key;
													$fin[$key] = array($idrayon, $idsegment, array($ligne));
												}
												else{
													/* Produit inaccessible */
													$inaccessibles[] = array($produit,$rayon,$ligne);
												}
											}
											else{
												/* Produit se trouvant dans un rayon non encore modélisé */
												$nonModelises[] = array($produit,$ligne);
											}
										}
									}
									break;					
							}
						}
						else{
								$inaccessibles[] = array($produit,$rayon,$ligne);
						}
					}
					else{
						/* Produits non géolocalisés */
						$nonModelises[] = array($produit,$ligne);
					}
					$qteTotale += (int) $ligne->getQuantiteCommandee();
				}
				
				$arraySegmentAPasser = array_merge($arraySegmentAPasser, $debut);
				$indice1 = count($arraySegmentAPasser);
				foreach($normal as $key => $segment){
					if (array_key_exists($key, $debut)){
						$arraySegmentAPasser[$key][2][] = $segment[2];
					}
					else{
						$arraySegmentAPasser[$key] = $segment;
					}
				}
				$indice2 = count($arraySegmentAPasser);
				foreach($fin as $key => $segment){
					if (array_key_exists($key, $debut) || array_key_exists($key, $normal)){
						$arraySegmentAPasser[$key][2][] = $segment[2];
					}
					else{
						$arraySegmentAPasser[$key] = $segment;
					}
				}
				
				$arraySegmentAPasser['pta_'.$idEtage] = array('pta', $idEtage, null);
				$keys = array_keys($arraySegmentAPasser);

			}
		}
		$inaccessibles[] = array($produit,$rayon,$ligne);
		return array($arraySegmentAPasser, $keys, $qteTotale, $nonModelises, $inaccessibles, $indice1, $indice2);
	}
	
	/**
	 *
	 * Retourne le temps pour une liste de produit à récupérer (des lignes de commandes)
	 * si $chargement = false, il faut passer $matrice_distance et $matrice_route en paramètre
	 */
/* 	function getTempsListe($lignes_commande=array(),$chargement=true,$matrice_distance=array(),$matrice_route=array(),$zone=false, $idEtage=1){
		
		$pdo = DB::getInstance();
		
		if($chargement){
			$matrices 			= getMatricePath();
			$matrice_distance 	= $matrices[0];
			$matrice_route 		= $matrices[1]; 			 
		}
		
		$retour 				= getSegmentsAPasser($lignes_commande,false,$matrice_distance,$matrice_route,$idEtage);
		$arraySegmentAPasser 	= $retour[0];
		$keys 					= $retour[1];
		$qteTotale				= $retour[2];
		$indice1 				= $retour[5];
		$indice2				= $retour[6];
		$tempsSeconde			= 0;
		if (count($arraySegmentAPasser) != 0){
			$retourListePoints	= getListePoints($arraySegmentAPasser,$matrice_distance,$matrice_route,$keys,$indice1,$indice2);			
			$tempsSeconde 		= getTempsByPx(getDistancePx($retourListePoints[0]),$qteTotale);
		}
		return $tempsSeconde;
	} */
	
	function getTemps($ligneCommandes){
	/**
	 *
	 * Retourne le temps de préparation pour une liste de lignes de commandes
	 *  
	 */
 
 
	$uniques = array(); //Permet de créer un tableau avec l'identifiant produit en clé pour dédoublonner le tableau
 	$nbProduits = 0 ;
 	$nbReferences = count($ligneCommandes);
		
		foreach($ligneCommandes as $ligne){
		$nbProduits += $ligne->getQuantiteCommandee();
		    $key = $ligne->getProduit()->getIdProduit();
			//$qte = Ligne_Commande::selectSumCmdByProduit($pdo, $ligne->getProduit(), $ligne->getCommande());
			//$ligne->setQuantiteCommandee($qte->getQuantiteCommandee(), false);
		    $uniques[$key] = $ligne;
		}

		$ligneCommandes=$uniques;
 
		$temps = 0;
		
		foreach($ligneCommandes as $ligne){

		 	$temps += $ligne->getProduit()->getTempsMoyenAccess(); 	
		}
		/*print "Nb produits = " . $nbProduits ."<br>";
		print "nb articles = " .  $nbReferences . "<br>";
		print "temps" . $temps;*/
		
		/*
		Dans le calcul ci-dessous, "5" correspond au nombre de secondes que l'on ajoute si le préparateur prend un même produit plusieurs fois.'
		*/
		
		$temps = $temps + (($nbProduits-$nbReferences)*5);
		return $temps;
	}
 
	function getListePoints($arraySegmentAPasser,$matrice_distance,$matrice_route,$keys,$indice1=0,$indice2=0,$pvc=true){
	
		/* calcul du graphe complet */
		$graphe_complet = array();				
		
		foreach($arraySegmentAPasser as $segment){
			$fromSegment = $segment[0].'_'.$segment[1];	 			
			foreach($arraySegmentAPasser as $segment2){
				$toSegment = $segment2[0].'_'.$segment2[1]; 
				if(isset($matrice_distance[$fromSegment][$toSegment]))
					$graphe_complet[$fromSegment][$toSegment] = $matrice_distance[$fromSegment][$toSegment]; 								
			}			
		}  
		
			

		/* calcul du pvc */	
		$Hamiltonien 			= $keys;
		if ($pvc)
			$retourPVC = Pvc::calcul($graphe_complet, $Hamiltonien, $indice1, $indice2);
		else
			$retourPVC = $Hamiltonien;
		
		$newArray 				= array();
		foreach($retourPVC as $segment){
			if(isset($arraySegmentAPasser[$segment]))
				$newArray[$segment] = $arraySegmentAPasser[$segment];
		}
		
		

		$listeDePoints			= array();		
		$route 					= '';	
		if ($retourPVC != null) { 		
			$fromSegment 			= $retourPVC[0];
			/* obtenir la route en px */
			for($i = 1; $i < count($retourPVC); $i++){ 				
				$toSegment 	= $retourPVC[$i]; 
				if(isset($matrice_route[$fromSegment][$toSegment]))
					$route 		= $matrice_route[$fromSegment][$toSegment];  

				
				
				$points 	= explode(';',$route); 
				$couleur 	= random_color();
				
				foreach($points as $point){
					$coordonnees 	= explode(',',$point); 	// on récupère yyy,xxx
					if(isset($coordonnees[0]) && isset($coordonnees[1])){
						$top 			= $coordonnees[0]; 		//on récupère yyy
						$left 			= $coordonnees[1]; 		//on récupère xxx
						$listeDePoints[]= array($top,$left,$couleur);
					}
				}
				
				$fromSegment = $toSegment;
			}
		}
		return array($listeDePoints, $newArray);
	}
 
	function getDistancePx($listeDePoints){
		/* calcul de la distance en pixel */ 
		
		$distancePixel 	= 0;
		if(count($listeDePoints) == 0) return 0;
		$fromPointX 	= $listeDePoints[0][1];
		$fromPointY 	= $listeDePoints[0][0];
		for($i = 1; $i < count($listeDePoints); $i++){ 				
			$toPointX = $listeDePoints[$i][1];
			$toPointY = $listeDePoints[$i][0];				
			$distancePixel += round(sqrt(pow(($toPointY-$fromPointY), 2)+pow(($toPointX-$fromPointX), 2)));				
			$fromPointX = $toPointX;
			$fromPointY = $toPointY;
		}
		return $distancePixel;
	}
  
	function getTempsByPx($distancePixel,$nbPoints){		
			/* conversion en temps */
			$distanceMetre 	= getDistanceM($distancePixel);
			$tempsSeconde 	= ($distanceMetre/VITESSE_MparS) + ($nbPoints * TEMPS_PRELEVEMENT) + rand ( 0 , 60 ); 
			return intval($tempsSeconde);
	}
 
	function getDistanceM($distancePixel){
		return ($distancePixel/ECHELLE_1M);
	}
	
	function do_transforme_smarty($content,$smarty=true){
		if ($content != '') {
			$time=$content;
			if( $time>=3600){
				// si le nombre de secondes ne contient pas de jours mais contient des heures
				$heure = floor($time/3600);
				$reste = $time%3600;
				$minute = ceil($reste/60);
				$result = $heure.' h '.$minute.' min';
			}
			else{
			// si le nombre de secondes ne contient pas d'heures mais contient des minutes
			$minute = ceil($time/60);
			$seconde = $time%60;
			$result = $minute.' min';
			}
			
			if($smarty)
				echo $result;
			else
				return $result;
		}
		else{
			if($smarty)
				echo '0 min';
			else
				return '0 min';
		}
	}
	
	/*
		Parse le fichier planning.ini qui se trouve dans le répertoire /application/config/conf/
	*/
	function parsePlanningFileConf($pdo, $smarty){
	
		$pdoStatement = $pdo->prepare('SELECT * FROM CONFIG WHERE NAME_CONFIG LIKE "PLANNING_%"');
							 
		if (!$pdoStatement->execute()) {
			throw new Exception('Param&egrave;tre(s) inexistant(s) dans la Base de donn&eacute;es');
		}
		else {
			$configs = $pdoStatement->fetchAll();
			foreach ($configs as $config){
				if ($config ['NAME_CONFIG'] == 'PLANNING_HEURE_DEBUT') { $heure_debut = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'PLANNING_HEURE_FIN') { $heure_fin = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'PLANNING_JOURNEE_DEBUT') { $jour_debut = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'PLANNING_JOURNEE_FIN') { $jour_fin = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'PLANNING_MODE_CRENEAU') { $creneau = $config['VALUE_CONFIG'];}
			}
			
			define ('PLANNING_HEURE_DEBUT'	, $heure_debut);
			define ('PLANNING_HEURE_FIN'	, $heure_fin);
			define ('PLANNING_JOURNEE_DEBUT', $jour_debut);
			define ('PLANNING_JOURNEE_FIN'	, $jour_fin);
			define ('PLANNING_MODE_CRENEAU'	, $creneau); // 0 pour créneau à l'heure , 1 pour créneau à la demi heure
			
			$smarty->assign('planning_heure_debut'	, PLANNING_HEURE_DEBUT);
			$smarty->assign('planning_heure_fin'	, PLANNING_HEURE_FIN);
			$smarty->assign('planning_journee_debut', PLANNING_JOURNEE_DEBUT);
			$smarty->assign('planning_journee_fin'	, PLANNING_JOURNEE_FIN);
			$smarty->assign('planning_mode_creneau'	, PLANNING_MODE_CRENEAU);
			
			return true;	
		}	
		
	}
	
	/*
		Parse le fichier affectation.ini qui se trouve dans le répertoire /application/config/conf/
	*/
	function parseAffectationFileConf ($pdo) {
		
		$pdoStatement = $pdo->prepare('SELECT * FROM CONFIG WHERE NAME_CONFIG LIKE "AFFECTATION_%"');
							 
		if (!$pdoStatement->execute()) {
			throw new Exception('Param&egrave;tre(s) inexistant(s) dans la Base de donn&eacute;es');
		}
		else {
			$configs = $pdoStatement->fetchAll();
			foreach ($configs as $config){
				if ($config ['NAME_CONFIG'] == 'AFFECTATION_NB_COMMANDES_MAX') { $nbCommandesMax = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'AFFECTATION_TEMPS_PREPA_MAX') { $tempsPrepaMax = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'AFFECTATION_NB_REFERENCES_MAX') { $nbReferencesMax = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'AFFECTATION_NB_ARTICLES_MAX') { $nbArticlesMax = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'AFFECTATION_POIDS_MAX') { $poidsMax = $config['VALUE_CONFIG'];}
			}
			
			define ('NB_COMMANDES_MAX'	, $nbCommandesMax);
			define ('TEMPS_PREPA_MAX'	, $tempsPrepaMax);
			define ('NB_REFERENCES_MAX'	, $nbReferencesMax);
			define ('NB_ARTICLES_MAX'	, $nbArticlesMax);
			define ('POIDS_MAX'			, $poidsMax); 
			
			return true;	
		}	

	}
	
	/*
		Parse le fichier application.ini qui se trouve dans le répertoire /application/config/conf/
	*/
	function parseApplicationConfigFile($pdo, $smarty=null){
		
		$pdoStatement = $pdo->prepare('SELECT * FROM CONFIG WHERE NAME_CONFIG LIKE "APPLICATION_%"');
							 
		if (!$pdoStatement->execute()) {
			throw new Exception('Param&egrave;tre(s) inexistant(s) dans la Base de donn&eacute;es');
		}
		else {
			$configs = $pdoStatement->fetchAll();
			foreach ($configs as $config){
				if ($config ['NAME_CONFIG'] == 'APPLICATION_PATH') 						{ $appliPath = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_PREFIXE') 					{ $appliPrefixe = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_FUSEAU_HORAIRE')			{ $fuseau = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_LANGUAGE') 					{ $language = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_ABBREVIATION_LANGUAGE') 	{ $abbreviation_language = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_DEVISE') 					{ $devise = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_RESULTAT_PAR_PAGE') 		{ $resultatParPage = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_EMAIL_RAPPORT') 			{ $emailRapport = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_NOMBRE_ETAGES') 			{ $nombreEtages = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_FINESSE_UTILISEE') 			{ $finesse = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_LARGEUR_ETAGERE') 			{ $largeurEtagere = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_DELAI_AVANT_LIVRAISON') 	{ $delaiAvantLivraison = $config['VALUE_CONFIG'];}
				if ($config ['NAME_CONFIG'] == 'APPLICATION_TEMPS_MOYEN_ACCES_PRODUIT') { $tempsMoyenAccesProduit = $config['VALUE_CONFIG'];}
			}
			
			define ('APPLICATION_PATH' 			, $appliPath);
			date_default_timezone_set($fuseau);
			define ('APPLICATION_PREFIXE' 		, $appliPrefixe);
			define ('APPLICATION_FUSEAU' 		, $fuseau);
			/* Configuration de GetText */
			define('LANGUAGE'					, $language);		// On définie une constante qui contient le code
			define('ABBREVIATION_LANGUAGE'		, $abbreviation_language);		// On définie une constante qui contient l'abbreviation
			define('LANGUAGE_FILE_NAME'			, 'messages');	// Le nom de nos fichiers .mo
			putenv("LANG=" . LANGUAGE . '.UTF8'); // On modifie la variable d'environnement
			setlocale(LC_ALL, LANGUAGE . '.UTF8'); // On modifie les informations de localisation en fonction de la langue
			setlocale (LC_TIME,  LANGUAGE, ABBREVIATION_LANGUAGE);
			bindtextdomain(LANGUAGE_FILE_NAME, applicationDir.'application/lang'); // On indique le chemin vers les fichiers .mo
			bind_textdomain_codeset ( LANGUAGE_FILE_NAME , "UTF-8" );
			textdomain(LANGUAGE_FILE_NAME); // Le nom du domaine par défaut
			/* Devise utilisée */
			define ('DEVISE'					, $devise);
			/* Nombre de résultat par page */
			define ('RESULTAT_PAR_PAGE'			, (int) $resultatParPage);
			/* Emails Rapport */
			define ('EMAIL_RAPPORT'			, $emailRapport);
			/* Finesse de géolocalisation utilisée */
			define ('NOMBRE_ETAGES'			, (int) $nombreEtages);
			/* Finesse de géolocalisation utilisée */
			define ('FINESSE_UTILISEE'			, (int) $finesse);
			/* Largeur par défaut des étagère du magasin */
			define ('LARGEUR_ETAGERE' 			, (float) $largeurEtagere);
			/* Délai avant la livraison */
			define ('DELAI_AVANT_LIVRAISON'		, (int) $delaiAvantLivraison);
			/* Temps moyen d'accès à un produit (en seconde)*/
			define ('TEMPS_MOYEN_ACCES_PRODUIT'			, (int) $tempsMoyenAccesProduit);
				
					
			$smarty->assign('application_path'	, APPLICATION_PATH);
			if($smarty != null) $smarty->assign('application_prefixe'	, APPLICATION_PREFIXE);
			if($smarty != null) $smarty->assign('devise'			, DEVISE);
			if($smarty != null) $smarty->assign('nombre_etages'	, NOMBRE_ETAGES);
			if($smarty != null) $smarty->assign('finesse_utilisee'	, FINESSE_UTILISEE);
			if($smarty != null) $smarty->assign('delai_avant_livraison'	, DELAI_AVANT_LIVRAISON);
			
			return true;	
		}	
				
	}
	/*
		Retourne le quadrillage ou se trouve l'objet (pour la fusion des points lors de la sauvegarde de la modélisation)
	*/
	function getCarres($object, $type, $largeurCarre){
	
		if ($type == 'obstacle')
			$sens 	= 0;
		else
			$sens 	= $object->getSens();
		
		$top 		= $object->getPosition_top();
		$left		= $object->getPosition_left();
		$largeur 	= $object->getLargeur();
		$hauteur	= $object->getHauteur();
		$cos45		= cos(deg2rad(45));
		$carres 	= array();
		
		
		/* Calcul des points Top Left du carré encadrant le rayon */
		switch ($sens){
			
			case 0 :
				$newTop 	= $top;
				$newLeft	= $left;
				break;
			
			case 45 :
				$newTop 	= $top;
				$newLeft	= $left - ($hauteur * $cos45);
				$newBottom	= $top + ($hauteur * $cos45) + ($largeur * $cos45);
				$newRight	= $left + ($largeur * $cos45);
				break;
			
			case 90 :
				$newTop 	= $top;
				$newLeft	= $left - $hauteur;
				break;
			
			case 135 :
				$newTop 	= $top - ($hauteur * $cos45);
				$newLeft	= $left - ($hauteur * $cos45) - ($largeur * $cos45); 
				$newBottom	= $top + ($largeur + $cos45);
				$newRight	= $left;
				break;
			
			case 180 :
				$newTop 	= $top - $hauteur;
				$newLeft	= $left - $largeur;
				break;
			
			case 225 :
				$newTop 	= $top - ($hauteur * $cos45) - ($largeur * $cos45); 
				$newLeft	= $left - ($largeur * $cos45);
				$newBottom	= $top; 
				$newRight	= $left - ($hauteur * $cos45);
				break;
			
			case 270 :
				$newTop 	= $top - $largeur;
				$newLeft	= $left;
				break;
			
			case 315 :
				$newTop 	= $top - ($hauteur * $cos45) - ($largeur * $cos45); 
				$newLeft	= $left - ($hauteur * $cos45);
				$newBottom	= $top + ($hauteur * $cos45);
				$newRight	= $left + ($hauteur * $cos45) + ($largeur * $cos45); 
				break;
			
			default :
		}
		
		/* Le carré dans lequel le point Top Left calculé ci-dessus se trouve */
		$ligne		= intval($newTop  / $largeurCarre);
		$colonne 	= intval($newLeft / $largeurCarre);
		$carres[]	= '' . $ligne . ',' . $colonne;
		
		
		/* Test si le rayon fait parti d'autres carrés */
		switch ($sens){
			
			case 0:
			case 180:
				if (((($newLeft + $largeur) / $largeurCarre) > ($colonne + 1)) && ((($newTop + $hauteur) / $largeurCarre) > ($ligne + 1)))
					$carres[]	= '' . ($ligne + 1) . ',' . ($colonne + 1);
				if ((($newLeft + $largeur) / $largeurCarre) > ($colonne + 1))
					$carres[]	= '' . $ligne . ',' . ($colonne + 1);
				if ((($newTop + $hauteur) / $largeurCarre) > ($ligne +1))
					$carres[]	= '' . ($ligne + 1) . ',' . $colonne;
				break;
			
			case 90:
			case 270:
				if (((($newLeft + $hauteur) / $largeurCarre) > ($colonne +1)) && ((($newTop + $largeur) / $largeurCarre) > ($ligne+1)))
					$carres[]	= '' . ($ligne + 1) . ',' . ($colonne + 1);
				if ((($newLeft + $hauteur) / $largeurCarre) > ($colonne + 1))
					$carres[]	= '' . $ligne . ',' . ($colonne + 1);
				if ((($newTop + $largeur) / $largeurCarre) > ($ligne + 1))
					$carres[]	= '' . ($ligne + 1) . ',' . $colonne;
				break;
				
			case 45:
			case 135:
			case 225:
			case 315:
				$largeurConteneur = $newRight - $newLeft;
				$hauteurConteneur = $newBottom - $newTop;
				
				if (((($newLeft + $largeurConteneur) / $largeurCarre) > ($colonne + 1)) && ((($newTop + $hauteurConteneur) / $largeurCarre) > ($ligne + 1)))
					$carres[]	= '' . ($ligne + 1) . ',' . ($colonne + 1);
				if ((($newLeft + $largeurConteneur) / $largeurCarre) > ($colonne + 1))
					$carres[]	= '' . $ligne . ',' . ($colonne + 1);
				if ((($newTop + $hauteurConteneur) / $largeurCarre) > ($ligne + 1))
					$carres[]	= '' . ($ligne + 1) . ',' . $colonne;
				break;
				
			default:
		}

		return $carres;
	}
	
	class PDF extends FPDF {
		function Header() {
			$this->SetFont('Times', 'B', 15);
			$this->Cell(0, 10, 'Bon de préparation', 0, 0, 'C');
			$this->Ln(15);
		}

		function Footer() {
			$this->SetY(-12);
			$this->SetFont('Times', 'I', 8);
			$this->Cell(0, 10, $this->PageNo() . '/{nb}', 0, 0, 'C');
		}
	}
	
	/**
	 *
	 *	Génération d'un bon de préparation sous format PDF
	 *
	 */
	function preparationPDF($prepa, $lignes, $nomFichier){
		$pdo = DB :: getInstance();
		
		/* Dimensions page */
		$wFeuille = 210;
		$hFeuille = 297;
		$marge = 6;
		$largeur = $wFeuille -2 * $marge;
		
		$colonne1 = 0.25 * $largeur;
		$colonne2 = 0.24 * $largeur;
		$colonne3 = 0.15 * $largeur;
		$colonne4 = 0.08 * $largeur;
		$colonne5 = 0.04 * $largeur;
		$colonne6 = 0.09 * $largeur;
		$colonne7 = 0.15 * $largeur;
	
		if($prepa != null){
			$pdf = new PDF('P', 'mm', 'A4');
			$pdf->SetMargins($marge, 3, $marge);
			$pdf->AliasNbPages();
			$pdf->AddFont('CodeBarre','','c39hrp24dhtt.php');
			$pdf->AddPage("","");
			
			$pdf->Cell(0, 6, 'Préparation n°' . $prepa->getIdPreparation(), 0, 1, 'C');
			$pdf->Ln(4);
			
			$yHaut = $pdf->GetY();
			
			/* Préparateur */
			$pdf->SetFont('Times', '', 12);
			$chaine = 'Préparateur' . ' : ';
			$wLibelle = $pdf->GetStringWidth($chaine);
			$pdf->Cell($wLibelle, 6, $chaine, 0, 0, 'L');
			$pdf->SetFont('Times', 'B', 12);
			$pdf->Cell($largeur / 2 - $wLibelle, 6, $prepa->getUtilisateur()->getPrenom() . ' ' . $prepa->getUtilisateur()->getNom() , 0, 1, 'L');
			
			/* Date de fin de préparation */
			$chaine = 'Date de fin de prépration' . ' : ';
			$wLibelle = $pdf->GetStringWidth($chaine);
			$pdf->SetFont('Times', '', 12);
			$pdf->Cell($wLibelle, 6, $chaine, 0, 0, 'L');
			$date = $prepa->getDate_preparation();
			$dmy = date('d/m/Y',$date);
			$heure = 'à' . ' ' . date('H \h i',$date);
			$pdf->SetFont('Times', 'B', 12);
			$pdf->Cell($largeur / 2 - $wLibelle, 6, $dmy . ' ' . $heure , 0, 1, 'L');
			
			/* Préparateur */
			$pdf->SetFont('Times', '', 12);
			$chaine = 'Mode de préparation' . ' : ';
			$wLibelle = $pdf->GetStringWidth($chaine);
			$pdf->Cell($wLibelle, 6, $chaine, 0, 0, 'L');
			$pdf->SetFont('Times', 'B', 12);
			$pdf->Cell($largeur / 2 - $wLibelle, 6, $prepa->getModePreparation() , 0, 1, 'L');
			
			/* Durée de préparation */
			$pdf->SetFont('Times', '', 12);
			$chaine = 'Durée approximative ' . ' : ';
			$wLibelle = $pdf->GetStringWidth($chaine);
			$pdf->Cell($wLibelle, 6, $chaine, 0, 0, 'L');
			$pdf->SetFont('Times', 'B', 12);
			$duree = do_transforme_smarty($prepa->getDuree(),false);
			$pdf->Cell($largeur / 2 - $wLibelle, 6, $duree , 0, 1, 'L');
			
			/* Tracer le cadre */
			$yBas = $pdf->GetY();
			$pdf->SetXY($marge, $yHaut);
			$pdf->Cell(0, $yBas - $yHaut, "", 1, 1);
		
			$pdf->Ln(5);
			
			/* Composition de la commande */
			$pdf->SetFont('Times', 'B', 14);
			$pdf->SetFillColor(215, 215, 215);
			$chaine = gettext('Composition de la commande') . ' : ';
			$pdf->Cell($pdf->GetStringWidth($chaine), 8, $chaine, 0, 0, 'L', 1);
			$nbReferences 	= count($lignes);
			$nbProduits		= 0;
			foreach($lignes as $ligne){
				$nbProduits += $ligne[0]->getQuantiteCommandee();
			}
			$txt_produits = ($nbProduits > 1) ? gettext('produits'):gettext('produit');
			$txt_references = ($nbReferences > 1) ? 'références':'références';
			$pdf->Cell($largeur - $pdf->GetStringWidth($chaine), 8, $nbProduits . ' ' . $txt_produits . ' ' . gettext('pour') . ' ' . $nbReferences . ' ' . $txt_references, 0, 1, 'L', 1);
			
			$pdf->Ln(3);

			$pdf->SetFont('Times', '', 12);
			
			/* Lignes de commande */
			$pdf->Cell($colonne1, 6, gettext('Article'), 1, 0, 'C', 1);
			$pdf->Cell($colonne2, 6, gettext('Code_EAN'), 1, 0, 'C', 1);
			$pdf->Cell($colonne3, 6, gettext('Rayon'), 1, 0, 'C', 1);
			$pdf->Cell($colonne4, 6, gettext('Geo') . '.', 1, 0, 'C', 1);
			$pdf->Cell($colonne5, 6, gettext('Qte'), 1, 0, 'C', 1);
			$pdf->Cell($colonne6, 6, gettext('Prix_TTC'), 1, 0, 'C', 1);
			$pdf->Cell($colonne7, 6, gettext('Client'), 1, 1, 'C', 1);
			
			$total = 0;
			
			foreach($lignes as $ligne){
				if ($pdf->GetY() > $hFeuille -40) {
					$pdf->AddPage('','');
					/* Ligne horizontale */
					$pdf->Line($marge, $pdf->GetY(), $wFeuille - $marge, $pdf->GetY());
				}
				
				/* Libelle du produit */
				$yHaut = $pdf->GetY();
				$pdf->MultiCell($colonne1, 5, unhtmlentities($ligne[0]->getProduit()->getLibelle()), 0, 'L');
				$yBas = $pdf->GetY();
				$pdf->setXY($marge + $colonne1, $yHaut);
				
				/* Liste des codes EANs */
				$pdf->SetFont('CodeBarre', '', 26);
				$listeEAN = '';
				foreach ($ligne[0]->getProduit()->selectEans() as $ean) {
					$listeEAN .= '*' . $ean->getEan() . '*' . chr(10);
				}
				$pdf->MultiCell($colonne2, 11, $listeEAN, 0, 'C');
				$pdf->SetFont('Arial', '', 12);
				if ($pdf->GetY() > $yBas) {
					$yBas = $pdf->GetY();
				}
				$pdf->setXY($marge + $colonne1 + $colonne2, $yHaut);
				
				/* Libelle du rayon */
				$pdf->MultiCell($colonne3, 5, $ligne[1], 0, 'L');
				if ($pdf->GetY() > $yBas) {
					$yBas = $pdf->GetY();
				}
				
				/* Code de géolocalisation */
				$pdf->setXY($marge + $colonne1 + $colonne2 + $colonne3, $yHaut);
				$pdf->MultiCell($colonne4, 5, $ligne[2], 0, 'L');
				if ($pdf->GetY() > $yBas) {
					$yBas = $pdf->GetY();
				}
				
				/* Quantité */
				$pdf->setXY($marge + $colonne1 + $colonne2 + $colonne3 + $colonne4, $yHaut);
				$pdf->MultiCell($colonne5, 5, $ligne[0]->getQuantiteCommandee(), 0, 0, 'C');
				if ($pdf->GetY() > $yBas) {
					$yBas = $pdf->GetY();
				}
				
				/* Prix TTC */
				$pdf->setXY($marge + $colonne1 + $colonne2 + $colonne3 + $colonne4 + $colonne5, $yHaut);
				$pdf->MultiCell($colonne6, 5, number_format($ligne[0]->getPrixUnitaireTTC(), 2, ',', ' ') . ' €', 0, 'R');
				if ($pdf->GetY() > $yBas) {
					$yBas = $pdf->GetY();
				}
				$total += $ligne[0]->getPrixUnitaireTTC() * $ligne[0]->getQuantiteCommandee();
				
				/* Libelle du produit */
				$pdf->setXY($marge + $colonne1 + $colonne2 + $colonne3 + $colonne4 + $colonne5 + $colonne6, $yHaut);
				$prenomClient = unhtmlentities($ligne[0]->getCommande()->getClient()->getPrenom());
				$nomClient = unhtmlentities($ligne[0]->getCommande()->getClient()->getNom());
				$pdf->MultiCell($colonne7, 5, $prenomClient . ' ' . $nomClient, 0, 'L');
				if ($pdf->GetY() > $yBas) {
					$yBas = $pdf->GetY();
				}
				
				$pdf->setY($yBas);
				
				/* Ligne horizontale */
				$pdf->Line($marge, $yBas, $wFeuille - $marge, $yBas);
		
				/* Lignes verticales */
				$x = $marge;
				$pdf->Line($x, $yHaut, $x, $yBas);
				$x += $colonne1;
				$pdf->Line($x, $yHaut, $x, $yBas);
				$x += $colonne2;
				$pdf->Line($x, $yHaut, $x, $yBas);
				$x += $colonne3;
				$pdf->Line($x, $yHaut, $x, $yBas);
				$x += $colonne4;
				$pdf->Line($x, $yHaut, $x, $yBas);
				$x += $colonne5;
				$pdf->Line($x, $yHaut, $x, $yBas);
				$x += $colonne6;
				$pdf->Line($x, $yHaut, $x, $yBas);
				$x += $colonne7;
				$pdf->Line($x, $yHaut, $x, $yBas);
		
				$pdf->SetTextColor(0,0,0);
			}
			
			if ($pdf->GetY() > $hFeuille -80) {
				$pdf->addPage('','');
			}
			
			/* Prix total */
			$yHaut = $pdf->GetY();
			$pdf->SetFont('Times', '', 12);
			$colonneCout = $colonne6 + $colonne7;
			$colonneLibelle = $largeur - $colonneCout;
			$pdf->Cell($colonneLibelle, 6, gettext('Total'), 'R', 0, 'R');
			$pdf->Cell($colonneCout, 6, number_format($total, 2, ',', ' ') . ' €', 0, 1, 'R');
			$yBas = $pdf->GetY();
			$pdf->SetXY($marge, $yHaut);
			$pdf->Cell(0, $yBas - $yHaut, "", 1, 1);
			
			
			$pdf->Output($nomFichier, 'F', "");
			
			return true;
		}
		else{
			return false;
		}
	}
	
	function retourCommandeXml($pdo, $arrayCommandes, $EXPORT_DIR){
		/* Génération des fichiers xml de retour */
		foreach($arrayCommandes as $com){
			if ($com->isPrepared()){
				$client = $com->getClient();
				
				/* Si la commande a été entièrement préparée => on génère le fichier xml */
				$save = new DomDocument('1.0', 'UTF-8');
				$save -> formatOutput = true;

				/* Noeud racine */
				$commande = $save->createElement('commande');
				$save->appendChild($commande);
				
				/* Création d'un noeud idCommande */
				$idCommande = $save->createElement('idCommande');
				$commande -> appendChild($idCommande); 
				$idCommande -> appendChild($save -> createTextNode($com->getCodeCommande()));
				
				/* Création d'un noeud dateCommande*/
				$dateCommande = $save->createElement('dateCommande');
				$commande -> appendChild($dateCommande); 
				$dateCommande -> appendChild($save -> createTextNode($com->getDateCommande()));
				
				/* Création d'un noeud modeLivraison*/
				$modeLivraison = $save->createElement('modeLivraison');
				$commande -> appendChild($modeLivraison); 
				$modeLivraison -> appendChild($save -> createTextNode($com->getModeLivraison()));
				
				/* Création d'un noeud dateLivraison*/
				$dateLivraison = $save->createElement('dateLivraison');
				$commande -> appendChild($dateLivraison); 
				$dateLivraison -> appendChild($save -> createTextNode($com->getDateLivraison()));
				
				/* Création d'un noeud codePaysLivraison*/
				$codePaysLivraison = $save->createElement('codePaysLivraison');
				$commande -> appendChild($codePaysLivraison); 
				$codePaysLivraison -> appendChild($save -> createTextNode($com->getCodePaysLivraison()));
				
				/* Création d'un noeud codePostalLivraison */
				$codePostalLivraison = $save->createElement('codePostalLivraison');
				$commande -> appendChild($codePostalLivraison); 
				$codePostalLivraison -> appendChild($save -> createTextNode($com->getCodePostalLivraison()));
				
				/* Création d'un noeud codeInseeLivraison*/
				$codeInseeLivraison = $save->createElement('codeInseeLivraison');
				$commande -> appendChild($codeInseeLivraison); 
				$codeInseeLivraison -> appendChild($save -> createTextNode($com->getCodeInseeLivraison()));
				
				/* Création d'un noeud regionLivraison*/
				$regionLivraison = $save->createElement('regionLivraison');
				$commande -> appendChild($regionLivraison); 
				$regionLivraison -> appendChild($save -> createTextNode($com->getRegionLivraison()));
				
				/* Création d'un noeud municipaliteLivraison*/
				$municipaliteLivraison = $save->createElement('municipaliteLivraison');
				$commande -> appendChild($municipaliteLivraison); 
				$municipaliteLivraison -> appendChild($save -> createTextNode($com->getMunicipaliteLivraison()));
				
				/* Création d'un noeud ligneAdresseLivraison*/
				$ligneAdresseLivraison = $save->createElement('ligneAdresseLivraison');
				$commande -> appendChild($ligneAdresseLivraison); 
				$ligneAdresseLivraison -> appendChild($save -> createTextNode($com->getLigneAdresseLivraison()));
				
				/* Création d'un noeud nomRueLivraison*/
				$nomRueLivraison = $save->createElement('nomRueLivraison');
				$commande -> appendChild($nomRueLivraison); 
				$nomRueLivraison -> appendChild($save -> createTextNode($com->getNomRueLivraison()));
				
				/* Création d'un noeud numeroBatimentLivraison*/
				$numeroBatimentLivraison = $save->createElement('numeroBatimentLivraison');
				$commande -> appendChild($numeroBatimentLivraison); 
				$numeroBatimentLivraison -> appendChild($save -> createTextNode($com->getNumeroBatimentLivraison()));
				
				/* Création d'un noeud uniteLivraison*/
				$uniteLivraison = $save->createElement('uniteLivraison');
				$commande -> appendChild($uniteLivraison); 
				$uniteLivraison -> appendChild($save -> createTextNode($com->getUniteLivraison()));
				
				/* Création d'un noeud boitePostaleLivraison*/
				$boitePostaleLivraison = $save->createElement('boitePostaleLivraison');
				$commande -> appendChild($boitePostaleLivraison); 
				$boitePostaleLivraison -> appendChild($save -> createTextNode($com->getBoitePostaleLivraison()));
				
				/* Création d'un noeud destinataireLivraisonNom*/
				$destinataireLivraison = $save->createElement('destinataireLivraisonNom');
				$commande -> appendChild($destinataireLivraison); 
				$destinataireLivraison -> appendChild($save -> createTextNode($com->getDestinataireLivraison()));
				
				/* Création d'un noeud destinataireLivraisonPrenom */
				$destinataireLivraison = $save->createElement('destinataireLivraisonPrenom');
				$commande -> appendChild($destinataireLivraison); 
				$destinataireLivraison -> appendChild($save -> createTextNode(''));
				
				/* Création d'un noeud idClient*/
				$idClient = $save->createElement('idClient');
				$commande -> appendChild($idClient); 
				$idClient -> appendChild($save -> createTextNode($client->getIdClient()));
				
				/* Création d'un noeud nomClient*/
				$nomClient = $save->createElement('nomClient');
				$commande -> appendChild($nomClient); 
				$nomClient -> appendChild($save -> createTextNode($client->getNom()));
				
				/* Création d'un noeud prenomClient*/
				$prenomClient = $save->createElement('prenomClient');
				$commande -> appendChild($prenomClient); 
				$prenomClient -> appendChild($save -> createTextNode($client->getPrenom()));
				
				/* Création d'un noeud civiliteCLient*/
				$civiliteCLient = $save->createElement('civiliteCLient');
				$commande -> appendChild($civiliteCLient); 
				$civiliteCLient -> appendChild($save -> createTextNode($client->getCivilite()));
				
				/* Création d'un noeud nomEntreprise*/
				$nomEntreprise = $save->createElement('nomEntreprise');
				$commande -> appendChild($nomEntreprise); 
				$nomEntreprise -> appendChild($save -> createTextNode($client->getNomEntreprise()));
				
				/* Création d'un noeud telephoneClient*/
				$telephoneClient = $save->createElement('telephoneClient');
				$commande -> appendChild($telephoneClient); 
				$telephoneClient -> appendChild($save -> createTextNode($client->getTelephone()));
				
				/* Création d'un noeud codePaysFacturation*/
				$codePaysFacturation = $save->createElement('codePaysFacturation');
				$commande -> appendChild($codePaysFacturation); 
				$codePaysFacturation -> appendChild($save -> createTextNode($client->getCodePaysFacturation()));
				
				/* Création d'un noeud codePostalFacturation*/
				$codePostalFacturation = $save->createElement('codePostalFacturation');
				$commande -> appendChild($codePostalFacturation); 
				$codePostalFacturation -> appendChild($save -> createTextNode($client->getCodePostalFacturation()));
				
				/* Création d'un noeud codeInseeFacturation */
				$codeInseeFacturation = $save->createElement('codeInseeFacturation');
				$commande -> appendChild($codeInseeFacturation); 
				$codeInseeFacturation -> appendChild($save -> createTextNode($client->getCodeInseeFacturation()));
				
				/* Création d'un noeud regionFacturation*/
				$regionFacturation = $save->createElement('regionFacturation');
				$commande -> appendChild($regionFacturation); 
				$regionFacturation -> appendChild($save -> createTextNode($client->getRegionFacturation()));
				
				/* Création d'un noeud municipaliteFacturation*/
				$municipaliteFacturation = $save->createElement('municipaliteFacturation');
				$commande -> appendChild($municipaliteFacturation); 
				$municipaliteFacturation -> appendChild($save -> createTextNode($client->getMunicipaliteFacturation()));
				
				/* Création d'un noeud ligneAdresseFacturation*/
				$ligneAdresseFacturation = $save->createElement('ligneAdresseFacturation');
				$commande -> appendChild($ligneAdresseFacturation); 
				$ligneAdresseFacturation -> appendChild($save -> createTextNode($client->getLigneAdresseFacturation()));
				
				/* Création d'un noeud nomRueFacturation*/
				$nomRueFacturation = $save->createElement('nomRueFacturation');
				$commande -> appendChild($nomRueFacturation); 
				$nomRueFacturation -> appendChild($save -> createTextNode($client->getNomRueFacturation()));
				
				/* Création d'un noeud numeroBatimentFacturation*/
				$numeroBatimentFacturation = $save->createElement('numeroBatimentFacturation');
				$commande -> appendChild($numeroBatimentFacturation); 
				$numeroBatimentFacturation -> appendChild($save -> createTextNode($client->getNumeroBatimentFacturation()));
				
				/* Création d'un noeud uniteFacturation*/
				$uniteFacturation = $save->createElement('uniteFacturation');
				$commande -> appendChild($uniteFacturation); 
				$uniteFacturation -> appendChild($save -> createTextNode($client->getUniteFacturation()));
				
				/* Création d'un noeud boitePostaleFacturation*/
				$boitePostaleFacturation = $save->createElement('boitePostaleFacturation');
				$commande -> appendChild($boitePostaleFacturation); 
				$boitePostaleFacturation -> appendChild($save -> createTextNode($client->getBoitePostaleFacturation()));
				
				/* Création d'un noeud destinataireFacturation*/
				$destinataireFacturation = $save->createElement('destinataireFacturation');
				$commande -> appendChild($destinataireFacturation); 
				$destinataireFacturation -> appendChild($save -> createTextNode($client->getDestinataireFacturation()));
				
				/* Création d'un noeud carteFidelite*/
				$carteFidelite = $save->createElement('carteFidelite');
				$commande -> appendChild($carteFidelite); 
				$carteFidelite -> appendChild($save -> createTextNode($com->getCarteFidelite()));
				
				/* Création d'un noeud commentaireClient*/
				$commentaireClient = $save->createElement('commentaireClient');
				$commande -> appendChild($commentaireClient); 
				$commentaireClient -> appendChild($save -> createTextNode($com->getCommentaireClient()));
				
				/* Création d'un noeud lignesCommandes*/
				$lignesCommandes = $save->createElement('lignesCommandes');
				$commande -> appendChild($lignesCommandes); 

				$lignes = $com->selectLigne_commandes();
				foreach($lignes as $ligne){
				
					/* Création d'un noeud ligneCommande */
					$ligneCommande = $save->createElement('ligneCommande');
					$lignesCommandes -> appendChild($ligneCommande);  
					
					/* Création d'un noeud idProduit*/
					$idProduit = $save->createElement('idProduit');
					$ligneCommande -> appendChild($idProduit); 
					$idProduit -> appendChild($save -> createTextNode($ligne->getProduit()->getCodeProduit()));
					
					/* Création d'un noeud quantiteCommandee*/
					$quantiteCommandee = $save->createElement('quantiteCommandee');
					$ligneCommande -> appendChild($quantiteCommandee); 
					$quantiteCommandee -> appendChild($save -> createTextNode($ligne->getQuantiteCommandee()));
					
					/* Création d'un noeud estDansUnLot*/
					$estDansUnLot = $save->createElement('estDansUnLot');
					$ligneCommande -> appendChild($estDansUnLot); 
					$estDansUnLot -> appendChild($save -> createTextNode($ligne->getEstDansUnLot()));
					
					/* Création d'un noeud idLot*/
					$idLot = $save->createElement('idLot');
					$ligneCommande -> appendChild($idLot); 
					$idLot -> appendChild($save -> createTextNode($ligne->getIdLot()));
					
					/* Création d'un noeud libelleLot*/
					$libelleLot = $save->createElement('libelleLot');
					$ligneCommande -> appendChild($libelleLot); 
					$libelleLot -> appendChild($save -> createTextNode($ligne->getLibelleLot()));
					
					/* Création d'un noeud codeEanLot*/
					$codeEanLot = $save->createElement('codeEanLot');
					$ligneCommande -> appendChild($codeEanLot); 
					$codeEanLot -> appendChild($save -> createTextNode($ligne->getCodeEanLot()));
					
					/* Création d'un noeud prixUnitaireTTC*/
					$prixUnitaireTTC = $save->createElement('prixUnitaireTTC');
					$ligneCommande -> appendChild($prixUnitaireTTC); 
					$prixUnitaireTTC -> appendChild($save -> createTextNode($ligne->getPrixUnitaireTTC()));
					
					$prelevement = Prelevement_realise::selectByLigne_commande($pdo, $ligne);

					/* Création d'un noeud */
					$codeEanScanne = $save->createElement('codeEanScanne');
					$ligneCommande -> appendChild($codeEanScanne); 
					$codeEanScanne -> appendChild($save -> createTextNode($prelevement->getEan_lu()));
					
					/* Création d'un noeud */
					$quantitePreparee = $save->createElement('quantitePreparee');
					$ligneCommande -> appendChild($quantitePreparee); 
					$quantitePreparee -> appendChild($save -> createTextNode($prelevement->getQuantite_prelevee()));
					
					/* Création d'un noeud */
					$prixUnitaireTTCPrepare = $save->createElement('prixUnitaireTTCPrepare');
					$ligneCommande -> appendChild($prixUnitaireTTCPrepare); 
					$prixUnitaireTTCPrepare -> appendChild($save -> createTextNode($prelevement->getPrix_unitaire_ttc_lu()));
				}
				
				/* Mettre à jour l'état de la commande à terminée */
				$com->setEtatCommande(3);
				
				/* Sauvegarde dans une fichier .xml dans /flux/out/commandes/ */
				$fileName = $EXPORT_DIR . "commande_" . $com->getCodeCommande() . "_" . time() .".out.xml";
				$save -> save($fileName);
			}
		}
	}
	
	/**
	 * 
	 * Impression des étiquettes
	 * @param $commandes -> chaque élèment est sous format array(Object(Commande), Nombre d'étiquette pour la commande)
	 * @param $nomFichier le nom du fichier PDF en sortie
	 *
	 */
	function etiquettePDFA4($commandes, $nomFichier){
		
		$marge_exterieur 	= 8;
		$marge_interieur 	= 5;
		$hauteur_case 		= 44;
		$marginTop 			= 21;
		$marginLeft 		= 8;
		$marginRight 		= 8;
		$nbEtiquette 		= 12;
		$interLigne 		= 0;
		
		/* Dimensions page */
		$wFeuille = 210;
		$hFeuille = 297;
		$largeur = $wFeuille - ($marge_exterieur + $marge_interieur);
		$centre = $largeur / 2 + ($marge_exterieur + $marge_interieur)/2;
		$colonne = $largeur / 2;

		$etiquette_tous = array();
		for ($i=1; $i<=$nbEtiquette; $i++) {
			$etiquette_tous[$i] = 1;
		}
		
		//les étiquettes vide dans la première page
		$etiquette_exclu 	= array();
		$etiquette 			= array();
		
		$index = 1;
		foreach ($commandes as $item) {
			for($i=0; $i<$item[1];$i++){
				$etiquette[$index] = 1;
				$index++;
			}
		}
		$etiquette_exclu = array_diff_key($etiquette_tous, $etiquette);

		$pdf = new FPDF('P', 'mm', 'A4');
		$pdf->SetMargins($marginLeft, $marginTop, $marginRight);
		$pdf->SetAutoPageBreak(false, 0);
		
		$indexEtiquette = 1;
		
		foreach($commandes as $item){
			$commande = $item[0];
			for($i=1; $i<=$item[1]; $i++){
				if ($indexEtiquette == 1) {
					$pdf->addPage('','');
				}
				
				//debut de controle de case vide sur la premier page
				//Si l'étiquette sont exclu dans la zone numéro $indexEtiquette
				while (array_key_exists($indexEtiquette,$etiquette_exclu)) {
					$yHaut = $hauteur_case * (floor((($indexEtiquette-1)%$nbEtiquette)/2)) + $marginTop;
					if ($indexEtiquette%$nbEtiquette == 1 && $indexEtiquette%$nbEtiquette != 1) break;
					
					$pdf->setY($yHaut + 3);
					if ($indexEtiquette%2 == 1) {
						$pdf->Line($centre, $yHaut + 2, $centre, $yHaut + $hauteur_case - 2);
						if ($indexEtiquette%$nbEtiquette != ($nbEtiquette-1))
							$pdf->Line($marge_exterieur, $yHaut + $hauteur_case, $colonne, $yHaut + $hauteur_case);
					}
					elseif($indexEtiquette%2 == 0 && $indexEtiquette%$nbEtiquette != 0) {
						$pdf->Line($marge_exterieur + $colonne + $marge_interieur, $yHaut + $hauteur_case, $colonne + $colonne + $marge_interieur, $yHaut + $hauteur_case);
					}
					$indexEtiquette++;
				}
				
				$yHaut = $hauteur_case * (floor((($indexEtiquette-1)%$nbEtiquette)/2)) + $marginTop;
				
				if ($indexEtiquette%$nbEtiquette == 1 && $indexEtiquette != 1) {
					$pdf->addPage('','');
				}
				
				if ($indexEtiquette%2 == 0) {
					$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yHaut + 3);
				}
				else {
					$pdf->setY($yHaut + 3);
				}
				//fin de controle de case vide sur la premier page
				
				$pdf->SetFont('Courier', 'B', 16);
				$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 5 + $interLigne, $commande->getCodeCommande(), 0, 'L');
				if ($indexEtiquette%2 == 0) {
					$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yHaut + 3);
				}
				else {
					$pdf->setY($yHaut + 3);
				}
				$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 5 + $interLigne, $i . '/' . $item[1], 0, 'R');
				$yBas = $pdf->getY();
				if ($indexEtiquette%2 == 0) {
					$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
				}
				
				$client = $commande->getClient();
				
				if (unhtmlentities($commande->getModeLivraison()) == 'Livraison à domicile') {
					$pdf->SetFont('Arial', 'B', 18);
					$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 6 + $interLigne, strtoupper(unhtmlentities($client->getNom())) . " " . unhtmlentities($client->getPrenom()), 0, 'L');
					$yBas = $pdf->getY();
					if ($indexEtiquette%2 == 0) {
						$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					}
					
					$pdf->SetFont('Courier', 'B', 12);
					$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, $client->getTelephone(), 0, 'L');
					$yBas = $pdf->getY();
					if ($indexEtiquette%2 == 0) {
						$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					}
					$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, substr(trim(unhtmlentities($commande->getLigneAdresseLivraison())),0,110), 0, 'L');
					$yBas = $pdf->getY();
					if ($indexEtiquette%2 == 0) {
						$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					}
					
					// $pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, substr(trim($commande['CLI_LIVADRESSE2']),0,110), 0, 'L');
					// $yBas = $pdf->getY();
					// if ($indexEtiquette%2 == 0) {
						// $pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					// }
					
					// $pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, substr(trim($commande['CLI_LIVADRESSE3']),0,110), 0, 'L');
					// $yBas = $pdf->getY();
					// if ($indexEtiquette%2 == 0) {
						// $pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					// }
					
					// $pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, $commande['CLI_LIVETAGE'] . " - " . $commande['CLI_LIVCODE'], 0, 'L');
					// $yBas = $pdf->getY();
					// if ($indexEtiquette%2 == 0) {
						// $pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					// }
					$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, $commande->getCodePostalLivraison() . " " . strtoupper(unhtmlentities($commande->getMunicipaliteLivraison())), 0, 'L');
				}
				else {
					$pdf->SetFont('Arial', 'B', 16);
					$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 6 + $interLigne, strtoupper(unhtmlentities($client->getNom())) . " " . unhtmlentities($client->getPrenom()), 0, 'L');
					$yBas = $pdf->getY();
					if ($indexEtiquette%2 == 0) {
						$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					}
					$pdf->SetFont('Courier', 'B', 12);
					$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, $client->getTelephone(), 0, 'L');
					$yBas = $pdf->getY();
					if ($indexEtiquette%2 == 0) {
						$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					}
					$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, substr(trim(unhtmlentities($client->getLigneAdresseFacturation())),0,110), 0, 'L');
					$yBas = $pdf->getY();
					if ($indexEtiquette%2 == 0) {
						$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					}
					// $pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, substr(trim($commande['CLI_FACADRESSE2']),0,110), 0, 'L');
					// $yBas = $pdf->getY();
					// if ($indexEtiquette%2 == 0) {
						// $pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					// }
					// $pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, substr(trim($commande['CLI_FACADRESSE3']),0,110), 0, 'L');
					// $yBas = $pdf->getY();
					// if ($indexEtiquette%2 == 0) {
						// $pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
					// }
					$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, $client->getCodeInseeFacturation() . " " . strtoupper(unhtmlentities($client->getMunicipaliteFacturation())), 0, 'L');
				}
				
				$yBas = $pdf->getY();
				if ($indexEtiquette%2 == 0) {
					$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
				}
				$pdf->SetFont('Courier', 'B', 16);
				$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 6 + $interLigne, unhtmlentities($commande->getModeLivraison()), 0, 'L');
				$yBas = $yHautBis = $pdf->getY();
				if ($indexEtiquette%2 == 0) {
					$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
				}
				$pdf->SetFont('Courier', 'B', 12);
				$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, date('d/m/Y H:i', $commande->getDateLivraison()), 0, 'L');
				if ($indexEtiquette%2 == 0) {
					$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yHautBis);
				}
				else {
					$pdf->setY($yHautBis);
				}
				$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 4 + $interLigne, "CAISSE " . $i . '/' . $item[1], 0, 'R');
				$yBas = $pdf->getY();
				if ($indexEtiquette%2 == 0) {
					$pdf->setXY($colonne + ($marge_exterieur + $marge_interieur), $yBas);
				}
				$pdf->MultiCell($colonne - ($marge_exterieur + $marge_interieur)/2, 7 + $interLigne, "", 0, 'L');
				
				//Ligne
				if ($indexEtiquette%2 == 1) {
					$pdf->Line($centre, $yHaut + 2, $centre, $yHaut + $hauteur_case - 2);
					if ($indexEtiquette%$nbEtiquette != ($nbEtiquette-1))
						$pdf->Line($marge_exterieur, $yHaut + $hauteur_case, $colonne, $yHaut + $hauteur_case);
				}
				elseif($indexEtiquette%2 == 0 && $indexEtiquette%$nbEtiquette != 0) {
					$pdf->Line($marge_exterieur + $colonne + $marge_interieur, $yHaut + $hauteur_case, $colonne + $colonne + $marge_interieur, $yHaut + $hauteur_case);
				}
				$indexEtiquette++;
			}
		}
		$pdf->Output($nomFichier, 'F', "");
		
	}
	
?>
