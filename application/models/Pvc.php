<?php	

class Pvc {
		
		/**
		 *
		 * Détermine le plus court chemin. Problème voyageur de commerce.
		 *
		 * @param $Graphe, Le graphe complet des sommets à parcourir.
		 * @param $Hamiltonien, un cycle initial au hasard.
		 * @return $Hamiltonien, le plus court chemin.
		 *
		 */
		 
		public static function calcul($Graphe, $Hamiltonien, $indice1, $indice2){
			$PrecedentCycle = "";
			$CurrentCycle = implode($Hamiltonien);	
			while($PrecedentCycle != $CurrentCycle){ 
				$PrecedentCycle = $CurrentCycle;
				$indiceArret = 0;
				for($i = 1; $i < count($Hamiltonien)-1; $i++){
					if($i < $indice1){
						$indiceArret = $indice1;
					}
					else if($i >= $indice1 && $i < $indice2){
						$indiceArret = $indice2;			
					}
					else if($i >= $indice2){
						$indiceArret = count($Hamiltonien)-1;			
					}
					for($j = $i+1; $j < $indiceArret; $j++){ 
						if(self::difference_cout($i, $j, $Graphe, $Hamiltonien) < 0){
							$Hamiltonien = self::renverse_parcours($i, $j, $Hamiltonien);					
						}					
					}			
				}	
				$CurrentCycle = implode($Hamiltonien);	
			}
			return $Hamiltonien;
		}
		
		/**
		 *
		 * Calcul la différence de cout entre deux sommets
		 * @param $si
		 * @param $sj
		 * @param $Graphe
		 * @param $Hamiltonien
		 * @return
		 *
		 */
		public static function difference_cout($i, $j, $Graphe, $Hamiltonien) {
			if ($j+1<count($Hamiltonien)) {
				return ($Graphe[$Hamiltonien[$i]][$Hamiltonien[$j+1]]
				 + $Graphe[$Hamiltonien[$i-1]][$Hamiltonien[$j]]
				 - $Graphe[$Hamiltonien[$i-1]][$Hamiltonien[$i]]
				 - $Graphe[$Hamiltonien[$j]][$Hamiltonien[$j+1]]
				 );
			} 
			else {
				return (
				$Graphe[$Hamiltonien[$i-1]][$Hamiltonien[$j]]
				- $Graphe[$Hamiltonien[$i-1]][$Hamiltonien[$i]]
				);
			}	
		}
		
		public static function renverse_parcours($i, $j, $Hamiltonien) {

			if ($i < $j) {
				$a = $i;
				$b = $j;
			} 
			else {
				$a = $j;
				$b = $i;
			}

			  while ($a < $b) {
				$Hamiltonien = self::echange_parcours ($a, $b, $Hamiltonien);
				$a++;
				$b--;
			}
			return $Hamiltonien;
		}
		
		public static function echange_parcours($ordre1, $ordre2, $Hamiltonien) {
			$inter=$Hamiltonien[$ordre2];
			$Hamiltonien[$ordre2]=$Hamiltonien[$ordre1];
			$Hamiltonien[$ordre1]=$inter;
			return $Hamiltonien;
		}
	
	
} // end class 
?>