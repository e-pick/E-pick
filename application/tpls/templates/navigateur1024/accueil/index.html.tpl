{include file="$user_template/common/page_header.tpl"}
	<div id="conteneur">
	<h3>Bienvenue {$userPrenom} {$userNom}, nous sommes le {$currentTimestamp|date_format:"%A %d %B %Y"}<br><br>Votre {$txt_tableau_de_bord}</h3>

		<div id="conteneur_stats_users">
			
			<div id="commandes"><h4>{$txt_titre_commandes}</h4><hr/>
				<div style="text-align:center;"><strong>{$nbCommandes}</strong> {if $nbCommandes <=1} {$txt_commande} {$txt_est} {else} {$txt_commandes} {$txt_sont} {/if} {$txt_attente}</div>
				<br style="clear:both;">
				<div style="text-align:center;margin-bottom:10px;">
					<a href="{$application_path}commande" title="{$txt_lien_liste_co}">{$txt_lien_liste_co}</a> <br><br>
					<a href="{$application_path}affectation/manuelle" title="{$txt_lien_affec_co}">{$txt_lien_affec_co}</a>
				</div>
			</div>
			
			<div id="users">
				<h4>{$txt_titre_users}</h4><hr />
				{if {$usersNext} == 0}
					 <div style="text-align:center;color:red">{$txt_attention_no_affect}</div>
				{/if}
				
				{foreach from=$arrayResume key=id item=user} 
				<div class="cadre_user">
					<div class="photo">
						<img src="{$application_path}images/photos/{$user[0]->getPhoto()}" style="width:40px;height:40px;"/>
					</div>
					<div class="info">
						<div class="name">{$user[0]->getPrenom()} {$user[0]->getNom()} <a href="{$application_path}utilisateur/editer/{$user[0]->getIdutilisateur()}" title="{$txt_editer}"><img src="{$application_path}images/edit.png" style="width:10px;height:10px;" alt="Edit" /></a></div>
						<div class="creneau">
						{*$user[1]|@print_r*}
						
						{foreach from=$user[1] item=planning}
							&raquo; {$planning[0]|date_format:"%H:%M"} à {$planning[1]|date_format:"%H:%M"}<br />
							
						{/foreach} 
						</div>
					</div>
					
				<br style="clear:both;"/>
				
				</div>
				{/foreach}
			<!--	<div id="acceder_planning">
					<a href="{$application_path}planning" title="">{$txt_acceder_planning} &raquo;</a>
				</div>
				-->
			
			</div>
				
			<br style="clear:both;"/>
		</div>
		
<!--		<div id="stats">	<h4>{$txt_titre_stats}</h4><hr />
			<br style="clear:both;"/>
		</div>
		
-->		
			<br style="clear:both;"/>

{include file="$user_template/common/page_footer.tpl"}