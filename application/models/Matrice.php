<?php
class Matrice
{

	private $distance = array();
	private $route = array();
	public function __construct($distance,$route){
		$this->distance = $distance;
		$this->route	= $route;
	} 
	
	
 

	public function getDistance(){
		return $this->distance;
	}
	
	public function getRoute(){
		return $this->route;
	}
	
	/********************************
	 *								*
	 * 	Fonctions supplémentaires 	*
	 *								*
	 ********************************/
	
	
	/* function add_point_virage
	 * teste la possibilité d'ajouter un point virage au rayon ou pas
	 * retourne le checkpoint sous la forme d'un string qui sera ajouter au tableau des points par la suite
	 */
	public static function add_point_virage($idrayon,$pos_top,$pos_left,$num,$objects,$hauteurpoint,$largeurpoint,$hauteuretage,$largeuretage){	
		$bog = false;
		// si le point est en dehors du cadre 
		if($pos_top < 0 || $pos_top > $hauteuretage || $pos_left < 0 || $pos_left > $largeuretage)
			$bog = true;
		else { //on teste si le point ne touche pas un rayon, une caisse ou un obstacle
			foreach($objects as $object){ 
				$top 		= $object[3];
				$left 		= $object[2];
				$hauteur 	= $object[0];
				$largeur 	= $object[1];

				//si le point touche un obstacle ou un rayon on le prend pas en compte
				if( (($pos_top + ($hauteurpoint/2)) >= ($top) 
					&& ($pos_top + ($hauteurpoint/2)) <= ($top + $hauteur) 
						&& ($pos_left + ($largeurpoint/2)) >= ($left) 
							&&  ($pos_left + ($largeurpoint/2)) <= ($left + $largeur)) ||
							($pos_top >= ($top) 
								&& ($pos_top) <= ($top + $hauteur) 
									&& ($pos_left) >= ($left) 
										&&  ($pos_left) <= ($left + $largeur)) ||
										(($pos_top + ($hauteurpoint)) >= ($top) 
											&& ($pos_top + ($hauteurpoint)) <= ($top + $hauteur) 
												&& ($pos_left + ($largeurpoint)) >= ($left) 
													&&  ($pos_left + ($largeurpoint)) <= ($left + $largeur))){  
					$bog = true;  						 
				} 				
				if($bog)
					return false;
			}
		}
		return $bog ? '' : 'bout_rayon_'.$idrayon.'_'.$num.':'.$pos_top.':'.$pos_left;  
	}
	
