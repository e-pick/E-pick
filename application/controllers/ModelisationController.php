<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * ModelisationController.php
 *
 * Cette classe permet de gérer la modélisation du magasin
 *
 */

class ModelisationController extends BaseController {

	
	public static function index(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){
		
		}
	}	
	
	/* Action Consulter
	 * Profil requis : >= Profil superviseur
	 * 
	 * Action permettant de visualiser/modéliser un étage du magasin
	 * Lors de la sauvegarde :
	 * 		- backup de la base de données
	 * 		- les positions des blocs sont enregistrées
	 * 		- le cacul des chemins dans le magasin pour accéder est effectué
	 * 		- sauvegarde des routes et des distances dans un fichier
	 */	
 
	public static function consulter(){
	// DEFINITION DES VARIABLES
	
		
		//définition de la taille d'un checkpoint
		$largeurpoint 		= 6;
		$hauteurpoint 		= 6;				
		//ecart des points par rapport au rayon 
		$ecartrayon 		= 12; //12
		$ecartrayonvirage 	= 20; //20				
		//ecart pour la fusion, jusqu'à combien on tolère la fusion entre deux points
		$ecarttop 			= 35; //35
		$ecartleft 			= 35; //35
		$ecarttopvirage 	= 30; //30
		$ecartleftvirage 	= 30; //30
		
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	// Profil superviseur au minimum
			
			//premièrement on charge tous les étages, puis on vérifie si un étage est passé en param pour l'afficher
			//sinon on prend le premier étage trouvé dans la DB et on l'affiche
			$arrayEtages 	= Etage::loadAll(parent::$_pdo,true);	
			$etage 			= parent::$_request->getVar('id');
			if(isset($etage))
				$etageSelectionne = $etage;
			else
				$etageSelectionne = Etage::getFirstId(parent::$_pdo);			
			$currentEtage = Etage::load(parent::$_pdo,$etageSelectionne,true);
			if($currentEtage == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
			//on passe à smarty la liste des étages et l'étage sélectionné.
			parent::$_response->addVar('arrayEtages'		, $arrayEtages);
			parent::$_response->addVar('etageSelectionne'	, $etageSelectionne);
			
			//lors de la sauvegarde, la première action est le backup de la DB. Si cette action est demandé
			//on appelle la méthode dumpDB se trouvant dans kernel/common.php
			if(parent::$_request->getVar('backuper_db')){ 
		
				dumpDB(null, '../application/backups', 'backup-'.time().'.sql', true);
				return true;
			}		
			
			if(parent::$_request->getVar('save_position')){ 
				//on récupère la liste des objets à sauvegarder (rayons, obstacles, caisse, étage) ainsi que la liste des obstacles à supprimer
				$arrayObjects 			= explode(';',parent::$_request->getVar('block_position'));  
				$arrayObstaclesToDelete = array_unique(explode(';',parent::$_request->getVar('obstacle_suppr'))); 
				$arrays 				= self::save_positions($arrayObjects,$arrayObstaclesToDelete,$largeurpoint,$hauteurpoint,$ecartrayon,$ecartrayonvirage,$currentEtage);
				$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Enregistrement automatique des positions - ') . $_SESSION['user_login']."\r\n";
				$logFile = '../application/logs/'.date('m-Y').'-modelisation.log';
				writeLog($log, $logFile);
				return true;
			}	
			
			
			
			
			
			
			//deuxieme étape de la sauvegarde  	
			//		- les positions des blocs sont enregistrées
			//		- le cacul des chemins dans le magasin pour accéder est effectué
			//		- application de dijkstra pour trouver les meilleures routes
			// 		- sauvegarde des routes et des distances dans un fichier
			if(parent::$_request->getVar('valider')){  	 
			
				 							
				$temp 			= time();
				$tempInit 		= $temp;
				$txt_temp_exec 	= "Les temps d'exécution des différentes étapes lors de la sauvegarde de l'étage " . $etageSelectionne . "\n\n\n";
								
			 
				//on récupère la liste des objets à sauvegarder (rayons, obstacles, caisse, étage) ainsi que la liste des obstacles à supprimer
			
				//echo '<br>debut placement des points : '. memory_get_usage() ;
				$arrayObjects			= explode(';',parent::$_request->getVar('block_position'));  
				$arrayObstaclesToDelete = array_unique(explode(';',parent::$_request->getVar('obstacle_suppr'))); 
			 
				//différentes matrices pour sauvegarder les points et les objets
				$arrayCheckpointsATester 	= array();
				$arrays 					= self::save_positions($arrayObjects,$arrayObstaclesToDelete,$largeurpoint,$hauteurpoint,$ecartrayon,$ecartrayonvirage,$currentEtage);
				unset($arrayObjects);
				unset($arrayObstaclesToDelete);
				$arrayCheckpoint			= array();
				// contient les informations pour chaque objet parser de $arrayObjects
				$arrayObjectsATester 		= $arrays[0]; 				
				$quadrillagePoint			= $arrays[1];
				$quadrillageObject			= $arrays[2];
				unset($arrays); 
				
				
				$txt_temp_exec .= "Placement des checkpoints : " . (time() - $temp) . " secondes \n";
				$temp = time();
				//echo '<br>debut fusion des points : '. memory_get_usage() ;
				
				//on passe à la fusion des points de passage, en effet pour diminuer leur nombre, on va fusionner
				//les points de passage qui sont proches. Il est possible de fusionner seulement les points de passage du
				//même type.
				
				$arrayCheckpoint = Matrice::merge($quadrillagePoint,$quadrillageObject,$ecarttop,$ecartleft,$ecarttopvirage,$ecartleftvirage);
				$quadrillagePoint = array();	
				$quadrillageObjectTmp = $quadrillageObject;	
				$quadrillageObject = array();	
				$tailleQuadrillage = 300;
				
				
				foreach($arrayCheckpoint as $point){
					$data =	 explode(':',$point);
					$quadrillagePoint[''.(int) (intval($data[1])/$tailleQuadrillage).','.(int) (intval($data[2])/$tailleQuadrillage)][]	= $point;
				}
				foreach($quadrillageObjectTmp as $zone){
					foreach($zone as $object){
						if($object[6] == 'obstacle'){
							$carres = getCarres(Obstacle::load(parent::$_pdo,$object[5]), 'obstacle', $tailleQuadrillage);
							foreach($carres as $carre){						
								$quadrillageObject[$carre][] = array($object[0], $object[1], $object[2], $object[3], $object[4],$object[5],$object[6]);
							}						
						}
						else{	
							$carres = getCarres(Rayon::load(parent::$_pdo, $object[5]), 'rayon', $tailleQuadrillage); 
							foreach($carres as $carre){						
								$quadrillageObject[$carre][] = array($object[0], $object[1], $object[2], $object[3], $object[4],$object[5],$object[6]);
							}							
						}		
					
					}
				} 
				$arrayCheckpoint = Matrice::merge($quadrillagePoint,$quadrillageObject,$ecarttop,$ecartleft,$ecarttopvirage,$ecartleftvirage);

				unset($quadrillagePoint);
				unset($quadrillageObject);
				unset($quadrillageObjectTmp);
				$matrice 			= array(); 
				$position_points 	= array();
					 
				// I is the infinite distance.
				define('I',1000000);
//				define('I',200000000000);


				
				
				$txt_temp_exec .= "Fusion des points : " . (time() - $temp) . " secondes \n";

				$txt_temp_exec .= "valeur de memory_limit = ".@get_cfg_var('memory_limit')."\n";
				$txt_temp_exec .= "mem_get_usage avant la boucle:".memory_get_usage()."\n";
				ini_set("memory_limit","4G");
				$txt_temp_exec .= "valeur de memory_limit = ".@get_cfg_var('memory_limit')."\n";
				$txt_temp_exec .= "mem_get_usage avant la boucle affecte :".memory_get_usage()."\n";
				$txt_temp_exec .= "valeur de memory_limit = ".@get_cfg_var('memory_limit')."\n";
				
				
				$temp 			= time();
				
				/* CALCUL DE LA MATRICE D'ADJACENCE */
				/* pour chaque point, tester la ligne avec tous les autres points sans couper un rayon , un obstacle ou une caisse */				
				//parcourir tous les éléments et retirer le point testé de la matrice
				while(($elem = array_pop($arrayCheckpoint))!= null){
					$checkpoint = explode(':',$elem);	 
					//on récupère les informations du checkpoint traité 
					if($checkpoint[0] == 'zone_depart'){  
						$idpoint1 		= 'ptd_' . $etageSelectionne;  // id du rayon particluer
						$position_top1 	= $checkpoint[1];
						$position_left1 = $checkpoint[2];
						$x1 			= $position_left1 + ($largeurpoint/2);
						$y1 			= $position_top1 + ($hauteurpoint/2);
					 
					}
					else if($checkpoint[0] == 'zone_arrive'){  
						$idpoint1 		= 'pta_' . $etageSelectionne;  // id du rayon particluer
						$position_top1 	= $checkpoint[1];
						$position_left1 = $checkpoint[2];
						$x1 			= $position_left1 + ($largeurpoint/2);
						$y1 			= $position_top1 + ($hauteurpoint/2);
					 
					}
					else if(strpos($checkpoint[0],'bout_rayon_') === false){ //segment  
						$idpoint1 		= $checkpoint[0];
						$position_top1 	= $checkpoint[1];
						$position_left1 = $checkpoint[2];
						$x1 			= $position_left1 + ($largeurpoint/2);
						$y1 			= $position_top1 + ($hauteurpoint/2);
					}
					else{   //bout_rayon 
						
						$idpoint1 		= substr($checkpoint[0],strlen('bout_rayon_')); 
						$position_top1 	= $checkpoint[1];
						$position_left1 = $checkpoint[2];
						$x1 			= $position_left1 + ($largeurpoint/2);
						$y1 			= $position_top1 + ($hauteurpoint/2); 						
					}	
				
					$key1 					= $idpoint1; //identifiant du checkpoint fusionné
					$matrice[$key1][$key1] 	= '0';					 
					$position_points[$key1] = $y1.','.$x1;
				
					foreach($arrayCheckpoint as $currentElem){
						$checkpoint2 = explode(':',$currentElem);	 
						//on récupère les informations du checkpoint traité 
						if($checkpoint2[0] == 'zone_depart'){  
							$idpoint2 		= 'ptd_' . $etageSelectionne;  // id du rayon particluer
							$position_top2 	= $checkpoint2[1];
							$position_left2 = $checkpoint2[2];
							$x2 			= $position_left2 + ($largeurpoint/2);
							$y2 			= $position_top2 + ($hauteurpoint/2);					 
						}
						else if($checkpoint2[0] == 'zone_arrive'){  
							$idpoint2 		= 'pta_' . $etageSelectionne;  // id du rayon particluer
							$position_top2 	= $checkpoint2[1];
							$position_left2 = $checkpoint2[2];
							$x2 			= $position_left2 + ($largeurpoint/2);
							$y2 			= $position_top2 + ($hauteurpoint/2);					 
						}
						else if(strpos($checkpoint2[0],'bout_rayon_') === false){ //segment  

							$idpoint2 		= $checkpoint2[0];
							$position_top2 	= $checkpoint2[1];
							$position_left2 = $checkpoint2[2];
							$x2 			= $position_left2 + ($largeurpoint/2);
							$y2 			= $position_top2 + ($hauteurpoint/2);
						}
						else{   //bout_rayon 			 
									
							$idpoint2 		= substr($checkpoint2[0],strlen('bout_rayon_')); 
							$position_top2 	= $checkpoint2[1];
							$position_left2 = $checkpoint2[2];
							$x2 			= $position_left2 + ($largeurpoint/2);
							$y2 			= $position_top2 + ($hauteurpoint/2); 						
						}	
					
						$key2 			= $idpoint2; //identifiant du checkpoint fusionné
						$intersection	 = false; 
						
						$d = round(sqrt(pow(($y2-$y1), 2)+pow(($x2-$x1), 2)));
						if($d <= 200){

							foreach($arrayObjectsATester as $object){ 
								$x1objet = $object[2];
								$y1objet = $object[3];
								
								
								$dObj = round(sqrt(pow(($y1objet-$y1), 2)+pow(($x1objet-$x1), 2)));
								if($dObj <= 500){
									$hauteur_objet = $object[0];
									$largeur_objet = $object[1];
									
									$sens = $object[5];
									
									$x1objetRot = 0;
									$y1objetRot = 0;
									$x2objetRot = $largeur_objet;
									$y2objetRot = $hauteur_objet;
									
									
									$dx1 = ($x1 - $x1objet);
									$dy1 = ($y1 - $y1objet);
									$dx2 = ($x2 - $x1objet);
									$dy2 = ($y2 - $y1objet);

									$x1Rot = ($dx1*cos(-deg2rad($sens)) - $dy1*sin(-deg2rad($sens)));
									$y1Rot = ($dx1*sin(-deg2rad($sens)) + $dy1*cos(-deg2rad($sens)));
									$x2Rot = ($dx2*cos(-deg2rad($sens)) - $dy2*sin(-deg2rad($sens)));
									$y2Rot = ($dx2*sin(-deg2rad($sens)) + $dy2*cos(-deg2rad($sens)));
														
									
									if($x1Rot <= $x2Rot){
										$x_inf = $x1Rot;
										$x_sup = $x2Rot;
									}									
									else if($x1Rot > $x2Rot){
										$x_inf = $x2Rot;
										$x_sup = $x1Rot;
									}
									if($y1Rot <= $y2Rot){
										$y_inf = $y1Rot;
										$y_sup = $y2Rot;
									}									
									else if($y1Rot > $y2Rot){
										$y_inf = $y2Rot;
										$y_sup = $y1Rot;
									}
									
									if ($x1Rot == $x2Rot) {
											
										$y_intery1 = $y1objetRot;
										$x_intery1 = $x1Rot;
										
										$y_intery2 = $y2objetRot;
										$x_intery2 = $x2Rot;
										
										if (($x_intery1 >= $x1objetRot) && ($x_intery1 <= $x2objetRot)) {
											if(($y_intery1 <= $y_sup) && ($y_intery1 >= $y_inf)) {
												$intersection = true;
											}										
										
											if (($y_intery2 <= $y_sup) && ($y_intery2 >= $y_inf)) {
													$intersection = true;
											}
										
											if (($y_intery1 <= $y_inf) && ($y_intery2 >= $y_sup)) {
													$intersection = true;
											}
										}
										else {
											//pas dintersection
										}
										
									}
									else if ($y1Rot == $y2Rot) {
									
										$x_interx1 = $x1objetRot; 
										$y_interx1 = $y1Rot;
										
										$x_interx2 = $x2objetRot;
										$y_interx2 = $y2Rot;
										
										if (($y_interx1 >= $y1objetRot) && ($y_interx1 <= $y2objetRot)) {
											if(($x_interx1 <= $x_sup) && ($x_interx1 >= $x_inf)) {
												$intersection = true;
											}
										
											if(($x_interx2 <= $x_sup) && ($x_interx2 >= $x_inf)) {
												$intersection = true;
											}
											
											if(($x_interx1 <= $x_inf) && ($x_interx2 >= $x_sup)) {
												$intersection = true;
											}
										}
										else {
											//pas dintersection
										}
										
									}
									else {
										
										$a = ($y2Rot - $y1Rot) / ($x2Rot - $x1Rot);
										$b = $y1Rot - ($a * $x1Rot);
									
										$x_interx1 = $x1objetRot; 
										$y_interx1 = ($a * $x_interx1) + $b;
										
										$y_intery1 = $y1objetRot;
										$x_intery1 = ($y_intery1 - $b) / $a;
										
										$x_interx2 = $x2objetRot;
										$y_interx2 = ($a * $x_interx2) + $b;
										
										$y_intery2 = $y2objetRot;
										$x_intery2 = ($y_intery2 - $b) / $a;
										
									 
										if (($y_interx1 >= $y1objetRot) && ($y_interx1 <= $y2objetRot)) {
											if(($x_interx1 <= $x_sup) && ($x_interx1 >= $x_inf) && ($y_interx1 <= $y_sup) && ($y_interx1 >= $y_inf)) {
												$intersection = true;
											}
										}
										if (($x_intery1 >= $x1objetRot) && ($x_intery1 <= $x2objetRot)) {
											if(($x_intery1 <= $x_sup) && ($x_intery1 >= $x_inf) && ($y_intery1 <= $y_sup) && ($y_intery1 >= $y_inf)) {
												$intersection = true;
											}										
										}
										if (($y_interx2 >= $y1objetRot) && ($y_interx2 <= $y2objetRot)) {
											if(($x_interx2 <= $x_sup) && ($x_interx2 >= $x_inf) && ($y_interx2 <= $y_sup) && ($y_interx2 >= $y_inf)) {
												$intersection = true;
											}
										}
										if (($x_intery2 >= $x1objetRot) && ($x_intery2 <= $x2objetRot)) {
											if(($x_intery2 <= $x_sup) && ($x_intery2 >= $x_inf) && ($y_intery2 <= $y_sup) && ($y_intery2 >= $y_inf)) {
												$intersection = true;
											}
										}
										if (($x_inf >= $x1objetRot) && ($x_inf <= $x2objetRot) && ($x_sup <= $x2objetRot) && ($x_sup >= $x1objetRot) && ($y_inf >= $y1objetRot) && ($y_inf <= $y2objetRot) && ($y_sup >= $y1objetRot) && ($y_sup <= $y2objetRot)) {
											$intersection = true;
										}

									}
									if($intersection)
										break;
								}
							}					  	
							
						}
						else
							$intersection = true;
						//sauvegarde de la matrice d'adjacence					
						//si intersection alors on met l'infini en distance 
						
						if($intersection){
							$matrice[$key1][$key2] = I;
							$matrice[$key2][$key1] = I;		
						}
						else{ //sinon on calcul de la distance entre x1 y1 et x2 y2
							if($d > I)
								$d = I;
							$matrice[$key1][$key2] = $d;
							$matrice[$key2][$key1] = $d;	
						} 						
					} 
				
				
				} 
				$txt_temp_exec .= "mem_get_usage après la boucle : ".memory_get_usage()."\n";
				ini_set("memory_limit","4G");
				//on sauvegarde dans un tableaux les identifiants des checkpoints
				unset($arrayCheckpoint);
				$keys = array();
				$keys = array_keys($matrice);
				$nbkeys = count($keys);
				$matrice_f = array();
				$matrice_path = array();
				$matrice_floyd = $matrice; //on duplique notre matrice pour floyd
				$matrice_f_floyd = array(); 
				$matrice_path_floyd = array();
				
				$txt_temp_exec .= "Calcul de la matrice d'adjacence : " . (time()-$temp) . " secondes \n";
				$temp = time();
				
				$dijkstra = new Dijkstra($matrice, I);// initialise l'algo				
				//on applique dijkstra sur tous nos points
				for($i = 0; $i < $nbkeys; $i++){ 
						$matrice_f[$keys[$i]] = $dijkstra->findShortestPath($keys[$i]);
						$matrice_path[$keys[$i]] = $dijkstra -> getResults();
				}
				unset($matrice);
				unset($dijkstra); 
				$txt_temp_exec .= "mem_get_usage après la boucle Djikstra : ".memory_get_usage()."\n";
				$txt_temp_exec .= "Dijkstra : " . (time()-$temp) . " secondes \n";
				$temp = time();
				
				/* $floyd = new Floyd($matrice_floyd, I);
				$matrice_f_floyd = $floyd -> calcul();
				$matrice_path_floyd = $floyd -> getResults();
				
				unset($matrice_floyd);
				unset($floyd);
				
				$txt_temp_exec .= "Floyd : " . (time()-$temp) . " secondes \n";
				$temp = time(); */
				
				//on obtient deux matrices, une avec la route (la liste des checkpoints par lesquels on doit passer pour rejoindre les deux points
				//et la matrice contenant la distance totale en suivant cette route
		   
				$matrice_segment 	= array();
				$liste_rayons 		= Rayon::loadAll(parent::$_pdo,null,null,null,null,true);
		   		foreach($liste_rayons as $rayon){						
					$matrice_segment[$rayon->getIdrayon()] =  Segment::selectByRayon(parent::$_pdo,$rayon);	
				}
				unset($liste_rayons);
				
				
				/* Construction des matrices à sauvegarder */
				
				$matrice_distance = array();
				$matrice_route = array();
				// $matrice_distance_floyd = array();
				// $matrice_route_floyd = array();
				
				for($i = 0; $i < $nbkeys; $i++){ 			
					for($j = $i; $j < $nbkeys; $j++){
						$arrayI = explode('-',$keys[$i]);
						$arrayJ = explode('-',$keys[$j]);
						
						foreach($arrayI as $pointI){
							$data = explode('_',$pointI);
									
							if($data[0] == 'ptd' || $data[0] == 'pta')
								$idseg = $etageSelectionne;
							else if($data[1] ==  'a' || $data[1] == 'b' || $data[1] ==  'c' || $data[1] == 'd'){
								break;
							}
							else{
								$idseg = $matrice_segment[$data[0]][($data[1]-1)]->getIdsegment();
							}
							$idray = $data[0];											
							$idI = $idray.'_'.$idseg;							
						
							foreach($arrayJ as $pointJ){
								
								
								$data = explode('_',$pointJ);
									
								if($data[0] == 'ptd' || $data[0] == 'pta')
									$idseg = $etageSelectionne;
								else if($data[1] ==  'a' || $data[1] == 'b' || $data[1] ==  'c' || $data[1] == 'd'){
									break;
								}
								else{
									$idseg = $matrice_segment[$data[0]][($data[1]-1)]->getIdsegment();
								}
								$idray = $data[0];											
								$idJ = $idray.'_'.$idseg;
								
								$matrice_distance[$idI][$idJ] = $matrice_f[$keys[$i]][$keys[$j]];
								$matrice_distance[$idJ][$idI] = $matrice_distance[$idI][$idJ];
								// $matrice_distance_floyd[$idI][$idJ] = $matrice_f_floyd[$keys[$i]][$keys[$j]];
								// $matrice_distance_floyd[$idJ][$idI] = $matrice_distance_floyd[$idI][$idJ];
								$matrice_route[$idI][$idJ] = '';
								$matrice_route[$idJ][$idI] = '';
								// $matrice_route_floyd[$idI][$idJ] = '';
								// $matrice_route_floyd[$idJ][$idI] = '';
								foreach($matrice_path[$keys[$i]][$keys[$j]] as $point){

									if(substr_count($point,"-") != 0){
										$array = explode('-',$point);
										$point2 = $array[0];
									}
									else{
										$point2 = $point;												
									}
									 
									$data = explode('_',$point2);
									
									if($data[0] == 'no route')
										$matrice_route[$idI][$idJ] = $data[0] . ';';
									else{
				
										$matrice_route[$idI][$idJ] .= $position_points[$point] . ';';
									}
								}
								
								/* foreach($matrice_path_floyd[$keys[$i]][$keys[$j]] as $point){

									if(substr_count($point,"-") != 0){
										$array = explode('-',$point);
										$point2 = $array[0];
									}
									else{
										$point2 = $point;												
									}
									 
									$data = explode('_',$point2);
									
									if($data[0] == 'no route')
										$matrice_route_floyd[$idI][$idJ] = $data[0] . ';';
									else{
										$matrice_route_floyd[$idI][$idJ] .= $position_points[$point] . ';';
									}
								} */
								
								
								$matrice_route[$idI][$idJ] = substr($matrice_route[$idI][$idJ],0,-1);
								$matrice_route[$idJ][$idI] = implode(';',array_reverse(explode(';',$matrice_route[$idI][$idJ])));
								// $matrice_route_floyd[$idI][$idJ] = substr($matrice_route_floyd[$idI][$idJ],0,-1);
								// $matrice_route_floyd[$idJ][$idI] = implode(';',array_reverse(explode(';',$matrice_route_floyd[$idI][$idJ])));	
							}
						}
					}	

				}					
				
				/* on libère la mémoire */
				unset($matrice_f);
				// unset($matrice_f_floyd);
				unset($matrice_path);
				// unset($matrice_path_floyd);
				
				
				// Ecriture des matrices dans le fichier php

				$name_file = '../application/tmp/matrice_'.$etageSelectionne.'.php';
				ob_start(); 
				
				echo '<?php ';
				echo '$matrice_distance_'. $etageSelectionne .' = ';
				var_export($matrice_distance);
				echo '; ';
				echo '$matrice_route_'. $etageSelectionne .' = ';
				var_export($matrice_route);
				echo '; ';
				echo '?>';
				
				$texte = ob_get_contents(); 
				ob_end_clean();
				
				$file = fopen($name_file, "w+");
				fputs($file, $texte); //ecriture
				unset($texte);
				fclose($file);
				
				/* $name_file = '../application/tmp/matrice_'.$etageSelectionne.'_floyd.php';
				ob_start(); 
				
				echo '<?php ';
				echo '$matrice_distance_'. $etageSelectionne .' = ';
				var_export($matrice_distance_floyd);
				echo '; ';
				echo '$matrice_route_'. $etageSelectionne .' = ';
				var_export($matrice_route_floyd);
				echo '; ';
				echo '?>';
				
				$texte = ob_get_contents(); 
				ob_end_clean();
				
				$file = fopen($name_file, "w+");
				fputs($file, $texte); //ecriture
				unset($texte);
				fclose($file); */
					

				/* on libère la mémoire */
				unset($matrice_distance);
				// unset($matrice_distance_floyd);
				unset($matrice_route);
				// unset($matrice_route_floyd);
				
				
				$txt_temp_exec .= "Ecriture dans le fichier : " . (time() - $temp) . " secondes \n\n";	
				$txt_temp_exec .= "Temps total : " . (time() - $tempInit) . " secondes \n";	
				
				$name_file2 = '../application/tmp/timeExec_'. $etageSelectionne . '.pp'; 
				$f2 = fopen($name_file2, "w+");
				fputs($f2, $txt_temp_exec );//ecriture
				unset($txt_temp_exec);
				fclose($f2);	
				
				$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('Sauvegarde et calcul des matrices - ') . $_SESSION['user_login']."\r\n";
				$logFile = '../application/logs/'.date('m-Y').'-modelisation.log';
				writeLog($log, $logFile);			
			
			}
			// on arrive enfin à la fin de la sauvegarde des positions et des checkpoints ainsi que le calcul des chemins
			
			//pour l'affichage de la modélisation on charge le point de départ
			parent::$_response->addVar('pt_depart_top'	, $currentEtage->getPtDepartTop());
			parent::$_response->addVar('pt_depart_left'	, $currentEtage->getPtDepartLeft());
			parent::$_response->addVar('pt_arrive_top'	, $currentEtage->getPtArriveTop());
			parent::$_response->addVar('pt_arrive_left'	, $currentEtage->getPtArriveLeft());
			$arrayRayons = array();
			$arrayObstacles = array();
			//Chargement des rayons et des zones POUR affichage
			$arrayZones = Zone::loadAllEtage(parent::$_pdo, $etageSelectionne,true);
			parent::$_response->addVar('arrayZones', $arrayZones);
			
			// pour chaque zone de l'étage on récupère les rayons
			foreach($arrayZones as $zone){ 
				foreach(Rayon::loadAllZone(parent::$_pdo,$zone->getIdzone(),true) as $rayon){
					$count = Segment::count(parent::$_pdo, $rayon->getIdrayon());
					array_push($arrayRayons, array($rayon,$count,$zone->getCouleur())); 
				}
			}			 
			parent::$_response->addVar('arrayRayons', $arrayRayons);			
			//chargement des obstacles
			$arrayObstacles = Obstacle::loadAllEtage(parent::$_pdo, $etageSelectionne,true);
			parent::$_response->addVar('arrayObstacles', $arrayObstacles);
			
			parent::$_response->addVar('txt_etape'		,gettext('Etape'));
			parent::$_response->addVar('txt_magasin'	,gettext('Magasin'));
			parent::$_response->addVar('txt_etape1'		,gettext('Synchronisation des produits'));
			parent::$_response->addVar('txt_etape2'		,gettext('Commencer la g&eacute;olocalisation'));
			parent::$_response->addVar('txt_etape3'		,gettext('Terminer la g&eacute;olocalisation'));
			parent::$_response->addVar('txt_etape4'		,gettext('Mod&eacute;lisation du magasin'));
			parent::$_response->addVar('txt_legende'	,gettext('L&eacute;gende'));
			parent::$_response->addVar('txt_obstacle'	,gettext('Obstacle'));
			parent::$_response->addVar('txt_caisse'		,gettext('Caisse'));
			parent::$_response->addVar('txt_rayon'		,gettext('Rayon'));
			parent::$_response->addVar('txt_zone_dep'	,gettext('Zone de d&eacute;part'));
			parent::$_response->addVar('txt_zone_fin'	,gettext('Zone d\'arriv&eacute;e'));
			parent::$_response->addVar('txt_superficie'	,gettext('Superficie'));
			parent::$_response->addVar('txt_1_metre'	,gettext('1 m&egrave;tre'));
			parent::$_response->addVar('txt_action'		,gettext('Actions'));
			parent::$_response->addVar('txt_ajouter_obs',gettext('Ajouter un obstacle'));
			parent::$_response->addVar('txt_sauvegarder',gettext('Sauvegarder'));
			parent::$_response->addVar('txt_demo'		,gettext('Demo'));
			parent::$_response->addVar('txt_conf_reinit',gettext('Etes-vous s&ucirc;r de vouloir r&eacute;initialiser la mod&eacute;lisation de cet &eacute;tage?'));
			parent::$_response->addVar('txt_reinit'		,gettext('R&eacute;initialiser la mod&eacute;lisation'));
			parent::$_response->addVar('txt_popup_sauve',gettext('Sauvegarde en cours'));
			parent::$_response->addVar('txt_popup_time'	,gettext('Cette op&eacute;ration peut prendre plusieurs minutes...'));
			parent::$_response->addVar('txt_popup_op1'	,gettext('g&eacute;n&eacute;ration du backup de la DB'));
			parent::$_response->addVar('txt_popup_op2'	,gettext('sauvegarde des positions et calcul des chemins'));
		}
	}//fin de l'action consulter

	
	public static function demo(){
	
	if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	
		 
			
			//on récupère l'étage à afficher/traiter
			$arrayEtages = Etage::loadAll(parent::$_pdo,true);	
			parent::$_response->addVar('arrayEtages', $arrayEtages);
			
			$etage = parent::$_request->getVar('id');
			if(isset($etage))
				$etageSelectionne = $etage;
			else{
				$etageSelectionne = Etage::getFirstId(parent::$_pdo);
			}
			$currentEtage = Etage::load(parent::$_pdo,$etageSelectionne,true);			
			if($currentEtage == null)
				throw new Exception(gettext('Param&egrave;tre invalide'),5);
			parent::$_response->addVar('etageSelectionne', $etageSelectionne);
			// $retourListeDePoints 			= array(); 		
			if(parent::$_request->getVar('valider')){  
	
				$matrice 			= getMatricePath($etageSelectionne);
				$matrice_distance 	= $matrice[0];
				$matrice_route 		= $matrice[1];
				if ($matrice_distance != null && $matrice_route != null){
					$keys = array();
					//récupération des segments à ordonner
					$arraySegmentAPasser 	= array();
					$listeSegment 			= parent::$_request->getVar('segment_select');
					
					if($listeSegment != ""){
						$arraySegments = explode(';',$listeSegment); 
						$keys[] = 'ptd_' . $etageSelectionne;
						array_push($arraySegmentAPasser, array('ptd',$etageSelectionne));
						foreach($arraySegments as $segment){
							$data 	= explode('_',$segment);
							$idseg 	= Segment::getIdSegmentByOrder(parent::$_pdo,$data[0],$data[1]);
							$idray 	= $data[0];								
							if($matrice_distance[$keys[count($keys)-1]][$idray.'_'.$idseg] < 1000000){
								$keys[] = $idray.'_'.$idseg; 
								$object = array($idray,$idseg);
								array_push($arraySegmentAPasser, $object);
							}
						}
						
						$keys[] = 'pta_' . $etageSelectionne; 
						array_push($arraySegmentAPasser,array('pta',$etageSelectionne));				
						
						
						$retourListeDePoints  	= getListePoints($arraySegmentAPasser,$matrice_distance,$matrice_route,$keys);
						$distancePixel 			= getDistancePx($retourListeDePoints[0]);
						$distanceMetre			= getDistanceM($distancePixel);
						$tempsSeconde 			= getTempsByPx($distancePixel,count($retourListeDePoints[1]));
						$temps 					= (int)($tempsSeconde / 3600) . ':'.(int)(($tempsSeconde % 3600) / 60).':'.(int)((($tempsSeconde % 3600) % 60));
						 
						parent::$_response->addVar('distancePixel', $distancePixel);
						parent::$_response->addVar('distanceMetre', $distanceMetre);
						parent::$_response->addVar('tempsParcours', $temps);
						parent::$_response->addVar('listeDePointsARelier', $retourListeDePoints[0]);
						
					} 
				}
			}
			else{
				parent::$_response->addVar('listeDePointsARelier', array());
			}
			
			parent::$_response->addVar('pt_depart_top'	, $currentEtage->getPtDepartTop());
			parent::$_response->addVar('pt_depart_left'	, $currentEtage->getPtDepartLeft());
			parent::$_response->addVar('pt_arrive_top'	, $currentEtage->getPtArriveTop());
			parent::$_response->addVar('pt_arrive_left'	, $currentEtage->getPtArriveLeft());
			
			parent::$_response->addVar('distancePixel', '');
			parent::$_response->addVar('distanceMetre', '');
			parent::$_response->addVar('tempsParcours', '');					
			// parent::$_response->addVar('listeDePointsARelier', $retourListeDePoints[0]);	
			$arrayRayons 	= array();
			$arrayObstacles = array();
			//Chargement des rayons et des zones POUR affichage
			$arrayZones 	= Zone::loadAllEtage(parent::$_pdo, $etageSelectionne,true);
			parent::$_response->addVar('arrayZones', $arrayZones);
			
			
			// pour chaque zone de l'étage on récupère les rayons
			foreach($arrayZones as $zone){ 
				foreach(Rayon::loadAllZone(parent::$_pdo,$zone->getIdzone(),true) as $rayon){
					$count = Segment::count(parent::$_pdo, $rayon->getIdrayon());
					array_push($arrayRayons, array($rayon,$count,$zone->getCouleur())); 
				}
			}	
			 
			parent::$_response->addVar('arrayRayons', $arrayRayons);
			
			//chargement des obstacles
			$arrayObstacles = Obstacle::loadAllEtage(parent::$_pdo, $etageSelectionne,true);
			parent::$_response->addVar('arrayObstacles', $arrayObstacles);
			
			
			parent::$_response->addVar('txt_legende'	,gettext('L&eacute;gende'));
			parent::$_response->addVar('txt_obstacle'	,gettext('Obstacle'));
			parent::$_response->addVar('txt_caisse'		,gettext('Caisse'));
			parent::$_response->addVar('txt_rayon'		,gettext('Rayon'));
			parent::$_response->addVar('txt_zone_dep'	,gettext('Zone de d&eacute;part'));
			parent::$_response->addVar('txt_zone_fin'	,gettext('Zone d\'arriv&eacute;e'));
			parent::$_response->addVar('txt_superficie'	,gettext('Superficie'));
			parent::$_response->addVar('txt_1_metre'	,gettext('1 m&egrave;tre'));
			parent::$_response->addVar('txt_action'		,gettext('Actions')); 
			parent::$_response->addVar('txt_distance_px',gettext('Distance parcourue')); 
			parent::$_response->addVar('txt_soit'		,gettext('soit')); 
			parent::$_response->addVar('txt_tmps_estime',gettext('Temps estim&eacute;')); 
			parent::$_response->addVar('txt_tracer'		,gettext('Tracer le chemin')); 
		}
	}
	
	public static function supprimer(){
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_ADMINISTRATEUR)){
			parent::$_response->addVar('txt_reinit_modelisation'		,gettext('R&eacute;initialiser la mod&eacute;lisation'));
			parent::$_response->addVar('txt_supprimer_modelisation'		,gettext('Supprimer la modélisation de'));
			$arrayEtages = array();
			$etages 	= Etage::loadAll(parent::$_pdo,true);
			foreach($etages as $etage){ 			
				$arrayEtages[$etage->getIdetage()] = $etage->getLibelle();
			}
			
			parent::$_response->addVar('arrayEtages', $arrayEtages);
		}	
		
	}
		
	public static function supprimeretage(){	
		if(Utilisateur::isAllowed(parent::$_pdo,PROFIL_SUPERVISEUR)){	
			$id = parent::$_request->getVar('id');	// Récupération de l'identifiant de l'étage
			if(isset($id)){
				dumpDB(null, '../application/backups/', 'backup-'.time().'.sql', true);
			 	$etage = Etage::load(parent::$_pdo,$id); 
				foreach($etage->selectZones() as $zone){
					foreach($zone->selectRayons() as $rayon){
						$rayon->setPosition_top(-1,false);
						$rayon->setPosition_left(-1,false);
						$rayon->setSens(0,false);
						$rayon->update();
					}
				}
				
				foreach(Obstacle::loadAllEtage(parent::$_pdo,$id) as $obstacle){
					$obstacle->delete();
				}
							
				$log 	= '[' . date('d/m/Y:H:i:s') . '] -- ' . gettext('R&eacute;initialisation de la mod&eacute;lisation de l\&eacute;tage ') . $id . ' - ' . $etage->getLibelle() . gettext(' par ') .$_SESSION['user_login']."\r\n";
				$logFile = '../application/logs/'.date('m-Y').'.log';
				writeLog($log, $logFile);
				
				header('Location: '. APPLICATION_PATH . 'modelisation/consulter/' . $id) or die('deleting problem');
			}
			else
				throw new Exception(gettext('Un param&egrave;tre est manquant pour effectuer l\'action demand&eacute;e'),3);		
		} 
	}

	/********************************
	 *								*
	 * 	Fonctions supplémentaires 	*
	 *								*
	 ********************************/
	
	private static function save_positions($arrayObjects,$arrayObstaclesToDelete,$largeurpoint,$hauteurpoint,$ecartrayon,$ecartrayonvirage,$currentEtage){
		//on commence par sauvegarder la position de chaque bloc (rayons, obstacles, caisse, étage) 
		// $arrayCheckpoint 		= array();
		$arrayObjectsATester 	= array();
		$quadrillagePoint		= array();
		$tailleQuadrillage		= 200;
		$save = array();
		//on continue par la suppression des obstacles, il contient les ID des obstacles à supprimer
		//si l'id vaut 0, ça implique que c'est un obstacle qui a été créé et supprimé dans la foulée
		foreach($arrayObstaclesToDelete as $idobstacle){
			if($idobstacle != 0 && $idobstacle != ''){
				$obstacle = Obstacle::load(parent::$_pdo,$idobstacle);
				if ($obstacle != null)
					$obstacle->delete();
			}
		}
		
		foreach($arrayObjects as $item){ 
			$arrayItem = explode('-',$item);	 
		
			if($arrayItem[0] == 'rayon'){ //pour chaque rayon on met à jour 
				list($type,$idobject,$position_top,$position_left,$sens,$hauteur,$largeur,$type_ray) = $arrayItem; 
				$rayon = Rayon::load(parent::$_pdo,$idobject);
				$continue = ($position_top == '*' || $position_left == '*') ? false : true; // ça veut dire qu'on supprime le rayon, donc on passe tous les calculs qui suivent
				$position_top = ($position_top == '*') ? -1 : abs($position_top);
				$position_left = ($position_left == '*') ? -1 : abs($position_left); 
				$rayon->setPosition_top($position_top,false);
				$rayon->setPosition_left($position_left,false);
				$rayon->setSens($sens,false);
				$rayon->setHauteur($hauteur,false);
				$rayon->setLargeur($largeur,false);
				$rayon->setType($type_ray,false);
				
				$rayon->update(); //requête SQL pour valider les setters
				
				if($continue){
					if(FINESSE_UTILISEE == FINESSE_RAYON){
						$nbsegment 		= 1;	
						$largeur_ray 	= $largeur; 						
					}
					else{

						$nbsegment 		= count(Segment::selectByRayon(parent::$_pdo,$rayon));	
						$largeur_ray	= (float) ($largeur / $nbsegment); 
					}					
					 
					//on récupère les informations
					$left 		= $position_left;
					$top 		= $position_top; 
					$idrayon	= $idobject; 
					//placement des points de passage
					//pour chaque rayon, on va placer un point de passage à côté de chaque segment et deux points
					//supplémentaires qui seront des points de "virage" pour rentrer et sortir du rayon
					//le placement des points dépend de l'orientation du rayon
					if($type_ray == "classique"){
						for ($i=1; $i<=$nbsegment; $i++){ 
							$pos_left 			= $left - ($largeurpoint/2) - HAUTEURSEGMENT*sin(deg2rad($sens)) + ($largeur_ray/2)*cos(deg2rad($sens)) +  ($i-1)*($largeur_ray*cos(deg2rad($sens))) - $ecartrayon*sin(deg2rad($sens));
							$pos_top 			= $top - ($largeurpoint/2) + HAUTEURSEGMENT*cos(deg2rad($sens)) + ($largeur_ray/2)*sin(deg2rad($sens))  + ($i-1)*($largeur_ray*sin(deg2rad($sens))) + $ecartrayon*cos(deg2rad($sens));
							// $arrayCheckpoint[] 	= $idrayon.'_'.$i.':'.intval($pos_top).':'.intval($pos_left);
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $idrayon.'_'.$i.':'.intval($pos_top).':'.intval($pos_left);
						}
						
						$carres = getCarres(Rayon::load(parent::$_pdo, $idrayon), 'rayon', $tailleQuadrillage);
						foreach($carres as $carre){
							$quadrillageObject[$carre][] = array(intval($pos_top), intval($pos_left), HAUTEURSEGMENT, $largeur_ray, $sens,$rayon->getIdrayon(),'rayon');
						}
						
						$pos_left 	= $left - ($largeurpoint/2) - HAUTEURSEGMENT*sin(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens)) - $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top - ($largeurpoint/2) + HAUTEURSEGMENT*cos(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= Matrice::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'a',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							// $arrayCheckpoint[] = $str;
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}

						$pos_left 	= $left - ($largeurpoint/2) - HAUTEURSEGMENT*sin(deg2rad($sens)) + ($nbsegment * $largeur_ray)*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top - ($largeurpoint/2) + HAUTEURSEGMENT*cos(deg2rad($sens)) + ($nbsegment * $largeur_ray)*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens)) + $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= Matrice::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'b',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							// $arrayCheckpoint[] = $str;
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}
					}
					else{ //rayon type vrac, 4 points virage + 1 point de picking
					
						$pos_left 			= $left - ($largeurpoint/2) - $hauteur*sin(deg2rad($sens)) + ($largeur/2)*cos(deg2rad($sens))  - $ecartrayon*sin(deg2rad($sens));
						$pos_top 			=  $top - ($largeurpoint/2) + $hauteur*cos(deg2rad($sens)) + ($largeur/2)*sin(deg2rad($sens))  + $ecartrayon*cos(deg2rad($sens));
						// $arrayCheckpoint[] 	= $idrayon.'_1:'.intval($pos_top).':'.intval($pos_left);
						$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $idrayon.'_1:'.intval($pos_top).':'.intval($pos_left);
							
						$carres = getCarres(Rayon::load(parent::$_pdo, $idrayon), 'rayon', $tailleQuadrillage);
						foreach($carres as $carre){
							$quadrillageObject[$carre][] = array(intval($pos_top), intval($pos_left), $hauteur, $largeur, $sens,$rayon->getIdrayon(),'rayon');
						}
						
						// $pos_left 			= $left - ($largeurpoint/2) - ($hauteur/2)*sin(deg2rad($sens)) - $ecartrayon*sin(deg2rad($sens));
						// $pos_top 			=  $top - ($largeurpoint/2) + ($hauteur/2)*cos(deg2rad($sens)) - $ecartrayon*cos(deg2rad($sens));
						// $arrayCheckpoint[] 	= $idrayon.'_2:'.$pos_top.':'.$pos_left;
					
						$pos_left 	= $left - ($largeurpoint/2) - $hauteur*sin(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens)) - $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top - ($largeurpoint/2) + $hauteur*cos(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= Matrice::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'a',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}

						$pos_left 	= $left - ($largeurpoint/2) - $hauteur*sin(deg2rad($sens)) + $largeur*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top  - ($largeurpoint/2) + $hauteur*cos(deg2rad($sens)) + $largeur*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens)) + $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= Matrice::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'b',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}
							
						$pos_left 	= $left - ($largeurpoint/2) + $ecartrayonvirage*sin(deg2rad($sens)) - $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top  - ($largeurpoint/2) - $ecartrayonvirage*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= Matrice::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'c',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}

						$pos_left 	= $left - ($largeurpoint/2) + $largeur*cos(deg2rad($sens)) + $ecartrayonvirage*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top  - ($largeurpoint/2) + $largeur*sin(deg2rad($sens)) - $ecartrayonvirage*cos(deg2rad($sens)) + $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= Matrice::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'d',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}
					}
					//on place notre objet dans l'array objectsATester avec toutes les informations dont on aura besoin  
					array_push($arrayObjectsATester, array($hauteur,$largeur,$position_left,$position_top,$type,$sens,$nbsegment,$idobject));
				}
			}
			else if($arrayItem[0] == 'etage'){ //on met à jour la hauteur et la largeur de l'étage car le cadre de l'étage est resizable
				list($type,$idobject,$largeur,$hauteur,$position_ptd_top,$position_ptd_left,$position_pta_top,$position_pta_left) = $arrayItem;
				$etage = Etage::load(parent::$_pdo,$idobject);  
				$etage->setLargeur($largeur,false);
				$etage->setHauteur($hauteur,false); 
				$etage->setPtDepartTop($position_ptd_top,false);
				$etage->setPtDepartLeft($position_ptd_left,false);						
				$etage->setPtArriveTop($position_pta_top,false);
				$etage->setPtArriveLeft($position_pta_left,false);						
				$etage->update();								
				//on place notre premier checkpoint particulier, le point de départ est considéré comme un checkpoint
				$quadrillagePoint[''.(int) (intval($position_ptd_top)/$tailleQuadrillage).','.(int) (intval($position_ptd_left)/$tailleQuadrillage)][]	= 'zone_depart:'.$position_ptd_top.':'.$position_ptd_left;
				$quadrillagePoint[''.(int) (intval($position_pta_top)/$tailleQuadrillage).','.(int) (intval($position_pta_left)/$tailleQuadrillage)][]	= 'zone_arrive:'.$position_pta_top.':'.$position_pta_left;
			}
			else{ //pour chaque obstacle on met à jour 					
				list($type,$idobject,$position_top,$position_left,$largeur,$hauteur,$libelle) = $arrayItem;
							
				if($idobject == 0 || $idobject == '') { //l'obstacle vient d'être créé, on le crée dans la DB
					 $obstacle = Obstacle::create(parent::$_pdo,$currentEtage,$position_top,$position_left,$hauteur,$largeur,$type,$libelle,'F6E5A7');						
					 $save[] = $obstacle->getIdobstacle();
				}
				else{ //l'obstacle existe, on le met seulement à jour
					$obstacle = Obstacle::load(parent::$_pdo,$idobject);
					if ($obstacle != null){
						$obstacle->setPosition_top($position_top,false);
						$obstacle->setPosition_left($position_left,false);
						$obstacle->setType($type,false);
						$obstacle->setLargeur($largeur,false);
						$obstacle->setHauteur($hauteur,false);
						$obstacle->setLibelle($libelle,false);
						$obstacle->update();
					}					
				}
				
				if($obstacle != null){
					$carres = getCarres($obstacle, 'obstacle', $tailleQuadrillage);
					foreach($carres as $carre){
						$quadrillageObject[$carre][] = array(intval($pos_top), intval($pos_left), HAUTEURSEGMENT, $largeur_ray, 0,$obstacle->getIdobstacle(),'obstacle');
					}
				}
				
				//on push l'objet dans notre tableau d'objet à tester 
				array_push($arrayObjectsATester, array($hauteur,$largeur,$position_left,$position_top,$type,0,0,0));
			}					
		}
		foreach ($save as $id)
			echo $id . ';';
		parent::$_response->addVar('save', true);
		return array($arrayObjectsATester,$quadrillagePoint,$quadrillageObject);
	}
	
}
