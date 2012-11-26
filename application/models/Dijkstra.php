<?php
class Dijkstra {
 
	var $visited = array();
	var $distance = array();
	var $previousNode = array();
	var $startnode =null;
	var $map = array();
	var $infiniteDistance = 0;
	var $bestPath = 0;
	var $matrixWidth = 0;
 
	function Dijkstra(&$ourMap, $infiniteDistance) {
		$this -> infiniteDistance = $infiniteDistance;
		$this -> map = &$ourMap;
		$this -> bestPath = 0;
	}
 
	function findShortestPath($start) {
		$this -> startnode = $start;
		foreach (array_keys($this->map) as $i) {
			if ($i == $this -> startnode) {
				$this -> visited[$i] = true;
				$this -> distance[$i] = 0;
			} else {
				$this -> visited[$i] = false;
				$this -> distance[$i] = isset($this -> map[$this -> startnode][$i]) 
					? $this -> map[$this -> startnode][$i] 
					: $this -> infiniteDistance;
			}
			$this -> previousNode[$i] = $this -> startnode;
		}
	
		$maxTries = count($this->map);
		for ($tries = 0; in_array(false,$this -> visited,true) && $tries <= $maxTries; $tries++) {			
			$this -> bestPath = $this->findBestPath($this->distance,array_keys($this -> visited,false,true));
			$this -> updateDistanceAndPrevious($this -> bestPath);			
			$this -> visited[$this -> bestPath] = true;
		}
		return ($this -> distance);
	}
 
	function findBestPath($ourDistance, $ourNodesLeft) {
		$bestPath = $this -> infiniteDistance;
		$bestNode = 0;
		foreach ($ourNodesLeft as $node) {
			if($ourDistance[$node] < $bestPath) {
				$bestPath = $ourDistance[$node];
				$bestNode = $node;
			}
		}
		return $bestNode;
	}
 
	function updateDistanceAndPrevious($obp) {		
		foreach (array_keys($this->map[$this -> startnode], $this->infiniteDistance) as $i) {
			if( 	isset($this->map[$obp][$i]) 
				&&	($this->map[$obp][$i] != $this->infiniteDistance || $this->map[$obp][$i] == 0 )	
				&&	($this->distance[$obp] + $this->map[$obp][$i] < $this -> distance[$i])
			) 	
			{
					$this -> distance[$i] = $this -> distance[$obp] + $this -> map[$obp][$i];
					$this -> previousNode[$i] = $obp;
			}			
		}
	}
 
	function printMap(&$map) {
		$placeholder = ' %' . strlen($this -> infiniteDistance) .'d';
		$foo = '';
		for($i=0,$im=count($map);$i<$im;$i++) {
			for ($k=0,$m=$im;$k<$m;$k++) {
				$foo.= sprintf($placeholder, isset($map[$i][$k]) ? $map[$i][$k] : $this -> infiniteDistance);
			}
			$foo.= "\n";
		}
		return $foo;
	}
 
	function getResults() {
		$ourShortestPath = array();
		$ourShortestPath_reversed = array();
		$path=array();
		foreach (array_keys($this->map) as $i) {
			if(substr_count($i,"_a") == 0 && substr_count($i,"_b") == 0 && substr_count($i,"_c") == 0 && substr_count($i,"_d") == 0){
				$ourShortestPath[$i] = array();
				$endNode = null;
				$currNode = $i;
				$ourShortestPath[$i][] = $i;
				while ($endNode === null || $endNode != $this -> startnode) {
					$ourShortestPath[$i][] = $this -> previousNode[$currNode];
					$endNode = $this -> previousNode[$currNode];
					$currNode = $this -> previousNode[$currNode];
				}
				$ourShortestPath_reversed[$i] = array_reverse($ourShortestPath[$i]);
				if($this -> distance[$i] >= $this -> infiniteDistance) {
					$path[$i][]= "no route";
				} 
				else {
					$path[$i]= $ourShortestPath_reversed[$i];
				}
			}
		}	
		return $path;
	}
} // end class 
?>