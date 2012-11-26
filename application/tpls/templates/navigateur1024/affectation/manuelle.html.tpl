{include file="$user_template/common/page_header.tpl"}
{include file="$user_template/common/menuLeftCommande.tpl"}
	
<div id="conteneur_menu">
	<h3>{$txt_titre}</h3>
	<hr />
	<br />
	{if $type == 'listing'}
		{if isset($rechargerTempsPreparation) && $rechargerTempsPreparation == 1}
			<div id="conteneurErreur">
					<div id="imageErreur">
						<img src="{$application_path}images/error.png" alt="error">
					</div>
					<div id="texteErreur">
						{$txt_rechargerTempsPreparation}
						<br />
						<a href="{$application_path}affectation/recalculer">{$txt_recalculer}</a>
					</div>
					<br style="clear:both"/>
			</div>

		{else}
			<!-- Filtre -->
			<form method="post" action="" class="form search">
				<fieldset>   
					<legend>{$txt_filtre}</legend> 
					<label for="numco">{$txt_num_com}</label>
						<input type="text" id="numco" name="numcoFilter" value="{if isset($form_numcoFilter) & !empty($form_numcoFilter)}{$form_numcoFilter}{/if}"  />
					<label for="clico">{$txt_cli_com}</label> 
					<input type="text" id="clico" name="clicoFilter" value="{if isset($form_clicoFilter) & !empty($form_clicoFilter)}{$form_clicoFilter}{/if}"  />
					 <label for="typeco">{$txt_typeLivraison}</label> 
						<select name="typeLivraisonFilter" id="typeLivraison"> 
								<option value=""></option>
							{foreach from=$array_typeLivraison key=id item=typeLivraison}
								<option value="{$typeLivraison}" {if isset($form_typeLivraisonFilter) & !empty($form_typeLivraisonFilter) & $form_typeLivraisonFilter == $typeLivraison}selected="true"{/if}>{$typeLivraison}</option>
							{/foreach}
						</select>
					 
					<label for="datedebco" class="label_date">{$txt_date_co_com} <span class="span_date">{$txt_du}</span></label>		
						<input type="text" class="date" id="datedebco" name="datedebcoFilter" value="{if isset($form_datedebcoFilter) & !empty($form_datedebcoFilter)}{$form_datedebcoFilter}{/if}"  />										
					<label for="datefinco" class="label_date_au">{$txt_au}</label>			
						<input type="text" class="date" id="datefinco" name="datefincoFilter" value="{if isset($form_datefincoFilter) & !empty($form_datefincoFilter)}{$form_datefincoFilter}{/if}"  />				

					<label for="datedebli" class="label_date">{$txt_date_li_com} <span class="span_date">{$txt_du}</span></label>		
						<input type="text" class="date" id="datedebli" name="datedebliFilter" value="{if isset($form_datedebliFilter) & !empty($form_datedebliFilter)}{$form_datedebliFilter}{/if}"  />										
					<label for="datefinli" class="label_date_au">{$txt_au}</label>			
						<input type="text" class="date" id="datefinli" name="datefinliFilter" value="{if isset($form_datefinliFilter) & !empty($form_datefinliFilter)}{$form_datefinliFilter}{/if}"  />		
					<br style="clear:both" />
										
					<div class="reset"><a href="{$application_path}affectation/manuelle" title="{$txt_effacer}">{$txt_effacer}</a></div>
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
				<p>{$txt_selection_commande}</p>
			</div>
		
			<form id="commandeForm" action="{$application_path}affectation/manuelle" method="POST" >
			

			{foreach from=$commandes key=myId item=j}
				<div style="text-align:center;width:89%;margin:auto;height:15px;font-weight:bold;margin-bottom:0px;">
					{$myId}
					</div>				
				<table class="tableau" id="sorter" style="margin-top:0px;">
				
					<tr>
						<th class="nosort"><input type="checkbox" class="check selectAll" name="" value="{$arrayEtages[$myId]}_0" /></th>
						<th class="nosort"> </th>
						<th><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_num_com}</th>
						<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_cli_com}</th>
						<th><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_typeLivraison}</th>
						<th><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_dateMaxPrepa}</th>
						<th><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_dateLivraison}</th>
						<th><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_nbRefAAffectees}</th>
						<th><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_nbRefTotal}</th>
						<th><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_nbProdAAffectees}</th>
						<th><img src="{$application_path}images/sort.gif" alt="sort" />  {$txt_tempsEstime}</th> 
						<th  class="nosort"> </th>
					</tr>
					
					{foreach from=$j key=myId item=commande}
						<tr>
							<td> <input type="checkbox" class="check total" name="commandes[]" value="{$commande[4]}_{$commande[0]->getIdCommande()}_{$commande[5]}" /></td>
							<td>
								{if $commande[6] > 0} 
									<img src="{$application_path}images/error.png" style="width:12px;height:12px;" alt="E"/>
								{else}
									<img src="{$application_path}images/check.png" style="width:12px;height:12px;" alt="E"/>
								{/if}
							</td>
							<td><a href="{$application_path}commande/afficher/{$commande[0]->getIdcommande()}" title="{$txt_details}">{$commande[0]->getCodeCommande()}</a></td>
							<td><a href="{$application_path}client/afficher/{$commande[1]->getIdClient()}" title="">{$commande[1]->getPrenom()} {$commande[1]->getNom()} {if {$commande[1]->getNomEntreprise()} != ''}({$commande[1]->getNomEntreprise()}){/if}</a></td>
							<td>{$commande[0]->getModelivraison()} </td> 
							<td nowrap>{{$commande[0]->getDateLivraison()-$delai_avant_livraison}|date_format:"%d %b &agrave; %H:%M"}</td>
							<td  nowrap> {$commande[0]->getDateLivraison()|date_format:"%d %b &agrave; %H:%M"}</td>
							<td>{$commande[2]}</td>
							<td>{$commande[3]}</td>
							<td>{$commande[8]}</td>
							<td>{$commande[5]|seconde_format}</td>
							<td class="action"><a href="{$application_path}commande/afficher/{$commande[0]->getIdcommande()}" title="{$txt_details}">{$txt_details}</a></td>
						</tr>
					{/foreach}
					
				</table>
				
			<script type="text/javascript">
				var sorter=new table.sorter("sorter");
				sorter.init("sorter",1);
			</script>
					<div style="margin:-30px 0 30px 90px;height:20px;">
						<div style="float:left;line-height:20px;margin-right:10px;font-size:110%;">{$txt_total_temps}
						<span class="total_prep_{$commande[4]}" style="font-weight:bold;font-size:120%;">0 min</span>.</div>
					</div>
                    <div style="width:60%; margin:auto; clear:both;text-align:center;font-size:110%;">
                    	<fieldset>   
                        <div style="margin:0px auto 10px auto;">{$txt_seconde_etape} </div>
                        <table style="width:60%; margin-bottom:20px;" align="center"> 
                        <tr>
							<td align="right">{$txt_calculer_options}</td>
                            <td><input type="radio" class="radio"  name="modePreparation" value="all" checked="checked" /></td>
							<td></td>
							<td></td>
						</tr>
                        <tr>
                        	<td align="right">{$txt_mono_commandes}</td>
							<td><input type="radio" class="radio"  name="modePreparation" value="monoCommande" /></td>
                            <td align="right">{$txt_mono_zones}</td>
							<td><input type="radio" class="radio"  name="modePreparation" value="monoZone" /></td>
						</tr>
						<tr>
                        	<td align="right">{$txt_multi_commandes}</td>
							<td><input type="radio" class="radio"  name="modePreparation" value="multiCommandes" /></td>
							<td align="right">{$txt_multi_zones}</td>
							<td><input type="radio" class="radio"  name="modePreparation" value="multiZones" /></td>
						</tr>
					
					</table>
						<span><input type="button"   class="submitListeCommande" value="{$txt_etape_selection}" style="padding:5px 15px;" /></span>
                        <!--<span><input type="checkbox"   name="optimisation_temps" value="1" /> {$txt_optimisation_temps}</span>-->
						<input type="submit" name="submitListeCommande" class="submitListeCommandeValid" value="" style="display:none;" />
                        </fieldset>
                    </div>
			{/foreach}

			</form>
		{/if}
	{else}
		<div class="lienhaut">
			<div class="liendroite"><a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
		</div>
		<!-- Choix mode de préparation -->
  
		<form method="post" action="" style="width:40%;margin:auto;">
			<fieldset  style="width:100%;">   
				<legend>{$txt_mode}</legend> 
                {if $modePreparation =='all'}
				<div style="margin:0px auto 10px auto;">{$txt_mode_selection} </div>
				 <textarea name="commandes_select" style="display:none;">{$selection}</textarea> 
					<table style="width:100%;"> 
							<td><input type="radio" class="radio"  name="submitType" value="submitMonoCommandes" {if $type =='monoCommande'}checked{/if} /></td>
							<td>{$txt_mono_commandes} <span style="font-weight:bold;">({$arrayNbPrepas['monoCommande']}   {if $type =='monoCommande'}: {$tpsTotal|seconde_format} {/if} ) </span></td>
							<td><input type="radio" class="radio"  name="submitType" value="submitMultiCommandes"  {if $type =='multiCommandes'}checked{/if} /></td>
							<td>{$txt_multi_commandes} <span style="font-weight:bold;">({$arrayNbPrepas['multiCommandes']} {if $type =='multiCommandes'}: {$tpsTotal|seconde_format} {/if})</span></td>
						</tr>
						<tr>
							<td><input type="radio" class="radio"  name="submitType" value="submitMonoZones" {if $type =='monoZone'}checked{/if} /></td>
							<td>{$txt_mono_zones} <span style="font-weight:bold;">({$arrayNbPrepas['monoZone']} {if $type =='monoZone'}: {$tpsTotal|seconde_format} {/if})</span></td>
							<td><input type="radio" class="radio"  name="submitType" value="submitMultiZones" {if $type =='multiZones'}checked{/if} /></td>
							<td>{$txt_multi_zones} <span style="font-weight:bold;">({$arrayNbPrepas['multiZones']}  {if $type =='multiZones'}: {$tpsTotal|seconde_format} {/if})</span></td>
						</tr>
					
					</table>
					<input type="hidden" name="modePreparation" id="modePreparation" value="{$modePreparation}"/>
					<input type="submit" name="submit" id="sumitForm" value="submit" style="display:none;visible:hidden;"/> 
                    {else}
                    <table style="width:100%;">
                    		{if $modePreparation =='monoCommande'}
							<td>{$txt_mono_commandes} <span style="font-weight:bold;">({$arrayNbPrepas['monoCommande']}   {if $type =='monoCommande'}: {$tpsTotal|seconde_format} {/if} ) </span></td>
                            {else if $modePreparation =='multiCommandes'}
							<td>{$txt_multi_commandes} <span style="font-weight:bold;">({$arrayNbPrepas['multiCommandes']} {if $type =='multiCommandes'}: {$tpsTotal|seconde_format} {/if})</span></td>
                            {else if $modePreparation =='monoZone'}
							<td>{$txt_mono_zones} <span style="font-weight:bold;">({$arrayNbPrepas['monoZone']} {if $type =='monoZone'}: {$tpsTotal|seconde_format} {/if})</span></td>
                            {else if $modePreparation =='multiZones'}
							<td>{$txt_multi_zones} <span style="font-weight:bold;">({$arrayNbPrepas['multiZones']}  {if $type =='multiZones'}: {$tpsTotal|seconde_format} {/if})</span></td>
                            {/if}
						</tr>
					</table>
                    {/if}
			</fieldset>
			 
		</form>
		
		<!-- Affichage préparations -->
		
		{if $type == 'multiCommandes' || $type == 'monoCommande' || $type == 'monoZone' || $type == 'multiZones'}
			<div id="information_bloc">
				<p>				<a href="javascript: void(0)" onclick="window.open('../images/mode_cmd.jpg', 'windowname1', 'width=600, height=400'); return false;">Aide</a></p>
			</div>
			<div style="width:90%;margin:auto;">
				[ <a href="#" onclick="return false;" class="select">{$txt_selection}</a><a href="#" onclick="return false;" class="deSelect" style="display:none;">{$txt_deselection}</a> ]
			</div>
			<form action="{$application_path}affectation/choixutilisateur" method="post">
				<input type="hidden" name="type" value="{$type}" />
                <input type="hidden" name="modePreparation" value="{$modePreparation}"/>
				<input type="hidden" name="etage" value="{$idEtage}" />
				{foreach from=$commandes key=myId item=j}
					<table class="tableau">
						
						<tr>
							<th colspan="9" style="text-align:left;"> {$txt_bon_preparation} n° : {$myId+1}  |  <a class="details_bons" onclick="return false;" id="{$myId}_{$type}_{$idEtage}" href='#'> {$txt_details} </a> </th>
						</tr>
						
						<tr> 
							<th  style="width:5%;"  rowspan="{$j[0]|@count + 1}"> <input type="checkbox" name="bons[]" class="prepa" value="{$myId}" /></th>
							<th > {$txt_num_com}</th>
							<th style="width:20%;"> {$txt_cli_com}</th>
							<th> {$txt_typeLivraison}</th>
							<th> {$txt_dateMaxPrepa}</th>
							<th> {$txt_dateLivraison}</th>
							<th> {$txt_nbRefAAffectees}</th>
							<th> {$txt_nbProdAAffectees}</th>  
							<th></th>  
						</tr>
						
						{foreach from=$j[0] key=myId item=commande}
							<tr>
								 
								<td> {$commande[0]->getCodeCommande()}({$commande[2]})</td>
								<td> {$commande[1]->getPrenom()} {$commande[1]->getNom()} {if {$commande[1]->getNomEntreprise()} != ''}({$commande[1]->getNomEntreprise()}){/if}</td>
								<td> {$commande[0]->getModelivraison()} </td> 
								<td nowrap> {{$commande[0]->getDateLivraison()-$delai_avant_livraison}|date_format:"%d %b &aacute; %H:%M"}</td>
								<td nowrap> {$commande[0]->getDateLivraison()|date_format:"%d %b &aacute; %H:%M"}</td>
								<td> {$commande[4]}</td>
								<td> {$commande[3]}</td> 
								<td> {$commande[5]}</td> 
							</tr>
						{/foreach}
						<tr>
							<td colspan="9" style="text-align:left;font-size:110%;border-top:1px solid black;">{$txt_tempsEstime} : <span class="total_prep_0" style="font-weight:bold;">{$j[1]|seconde_format}</span>.</td>
						</tr>
					</table> 
				{/foreach}
				<div style="width:24%;margin:auto;margin-bottom:10px;">
					<p style="text-align:center;font-size:14px;">{$txt_etape_user2} <span style="font-weight:bold;" id="nb_bons">0</span> {$txt_etape_user3}.</p>
					<input type="submit" style="width:100%;height:20px;" name="submitChoixUtilisateur" id="submitChoixUtilisateur" value="{$txt_etape_user}" />
				</div>
				</form>
		
		
		
		{/if}	
	{/if}
	</div>
{include file="$user_template/common/page_footer.tpl"}