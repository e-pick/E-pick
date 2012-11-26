{include file="$user_template/common/page_header.tpl"}

{if $userLevel > 1}
	{include file="$user_template/common/menuLeftCommande.tpl"}
	<div id="conteneur_menu">
{else}
	<div id="conteneur">
{/if}

	<h3>{$txt_titre}</h3>
	<hr /> 
 
 	
<!-- Filtre -->
		<form method="post" action="" class="form search">
			<fieldset>   
				<legend>{$txt_filtre}</legend> 
				<label for="numco">{$txt_num_prepa}</label>
					<input type="text" id="numco" name="numprepaFilter" value="{if isset($form_numprepaFilter) & !empty($form_numprepaFilter)}{$form_numprepaFilter}{/if}"  />
				<label for="etatco">{$txt_etat_prepa}</label> 
					<select name="etatprepaFilter" id="etatco"> 
							<option value=""></option>
						{foreach from=$array_etat_preparation key=id item=etat}
							<option value="{$id}" {if isset($form_etatprepaFilter) & !empty($form_etatprepaFilter) & $form_etatprepaFilter == $id}selected="true"{/if}>{$etat}</option>
						{/foreach}
					</select> 
				 
				<label for="datedebco" class="label_date">{$txt_date_co_com}<span class="span_date">{$txt_du}</span></label>		
					<input type="text" class="date" id="datedebco" name="datedebcoFilter" value="{if isset($form_datedebcoFilter) & !empty($form_datedebcoFilter)}{$form_datedebcoFilter}{/if}"  />										
				<label for="datefinco" class="label_date_au">{$txt_au}</label>			
					<input type="text" class="date" id="datefinco" name="datefincoFilter" value="{if isset($form_datefincoFilter) & !empty($form_datefincoFilter)}{$form_datefincoFilter}{/if}"  />				

									
				<div class="reset"><a href="{$application_path}affectation" title="{$txt_effacer}">{$txt_effacer}</a></div>
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
		
		<div id="information_bloc"> 
			<p>{$txt_explication}</p>	 
		</div>
		
		<div style="width:50%;margin:-10px auto 0px auto;height:15px;">
		 {if isset($user_feeedback) & !empty($user_feeedback)}
                <div class="{$user_feeedback[0]}_ux">{$user_feeedback[1]}</div>
        {/if}
		</div>
		
		<form action="" method="post">
		<!--<div style="text-align:right;width:95%;">
		<input type="submit" value=" {$txt_exportPDA} &raquo; " name="submitPDA" style="height:25px;width:200px" /><br />
		{$txt_modeDegrade} : <input type="submit" value=" {$txt_exportPDF} &raquo; " name="submitPDF" style="height:25px;width:200px;margin-top:2px" />
		</div>-->
		<table class="tableau" id="sorter">
			<tr> 
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_num_preparation}</th>
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
				<tr {if $i[0]->getEtat() == 0} onMouseOver="this.bgColor='red'" {else if $i[0]->getEtat() == 1} onMouseOver="this.bgColor='orange'" {else if $i[0]->getEtat() == 2} onMouseOver="this.bgColor='yellow'" {else if $i[0]->getEtat() == 3} onMouseOver="this.bgColor='green'"{/if}  onMouseOut="this.bgColor='white'">
					<td><a href="{$application_path}affectation/details/{$i[0]->getIdpreparation()}" title="{$txt_afficherDetails}">n°{$i[0]->getIdpreparation()}</a></td> 
					<td>{$array_etat_preparation[$i[0]->getEtat()]}</td> 
					<td>{$i[1]}</td>
					<td>{$i[2]}</td>
					<td>{$i[3]|seconde_format}</td>
					<td>{($i[0]->getDate_preparation() - $i[0]->getDuree())|date_format:"%d/%m/%Y %H:%M"}</td>
					<td>{$i[0]->getDate_preparation()|date_format:"%d/%m/%Y %H:%M"}</td>
					<td class="action">
						{if $i[0]->getTypePreparation() == 'PDF' && $i[0]->getEtat() == 1}
							{$filename = "PDF/{$application_prefixe}{$i[0]->getIdPreparation()}.pdf"}
							{if $filename|file_exists}
							<a href="{$application_path}{$filename}" title="{$txt_afficherPDF}" target="_blank"><img src="{$application_path}images/pdf.png" style="width:14px;height:14px;" alt="D"/></a>
							{/if}
						{/if}					
						{if $i[0]->getTypePreparation() == 'PDA' && $i[0]->getEtat() == 1}
							<img src="{$application_path}images/pda_rouge.png" style="width:14px;height:14px;" alt="D"/>
						{/if}
                        {if $i[0]->getTypePreparation() == 'PDA' && $i[0]->getEtat() == 2}
							<img src="{$application_path}images/pda_bleu.png" style="width:14px;height:14px;" alt="D"/>
						{/if}	
						
					</td> 
					<td class="action"><a href="{$application_path}commande/chemin/preparation-{$i[0]->getIdpreparation()}" title="{$txt_afficherChemin}"><img src="{$application_path}images/path.png" style="width:14px;height:14px;" alt="D"/></a></td> 
					<td class="action">{if $i[0]->getEtat() == 0}<input type="checkbox" value="{$i[0]->getIdpreparation()}" class="check" name="selected[]"/>{/if}</td>
                    <td class="action"><img src="{$application_path}images/{$i[0]->getEtat()+1}.png" style="width:25px;height:25px;" alt="D"/></td>
                    <td class="action">
                    	{if $i[0]->getEtat() >= 1}
							{$filename = "uploads/Etiq_{$i[0]->getIdPreparation()}.pdf"}
							{if $filename|file_exists}
							<a href="{$application_path}{$filename}" title="{$txt_afficherEtiq}" target="_blank"><img src="{$application_path}images/etiquette.png" style="width:14px;height:14px;" alt="D"/></a>
							{/if}
						{/if}
                    </td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="10">{$txt_no_com}</td>
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
			sorter.init("sorter",1);
		</script>
	</div>
{include file="$user_template/common/page_footer.tpl"}