	public static function calculate_positions(PDO $pdo,$arrayObjects,$arrayObstaclesToDelete,$largeurpoint,$hauteurpoint,$ecartrayon,$ecartrayonvirage,$currentEtage){
		//on commence par sauvegarder la position de chaque bloc (rayons, obstacles, caisse, étage) 
		// $arrayCheckpoint 		= array();
		$arrayObjectsATester 	= array();
		$quadrillagePoint		= array();
		$tailleQuadrillage		= 200;

		foreach($arrayObjects as $item){ 
			$arrayItem = explode('-',$item);	 
		
			if($arrayItem[0] == 'rayon'){ //pour chaque rayon on met à jour 
				list($type,$idobject,$position_top,$position_left,$sens,$hauteur,$largeur,$type_ray) = $arrayItem; 
				$rayon = Rayon::load($pdo,$idobject);
				$continue = ($position_top == '*' || $position_left == '*') ? false : true; // ça veut dire qu'on supprime le rayon, donc on passe tous les calculs qui suivent
				$position_top = ($position_top == '*') ? -1 : abs($position_top);
				$position_left = ($position_left == '*') ? -1 : abs($position_left); 
				
				if($continue){
					if(FINESSE_UTILISEE == FINESSE_RAYON){
						$nbsegment 		= 1;	
						$largeur_ray 	= $largeur; 						
					}
					else{

						$nbsegment 		= count(Segment::selectByRayon($pdo,$rayon));	
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
						
						$carres = getCarres(Rayon::load($pdo, $idrayon), 'rayon', $tailleQuadrillage);
						foreach($carres as $carre){
							$quadrillageObject[$carre][] = array(intval($pos_top), intval($pos_left), HAUTEURSEGMENT, $largeur_ray, $sens,$rayon->getIdrayon(),'rayon');
						}
						
						$pos_left 	= $left - ($largeurpoint/2) - HAUTEURSEGMENT*sin(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens)) - $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top - ($largeurpoint/2) + HAUTEURSEGMENT*cos(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= self::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'a',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							// $arrayCheckpoint[] = $str;
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}

						$pos_left 	= $left - ($largeurpoint/2) - HAUTEURSEGMENT*sin(deg2rad($sens)) + ($nbsegment * $largeur_ray)*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top - ($largeurpoint/2) + HAUTEURSEGMENT*cos(deg2rad($sens)) + ($nbsegment * $largeur_ray)*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens)) + $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= self::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'b',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							// $arrayCheckpoint[] = $str;
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}
					}
					else{ //rayon type vrac, 4 points virage + 1 point de picking
					
						$pos_left 			= $left - ($largeurpoint/2) - $hauteur*sin(deg2rad($sens)) + ($largeur/2)*cos(deg2rad($sens))  - $ecartrayon*sin(deg2rad($sens));
						$pos_top 			=  $top - ($largeurpoint/2) + $hauteur*cos(deg2rad($sens)) + ($largeur/2)*sin(deg2rad($sens))  + $ecartrayon*cos(deg2rad($sens));
						$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $idrayon.'_1:'.intval($pos_top).':'.intval($pos_left);
							
						$carres = getCarres(Rayon::load($pdo, $idrayon), 'rayon', $tailleQuadrillage);
						foreach($carres as $carre){
							$quadrillageObject[$carre][] = array(intval($pos_top), intval($pos_left), $hauteur, $largeur, $sens,$rayon->getIdrayon(),'rayon');
						}
					
						$pos_left 	= $left - ($largeurpoint/2) - $hauteur*sin(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens)) - $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top - ($largeurpoint/2) + $hauteur*cos(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= self::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'a',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}

