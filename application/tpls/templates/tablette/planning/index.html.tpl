{include file="$user_template/common/page_header.tpl"}
{include file="$user_template/common/menuLeftCommande.tpl"}


	<div id="conteneur_menu">
	 
		<div style="text-align:center;">
			<h3>{$txt_planning} {$timestamp_en_cours|date_format:"%B"|utf8_decode} {$annee_en_cours}</h3>
			<h3>Semaine {$semaine_en_cours}</h3>
			<hr style="width:80%;"/>
		</div>
		<div class="boutonGauche">
		<a href="#" class="launch_help_popup" onclick="return false;" title="{$txt_help}"><img src="{$application_path}images/helpm.png" alt="H" /></a> 
		</div>
		
			
		<div>
			<div style="float:left;width:33%;"><a href="{$application_path}planning/{$semaine_precedente}" title="{$txt_semaine_precedente}">&laquo; {$txt_semaine_precedente}</a></div>
			
			<div style="float:left;width:33%;text-align:center;">
				{if $semaine_en_cours >=  $aujourdhui_semaine}
					<a href="{$application_path}planning/dupliquersemaine/{$jour_semaine[{$planning_journee_fin-($planning_journee_fin-$planning_journee_debut)}]}" onclick="return confirm('{$txt_confirm_duplication}')" title="{$txt_dupliquer_semaine}"><img src="{$application_path}images/duplicate.png" alt="D" /> {$txt_dupliquer_semaine}</a> | 
				{/if}
				<a href="{$application_path}planning/{$aujourdhui}" title="{$txt_aujourdhui}">{$txt_aujourdhui}</a></div>
			
			<div style="float:right;width:33%;text-align:right;"><a href="{$application_path}planning/{$semaine_suivante}" title="{$txt_semaine_suivante}">{$txt_semaine_suivante} &raquo;</a></div>
		</div>
		{$pixel = {({$jour_semaine|@count} * 150)+ 100}}
		<div id="cadre_planning" style="width:{$pixel|string_format:"%d"}px;">	
			<div id="colonne_heure">
				<div id="premiere_case">&nbsp;</div>
				{section name=heure start=$planning_heure_debut loop=$planning_heure_fin}
					<div id="ligne_heure">{$smarty.section.heure.index}:00&nbsp;&nbsp;</div>
				{/section}
			</div>
			{foreach from=$jour_semaine key=id item=jour}  
				<div class="colonne_jour"> 
					<div id="{$jour}" class="date_jour {if $jour|date_format:"%a %d" == $aujourdhui_timestamp|date_format:"%a %d"}today{/if}">
						{$jour|date_format:"%a %d"} 
						{if $jour >= $aujourdhui_timestamp}<a href="#" onclick="return false" class="duplicate" title="{$txt_dupliquer}"><img src="{$application_path}images/duplicate.png" alt="D" /></a>
						{/if}
					</div>
					{section name=heure start=$planning_heure_debut loop=$planning_heure_fin}
						{if $planning_mode_creneau == 0}
							{$nb = $utilisateurs[$jour][$smarty.section.heure.index][0]}
							<div class="ligne_date {if $nb > 0}affecte{/if} {if $jour|date_format:"%a %d" == $aujourdhui_timestamp|date_format:"%a %d"}today{/if}" id="{$jour}{if $smarty.section.heure.index <10}0{/if}{$smarty.section.heure.index}0">
								{if $nb > 0}{$nb}  {if $nb == 1}{$txt_utilisateur}{else}{$txt_utilisateurs} {/if} {/if}
							</div>
						{else}
							{section name=demiheure start=1 loop=3}		
								{$nb = $utilisateurs[$jour][$smarty.section.heure.index][$smarty.section.demiheure.index]}
								<div class="ligne_date {if $nb > 0}affecte{/if} {if $jour|date_format:"%a %d" == $aujourdhui_timestamp|date_format:"%a %d"}today{/if} demiheure" id="{$jour}{if $smarty.section.heure.index <10}0{/if}{$smarty.section.heure.index}{$smarty.section.demiheure.index}">
									{if $nb > 0}{$nb}  {if $nb == 1}{$txt_utilisateur}{else}{$txt_utilisateurs} {/if} {/if}
								</div>
							{/section}
						{/if}
					{/section}
				</div>
			{/foreach}		
			<br style="clear:both;"/>
		</div>
		
	
	</div>


	<div id="help_popup">
		<div id="bar">
			<div id="title">{$txt_help_titre}</div>
			<div id="close"></div>
		</div>
		<div id="content">
			<p>
				{$txt_help_contenu} {$txt_help_contenu} {$txt_help_contenu}<br /><br />
				{$txt_help_contenu} {$txt_help_contenu} {$txt_help_contenu} {$txt_help_contenu} {$txt_help_contenu} {$txt_help_contenu} {$txt_help_contenu}<br />{$txt_help_contenu} {$txt_help_contenu} 
			</p>
		</div>
	</div>
	
{include file="$user_template/common/page_footer.tpl"}
