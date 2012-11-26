{include file="$user_template/common/page_header.tpl"}
<div id="conteneur">
	<div style="text-align:center;"><h3>Statistiques</h3><hr style="width:80%;"/></div> 
	
	
	
	
	<!-- Filtre -->
			<form method="post" action="" class="form search">
				<fieldset>   
					<legend>{$txt_filtre}</legend> 
					<label for="libelle">{$txt_libelle_produit}</label>
						<input type="text" id="libelle" name="libelleFilter" value="{if isset($form_libelleFilter) & !empty($form_libelleFilter)}{$form_libelleFilter}{/if}"  />
					
					<label for="codeProduit">{$txt_code_produit}</label>
						<input type="text" id="codeProduit" name="codeFilter" value="{if isset($form_codeFilter) & !empty($form_codeFilter)}{$form_codeFilter}{/if}"  />
					
						
				<div class="reset"><a href="{$application_path}admin/stats" title="{$txt_effacer}">{$txt_effacer}</a></div>
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
	
	
	 
	
	<table class="tableau" id="sorter" style="width:80%;">
		<tr>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_codeProduit}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_libelleProduit}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_tempsMoyen}</th>  
		</tr>
		{$prix_total 	 = 0}
		{foreach from=$produits key=myId item=produit}
			<tr>
				<td> {$produit->getCodeProduit()}</td>  
				<td style="text-align:left;">{$produit->getLibelle()}</td>  
				<td >{$produit->getTempsMoyenAccess()}</td>  
			</tr>
		{foreachelse}
			<tr>
				<td colspan="5">{$txt_no_ligne}</td>
			</tr>
		{/foreach}
	</table>
	<script type="text/javascript">
		var sorter=new table.sorter("sorter");
		sorter.init("sorter");
	</script> 
</div>
{include file="$user_template/common/page_footer.tpl"}