{include file="$user_template/common/page_header_ajax.tpl"}

<table class="tableau" id="sorter" style="width:95%;">
		<tr>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_produit}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_commande}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_qteCommandee}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_prixU}</th>  
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_prix}</th> 
		</tr>

	{foreach from=$lignes_commande key=myId item=ligne}
			{$produit 		= $ligne[0]}
			{$commande		= $ligne[1]}
			{$qte			= $ligne[2]}
			{$prixU		 	= $ligne[3]}
			{$prix 		 	= $prixU * $qte}			
			<tr>
				<td style="text-align:left;{if !$ligne[4]}background:#D72424;{/if}">{$produit}</td>  
				<td style="text-align:left;">{$commande}</td> 
				<td>{$qte}</td>
				<td>{$prixU|string_format:"%.2f"} {$devise}</td>  
				<td>{$prix|string_format:"%.2f"} {$devise}</td>  
				
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

{include file="$user_template/common/page_footer_ajax.tpl"}