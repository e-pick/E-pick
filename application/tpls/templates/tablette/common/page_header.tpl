<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Proxi-Picking - Préparation de commandes</title>
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
		<script type="text/javascript" src="{$application_path}js/help.js"></script>	 
		{if isset($smarty.get.action) &&  $smarty.get.action == "consulter"}
			<script type="text/javascript" src="{$application_path}js/modelisation.js"></script> 
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
<!--    <div id="topWrapper"> 
      <div id="topBanner"></div> 
    </div>  
	-->
	
    <div id="topnav"> 
	  		{if $isConnected == 'true'} 
				<ul id="menu">
					<li><a href="{$application_path}" title="" shape="rect"><img src="{$application_path}/images/{$user_template}/accueil.png" border="0" alt="{$menu_accueil}"><br>{$menu_accueil}</a>  </li>
					{if $userLevel eq 3 || $userLevel eq 2}
						<li><a href="{$application_path}commande" title="" shape="rect"><img src="{$application_path}/images/{$user_template}/commandes.png" border="0" alt="{$menu_commandes}"><br>{$menu_commandes}</a>  </li> 
						<li><a href="{$application_path}produit" title=""><img src="{$application_path}/images/{$user_template}/produits.png" border="0" alt="{$menu_produits}"><br>{$menu_produits}</a></li>

						<li><a href="#" title=""><img src="{$application_path}/images/{$user_template}/modelisation.png" border="0" alt="{$menu_modelisation}"><br>{$menu_modelisation}</a>
							<ul>
								<li>&nbsp;<a href="{$application_path}modelisation/consulter" title="" shape="rect">{$menu_magasin}</a>  </li>
								<li>&nbsp;<a href="{$application_path}modelisation/demo" title="" shape="rect">{$menu_demo}</a>  </li>

							</ul>
						</li>
<!--	
						<li><a href="#" title=""><img src="{$application_path}/images/{$user_template}/admin.png" border="0" alt="{$menu_admin}"><br>{$menu_admin}</a>
							<ul>
								<li>&nbsp;<a href="{$application_path}planning/config" title="" shape="rect">{$menu_config_planning}</a>  </li>
								<li>&nbsp;<a href="{$application_path}affectation/config" title="" shape="rect">{$menu_config_affectation}</a>  </li>
								<li>&nbsp;<a href="{$application_path}utilisateur/gerer" title="" shape="rect">{$menu_gerer_les_utilisateurs}</a> </li>
								<li>&nbsp;<a href="{$application_path}geolocalisation/vider" title="" shape="rect" onclick="return confirm('Etes-vous sûr?');">{$menu_vider_geolocalisation}</a>  </li>
								<li>&nbsp;<a href="{$application_path}admin/vider" title="" shape="rect" onclick="return confirm('Etes-vous sûr?');">{$menu_vider_produit_commande}</a>  </li>
								<li>&nbsp;<a href="{$application_path}admin/stats" title="" shape="rect">{$menu_stats}</a>  </li>
								<li>&nbsp;<a href="{$application_path}admin/langue" title="" shape="rect">{$menu_messages}</a>  </li>
							</ul>
						</li>
-->
					{/if}

				<li><a href="{$application_path}utilisateur/editer/{$userId}" title="" shape="rect"><img src="{$application_path}/images/{$user_template}/user.png" border="0"><br>{$userPrenom} {$userNom}</a>  </li>			
					<li><a href="{$application_path}utilisateur/deconnexion" title="" shape="rect"><img src="{$application_path}/images/{$user_template}/deconnexion.png" border="0" alt="{$txt_se_deconnecter}"><br>{$txt_se_deconnecter}</a>  </li>
				</ul> 
			{/if}
    </div>  
    <!-- end top wrapper -->  
  
   