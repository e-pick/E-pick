{include file="$user_template/common/page_header.tpl"}
{include file="$user_template/common/menuLeftCommande.tpl"}
	
<div id="conteneur_menu">
	<h3>{$txt_titre}</h3>
	<hr /> 

	
	{if $txt_etat == 0}
		{$txt_error}<br />
		<a href="javascript:history.go(-1)" title="{$txt_retour}">{$txt_retour}</a>
	{else if $txt_etat == 1}
		
	<div class="lienhaut">
		<div class="liendroite"><a href="javascript:history.go(-1)" title="{$txt_retour}">{$txt_retour}</a></div>
	</div>
	<form action="" method="post">
	
		<!-- Choix utilisateur -->
		<div style="width:400px;margin:auto;margin-top:50px;"> 
		<fieldset>
			{$txt_date_max} : {$date_limite|date_format:"%d/%m/%Y %H:%M"}<br />
			{$txt_duree_appro} : {$temps_prepa|seconde_format}<br />
			{$txt_mode_prepa} : <span style="font-weight:bold"> {$modePrepa} </span><br /><br />
			{if $affectation_valide == 1}
			{else}
				<span style="color:red;">{$txt_error}</span><br />
			{/if}
			<legend>{$txt_choix_prep}</legend>
			<!--<label for="choixPriorite">{$txt_preparateur} : </label> 
			<select name="utilisateur" id="utilisateur">
				{foreach from=$users key=id item=user}  
					<option value="{$user->getIdUtilisateur()}">{$user->getPrenom()} {$user->getNom()}</option>
				{/foreach}
			</select>-->
			</fieldset>
		</div>
	
		<!-- Rappel bons de préparation à affecter -->
		
		<div > <h3>{$txt_liste_prepas}</h3> </div>
		
		{foreach from=$affichage key=myId item=prepa}
			<table class="tableau">
				
				<tr>
					<th colspan="6" style="text-align:left;"> {$txt_bon_preparation} n° : {$myId+1} | <a class="details_bons"  onclick="return false;" id="{$myId}_{$type}_{$idEtage}" href='#'> {$txt_details} </a></th>
                    <th colspan="3" style="text-align:right"><label for="choixPriorite">{$txt_preparateur} : </label> 
                    <select name="utilisateur[{$myId}]" id="utilisateur[{$myId}]" class="choix">
                     <option value="">{$txt_choix}</option>
				{foreach from=$users key=id item=user}  
					<option value="{$user->getIdUtilisateur()}">{$user->getPrenom()} {$user->getNom()}</option>
				{/foreach}
			</select></th>
				</tr>
				
				<tr> 
					<th > {$txt_num_com}</th>
					<th style="width:20%;"> {$txt_cli_com}</th>
					<th> {$txt_typeLivraison}</th>
					<th> {$txt_dateMaxPrepa}</th>
					<th> {$txt_dateLivraison}</th>
					<th> {$txt_nbRefAAffectees}</th>
					<th> {$txt_nbProdAAffectees}</th>  
					<th></th>  
				</tr>
				
				{foreach from=$prepa[0] key=myId item=commande}
					<tr>
						 
						<td> {$commande[0]->getCodeCommande()}({$commande[2]})</td>
						<td> {$commande[1]->getPrenom()} {$commande[1]->getNom()} {if {$commande[1]->getNomEntreprise()} != ''}({$commande[1]->getNomEntreprise()}){/if}</td>
						<td> {$commande[0]->getModelivraison()} </td> 
						<td> {{$commande[0]->getDateLivraison()-$delai_avant_livraison}|date_format:"%d %b %Y à %H:%M"}</td>
						<td> {$commande[0]->getDateLivraison()|date_format:"%d %b %Y à %H:%M"}</td>
						<td> {$commande[2]} / {$commande[4]}</td>
						<td> {$commande[3]}</td> 
						<td> {$commande[5]}</td> 
					</tr>
				{/foreach}
				<tr>
					<td colspan="9" style="text-align:left;font-size:110%;border-top:1px solid black;">{$txt_tempsEstime} : <span class="total_prep_0" style="font-weight:bold;">{$prepa[1]|seconde_format}</span>.</td>
				</tr>
			</table> 
		{/foreach}  
        <input type="submit" name="submit_user" id="submit_user" value="{$txt_affecter}" style="float:right; padding:5px 20px;margin-bottom:20px;margin-right:60px;"/>
	</form>
	
	{/if}
	</div>
{include file="$user_template/common/page_footer.tpl"}