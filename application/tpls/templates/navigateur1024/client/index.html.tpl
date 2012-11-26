{include file="$user_template/common/page_header.tpl"}

{include file="$user_template/common/menuLeftCommande.tpl"}
	
<div id="conteneur_menu">
<h3>{$txt_titre}</h3>
<hr /> 

 
<!-- Filtre -->
		<form method="post" action="" class="form search">
			<fieldset>   
				<legend>{$txt_filtre}</legend> 
				<label for="prenom">{$txt_cli_prenom}</label>
					<input type="text" id="prenom" name="prenomFilter" value="{if isset($form_prenomFilter) & !empty($form_prenomFilter)}{$form_prenomFilter}{/if}"  />
				<label for="nom">{$txt_cli_nom}</label>
					<input type="text" id="nom" name="nomFilter" value="{if isset($form_nomFilter) & !empty($form_nomFilter)}{$form_nomFilter}{/if}"  />
				<label for="societe">{$txt_cli_soc}</label>
					<input type="text" id="societe" name="societeFilter" value="{if isset($form_societeFilter) & !empty($form_societeFilter)}{$form_societeFilter}{/if}"  />
				<label for="codep">{$txt_cli_codep}</label>
					<input type="text" id="codep" name="codepFilter" value="{if isset($form_codepFilter) & !empty($form_codepFilter)}{$form_codepFilter}{/if}"  />
				<label for="municipalite">{$txt_cli_munici}</label>
					<input type="text" id="municipalite" name="municipaliteFilter" value="{if isset($form_municipaliteFilter) & !empty($form_municipaliteFilter)}{$form_municipaliteFilter}{/if}"  />				
			 
				<label for="nbco">{$txt_cli_nb_co}</label>
					<select name="nbcoFilter" id="nbco"> 
						{foreach from=$arrayNumber key=id item=nbco}
							<option value="{$nbco}" {if isset($form_nbco) & !empty($form_nbco) & $form_nbco == $nbco}selected="true"{/if}>>= {$nbco}</option>
						{/foreach}
					</select>
				<br style="clear:both" />
									
				<div class="reset"><a href="{$application_path}client" title="{$txt_effacer}">{$txt_effacer}</a></div>
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
		 
		 

	<table class="tableau" id="sorter">
		<tr>
			<th class="nosort"></th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_cli_prenom}</th>  
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_cli_nom}</th>  
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_cli_soc}</th>    
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_cli_codep}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_cli_munici}</th>  
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_cli_nb_co}</th>  
		</tr>
		{foreach from=$array_clients key=myId item=client}
			<tr> 
				<td><a href="{$application_path}client/afficher/{$client[0]->getIdClient()}" title=""><img src="{$application_path}images/edit.png" style="width:12px;height:12px;" alt="E"/></a></td> 
				<td>{$client[0]->getPrenom()}</td> 
				<td>{$client[0]->getNom()}</td> 
				<td>{if $client[0]->getNomEntreprise() != ""}{$client[0]->getNomEntreprise()}{else}-{/if}</td> 
				<td>{$client[0]->getCodePostalFacturation()}</td> 
				<td>{$client[0]->getMunicipaliteFacturation()}</td> 
				<td>{$client[1]}</td> 
			</tr>
		{foreachelse}
			<tr>
				<td colspan="7">{$txt_no_cli}</td>
			</tr>
		{/foreach}
	</table>
	<script type="text/javascript">
		var sorter=new table.sorter("sorter");
		sorter.init("sorter",0);
	</script>
</div>

{include file="$user_template/common/page_footer.tpl"}