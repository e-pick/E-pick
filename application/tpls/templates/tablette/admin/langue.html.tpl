{include file="$user_template/common/page_header.tpl"}
<div id="conteneur">
	<div style="text-align:center;"><h3>{$txt_langue}</h3><hr style="width:80%;"/></div> 
	<br /><br />

	<form method="post">
	<div style="width:300px;margin:auto;margin-top:50px;">
	<fieldset>
		<label for="langue">{$txt_choix_langue}</label>
	<select name="langue" id="langue" style="float:right">
	{foreach from=$arrayLangues item=langue}
		<option value="{$langue}">{$langue}</option>		
	{/foreach}
	</select>
	<br/><br/>
	<input type="submit" name="editer" style="float:right" value="{$txt_editer}"/>
	</fieldset>
	</div>
	</form>
	<form method="post">
	<div style="width:300px;margin:auto;margin-top:50px;">
	<fieldset>
		<label for="new_langue">{$txt_new}</label>
	<input type="text" name="new_langue" id="new_langue" style="float:right"/>
	<br/><br/>
	<input type="submit" name="ajouter" style="float:right" value="{$txt_ajouter}"/>
	</fieldset>
	</div>
	</form>
	<br />
</div>
{include file="$user_template/common/page_footer.tpl"}