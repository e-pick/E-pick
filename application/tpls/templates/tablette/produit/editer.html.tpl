{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		<div id="conteneur_menu">
		<h3>{$txt_editerProduit}</h3>
		<hr /> 
		{if isset($userLevel) & $userLevel>1}
			<div class="lienhaut">
				<div class="liendroite"><a href="{$application_path}produit/supprimer/{$form_idProduit}" title="" onclick="return confirm('{$txt_confirmSuppression} ?')">{$txt_lienSupprimer}</a> | <a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
			</div>
		{/if}

			<form action="" method="POST" class="form" enctype="multipart/form-data">
				
				<fieldset>
					<legend>{$txt_infosProduit} </legend>
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
					<label for="CodeProduit">{$txt_code_produit} :</label><input type="text" name="codeProduit" id="codeProduit" value="{if isset($form_codeProduit) & !empty($form_codeProduit)}{$form_codeProduit}{/if}"/>
					<label for="libelle">{$txt_libelle} :</label><input type="text" name="libelle" id="libelle" value="{if isset($form_libelle) & !empty($form_libelle)}{$form_libelle}{/if}"/>
					<label for="largeur">{$txt_largeur} :</label><input type="text" name="largeur" id="largeur" value="{if isset($form_largeur) & !empty($form_largeur)}{$form_largeur}{/if}"/>
					<label for="hauteur">{$txt_hauteur} :</label><input type="text" name="hauteur" id="hauteur" value="{if isset($form_hauteur) & !empty($form_hauteur)}{$form_hauteur}{/if}"/>
					<label for="profondeur">{$txt_profondeur} :</label><input type="text" name="profondeur" id="profondeur" value="{if isset($form_profondeur) & !empty($form_profondeur)}{$form_profondeur}{/if}"/>
					<label for="uniteMesure">{$txt_uniteMesure} :</label><input type="text" name="uniteMesure" id="uniteMesure" value="{if isset($form_uniteMesure) & !empty($form_uniteMesure)}{$form_uniteMesure}{/if}"/>
					<label for="quantiteParUniteMesure">{$txt_quantiteParUniteMesure} :</label><input type="text" name="quantiteParUniteMesure" id="quantiteParUniteMesure" value="{if isset($form_quantiteParUniteMesure) & !empty($form_quantiteParUniteMesure)}{$form_quantiteParUniteMesure}{/if}"/>
					<label for="poidsBrut">{$txt_poidsBrut} :</label><input type="text" name="poidsBrut" id="poidsBrut" value="{if isset($form_poidsBrut) & !empty($form_poidsBrut)}{$form_poidsBrut}{/if}"/>
					<label for="poidsNet">{$txt_poidsNet} :</label><input type="text" name="poidsNet" id="poidsNet" value="{if isset($form_poidsNet) & !empty($form_poidsNet)}{$form_poidsNet}{/if}"/>
					<label for="estPoidsVariable">{$txt_estPoidsVariable} :</label><select name="estPoidsVariable" id="estPoidsVariable"> 
																						<option value="0" {if isset($form_estPoidsVariable) & !empty($form_estPoidsVariable) & $form_estPoidsVariable == '0'}selected="true"{/if}>{$txt_non}</option>
																						<option value="1" {if isset($form_estPoidsVariable) & !empty($form_estPoidsVariable) & $form_estPoidsVariable == '1'}selected="true"{/if}>{$txt_oui}</option>
																					</select>
					<label for="priorite">{$txt_priorite} :</label>	<select name="priorite" id="priorite">
																		<option value=""  {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == ''} selected="true"{/if}>{$txt_null}</option>
																		<option value="3" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '3'}selected="true"{/if}>{$txt_debut}</option>
																		<option value="2" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '2'}selected="true"{/if}>{$txt_normal}</option>
																		<option value="1" {if isset($form_priorite) & !empty($form_priorite) & $form_priorite == '1'}selected="true"{/if}>{$txt_fin}</option>	
																	</select>
					<label for="stock">{$txt_stock} :</label><input type="text" name="stock" id="stock" value="{if isset($form_stock) & !empty($form_stock)}{$form_stock}{/if}"/>
																
					<br style="clear:both;"/>
					<label for="photo">{$txt_photo}</label><input type="text" name="photo" id="photo" value="{if isset($form_photo) & !empty($form_photo)}{$form_photo}{/if}"/>
					<div style="margin-left:65%;"><img src="{$form_photo}" width="128" height="128" title="{$form_libelle}"></div>
					<br />
					
				</fieldset>
				
				<fieldset>
					<legend>{$txt_eans} </legend>
					<div id="conteneurEans" style="width:100%;">
						<a id="ajouterEan" href="#" style="float:right">{$txt_ajouterEan}</a>
						<br />
						<br />
						{foreach from=$form_arrayEans item=ean}
							<label> ean </label> <input type="text" id="ean" name="ean[]" value="{$ean[1]}" style="width:47%;float:right"/> {if $ean[0] != 0}<a href="#" id="{$ean[0]}" class="supprimerEan" title="{$txt_supprimerEan}"><img src="{$application_path}images/close.png" alt="X" /></a> {/if}
						{foreachelse}
							<div id="conteneurErreur">
								<div id="imageErreur">
									<img src="{$application_path}images/error.png" alt="error">
								</div>
								<div id="texteErreur">
									{$form_noEan}
								</div>
								<br style="clear:both"/>
							</div>
							<label> ean </label><input type="text" name="ean[]"/>
						{/foreach}
						<input type="text" id="eanToDelete" name="eanToDelete" value="" style="display:none"/>
					</div>
				</fieldset>
				
				<input type="submit" id="submitEdition" name="submit" value="{$txt_boutonEnregistrer}"/>
			</form>
			
			<br style="clear:both;"/>
			
		</div>
	</div>
</div>
{include file="$user_template/common/page_footer.tpl"}
