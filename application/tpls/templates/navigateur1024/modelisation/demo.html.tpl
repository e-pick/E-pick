{include file="$user_template/common/page_header.tpl"}


 

			<ul id="onglets">
				{foreach from=$arrayEtages key=myId item=i}
					{if $etageSelectionne == $i->getIdEtage()}
						{$cle = $myId}
						<li class="active"><a href="{$application_path}modelisation/demo/{$i->getIdEtage()}"> {$i->getIdEtage()} {$i->getLibelle()} </a></li>
					{else}
						<li><a href="{$application_path}modelisation/demo/{$i->getIdEtage()}"> {$i->getIdEtage()} {$i->getLibelle()} </a></li>
					{/if}
				{/foreach}
			</ul>
 
			 <div id="conteneurEtage">
				
					 
					<form method="post" action="" id="form">
					
					
					<div id="legende" >
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
									<td>{$txt_zone_dep}</td>
								</tr>
								<tr>
									<td><div style="width:15px;height:10px;background-color:#999;border:1px solid black;"></div></td>
									<td>{$txt_zone_fin}</td>
								</tr>
							</table>
						</div>
						
						
						
						<div style="text-align:center;float:right;width:60%;">
							<div style="text-align:center;"><h3>{$txt_action}</h3><hr style="width:80%;"/></div>
							{$txt_distance_px} : {$distancePixel}px {$txt_soit} {$distanceMetre|string_format:"%.2f"}m <br />
							{$txt_tmps_estime} : {$tempsParcours} 
								<input type="submit" id="valider"  name="valider" value="{$txt_tracer}"/>
						</div> 
						<br style="clear:both;">
						
						<div id="debug_test">
						
						</div>
					</div>
					
					
					<!-- AFFICHAGE DES RAYONS et des OBSTACLES MODELISES DANS L'ETAGE -->
					
					 <div class="etage_demo" id="{$arrayEtages[$cle]->getIdetage()}" style="width:{$arrayEtages[$cle]->getlargeur()}px;height:{$arrayEtages[$cle]->gethauteur()}px;margin:auto;">		
					 
						
					 
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
					<div style="clear:both;"></div>
					
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
									<td>{$txt_zone_dep}</td>
								</tr>
								<tr>
									<td><div style="width:15px;height:10px;background-color:#999;border:1px solid black;"></div></td>
									<td>{$txt_zone_fin}</td>
								</tr>
							</table>
						</div>
						
						
						
						<div style="text-align:center;float:right;width:60%;">
							<div style="text-align:center;"><h3>{$txt_action}</h3><hr style="width:80%;"/></div>
							{$txt_distance_px} : {$distancePixel}px {$txt_soit} {$distanceMetre|string_format:"%.2f"}m <br />
							{$txt_tmps_estime} : {$tempsParcours} 
								<textarea style="display:none;" name="segment_select" id="segment_select" rows="10" cols="35"></textarea> <br />
								<input type="submit" id="valider"  name="valider" value="{$txt_tracer}"/>
						</div> 
						<br style="clear:both;">
						
						<div id="debug_test">
						
						</div>
					</div>
					
					</form> 
				</div>
{include file="$user_template/common/page_footer.tpl"}
	
