<?php
$log = log;

	$db = new mysqli('epick01', 'dev' ,'ieghaiteij', 'dev');
	if(!$db) {
		echo 'ERROR: Could not connect to the database.';
	} else {
	
		if(isset($_GET['term'])) {
		

			$queryString = $db->real_escape_string($_GET['term']);
			if(strlen($queryString) >0) {
				$query = $db->query("SELECT PRO_LIBELLE, PRO_CODE_PRODUIT FROM PRODUIT WHERE PRO_LIBELLE LIKE '$queryString%' LIMIT 150");
				if($query) {
				
				
					while ($result = $query ->fetch_object()) {
$fp = fopen($log, 'a+');
fwrite($fp, $result->PRO_LIBELLE);
fclose($fp);				

					
//	         			echo '<li onClick="fill(\''.$result->PRO_LIBELLE.'\',\''.$result->PRO_CODE_PRODUIT.'\');">'.$result->PRO_LIBELLE.'</li>';
	         			print_r ('label=' . $result->PRO_LIBELLE);
						
	         		}
				} else {
					echo 'ERROR: There was a problem with the query.';
				}
			} 
		} 
	}
?>