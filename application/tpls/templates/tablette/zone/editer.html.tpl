{include file="$user_template/common/page_header.tpl"}

<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
		<div id="conteneur_menu">
			
			<div style="text-align:center;"><h3>{$txt_titre}</h3><hr style="width:80%;"/></div>
			<div class="lienhaut">
				<div class="liendroite">{if $form_libelle != $magasin}<a href="{$application_path}zone/supprimer/{$form_id}" title="" onclick="return confirm('{$txt_confirmSuppression} \'{$form_libelle}\' ?')">{$txt_lienSupprimer}</a> | {/if}<a href="javascript:history.go(-1)">{$txt_retour}</a></div><br />
			</div>
			
			<form action="" method="POST" class="form">
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
					
					{if isset($form_libelle_display) && !empty($form_libelle_display)} <label>{$txt_libelle} 		: </label><input type="text" name="libelle" value="{if isset($form_libelle) & !empty($form_libelle)}{$form_libelle}{/if}"/>{/if}
					<label for="priorite">{$txt_priorite}:</label>	<select name="priorite" id="priorite">
																		<option value=""  {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == ''} selected="true"{/if}>{$txt_null}</option>
																		<option value="3" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '3'}selected="true"{/if}>{$txt_debut}</option>
																		<option value="2" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '2'}selected="true"{/if}>{$txt_normal}</option>
																		<option value="1" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '1'}selected="true"{/if}>{$txt_fin}</option>	
																	</select><br style="clear:both"/>
					<div style="float:left;width:49%;"><label>{$txt_couleur} : </label><input type="text" style="display:none;" name="couleur" id="couleur" value="{if isset($form_couleur) & !empty($form_couleur)}{$form_couleur}{else}000000{/if}"/> </div>
					<div class="couleur_choisie" style="background-color:#{if isset($form_couleur) && !empty($form_couleur)}{$form_couleur}{else}000000{/if}"></div><a href="#" id="choisir_couleur" onclick="return false;" style="float:none">{$txt_choixAutreCouleur}</a>		
				</fieldset>  
				<input type="submit" name="submit" id="submit" value="{$txt_boutonEditer}"/>
			</form>
			<br style="clear:both" />
		</div>
</div>
	
{include file="$user_template/common/page_footer.tpl"}