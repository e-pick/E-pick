{include file="$user_template/common/page_header.tpl"}

{include file="$user_template/common/menuLeftCommande.tpl"}
	
<div id="conteneur_menu">
<h3>{$txt_titre}</h3>
<hr /> 


<!-- Filtre -->
		<form method="post" action="" class="form search">
			<fieldset>   
				<legend>{$txt_filtre}</legend> 
				<label for="numco">{$txt_num_com}</label>
					<input type="text" id="numco" name="numcoFilter" value="{if isset($form_numcoFilter) & !empty($form_numcoFilter)}{$form_numcoFilter}{/if}"  />
				<label for="clico">{$txt_cli_com}</label> 
					<input type="text" id="clico" name="clicoFilter" value="{if isset($form_clicoFilter) & !empty($form_clicoFilter)}{$form_clicoFilter}{/if}"  />
				<label for="etatco">{$txt_eta_com}</label> 
					<select name="etatcoFilter" id="etatco"> 
							<option value=""></option>
						{foreach from=$array_etat_commande key=id item=etat}
							<option value="{$id}" {if isset($form_etatcoFilter) & !empty($form_etatcoFilter) & $form_etatcoFilter == {$id}}selected="true"{/if}>{$etat}</option>
						{/foreach}
					</select>
				<label for="datedebco" class="label_date">{$txt_date_co_com} <span class="span_date">{$txt_du}</span></label>		
					<input type="text" class="date" id="datedebco" name="datedebcoFilter" value="{if isset($form_datedebcoFilter) & !empty($form_datedebcoFilter)}{$form_datedebcoFilter}{/if}"  />										
				<label for="datefinco" class="label_date_au">{$txt_au}</label>			
					<input type="text" class="date" id="datefinco" name="datefincoFilter" value="{if isset($form_datefincoFilter) & !empty($form_datefincoFilter)}{$form_datefincoFilter}{/if}"  />				

				<label for="datedebli" class="label_date">{$txt_date_li_com}<span class="span_date">{$txt_du}</span></label>		
					<input type="text" class="date" id="datedebli" name="datedebliFilter" value="{if isset($form_datedebliFilter) & !empty($form_datedebliFilter)}{$form_datedebliFilter}{/if}"  />										
				<label for="datefinli" class="label_date_au">{$txt_au}</label>			
					<input type="text" class="date" id="datefinli" name="datefinliFilter" value="{if isset($form_datefinliFilter) & !empty($form_datefinliFilter)}{$form_datefinliFilter}{/if}"  />	
                <label for="archiveco">{$txt_com_archive}</label> 
					<select name="archivecoFilter" id="archiveco"> 
						{foreach from=$array_commande_archivee key=id item=etat}
							<option value="{$id}" {if isset($form_archivecoFilter) & !empty($form_archivecoFilter) & $form_archivecoFilter == {$id}}selected="true"{/if}>{$etat}</option>
						{/foreach}
					</select>	
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

	<form action="" method="post" class="form width100" name="form2">
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
            <th class="nosort">{$txt_archiver}<input type="checkbox" name="Check_ctr" value="yes" onClick="if (this.checked == true)checkAll(document.form2.archiveList); else uncheckAll(document.form2.archiveList);"/></th>
            <th class="nosort"></th>
		</tr>
		{foreach from=$array_commandes key=myId item=i}
			<tr {if $i[0]->getEtatCommande() == 0} onMouseOver="this.bgColor='red'" {else if $i[0]->getEtatCommande() == 1} onMouseOver="this.bgColor='orange'" {else if $i[0]->getEtatCommande() == 2} onMouseOver="this.bgColor='yellow'" {else if $i[0]->getEtatCommande() == 3} onMouseOver="this.bgColor='#5C72E2'" {else if $i[0]->getEtatCommande() == 4} onMouseOver="this.bgColor='green'"{/if}  onMouseOut="this.bgColor='white'">
				<td>
					{if $i[3]} 
						<img src="{$application_path}images/error.png" style="width:12px;height:12px;" alt="E"/>
					{else}
						<img src="{$application_path}images/check.png" style="width:12px;height:12px;" alt="E"/>
					{/if}
				</td>
				<td><a href="{$application_path}commande/afficher/{$i[0]->getIdcommande()}" title="{$txt_details}">{$i[0]->getCodeCommande()} ({$i[1]})</a></td> 
				<td><a href="{$application_path}client/afficher/{$i[2]->getIdClient()}" title="">{$i[2]->getPrenom()} {$i[2]->getNom()} {if $i[2]->getNomEntreprise() != ""}({$i[2]->getNomEntreprise()}){/if}</a></td>
				<td>{$index = $i[0]->getEtatCommande()}{$array_etat_commande[$index]}</td> 
				<td>{$i[4]} / {$i[1]} </td> 
				<td>{$i[0]->getDateCommande()|date_format:"%d/%m/%Y %H:%M"}</td> 
				<td>{$i[0]->getDateLivraison()|date_format:"%d/%m/%Y %H:%M"}</td>
				<td class="action"><a href="{$application_path}commande/chemin/commande-{$i[0]->getIdcommande()}" title="{$txt_afficherChemin}"><img src="{$application_path}images/path.png" style="width:25px;height:25px;" alt="D"/></a></td> 
				<td class="action"><a href="{$application_path}commande/afficher/{$i[0]->getIdcommande()}" title="{$txt_details}">{$txt_details}</a></td>
                <td class="action"><input type="checkbox" id="archiveList" name="archive[{$i[0]->getIdcommande()}]" value="1" {if {$i[0]->getArchiveCommande()} == "1"}checked="checked"{/if} /></td>
                <td class="action"><img src="{$application_path}images/{$i[0]->getEtatCommande()}.png" style="width:35px;height:35px;" alt="D"/></td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="10">{$txt_no_com}</td>
			</tr>
		{/foreach}
	</table>
    <input type="submit" class="submit" name="submitArchiver"  id="submitArchiver" value="{$txt_archiver}" />
    </form>
	<script type="text/javascript">
		var sorter=new table.sorter("sorter");
		sorter.init("sorter");
	</script>
</div>

{include file="$user_template/common/page_footer.tpl"}