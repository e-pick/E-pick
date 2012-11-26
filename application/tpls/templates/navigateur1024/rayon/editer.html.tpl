{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		<div id="conteneur_menu">
			<div style="text-align:center;"><h3>{$txt_Edition}</h3><hr style="width:80%;"/></div>
			<div class="lienhaut">
				<div class="liendroite"><a href="{$application_path}rayon/supprimer/{$form_idRayon}" title="" onclick="return confirm('{$txt_confirmSuppression} ?')">{$txt_lienSupprimer}</a> | <a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
			</div>
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
			<form method="POST" action="" class="form">
				<fieldset> 
				<legend>{$txt_infosRayon}</legend>
				<label for="libelle">{$txt_Libelle} : </label><input type="text" name="libelle" id="libelle" value="{if isset($form_libelle) & !empty($form_libelle)}{$form_libelle}{/if}"/>
				<label for="localisation">{$txt_localisation} : </label><input type="text" name="localisation" id="localisation" value="{if isset($form_localisation) & !empty($form_localisation)}{$form_localisation}{/if}"/>
				<label for="priorite">{$txt_priorite}:</label>	<select name="priorite" id="priorite">
																		<option value=""  {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == ''} selected="true"{/if}>{$txt_null}</option>
																		<option value="3" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '3'}selected="true"{/if}>{$txt_debut}</option>
																		<option value="2" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '2'}selected="true"{/if}>{$txt_normal}</option>
																		<option value="1" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '1'}selected="true"{/if}>{$txt_fin}</option>	
																	</select>
				</fieldset>
				<input type="submit" name="submit" value="valider"/>
			</form>
			<br style="clear:both" />
		</div>
	</div>
</div>

{include file="$user_template/common/page_footer.tpl"}
