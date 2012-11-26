<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***
 * Install file 
 */
 
session_start();
error_reporting(0); 
$_SESSION['etape'] = (!isset($_SESSION['etape']) || empty($_SESSION['etape'])) ? 1 : $_SESSION['etape'];
$etapes = array(
		'Droits dossiers',
		'Base de données',
		'Application 1/2',
		'Application 2/2',
		'Planning',
		'Admin',
		'Fin'	
		);

echo '
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>E-pick</title>	
		<style type="text/css">
			body {padding:0;margin :0;	display:table; 	width:100%;background-color:#EAEAE7;}
			#conteneur{	width:920px;margin:20px auto 0px auto;}
			#titre{	text-align:center;font-size:28px;font-family:verdana;}
			#titre .small{text-transform:uppercase;	font-size:13px;}
			.gras{ font-weight:bold;}
			.red{ 	color:DC0707;}
			
			#chemin{ width:100%; height:30px; margin:40px auto 40px auto;}
			#chemin .etape{ border : 1px solid #666;float:left;height:30px; line-height:30px; font-size:16px;margin: 0px 10px 0px 0px; background:#fff;border-radius:5px; padding:2px 5px 2px 5px;}
			#chemin .active{ background:#ddd; font-weight:bold;}
			#chemin .past{ background:#ddd; text-decoration:line-through;background-image:url(\'images/check.png\');background-repeat:no-repeat;background-position:right center; padding-right:30px;}
			
			#content{border:1px solid #999;	background-color:#fff;	min-height:400px;width:90%; margin:auto;	}
			#content h2 {text-align:center;border-bottom:1px solid black;width:80%;font-size:18px;line-height:30px;height:30px; margin:0px auto 10px auto; padding-top:10px 0px 10px 0px;}
			
			form{width:80%;	margin:auto;clear:both;}
			form label{	clear:both;	display:block;	float:left;	width:40%;text-align:left; 	height:18px;line-height:18px;padding-left:20px;	margin-bottom:5px;cursor:pointer;font-size:12px;  font-weight:bold;	}
			form label.label_date{ 	text-align:right;	}
			form select,form input{	display:block;float:right;width:49%; height:16px; line-height:16px; margin-bottom:3px; }
			form a{	float:right;}
			form input.date{height:16px;}
			form input.submit,form  div.reset{float:right;width:25%;font-size:11px;text-align:center;height:20px;	}
			form div.reset{	line-height:20px;	}
			form select{width:50%;height:20px;display:block; }			
			fieldset {border-top : 1px solid #666; padding:15px 15px 15px 5px;margin: 0 0 10px 0;background-color: #fff;}
			legend {font-size: 12px;letter-spacing: 1.5px;color: #0f0f0f;border : 1px solid #666;padding: 1px 5px; }
			
			#information_bloc{ clear:both;margin:auto;width:50%;margin-top:30px; }
			#information_bloc p{margin:10px;border:1px solid #DDD;	margin-bottom:40px;	padding-left:100px;	line-height:22px;background-image:url(\'images/info_chemin.png\');	background-repeat:no-repeat;background-position:15px center;	min-height:60px} 
			p.error{margin:10px;border:1px solid #DDD;	margin-bottom:40px;	padding-left:100px;	line-height:22px;background-image:url(\'images/error.png\');	background-repeat:no-repeat;background-position:15px center;	min-height:60px} 
 
			</style>

	</head>
	<body> 
		
		<div id="conteneur">
			<div id="titre">
				<span class="red">P</span>roxi-Picking <span class="small">by</span> <span class="red">P</span>roxi-Business</span>
			</div>
			
			<div id="chemin" >';
			
				foreach($etapes as $key=>$etape){
					if(($key+1) < $_SESSION['etape'])
						$class = 'past';					
					else if(($key+1) == $_SESSION['etape'])					
						$class = 'active';
					else
						$class = '';
					echo '<div class="etape ' . $class . '">' . ($key+1) .'. ' . $etape . '</div>';
				}
			echo '	
			</div>
			
			<div id="content">
				
';

if(isset($_GET['init'])){
	$_SESSION['etape'] = 1;
	header('Location:'. $_SERVER['PHP_SELF']);

}

switch($_SESSION['etape']){

	case 1 : 
		/* Tests des droits d'écriture des fichiers */
		echo' <h2>Etape 1 : Droits dossiers</h2>
				
				<div id="information_bloc">
					<p></p>
				</div>';
		if(isset($_POST['submit'])){
			/* Passer à l'étape suivante */
			$_SESSION['etape'] = 2;
			header('Location:'. $_SERVER['PHP_SELF']);
		}
		else{
			/* Tableau de tous les fichiers/dossiers qui doivent être en 777 */
			$arrayDirs = array(	'../application/tmp/', 
								'../application/backups', 
								'../application/config/conf/', 
								'../flux/in/commandes/logs/', 
								'../flux/in/commandes/save/', 
								'../flux/in/produits/logs/', 
								'../flux/in/produits/save/',
								'./PDA/geolocalisation/in/save/',
								'./PDA/geolocalisation/in/logs/',
								'./PDA/geolocalisation/out/save/',
								'./PDA/geolocalisation/out/logs/',
								'./PDA/commandes/in/save/',
								'./PDA/commandes/in/logs/',
								'./PDA/commandes/out/save/',
								'./PDA/commandes/out/logs/'
							);
			echo '<form action="" method="POST">';
			echo '<fieldset>';
			echo '<legend> Vérification des CHMOD </legend>';
			
			echo '<table style="width:100%;margin:auto;text-align:center;" >';
			echo '<tr style="font-weight:bold;">';
			echo '<td style="text-align:left;">Répertoire</td><td>Droits requis</td><td>Droits du répertoire</td><td></td>';
			echo '</tr>';
			$nb_erreurs = 0;
			foreach ($arrayDirs as $dir){
				echo '<tr>';
				if(file_exists($dir)){
					$droits = substr(sprintf('%o', fileperms($dir)), -4);
					if ($droits >= '0666')
						echo '<td style="text-align:left;">' . $dir . '</td><td>0666</td><td>' . $droits . '</td><td><img src="images/check.png" alt="ok"/></td>';
					else{
						$nb_erreurs ++;
						echo '<td style="text-align:left;font-weight:bold;">' . $dir . '</td><td>0666</td><td style="font-weight:bold;">' . $droits . '</td><td><img src="images/close.png" alt="nok"/></td>';
					}
				}
				else{
					$nb_erreurs ++;
					echo '<td style="text-align:left;font-weight:bold;">' . $dir . '</td><td>0666</td><td style="font-weight:bold;"> inexistant </td><td><img src="images/close.png" alt="nok"/></td>';				
				}
				echo '</tr>';
			}
			echo '</table>';
			echo '</fieldset>'; 
			if($nb_erreurs == 0)
				echo '<label></label><input type="submit" class="submit" name="submit" value="Passer à l\'étape suivante &raquo; " />';
			else
				echo '<label></label><input type="submit" class="submit" name="try_again" value="Vérifier à nouveau" />';
			echo '</form>';
		}
		break;
		
	case 2 : 
		echo' <h2>Etape 2 : Base de données</h2>
				
				<div id="information_bloc">
					<p></p>
				</div>';
		
		/* informations base de données */
			$dbname 		= '';
			$dbdsn			= '';
			$dbuser			= '';
			$dbpassword		= '';
			echo '<form action="" method="POST">';
				echo '<fieldset>';	
				echo '<legend> Informations sur la base de données </legend>';
		
		if (isset($_POST['submit'])){
			
			$text_errors	= '';

			
			
			$dbname 	= (isset($_POST['dbname'])) ? $_POST['dbname']  : '' ;
			if ($dbname == ''){
				$text_errors .='Veuillez renseigner le nom de la base de données <br />';
			}
			$dbdsn 		= (isset($_POST['dbdsn'])) ? $_POST['dbdsn']  : '' ;
			if ($dbdsn == ''){
				$text_errors .='Veuillez renseigner le nom/adresse de l\'hôte <br />';
			}
			$dbuser 	= (isset($_POST['dbuser'])) ? $_POST['dbuser']  : '' ;
			if ($dbuser == ''){
				$text_errors .='Veuillez renseigner l\'identifiant <br />';
			}
			$dbpassword = $_POST['dbpassword'];	// Le mot de passe peut être vide !
			
			if ($text_errors == ''){
				/* S'il n'y a pas d'erreurs de saisie */
				try{
					/* Test de connexion à la base de données */
					$connexion = new PDO('mysql:host=' . $dbdsn . ';dbname=' . $dbname , $dbuser, $dbpassword);
					
					/* Vider la table */
					
					
					/* Créer les tables  */
					
					
					/* Ecrire dans le fichier */
					$fileName 	= "../application/config/conf/databaseInstall.ini";
					$fichier 	= fopen($fileName, 'w');
					
					$texte 		= ";Database configuration file \r\n\r\n";
					$texte 	   .= 'DB_NAME = ' . $dbname . "\r\n";
					$texte 	   .= 'DB_DSN = "mysql:dbname=' . $dbname . ';host=' . $dbdsn . '"' . "\r\n";		
					$texte 	   .= 'DB_USER = ' . $dbuser . "\r\n";		
					$texte 	   .= 'DB_PASSWORD = ' . $dbpassword . "\r\n";
					
					fputs($fichier, $texte);
					fclose($fichier);
					
					/* Passer à l'étape suivante */
					$_SESSION['etape'] = 3;
					header('Location:'. $_SERVER['PHP_SELF']);
				}
				catch(Exception $e)  {
					echo '<p class="error">' . $text_errors . '<br />'.$e->getMessage().' ('.$e->getCode().')</p>';
					echo '<label for="dbname">Nom de la base de donnée : </label> <input type="text" id="dbname" name="dbname" value="' . $dbname . '"/> <br />'; 
					echo '<label for="dbdsn">Nom de l\'hôte : </label> <input type="text" id="dbdsn" name="dbdsn" value="' . $dbdsn . '"/> <br />' ;
					echo '<label for="dbuser">Identifiant : </label> <input type="text" id="dbuser" name="dbuser" value="' . $dbuser . '"/> <br />' ;
					echo '<label for="dbpassword">Mot de passe : </label> <input type="text" id="dbpassword" name="dbpassword" value="' . $dbpassword . '"/> <br />';	
				}
				
			}
			else{				 
				echo '<p class="error">' . $text_errors . '</p>';
				echo '<label for="dbname">Nom de la base de donnée : </label> <input type="text" id="dbname" name="dbname" value="' . $dbname . '"/> <br />'; 
				echo '<label for="dbdsn">Nom de l\'hôte : </label> <input type="text" id="dbdsn" name="dbdsn" value="' . $dbdsn . '"/> <br />' ;
				echo '<label for="dbuser">Identifiant : </label> <input type="text" id="dbuser" name="dbuser" value="' . $dbuser . '"/> <br />' ;
				echo '<label for="dbpassword">Mot de passe : </label> <input type="text" id="dbpassword" name="dbpassword" value="' . $dbpassword . '"/> <br />';	
			}
			
		}
		else{			
				
			echo '<label for="dbname">Nom de la base de donnée : </label> <input type="text" id="dbname" name="dbname" value="' . $dbname . '"/> <br />'; 
			echo '<label for="dbdsn">Nom de l\'hôte : </label> <input type="text" id="dbdsn" name="dbdsn" value="' . $dbdsn . '"/> <br />' ;
			echo '<label for="dbuser">Identifiant : </label> <input type="text" id="dbuser" name="dbuser" value="' . $dbuser . '"/> <br />' ;
			echo '<label for="dbpassword">Mot de passe : </label> <input type="text" id="dbpassword" name="dbpassword" value="' . $dbpassword . '"/> <br />';
		}
		echo '</fieldset>';
		echo '<label></label><input type="submit" class="submit" name="submit" value="Passer à l\'étape suivante > " />';
		echo '</form>';
		
		break;
	case 3 : 
		/* Informations utiles pour l'utilisation de l'application : phase 1 */
		$timezones = array('Europe/Amsterdam','Europe/Andorra','Europe/Athens','Europe/Belfast','Europe/Belgrade','Europe/Berlin','Europe/Bratislava','Europe/Brussels','Europe/Bucharest',
		'Europe/Budapest','Europe/Chisinau','Europe/Copenhagen','Europe/Dublin','Europe/Gibraltar','Europe/Guernsey','Europe/Helsinki','Europe/Isle_of_Man','Europe/Istanbul',
		'Europe/Jersey','Europe/Kaliningrad','Europe/Kiev','Europe/Lisbon','Europe/Ljubljana','Europe/London','Europe/Luxembourg','Europe/Madrid','Europe/Malta',
		'Europe/Mariehamn','Europe/Minsk','Europe/Monaco','Europe/Moscow','Europe/Nicosia','Europe/Oslo','Europe/Paris','Europe/Podgorica','Europe/Prague','Europe/Riga','Europe/Rome',
		'Europe/Samara','Europe/San_Marino','Europe/Sarajevo','Europe/Simferopol','Europe/Skopje','Europe/Sofia','Europe/Stockholm','Europe/Tallinn','Europe/Tirane',
		'Europe/Tiraspol','Europe/Uzhgorod','Europe/Vaduz','Europe/Vatican','Europe/Vienna','Europe/Vilnius','Europe/Volgograd','Europe/Warsaw','Europe/Zagreb',
		'Europe/Zaporozhye','Europe/Zurich');
		
		echo' <h2>Etape 3 : Paramètrage de l\'application 1/2</h2>
				
				<div id="information_bloc">
					<p></p>
				</div>';
		
		if(isset($_POST['submit'])){
			/* Passer à l'étape suivante */
			$_SESSION['application_path']	 = $_POST['application_path'];
			$_SESSION['application_prefixe'] = $_POST['application_prefixe'];
			$_SESSION['langue']				 = $_POST['langue'];
			$_SESSION['fuseau_horaire']		 = $_POST['fuseau_horaire'];
			$_SESSION['devise']				 = $_POST['devise'];
			$_SESSION['resultat_par_page']	 = $_POST['resultat_par_page'];
			
			
			$_SESSION['etape'] = 4;
			header('Location:'. $_SERVER['PHP_SELF']);
		}
		
		echo '<form action="" method="POST">';
		echo '<fieldset>';	
		echo '<legend> Paramètrage de l\'application 1/2 </legend>';
		
		echo '<label for="application_path">Chemin de l\'application : </label> <input type="text" id="application_path" name="application_path" value="'.substr($_SERVER['REQUEST_URI'],0,-11).'"/> <br />'; 
		echo '<label for="application_prefixe">Préfixe de l\'application : </label> <input type="text" id="application_prefixe" name="application_prefixe" value="PP"/> <br />'; 
		echo '<label for="langue">Langue par défaut de l\'application :</label>
					<select name="langue" id="langue">
					';
					echo '<option value="fr_FR">Français</option>';
					echo '<option value="en_EN">Anglais</option>';
				echo '	
					</select>';	
	echo '<label for="fuseau_horaire">Fuseau horaire :</label>
					<select name="fuseau_horaire" id="fuseau_horaire">
					';
					foreach($timezones as $timezone)
						echo '<option value="'.$timezone.'">'.$timezone.'</option>'; 
				echo '	
					</select>';
		echo '<label for="devise">Devise : </label> 	
			<select name="devise" id="devise">
					';
					echo '<option value="€">€uro</option>';
					echo '<option value="$">Dollar</option>';
					echo '<option value="£">Livre</option>';
				echo '	
					</select>';	
		echo '<label for="resultat_par_page">Nombre de résultats par page : </label> 	
			<select name="resultat_par_page" id="resultat_par_page">
					';
					echo '<option value="25">25 résultats</option>';
					echo '<option value="50">50 résultats</option>';
					echo '<option value="75">75 résultats</option>';
					echo '<option value="100">100 résultats</option>';
					echo '<option value="150">150 résultats</option>';
				echo '	
					</select>';	
	
		echo '</fieldset>';
		echo '<label></label><input type="submit" class="submit" name="submit" value="Passer à l\'étape suivante > " />';
		echo '</form>';
		break;
		
	case 4 : 
		/* Informations utiles pour l'utilisation de l'application : phase 2 */
		echo' <h2>Etape 4 : Paramètrage de l\'application 2/2</h2>
				
				<div id="information_bloc">
					<p></p>
				</div>';
				
		if(isset($_POST['submit'])){
		
			/* INSERER LES ETAGES AVEC POINT ARRIVE , DEPART, ZONE MAGASIN ET CAISSE */
			
			
			
			$fileName 	= "../application/config/conf/applicationInstall.ini";
			$fichier 	= fopen($fileName, 'w');
			 
			$texte 		= ";Application configuration file \r\n\r\n";
			$texte 	   .= 'APPLICATION_PATH = "' . $_SESSION['application_path'] .'"' . "\r\n";
			$texte 	   .= 'APPLICATION_PREFIXE = "' . $_SESSION['application_prefixe']  .'"'. "\r\n";
			$texte 	   .= 'LANGUAGE = "' . $_SESSION['langue']  .'"'. "\r\n";
			$texte 	   .= 'FUSEAU_HORAIRE = "' . $_SESSION['fuseau_horaire'] .'"' . "\r\n";
			$texte 	   .= 'DEVISE = "' . $_SESSION['devise']  .'"'. "\r\n";
			$texte 	   .= 'RESULTAT_PAR_PAGE = ' . $_SESSION['resultat_par_page'] . "\r\n";
			$texte 	   .= 'NOMBRE_ETAGES = ' . $_POST['nombre_etages'] . "\r\n";
			$texte 	   .= 'FINESSE_UTILISEE = ' . $_POST['finesse_utilisee'] . "\r\n";
			$texte 	   .= 'DELAI_AVANT_LIVRAISON = ' . $_POST['delai_avant_livraison'] . "\r\n";
			$texte 	   .= 'LARGEUR_ETAGERE = 1.10'. "\r\n";
			
			
			fputs($fichier, $texte);
			fclose($fichier);
			
			/* Passer à l'étape suivante */     
			
			$_SESSION['etape'] = 5;
			header('Location:'. $_SERVER['PHP_SELF']);
		}			
				
		echo '<form action="" method="POST">';
		echo '<fieldset>';	
		echo '<legend> Paramètrage de l\'application 2/2 </legend>';
		
		echo '<label for="nombre_etages">Nombre d\'étages : </label> 	
		<select name="nombre_etages" id="nombre_etages">
				';
				echo '<option value="1">1</option>';
				echo '<option value="2">2</option>';
				echo '<option value="3">3</option>';
		echo '</select>';	
		
		echo '<label for="finesse_utilisee">Finesse utilisée : </label> 	
		<select name="finesse_utilisee" id="finesse_utilisee">
				';
				echo '<option value="3">Finesse à l\'étagère</option>';
				echo '<option value="2">Finesse au segment</option>';
				echo '<option value="1">Finesse au rayon</option>';
		echo '</select>';	
		
		echo '<label for="delai_avant_livraison">Délai fin préparation avant livraison : </label> 	
		<select name="delai_avant_livraison" id="delai_avant_livraison">
				';
				echo '<option value="0">Aucun délai</option>'; 
				echo '<option value="3600">1 heure</option>'; 
				echo '<option value="7200">2 heures</option>'; 
				echo '<option value="10800">3 heures</option>'; 
				echo '<option value="14400">4 heures</option>'; 
				echo '<option value="18000">5 heures</option>'; 
				echo '<option value="21600">6 heures</option>'; 
				echo '<option value="25200">7 heures</option>'; 
		echo '</select>';	
				
		echo '</fieldset>';
		echo '<label></label><input type="submit" class="submit" name="submit" value="Passer à l\'étape suivante > " />';
		echo '</form>';
		break;
		
	case 5 : 
		/* Informations sur le planning des utilisateurs */
			echo' <h2>Etape 5 : Paramétrage du planning</h2>
				
				<div id="information_bloc">
					<p></p>
				</div>';
				
			if (isset($_POST['submit'])){
					$fileName 	= "../application/config/conf/planningInstall.ini";
					$fichier 	= fopen($fileName, 'w');
					 
					$texte 		= ";Planning configuration file \r\n\r\n";
					$texte 	   .= 'HEURE_DEBUT = ' . (int) $_POST['heure_debut'] . "\r\n";
					$texte 	   .= 'HEURE_FIN = ' . (int) $_POST['heure_fin'] . "\r\n";		
					$texte 	   .= 'JOURNEE_DEBUT = ' . (int) $_POST['jour_debut'] . "\r\n";		
					$texte 	   .= 'JOURNEE_FIN = ' . (int) $_POST['jour_fin'] . "\r\n";		
					$texte 	   .= 'MODE_CRENEAU = ' . (int) $_POST['creneau'] . "\r\n";	
					
					fputs($fichier, $texte);
					fclose($fichier);
					
					/* Passer à l'étape suivante */
					$_SESSION['etape'] = 6;
					header('Location:'. $_SERVER['PHP_SELF']);
			}
				
			echo '<form action="" method="POST" class="form"> 
			
			<fieldset>
				<legend>Plage horaire</legend>
					
				<label for="heure_debut">Heure de début de journée :</label>
					<select name="heure_debut" id="heure_debut">
					';
					for ($h = 0; $h<= 24;$h++)
						echo '<option value="'.$h.'">'.$h.'</option>';
				echo '	
					</select>
				 
				<br style="clear:both;"/>	
				<label for="heure_fin">Heure de fin de journée :</label>
					<select name="heure_fin" id="heure_fin">
						';
					for ($h = 0; $h<= 24;$h++){
						echo '<option value="'.$h.'"';
						if($h == 24) echo ' selected=true';
						echo '	>'.$h.'</option>';
					}
				echo '	
					</select>
				
			</fieldset>
			
			<fieldset>
				<legend>Vue semaine </legend>
				
				<label for="jour_debut">Journée de début :</label>
					<select name="jour_debut" id="jour_debut">';
					$semaine = array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');	
					for ($h = 1; $h<= 7;$h++)
						echo '<option value="'.$h.'" >'.$semaine[$h-1].'</option>';

			
				echo '									
					</select>
				<br style="clear:both;"/>
				<label for="jour_fin">Journée de fin :</label>
					<select name="jour_fin" id="jour_fin">';
					for ($h = 1; $h<= 7;$h++){
						echo '<option value="'.$h.'"';
						if($h == 7) echo ' selected=true';
						echo '	>'.$semaine[$h-1].'</option>';
					}
			
				echo '	
					</select>
				
			</fieldset>
			
			<fieldset>
				<legend>Créneau </legend>
				<label for="creneau">Créneau par :</label> 
					<select name="creneau" id="creneau">';
					$creneau = array('Heure','Demi heure');
						for ($h =0; $h<= 1;$h++)
							echo '<option value="'.$h.'">'.$creneau[$h].'</option>';
					echo '
					</select>
				
			</fieldset>
			
			<input type="submit" name="submit" class="submit" value="Passer à l\'étape suivante > "/>
				';
				
		break;
		
	case 6 : 
		/* Ajout de l'utilisateur ADMIN */
		echo 'user';
		break;
		
	default :
		echo 'error';

} 
	
	echo '	<br style="clear:both" />
			</div>
		</div>
	</body>
</html>';
 
 
 
 
 
 
 
 
?>