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
	<td valign="center" height="117"><h1>SITE DE DEVELOPPEMENT</h1></td>
	<td valign="right" width="1034" height="117"><img src="/E-Pick/www/images/bandeau-epick-droite.png"></td>
</tr>
</table>

	
    <!-- end top wrapper -->  
  
   