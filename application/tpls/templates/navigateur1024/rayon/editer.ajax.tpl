{include file="$user_template/common/page_header_ajax.tpl"}
	
	<div id="info">
		<h3>{$txt_infosRayon}</h3> <hr/>
		<br />
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
        <form method="POST" action="" id="modifier_form" class="form" style="width:100%">
        <label for="libelle">{$txt_Libelle} : </label><input type="text" name="libelle" id="libelle" value="{if isset($form_libelle) & !empty($form_libelle)}{$form_libelle}{/if}"/>
        <label for="pasition_x">{$txt_positionx} : </label><input type="text" name="position_x" id="position_x" value="{if isset($form_position_x) & !empty($form_position_x)}{$form_position_x}{/if}"/>
        <label for="pasition_y">{$txt_positiony} : </label><input type="text" name="position_y" id="position_y" value="{if isset($form_position_y) & !empty($form_position_y)}{$form_position_y}{/if}"/>
        <label for="largeur">{$txt_width} : </label><input type="text" name="largeur" id="largeur" value="{if isset($form_largeur) & !empty($form_largeur)}{$form_largeur}{/if}"/>
        <label for="hauteur">{$txt_height} : </label><input type="text" name="hauteur" id="hauteur" value="{if isset($form_hauteur) & !empty($form_hauteur)}{$form_hauteur}{/if}"/>
        <input type="hidden" name="idRayon" id="idRayon" value="{$rayon->getIdrayon()}" />
        <br/><br/>
		<span style="display:block;clear:both;"> <a href="#" id="modifier_nom" onclick="return false;">{$txt_modifier}</a></span><br />
        <span style="display:block;clear:both;margin-top:20px;"><a href="#" id="close_popup" onClick="return false;">{$txt_fermer}</a></span><br />
        </form>
	</div>
	
	
{include file="$user_template/common/page_footer_ajax.tpl"}