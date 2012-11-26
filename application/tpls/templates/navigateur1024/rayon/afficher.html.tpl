{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		<div id="conteneur_menu">
			<div style="text-align:center;"><h3>{$txt_titre}</h3><hr style="width:80%;"/></div>
			<div class="lienhaut">
				<div class="liendroite"> <a href="{$application_path}rayon/editer/{$rayon->getIdrayon()}">Editer</a> <a href="javascript:history.go(-1)">{$txt_retour}</a> </div>
			</div>
			<div class="boutonGauche">
				<a href="#" class="launch_help_popup" onclick="return false;" title="{$txt_help}"><img src="{$application_path}images/helpm.png" alt="H" /></a>
			</div>
			
			<div class="conteneurRayon" >
				<div class="libelleRayon">
					<a class="blocDisplayRayon" href="{$application_path}rayon/afficher/{$rayon->getIdrayon()}/0/0">{$rayon->getLibelle()}</a>
				</div>
				<div class="contourRayon">
					<div class="conteneurSegments">
						{foreach from=$arraySegments key=myId item=segment}
							<div class="conteneurSegment" style="width:{(100/{$nbSegments})|string_format:"%d"}%;">
								<div class="libelleSegment">
									<a class="blocDisplayRayon" href="{$application_path}rayon/afficher/{$rayon->getIdrayon()}/{$segment[0]->getIdsegment()}/0">{$segment[1]}</a>
								</div>
								<div class="Segment">
									{foreach from=$segment[2] key=myId item=etagere}
										{if $idetagerechoisie != 0}
											{if $idetagerechoisie == $etagere->getIdetagere()}
												{$style = "background:#CE351D;"}
											{else}
												{$style=""}
											{/if}
										{elseif $idsegmentchoisi != 0}
											{if $idsegmentchoisi == $segment[0]->getIdsegment()}
												{$style = "background:#CE351D;"}
											{else}	
												{$style =""}
											{/if}
										{else}
											{$style = "background:#CE351D;"}
										{/if}
										<div class="Etagere" style="height:{(100/{$segment[2]|@count})|string_format:"%d"}%;{$style}">
											<a class="blocDisplayRayon" href="{$application_path}rayon/afficher/{$rayon->getIdrayon()}/{$segment[0]->getIdsegment()}/{$etagere->getIdetagere()}"> </a>
										</div>
									{/foreach}
								</div>
							</div>
						{/foreach}
					</div>
					<br style="clear:both" />
				</div>
				<br style="clear:both" />
			</div>
			
			<div id="conteneurTableauproduits">
				<table class="tableau" id="sorter">
					<th><img src="{$application_path}images/sort.gif" alt="sort" />{$txt_codeEan}</th>
					<th><img src="{$application_path}images/sort.gif" alt="sort" />{$txt_Libelle}</th>
					
					{foreach from=$arrayProduits key=myId item=produit}
						<tr>
							<td>{$produit[1][0]->getEan()}</td>
							<td><a href="{$application_path}produit/afficher/{$produit[0]->getIdProduit()}" title="{$txt_consulter_produit}">{$produit[0]->getLibelle()}</a></td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
	</div>
</div>
	
	<script type="text/javascript">
		var sorter=new table.sorter("sorter");
		sorter.init("sorter",0);
	</script>

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
