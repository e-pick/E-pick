{include file="$user_template/common/page_header.tpl"}

{include file="$user_template/common/menuLeftCommande.tpl"}
	
<div id="conteneur_menu">
<h3>{$txt_commande} : {$commande->getCodeCommande()}</h3>
<hr /> 

<div class="lienhaut">
	<div class="liendroite"><a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
</div>
<br />

<fieldset class="commande" style="width:80%">
<legend>{$txt_info_com}</legend>
<div class="gauche">
	<div class="gauche inside">{$txt_commande} : </div>
	<div class="droite inside">{$commande->getCodeCommande()}</div>
</div>
<div class="droite">
	<div class="gauche inside">{$txt_etat} :</div>
	<div class="droite inside">{$array_etat_commande[$commande->getEtatCommande()]}</div>
</div>

<div class="gauche">
	<div class="gauche inside">{$txt_date_co_com} : </div>
	<div class="droite inside">{$commande->getDateCommande()|date_format:"%d/%m/%Y %H:%M"}</div>
</div>
<div class="droite">
	<div class="gauche inside">{$txt_date_li_com} :</div>
	<div class="droite inside">{$commande->getDateLivraison()|date_format:"%d/%m/%Y %H:%M"}</div>
</div>

<div class="gauche">
	<div class="gauche inside">{$txt_cli_com} : </div>
	<div class="droite inside"><a href="{$application_path}client/afficher/{$client->getIdClient()}" title="">{$client->getPrenom()} {$client->getNom()} {if $client->getNomEntreprise() != ""}({$client->getNomEntreprise()}){/if}</a></div>
</div>
<div class="droite">
	<div class="gauche inside">{$txt_cli_tel} :</div>
	<div class="droite inside">{$client->getTelephone()}</div>
</div>

<div class="gauche">
	<div class="gauche inside">{$txt_cli_fidel} : </div>
	<div class="droite inside">{$commande->getCarteFidelite()}</div>
</div>
<div class="droite">
	<div class="gauche inside">{$txt_cli_comment} :</div>
	<div class="droite inside">{$commande->getCommentaireClient()}</div>
</div>

<br style="clear:both;"/>
<div class="gauche">
	<fieldset class="adresse">
		<legend>{$txt_adr_fact}</legend>
		{$client->getDestinataireFacturation()}<br />
		{if {$client->getNumeroBatimentFacturation()} != ""}{$client->getNumeroBatimentFacturation()} - {/if}
		{if {$client->getUniteFacturation()} != ""}{$client->getUniteFacturation()}<br/>{/if}
		{if {$client->getNomRueFacturation()} != ""}{$client->getNomRueFacturation()}<br/>{/if}
		{if {$client->getLigneAdresseFacturation()} != ""}{$client->getLigneAdresseFacturation()}<br />{/if}
		{if {$client->getBoitePostaleFacturation()}!= ""}{$client->getBoitePostaleFacturation()}<br />{/if}
		{$client->getCodePostalFacturation()} {$client->getMunicipaliteFacturation()} ({$client->getRegionFacturation()} - {$client->getCodePaysFacturation()})
	</fieldset>
</div>
<div class="droite">
	<fieldset  class="adresse">
		<legend>{$txt_adr_liv}</legend>
		{$commande->getDestinataireLivraison()}<br />
		{if {$commande->getNumeroBatimentLivraison()} != ""}{$commande->getNumeroBatimentLivraison()} - {/if}
		{if {$commande->getUniteLivraison()} != ""}{$commande->getUniteLivraison()}<br/>{/if}
		{if {$commande->getNomRueLivraison()} != ""}{$commande->getNomRueLivraison()}<br/>{/if}
		{if {$commande->getLigneAdresseLivraison()} != ""}{$commande->getLigneAdresseLivraison()}<br />{/if}
		{if {$commande->getBoitePostaleLivraison()}!= ""}{$commande->getBoitePostaleLivraison()}<br />{/if}
		{$commande->getCodePostalLivraison()} {$commande->getMunicipaliteLivraison()} ({$commande->getRegionLivraison()} - {$commande->getCodePaysLivraison()})
	</fieldset>	
</div>

</fieldset>
<br style="clear:both;" />

<fieldset class="commande" style="width:80%">
<legend>{$txt_compo_com}</legend>
<div class="lienhaut">
	<div class="liendroite"><a href="{$application_path}commande/chemin/commande-{$commande->getIdcommande()}" title="">{$txt_afficher_chemin}</a></div>
</div>
	<table class="tableau" id="sorter" style="width:80%;">
		<tr>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_produit}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_commandee}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_prixU}</th>  
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_prix}</th>  
		</tr>
		{$prix_total 	 = 0}
		{foreach from=$lignes_commande key=myId item=ligne}
			{$qte		 = $ligne[0]->getQuantiteCommandee()}
			{$prixU		 = $ligne[0]->getPrixUnitaireTTC()}
			{$prix 		 = $prixU * $qte}
			{$prix_total = $prix_total + $prix}
			<tr {if !$ligne[2]} style="background:#D72424" {/if}>
				<td style="text-align:left;"><a href="{$application_path}produit/afficher/{$ligne[1]->getIdProduit()}" title="{$txt_details}">{$ligne[1]->getLibelle()}</a></td>  
				<td>{$qte}</td>  
				<td>{$prixU|string_format:"%.2f"} {$devise}</td>  
				<td>{$prix|string_format:"%.2f"} {$devise}</td>  
			</tr>
		{foreachelse}
			<tr>
				<td colspan="4">{$txt_no_ligne}</td>
			</tr>
		{/foreach}
	</table>
	<div id="prix_total">{$txt_prix_total} : {$prix_total} {$devise}</div>
	<script type="text/javascript">
		var sorter=new table.sorter("sorter");
		sorter.init("sorter",0);
	</script> 
</fieldset>
	 
</div>

{include file="$user_template/common/page_footer.tpl"}