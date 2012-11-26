{include file="$user_template/common/page_header.tpl"}
	<div id="conteneur">
	<h3>Bienvenue {$userPrenom} {$userNom}<br><br>
	Nous sommes le {$currentTimestamp|date_format:"%A %d %B %Y"|utf8_decode}<br></h3>


		<div id="commandes">
				<div style="text-align:center;color:#fff"><strong>{$nbCommandes}</strong> {if $nbCommandes <=1} {$txt_commande} {$txt_est} {else} {$txt_commandes} {$txt_sont} {/if} {$txt_attente}</div>
				<br style="clear:both;">
				
				<table width="100%" border="0">
				<tr>
				<td><div style="text-align:center;margin-bottom:10px;color:#fff"><a href="{$application_path}affectation"><img src="{$application_path}/images/{$user_template}/panier.png" alt="Accéder à vos préparations de commandes"></a><br>Vos commandes à préparer</div></td>
				<td><div style="text-align:center;margin-bottom:10px;color:#fff"><a href="{$application_path}planning" title=""><img src="{$application_path}/images/{$user_template}/calendrier.png" alt="{$txt_acceder_planning}"></a><br>{$txt_acceder_planning}</div></td>
				</tr>
				</table>
								<!--
				<div style="text-align:center;margin-bottom:10px;">
				<br>
				<a href="{$application_path}commande" title="{$txt_lien_liste_co}">{$txt_lien_liste_co}</a> |
					<a href="{$application_path}affectation/manuelle" title="{$txt_lien_affec_co}">{$txt_lien_affec_co}</a>
				</div>

					<br>
				-->
				
				
				
			</div>
			<br style="clear:both;"/>
		</div>
		

{include file="$user_template/common/page_footer.tpl"}