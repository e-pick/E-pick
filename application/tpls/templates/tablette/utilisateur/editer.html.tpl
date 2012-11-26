{include file="$user_template/common/page_header.tpl"}
<div id="conteneur">
<h3>{$txt_editerUtilisateurs}</h3>
<hr /> 
<div class="lienhaut">
	<div class="liendroite">{if isset($userId) && $userId != $form_id && ($userLevel == 3 || $form_level<3)}<a href="{$application_path}utilisateur/supprimer/{$form_id}" title="" onclick="return confirm('{$txt_confirmSuppression} {$form_login}?')">{$txt_lienSupprimer}</a> | {/if}<a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
</div>
<form action="{$form_id}" method="POST" class="form" enctype="multipart/form-data">

<fieldset>
	<legend>Informations	 </legend>
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
	<label for="prenom">{$txt_prenom} :</label> <input type="text" name="prenom" id="prenom" value="{if isset($form_prenom) & !empty($form_prenom)}{$form_prenom}{/if}"/>
	<label for="nom">{$txt_nom} : </label><input type="text" name="nom" id="nom" value="{if isset($form_nom) & !empty($form_nom)}{$form_nom}{/if}"/>
	<label for="email">{$txt_email} : </label><input type="text" name="email" id="email" value="{if isset($form_email) & !empty($form_email)}{$form_email}{/if}"/>
	<label for="login">{$txt_login} : </label><input type="text" AUTOCOMPLETE=OFF id="login" name="login" value="{if isset($form_login) & !empty($form_login)}{$form_login}{/if}"/>
	 
	{if isset($userLevel) & $userLevel>=2}
	<label for="level">{$txt_fonction} : </label><select name="level" id="level"> 
				<option value="1" {if isset($form_level) & !empty($form_level) & $form_level == '1'}selected="true"{/if}>{$txt_preparateur}</option>
				<option value="2" {if isset($form_level) & !empty($form_level) & $form_level == '2'}selected="true"{/if}>{$txt_superviseur}</option>
				{if isset($userLevel) & $userLevel>=3}
				<option value="3" {if isset($form_level) & !empty($form_level) & $form_level == '3'}selected="true"{/if}>{$txt_administrateur}</option>
				{/if}
			</select>
	{/if}
	<label for="template">{$txt_templateUtilise} : </label>
	
		<select name="template" id="template"> 
			{foreach from=$arrayTemplate key=myId item=i}
				<option value="{$i}" {if isset($form_template) & !empty($form_template) & $form_template == $i}selected="true"{/if}>{$i}</option> 
			{/foreach}
		</select>
			<br style="clear:both;"/>
			<div style="width:100%;">
				<div style="margin-left:65%;"><img src="{$application_path}images/photos/{$form_photo}" title="{$form_prenom} {$form_nom}"></div>
				<div style="margin-left:55%;">
							<input type="file" size="20" name="photo" style="float:left;">
							<input type="checkbox" name="supprimerPhoto" style="margin-left:15px;float:left;width:10px;"> {$txt_supprimerPhoto}
				</div>
			</div>
</fieldset>
<fieldset>
	<legend> Mot de passe</legend>
	<label for="password">{$txt_password} : </label><input type="password" AUTOCOMPLETE=OFF name="password" id="password" value=""/><br />
	<label for="password_conf">{$txt_confirmPassword} : </label><input type="password" name="password_conf" id="password_conf" value=""/><br />
</fieldset>

<input type="submit" name="submit" value="{$txt_boutonEnregistrer}"/>
</form>
<br style="clear:both;"/>
</div>
{include file="$user_template/common/page_footer.tpl"}