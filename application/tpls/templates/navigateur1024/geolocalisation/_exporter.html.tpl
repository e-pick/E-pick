{include file="$user_template/common/page_header.tpl"}
	
	<div id="conteneur">
	
	<h3>{$txt_export}</h3>
	
		<div class="lienhaut">
			<div class="liendroite"> <a href="{$application_path}produit">{$txt_retour}</a> </div>
		</div>
	<br />
		<p>{$txt_comm}"{$txt_importer}".</p>
		
		<div id="geolocalisation"><a href="{$application_path}geolocalisation/importer" title="">{$txt_importer}</a></div>	
	 
</div>
{include file="$user_template/common/page_footer.tpl"}