{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		<div id="conteneur_menu" >
				<div style="text-align:center;"><h3>{$txt_titre}</h3><hr style="width:80%;"/></div>
				<div class="lienhaut">
					<div class="liendroite"><a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
				</div>
				<div style="margin-bottom:40px;">
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
								<option value="{$etage->getIdetage()}" {if isset($form_etage) & !empty($form_etage) & $form_etage == {$etage->getIdetage()}}selected="true"{/if}>{$etage->getLibelle()}</option>
							{/foreach}
						</select> 
				
					<label for="type">{$txt_type}</label> 
						<select name="type" id="type">
							{foreach from=$arrayTypes key=id item=type}
								<option value="{$type}" {if isset($form_type) & !empty($form_type) & $form_type == {$type}}selected="true"{/if}>{$type}</option>
							{/foreach}
						</select> 
				
						
						
						
					<br style="clear:both" />
					<label for="libelle">{$txt_libelle} </label><input type="text" name="libelle" id="libelle" value="{if isset($form_libelle) & !empty($form_libelle)}{$form_libelle}{/if}"/><br />

					<label for="localisation">{$txt_localisation} </label><input type="text" name="localisation" id="localisation" value="{if isset($form_localisation) & !empty($form_localisation)}{$form_localisation}{/if}"/><br />
					
					<label for="hauteur">{$txt_hauteur} </label><input type="text" name="hauteur" id="hauteur" value="{if isset($form_hauteur) & !empty($form_hauteur)}{$form_hauteur}{/if}"/ size="3"><br />
 					<label for="largeur">{$txt_largeur} </label><input type="text" name="largeur" id="largeur" value="{if isset($form_largeur) & !empty($form_largeur)}{$form_largeur}{/if}"/ size="3"><br />  
					 
					 
					 
					 </fieldset>
					<input type="submit" name="submit" id="submit" value="{$txt_boutonCreer}"/>
				</form>	
				<br style="clear:both" />
				</div>
		</div>
	</div>
</div>
{include file="$user_template/common/page_footer.tpl"}