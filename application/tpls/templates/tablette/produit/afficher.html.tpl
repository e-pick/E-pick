{include file="$user_template/common/page_header.tpl"}
<div>


	
	{if $userLevel > 1}
		{include file="$user_template/common/menuLeftProduit.tpl"}		
		<div id="conteneur_menu">
	{else}
		<div id="conteneur">
	{/if}
	 
			<div style="text-align:center;"><h3>{$txt_produit} : {$produit->getLibelle()}</h3><hr style="width:80%;"/></div>
			<div class="lienhaut">
				<div class="liendroite"> <a href="{$application_path}geolocalisation/editer/{$produit->getIdproduit()}" title=""><img src="{$application_path}images/edit.png" style="width:10px;height:10px;" alt="Edit" /> {if $userLevel > 1}{$txt_modifGeoloc}{else}{$txt_modifGeolocPreparateur}{/if}</a> | {if $userLevel > 1} <a href="{$application_path}produit/editer/{$produit->getIdproduit()}" title=""><img src="{$application_path}images/edit.png" style="width:10px;height:10px;" alt="Edit" /> Editer</a> |  {/if} <a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
			</div>
			<br />
			
			<div class="cadre_produit" style="width:80%">
				<div class="photo_produit">
					<img src="{$produit->getPhoto()}" width="128" height="128"/>
				</div>
				<div class="info_produit">
					<div class="creneau">
						<form method="post" action="" class="form" style="width:100%;">
						<fieldset>   
							<legend>{$txt_infos}</legend> 
							{if {$produit->getCodeProduit()|truncate:7:""} != "unknown"}
								&raquo; {$txt_codeProduit} : {$produit->getCodeProduit()} <br />
							{/if}						
						&raquo; {$txt_largeur} : {$produit->getLargeur()} <br />	
						&raquo; {$txt_hauteur} : {$produit->getHauteur()} <br />
						&raquo; {$txt_profondeur} : {$produit->getProfondeur()} <br />
						&raquo; {$txt_uniteMesure} : {$produit->getUniteMesure()} <br />
						&raquo; {$txt_quantiteParUniteMesure} : {$produit->getQuantiteParUniteMesure()} <br />
						&raquo; {$txt_poidsBrut} : {$produit->getPoidsBrut()} <br />
						&raquo; {$txt_poidsNet} : {$produit->getPoidsNet()} <br />
						&raquo; {$txt_estPoidsVariable} : {if $produit->getEstPoidsVariable() == 0}{$txt_non}{elseif $produit->getEstPoidsVariable() == 1}{$txt_oui}{/if}<br />
						{$priorite = $produit->getPriorite()}
						&raquo; {$txt_priorite} ({$txt_provenance}) : 	{if isset($priorite[0]) & !empty($priorite[0]) & $priorite[0] == ''}{$txt_null}{/if}
																		{if isset($priorite[0]) & !empty($priorite[0]) & $priorite[0] == 3}{$txt_debut}{/if}
																		{if isset($priorite[0]) & !empty($priorite[0]) & $priorite[0] == 2}{$txt_normal}{/if}
																		{if isset($priorite[0]) & !empty($priorite[0]) & $priorite[0] == 1}{$txt_fin}{/if}
																		
																		{if isset($priorite[1]) & !empty($priorite[1])}({$priorite[1]}){/if}<br />
						&raquo; {$txt_stock} : {if $produit->getStock() != null}{$produit->getStock()}{else}{$txt_aucun}{/if} <br />
						</fieldset>
					</form>
					
					<form method="post" action="" class="form" style="width:100%;">
						<fieldset>   
							<legend>{$txt_eans}</legend> 
							<ul>
							{foreach from=$arrayEans item=ean}
								<li>{$ean->getEan()}</li>
							{/foreach}
							</ul>
						</fieldset>
					</form>
					</div>
				</div>
			<br style="clear:both;"/>
			</div>
		</div>
	</div>
</div>

{include file="$user_template/common/page_footer.tpl"}