{include file="$user_template/common/page_header.tpl"}
	
	<div id="conteneur">
	{if isset($txt_fail)}
		<h3>{$txt_fail}</h3>
		<div class="lienhaut">
			<div class="liendroite"> <a href="{$application_path}produit">{$txt_retour}</a> </div>
		</div>
	{else}
		
	<h3>{$txt_succes}</h3>
			<div class="lienhaut">
			<div class="liendroite"> <a href="{$application_path}produit">{$txt_retour}</a> </div>
		</div>
	<br />
	<h3>{$txt_rapport}</h3>

		<div id="conteneurRapport">
			<form class="formRapport">
            	{if isset($form_errors) & !empty($form_errors)}
				<div id="conteneurErreur">
					<div id="imageErreur">
						<img src="{$application_path}images/error.png" alt="error">
					</div>
					<div id="texteErreur">
						{$form_errors}
					</div>
					<br style="clear:both"/>
				</div>
				{/if}
				<label class="libelle">{$txt_produitsScannes} :</label> 	<label class="data">{$nbPtsGeolocalises} {if $nbPtsGeolocalises <= 1}{$txt_produit}{else}{$txt_produits}{/if}</label>
				<label class="libelle">{$txt_dans} :</label> 				<label class="data">{$nbEtages} {if $nbEtages <= 1}{$txt_Etage}{else}{$txt_Etages}{/if}</label>
				<label class="libelle">{$txt_contenant} :</label> 			<label class="data">{$nbRayons} {if $nbRayons <= 1}{$txt_Rayon}{else}{$txt_Rayons}{/if}</label>
				{if $finesse_utilisee >= 2}
				<label class="libelle">{$txt_comportant} :</label> 			<label class="data">{$nbSegments} {if $nbSegments <= 1}{$txt_Segment}{else}{$txt_Segments}{/if}</label>
				{if $finesse_utilisee == 3}
				<label class="libelle">{$txt_et} :</label> 					<label class="data">{$nbEtageres} {if $nbEtageres <= 1}{$txt_Etagere}{else}{$txt_Etageres}{/if}</label>
				{/if}
				{/if}
			</form>
			<br style="clear:both"/>
		</div>
			
		
		<div id="conteneurRapportDetails">
			<div id="conteneurProduitsInconnus">
				<h4>{$nombreProduitsInconnus} {if $nombreProduitsInconnus <= 1}{$txt_produitInconnu}{else}{$txt_produitsInconnus}{/if}</h4>
				<table class="tableauRapport" style="width=90%" id="sorter">
					<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_codeEan} </th>
					<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_Etage} </th>
					<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_Rayon} </th>
					
					{if $finesse_utilisee >= 2}
					<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_Segment} </th>
					{if $finesse_utilisee == 3}
					<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_Etagere} </th>
					{/if}
					{/if}
					
					{foreach from=$arrayProduitsInconnus item=produit}
						<tr>
							<td> {$produit['ean']} </td>
							<td> {$produit['etage']} </td>
							<td> {$produit['rayon']} </td>
							{if $finesse_utilisee >= 2}
							<td> {$produit['segment']} </td>
							{if $finesse_utilisee == 3}
							<td> {$produit['etagere']} </td>
							{/if}
							{/if}
						</tr>
					{/foreach}
				</table>
			</div>
			
			<div id="conteneurProduitsNonGeolocalises" >
				<h4>{$nombreProduitsNonScannes} {if $nombreProduitsNonScannes <= 1}{$txt_produitNonScanne}{else}{$txt_produitsNonScannes}{/if}</h4>
				<table class="tableauRapport" id="sorter1">
					<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_codeEan} </th>
					<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_libelle} </th>
					
					{foreach from=$arrayProduitsNonScannes item=produit}
						<tr>
							<td> {$produit[1]} </td>
							<td> {$produit[0]->getLibelle()} </td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
		<br style="clear:both"/>
	{/if}
	</div>
	
	<script type="text/javascript">
		var sorter1=new table.sorter("sorter1");
		sorter1.init("sorter1",0);
		var sorter=new table.sorter("sorter");
		sorter.init("sorter",0);
	</script>
	
{include file="$user_template/common/page_footer.tpl"}