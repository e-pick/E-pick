{include file="$user_template/common/page_header.tpl"}


{if $userLevel > 1}
	{include file="$user_template/common/menuLeftCommande.tpl"}
	<div id="conteneur_menu">
{else}
	<div id="conteneur">
{/if}

	<h3>{$txt_preparation} : {$preparation->getIdpreparation()}</h3>
	<hr /> 

	<div class="lienhaut">
		<div class="liendroite">
			{if $userLevel > 1 && $preparation->getEtat() == 0}<a href="{$application_path}affectation/supprimer/{$preparation->getIdpreparation()}" onclick="return confirm('{$txt_confirmSuppression}')">{$txt_supprimerPreparation}</a> | {/if}
			{if $userLevel > 1 && $preparation->getEtat() == 1}<a href="{$application_path}affectation/reinitialiser/{$preparation->getIdpreparation()}" onclick="return confirm('{$txt_confirmReinit}')">{$txt_reinitialiserPreparation}</a> | {/if}
			<a href="javascript:history.go(-1)" title="">{$txt_retour}</a>
		</div>
	</div>
	<br />

	<fieldset class="commande" style="width:80%">
	<legend>{$txt_info_pre}</legend>
	<div class="gauche">
		<div class="gauche inside">{$txt_preparation} : </div>
		<div class="droite inside">{$preparation->getIdpreparation()}</div>
	</div>
	<div class="droite">
		<div class="gauche inside">{$txt_etat} :</div>
		<div class="droite inside">{$array_etat_preparation[$preparation->getEtat()]}</div>
	</div>

	<div class="gauche">
		<div class="gauche inside">{$txt_date_limite_debut} : </div>
		<div class="droite inside">{($preparation->getDate_preparation() - $preparation->getDuree())|date_format:"%d/%m/%Y %H:%M"}</div>
	</div>
	<div class="droite">
		<div class="gauche inside">{$txt_date_limite_fin} :</div>
		<div class="droite inside">{$preparation->getDate_preparation()|date_format:"%d/%m/%Y %H:%M"}</div>
	</div>
	<div class="gauche">
		<div class="gauche inside">{$txt_duree} : </div>
		<div class="droite inside">{$duree_pre|seconde_format}</div>
	</div>
	<div class="droite">
		<div class="gauche inside">{$txt_preparateur} :</div>
			<div class="droite inside"><a href="{$application_path}utilisateur/editer/{$preparateur->getIdUtilisateur()}">{$preparateur->getPrenom()} {$preparateur->getNom()}</a></div>
	</div>
	<div class="gauche">
		<div class="gauche inside">{$txt_modePrepa} : </div>
		<div class="droite inside">{$preparation->getModePreparation()} ({$preparation->getTypePreparation()})</div>
	</div>

	{if $userLevel > 1}
	<div class="droite" >
		<form method="post" action="">
		<div class="gauche inside">{$txt_changerPreparateur} :</div>
		<div class="droite inside">
			<select name="utilisateur">
				<option value=''/>
				{foreach from=$users key=id item=user}  
					{if $user->getIdUtilisateur() != $preparateur->getIdUtilisateur()}<option value="{$user->getIdUtilisateur()}">{$user->getPrenom()} {$user->getNom()}</option>{/if}
				{/foreach}
			</select>
			<input type="submit" name="submit" value="{$txt_affecter}"/>
		</div>
		</form>
	</div>
	{/if}
	<br style="clear:both;"/>


	</fieldset>
	<br style="clear:both;" />

	
	<fieldset class="commande" style="width:80%">
	{if $erreur}
		{$txt_erreur}
		{if $preparation->getEtat() == 0}
			{$txt_annuler}
		{/if}
	{/if}
	<legend>{$txt_compo_pre}</legend>
	<div class="lienhaut">
		{if !$erreur}
			<div class="liendroite"><a href="{$application_path}commande/chemin/preparation-{$preparation->getIdpreparation()}" title="">{$txt_afficher_chemin}</a></div>
		{/if}
	</div>
	{$txt_contient} {$nbReferences} {$txt_reference} / {$nbProduits} {$txt_produit}
		<form action="" method="POST">
		<table class="tableau" id="sorter" style="width:80%;">
			<tr>
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_produit}</th>
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_commande}</th>
				{if $preparation->getEtat() == 2}		
					<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_qtePrelevee}</th>
				{/if}
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_qteCommandee}</th>
				{if $preparation->getEtat() == 1 && $preparation->getTypePreparation() == "PDF"}	
					<th>{$txt_ligne_qtePrelevee}</th>
				{/if}
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_prixU}</th>  
				<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_ligne_prix}</th> 
				
			</tr>
			{foreach from=$lignes_commande key=myId item=ligne}
				{$qte_prelevee 	= $ligne[4]}
				{$qte			= $ligne[0]->getQuantiteCommandee()}
				{$prixU		 	= $ligne[0]->getPrixUnitaireTTC()}
				{$prix 		 	= $prixU * $qte}
				<tr>
					<td style="text-align:left;{if !$ligne[3]}background:{$ligne[5]};{/if}"><a href="{$application_path}produit/afficher/{$ligne[1]->getIdProduit()}" title="{$txt_details_pro}">{$ligne[1]->getLibelle()}</a></td>  
					<td style="text-align:left;"><a href="{$application_path}commande/afficher/{$ligne[2]->getIdcommande()}" title="{$txt_details_com}">{$ligne[2]->getCodeCommande()}</a></td> 
					{if $preparation->getEtat() == 2}
						<td>{$qte_prelevee}</td> 
					{/if}
					<td>{$qte}</td>
					{if $preparation->getEtat() == 1 && $preparation->getTypePreparation() == "PDF"}	
						<td><input type="text" name="saisieQtePrelevee[{$ligne[0]->getIdLigne()}]" value="{if isset($qte) & !empty($qte)}{$qte}{/if}" style="text-align:center;"/></td>
					{/if}
					<td>{$prixU|string_format:"%.2f"} {$devise}</td>  
					<td>{$prix|string_format:"%.2f"} {$devise}</td>  
					
				</tr>
			{foreachelse}
				<tr>
					<td colspan="6">{$txt_no_ligne}</td>
				</tr>
			{/foreach}
		</table>

		{if $preparation->getEtat() == 1 && $preparation->getTypePreparation() == "PDF"}
		<div style="width:80%;margin:auto;"> <h4>{$txt_renseignerNbBacs} :</h4></div>
		<table class="tableau" id="sorter" style="width:80%;">
			<th><img src="{$application_path}images/sort.gif" alt="sort" style="widht:35%"/> {$txt_ligne_commande}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" style="widht:50%"/> {$txt_client}</th>
			<th style="width:8%">{$txt_nb_bacs}</th>
			
			{foreach from=$arrayCommandes key=myId item=commande}
				<tr>
					<td style="text-align:left;"><a href="{$application_path}commande/afficher/{$commande[0]}" title="{$txt_details_com}">{$commande[1]}</a></td> 
					<td style="text-align:left;"><a href="{$application_path}client/afficher/{$commande[2]}" title="{$txt_details_cli}">{$commande[3]}</a></td> 
					<td><input type="text" name="nbBacs[{$commande[0]}]" value="1" style="text-align:center;"/></td>
				</tr>
			{foreachelse}
			
			{/foreach}
		
			</table>
		
		<div style="width:80%;margin:auto;margin-bottom:10px;">
			<input type="submit" style="width:25%;height:20px;float:right" name="submitSaisie" value="{$txt_valider_saisie}" />
		</div>
		{/if}

		</form>
		<script type="text/javascript">
			var sorter=new table.sorter("sorter");
			sorter.init("sorter");
		</script> 
	</fieldset>

</div>

{include file="$user_template/common/page_footer.tpl"}