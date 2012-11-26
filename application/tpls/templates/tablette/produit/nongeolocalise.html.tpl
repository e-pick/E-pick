{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		<div id="conteneur_menu">
		 
			<div style="text-align:center;"><h3>{$txt_produitsNonGeoloc}</h3><hr style="width:80%;"/></div>
			<div class="lienhaut">
				<div class="liendroite"> <a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
			</div>
			<br />
			
			<!-- Filtre -->
			<form method="post" action="" class="form search">
				<fieldset>   
					<legend>{$txt_filtre}</legend> 
					<label for="libelle">{$txt_libelle}</label>
						<input type="text" id="libelle" name="libelleFilter" value="{if isset($form_libelleFilter) & !empty($form_libelleFilter)}{$form_libelleFilter}{/if}"  />
					
					<label for="codeProduit">{$txt_code_produit}</label>
						<input type="text" id="codeProduit" name="codeFilter" value="{if isset($form_codeFilter) & !empty($form_codeFilter)}{$form_codeFilter}{/if}"  />
						
					<label for="ean">{$txt_code_ean}</label>
						<input type="text" id="ean" name="eanFilter" value="{if isset($form_eanFilter) & !empty($form_eanFilter)}{$form_eanFilter}{/if}"  />
					<div class="reset"><a href="{$application_path}produit/nongeolocalise" title="{$txt_effacer}">{$txt_effacer}</a></div>
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
			
			<table class="tableau ligne" id="sorter">
				<tr>
					<th>{$txt_code_produit}</th>
					<th>{$txt_libelle}</th>
				</tr>
				{foreach from=$arrayProduits key=myId item=produit}
					<tr>
						<td><a href="{$application_path}produit/afficher/{$produit->getIdProduit()}" title="{$txt_consulter_produit}">
							{if {$produit->getCodeProduit()|truncate:7:""} != "unknown"}
								{$produit->getCodeProduit()}
							{/if}
							</a></td>
						<td><a href="{$application_path}produit/afficher/{$produit->getIdProduit()}" title="{$txt_consulter_produit}">{$produit->getLibelle()}</a></td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="2"> {$txt_noResults}</td>
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