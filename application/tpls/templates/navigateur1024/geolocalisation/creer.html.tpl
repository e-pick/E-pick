{include file="$user_template/common/page_header.tpl"}
<div>
	{include file="$user_template/common/menuLeftProduit.tpl"}
	<div>	
		
		<div id="conteneur_menu">
			<div style="text-align:center;"><h3>{$txt_newGeolocProduit} : {$produit->getLibelle()}</h3><hr style="width:80%;"/></div>
			
			{if isset($userLevel) & $userLevel>1}
				<div class="lienhaut">
					<div class="liendroite"><a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
				</div>
			{/if}
			
			{if isset($noEan)}
				<div id="geolocalisationAlert">
					<p>{$noEan}</p>
				</div>
			{else if isset($noRayons)}
				<div id="geolocalisationAlert">
					<p>{$noRayons}</p>
				</div>
			{else}
			<form method="post" action="" class="form" style="width:80%">
				<fieldset>
					<legend>{$txt_geoloc}</legend>
					
					<label for="etage">{$txt_etage}</label>
						{if isset($arrayEtages) && !empty($arrayEtages)}
							<select name="etageFilter" id="etage">
								<option value=""></option>
								{foreach from=$arrayEtages key=id item=etage}
									<option value="{$etage->getIdetage()}" {if isset($form_etage) & !empty($form_etage) & $form_etage == {$etage->getIdetage()}}selected="true"{/if}>{$etage->getLibelle()}</option>
								{/foreach}
							</select>
						{else}
							<select name="etageFilter" id="etage" value="" disabled></select>
						{/if}
					<br style="clear:both" />
					<label for="zone">{$txt_zone}</label>
						{if isset($arrayZones) && !empty($arrayZones)}
							<select name="zoneFilter" id="zone">
								<option value=""></option>
								{foreach from=$arrayZones key=id item=zone}
									<option value="{$zone->getIdzone()}" {if isset($form_zone) & !empty($form_zone) & $form_zone == {$zone->getIdzone()}}selected="true"{/if}>{$zone->getLibelle()}</option>
								{/foreach}
							</select>
						{else}
							<select name="etageFilter" id="etage" value="" disabled></select>
						{/if}
					<br style="clear:both" />
					<label for="rayon">{$txt_rayon}</label>
						{if isset($arrayRayons) && !empty($arrayRayons)}
							<select name="rayonFilter" id="rayon">
								<option value=""></option>
								{foreach from=$arrayRayons key=id item=rayon}
									<option value="{$rayon->getIdrayon()}" {if isset($form_rayon) & !empty($form_rayon) & $form_rayon == $rayon->getIdrayon()}selected="true"{/if}>{$rayon->getLibelle()}</option>
								{/foreach}
							</select>
						{else}
							<select name="etageFilter" id="etage" value="" disabled></select>
						{/if}
					<br style="clear:both" />	
					
					{if isset($rayonChoisi) && $rayonChoisi && $finesse_utilisee >= $finesse_segment}
						<!-- Affichage du rayon -->
						<div id="geolocalisation"> 							
							<p>{$txt_explication}</p>
						</div>
						
						<div class="conteneurRayon" style="width:95%">
							<div class="libelleRayon">
								{$nouveauRayon->getLibelle()}
							</div>
							<div class="contourRayon">
								{foreach from=$arraySegments key=myId item=segment}
									<div class="conteneurSegment" style="width:{(100/{$nbSegments})|string_format:"%d"}%;">
										<div class="libelleSegment">
											{$segment[1]}
										</div>
										<div class="Segment">
											{foreach from=$segment[2] key=myId item=etagere}
												{if isset($etagereChoisie)}
													{if $etagereChoisie == $etagere->getIdetagere()}
														{$style = "background:#CE351D;"}
														{$class = "Etagere selected"}
													{else}
														{$style = ""}
														{$class = "Etagere"}
													{/if}
												{else}
													{$style = ""}
													{$class = "Etagere"}
												{/if}
												<div class="{$class}" id="{$etagere->getIdetagere()}" style="height:{(100/{$segment[2]|@count})|string_format:"%d"}%;{$style}">
													<a class="blocDisplayRayon" > </a>
												</div>
											{/foreach}
										</div>
									</div>
								{/foreach}
								<br style="clear:both" />
							</div>
							<br style="clear:both" />
						</div>
						
						<input type="text" name ="idEtagere" id="idEtagere" value="" style="display:none" />
					{else}
						{if isset($txt_errors)}
							<div id="geolocalisationAlert">
								<p>{$txt_errors}</p>
							</div>
						{/if}
					{/if}	
					
					<input type="text" name="save" id="save" value="0" style="display:none"/>
					<input type="submit" name="submitGeoloc" id="submitFilter" value="{$txt_boutonEnregistrer}" style="display:none"/>
					<input type="button" name="saveGeoloc" id="saveGeoloc" value="{$txt_boutonEnregistrer}" />
				</fieldset>
			</form>
			{/if}
			<br style="clear:both;"/>

		</div>
	</div>
</div>
{include file="$user_template/common/page_footer.tpl"}
