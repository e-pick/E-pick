 /**
*** Logiciel E-Pick ***
 *
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
 *
 * Modelisation.js
 *
 * Script javascript permettant d'afficher et de modéliser un magasin contenant un ou plusieurs étages
 * 
 */ 
  
$(document).ready(function() {		

/********************************************/
/********************************************/
/*				CONFIGURATION				*/
/********************************************/
/********************************************/

var classEtage 			= "etage";
var classCaisse 		= "caisse";
var idRes 				= "block_position";
var idSuppr_obs 		= "obstacle_suppr";
var idZoneDepart 		= "zone_depart";
var idZoneArrive 		= "zone_arrive";
var classRayonDraggable	= "rayon";
var classObstacle 		= "obstacle";
var idAjoutObstacle		= "ajouter_obstacle";   
var idInfoBulle 		= "infobulle";
var modification 		= false;
var save				= false;
var path 				= $('body').attr('path');
 
var echelle 			= 27;  // ! \\  ces valeurs doivent être identiques à celle du bootstrap.php
var largeursegment 		= 31;
var hauteursegment 		= 13;		  	
 
$('.boxy').boxy();
 
  
  
   
var EcranHaut = screen.height;
var EcranLarg = screen.width;
if(EcranLarg > 1900)
	$('#scrolletage').css('width','1750px');
else if(EcranLarg > 1600)
	$('#scrolletage').css('width','1450px');
else if(EcranLarg > 1400) 
	$('#scrolletage').css('width','1200px');
else if(EcranLarg > 1200)
	$('#scrolletage').css('width','1050px');
else if(EcranLarg > 1100)
	$('#scrolletage').css('width','800px');
else if(EcranLarg > 1000)
	$('#scrolletage').css('width','700px');
else
	$('#scrolletage').css('width','550px'); 
 
/*********************************************/
/*  		Sauvegarde générale				 */
/*********************************************/
     

function apply_ids_received_from_DB (dat) {
	console.log ('IDs received from DB "' + dat + '"');

	var id = dat.split(';');
	var compteur = 0;
	$('.'+classEtage).children('.'+classObstacle).each(function(){
		if ($(this).attr('id') == 0){
			$(this).attr('id',id[compteur]);
			compteur++;
		}
	});
}

$('#sauvegarder').click(function() {  
	save = true;
	$('#etape').children('li').each(function(){ //on réinitialise la box pour l'affiche de l'état de la sauvegarde
		$(this).css('color','#000'); 
		$(this).html($(this).text());
	});
	$('#load').html('<img src="'+path+'images/load.gif" alt="Merci de patienter" />');
	var original_op_1 = $('li#op_1').html();
	var original_op_2 = $('li#op_2').html(); 
  
  
   // display_children_position('.'+classEtage);
	 // setTimeout("$('.close').click()",3000); 
	// $('#valider').click();
		
	$.ajax({ //on fait un premier appel ajax pour le backup de la base de données
		type: "POST", 
		url: document.location.href,  	   
		data: {  				  
				backuper_db: 'backup'
		},
		success: function(){  // quand le backup est terminé, on check la ligne
			$('li#op_1').css('color','#999');
			str = "<del>" +original_op_1 + "</del> <img src=\""+path+"images/check.png\" alt=\"V\" />"
			$('li#op_1').html(str); 				
			display_children_position('.'+classEtage); 		
			  
			 $.ajax({  // on effectue un deuxieme appel ajax pour effectuer le reste de la sauvegarde, cela peut prendre plusieurs minutes (bcp de calculs)
				type: "POST", 
				url: document.location.href,  	   
				data: {  
						block_position: $("#"+idRes).val(),
						obstacle_suppr: $("#"+idSuppr_obs).val(),
						valider: $('#valider').val()
				},
				success: function(dat){  //au succes, on grise la ligne, et on ferme le popup automatiquement trois secondes après
						modification = false;
						apply_ids_received_from_DB (dat);
						$("#"+idRes).html('');
						$("#"+idSuppr_obs).html('');
						$('li#op_2').css('color','#999');
						str = "<del>" +original_op_2+ "</del> <img src=\""+path+"images/check.png\" alt=\"V\" />"
						$('li#op_2').html(str); 		
						$("#load").html('<img src=\"'+path+'images/check.png\" alt=\"V\" /><span class="gras"> Sauvegarde complète !</span><br /><span style="font-size:10px;">(Fermeture automatique dans 3 secondes)</span>');  
						$('.close').html('Fermer cette fenêtre');
						setTimeout("$('.close').click()",3000); 
					}
			}); 
		},
		error: function(){
			$("#"+idRes).html('');
			$("#"+idSuppr_obs).html('');
		}
    });  
	save = false;
});



setInterval(save_positions, 30000);
 
function save_positions(){ 
	if(modification == true && save == false){
		display_children_position('.'+classEtage); 
		 $.ajax({  
			type: "POST", 
			url: document.location.href,  	   
			data: {  
					block_position: $("#"+idRes).val(),
					obstacle_suppr: $("#"+idSuppr_obs).val(),
					save_position: 'save_position'
			},
			success: function(dat){ 
					modification = false;
					apply_ids_received_from_DB (dat);
					$("#"+idRes).html('');
					$("#"+idSuppr_obs).html('');
					$("#save_auto p").fadeIn("fast");
					$("#save_auto p").html('<img src=\"'+path+'images/check.png\" alt=\"V\" /> Positions sauvegardées');  
					setTimeout(fadeoutsave, 2000);
			},
			error : function(){
				$("#"+idRes).html('');
				$("#"+idSuppr_obs).html('');
			}
		}); 
	}
} 
function fadeoutsave(){
	$("#save_auto p").fadeOut("slow");
}
/**************************************************/
/* Alert en cas de modification de la modélisation*/
/**************************************************/

$('a').click(function(){
	if(modification && $(this).attr('id') != idAjoutObstacle && $(this).attr('id') != idInfoBulle && $(this).attr('id') != "sauvegarder" && $(this).attr('class') != 'atransferer' && $(this).attr('class') != 'reinit' && $(this).attr('class') != 'suppr_obstacle' && $(this).attr('class') != 'hover_obstacle'){
		return confirm('Vous avez effectué des modifications des blocs sans avoir calculer les nouveaux chemins. Etes-vous sûr de vouloir quitter cette page sans sauvegarder? Si vous voulez sauvegarder les chemins, cliquez sur sauvegarder en bas à côté de la légende.');
	}
});
 
 

/*********************************************/
/*  Transfert d'un rayon dans le div Etage	 */
/*********************************************/
function rayonToTransfer(){

	$(".rayonToTransfer a").unbind('click');
	$(".rayonToTransfer a").click(function() { 
		//on récupère les informations du rayon
		var largeur = $(this).parent().css('width');
		var hauteur = $(this).parent().css('height'); 
		var id 		= $(this).parent().attr('id').split('_');
		var idrayon = id[1]; 
		var type	= $(this).parent().attr('type_ray');
		var couleur = $(this).parent().css('border-bottom-color');
		if(type == "classique"){
			var bordure = "border-bottom: 2px dashed "+couleur ;
			var classes	= classRayonDraggable;
		}
		else{
			var bordure = "border: 2px dashed "+couleur ;
			var classes	= classRayonDraggable+" vrac";
		}
		var libelle = $(this).html(); 
		var top 	= $('.'+classEtage).parent().scrollTop(); //on récupère la position du scroll
		var left 	= $('.'+classEtage).parent().scrollLeft();
		//on ajoute le rayon à l'étage dans le coin supérieur gauche avec la bonne taille
		var string 	= '<div class="'+classes+'" sens="0" type_ray="'+type+'" top_deb="'+top+'" largeur_ray="'+parseInt(largeur)+'" hauteur_ray="'+parseInt(hauteur)+'" left_deb="'+left+'" id="id_'+idrayon+'" style="'+bordure+';width:'+largeur+';height:'+hauteur+';top:'+top+'px;left:'+left+'px;"><a href="#" class="rotation_rayon" onclick="return false;" style="display:block;float:left;margin-right:2px;"><img src="'+path+'images/rotate.png" style="width:12px;height:12px;" alt="R"/></a><a href="#" class="voir_info" onclick="return false;" style="display:block;float:left;"><img src="'+path+'images/info.png" style="width:12px;height:12px;" alt="I"/></a></div>';
		$('.'+classEtage).append(string);
	 

		//on le supprime de #rayons
		$(this).parent().remove(); 
		//on le rend draggable
		rayon_as_draggable($("#id_"+idrayon)); 
		//do_all();   
		//on lui actionne les manipulations possibles
		enable_rayon_for_manipulations();
		modification = true;
	});
}

rayonToTransfer();
/*********************************************/
/* Manipulation d'un rayon dans le div Etage */
/*********************************************/
function enable_rayon_for_manipulations(){ 

	$(".rayon a").unbind('click');
	$(".rayon a").click(function() {
	
		var action = $(this).attr('class'); //on récupère l'action a effectuer soit afficher les infos soit faire une rotation	 
		if(action.indexOf("rotation_rayon") != -1 ){ // on fait une rotation sur le rayon
			$('#'+idInfoBulle).remove(); //on supprime les infobulles existantes
			var sens = parseInt($(this).parent().attr('sens'));
			var prochainSens = ((sens + 45) % 360); 				
			$(this).parent().attr('sens',prochainSens);  //on met à jour l'attribut sens du rayon qui nous permet de connaitre l'inclinaison du rayon
			rotate_a_div($(this).parent(),prochainSens);  //on fait la rotation du rayon
			modification = true;	
		} 
		else if (action.indexOf("modifier_info") != -1){ 
			$('#'+idInfoBulle).remove(); //on supprime les infobulles existantes
			var position = $(this).parent().position(); //on récupère la position du rayon
			//on crée l'infobulle
			var id 		 = $(this).parent().attr('id').split('_');
			$('.'+classEtage).append('<div id="infobulle" class="'+id[1]+'"></div>');
			$('#'+idInfoBulle).css("top",(position.top + 15)+'px');
			$('#'+idInfoBulle).css("left",(position.left +15)+'px'); 
			$.ajax({ 
				type: "POST", 
				url: path+'rayon/editer/'+id[1],  			
				data: {  
						appel_ajax: 'appel_ajax'
				},
				dataType: 'html',	
				success: function(data){ 								
					$('#'+idInfoBulle).html(data); //on charge le contenu dans l'info bulle
					 
					$("#close_popup").click(function() {   //possibilité de la fermer en cliquant sur le lien fermer
						$('#'+idInfoBulle).remove();
					});
					
					$("#modifier_nom").click(function(){ //possibilité de modifier nom du rayon
						$.ajax({  // on effectue un deuxieme appel ajax pour effectuer la sauvegarde
							type: "POST", 
							url: path+'rayon/editer/'+id[1],  	   
							data: {  
									valider: 'valider',
									idrayon: $('#idRayon').val(),
									libelle:  $('#libelle').val(),
									position_x:  $('#position_x').val(),
									position_y:  $('#position_y').val(),
									largeur:  $('#largeur').val(),
									hauteur:  $('#hauteur').val(),
							},
							success: function(data){ 
								
								var idrayon 	= parseInt($('#'+idInfoBulle).attr('class'));
								var libelle 	= $('#libelle').val(); 
								var top_deb		= $('#position_y').val();
								var left_deb	= $('#position_x').val();
								var largeur_ray	= $('#largeur').val();
								var hauteur_ray	= $('#hauteur').val();
								
								$('#'+idInfoBulle).html(data); //on charge le contenu dans l'info bulle
								$('#id_'+idrayon).attr('top_deb',top_deb); 
								$('#id_'+idrayon).attr('left_deb',left_deb); 
								$('#id_'+idrayon).attr('largeur_ray',largeur_ray); 
								$('#id_'+idrayon).attr('hauteur_ray',hauteur_ray); 
								$('#id_'+idrayon).css({top: top_deb+'px', left:left_deb+'px', width:largeur_ray+'px', height:hauteur_ray+'px'}); 
								$('#libelle_'+idrayon).html(libelle); 
								$('#'+idInfoBulle).remove();
							}
						});
					});
				}
			});
		}
		else{  
			$('#'+idInfoBulle).remove(); //on supprime les infobulles existantes		
			var position = $(this).parent().position(); //on récupère la position du rayon
			//on crée l'infobulle
			var id 		 = $(this).parent().attr('id').split('_');
			$('.'+classEtage).append('<div id="infobulle" class="'+id[1]+'"></div>');
			$('#'+idInfoBulle).css("top",(position.top + 15)+'px');
			$('#'+idInfoBulle).css("left",(position.left +15)+'px'); 
			//on fait une requête ajax pour charger le contenu
			 $.ajax({ 
				type: "POST", 
				url: path+'rayon/afficher/'+id[1],  			
				data: {  
						appel_ajax: 'appel_ajax'
				},
				dataType: 'html',	
				success: function(data){ 								
					$('#'+idInfoBulle).html(data); //on charge le contenu dans l'info bulle
					 
					$("#close_popup").click(function() {   //possibilité de la fermer en cliquant sur le lien fermer
						$('#'+idInfoBulle).remove();
					});
					
					$("#demodeliser").click(function(){ //possibilité de dé-modéliser un rayon
						//on récupère les infos du rayon
						modification 	= true;
						var idrayon 	= parseInt($('#'+idInfoBulle).attr('class')); 
						var type_ray 	= $('#id_'+idrayon).attr('type_ray'); 
						var libelle 	= $(this).attr('libelle'); 
						var sens 		= 0;
						var top_deb		= '*';
						var left_deb	= '*';
						if(type_ray == "classique"){
							var couleur =  $('#id_'+idrayon).css('border-bottom-color');
							var bordure = "border-bottom: 2px dashed "+couleur ;
							var classes	= "rayonToTransfer";
							if(parseInt($('body').attr('finesse')) == 1){
								var largeur_ray = 100;
								var hauteur_ray = hauteursegment;
							}
							else{
								var largeur_ray = parseInt($('#id_'+idrayon).attr('largeur_ray'));
								var hauteur_ray = parseInt($('#id_'+idrayon).attr('hauteur_ray'));
							}
						}
						else{
							var couleur =  $('#id_'+idrayon).css('border-bottom-color');
							var bordure		= "border: 2px dashed "+couleur ;
							var classes		= "rayonToTransfer";
							var largeur_ray = 40;
							var hauteur_ray = 40;
						}
						//on supprimer l'info bulle et le rayon
						$('#'+idInfoBulle).remove();
						$('#id_'+idrayon).remove(); 
						//on crée un rayon dans la liste des rayons à modéliser
						$('#rayons').append('<div id="id_'+idrayon+'" class="'+classes+'" type_ray="'+type_ray+'"  style="width:'+largeur_ray+'px;height:'+hauteur_ray+'px;'+bordure+'"><a href="#" onclick="return false;" title="'+libelle+'" class="atransferer">'+libelle+'</a></div>'); 
						
						//on précise dans le textarea pour la sauvegarde que ce rayon doit etre démodéliser
						var currentHtml = $("#"+idRes).html();							
						if(currentHtml != '')
							$("#"+idRes).html(currentHtml+";" + "rayon" + "-" + idrayon + "-"+top_deb+"-"+ left_deb + "-"+sens+"-"+ hauteur_ray+"-"+ largeur_ray+"-"+type_ray);
						else
							$("#"+idRes).html("rayon" + "-" +idrayon + "-"+ top_deb+"-"+left_deb  + "-"+sens+"-"+ hauteur_ray+"-"+ largeur_ray+"-"+type_ray);
						//on l'autorise à nouveau à être transféré
						rayonToTransfer();
						
					});
				} 
			}); 
		}
	});
}


 

/*********************************************/
/*		 Rend tous les rayons déplacable 	 */
/*********************************************/
function do_all_rayon_as_draggable(div){

	if(div == null){	
		$('.'+classRayonDraggable).each(function(){
			rayon_as_draggable($(this));
		});
	}
	else{ 
		rayon_as_draggable(div);
	}
						
} 

function rayon_as_draggable(div){

	//POSSIBILITE DE REDIMENSIONNER LES RAYONS SI FINESSE RAYON SI SEULEMENT SENS == 0
	if(div.attr('sens') == 0){	
		if(parseInt($('body').attr('finesse')) >= 1 || div.attr('type_ray') == 'vrac'){
			div.resizable({					
				start: function(event, ui){	
					modification = true;		
				},
				stop: function(event, ui){
					var h 		= parseInt(div.css('height'));
					var l 		= parseInt(div.css('width'));
					var sens 	= (parseInt(div.attr('sens'))*Math.PI/180);		
					var v_left 	= ((-h * Math.sin(sens)) + ((l/2) * Math.cos(sens)) + ((h/2) * Math.sin(sens)));
					var v_top 	= ((h*Math.cos(sens)) + ((l/2) * Math.sin(sens)) - ((h/2)* Math.cos(sens)));
					if(!$.browser.msie)
						div.draggable( "option", "cursorAt", {  top: v_top, left: v_left } ); 
											
					//on met à jour les attributs du rayon apres le resize				
					div.attr('hauteur_ray',h);
					div.attr('largeur_ray',l);					
					coordonnees = top_left(div,div.position().top,div.position().left);
					div.attr('top_deb',coordonnees[0]);    
					div.attr('left_deb',coordonnees[1]); 
					
					// alert(coordonnees[0] + ', '+ coordonnees[1]);
					  
				}
			});		
			//on définit les caractéristiques du resize (hauteur min, hauteur max, largeur min, grille) selon le type de rayon
			if(div.attr('type_ray') == 'classique'){
				div.resizable( "option", "minHeight", hauteursegment );
				div.resizable( "option", "maxHeight", hauteursegment );
				div.resizable( "option", "minWidth", largeursegment ); 
			}
			else{
				div.resizable( "option", "minHeight", echelle ); 
				div.resizable( "option", "minWidth", echelle ); 
				div.resizable( "option", "grid", [(echelle/2),(echelle/2)] );  
			}
		}
		else
			div.resizable( "destroy" );  //aucun resize autorisé sinon 
	} 
	else
		div.resizable( "destroy" ); //aucun resize autorisé sinon 
	
	
	// CALCUL du cadre du draggable pour pas que le rayon sort de la grille
	var posTop 	= $('.'+classEtage).parent().offset().top+ 5;
	var posLeft = $('.'+classEtage).parent().offset().left +5; 	
	
	if(parseInt($('.'+classEtage).css('height')) > parseInt($('.'+classEtage).parent().css('height')))
		var posBottom = posTop + parseInt($('.'+classEtage).parent().css('height')) - 40;
	else
		var posBottom = posTop + parseInt($('.'+classEtage).css('height')) -6;
		
	
	if(parseInt($('.'+classEtage).css('width')) >parseInt($('.'+classEtage).parent().css('width')))
		var posRight = posLeft + parseInt($('.'+classEtage).parent().css('width')) - 40;
	else
		var posRight = posLeft + parseInt($('.'+classEtage).css('width'))- 6;
		
	//hack pour le bug ie des hauteurs et des largeurs après transform
	if($.browser.msie){			
		var hauteur = parseInt(div.attr('hauteur_ray'));
		var largeur = parseInt(div.attr('largeur_ray'));
	
	}else{	
		var hauteur = parseInt(div.css('height'));
		var largeur = parseInt(div.css('width'));
	} 
	var sens = parseInt(div.attr('sens'));
	var type = parseInt(div.attr('type'));
	 
	
	var x1;
	var y1;
	var x2;
	var y2;
	
	
	var rad = (sens*Math.PI/180);
							
	switch (sens){
		case 0: 
			x1 = posLeft;
			y1 = posTop;
			x2 = posRight - largeur;
			y2 = posBottom - hauteur;
			break;
		case 45:
			x1 = posLeft + hauteur*(Math.sin(rad));
			y1 = posTop;
			x2 = posRight - largeur*(Math.cos(rad));
			y2 = posBottom - (hauteur + largeur)*(Math.sin(rad));
			break;
		case 90:
			x1 = posLeft + hauteur;
			y1 = posTop;
			x2 = posRight;
			y2 = posBottom - largeur;			
			break;
		case 135:
			x1 = posLeft + (hauteur + largeur)*(Math.sin(rad));
			y1 = posTop - hauteur*(Math.cos(rad));
			x2 = posRight;
			y2 = posBottom - largeur * (Math.sin(rad));
			break;
		case 180: 
			x1 = posLeft + largeur;
			y1 = posTop + hauteur;
			x2 = posRight;
			y2 = posBottom;
			break;
		case 225:
			x1 = posLeft - largeur*(Math.cos(rad));
			y1 = posTop - (hauteur + largeur)*(Math.cos(rad));
			x2 = posRight + hauteur*(Math.cos(rad));
			y2 = posBottom;
			break;
		case 270:
			x1 = posLeft;
			y1 = posTop + largeur;
			x2 = posRight - hauteur;
			y2 = posBottom;
			break;
		case 315:
			x1 = posLeft;
			y1 = posTop + (largeur)*(Math.cos(rad));
			x2 = posRight - (hauteur + largeur)*(Math.cos(rad));
			y2 = posBottom - (hauteur)*(Math.cos(rad));
			break;
		default:
			break;
	}
	//on le rend draggable
	// alert(div.attr('id'));
	div.draggable({  
			grid:[1,1],
			cursor: 'pointer',
			start: function(event, ui) {				
				modification = true;
				$('#'+idInfoBulle).remove(); //on supprime les infobulles existantes
			},
			stop: function(event, ui){ 			 
				coordonnees = top_left($(this),$(this).position().top,$(this).position().left); 
				$(this).attr('top_deb',coordonnees[0]);    
				$(this).attr('left_deb',coordonnees[1]); 	

					// alert(coordonnees[0] + ', '+ coordonnees[1]);				
	 		}
	});
	//on met à jour le cadre calculé
	if($.browser.msie)	
		div.draggable( "option", "containment", 'parent' );
	else
		div.draggable( "option", "containment",  [x1,y1,x2,y2] );
	
	//on met le cursor au centre du rayon
	 if(!$.browser.msie){
		var l 		= parseInt(div.css('width'));
		var h 		= parseInt(div.css('height'));
		var sens 	= (parseInt(div.attr('sens'))*Math.PI/180);		
		var v_left 	= ((-h * Math.sin(sens)) + ((l/2) * Math.cos(sens)) + ((h/2) * Math.sin(sens)));
		var v_top 	= ((h*Math.cos(sens)) + ((l/2) * Math.sin(sens)) - ((h/2)* Math.cos(sens)));
		
		div.draggable( "option", "cursorAt", {  top: v_top, left: v_left } ); 
	}
}
 

/*********************************************/
/*	 Affiche dans le textarea les positions	 */
/*********************************************/
// ! \\ ATTENTION, la mise en forme des informations est directement utilisée dans le script php, si vous vous aventurez
// ! \\ à changer la méthode de sauvegarde, pensez à le faire aussi dans le modelisationController.php 

function from_DOM_id_to_DB_id (id) {
    if (id == '0')
        // A brand new item that the DB has not yet assigned an ID to
        return id;

    var a = id.split ('_');

    if (a.length != 2) {
        console.log ('Malformed DOM id "' + id + '"');
        return id;
    }

    // TODO validate a[0] is "obs", "id", ...
    return a [1];
}

function display_children_position(node){ 

	   $(node).children('.'+classRayonDraggable).each(function(){
		var currentHtml = $("#"+idRes).html();
		var position = $(this).position();	
		var sens = $(this).attr('sens');
		var type = $(this).attr('type_ray');
		
		var id = from_DOM_id_to_DB_id ($(this).attr('id'));
		if(currentHtml != '')
			$("#"+idRes).html(currentHtml+";" + "rayon" + "-" + id + "-"+$(this).attr('top_deb')+"-"+ $(this).attr('left_deb') + "-"+sens+"-"+ $(this).attr('hauteur_ray')+"-"+ $(this).attr('largeur_ray')+"-"+type);
		else
			$("#"+idRes).html("rayon" + "-" + id + "-"+ $(this).attr('top_deb')+"-"+$(this).attr('left_deb')  + "-"+sens+"-"+ $(this).attr('hauteur_ray')+"-"+ $(this).attr('largeur_ray')+"-"+type);
	  });
	  
	  $(node).children('.'+classObstacle).each(function(){
		var currentHtml = $("#"+idRes).html();
		var position = $(this).position();	 			
		var height = parseInt($(this).css("height"));
		var width = parseInt($(this).css("width"));  
		var libelle = $(this).attr('libelle'); 
		var id = from_DOM_id_to_DB_id ($(this).attr('id'));
		if(currentHtml != '')
			$("#"+idRes).html(currentHtml+";" + "obstacle" + "-" + id + "-"+ parseInt(position.top)+"-"+ parseInt(position.left)+"-"+width+"-"+height+"-"+libelle);
		else
			$("#"+idRes).html("obstacle" + "-" +id + "-"+ parseInt(position.top)+"-"+ parseInt(position.left)+"-"+width+"-"+height+"-"+libelle);
	  });
	  
	  $(node).children('.'+classCaisse).each(function(){
		var currentHtml = $("#"+idRes).html();
		var position = $(this).position();	 						
		var height = parseInt($(this).css("height"));
		var width = parseInt($(this).css("width"));  

		if(currentHtml != '')
			$("#"+idRes).html(currentHtml+";" + "caisse" + "-" + $(this).attr('id') + "-"+ parseInt(position.top)+"-"+ parseInt(position.left) +"-"+width+"-"+height+"-caisse");
		else
			$("#"+idRes).html("caisse" + "-" +$(this).attr('id') + "-"+ parseInt(position.top)+"-"+ parseInt(position.left)+"-"+width+"-"+height+"-caisse");
	  });
	  
	   
		var height = parseInt($(node).css("height"));
		var width = parseInt($(node).css("width"));
		var ptd = $('#zone_depart').position(); 
		var pta = $('#zone_arrive').position(); 
		var etage = $(node).attr('id'); 
		var currentHtml = $("#"+idRes).html(); 
		
		 if(currentHtml != '')
			$("#"+idRes).html(currentHtml+";etage-" + etage + "-" + width + "-" + height+"-"+parseInt(ptd.top)+"-"+parseInt(ptd.left)+"-"+parseInt(pta.top)+"-"+parseInt(pta.left));
		else
			$("#"+idRes).html("etage-" + etage + "-"+width+"-"+height+"-"+parseInt(ptd.top)+"-"+parseInt(ptd.left)+"-"+parseInt(pta.top)+"-"+parseInt(pta.left));					
}

 
 
/*********************************************/
/*			Calcul de la superficie			 */
/*********************************************/
 function superficie(){
	heightpx 	= parseInt($('.'+classEtage).css('height'));
	widthpx 	= parseInt($('.'+classEtage).css('width'));	
	heightm 	= parseInt(heightpx / echelle);
	widthm 		= parseInt(widthpx / echelle); 
	$('#superficie').html('L : '+widthm + 'm H : '+heightm+'m <br />&raquo; '+ (widthm*heightm) +'m²')
 }
 superficie();

/*********************************************/
/*			TRAITEMENT DES OBSTACLES		 */
/*********************************************/

$("#"+idAjoutObstacle).click(function() {  
	//on ajoute lobstacle à l'étage dans le coin supérieur gauche   
	var name 	= prompt('Définir un libellé pour l\'obstacle : ','obstacle');
	var top 	= $('.'+classEtage).parent().scrollTop();
	var left 	= $('.'+classEtage).parent().scrollLeft();
	$('.'+classEtage).append('<div class="'+classObstacle+'" id="0" libelle="'+name+'" style="top:'+top+'px;left:'+left+'px;background-color:#F6E5A7;"><a href="#" onclick="return false;" class="suppr_obstacle"><img src="'+path+'images/delete.png" style="width:12px;height:12px;" alt="D"/></a><a href="#" class="hover_obstacle" onclick="return false;" ><img src="'+path+'images/info.png" style="width:10px;height:10px;" alt="I"/></a></div>');
	
	modification = true;	  
	do_all();
	suppr_obstacle();
	hover_obstacle();
/*	modifier_obstacle(); Fonction à revoir car duplique les obstacle */ 
}); 


function suppr_obstacle(){ //suppression d'un obstacle
	$(".suppr_obstacle").click(function() {   
		if(confirm('Etes-vous sûr de vouloir supprimer l\'obstacle')){
			var currentHtml = $("#"+idSuppr_obs).html();
			var idobstacle 	= $(this).parent().attr('id');
								
			 if(currentHtml != '')
				$("#"+idSuppr_obs).html(currentHtml+";" +idobstacle);
			else
				$("#"+idSuppr_obs).html(idobstacle);
			
			modification = true;
			$(this).parent().remove();
		} 
	}); 

}


function hover_obstacle(){ //affiche la superficie ainsi que le libelle de l'obstacle à son survol 
	$('.hover_'+classObstacle).hover(function(){   
		var libelle = $(this).parent().attr('libelle');
		var position = $(this).parent().position();
		$('#infopreps').remove();
		$('.'+classEtage).append('<div id="infopreps"></div>');  
		$('#infopreps').css("top",(position.top+10)+'px');
		$('#infopreps').css("left",(position.left+10)+'px'); 
		$('#infopreps').css("width",'150px'); 
		$('#infopreps').css("height",'30px');  
		$('#infopreps').css("overflow-y","hidden");  
		 
		var hauteur		= parseInt($(this).parent().css('height'))/echelle;
		var largeur		= parseInt($(this).parent().css('width'))/echelle;
		var superficie	= Math.round(hauteur*largeur);
		$('#infopreps').html('obstacle : '+ libelle+'<br />superficie : '+superficie+'m²');
		
	}, function () {		
		$('#infopreps').remove();
	});
}	

function modifier_obstacle(){
	$(".modifier_obstacle").click(function() {  
		$('#'+idInfoBulle).remove(); //on supprime les infobulles existantes
		var position = $(this).parent().position(); //on récupère la position de l'obstacle
		//on crée l'infobulle
		var id 		 = $(this).parent().attr('id').split('_');
		$('.'+classEtage).append('<div id="infobulle" class="'+id[1]+'"></div>');
		$('#'+idInfoBulle).css("top",(position.top + 15)+'px');
		$('#'+idInfoBulle).css("left",(position.left +15)+'px'); 
		$.ajax({ 
			type: "POST",
          	cache: false, 
			url: path+'obstacle/editer/'+id[1],  			
			data: {  
					appel_ajax: 'appel_ajax'
			},
			dataType: 'html',	
			success: function(data){ 								
				$('#'+idInfoBulle).html(data); //on charge le contenu dans l'info bulle
				var myPicker = new jscolor.color(document.getElementById('couleur'), {})
				
				$("#close_popup").click(function() {   //possibilité de la fermer en cliquant sur le lien fermer
					$('#'+idInfoBulle).remove();
				});
				
				$("#modifier_couleur").click(function(){ //possibilité de modifier nom du rayon
						$.ajax({  // on effectue un deuxieme appel ajax pour effectuer la sauvegarde
							type: "POST", 
							url: path+'obstacle/editer/'+id[1],  	   
							data: {  
									valider: 'valider',
									idObstacle: $('#idObstacle').val(),
									couleur:  $('#couleur').val(),
							},
							success: function(data){ 
								
								var idobstacle 	= parseInt($('#'+idInfoBulle).attr('class'));
								var couleur 	= $('#couleur').val(); 
								
								$('#'+idInfoBulle).html(data); //on charge le contenu dans l'info bulle
								$('#obs_'+idobstacle).css("background-color","#"+couleur);
								$('#'+idInfoBulle).remove();
							}
						});
					});
			}
		});
	});
}
function do_all_obstacle_as_draggable_and_resizable(){
  
	$('.'+classObstacle).draggable({
		containment: 'parent',
		grid:[2,2],
		start: function(event, ui){
			modification = true; 
		}
	});
	$('.'+classObstacle).resizable({
		minHeight: echelle-1,
		minWidth: echelle-1,
		start: function(event,ui){
			modification = true;
		},
		stop: function(event, ui){ //on vérifie que l'obstacle ne sort pas du cadre sinon on rectifie
			var position = $(this).position();
			var height = parseInt($(this).css("height"));
			var width = parseInt($(this).css("width"));
			
			var positionParent = $(this).parent().position();
			var heightParent = parseInt($(this).parent().css("height"));
			var widthParent = parseInt($(this).parent().css("width"));
			
			if ( (position.left + width) > widthParent && (position.top + height) > heightParent ) {
				$(this).css("width", widthParent - position.left +"px");
				$(this).css("height", heightParent - position.top +"px");
			}
			else if ((position.left + width) > widthParent) {
				$(this).css("width", widthParent - position.left +"px");
			}
			else if ((position.top + height) > heightParent) {
				$(this).css("height", heightParent - position.top +"px");
			}
		}
	});
	 
}

function load_caisses(){
	$('.'+classCaisse).draggable({ 
		containment: 'parent',
		grid:[2,2],
		start: function(event, ui){
			modification = true;
		}
	});
	
	$('.'+classCaisse).resizable({
		stop: function(event, ui){ 								
			modification = true;
		} 
	}); 
}


/*********************************************/
/*	 CHARGEMENT INITIAL DE LA MODELISATION	*/
/*********************************************/
function do_all(){
	
	$('#'+idZoneDepart).draggable({ 
		containment: 'parent',
		grid:[2,2],
		start: function(event, ui){
			modification = true;
		}
	});
	
	$('#'+idZoneArrive).draggable({ 
		containment: 'parent',
		grid:[2,2],
		start: function(event, ui){
			modification = true;			 
		}
	});

	$('.'+classEtage).resizable({
		minHeight:100,
		minWidth:100,
		stop: function(event, ui){
			do_all_rayon_as_draggable(null);
			modification = true;		
			superficie();
		}
	});   
		
	hover_obstacle();
	/*	modifier_obstacle(); Fonction à revoir car duplique les obstacle */
	load_caisses();
	do_all_obstacle_as_draggable_and_resizable();
	// do_all_rayon_as_draggable(null);
} 

	do_all();
	suppr_obstacle(); 
	enable_rayon_for_manipulations();
 
	//on incline tous les rayons au départ
	$('.'+classRayonDraggable).each(function(){
		if($(this).attr('sens') != '0')
			rotate_a_div($(this),$(this).attr('sens'));
		else
			do_all_rayon_as_draggable($(this));
			
	});
 
   $('.'+classEtage).mousemove(function(e){
	var decalageY = $(this).offset().top
	var decalageX = $(this).offset().left
	if($.browser.mozilla)
		decalageX += 0.79998779296875;
      $('#debug').html('top'+ (e.pageY-decalageY) + ' left ' + (e.pageX-decalageX) );
   }); 
 
 

 
/**********************************************/
/* ROTATION DES DIVS SOUS MSIE, WEBKIT, MOZ, O*/
/**********************************************/

 function rotate_a_div(div,degree){
 	if($.browser.msie){
		// use parseFloat twice to kill exponential numbers and avoid things like 0.00000000
		var rad = degree * (Math.PI/180),
			costheta = parseFloat(parseFloat(Math.cos(rad)).toFixed(8)),
			sintheta = parseFloat(parseFloat(Math.sin(rad)).toFixed(8));
		
		// collect all of the values  in our matrix
		var a = costheta,
			b = sintheta,
			c = -sintheta,
			d = costheta,
			tx = 0,
			ty = 0;
		
		// Transform the element in IE
		div.css({"filter":'progid:DXImageTransform.Microsoft.Matrix(M11=' + a + ', M12=' + c + ', M21=' + b + ', M22=' + d + ', sizingMethod=\'auto expand\')'});
		
		 // use the Sylvester $M() function to create a matrix
		var matrix = $M([
		  [a, c, tx],
		  [b, d, ty],
		  [0, 0, 1]
		]);

		transformOrigin(div, matrix,div.attr('top_deb'),div.attr('left_deb'));
		fixIeBoundaryBug(div, matrix);
		 
	}
	else if($.browser.webkit || $.browser.opera){  
		div.css({ '-o-transform-origin': '0 0' });
		div.css({ '-webkit-transform-origin': '0 0' });
		div.css({ '-webkit-transform': 'rotate('+degree+'deg)'});
		div.css({ '-o-transform': 'rotate('+degree+'deg)'});
	}
	else{		
		div.css({ '-moz-transform-origin': '0 0' });
		div.css({ '-ms-transform-origin': '0 0' });
		div.css({ '-moz-transform': 'rotate('+degree+'deg)'});
		div.css({ '-ms-transform': 'rotate('+degree+'deg)'});	
	} 
	do_all_rayon_as_draggable(div);
 
 } 

//permet d'avoir un top left identique apres une transfomation pour tout les navigateurs
function top_left(div,top_init,left_init){
	if($.browser.msie || $.browser.webkit || $.browser.opera){    
		var top 			= parseInt(top_init);
		if ($.browser.msie)
			var largeurrayon 	= parseInt(div.attr('largeur_ray'));
		else 
			var largeurrayon 	= parseInt(div.css('width')); 
		var left 			= parseInt(left_init);
		var sens 			= parseInt(div.attr('sens'));		
		var rad 			= (sens*Math.PI/180);
		
		hauteur = parseInt(div.attr('hauteur_ray'));
		
		switch (sens){
			case 0: 
				break;
			case 45:
				if($.browser.msie && $.browser.version=="6.0")
					left += 1;
				left += (parseInt((hauteur*Math.sin(rad))) + 3);
				break;
			case 90:
				if($.browser.msie && $.browser.version=="6.0")
					left += 2;
				left += (parseInt(hauteur+1) + 2); 
				break;
			case 135:
				if($.browser.msie && $.browser.version=="6.0"){
					left += 1;
					top  += 1;
				}
				left += (parseInt((Math.sin(rad) * (hauteur + largeurrayon))) + 5);
				top  += (parseInt((hauteur*Math.sin(rad))) + 3);
				break;
			case 180:
				if($.browser.msie && $.browser.version=="6.0")
					top  += 2;
				left += (largeurrayon + 2);		
				top +=  (hauteur + 3);
				break;
			case 225:
				if($.browser.msie && $.browser.version=="6.0")
					top  += 1;
				left += (parseInt(-(largeurrayon*Math.cos(rad))) + 3);
				top  += (parseInt(-(hauteur*Math.cos(rad) + largeurrayon*Math.sin(rad))) + 5);
				break;
			case 270:
				top  += (largeurrayon + 2); 
				break;
			case 315:
				top  += (parseInt((largeurrayon*Math.cos(rad))) + 3);  
				break;
		}
	}
	else{		
	
		top = top_init;    
		left = left_init; 
	} 							
	return new Array(top,left);
}
    
 
function transformOrigin(div, matrix,top,left) {
    // undo the filter
    var filter = div.css('filter');
   div.css('filter','');
    
    // measure the element
    var width = div.outerWidth();
    var height = div.outerHeight();
    
    // re-do the filter
    div.css('filter', filter);
    
    // The destination origin
    toOrigin = {
        x: height *0,
        y: width * 0
    };
    
    // The original origin
    fromOrigin = {
        x: 0,
        y: 0
    };
    
    // Multiply our rotation matrix against an x, y coord matrix
    var toCenter = matrix.x($M([
        [toOrigin.x],
        [toOrigin.y],
        [1]
    ]));
    var fromCenter = matrix.x($M([
        [fromOrigin.x],
        [fromOrigin.y],
        [1]
    ]));
    
    // Position the element
    // The double parse float simply keeps the decimals sane
    div.css({
        position: 'absolute',
        top: (parseFloat(parseFloat((fromCenter.e(2, 1) - fromOrigin.y) - (toCenter.e(2, 1) - toOrigin.y)).toFixed(8))+top )+ 'px',
        left: (parseFloat(parseFloat((fromCenter.e(1, 1) - fromOrigin.x) - (toCenter.e(1, 1) - toOrigin.x)).toFixed(8))+left ) + 'px'
    });
}

function fixIeBoundaryBug(div, matrix) {
    // undo the filter
    var filter = div.css('filter');
   div.css('filter','');
    
    // measure the element
    var x = div.outerWidth();
    var y = div.outerHeight();
    
    // re-do the filter
    div.css('filter', filter); 
   // create corners for the original element
      var matrices = {
        tl: matrix.x($M([[0], [0], [1]])),
        bl: matrix.x($M([[0], [y], [1]])),
        tr: matrix.x($M([[x], [0], [1]])),
        br: matrix.x($M([[x], [y], [1]]))
    };
            
    var corners = {
        tl: {
            x: parseFloat(parseFloat(matrices.tl.e(1, 1)).toFixed(8)),
            y: parseFloat(parseFloat(matrices.tl.e(2, 1)).toFixed(8))
        },
        bl: {
            x: parseFloat(parseFloat(matrices.bl.e(1, 1)).toFixed(8)),
            y: parseFloat(parseFloat(matrices.bl.e(2, 1)).toFixed(8))
        },
        tr: {
            x: parseFloat(parseFloat(matrices.tr.e(1, 1)).toFixed(8)),
            y: parseFloat(parseFloat(matrices.tr.e(2, 1)).toFixed(8))
        },
        br: {
            x: parseFloat(parseFloat(matrices.br.e(1, 1)).toFixed(8)),
            y: parseFloat(parseFloat(matrices.br.e(2, 1)).toFixed(8))
        }
    };
    
    // Initialize the sides
    var sides = {
        top: 0,
        left: 0
    };
    
    // Find the extreme corners
    for (var pos in corners) {
        // Transform the coords
        var corner = corners[pos];
        
        if (corner.y < sides.top) {
            sides.top = corner.y;
        }
        if (corner.x < sides.left) {
            sides.left = corner.x;
        }
    }
    
    // find the top and left we set earlier (the hard way)
	div.wrap('<div style="position: absolute" />');
    var pos = div.position();
	div.unwrap();
    
    // Position the element
    div.css({
        top: pos.top + sides.top,
        left: pos.left + sides.left
    });
 }


 });
			 
