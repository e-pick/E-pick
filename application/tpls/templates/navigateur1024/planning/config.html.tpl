{include file="$user_template/common/page_header.tpl"}
	
	<div id="conteneur">
		<h3>{$txt_configPlanning}</h3>
		<hr /> 
		<br />
	
					<a href="{$application_path}planning" title="">Gérer le planning des utilisateurs</a>
			
			
		<form action="" method="POST" class="form" enctype="multipart/form-data">
			{if isset($form_errors) & !empty($form_errors)}
				<div id="conteneurErreur">
					<div id="imageErreur">
						<img src="{$application_path}images/error.png" alt="error">
					</div>
					<div id="texteErreur">
						{$form_errors}
					</div>
					<br style="clear:both"/>
				</div>
			{/if}
			
			<fieldset>
	

	
					<legend>{$txt_Plage_horaire} </legend>
					
				<label for="heure_debut">{$txt_heureDebut} :</label>
					<select name="heure_debut" id="heure_debut">
						{section name=i start=0 loop=24 step=1}
							{$heure = $smarty.section.i.index}
							<option value="{$heure}" {if isset($form_heure_debut) & !empty($form_heure_debut) & $form_heure_debut == {$heure}}selected="true"{/if}>{$heure}</option>
						{/section}
					</select>
				<br style="clear:both;"/>	
				<label for="heure_fin">{$txt_heureFin} :</label>
					<select name="heure_fin" id="heure_fin">
						{section name=j start=$form_heure_debut loop=25 step=1}
							{$heure = $smarty.section.j.index}
							<option value="{$heure}" {if isset($form_heure_fin) & !empty($form_heure_fin) & $form_heure_fin == {$heure}}selected="true"{/if}>{$heure}</option>
						{/section}
					</select>
				
			</fieldset>
			
			<fieldset>
				<legend>{$txt_Vue_semaine} </legend>
				
				<label for="jour_debut">{$txt_jour_debut} :</label>
					<select name="jour_debut" id="jour_debut">
						{section name=i start=1 loop=8 step=1}
							{$jour = $smarty.section.i.index}
							<option value="{$jour}" {if isset($form_jour_debut) & !empty($form_jour_debut) & $form_jour_debut == {$jour}}selected="true"{/if}>{$semaine[$jour-1]}</option>
						{/section}
					</select>
				<br style="clear:both;"/>
				<label for="jour_fin">{$txt_jour_fin} :</label>
					<select name="jour_fin" id="jour_fin">
						{section name=j start=$form_jour_debut loop=8 step=1}
							{$jour = $smarty.section.j.index}
							<option value="{$jour}" {if isset($form_jour_fin) & !empty($form_jour_fin) & $form_jour_fin == {$jour}}selected="true"{/if}>{$semaine[$jour-1]}</option>
						{/section}
					</select>
				
			</fieldset>
			
			<fieldset>
				<legend>{$txt_Creneau} </legend>
				<span style="font-size:12px;">{$txt_modification_creneau}</span><br />
				<label for="creneau">{$txt_creneau_par} :</label>
					<input type="hidden" name="creneau_ancien" value="{$form_creneau}"/>
					<select name="creneau" id="creneau">
						{section name=i start=0 loop=2 step=1}
							{$indice = $smarty.section.i.index}
							<option value="{$indice}" {if isset($form_creneau) & !empty($form_creneau) & $form_creneau == {$indice}}selected="true"{/if}>{$creneau[$indice]}</option>
						{/section}
					</select>
				
			</fieldset>
			
			<input type="submit" name="submit" value="{$txt_boutonEnregistrer}"/>
			
		</form>
		<br style="clear:both;"/>
	</div>

{include file="$user_template/common/page_footer.tpl"}