{include file="$user_template/common/page_header_ajax.tpl"}
<div id="info"  style="margin-top:-20px;margin-left:-10px;">
<h3>{$txt_liste_utilisateurs}</h3> <hr/>
{foreach from=$users key=id item=user}- {$user->getPrenom(true)} {$user->getNom(true)}<br/>{/foreach}
</div>
{include file="$user_template/common/page_footer_ajax.tpl"}