						$pos_left 	= $left - ($largeurpoint/2) - $hauteur*sin(deg2rad($sens)) + $largeur*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top  - ($largeurpoint/2) + $hauteur*cos(deg2rad($sens)) + $largeur*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens)) + $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= self::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'b',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}
							
						$pos_left 	= $left - ($largeurpoint/2) + $ecartrayonvirage*sin(deg2rad($sens)) - $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top  - ($largeurpoint/2) - $ecartrayonvirage*cos(deg2rad($sens)) - $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= self::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'c',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
						if($str != ''){
							$quadrillagePoint[''.(int) (intval($pos_top)/$tailleQuadrillage).','.(int) (intval($pos_left)/$tailleQuadrillage)][]	= $str;
						}

						$pos_left 	= $left - ($largeurpoint/2) + $largeur*cos(deg2rad($sens)) + $ecartrayonvirage*sin(deg2rad($sens)) + $ecartrayonvirage*cos(deg2rad($sens));
						$pos_top 	= $top  - ($largeurpoint/2) + $largeur*sin(deg2rad($sens)) - $ecartrayonvirage*cos(deg2rad($sens)) + $ecartrayonvirage*sin(deg2rad($sens));										
						$str 		= self::add_point_virage($idrayon,intval($pos_top),intval($pos_left),'d',$arrayObjectsATester,$hauteurpoint,$largeurpoint,$currentEtage->getHauteur(),$currentEtage->getLargeur());	
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
				$etage = Etage::load($pdo,$idobject);  
								
				//on place notre premier checkpoint particulier, le point de départ est considéré comme un checkpoint
				$quadrillagePoint[''.(int) (intval($position_ptd_top)/$tailleQuadrillage).','.(int) (intval($position_ptd_left)/$tailleQuadrillage)][]	= 'zone_depart:'.$position_ptd_top.':'.$position_ptd_left;
				$quadrillagePoint[''.(int) (intval($position_pta_top)/$tailleQuadrillage).','.(int) (intval($position_pta_left)/$tailleQuadrillage)][]	= 'zone_arrive:'.$position_pta_top.':'.$position_pta_left;
			}
			else{ //pour chaque obstacle on met à jour 					
				list($type,$idobject,$position_top,$position_left,$largeur,$hauteur,$libelle) = $arrayItem;
							
				$obstacle = Obstacle::load($pdo,$idobject);
				
				$carres = getCarres($obstacle, 'obstacle', $tailleQuadrillage);
				foreach($carres as $carre){
					$quadrillageObject[$carre][] = array(intval($pos_top), intval($pos_left), HAUTEURSEGMENT, $largeur_ray, 0,$obstacle->getIdobstacle(),'obstacle');
				}
				
				//on push l'objet dans notre tableau d'objet à tester 
				array_push($arrayObjectsATester, array($hauteur,$largeur,$position_left,$position_top,$type,0,0,0));
			}					
		}

		return array($arrayObjectsATester,$quadrillagePoint,$quadrillageObject);
	}
	
	public static function merge($quadrillagePoint,$quadrillageObject,$ecarttop,$ecartleft,$ecarttopvirage,$ecartleftvirage){
		$arrayPoints = array();
		foreach($quadrillagePoint as $key=>$zone){
			$fin = false;
			$n = 0;
			while(!$fin){	
				$n++;
				$merge = false;

				 $nbpoint = count($zone);
				   
				//on teste chaque point avec chaque point
				for($i=0; $i <  $nbpoint; $i++){
					//on récupère les infos du point qui va être testé avec tous les autres
				
					$arrayId1 		= array();
					$point 			= explode(':',$zone[$i]);
					$idpoint		= $point[0];
					$toppoint		= $point[1];
					$leftpoint 		= $point[2];
					if($idpoint == "zone_depart")
						$typepoint 	= "zone_depart";
					else if($idpoint == "zone_arrive")
						$typepoint 	= "zone_arrive";
					else if(strpos($idpoint,'bout_rayon_') !== false ){
						$typepoint 	= "bout_rayon";  			
						$arrayIdSeg = explode('-',substr($idpoint,11));
						foreach($arrayIdSeg as $idSeg){
							$arrayId = explode('_',$idSeg);
							$arrayId1[] = $arrayId[0];
						}
					}
					else{
						$typepoint 	= "segment"; 
						$arrayIdSeg = explode('-',$idpoint);
						foreach($arrayIdSeg as $idSeg){
							$arrayId = explode('_',$idSeg);
							$arrayId1[] = $arrayId[0];
						} 
					}
				
					//on teste tous les autres points
					for($j=0; $j <  $nbpoint; $j++){
						//on récupère les infos du point en cours 
						
						$arrayId2 			= array();
						$pointencours 		= explode(':',$zone[$j]);
						$idencours 			= $pointencours[0];
						$topencours			= $pointencours[1];
						$leftencours 		= $pointencours[2];
						if($idencours == "zone_depart")
							$typeencours 	= "zone_depart";
						else if($idencours == "zone_arrive")
							$typeencours 	= "zone_arrive";
						else if(strpos($idencours,'bout_rayon_') !== false ){
							$typeencours 	= "bout_rayon";												
							$arrayIdSeg2 = explode('-',substr($idencours,11));
							foreach($arrayIdSeg2 as $idSeg2){
								$arrayId = explode('_',$idSeg2);
								$arrayId2[] = $arrayId[0];
							}
						}
						else{
							$typeencours 	= "segment";
							$arrayIdSeg2 = explode('-',$idencours);
							foreach($arrayIdSeg2 as $idSeg2){
								$arrayId = explode('_',$idSeg2);
								$arrayId2[] = $arrayId[0];
							}
						}
				
						// on ne fusionne pas des points du même rayon et faut que ça soit segment/segment ou virage/virage 
						if(count(array_intersect($arrayId1,$arrayId2)) == 0 && $typeencours == $typepoint){ 
							if($typepoint == "segment"){  
								if($topencours >= ($toppoint - $ecarttop) && $topencours <= ($toppoint + $ecarttop) && $leftencours >= ($leftpoint - $ecartleft) && $leftencours <= ($leftpoint + $ecartleft)){
									$zoneRetour 	= self::do_merge($idpoint,$toppoint,$leftpoint,$typepoint,$idencours,$topencours,$leftencours,$typeencours,$i,$j,$zone,$quadrillageObject[$key]); 
									
									if(count($zone) == count($zoneRetour)){
										$merge = false;
									}
									else{
										$merge = true;
										$zone = $zoneRetour;
									}
									if ($merge) break;
								} 
							}
							else if($typepoint == "bout_rayon") { //typepoint = bout_rayon
								if($topencours >= ($toppoint - $ecarttopvirage) && $topencours <= ($toppoint + $ecarttopvirage) && $leftencours >= ($leftpoint - $ecartleftvirage) && $leftencours <= ($leftpoint + $ecartleftvirage)){
									$zoneRetour 	= self::do_merge($idpoint,$toppoint,$leftpoint,$typepoint,$idencours,$topencours,$leftencours,$typeencours,$i,$j,$zone,$quadrillageObject[$key]); 
									
									if(count($zone) == count($zoneRetour)){
										$merge = false;
									}
									else{
										$merge = true;
										$zone = $zoneRetour;
									}
									if ($merge) break;
								} 								
							}
						}
					} 
					if($merge) break;			
				} 
				if(!$merge)	$fin = true; //on sort dès qu'il n'y a plus de fusion à faire 
			}	
			$arrayPoints = array_merge($arrayPoints,$zone);
		}
		return $arrayPoints;
	}
	
	
	/* function do_merge
	 * permet de fusionner deux points (les supprimer et en créer un nouveau)
	 * retourne le tableau des points mis à jour
	 */
	private static function do_merge($id1,$top1,$left1,$type1,$id2,$top2,$left2,$type2,$i,$j,$arrayCheckpoint,$arrayObject){  
		$pos_left 	= ($left1 + $left2)/2;
		$pos_top 	= ($top1 + $top2)/ 2;		
		
		$canFusion = true;

		foreach($arrayObject as $object){ 
			$top 		= $object[0];
			$left 		= $object[1];
			$hauteur 	= $object[2];
			$largeur 	= $object[3];
			$sens		= $object[4];
			
			$dx = $pos_left - $left;
			$dy = $pos_top - $top;
			
			$xRot = ($dx * cos(deg2rad($sens)) - $dy * sin(deg2rad($sens)));
			$yRot = ($dx * sin(deg2rad($sens)) + $dy * cos(deg2rad($sens))); 
			if($xRot > 0 && $xRot < $largeur ){
				$canFusion = false;
				break;
			}
		}
		
		if ($canFusion){
			//suppression des checkpoints à fusionner

			unset($arrayCheckpoint[$i]);
			unset($arrayCheckpoint[$j]);
			$arrayCheckpoint = array_merge($arrayCheckpoint); 
			
			// on calcul les nouveaux identifiants et on ajoute le point
			if($type1 == 'bout_rayon'){
				$search 			= 'bout_rayon_';
				$pos1 				= strpos($id1, $search);
				$pos2 				= strpos($id2, $search);
				$newid1 			= substr($id1,$pos1+strlen($search),strlen($id1));
				$newid2 			= substr($id2,$pos2+strlen($search),strlen($id2));
				$id 				= "bout_rayon_".$newid1."-".$newid2;  
				$arrayCheckpoint[] 	= $id.':'.$pos_top.':'.$pos_left;
			}
			else{
				$id 				= $id1."-".$id2;		
				$arrayCheckpoint[] 	= $id.':'.$pos_top.':'.$pos_left;
			}
		}
		
		return array_merge($arrayCheckpoint); 
	}
}

?>