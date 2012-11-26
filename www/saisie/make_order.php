<?

$Chemin_Fichier="/var/www/restricted/ssh/dev/www/E-Pick/flux/in/commandes/";//Avec le / à la fin
$File=$Chemin_Fichier."Demo_".date("dmYHi").".in.xml";
$datetime = Date(now);
$timestamp = strtotime(date("Y-m-d H:i:s"));
echo $timestamp; 

$file= fopen($File, "w+");	
	$xml="";
	$xml.= "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";	 	
	$xml.="<commande> \n";


		$xml.="<idCommande>Demo_".date("dmYHi")."</idCommande> \n";
		$xml.="<dateCommande>".$timestamp."</dateCommande> \n";
		$xml.="<modeLivraison></modeLivraison> \n";
		$xml.="<dateLivraison></dateLivraison> \n";
		$xml.="<destinataireLivraisonNom></destinataireLivraisonNom> \n";
		$xml.="<destinataireLivraisonPrenom></destinataireLivraisonPrenom> \n";
		$xml.="<codePaysLivraison>FR</codePaysLivraison> \n";
		$xml.="<codePostalLivraison></codePostalLivraison> \n";
		$xml.="<codeInseeLivraison></codeInseeLivraison>\n";
		$xml.="<regionLivraison></regionLivraison> \n ";
		$xml.="<municipaliteLivraison></municipaliteLivraison> \n";
		$xml.="<ligneAdresseLivraison></ligneAdresseLivraison> \n";
		$xml.="<nomRueLivraison></nomRueLivraison> \n";
		$xml.="<numeroBatimentLivraison></numeroBatimentLivraison> \n";
		$xml.="<uniteLivraison></uniteLivraison>\n";
		$xml.="<boitePostaleLivraison></boitePostaleLivraison> \n";
		
		$xml.="<idClient>999999999</idClient> \n";
		$xml.="<nomClient>".$_POST["Nom_Prep"]."</nomClient> \n";
		$xml.="<prenomClient>Préparateur :".$_POST["Prenom"]."</prenomClient> \n";
		$xml.="<civiliteClient></civiliteClient> \n";
		$xml.="<nomEntreprise></nomEntreprise> \n";
		$xml.="<telephoneClient></telephoneClient> \n";
		$xml.="<codePaysFacturation>FR</codePaysFacturation>\n";
		$xml.="<codePostalFacturation></codePostalFacturation>\n";
		$xml.="<codeInseeFacturation></codeInseeFacturation>\n";
		$xml.="<regionFacturation></regionFacturation> \n";
		$xml.="<municipaliteFacturation></municipaliteFacturation>\n";
		$xml.="<ligneAdresseFacturation></ligneAdresseFacturation>\n";
		$xml.="<nomRueFacturation> </nomRueFacturation> \n";
		$xml.="<numeroBatimentFacturation> </numeroBatimentFacturation>\n";
		$xml.="<uniteFacturation></uniteFacturation> \n";
		$xml.="<boitePostaleFacturation> </boitePostaleFacturation> \n";
		$xml.="<destinataireFacturation></destinataireFacturation> \n";
		$xml.="<carteFidelite></carteFidelite> \n";
		$xml.="<commentaireClient></commentaireClient> \n";
		
		 
		
		$xml.="<lignesCommandes>\n";

		for($i=0;$i<count($_POST["CodeProd"]);$i++)
							{  
							
							$cod_prod = str_replace("\r\n","",$_POST["CodeProd"][$i]);
							
			$xml.="<ligneCommande>\n";
			$xml.="<idProduit>".$cod_prod."</idProduit>\n";
			$xml.="<quantiteCommandee>1</quantiteCommandee>\n";
			//if(isset($rowTemps['PRO_ESTUNLOT'])) {
				//$xml.="<estDansUnLot>".$rowTemps['PRO_ESTUNLOT'] ."  </estDansUnLot>\n";
				//$xml.="<idLot>".$rowTemps['ID_LOTPRODUIT'] ." </idLot> \n";
				//$xml.="<libelleLot>  </libelleLot> \n";
				//$xml.="<codeEanLot>  </codeEanLot>\n";
				
			//} else {
				$xml.="<estDansUnLot>0</estDansUnLot>\n";
				$xml.="<idLot></idLot> \n";
				$xml.="<libelleLot></libelleLot> \n";
				$xml.="<codeEanLot></codeEanLot>\n";
			//}
			
			 
			$xml.="<prixUnitaireTTC></prixUnitaireTTC>\n";
			$xml.="<codeEanScanne>".$_POST["myInputs"][$i]."</codeEanScanne>\n";
			$xml.="<quantitePreparee></quantitePreparee>\n";
			$xml.="<prixUnitaireTTCPrepare> </prixUnitaireTTCPrepare> \n";
			$xml.="</ligneCommande>\n";
		}  

		
	 	$xml.="</lignesCommandes> \n";
	$xml.="</commande> \n";
	
	

fwrite($file, $xml);
fclose($file);
 
//Insérer la commmande
commandes_recup_online.php

//Visualiser la Géoloc


?>