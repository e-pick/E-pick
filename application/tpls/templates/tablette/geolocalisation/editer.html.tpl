{include file="$user_template/common/page_header.tpl"}
<div>
	{if $userLevel > 1}
		{include file="$user_template/common/menuLeftProduit.tpl"}
		<div id="conteneur_menu">	
	{else}
		<div id="conteneur">
	{/if}
		{if !isset($noGeoloc)}
			<ul id="onglets">
				{foreach from=$arrayGeolocs key=myId item=i}
					{if $geolocSelectionnee == $i->getIdetagere()}
						<li class="active"><a href="{$application_path}geolocalisation/editer/{$produit->getIdProduit()}/{$i->getIdetagere()}">{$txt_geoloc} {$myId+1}</a></li>
					{else}
						<li><a href="{$application_path}geolocalisation/editer/{$produit->getIdProduit()}/{$i->getIdetagere()}">{$txt_geoloc} {$myId+1}</a></li>
					{/if}
				{/foreach}
			</ul>
		{/if}	
		
		<div id="conteneurEtage">
			<div style="text-align:center;"><h3>{$txt_geolocProduit} : {$produit->getLibelle()}</h3><hr style="width:80%;"/></div>
			{if isset($userLevel) & $userLevel>1}
			<div class="lienhaut">
				{if !isset($noGeoloc)}<a href="{$application_path}geolocalisation/supprimer/{$produit->getIdProduit()}/{$geolocSelectionnee}" onclick="return confirm('{$txt_confirmSuppression}?')" title="">{$txt_supprimer}</a>{/if}
				<div class="liendroite">
					<a href="{$application_path}geolocalisation/creer/{$produit->getIdProduit()}">{$txt_ajouterGeoloc}</a> | 
					<a href="javascript:history.go(-1)" title="">{$txt_retour}</a>
				</div>
			</div>
			{else}
				<div class="lienhaut">
					<div class="liendroite">
						<a href="javascript:history.go(-1)" title="">{$txt_retour}</a>
					</div>
				</div>
			{/if}
			{if isset($noGeoloc) && $noGeoloc}
				<div id="geolocalisationAlert">
					<p>{$noGeoloc}</p>
				</div>
				
			{else}
				{if $userLevel > 1}
				<form method="post" action="" class="form" style="width:80%">
					<fieldset>
						<legend>{$txt_geoloc}</legend>
						
						<label for="etage">{$txt_etage}</label>
							<select name="etageFilter" id="etage">
								<option value=""></option>
								{foreach from=$arrayEtages key=id item=etage}
									<option value="{$etage->getIdetage()}" {if isset($form_etage) & !empty($form_etage) & $form_etage == {$etage->getIdetage()}}selected="true"{/if}>{$etage->getLibelle()}</option>
								{/foreach}
							</select>
						<br style="clear:both" />
						<label for="zone">{$txt_zone}</label>
							<select name="zoneFilter" id="zone">
								<option value=""></option>
								{foreach from=$arrayZones key=id item=zone}
									<option value="{$zone->getIdzone()}" {if isset($form_zone) & !empty($form_zone) & $form_zone == {$zone->getIdzone()}}selected="true"{/if}>{$zone->getLibelle()}</option>
								{/foreach}
							</select>
						<br style="clear:both" />
						<label for="rayon">{$txt_rayon}</label>
							<select name="rayonFilter" id="rayon">
								<option value=""></option>
								{foreach from=$arrayRayons key=id item=rayon}
									<option value="{$rayon->getIdrayon()}" {if isset($form_rayon) & !empty($form_rayon) & $form_rayon == $rayon->getIdrayon()}selected="true"{/if}>{$rayon->getLibelle()}</option>
								{/foreach}
							</select>
						<br style="clear:both" />	
						
						<br style="clear:both"/>
				{/if}		

						{if isset($rayonChoisi) && $rayonChoisi && $finesse_utilisee >= $finesse_segment}
							<!-- Affichage du rayon -->
							{if $userLevel > 1}
							<div id="geolocalisation"> 							
								<p>{$txt_explication}</p>
							</div>
							{/if}
							
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
						{if $userLevel > 1}
						<input type="text" name="save" id="save" value="0" style="display:none"/>
						<input type="submit" name="submitGeoloc" id="submitFilter" value="{$txt_boutonEnregistrer}" style="display:none"/>
						<input type="button" name="saveGeoloc" id="saveGeoloc" value="{$txt_boutonEnregistrer}" />
					</fieldset>
				</form>
						{/if}
			{/if}
			<br style="clear:both;"/>

		</div>
	</div>
</div>
{include file="$user_template/common/page_footer.tpl"}
