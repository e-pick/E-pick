{include file="$user_template/common/page_header_ajax.tpl"}
	
	<div id="info">
    	<script type="text/javascript" src="{$application_path}js/jscolor/jscolor.js"></script>	
		<h3>{$txt_infosObstacle}</h3> <hr/>
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
        <label for="libelle">{$txt_Couleur} : </label><input type="text" name="couleur" id="couleur" class="color" value="{if isset($form_couleur) & !empty($form_couleur)}{$form_couleur}{/if}"/>
        <input type="hidden" name="idObstacle" id="idObstacle" value="{$obstacle->getIdobstacle()}" />
        <br/><br/>
		<span style="display:block;clear:both;"> <a href="#" id="modifier_couleur" onclick="return false;">{$txt_modifier}</a></span><br />
        <span style="display:block;clear:both;margin-top:20px;"><a href="#" id="close_popup" onClick="return false;">{$txt_fermer}</a></span><br />
        </form>
	</div>
	
	
{include file="$user_template/common/page_footer_ajax.tpl"}