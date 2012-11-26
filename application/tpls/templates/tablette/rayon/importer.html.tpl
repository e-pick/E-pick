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
				<form action="" enctype="multipart/form-data" method="POST" class="form" >
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
					
					{if isset($form_ok) & !empty($form_ok)}
						<div>
							<div id="imageErreur">
								<img src="{$application_path}images/check.png" alt="OK">
							</div>
							{$form_ok}
							<br style="clear:both"/>
						</div>
					{/if}
					
					<label for="etage">{$txt_etage}</label> 
						<select name="etage" id="etage">
							{foreach from=$arrayEtages key=id item=etage}
								<option value="{$etage->getIdetage()}" {if isset($form_etage) & !empty($form_etage) & $form_etage == {$etage->getIdetage()}}selected="true"{/if}>{$etage->getLibelle()}</option>
							{/foreach}
						</select> 
				
					<label for="fichier">{$txt_fichier}</label> 
						<input type="file" name="fichier" size="30">
					 
					 </fieldset>
					<input type="submit" name="submit" id="submit" value="{$txt_boutonValider}"/>
				</form>	
				<br style="clear:both" />
				</div>
		</div>
	</div>
</div>
{include file="$user_template/common/page_footer.tpl"}