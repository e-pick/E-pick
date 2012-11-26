{include file="$user_template/common/page_header.tpl"}


<div id="conteneur">
	<div style="text-align:center;"><h3>{$txt_magasin}</h3><hr style="width:80%;"/></div> 
	
	<div id="chemin">
		<div class="etape"><a href="{$application_path}admin/importerproduits/" title="" {if $recup_ok == 0}class="active"{/if}><span class="number">1 /</span> {$txt_etape1}</a></div>	
		<div class="next"><img src="{$application_path}images/arrow.png" alt="V" /></div>
		<div class="etape"><a href="{$application_path}admin/importerproduits/1" title="" {if $recup_ok == 1}class="active"{/if}><span class="number">2 /</span> {$txt_etape2}</a></div>
		<div class="next"><img src="{$application_path}images/arrow.png" alt="V" /></div>
		<div class="etape"><span class="number">3 /</span> {$txt_etape3}</div>
		<div class="next"><img src="{$application_path}images/arrow.png" alt="V" /></div>
		<div class="etape"><a href="{$application_path}modelisation/consulter" title=""><span class="number">4 /</span> {$txt_etape4}</a></div>
	</div>
	<div id="geolocalisation"> 
		{if $recup_ok == 1}
			<p>{$txt_com_etape2}</p>
			<a href="{$application_path}geolocalisation/exporter" title="">{$txt_etape} 2 : {$txt_etape2}</a> 
		{else}		
			
			<p>{$txt_com_etape1}</p>
			<a href="{$application_path}admin/importerproduits/1"   title=""  id="sauvegarder">{$txt_etape} 1 : {$txt_etape1}</a> 
			<!--<a href="#save_box"  class="boxy" title=""  id="sauvegarder">{$txt_etape} 1 : {$txt_etape1}</a> -->
		{/if}
	</div>
	
	<div id="save_box" style="display:none;width:400px;height:220px;">
		<h2 style="text-align:center;">Importation en cours</h2>
		<h4 style="text-align:center;">Cette opération peut prendre plusieurs minutes...</h4>
		 <p>
			<ul id="etape">
			<li id="op_1">Importation des produits en cours</li>
			</ul>
		 </p>		
		<div id="load" style="text-align:center;margin:auto;"></div>		
		<div class="closeThePopup" style="margin-top:10px;cursor:pointer;font-size:12px;text-align:center;margin:auto;"></div>
	</div>
	<br style="clear:both" />
	 
</div>
{include file="$user_template/common/page_footer.tpl"}