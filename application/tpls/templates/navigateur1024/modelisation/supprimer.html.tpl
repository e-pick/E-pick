{include file="$user_template/common/page_header.tpl"}
<div id="conteneur">
		<h3>{$txt_reinit_modelisation}</h3>
		<hr /> 
		<br />
				<ul>
				{foreach from=$arrayEtages key=id item=libelle}
                    <li>{$txt_supprimer_modelisation} : <a href="{$application_path}modelisation/supprimeretage/{$id}" onclick="return doubleConfirm();">{$libelle}</a></li>
                {/foreach}
                </ul>
</div>
{include file="$user_template/common/page_footer.tpl"}