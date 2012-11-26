{include file="$user_template/common/page_header_ajax.tpl"}

<h3>Planning du {$creneauDebutSelect|date_format:"%A"} {$jourSelect} {$creneauDebutSelect|date_format:"%B"} {$anneeSelect} {$txt_de} {$heureSelect}h{$minutesDeb} {$txt_a} {$heureFin}h{$minutesFin}</h3>
<hr />
<div style="margin:20px;">
	<strong>{$txt_ajouter_creneau} :</strong><br /> 
	{if $currentTimestamp > $creneauFinSelect}
		{$txt_trop_tard}
	{else}
		{if $utilisateur_a_affecter|@count > 0}
		<form action="#" id="form_ajouter" method="post">
		{$txt_utilisateur} :
			<select name="utilisateur" id="utilisateur">
				{foreach from=$utilisateur_a_affecter key=id item=user}  
					<option value="{$user->getIdUtilisateur()}">{$user->getPrenom(true)} {$user->getNom(true)}</option>
				{/foreach}
			</select>	
			{$txt_de} 
			<input type="hidden" value="{$creneauDebutSelect}" name="heure_debut" id="heure_debut" size="4"/> 
			<input type="hidden" value="{$creneauFinSelect}" name="heure_fin_creneau" id="heure_fin_creneau" size="4"/> 
			<input type="text" readonly="readonly" value="{$heureSelect}:{$minutesDeb}" name="heure_debut" size="4"/> {$txt_a} 
				<select name="heure_fin" id="heure_fin">
				{foreach from=$listeHeure key=id item=heure} 
					<option value="{$heure}}">{$heure|date_format:"%R"}</option>
				{/foreach}
				</select> 
			<input type="submit" name="submit" value="{$txt_ajouter}" />
		</form>
		{else}
			{$txt_plus_user_dispo}
		{/if}
	{/if}
</div>
<hr  style="width:60%;"/>
<div style="margin:20px;">
<strong>{$txt_liste_personnes} :</strong><br />
<ul>
	{foreach from=$utilisateur_travaillent key=id item=user}  
		<li>{$user->getPrenom(true)} {$user->getNom(true)}{if $currentTimestamp <= $creneauFinSelect} <a href="#" onclick="return false;" id="{$user->getIdUtilisateur()}" class="delete">X</a>{/if}</li>
	{/foreach}
</ul>
</div>
<hr  style="width:60%;"/>
<br />
{include file="$user_template/common/page_footer_ajax.tpl"}