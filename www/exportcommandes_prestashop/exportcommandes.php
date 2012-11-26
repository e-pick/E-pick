<?php

class ExportCommandes extends Module
{
	function __construct()
	{
		$this->name = 'exportcommandes';
		$this->tab = 'Export';
		$this->version = 1.0;
		
		parent::__construct();
		
		$this->displayName = $this->l('Export commandes');
		$this->description = $this->l('Module d\'exportation des commandes.');
	}

	function install()
	{
		if (!parent::install())
			return false;
	}

	function getContent()
	{
		$this->_html = '<hr><h2>'.$this->displayName.'</h2>';
		$this->_html.= '<p>'.$this->l('Ce module permet d\'exporter toutes les commandes valid&eacute;es dans un fichier CSV.').'</p>';
		
		if (isset($_POST['download']))
		{
			$sql="SELECT o.id_order AS 'Num commande', 
			p.name AS 'Produit', 
			r.quantity AS 'Quantite', 
			a1.company AS 'Raison sociale', 
			c1.id_gender AS 'Civilite', 
			a1.lastname AS 'Nom', 
			a1.firstname AS 'Prenom', 
			a1.address1 AS 'Addresse de facturation', 
			a1.address2 AS 'Addresse de facturation 2', 
			a1.postcode AS 'Code postal', 
			a1.city AS 'Ville', 
			y1.name AS 'Pays de facturation', 
			a1.phone AS 'Telephone fixe facturation', 
			a1.phone_mobile AS 'Telephone mobile facturation', 
			c1.email AS 'Email', 
			c1.newsletter AS 'Newsletter', 
			c1.optin AS 'Newsletter groupe', 
			c2.id_gender AS 'Civilite livraison', 
			CONCAT_WS(' ',a2.lastname, a2.firstname) AS 'Destinataire',
			a2.address1 AS 'Addresse 1', 
			a2.address2 AS 'Addresse 2', 
			a2.postcode AS 'CP', 
			a2.city AS 'Commune', 
			n.iso_code AS 'Pays', 
			a2.phone AS 'Telephone', 
			a2.other AS 'Instructions'
			FROM "._DB_PREFIX_."customer c1, "._DB_PREFIX_."customer c2, "._DB_PREFIX_."address a1, "._DB_PREFIX_."address a2, "._DB_PREFIX_."country_lang y1, "._DB_PREFIX_."country_lang y2, "._DB_PREFIX_."country n, "._DB_PREFIX_."product_lang p, "._DB_PREFIX_."cart_product r, "._DB_PREFIX_."orders o 
			WHERE o.valid=1 
			AND o.id_address_invoice=a1.id_address 
			AND o.id_address_delivery=a2.id_address 
			AND o.id_cart = r.id_cart 
			AND r.id_product = p.id_product 
			AND p.id_lang=1 
			AND a1.id_country = y1.id_country 
			AND a2.id_country = y2.id_country 
			AND y1.id_lang=1 
			AND y2.id_lang=1 
			AND a1.id_customer=c1.id_customer 
			AND a2.id_customer=c2.id_customer
			AND a2.id_country=n.id_country 
			ORDER BY o.id_order ASC";
			
			$orderlist = Db::getInstance()->ExecuteS($sql);
			
			$file = fopen(dirname(__FILE__).'/export.csv', 'w');
			$firstline = "Num commande;Produit;Quantite;Raison sociale;Civilite;Nom;Prenom;Addresse de facturation;Addresse de facturation 2;Code postal;Ville;Pays de facturation;Telephone fixe facturation;Telephone mobile facturation;Email;Newsletter;Newsletter groupe;Civilite livraison;Destinataire;Addresse 1;Addresse 2;CP;Commune;Pays;Telephone;Instructions";
			fwrite($file, $firstline."\r\n");
			foreach($orderlist AS $orderline){
				fwrite($file, implode(';', $orderline)."\r\n");
			}
			fclose($file);
			
			Tools::redirect('modules/exportcommandes/export.csv');
			
		}
		else
		{
			$this->_html .= '<form method="post"><input type="submit" name="download" value="'.$this->l('Exporter le fichier').'" /></form>';
			$this->_html.='
			<h3 style="margin-top:2em;">'.$this->l('Explication de quelques champs :').'</h3>
			<dl>
				<dt><i class="champ">'.$this->l('Num commande').'</i> :</dt><dd style="padding: 0.2em 0 0.6em 2em;">'.$this->l('Identifiant unique de la commande').'</dd>
				<dt><i class="champ">'.$this->l('Civilit&eacute;').'</i> :</dt><dd style="padding: 0.2em 0 0.6em 2em;">'.$this->l('(1/2) 1 si le client est un homme &ndash; 2 si le client est une femme &ndash 9 si non renseign&eacute;').'</dd>
				<dt><i class="champ">'.$this->l('Newsletter').'</i> '.$this->l('et').' <i class="champ">'.$this->l('Newsletter groupe').'</i> :</dt><dd style="padding: 0.2em 0 0.6em 2em;">'.$this->l('(0/1) 0 si non inscrit &ndash; 1 si inscrit').'</dd>
				<dt><i class="champ">'.$this->l('Destinataire').'</i> '.$this->l('et colonnes suivantes').' :</dt><dd style="padding: 0.2em 0 0.6em 2em;">'.$this->l('Coordonn&eacute;es de livraison').'</dd>
			</dl>
			';
			$this->_html .= '<hr>';
			return $this->_html;
		}
	}
}
?>