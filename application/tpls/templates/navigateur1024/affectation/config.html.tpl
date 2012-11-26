{include file="$user_template/common/page_header.tpl"}
{include file="$user_template/common/menuLeftCommande.tpl"}

	<div id="conteneur_menu">
		<h3>{$txt_configAffectation}</h3>
		<hr /> 
		<br />
		<form action="" method="POST" class="form" enctype="multipart/form-data">
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
			
			<fieldset>
				<legend>{$txt_param_affect} </legend>
					
				<label for="nbCommandesMax">{$txt_nbCommandesMax} :</label>
				<input type="text" name="nbCommandesMax" value="{if isset($form_nbCommandesMax) & !empty($form_nbCommandesMax)}{$form_nbCommandesMax}{/if}" />
				
				<label for="tempsMaxPrepa">{$txt_tempsMaxPrepa} :</label>
				<input type="text" name="tempsMaxPrepa" value="{if isset($form_tempsPrepaMax) & !empty($form_tempsPrepaMax)}{$form_tempsPrepaMax}{/if}" />
				
				<label for="nbRefsMax">{$txt_nbRefsMax} :</label>
				<input type="text" name="nbRefsMax" value="{if isset($form_nbRefsMax) & !empty($form_nbRefsMax)}{$form_nbRefsMax}{/if}" />
				
				<label for="nbArticlesMax">{$txt_nbArticlesMax} :</label>
				<input type="text" name="nbArticlesMax" value="{if isset($form_nbArticlesMax) & !empty($form_nbArticlesMax)}{$form_nbArticlesMax}{/if}" />
				
				<label for="poidsMax">{$txt_poidsMax} :</label>
				<input type="text" name="poidsMax" value="{if isset($form_poidsMax) & !empty($form_poidsMax)}{$form_poidsMax}{/if}" />
				
			</fieldset>

			<input type="submit" name="submit" value="{$txt_boutonEnregistrer}"/>
			
		</form>
		<br style="clear:both;"/>
	</div>

{include file="$user_template/common/page_footer.tpl"}