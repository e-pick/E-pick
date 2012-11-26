{include file="$user_template/common/page_header.tpl"}


	<div id="conteneur">
		<div style="text-align:center;"><h3>{$txt_recup}</h3><hr style="width:80%;"/></div> 
		<div class="lienhaut">
				<div class="liendroite"><a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
		</div>
		<form method="post" action="recup" class="filtre">
		
			<fieldset>
				{$txt_info}
			
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
				<legend>{$txt_saisieLogin}</legend>
				<table>
					<tfoot>
						<tr> 
							<td colspan="2">
								<input type="submit" name="submit" value="{$txt_valider}" class="submit"> 
							</td>
						</tr>
					</tfoot>
					<tbody>
						<tr> 
							<th><label for="login">{$txt_login}</label></th>
							<td><input type="text" name="login" value="{if isset($form_login) & !empty($form_login)}{$form_login}{/if}"/>   
							</td>
						</tr>
					</tbody>
				</table>
							
			</fieldset>
			
		</form>	

	</div>
{include file="$user_template/common/page_footer.tpl"}
