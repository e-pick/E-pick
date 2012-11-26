{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		<div id="conteneur_menu">
				<div style="text-align:center;"><h3>{$txt_titre}</h3><hr style="width:80%;"/></div>
				<div class="lienhaut">
					<div class="liendroite"><a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
				</div>
				
				<form action="" method="POST" class="form" >
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
					<label for="etage">{$txt_etage}</label>
						<select name="etage" id="etage">
							{foreach from=$arrayEtages key=id item=etage}
								<option value="{$etage->getIdetage()}" {if isset($form_etage) & !empty($form_etage) & $form_etage == {$etage->getIdetage()}} selected="true"{/if}>{$etage->getLibelle()}</option>
							{/foreach}
						</select>
					<br style="clear:both" />
					<label for="libelle">{$txt_libelle} : </label><input type="text" name="libelle" id="libelle" value="{if isset($form_libelle) & !empty($form_libelle)}{$form_libelle}{/if}"/><br />
					<div style="float:left;width:49%;"><label>{$txt_couleur} : </label><input type="text" style="display:none;" name="couleur" id="couleur" value="{if isset($form_couleur) & !empty($form_couleur)}{$form_couleur}{else}000000{/if}"/> </div><br />
					<div class="couleur_choisie" style="float:left;background-color:#{if isset($form_couleur) && !empty($form_couleur)}{$form_couleur}{else}000000{/if}"></div><a href="#" id="choisir_couleur" onclick="return false;" style="float:none">{$txt_choixAutreCouleur}</a>
					</fieldset>
					<input type="submit" name="submit" id="submit" value="{$txt_boutonCreer}"/>
				</form>	
				<br style="clear:both" />
		</div>
	</div>
</div>
{include file="$user_template/common/page_footer.tpl"}