{include file="$user_template/common/page_header.tpl"}
{include file="$user_template/common/menuLeftCommande.tpl"}
	
<div id="conteneur_menu">
	<h3>{$txt_titre}</h3>
	<hr /> 

	<!-- Filtre -->
		<form method="post" action="" class="form search" >
			<fieldset>   
				<legend>{$txt_filtre}</legend> 
				<label for="numco">{$txt_num_com}</label>
					<input type="text" id="numco" name="numcoFilter" value="{if isset($form_numcoFilter) & !empty($form_numcoFilter)}{$form_numcoFilter}{/if}"  />
				<label for="etatco">{$txt_eta_prepa}</label> 
					<select name="etatcoFilter" id="etatco"> 
							<option value=""></option>
						{foreach from=$array_etat_preparation key=id item=etat}
							<option value="{$id}" {if isset($form_etatcoFilter) & !empty($form_etatcoFilter) & $form_etatcoFilter == {$id}}selected="true"{/if}>{$etat}</option>
						{/foreach}
					</select>
					
				<label for="preparateur">{$txt_preparateur}</label> 
					<select name="preparateurFilter" id="preparateurFilter"> 
							<option value=""></option>
						{foreach from=$arrayPreparateurs key=id item=preparateur}
							<option value="{$preparateur->getIdUtilisateur()}" {if isset($form_preparateurFilter) & !empty($form_preparateurFilter) & $form_preparateurFilter == $preparateur->getIdUtilisateur()}selected="true"{/if}>{if !$preparateur->getActif()}*{$txt_supprime}*{/if} {$preparateur->getPrenom()} {$preparateur->getNom()}</option>
						{/foreach}
					</select>
					
				<br style="clear:both" />									
				<div class="reset"><a href="{$application_path}affectation/all" title="{$txt_effacer}">{$txt_effacer}</a></div>
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
		
		
		<form action="" method="post">
		<div style="text-align:right;width:95%;">
		<br><a href="../images/6.jpg" target="_blank">Exemple de chemin</a>
		</div>
		
		<table class="tableau" id="sorter">
			<tr> 
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_num_preparation}</th>
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_preparateur}</th>
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_etat_preparation}</th> 
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nb_references}</th> 
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nb_produits}</th> 
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_duree}</th> 
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_date_limite_debut}</th> 
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_date_limite_fin}</th> 
				<th class="nosort"></th>
				<th class="nosort"></th>
				<th class="nosort"></th>
                <th class="nosort"></th>
                <th class="nosort"></th>
			</tr>
			{foreach from=$arrayPrepas key=myId item=i}
				<tr {if $i[0]->getEtat() == 0} onMouseOver="this.bgColor='red'" {else if $i[0]->getEtat() == 1} onMouseOver="this.bgColor='orange'" {else if $i[0]->getEtat() == 2} onMouseOver="this.bgColor='#029DE4'" {else if $i[0]->getEtat() == 3} onMouseOver="this.bgColor='green'"{/if}  onMouseOut="this.bgColor='white'">
					<td><a href="{$application_path}affectation/details/{$i[0]->getIdpreparation()}" title="">n°{$i[0]->getIdpreparation()}</a></td> 
					<td>{if !$i[4]->getActif()}*{$txt_supprime}*{/if} {$i[4]->getPrenom()} {$i[4]->getNom()}</td> 
					<td>{$array_etat_preparation[$i[0]->getEtat()]}</td> 
					<td width="55" valign="center">{$i[1]}</td>
					<td width="55" valign="center">{$i[2]}</td>
					<td width="55" valign="center">{$i[3]|seconde_format}</td>
					<td>{($i[0]->getDate_preparation() - $i[0]->getDuree())|date_format:"%d/%m/%Y %H:%M"}</td>
					<td>{$i[0]->getDate_preparation()|date_format:"%d/%m/%Y %H:%M"}</td>
					<td class="action">
						{if $i[0]->getTypePreparation() == 'PDF' && $i[0]->getEtat() == 1}
							{$filename = "PDF/{$application_prefixe}{$i[0]->getIdPreparation()}.pdf"}
							{if $filename|file_exists}
							<a href="{$application_path}{$filename}" title="{$txt_afficherPDF}" target="_blank"><img src="{$application_path}images/pdf.png" style="width:20px;height:20px;" alt="D"/></a>
							{/if}
						{/if}	
                        {if $i[0]->getTypePreparation() == 'PDA' && $i[0]->getEtat() == 1}
							<img src="{$application_path}images/pda_rouge.png" style="width:27px;height:29px;" alt="D"/>
						{/if}
                        {if $i[0]->getTypePreparation() == 'PDA' && $i[0]->getEtat() == 2}
							<img src="{$application_path}images/pda_bleu.png" style="width:27px;height:29px;" alt="D"/>
						{/if}					
					</td> 
					<td class="action"><a href="{$application_path}commande/chemin/preparation-{$i[0]->getIdpreparation()}" title="{$txt_afficherChemin}"><img src="{$application_path}images/path.png" style="width:20px;height:20px;" alt="D"/></a></td> 
					<td class="action">{if $i[0]->getEtat() == 0}<input type="checkbox" value="{$i[0]->getIdpreparation()}" name="selected[]" class="check"/>{/if}</td>
                    <td class="action"><img src="{$application_path}images/{$i[0]->getEtat()+1}.png" style="width:30px;height:30px;" alt="D"/></td>
                    <td class="action">
   						{if $i[0]->getEtat() >= 1}
							{$filename = "uploads/Etiq_{$i[0]->getIdPreparation()}.pdf"}
							{if $filename|file_exists}
							<a href="{$application_path}{$filename}" title="{$txt_afficherEtiq}" target="_blank"><img src="{$application_path}images/etiquette.png" style="width:20px;height:20px;" alt="D"/></a>
							{/if}
						{/if}
                    </td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="11">{$txt_no_com}</td>
				</tr>
			{/foreach}
		</table> 
        
        <div style="width:40%; margin:auto; clear:both;text-align:center;font-size:110%;">
            <fieldset>   
            <div style="margin:0px auto 10px auto;">{$txt_explain}</div>
            <table style="width:80%; margin-bottom:20px;" align="center"> 
                <tr>
                    <td><input type="checkbox" class="radio"  name="exportPDA" id="exportPDA" value="exportPDA" /></td>
                    <td align="left">{$txt_exportPDA}</td>
                    <td><input type="checkbox" class="radio"  name="exportPDF" id="exportPDF" value="exportPDF" /></td>
                    <td align="left">{$txt_exportPDF}</td>
                </tr>
            
            </table>
            <span><input type="submit" class="submitExport" value=" {$txt_exporter} &raquo; " name="submit" style="height:25px;width:200px" /></span>
            </fieldset>
        </div>
		</form>
		<script type="text/javascript">
			var sorter=new table.sorter("sorter");
			sorter.init("sorter");
		</script>
		

	
	</div>
{include file="$user_template/common/page_footer.tpl"}