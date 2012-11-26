{include file="$user_template/common/page_header.tpl"}
<div id="conteneur">
	<div style="text-align:center;"><h3>{$txt_traduction}</h3><hr style="width:80%;"/></div> 
	<br /><br />
	
	
	<form method="post">
		<table class="tableau">
		<tr>
			<th style="border-style: inset; border-width: 1px;border-color: gray; width:37%">{$txt_messages}</th>
			<th style="border-style: inset; border-width: 1px;border-color: gray; width:37%">{$txt_traduction}</th>
			<th style="border-style: inset; border-width: 1px;border-color: gray; width:26%">{$txt_references}</th>
		</tr>
		{foreach from=$messages key=msgid item=mess}
			<tr style="height:45px;">
				<td style="border-style: inset; border-width: 1px;border-color: gray; width:37%">{$msgid}</td>   
				<td style="border-style: inset; border-width: 1px;border-color: gray; width:37%">
				{$nbChar = $msgid|count_characters:true}
				{if $nbChar <= 83}
					<input type="text" size="83" name="messages['{$msgid|escape:'htmlall'}']" value="{$mess['msgstr']}"/>
				{else}
					<textarea rows={math equation="ceil(x/83)" x=$nbChar} cols=80 name="messages['{$msgid|escape:'htmlall'}']">{$mess['msgstr']}</textarea>
				{/if}
				</td>   
				<td style="border-style: inset; border-width: 1px;border-color: gray; width:26%;">
				<div style="max-height:45px;overflow:auto;display:block;">
				<div style="display:table-cell;">
				{foreach from=$mess['references'] item=ref}
					{$ref}<br/>
				{/foreach}
				</div>
				</div>
				</td>   
			</tr>
		{/foreach}
		</table>
		<input type="submit" name="enregistrer" value="{$txt_enregistrer}"/>
	</form>
<br />
</div>
{include file="$user_template/common/page_footer.tpl"}