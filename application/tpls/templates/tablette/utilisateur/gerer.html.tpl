{include file="$user_template/common/page_header.tpl"}

<div id="conteneur">
<h3>{$txt_listeUtilisateurs}</h3>
<hr /> 
<div class="lienhaut">
	<div class="liendroite"><a href="{$application_path}utilisateur/creer" title="">{$txt_ajouter}</a></div>
</div>
<br />
	<table class="tableau" id="sorter">
		<tr>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_login}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_prenom}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_nom}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_email}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_templateUtilise}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_fonction}</th>
			<th><img src="{$application_path}images/sort.gif" alt="sort" /> {$txt_derniereConnexion}</th>
			<th class="nosort"></th>
			<th class="nosort"></th>
		</tr>
		{foreach from=$users key=myId item=i}
			<tr>
				<td>{$i->getLogin()}</td>
				<td>{$i->getPrenom()}</td>
				<td>{$i->getNom()}</td>
				<td>{$i->getEmail()}</td>
				<td>{$i->getTemplate()}</td>
				<td>{$lvl = $i->getUserLevel()}
					{if $lvl == 3}
					{$txt_administrateur}
					{else if $lvl == 2}
					{$txt_superviseur}
					{else}
					{$txt_preparateur}
					{/if}
				</td>
				<td>{$i->getDerniereConnexion()|date_format:"%d/%m/%y %T"}</td>
				<td class="action">
					{if ($lvl < 3) || ($lvl == 3 && isset($userLevel) && $userLevel>=3)}
					<a href="{$application_path}utilisateur/editer/{$i->getIdUtilisateur()}" title="{$txt_editer}">{$txt_editer}</a>
					{/if}
				</td>
				<td class="action">{if isset($userId) && $userId != $i->getIdUtilisateur() && ($userLevel == 3 || $lvl<3)}<a href="{$application_path}utilisateur/supprimer/{$i->getIdUtilisateur()}" title="" onclick="return confirm('{$txt_confirmSuppression} {$i->getPrenom()} {$i->getNom()} ?')">{$txt_lienSupprimer}</a>{/if}</td>
			</tr>
		{/foreach}
	</table>
	<script type="text/javascript">
		var sorter=new table.sorter("sorter");
		sorter.init("sorter",0);
	</script>
</div>

{include file="$user_template/common/page_footer.tpl"}