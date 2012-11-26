<div id="menuLeft">
	<div class="itemMenu">
		<h4>{$menu_gestion_commandes}</h4>
		<div class="itemSousMenu"> 
			
			<div class="item">
				<h5>{$menu_commandes}</h5>
				<div id='blocRight'>
					<a href="{$application_path}commande" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>{$menu_recup_commandes} ({$count_commandes})</h5>
				<div id='blocRight'>
					<a href="{$application_path}commande/importer" title="" onclick="return confirm('{$txt_confirm_commandes} ?')"><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
		</div> 
	</div>
	
	<div class="itemMenu">
		<h4>{$menu_gestion_commandes_affect}</h4>
		<div class="itemSousMenu"> 
			
			<div class="item">
				<h5>{$menu_gestion_affect_manuelle}</h5>
				<div id='blocRight'>
					<a href="{$application_path}affectation/manuelle" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div class="item">
				<h5>{$menu_gestion_affect_liste}</h5>
				<div id='blocRight'>
					<a href="{$application_path}affectation/all" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
            <div class="item">
				<h5>{$menu_gestion_mes_preparations}</h5>
				<div id='blocRight'>
					<a href="{$application_path}affectation/" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
            
		</div> 
	</div>
	
	<div class="itemMenu">
		<h4>{$menu_gestion_planning}</h4>
		<div class="itemSousMenu"> 
			
			<div class="item">
				<h5>{$menu_planning}</h5>
				<div id='blocRight'>
					<a href="{$application_path}planning" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>

		</div> 
	</div>
	
	
	<div class="itemMenu">
		<h4>{$menu_gestion_clientele}</h4>
		<div class="itemSousMenu"> 
			
			<div class="item">
				<h5>{$menu_clients}</h5>
				<div id='blocRight'>
					<a href="{$application_path}client" title=""><img src="{$application_path}images/{$user_template}/liste.png" alt="liste" /></a>
				</div>
				<div style="clear:both;"></div>
			</div>
			
		</div> 
	</div>
	 
</div>



							
