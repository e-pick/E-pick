{include file="$user_template/common/page_header.tpl"}


	<div id="conteneur">
		<div style="text-align:center;"><h3>{$txt_connexion}</h3><hr style="width:80%;"/></div> 

		
		<div style="text-align:center;margin:40px 0 40px 0;"><img src="{$application_path}images/logo_proxistore.gif" alt=""/></div>
		
		
		
		<form method="post" action="connexion" class="filtre">
			<fieldset>
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
				<legend>{$txt_saisieIdentifiants}</legend>
				<table>
					<tfoot>
						<tr> 
							<td colspan="2">
								<input type="submit" name="submit" value="{$txt_seConnecter}" class="submit"> 
							</td>
						</tr>
					</tfoot>
					<tbody>
						<tr> 
							<th><label for="login" class="isNotNull">{$txt_login}</label></th>
							<td><input type="text" name="login" value="{if isset($form_login) & !empty($form_login)}{$form_login}{/if}"/>   
							</td>
						</tr>
						<tr> 
							<th><label for="password" class="isNotNull">{$txt_password}</label></th>
							<td><input type="password" name="password" value=""/></td>
						</tr>
					</tbody>
				</table>
				
				<a href='{$application_path}utilisateur/recup'>{$txt_recup}</a>
				
			</fieldset>
		</form>	  
	</div>
{include file="$user_template/common/page_footer.tpl"}
