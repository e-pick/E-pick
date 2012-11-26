{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		<div id="conteneur_menu">
        	{if isset($done)}
        	<h3>{$txt_export}</h3>
            <div class="lienhaut">
                <div class="liendroite"> <a href="{$application_path}produit">{$txt_retour}</a> </div>
            </div>
            <br />
            <p>{$txt_comm}"{$txt_importer}".</p>
            
            <div id="geolocalisation"><a href="{$application_path}geolocalisation/importer" title="">{$txt_importer}</a></div>	
            {else}
			<div style="text-align:center;"><h3>{$txt_titre}</h3><hr style="width:80%;"/></div>

			<!-- Filtre -->
			<form method="post" action="" class="form search">
				<fieldset>   
					<legend>{$txt_filtre}</legend> 
					<label for="libelle">{$txt_libelle_zone}</label>
						<input type="text" id="libelle" name="libelleFilter" value="{if isset($form_libelleFilter) & !empty($form_libelleFilter)}{$form_libelleFilter}{/if}"  />
					<label for="etage">{$txt_etage}</label>
						<select name="etageFilter" id="etage">
							<option value=""></option>
							{foreach from=$arrayEtages key=id item=etage}
								<option value="{$etage->getIdetage()}" {if isset($form_etage) & !empty($form_etage) & $form_etage == {$etage->getIdetage()}}selected="true"{/if}>{$etage->getLibelle()}</option>
							{/foreach}
						</select>
					
				<div class="reset"><a href="{$application_path}geolocalisation/exporter" title="{$txt_effacer}">{$txt_effacer}</a></div>
				<input type="submit" class="submit" name="submitFilter"  id="submitFilter" value="{$txt_filtrer}" />
				</fieldset>
			</form>
			
			<!-- Affichage des zones -->
            <form action="" method="post" class="form width100" name="form2">
			<table class="tableau ligne" id="sorter">
				<tr>
					<th style="width:30%;"><img src="{$application_path}images/sort.gif" alt="sort"  /> {$txt_zone}</th>
					<th class="nosort" style="width:20px;"></th>
					<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_etage}</th>			
					<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nb_rayons}</th>
					<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nb_produits}</th>
					<th class="nosort" style="width:20px;">{$txt_selectionner}<input type="checkbox" name="Check_ctr" value="yes" onClick="if (this.checked == true)checkAll(document.form2.zoneList); else uncheckAll(document.form2.zoneList);"/></th>
					 
				</tr>
				{foreach from=$arrayZones key=myId item=zone}
					<tr>
						<td>{$zone[0]->getLibelle()}</td>
						<td style="width:20px;"><div class="couleur_choisie" style="background-color:#{$zone[0]->getCouleur()};">&nbsp;</div></td>
						<td>{$zone[1]->getLibelle()} </td>				
						<td>{$zone[2]}</td>
						<td>{$zone[3]}</td>
						<td  style="width:20px;"><input type="checkbox" id="zoneList" name="zoneList[]" value="{$zone[0]->getIdzone()}" /></td>
					</tr>
				{/foreach}
			</table>
            <input type="submit" class="submit" name="submitZones"  id="submitZones" value="{$txt_submitzones}" />
            </form>
			<script type="text/javascript">
				var sorter=new table.sorter("sorter");	sorter.init("sorter",2);	
			</script>	
            {/if}
		</div>
	</div>
</div>
{include file="$user_template/common/page_footer.tpl"}