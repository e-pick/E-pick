{include file="$user_template/common/page_header.tpl"}

{include file="$user_template/common/menuLeftCommande.tpl"}
	
<div id="conteneur_menu">
<h3>{$txt_titre}</h3>

<!-- Filtre -->
<!--		<form method="post" action="" class="form search">
			<fieldset>   
				<legend>{$txt_filtre}</legend> 
				<label for="numco">{$txt_num_com}</label>
					<input type="text" id="numco" name="numcoFilter" value="{if isset($form_numcoFilter) & !empty($form_numcoFilter)}{$form_numcoFilter}{/if}"  />
				<label for="clico">{$txt_cli_com}</label> 
					<select name="clicoFilter" id="clico"> 
							<option value=""></option>
						{foreach from=$array_clients key=id item=client}
							<option value="{$client->getIdClient()}" {if isset($form_clicoFilter) & !empty($form_clicoFilter) & $form_clicoFilter == $client->getIdClient()}selected="true"{/if}>{$client->getPrenom()} {$client->getNom()} {if $client->getNomEntreprise() != ""}({$client->getNomEntreprise()}){/if}</option>
						{/foreach}
					</select>
				<label for="etatco">{$txt_eta_com}</label> 
					<select name="etatcoFilter" id="etatco"> 
							<option value=""></option>
						{foreach from=$array_etat_commande key=id item=etat}
							<option value="{$id}" {if isset($form_etatcoFilter) & !empty($form_etatcoFilter) & $form_etatcoFilter == {$id}}selected="true"{/if}>{$etat}</option>
						{/foreach}
					</select>
				<label for="datedebco">{$txt_date_co_com} {$txt_du}</label>		
					<input type="text" class="date" id="datedebco" name="datedebcoFilter" value="{if isset($form_datedebcoFilter) & !empty($form_datedebcoFilter)}{$form_datedebcoFilter}{/if}"  />										
				<label for="datefinco" class="label_date">{$txt_au} &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</label>			
					<input type="text" class="date" id="datefinco" name="datefincoFilter" value="{if isset($form_datefincoFilter) & !empty($form_datefincoFilter)}{$form_datefincoFilter}{/if}"  />				

				<label for="datedebli">{$txt_date_li_com} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {$txt_du}</label>		
					<input type="text" class="date" id="datedebli" name="datedebliFilter" value="{if isset($form_datedebliFilter) & !empty($form_datedebliFilter)}{$form_datedebliFilter}{/if}"  />										
				<label for="datefinli" class="label_date">{$txt_au} &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</label>			
					<input type="text" class="date" id="datefinli" name="datefinliFilter" value="{if isset($form_datefinliFilter) & !empty($form_datefinliFilter)}{$form_datefinliFilter}{/if}"  />		
				<br style="clear:both" />
									
				<div class="reset"><a href="{$application_path}commande" title="{$txt_effacer}">{$txt_effacer}</a></div>
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
-->

	<table class="tableau" id="sorter">
		<tr>
			<th class="nosort"></th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_num_com}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_cli_com}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_eta_com}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nonAffectes}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_date_co_com}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_date_li_com}</th> 
			<th class="nosort"></th>
			<th class="nosort"></th>
		</tr>
		{foreach from=$array_commandes key=myId item=i}
			<tr>
				<td>
					{if $i[3]} 
						<img src="{$application_path}images/error.png" style="width:12px;height:12px;" alt="E"/>
					{else}
						<img src="{$application_path}images/check.png" style="width:12px;height:12px;" alt="E"/>
					{/if}
				</td>
				<td><a href="{$application_path}commande/afficher/{$i[0]->getIdcommande()}" title="{$txt_details}">{$i[0]->getCodeCommande()} </a></td> 
				<td style="text-align:left;"><a href="{$application_path}client/afficher/{$i[2]->getIdClient()}" title="">{$i[2]->getPrenom()} {$i[2]->getNom()} {if $i[2]->getNomEntreprise() != ""}({$i[2]->getNomEntreprise()}){/if}</a></td>
				<td>{$index = $i[0]->getEtatCommande()}{$array_etat_commande[$index]}</td> 
				<td><center>{$i[4]} / {$i[1]}</center></td> 
				<td>{$i[0]->getDateCommande()|date_format:"%d/%m/%y %H:%M"}</td> 
				<td>{$i[0]->getDateLivraison()|date_format:"%d/%m/%y %H:%M"}</td>
				<td class="action"><a href="{$application_path}commande/chemin/commande-{$i[0]->getIdcommande()}" title="{$txt_afficherChemin}"><img src="{$application_path}images/path.png" style="width:14px;height:14px;" alt="D"/></a></td> 
				<td class="action"><a href="{$application_path}commande/afficher/{$i[0]->getIdcommande()}" title="{$txt_details}">{$txt_details}</a></td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="9">{$txt_no_com}</td>
			</tr>
		{/foreach}
	</table>
	<script type="text/javascript">
		var sorter=new table.sorter("sorter");
		sorter.init("sorter");
	</script>
</div>

{include file="$user_template/common/page_footer.tpl"}