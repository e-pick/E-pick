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
					<label for="libelle">{$txt_libelle_etage}</label>
						<input type="text" id="libelle" name="libelleFilter" value="{if isset($form_libelleFilter) & !empty($form_libelleFilter)}{$form_libelleFilter}{/if}"  />

					
				<div class="reset"><a href="{$application_path}etage" title="{$txt_effacer}">{$txt_effacer}</a></div>
				<input type="submit" class="submit" name="submitFilter"  id="submitFilter" value="{$txt_filtrer}" />
				</fieldset>
			</form>
			
			<!-- Affichage des étages -->
			<table class="tableau ligne" id="sorter">
				<tr>
					<th class="nosort" style="width:20px;"></th>
					<th style="width:35%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_etage}</th>
					<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nb_zones}</th>			
					<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nb_rayons}</th>
					<th style="width:20%;"><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nb_produits}</th>
					{if userLevel >=3}<th class="nosort" style="width:20px;"></th>{/if}
					 
				</tr>
				{foreach from=$arrayEtages key=myId item=etage}
					<tr>
						<td>{$myId+1}</td>
						<td>{$etage[0]->getLibelle()}</td>
						<td>{$etage[1]} </td>				
						<td>{$etage[2]}</td>
						<td>{$etage[3]}</td>
						{if $userLevel >=3}<td  style="width:20px;"><a href="{$application_path}etage/editer/{$etage[0]->getIdetage()}" title="{$txt_editer_etage}"><img src="{$application_path}images/edit.png" alt="" style="width:16px;height:16px;"/></a></td>{/if}
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