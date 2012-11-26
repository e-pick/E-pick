{include file="$user_template/common/page_header.tpl"}
	
	<div id="conteneur">
		<h3>{$txt_configApplication}</h3>
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
				<legend>{$txt_Parametres_generaux} </legend>
					
				<label for="appliPath">{$txt_path} :</label>
				<input type="text" name="appliPath" value="{if isset($form_appliPath) & !empty($form_appliPath)}{$form_appliPath}{/if}" />

				<label for="appliPrefixe">{$txt_prefix} :</label>
				<input type="text" name="appliPrefixe" value="{if isset($form_appliPrefixe) & !empty($form_appliPrefixe)}{$form_appliPrefixe}{/if}" />
				
                <label for="fuseau">{$txt_fuseau} :</label>
				<input type="text" name="fuseau" value="{if isset($form_fuseau) & !empty($form_fuseau)}{$form_fuseau}{/if}" />
                
                <label for="language">{$txt_language} :</label>
				<select name="language" id="language" style="float:right">
                {foreach from=$arrayLangues item=langue}
                    <option value="{$langue}" {if isset($form_language) & !empty($form_language) & $form_language == {$langue}}selected="true"{/if}>{$langue}</option>		
                {/foreach}
                </select>
                <br/><br/>
                
                <label for="abbreviation_language">{$txt_abbreviation_language} :</label>
				<input type="text" name="abbreviation_language" value="{if isset($form_abbreviation_language) & !empty($form_abbreviation_language)}{$form_abbreviation_language}{/if}" />
                
                <label for="devise">{$txt_devise} :</label>
				<input type="text" name="devise" value="{if isset($form_devise) & !empty($form_devise)}{$form_devise}{/if}" />
                
                <label for="resultatParPage">{$txt_resultat_page} :</label>
				<input type="text" name="resultatParPage" value="{if isset($form_resultatParPage) & !empty($form_resultatParPage)}{$form_resultatParPage}{/if}" />
                
                <label for="emailRapport">{$txt_email_rapport} :</label>
				<input type="text" name="emailRapport" value="{if isset($form_emailRapport) & !empty($form_emailRapport)}{$form_emailRapport}{/if}" />
			</fieldset>
			
			<fieldset>
				<legend>{$txt_Parametres_geolocalisation} </legend>
				
				<label for="nombreEtages">{$txt_nombre_etages} :</label>
				<input type="text" name="nombreEtages" value="{if isset($form_nombreEtages) & !empty($form_nombreEtages)}{$form_nombreEtages}{/if}" />
                  
                <label for="finesse">{$txt_finesse} :</label>  
				<select name="finesse" id="finesse" style="float:right">
                {section name=i start=1 loop=4 step=1}
							{$indice = $smarty.section.i.index}
							<option value="{$indice}" {if isset($form_finesse) & !empty($form_finesse) & $form_finesse == {$indice}}selected="true"{/if}>{$arrayFinesse[$indice]}</option>
						{/section}
                </select>
                <br/><br/>
                
                <label for="largeurEtagere">{$txt_largeur_etagere} :</label>
				<input type="text" name="largeurEtagere" value="{if isset($form_largeurEtagere) & !empty($form_largeurEtagere)}{$form_largeurEtagere}{/if}" />
                
                <label for="delaiAvantLivraison">{$txt_delai_livraison} :</label>
				<input type="text" name="delaiAvantLivraison" value="{if isset($form_delaiAvantLivraison) & !empty($form_delaiAvantLivraison)}{$form_delaiAvantLivraison}{/if}" />
                
                <label for="tempsMoyenAccesProduit">{$txt_tempsMoyenAccesProduit} :</label>
				<input type="text" name="tempsMoyenAccesProduit" value="{if isset($form_tempsMoyenAccesProduit) & !empty($form_tempsMoyenAccesProduit)}{$form_tempsMoyenAccesProduit}{/if}" />
				
			</fieldset>
			
			<input type="submit" name="submit" value="{$txt_boutonEnregistrer}"/>
			
		</form>
		<br style="clear:both;"/>
	</div>

{include file="$user_template/common/page_footer.tpl"}