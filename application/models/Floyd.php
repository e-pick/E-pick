<?php

class Floyd {

	var $matrice = array();
	var $previousNode = array();
	var $infiniteDistance = 1000000;

	function floyd($matrice, $infiniteDistance){
		$this -> infiniteDistance = $infiniteDistance;
		$this -> matrice = &$matrice;
		foreach (array_keys($this->matrice) as $i) {
			foreach (array_keys($this->matrice) as $j) {
				$this -> previousNode[$i][$j] = $i;
			}
		}
	}
	
	function calcul() {			
		foreach (array_keys($this->matrice) as $k) {
			foreach (array_keys($this->matrice) as $i) {
				foreach (array_keys($this->matrice) as $j) {
					$distanceIK = $this -> matrice[$i][$k];
					$distanceKJ = $this -> matrice[$k][$j];
					if ($distanceIK != $this -> infiniteDistance && $distanceKJ != $this -> infiniteDistance){
						$u = $distanceIK + $distanceKJ;
						if ($u < $this -> matrice[$i][$j]) {
							$this -> matrice[$i][$j] = $u;
							$this -> previousNode[$i][$j] = $this -> previousNode[$k][$j];							
						}
					}
				}
			}
		}
		return $this -> matrice;
	}

	function getResults() {
		$path=array();
		foreach (array_keys($this->matrice) as $start) {
			$ourShortestPath = array();
			$ourShortestPath_reversed = array();
			foreach (array_keys($this->matrice) as $i) {
				if(substr_count($i,"_a") == 0 && substr_count($i,"_b") == 0 && substr_count($i,"_c") == 0 && substr_count($i,"_d") == 0){
					$ourShortestPath[$i] = array();
					$endNode = null;
					$currNode = $i;
					$ourShortestPath[$i][] = $i;
					while ($endNode === null || $endNode != $start) {
						$ourShortestPath[$i][] = $this -> previousNode[$start][$currNode];
						$endNode = $this -> previousNode[$start][$currNode];
						$currNode = $this -> previousNode[$start][$currNode];
					}
					$ourShortestPath_reversed[$i] = array_reverse($ourShortestPath[$i]);
					if($this -> matrice[$start][$i] >= $this -> infiniteDistance) {
						$path[$start][$i][]= "no route";
					} 
					else {
						$path[$start][$i]= $ourShortestPath_reversed[$i];
					}
				}
			}	
		}
		return $path;
	}
}
  
?>