{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		
		<div id="conteneur_menu">
			<div style="text-align:center;"><h3>{$txt_titre}</h3><hr style="width:80%;"/></div>
			
			<!-- Filtre -->
			<form method="post" action="" class="form search">
				<fieldset>   
					<legend>{$txt_filtre}</legend> 
					<label for="libelle">{$txt_libelle_rayon}</label>
						<input type="text" id="libelle" name="libelleFilter" value="{if isset($form_libelleFilter) & !empty($form_libelleFilter)}{$form_libelleFilter}{/if}"  />
					<label for="etage">{$txt_etage}</label>
						{if isset($arrayEtages) && !empty($arrayEtages)}
							<select name="etageFilter" id="etage">
								<option value=""></option>
								{foreach from=$arrayEtages key=id item=etage}
									<option value="{$etage->getIdetage()}" {if isset($form_etage) & !empty($form_etage) & $form_etage == {$etage->getIdetage()}}selected="true"{/if}>{$etage->getLibelle()}</option>
								{/foreach}
							</select>
						{else}
							<select name="etageFilter" id="etage" disabled></select>
						{/if}
					<label for="zone">{$txt_zone}</label>
						{if isset($arrayZones) && !empty($arrayZones)}
							<select name="zoneFilter" id="zone">
								<option value=""></option>
								{foreach from=$arrayZones key=id item=zone}
									<option value="{$zone->getIdzone()}" {if isset($form_zone) & !empty($form_zone) & $form_zone == {$zone->getIdzone()}}selected="true"{/if}>{$zone->getLibelle()}</option>
								{/foreach}
							</select>
						{else}
							<select name="zoneFilter" id="zone" disabled></select>
						{/if}
				<div class="reset"><a href="{$application_path}rayon" title="{$txt_effacer}">{$txt_effacer}</a></div>
				<input type="submit" class="submit" name="submitFilter"  id="submitFilter" value="{$txt_filtrer}" />
				</fieldset>
			<div style="text-align:center;clear:both;margin:-10px auto 10px auto;">
				{if $nb_resultats <= $nb_resultats_par_page}
					{$nb_resultats} {$txt_nb_resultats} {$nb_resultats}
				{else}
					{$nb_resultats_par_page} {$txt_nb_resultats} {$nb_resultats} - {$nombre_de_pages} {$txt_pages}
				{/if}
			</div>
				<label for="pageChange">{$txt_page} :  </label>  
				<select name="pageFilter" id="pageChange">
				{section name=i start=1 loop=($nombre_de_pages+1)}
					{$nb = $smarty.section.i.index}
						<option value="{$nb}"{if isset($form_page) & !empty($form_page) & $form_page == $nb}selected="true"{/if}>{$nb}</option>
				{/section}
				</select>
			</form>

			
			
			<!-- Affichage des  rayons -->
			<table class="tableau ligne" id="sorter">
				<tr>
					<th	style="width:35%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_libelle_rayon}</th>
					<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort"  /> {$txt_etage}</th>
					<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort"  /> {$txt_zone}</th>
					{if $finesse_utilisee >= $finesse_segment}				
						<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nb_segments}</th>
					{/if}
					 
				</tr>
				{foreach from=$arrayRayons key=myId item=rayon}
					<tr>
						<td><a href="{$application_path}rayon/afficher/{$rayon[0]->getIdrayon()}/0/0" title="{$txt_consulter_rayon}">{$rayon[0]->getLibelle()}</a></td>
						<td>{$rayon[1]->getLibelle()}</td>						
						<td>{$rayon[2]->getLibelle()}</td>						
						{if $finesse_utilisee >= $finesse_segment}
							<td>{if $rayon[3] <= 1}{$rayon[3]} {$txt_segment}{else}{$rayon[3]} {$txt_segments}{/if}	</td> 
						{/if}
					</tr>
				{/foreach}
			</table>
			<script type="text/javascript">
				var sorter=new table.sorter("sorter");	sorter.init("sorter",0);	
			</script>	
		</div>
	</div>
</div>
	
{include file="$user_template/common/page_footer.tpl"}