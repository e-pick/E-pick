<?php

if (isset($_POST['CodeProd']))
{
	$MonCodeProduit=$_POST['CodeProd'];
}
Else if (isset($_POST['ean']))
{
	$MonCodeEan = $_POST['ean'];
}
Else if (isset($_GET['ean']))
{
	$MonCodeEan = $_GET['ean'];
}
Else
{
	Echo "Pas de code Ean";
	Exit;
}






define('applicationDir', '../../../../E-Pick/');
require_once(applicationDir.'application/config/bootstrap.php');
require_once(applicationDir.'application/kernel/PDO.php');

$pdo = DB :: getInstance();

if (empty($MonCodeProduit))
{
	$Where = " WHERE `EAN`.`EAN_EAN` ='".$MonCodeEan."' LIMIT 1";
}
Else
{
	$Where = " WHERE `PRODUIT`.`PRO_CODE_PRODUIT` ='".$MonCodeProduit."' LIMIT 1";
}


$select .= 'SELECT DISTINCT `PRODUIT`.`PRO_LIBELLE`, `PRODUIT`.`PRO_PHOTO`,`PRODUIT`.`PRO_CODE_PRODUIT`, `RAYON`.`RAY_LIBELLE`, `RAYON`.`ID_RAYON`,`ETAGERE`.`ID_SEGMENT`, `ETAGERE`.`ID_ETAGERE`FROM
`EST_GEOLOCALISE_DANS`
INNER JOIN `ETAGERE` ON (`EST_GEOLOCALISE_DANS`.`ID_ETAGERE` = `ETAGERE`.`ID_ETAGERE`)
INNER JOIN `PRODUIT` ON (`EST_GEOLOCALISE_DANS`.`ID_PRODUIT` = `PRODUIT`.`ID_PRODUIT`)
INNER JOIN `EAN` ON (`PRODUIT`.`ID_PRODUIT` = `EAN`.`ID_PRODUIT`)
INNER JOIN `SEGMENT` ON (`ETAGERE`.`ID_SEGMENT` = `SEGMENT`.`ID_SEGMENT`)
INNER JOIN `RAYON` ON (`SEGMENT`.`ID_RAYON` = `RAYON`.`ID_RAYON`)';
$select .=$Where;

//WHERE `EAN`.`EAN_EAN` ='. $MonCodeEan;
//Echo $select;
$pdoStatement = $pdo->prepare($select);
$pdoStatement->execute();
$count = $pdoStatement->rowCount();

if (($count = $pdoStatement->rowCount())> 0)
{



	foreach ($pdoStatement as $row)
	{
		$ETAGERE = $row['ID_ETAGERE'];
		$RAYON = $row['ID_RAYON'];
		$SEGMENT = $row['ID_SEGMENT'];
		$CODE_PRODUIT = $row['PRO_CODE_PRODUIT'];
		echo $row['PRO_LIBELLE'];
		echo "|";
		echo $row['PRO_PHOTO'];
		echo "|";
		echo $row['RAY_LIBELLE'];
		echo "|";
	}
	
	//echo "<br>=>Segment :" . $row['ID_SEGMENT'];
	//echo "<br>=>Rayon :" . $row['ID_RAYON'];
	//Donne la liste des étagères
	$select2 = 'SELECT DISTINCT `ETAGERE`.`ID_ETAGERE` FROM `ETAGERE` INNER JOIN `SEGMENT` ON (`ETAGERE`.`ID_SEGMENT` = `SEGMENT`.`ID_SEGMENT`) WHERE `SEGMENT`.`ID_SEGMENT` ='.$row['ID_SEGMENT'];
	//print_r($select2);
	$pdoStatement = $pdo->prepare($select2);
	$pdoStatement->execute();
	
	
	$pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
	$Position_Etagere = 1;
	foreach ($pdoStatement as $row)
	{
		if ($ETAGERE == $row['ID_ETAGERE'])
		{
			echo $Position_Etagere."|";
		}
		Else{
			$Position_Etagere++;
		}
	}
	
	
	//Donne la liste des segments
	$select3 = 'SELECT DISTINCT `SEGMENT`.`ID_SEGMENT` FROM `SEGMENT` INNER JOIN `RAYON` ON (`SEGMENT`.`ID_RAYON` = `RAYON`.`ID_RAYON`)WHERE `RAYON`.`ID_RAYON` ='.$RAYON;
	$pdoStatement = $pdo->prepare($select3);
	$pdoStatement->execute();
	$pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
	$Position_Segment = 1;
	foreach ($pdoStatement as $row)
	{
		if ($SEGMENT == $row['ID_SEGMENT'])
		{
			$lettre = chr($Position_Segment + ord('A') - 1);
			echo $lettre."|";
		}
		Else{
			$Position_Segment++;
		}
	}
	
	$select4 = 'SELECT DISTINCT `ZONE`.`ZON_LIBELLE` FROM `RAYON` INNER JOIN `ZONE` ON (`RAYON`.`ID_ZONE` = `ZONE`.`ID_ZONE`) WHERE `RAYON`.`ID_RAYON` ='.$RAYON;
	$pdoStatement = $pdo->prepare($select4);
	$pdoStatement->execute();
	$pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
	foreach ($pdoStatement as $row)
	{
		echo $row['ZON_LIBELLE'];
	}
	
	echo "|".$CODE_PRODUIT;
	
}
Else {
	Exit;
}

?>


  