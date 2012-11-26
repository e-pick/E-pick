{include file="$user_template/common/page_header_ajax.tpl"}
<form id="form_dupliquer" action="" method="POST">
{$txt_dupliquer_sur} <input type="text" size="4" name="nbjours" id="nbjours" maxlength="3" value="1"/> {$txt_jours} <input type="submit" value="{$txt_valider}"/>
</form>
<div style="text-align:center;"><a href="#" id="close_popup" onclick="return false;">Fermer</a></div>
{include file="$user_template/common/page_footer_ajax.tpl"}