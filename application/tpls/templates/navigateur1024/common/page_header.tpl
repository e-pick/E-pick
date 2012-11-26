<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>E-Pick - votre solution de préparation de commandes</title>
		<link rel="icon" type="image/png" href="{$application_path}images/favicon.png" />
		<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="{$application_path}images/favicon.ico" /><![endif]-->
		<link type="text/css" href="{$application_path}css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="stylesheet" />	
		<link type="text/css" href="{$application_path}css/{$user_template}/style.css" rel="stylesheet" />	
		<link type="text/css" href="{$application_path}css/{$user_template}/boxy.css" rel="stylesheet" />	
		<script type="text/javascript" src="{$application_path}js/menu.js"></script>
		<script type="text/javascript" src="{$application_path}js/sorter.js"></script>
		<script type="text/javascript" src="{$application_path}js/sylvester.src.js"></script>
		<script type="text/javascript" src="{$application_path}js/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="{$application_path}js/jquery-ui-1.8.10.custom.min.js"></script>
		<script type="text/javascript" src="{$application_path}js/uidatepicker-fr.js"></script>
		<script type="text/javascript" src="{$application_path}js/jquery.boxy.js"></script>  
		<script type="text/javascript" src="{$application_path}js/jquery-barcode.js"></script> 
		<script type="text/javascript" src="{$application_path}js/help.js"></script>	 
		{if isset($smarty.get.action) &&  $smarty.get.action == "consulter"}
			<script type="text/javascript" src="{$application_path}js/modelisation.js"></script> 
            <script type="text/javascript" src="{$application_path}js/jscolor/jscolor.js"></script> 
		{else if isset($smarty.get.action) && ($smarty.get.action == "demo" || $smarty.get.action == "chemin")}
			<script type="text/javascript" src="{$application_path}js/demo.js"></script> 
			<script type="text/javascript" src="{$application_path}js/commande.js"></script>
		{else if $smarty.get.controller == "zone"}
			<script type="text/javascript" src="{$application_path}js/zone.js"></script> 
		{else if $smarty.get.controller == "planning"}
			<script type="text/javascript" src="{$application_path}js/calendar.js"></script> 
		{else if $smarty.get.controller == "commande"}			 
			<script type="text/javascript" src="{$application_path}js/commande.js"></script> 
		{else if $smarty.get.controller == "produit"}
			<script type="text/javascript" src="{$application_path}js/produit.js"></script>
		{else if $smarty.get.controller == "client"}
			<script type="text/javascript" src="{$application_path}js/client.js"></script>
		{else if $smarty.get.controller == "affectation"}
			<script type="text/javascript" src="{$application_path}js/affectation.js"></script>
		{else if $smarty.get.controller == "admin"}
			<script type="text/javascript" src="{$application_path}js/admin.js"></script>
		{else if $smarty.get.controller == "geolocalisation"}
			{if isset($smarty.get.action) && ($smarty.get.action == "editer" || $smarty.get.action == "creer")}
				<script type="text/javascript" src="{$application_path}js/rayon.js"></script> 
				<script type="text/javascript" src="{$application_path}js/produit.js"></script> 
			{/if}
		{else if $smarty.get.controller == "rayon"}
			<script type="text/javascript" src="{$application_path}js/rayon.js"></script> 
		{/if}
	</head>
	<body finesse="{$finesse_utilisee}" path="{$application_path}"> 

	
	
	    <!-- top wrapper -->  
<table cellspacing=0 cellpadding=0 width="100%" border="0">
<tr>
	<td valign="left" height="117"><img src="/E-Pick/www/images/bandeau-logo-epick.png"></td>
	<td valign="center" height="117"><h1></h1></td>
	<td valign="right" width="1034" height="117"><a href="/E-Pick/www/saisie/index.php"><img src="/E-Pick/www/images/bandeau-epick-droite.png"></a></td>
</tr>
</table>

	
    <div id="topnav" style="background:transparent url('{$application_path}/images/{$user_template}/fond_menu.jpg') repeat-x top left;"> 
	  		{if $isConnected == 'true'} 
				<ul id="menu">
					<li><a href="{$application_path}" title="" shape="rect"><img src="{$application_path}/images/{$user_template}/home.png" border="0" alt="{$menu_accueil}"><br>{$menu_accueil}</a>  </li>
					{if $userLevel eq 3 || $userLevel eq 2}
						<li><a href="{$application_path}commande" title="" shape="rect"><img src="{$application_path}/images/{$user_template}/commandes.png" border="0" alt="{$menu_commandes}"><br>{$menu_commandes}</a>  </li> 
						<li><a href="{$application_path}produit" title=""><img src="{$application_path}/images/{$user_template}/produits.png" border="0" alt="{$menu_produits}"><br>{$menu_produits}</a></li>
						<li><a href="{$application_path}modelisation/consulter" title=""><img src="{$application_path}/images/{$user_template}/modelisation.png" border="0" alt="{$menu_modelisation}"><br>{$menu_modelisation}</a>
							
						</li>
	
						<li><a href="#" title=""><img src="{$application_path}/images/{$user_template}/admin.png" border="0" alt="{$menu_admin}"><br>{$menu_admin}</a>
							<ul>
								<li>&nbsp;<a href="{$application_path}planning" title="" shape="rect">Gérer le planning</a></li>
								<li>&nbsp;<a href="{$application_path}utilisateur/gerer" title="" shape="rect">{$menu_gerer_les_utilisateurs}</a> </li>

                            	<li>&nbsp;<a href="{$application_path}admin/config" title="" shape="rect">{$menu_config_application}</a></li>

                                <li>&nbsp;<a href="{$application_path}affectation/config" title="" shape="rect">{$menu_config_affectation}</a></li>
                                <li>&nbsp;<a href="{$application_path}planning/config" title="" shape="rect">{$menu_config_planning}</a></li>

                                <li>&nbsp;<a href="{$application_path}modelisation/supprimer" title="" shape="rect">{$menu_reinit_modelisation}</a></li>
								<li>&nbsp;<a href="{$application_path}geolocalisation/vider" title="" shape="rect" onclick="return confirm('Etes-vous sûr?');">{$menu_vider_geolocalisation}</a>  </li>
								<!--<li>&nbsp;<a href="{$application_path}admin/viderProduit" title="" shape="rect" onclick="return confirm('Etes-vous sûr?');">Vider la base produit</a>  </li>-->
								<li>&nbsp;<a href="{$application_path}admin/vider" title="" shape="rect" onclick="return confirm('Etes-vous sûr?');">Vider la base commnande</a>  </li>
								<!--<li>&nbsp;<a href="{$application_path}admin/stats" title="" shape="rect">{$menu_stats}</a>  </li>-->
								<li>&nbsp;<a href="{$application_path}admin/langue" title="" shape="rect">Gérer les langues</a>  </li>
							</ul>
						</li>
					{/if}
					<li><a href="{$application_path}utilisateur/editer/{$userId}" title="" shape="rect"><img src="{$application_path}/images/{$user_template}/user.gif" border="0"><br>{$userPrenom} {$userNom}</a>  </li>			
					<li><a href="{$application_path}utilisateur/deconnexion" title="" shape="rect"><img src="{$application_path}/images/{$user_template}/deconnexion.png" border="0" alt="{$txt_se_deconnecter}"><br>{$txt_se_deconnecter}</a>  </li>
				</ul> 
			{/if}
    </div>  
    <!-- end top wrapper -->  
  
   