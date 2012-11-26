{include file="$user_template/common/page_header.tpl"}
<div id="conteneur">
{if isset($txt_erreur)}
<div id="conteneurErreur">
	<div id="imageErreur">
		<img src="{$application_path}images/error.png" alt="error">
	</div>
	<div id="texteErreur">
		{$txt_erreur}
	</div>
	<br style="clear:both"/>
</div>
{else}
<h3>{$txt_supprime}</h3>  
{/if}
<a href="{$application_path}/produit" title="">{$txt_retour}</a>
</div>
{include file="$user_template/common/page_footer.tpl"}