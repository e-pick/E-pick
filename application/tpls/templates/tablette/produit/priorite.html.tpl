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
					<label for="libelle">{$txt_libelle_produit}</label>
						<input type="text" id="libelle" name="libelleFilter" value="{if isset($form_libelleFilter) & !empty($form_libelleFilter)}{$form_libelleFilter}{/if}"  />
						
					<label for="codeProduit">{$txt_code_produit}</label>
						<input type="text" id="codeProduit" name="codeFilter" value="{if isset($form_codeFilter) & !empty($form_codeFilter)}{$form_codeFilter}{/if}"  />
						
					<label for="ean">{$txt_code_ean}</label>
						<input type="text" id="ean" name="eanFilter" value="{if isset($form_eanFilter) & !empty($form_eanFilter)}{$form_eanFilter}{/if}"  />
						
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
					<br style="clear:both" />
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
					<br style="clear:both" />
					<label for="rayon">{$txt_rayon}</label>
						{if isset($arrayRayons) && !empty($arrayRayons)}
							<select name="rayonFilter" id="rayon">
								<option value=""></option>
								{foreach from=$arrayRayons key=id item=rayon}
									<option value="{$rayon->getIdrayon()}" {if isset($form_rayon) & !empty($form_rayon) & $form_rayon == $rayon->getIdrayon()}selected="true"{/if}>{$rayon->getLibelle()}</option>
								{/foreach}
							</select>
						{else}
							<select name="rayonFilter" id="rayon" disabled></select>
						{/if}
					<br style="clear:both" />
					{if $finesse_utilisee >= $finesse_segment}
						<label for="segment">{$txt_segment}</label>
							{if isset($arraySegments) && !empty($arraySegments)}
								<select name="segmentFilter" id="segment">
									<option value=""></option>
									{foreach from=$arraySegments key=id item=segment}
										<option value="{$segment[0]->getIdsegment()}" {if isset($form_segment) & !empty($form_segment) & $form_segment == $segment[0]->getIdsegment()}selected="true"{/if}>{$segment[1]}</option>
									{/foreach}
								</select>
							{else}
								<select name="segmentFilter" id="segment" disabled></select>
							{/if}
						<br style="clear:both" />
						{if $finesse_utilisee >= $finesse_etagere}
							<label for="etagere">{$txt_etagere}</label>
								{if isset($arrayEtageres) && !empty($arrayEtageres)}
									<select name="etagereFilter" id="etagere">
										<option value=""></option>
										{foreach from=$arrayEtageres key=id item=etagere}
											<option value="{$etagere[0]->getIdetagere()}" {if isset($form_etagere) & !empty($form_etagere) & $form_etagere == $etagere[0]->getIdetagere()}selected="true"{/if}>{$etagere[1]}</option>
										{/foreach}
									</select>
								{else}
									<select name="etagereFilter" id="etagere" disabled></select>
								{/if}
							<br style="clear:both" />
						{/if}
					{/if}
						
				<div class="reset"><a href="{$application_path}produit/priorite" title="{$txt_effacer}">{$txt_effacer}</a></div>
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
			<!--Fin Filte -->
			
			<!-- Choix priorité -->
			<form method="post" action="">
				<div style="width:300px;margin:auto;margin-top:50px;">
					
					<fieldset>
						
						{if $choixFinessePriorite != 'segment' && $choixFinessePriorite != 'etagere'}
							<div style="float:left;width:8%;"><input type="checkbox" name="idFinessePriorite" value="{$idFinessePriorite}"/></div>
							<div> {$txt_finessePriorite}</div>
							<input type="text" name="choixFinessePriorite" value="{$choixFinessePriorite}" style="display:none"/>
						{/if}
						
						<legend> {$txt_affectPriorite}</legend>
						<label for="choixPriorite"> {$txt_priorite}</label> 
						<select name="choixPriorite" id="choixPriorite">
							<option value="">{$txt_null}</option>
							<option value="3">{$txt_debut}</option>
							<option value="2" selected="true">{$txt_normal}</option>
							<option value="1">{$txt_fin}</option>	
						</select>
						<input type="submit" name="submitPriorite" id="submitPriorite" value="Affecter" onclick="return confirm('{$txt_confirm}')"/>
					</fieldset>
				</div>
			
				<!-- Affichage des rayons -->
				<table class="tableau ligne" id="sorter">
					<tr>
						<th class="nosort" style="width:20px;"><input id="selectAllProduits" type="checkbox" value="all"></th>
						<th style="width:15%;"><img src="{$application_path}images/sort.gif" alt="sort"  /> {$txt_code_produit}</th>
						<th style="width:25%;"><img src="{$application_path}images/sort.gif" alt="sort"  /> {$txt_libelle_produit}</th>
						<th style="width:15%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_zone}</th>
						<th style="width:15%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_rayon}</th>
						{if $finesse_utilisee >= $finesse_segment}
							<th style="width:10%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_segment}</th>
							{if $finesse_utilisee >= $finesse_etagere}
								<th style="width:10%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_etagere}</th>
							{/if}
						{/if}
						<th style="width:10%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_priorite} ({$txt_provenance})</th>						 
					</tr>
					{foreach from=$arrayProduits key=myId item=produit}
						<tr>
							<td><input type="checkbox" class ="checkProduit" name="Produit[]" value="{$produit[0]->getIdProduit()}"> </td>
							<td><a href="{$application_path}produit/afficher/{$produit[0]->getIdProduit()}" title="{$txt_consulter_produit}">
								{if {$produit[0]->getCodeProduit()|truncate:7:""} != "unknown"}
									{$produit[0]->getCodeProduit()}
								{/if}
								</a></td>
							<td><a href="{$application_path}produit/afficher/{$produit[0]->getIdProduit()}" title="{$txt_consulter_produit}">{$produit[0]->getLibelle()}</a></td>
							<td>{$produit[4]} </td>				
							<td><a href="{$application_path}rayon/afficher/{$produit[3]->getIdrayon()}/0/0" title="{$txt_consulter_rayon}">{$produit[3]->getLibelle()}</a></td>
							{if $finesse_utilisee >= $finesse_segment}
								<td>{$produit[2]} </td>
								{if $finesse_utilisee >= $finesse_etagere}
									<td>{$produit[1]} </td>
								{/if}
							{/if}
							{$retour = $produit[0]->getPriorite()}
							{$priorite = $retour[0]}
							{$provenance = $retour[1]}
							<td>{if $priorite == 1}{$txt_fin}{else if $priorite == 2}{$txt_normal}{else if $priorite == 3}{$txt_debut}{/if} ({$provenance})</td>
						</tr>
					{foreachelse}
						<tr>
							<td colspan="8">{$txt_noResults}</td>
						</tr>
					{/foreach}
				</table>
			</form>
			
			<script type="text/javascript">
				var sorter=new table.sorter("sorter");	sorter.init("sorter",0);	
			</script>	
		</div>
	</div>
</div>

{include file="$user_template/common/page_footer.tpl"}