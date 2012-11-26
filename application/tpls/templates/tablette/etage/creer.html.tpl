{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		<div id="conteneur_menu">
			<div style="text-align:center;"><h3>{$txt_titre}</h3><hr style="width:80%;"/></div>
			<div class="lienhaut">
				<div class="liendroite"> <a href="javascript:history.go(-1)">{$txt_retour}</a></div>
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
					
					<label>{$txt_libelle} 		: </label><input type="text" name="libelle" value="{if isset($form_libelle) & !empty($form_libelle)}{$form_libelle}{/if}"/>
					
					</fieldset>  
				<input type="submit" name="submit" id="submit" value="{$txt_boutonCreer}"/>
			</form>
			<br style="clear:both" />
		</div>
	</div>
</div>
{include file="$user_template/common/page_footer.tpl"}