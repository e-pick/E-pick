{include file="$user_template/common/page_header.tpl"}

{include file="$user_template/common/menuLeftCommande.tpl"}
	
<div id="conteneur_menu">
<h3>{$txt_cli} : {$client->getPrenom()} {$client->getNom()}</h3>
<hr /> 

<div class="lienhaut">
	<div class="liendroite"><a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
</div>
<br />

<fieldset class="commande">
<legend>{$txt_info_com}</legend>
<div class="gauche">
	<div class="gauche inside">{$txt_cli_prenom} : </div>
	<div class="droite inside">{$client->getPrenom()}</div>
</div>
<div class="droite">
	<div class="gauche inside">{$txt_cli_nom} :</div>
	<div class="droite inside">{$client->getNom()}</div>
</div>

<div class="gauche">
	<div class="gauche inside">{$txt_cli_soc} : </div> 
	<div class="droite inside">{if $client->getNomEntreprise() != ""}{$client->getNomEntreprise()}{else}-{/if}</div>
</div>
<div class="droite">
	<div class="gauche inside">{$txt_cli_tel} :</div>
	<div class="droite inside">{$client->getTelephone()}</div>
</div>
 

<div class="gauche">
	<fieldset class="adresse">
		<legend>{$txt_adr_fact}</legend>
		{$client->getDestinataireFacturation()}<br />
		{if $client->getNumeroBatimentFacturation()!= ""}{$client->getNumeroBatimentFacturation()} - {/if}
		{if {$client->getUniteFacturation()}!=""}{$client->getUniteFacturation()}<br/>{/if}
		{if {$client->getNomRueFacturation()}!=""}{$client->getNomRueFacturation()}<br/>{/if}
		{if {$client->getLigneAdresseFacturation()}!=""}{$client->getLigneAdresseFacturation()}<br />{/if}
		{if {$client->getBoitePostaleFacturation()}!=""}{$client->getBoitePostaleFacturation()}<br />{/if}
		{$client->getCodePostalFacturation()} {$client->getMunicipaliteFacturation()} ({$client->getRegionFacturation()} - {$client->getCodePaysFacturation()})
	</fieldset>
</div>
<div class="droite">

</div>

</fieldset>
 
	
	
</div>

{include file="$user_template/common/page_footer.tpl"}