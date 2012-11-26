<div id="menuLeft">
	
	<div class="itemMenu">
		<h4>{$menu_produits}</h4>
		<div class="itemSousMenu">
		
			<div class="item">
				<h5>{$menu_produits}</h5>
				<div id='blocRight'> 
					<a href="{$application_path}produit" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
					<a href="{$application_path}produit/ajouter" title=""><img src="{$application_path}images/{$user_template}/plus.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>

			<div class="item">
				<h5>{$menu_gestion_priorite}</h5>
				<div id='blocRight'>
					<a href="{$application_path}produit/priorite" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
		</div> 
	</div>
	

	
	<div class="itemMenu">
		<h4>{$menu_flux_produits}</h4>
		<div class="itemSousMenu"> 
		
			<div class="item">
				<h5>{$menu_produits_synchro}</h5> 
				<div id='blocRight'>
					<a href="{$application_path}produit/importer/" title="" onclick="return confirm('{$txt_confirm_produits} ?')"><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>{$menu_geo_vers_Pda}</h5> 
				<div id='blocRight'>
					<a href="{$application_path}geolocalisation/exporter" title="" onclick="return confirm('{$txt_confirm_geoloc_export} ?')"><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>{$menu_geo_du_Pda}</h5> 
				<div id='blocRight'>
					<a href="{$application_path}geolocalisation/importer" title="" onclick="return confirm('{$txt_confirm_geoloc_import} ?')"><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>	
		
		</div> 
	</div>
<div class="itemMenu">
		<h4>{$menu_organisation}</h4>
		<div class="itemSousMenu"> 
		
			<div class="item">
				<h5>{$menu_etage}</h5>
				<div id='blocRight'>
					<a href="{$application_path}etage" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
					{if $userLevel >= 3}<a href="{$application_path}etage/creer" title=""><img src="{$application_path}images/{$user_template}/plus.png" alt="liste" /></a>{/if}
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>&nbsp;&nbsp;&nbsp;&nbsp;{$menu_zone}</h5>
				<div id='blocRight'>
					<a href="{$application_path}zone" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
					<a href="{$application_path}zone/creer" title=""><img src="{$application_path}images/{$user_template}/plus.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$menu_rayon}</h5>
				<div id='blocRight'>
					<a href="{$application_path}rayon" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
					<a href="{$application_path}rayon/creer" title=""><img src="{$application_path}images/{$user_template}/plus.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>{$menu_import_export}</h5>
				<div id='blocRight'>
					<a href="{$application_path}rayon/importer" title=""><img src="{$application_path}images/{$user_template}/importer.png" alt="liste" /></a>
					<a href="{$application_path}rayon/exporter" title=""><img src="{$application_path}images/{$user_template}/exporter.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>{$menu_affectation_rayons}</h5>				
				<div id='blocRight'>
					<a href="{$application_path}zone/affecter" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>		
			</div>
			
		</div>
	</div>
	
	<div class="itemMenu">
		<h4>{$menu_integrite}</h4>
		<div class="itemSousMenu"> 
		
			<div class="item">
				<h5>{$menu_non_geolocalises}</h5>
				<div id='blocRight'>
					<a href="{$application_path}produit/nongeolocalise" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>	
			
			<div class="item">
				<h5>{$menu_produits_inconnus}</h5>
				<div id='blocRight'>
					<a href="{$application_path}produit/inconnu" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>{$menu_codes_ean}</h5>
				<div id='blocRight'>
					<a href="{$application_path}produit/sanscodeean" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
		</div>
	</div>
	
</div>