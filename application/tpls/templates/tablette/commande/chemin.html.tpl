{include file="$user_template/common/page_header.tpl"}

	<div id="conteneur">
		<div style="text-align:center;"><h3>{$txt_titre}</h3><hr style="width:80%;"/></div>
	
		<div class="lienhaut">
			<div class="liendroite"><a href="javascript:history.go(-1)" title="">{$txt_retour}</a></div>
		</div>
		<br />
		
		<div id="legende" style="margin-top:20px;" >
			<div style="float:left;width:37%;border-right:1px solid black;padding:1px;">
				<div style="text-align:center;"><h3>{$txt_legende}</h3><hr style="width:80%;"/></div>
				<table>
					<tr>
						<td><div style="width:15px;height:10px;background-color:#ba2929;border:1px solid black;"></div></td>
						<td>{$txt_obstacle}</td>
					</tr>
					<tr>
						<td><div style="width:15px;height:10px;background-color:#288BD6;border:1px solid black;"></div></td>
						<td>{$txt_caisse}</td>
					</tr>
					<tr>
						<td><div style="width:15px;height:10px;background-color:#DDD;border:1px solid black;"></div></td>
						<td>{$txt_rayon}</td>
					</tr>
					<tr>
						<td><div style="width:15px;height:10px;background-color:#000;border:1px solid black;"></div></td>
						<td>{$txt_zoneDepart}</td>
					</tr>
				</table>
			</div>
			
			
			
			<div style="text-align:center;float:right;width:60%;">
				<div style="text-align:center;"><h3>{$txt_infos}</h3><hr style="width:80%;"/></div>
				{$txt_distanceParcourue} : {$distancePixel}px {$txt_soit} {$distanceMetre|string_format:"%.2f"}m <br />
				{$txt_tempsEstime} : {$tempsParcours}
					<textarea style="display:none;" name="segment_select" id="segment_select" rows="10" cols="35"></textarea> <br />
				{if isset($etagesCommande) && $etagesCommande|@count > 1}
					<div id="conteneurErreur">
						<div id="imageErreur">
							<img src="{$application_path}images/error.png" alt="error">
						</div>
						<div id="texteErreur">
							{$txt_alert}
						</div>
						<br style="clear:both"/>
					</div>
				{/if}
			</div> 
			<br style="clear:both;">
			
			<div id="debug_test">
			
			</div>
			
		</div>	
		  
		{if isset($etagesCommande) && $etagesCommande|@count > 1 && {$type == 'commande'}}
			<div style="text-align:center;margin-bottom:20px"> 
				- 
				{foreach from=$etagesCommande key=myId item=i}
					<a href="{$application_path}commande/chemin/commande-{$commande->getIdcommande()}/{$i->getIdetage()}"> {$i->getLibelle()} </a> - 
				{/foreach}
			</div>
		{/if}
		
		<!-- AFFICHAGE DES RAYONS et des OBSTACLES MODELISES DANS L'ETAGE -->
		<div style="width:{$currentEtage->getlargeur()}px;margin:auto;"> 
			<div style="width:10%; border: 1px solid; border-bottom : none; text-align:center; ">
				{$currentEtage->getLibelle()}
			</div>
		</div>
		<div class="etage_demo" id="{$currentEtage->getIdetage()}" style="width:{$currentEtage->getlargeur()}px;height:{$currentEtage->gethauteur()}px;margin:auto;"> 
			<!-- Affichage du point de départ  -->
			<div class="point_demo" id="zone_depart_demo" style="position:absolute;top:{$pt_depart_top}px;left:{$pt_depart_left}px;"></div>
			<!-- Affichage du point d'arrive  -->
			<div class="point_demo" id="zone_arrive_demo" style="position:absolute;top:{$pt_arrive_top}px;left:{$pt_arrive_left}px;"></div>
				
			
			<!-- Affichage des obstacles  -->
			{foreach from=$arrayObstacles key=myId item=i}  
				{if {$i->getType()} == "caisse"}
					<div class="caisse_demo" id="{$i->getIdObstacle()}" style="position:absolute;top:{$i->getPosition_top()}px;left:{$i->getPosition_left()}px;width:{$i->getLargeur()}px;height:{$i->getHauteur()}px;">CAISSE</div>
				{else}
					<div class="obstacle_demo" id="{$i->getIdObstacle()}" style="position:absolute;top:{$i->getPosition_top()}px;left:{$i->getPosition_left()}px;width:{$i->getLargeur()}px;height:{$i->getHauteur()}px;"></div>
				{/if}
			{/foreach}
			
			<!-- Affichage des rayons  -->
			{foreach from=$arrayRayons key=myId item=i} 
				{if {$i[0]->getPosition_top()} != "-1" && {$i[0]->getPosition_left()} != "-1"}
					{if $i[0]->getType() == 'classique'}
						{$top 		= $i[0]->getPosition_top()}
						{$left 		= $i[0]->getPosition_left()} 
						{$nbSegment = $i[1]}
						{if $finesse_utilisee >= $finesse_rayon}
							{$width 	= $i[0]->getLargeur()}
							{$height 	= $i[0]->getHauteur()}
							{$class		= "rayon resize"}
						{else}
							{$width 	= {math equation="x * y" x=$i[1] y=$largeursegment}}
							{$height 	= $hauteursegment}
							{$class		= "rayon"}
						{/if}
						{$border 	= "border:1px solid #666;border-bottom:2px dashed #{$i[2]}"} 
						{$type 		= "classique"}
					{else}								
						{$top 		= $i[0]->getPosition_top()}
						{$left 		= $i[0]->getPosition_left()} 
						{$width 	= $i[0]->getLargeur()}
						{$height 	= $i[0]->getHauteur()}
						{$nbSegment = 1}
						{$border 	= "border:2px dashed #{$i[2]}"} 								
						{$type 		= "vrac"}
						{$class		= "rayon vrac"}
					{/if}
					
					<div id="{$i[0]->getIdrayon()}" class="rayon_demo" sens="{$i[0]->getSens()}" type_ray="{$type}" nb_segment="{$nbSegment}" largeur_ray="{$width}" hauteur_ray="{$height}"  top_deb="{$top}" left_deb="{$left}" style="top:{$top}px;left:{$left}px;width:{$width}px;height:{$height}px;{$border};">
					<a href="#" onclick="return false;" style="display:block;width:100%;height:100%;"></a>
						
					</div>
				{/if}
			{/foreach}
		</div>		

		<div>
			 <!-- tracage des traits  -->
			 <textarea  id="point_res" style="display:none;"  rows="10" cols="25">{foreach from=$listeDePointsARelier key=myId item=i}{$i[0]},{$i[1]},{$i[2]};{/foreach}</textarea> 
		</div>
		
		<div style="width:70%; margin : 50px auto 50px auto;"> 
			<div id="inaccessibles" style="padding: 20px 0 0 20px;">
				{$txt_produitsInaccessibles} : <br />
				{$nbInaccessibles = $inaccessibles|count}
				<span style="font-style:italic">
					{if $nbInaccessibles == 0}
						{$txt_aucun}.
					{else if $nbInaccessibles == 1}
						{$txt_total} : {$nbInaccessibles} {$txt_produit}.
					{else}
						{$txt_total} : {$nbInaccessibles} {$txt_produits}.
					{/if}
				</span>
				<ul>
				{foreach from=$inaccessibles item=produit}
					<li>{$produit[0]->getLibelle()} ({$produit[1]->getLibelle()})</li>
				{foreachelse}
					
				{/foreach}
				</ul>
			</div>

			<div id="nonModelises" style="padding: 20px 0 0 20px">
				{$txt_produitsNonModelises} : <br />
				{$nbPdtsNonModelises = $nonModelises|count}
				
				<span style="font-style:italic">
					{if $nbPdtsNonModelises == 0}
						{$txt_aucun}.
					{else if $nbPdtsNonModelises == 1}
						{$txt_total} : {$nbPdtsNonModelises} {$txt_produit}.
					{else}
						{$txt_total} : {$nbPdtsNonModelises} {$txt_produits}.
					{/if}
				</span>
				<ul>
				{foreach from=$nonModelises item=produit}
					<li>{$produit->getLibelle()}</li>
				{foreachelse}
					
				{/foreach}
				</ul>
			</div>
		</div>
		
		<br style="clear:both" />
		<br />
	</div>
	
	
{include file="$user_template/common/page_footer.tpl"}