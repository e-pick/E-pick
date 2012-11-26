{include file="$user_template/common/page_header_ajax.tpl"}
	
	<div id="info">
		<h3>{$txt_rayon} : {$rayon->getLibelle()}</h3> <hr/>
		{$txt_1} '{$rayon->getZone()->getLibelle()}' {if $finesse_utilisee == $finesse_etagere} {$txt_2} {$nbSegments} {if $nbSegments == 1}{$txt_segment}{else}{$txt_segments}{/if} {/if}.<br /> 
		{$txt_3} {$nbPdts} {if $nbPdts <= 1} {$txt_produit} {else} {$txt_produits} {/if} {if $nbPdtsInconnus != 0} {$txt_dont} {$nbPdtsInconnus} {if $nbPdtsInconnus == 1} {$txt_est} {$txt_inconnu} {else} {$txt_sont} {$txt_inconnus}{/if} {/if}.<br /><br />		
			{if $nbPdts != 0}
				{$txt_4}<br />
				<ul>
					{foreach from=$arrayRandom item=produit}
						<li>{$produit->getLibelle()} </li>
					{/foreach}
				</ul>
			{/if}
		
		<br />
		<span style="display:block;"><a href="#" id="demodeliser" libelle="{$rayon->getLibelle()}" onclick="return false;">{$txt_annuler}</a> <br /><br /> <a href="#" id="close_popup" onclick="return false;">{$txt_fermer}</a></span><br />
	</div>
	
	
{include file="$user_template/common/page_footer_ajax.tpl"}