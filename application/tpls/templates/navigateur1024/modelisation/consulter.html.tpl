{if !isset($save)}
{include file="$user_template/common/page_header.tpl"}

					<div id="legende" style="">
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
								<tr>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td><div style="width:27px;height:5px;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;"></div></td>
									<td>{$txt_1_metre}</td>
								</tr>
								<tr>
									<td>{$txt_superficie}</td>
									<td id="superficie"></td>
								</tr>
							</table>
						</div>
						
						
						
						<div style="text-align:center;float:right;width:60%;">
							<div style="text-align:center;"><h3>{$txt_action}</h3><hr style="width:80%;"/></div>
							
							<!-- ne pas toucher au identifiant et au nom des attributs de ce formulaire -->
							<form method="post" action="" id="form">
								<textarea  style="display:none"  name="block_position" id="block_position" rows="10"></textarea>
								<textarea  style="display:none"  name="obstacle_suppr" id="obstacle_suppr" rows="10"></textarea> 
								<input  style="display:none;"  type="submit" id="valider"  name="valider" value="Sauvegarder"/>
							</form> 
							
							<!-- liste des actions possibles -->
							<center>
							<table border="0">
								<tr>
									<td valign="left">
										<ul type="square">
											<li><a href="#" onclick="return false;"  id="ajouter_obstacle">{$txt_ajouter_obs}</a>  <br /></li>
											<li><a href="#save_box"  class="boxy" title="Sauvegarde"  id="sauvegarder">{$txt_sauvegarder} !</a>   <br /></li>
											<li><a href="{$application_path}modelisation/demo/{$etageSelectionne}">{$txt_demo} !</a><br /><br /></li>
										</ul>
											
										<!--<ul type="square">	
											<li><a href="{$application_path}modelisation/supprimer/{$etageSelectionne}" class="reinit" onclick="return confirm('{$txt_conf_reinit}');">{$txt_reinit} !</a></li>							</ul>-->
							
							</td></tr></table>
							
						</div> 
						<br style="clear:both;">
						
						<div id="save_box" style="display:none;width:400px;height:220px;">
							<h2 style="text-align:center;">{$txt_popup_sauve}</h2>
							<h4 style="text-align:center;">{$txt_popup_time}</h4>
							 <p>
								<ul id="etape">
								<li id="op_1">{$txt_popup_op1}</<li> 
								<li id="op_2">{$txt_popup_op2}</<li>  
								</ul>
							 </p>
							
							<div id="load" style="text-align:center;margin:auto;"></div>
							
							<div id="close" style="text-align:center;margin:auto;"></div>
						</div>
						<div id="debug">
						 debug
						</div>
					</div>
			<div style="clear:both;"></div>
			<div style="text-align:center;height:12px;" id="save_auto" ><p style="text-align:center;"></p></div>
			<ul id="onglets">
				{foreach from=$arrayEtages key=myId item=i}
					{if $etageSelectionne == $i->getIdEtage()}
						{$cle = $myId}
						<li class="active"><a href="{$application_path}modelisation/consulter/{$i->getIdEtage()}"> {$i->getIdEtage()} {$i->getLibelle()} </a></li>
					{else}
						<li><a href="{$application_path}modelisation/consulter/{$i->getIdEtage()}"> {$i->getIdEtage()} {$i->getLibelle()} </a></li>
					{/if}
				{/foreach}
			</ul>

			
			 <div id="conteneurEtage">

				
					<!-- AFFICHAGE DES RAYONS à MODELISER -->
				
					<div id="rayons" style="height:100%;">
						
						{foreach from=$arrayRayons key=myId item=i}  
							{if {$i[0]->getPosition_top()} == "-1" && {$i[0]->getPosition_left()} == "-1"}
								{if $i[0]->getType() == 'classique'}
									
									{if $finesse_utilisee == $finesse_rayon}									
										<div id="id_{$i[0]->getIdrayon()}" class="rayonToTransfer" type_ray="classique"  style="width:100px;border:1px solid #666;border-bottom:2px dashed #{$i[2]}">
									{else}
										   {if $i[0]->getLargeur()> 0}
											 <div id="id_{$i[0]->getIdrayon()}" class="rayonToTransfer" type_ray="classique"  style="width:{$i[0]->getLargeur()}px; height:{$i[0]->getHauteur()}px; border:1px solid #666;border-bottom:2px dashed #{$i[2]}">										    
											{else}
											<div id="id_{$i[0]->getIdrayon()}" class="rayonToTransfer" type_ray="classique"  style="width:{math equation="x * y" y=$largeursegment x=$i[1]}px;border:1px solid #666;border-bottom:2px dashed #{$i[2]}">
											{/if}						
									{/if}
									<a href="#" onclick="return false;" title="{$i[0]->getLibelle()}" class="atransferer">{$i[0]->getLibelle()}</a>
									</div>
								{else}
									<div id="id_{$i[0]->getIdrayon()}" class="rayonToTransfer" type_ray="vrac"  style="width:40px;height:40px;border:2px dashed #{$i[2]}">
										<a href="#" onclick="return false;" title="{$i[0]->getLibelle()}" class="atransferer">{$i[0]->getLibelle()}</a>
									</div>
								{/if}
							{/if}
						{/foreach}
					</div>
					
						
					
					
					
					<!-- AFFICHAGE DES RAYONS et des OBSTACLES MODELISES DANS L'ETAGE --> 
					<div  id="scrolletage">
					 <div class="etage" id="{$arrayEtages[$cle]->getIdetage()}" style="position:relative;width:{$arrayEtages[$cle]->getlargeur()}px;height:{$arrayEtages[$cle]->gethauteur()}px;">		
					
					
					  
						<!-- Affichage du point de départ  -->
						<div class="point" id="zone_depart" style="position:absolute;top:{$pt_depart_top}px;left:{$pt_depart_left}px;"></div>
						<!-- Affichage du point d'arrive  -->
						<div class="point" id="zone_arrive" style="position:absolute;top:{$pt_arrive_top}px;left:{$pt_arrive_left}px;"></div>
							
						
						<!-- Affichage des obstacles  -->
						{foreach from=$arrayObstacles key=myId item=i}  
							{if {$i->getType()} == "caisse"}
								<div class="caisse" id="{$i->getIdobstacle()}" libelle="{$i->getLibelle()}" style="position:absolute;top:{$i->getPosition_top()}px;left:{$i->getPosition_left()}px;width:{$i->getLargeur()}px;height:{$i->getHauteur()}px;">CAISSE</div>
							{else}
								<div class="obstacle" id="obs_{$i->getIdobstacle()}" libelle="{$i->getLibelle()}" style="position:absolute;top:{$i->getPosition_top()}px;left:{$i->getPosition_left()}px;width:{$i->getLargeur()}px;height:{$i->getHauteur()}px;background-color:#{$i->getCouleur()};">
									<a href="#" onclick="return false;" class="suppr_obstacle"><img src="{$application_path}images/delete.png" style="width:12px;height:12px;" alt="D"/></a>
									<a href="#" class="hover_obstacle" onclick="return false;" ><img src="{$application_path}images/info.png" style="width:10px;height:10px;" alt="I"/></a>
                                    <a href="#" class="modifier_obstacle" onclick="return false;"><img src="{$application_path}images/modifier.png" style="width:12px;height:12px;" alt="I"/></a>
									</div>
							{/if}
						{/foreach}
						<!-- Affichage des rayons  -->
						{foreach from=$arrayRayons key=myId item=i} 
							{if {$i[0]->getPosition_top()} != "-1" && {$i[0]->getPosition_left()} != "-1"}
								
								{if $i[0]->getType() == 'classique'}
									{$top 		= $i[0]->getPosition_top()}
									{$left 		= $i[0]->getPosition_left()} 
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
									{$border 	= "border:2px dashed #{$i[2]}"} 								
									{$type 		= "vrac"}
									{$class		= "rayon vrac"}
								{/if}
									<div id="id_{$i[0]->getIdrayon()}" class="{$class}" sens="{$i[0]->getSens()}" type_ray="{$type}" largeur_ray="{$width}" hauteur_ray="{$height}"  top_deb="{$top}" left_deb="{$left}" style="top:{$top}px;left:{$left}px;width:{$width}px;height:{$height}px;{$border};">									
										<a href="#" class="rotation_rayon" onclick="return false;"   style="display:block;float:left;margin-right:5px; "><img src="{$application_path}images/rotate.png" style="width:12px;height:12px;" alt="R"/></a>
										<a href="#" class="voir_info" onclick="return false;"  style="display:block;float:left; margin-right:5px;"><img src="{$application_path}images/info.png" style="width:12px;height:12px;" alt="I"/></a>
                                        <a href="#" class="modifier_info" onclick="return false;"  style="display:block;float:left;margin-right:5px;"><img src="{$application_path}images/modifier.png" style="width:12px;height:12px;" alt="I"/></a>
										<span id="libelle_{$i[0]->getIdrayon()}">{$i[0]->getLibelle()}</span>
									</div>	
							{/if}
						{/foreach}
					</div>
					</div>
					</div>
					
					
				</div>
{include file="$user_template/common/page_footer.tpl"}
{/if}