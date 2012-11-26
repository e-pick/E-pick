<?php
 switch($_GET['type']){
	case 'http404' :						
		header("HTTP/1.0 404 Not found"); 	break;
	case 'http500' :
		header("HTTP/1.0 500 Internal Server Error"); 	break;
	default :						
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>E-Pick</title>	
		<style type="text/css">
			body {
				padding:0;
				margin :0;
				display:table; 
				width:100%;	
				background-color:#EAEAE7;
			}
			#conteneur{
				width:600px;
				height:400px;
				margin:auto; 
				margin-top:160px;
			}
			#titre{
				text-align:center;
				font-size:28px;
				font-family:verdana;
				margin-bottom:15px;
			}
			
			.small{
				text-transform:uppercase;
				font-size:12px;
				font-family:verdana;
			}
			
			#erreur{				
				border:1px solid #999;
				background-color:#fff;
				min-height:400px;
			}
			#erreur h2{
				text-align:center;
				text-transform:uppercase;
				font-size:18px;
				text-decoration:underline;
				color:#DC0707;
				margin-top:30px;
			}
			#erreur table{
				width:90%;
				margin:auto;
				margin-top:30px;
			}
			#erreur_table tr{
				height:80px;
			}
			#erreur table td{
				font-size:12px;
				vertical-align:top;
				text-align:justify;
			}			
			#erreur table td.small{
				text-transform:uppercase;
				font-size:13px;
				font-weight:bold;
				width:15%;
				text-align:right;
			}
			#erreur #link{
				margin:auto;
				margin-top:50px;
				text-align:center;
			}
			#erreur #link a{
				color:#000;
			}
		</style>

	</head>
	<body> 
		
		<div id="conteneur">
			<div id="titre">
				E-Pick
			</div>
			<div id="erreur">
				<h2>Une erreur a été rencontrée</h2>
				
				<table border="1">
					<tr>
						<td class="small">Code :</td>
						<td><?php	if(isset($_GET['code'])) echo '0x00'.$_GET['code']; ?></td>
					</tr>
					<tr>
						<td class="small">Intitulé :</td>
						<td><?php 	if(isset($_GET['message']))	echo $_GET['message'];	?></td>
					</tr>
					<tr>
						<td class="small" nowrap>Description :</td>
						<td><?php
							switch((int) $_GET['code']){
								case 0 :
									echo 'Une erreur est survenue, si le problème persiste n\'hésitez pas à nous contacter.';
									break;
								case 2 :
									echo 'Vous avez demandé l\'accès à une action nécessitant des droits d\'accès supérieurs aux vôtres. N\'hésitez pas à contacter l\'administrateur 
										de cette plateforme pour lui demander de vous fournir les droits d\'accès nécessaires.';
									break;
								case 3 :
									echo 'L\'action demandée nécessite un ou plusieurs paramètres supplémentaires. Vous devez vérifier l\'adresse saisie dans le navigateur et recommencer.
										Si vous venez de cliquer sur un lien de cette plateforme, veuillez nous contacter.';
									break;
								case 404:
									echo 'La page que vous demandez n\'existe pas ou plus. La plateforme a surement évolué et la page que vous cherchez a peu être été déplacée ou remplacée par une nouvelle encore plus jolie.
										Naviguez sur le site grâce au menu principal en haut de la page et vous serez sûr de trouver la fameuse page qui vous interesse tant.';
									break;
								case 5 :
									echo 'Un ou plusieurs paramètres de votre requête sont invalides. Merci de vérifier l\'adresse saisie dans le navigateur et recommencer.
										Si vous venez de cliquer sur un lien de cette plateforme, veuillez nous contacter.';
									break;
								default :
									echo 'Une erreur est survenue, si le problème persiste n\'hésitez pas à nous contacter.';
							}					
						?></td>
					</tr>
				
				</table>

				<div id="link"><a href="<?php echo $_GET['url'];?>">Retour à l'accueil</a></div>
			</div>
		</div>
	</body>
</html>