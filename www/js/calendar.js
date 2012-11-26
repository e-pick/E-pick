 /**
*** Logiciel E-Pick ***
 *
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
 *
 *
 * Calendar.js
 *
 */ 
  
$(document).ready(function() {	 
	var path = $('body').attr('path');
	
	
	
	$('.ligne_date').hover(function(){ 
		$(this).addClass('hover_ligne');
	}, function () {
		$(this).removeClass('hover_ligne');
	});
 
	$('.ligne_date').click(function(){		
		contenu_bulle($(this));
	});
	
	
	
	
	$('.affecte').hover(function(){  
		var id = $(this).attr('id');
		var position = $(this).position();
		$('#infopreps').remove();
		$('#cadre_planning').append('<div id="infopreps"></div>');  
		$('#infopreps').css("top",(position.top+20)+'px');
		$('#infopreps').css("left",(position.left+100)+'px'); 
		$('#infopreps').css("width",'180px'); 
		$('#infopreps').css("height",'70px');  
		$.ajax({ 
			type: "POST", 
			url: path+'planning/utilisateurscreneau/'+id,  			
			data: {  
					appel_ajax: 'appel_ajax'
			},
			dataType: 'html',	
			success: function(data){   
				$('#infopreps').html(data);
			} ,
			error: function(){
				alert('may be a bad path : ' +path+'planning/utilisateurscreneau/'+id);
			}
		});
	}, function () {		
		$('#infopreps').remove();
	});
	
	
	$('.duplicate').click(function(){ 
		var id = $(this).parent().attr('id');
		var position = $(this).parent().position();
		$('#infobulle').remove();
		$('#cadre_planning').append('<div id="infobulle"></div>');  
		 
		$('#infobulle').css("top",(position.top+20)+'px');
		$('#infobulle').css("left",(position.left+100)+'px'); 
		$('#infobulle').css("width",'300px'); 
		$('#infobulle').css("height",'50px');  
		
		
		 $.ajax({ 
			type: "POST", 
			url: path+'planning/dupliquerjour/'+id,  			
			data: {  
					appel_ajax: 'appel_ajax'
			},
			dataType: 'html',	
			success: function(data){  
				$('#infobulle').html(data);
				 $('#form_dupliquer').attr('action',path+'planning/dupliquerjour/'+id); 
				  
				$("#close_popup").click(function() {  
					$('#infobulle').remove();
						location.reload();
				});
				
			} ,
			error: function(){
				alert('may be a bad path : ' +path+'planning/dupliquerjour/'+id);
			}
		});
	});
	
	$('#heure_debut').change(function(){
		
	});
	
	
	
	function contenu_bulle(div){	
	  
		var id = div.attr('id');
		$('#infobulle').remove();
		$('#filter').remove();
			
		var position = div.parent().parent().position();

		$('body').append('<div id="filter">');
		$('#cadre_planning').append('<div id="infobulle"></div>');  
		$('#infobulle').css("top",'40%');
		$('#infobulle').css("left",'50%'); 
		$('#infobulle').css("width",'850px'); 
		$('#infobulle').css("height",'500px'); 
		$('#infobulle').css("margin-top",'-250px'); 
		$('#infobulle').css("margin-left",'-425px'); 
		$('#infobulle').css("overflow-y",'hidden'); 
		 $.ajax({ 
			type: "POST", 
			url: path+'planning/afficher/'+id,  			
			data: {  
					appel_ajax: 'appel_ajax'
			},
			dataType: 'html',	
			success: function(data){  
				text= data+'<br /><a href="#" id="close_popup" onclick="return false;">Fermer</a>'; 			
				$('#infobulle').html(text);
				  
				$("#close_popup").click(function() {  
					$('#infobulle').remove();
						location.reload();
				});
				 
				ajouter(div,id);
				supprimer(div,id);
				
			} ,
			error: function(){
				alert('may be a bad path :' +path+'planning/afficher/'+id);
			}
		});
	
	}
	
	
	function ajouter(div,id){
		$('#form_ajouter').submit(function() { 
			
			
			var idutilisateur = $('#utilisateur').val();
			var heure_fin = $('#heure_fin').val();	 
			var heure_debut = $('#heure_debut').val();	 
			$.ajax({ 
					type: "POST", 
					url: path+'planning/affecter/'+id,  			
					data: {  
							appel_ajax: 'appel_ajax',
							idutilisateur : idutilisateur,
							heure_debut : heure_debut,
							heure_fin : heure_fin
					},
					dataType: 'html',	
					success: function(data){  
							// alert(data);
						contenu_bulle(div); 
						return true;
					} ,
					error: function(){
						alert('may be a bad path :' +path+'planning/affecter/'+id);						
					}
				});
				return false;
		});	
	}
	function supprimer(div,id){
		$('.delete').click(function() { 
			if(confirm('Etes-vous sûr?')){
				var idutilisateur = $(this).attr('id');
				var heure_fin = $('#heure_fin_creneau').val();	 
				var heure_debut = $('#heure_debut').val();	  
				$.ajax({ 
						type: "POST", 
						url: path+'planning/supprimer/'+id,  			
						data: {  
								appel_ajax		: 'appel_ajax',
								idutilisateur	: idutilisateur,
								heure_debut 	: heure_debut,
								heure_fin 		: heure_fin
						},
						dataType: 'html',	
						success: function(data){ 
							// alert(data);
							contenu_bulle(div);  
							return true;
						} ,
						error: function(){
							alert('may be a bad path :' +path+'planning/supprimer/'+id);						
						}
					});
				return false;
			}
			else{
				return false;
			}
		});	
	}
	
	
	
	
	
	
	$('#heure_debut').change(function(){
		var dep = (parseInt($('#heure_debut').val())+1);
		var fin = parseInt($('#heure_fin').val());
		var txt = '';
		
		for(var i = dep; i <= 24; i++){
			txt += '<option value="'+i+'"';
			if(i == fin) txt += ' selected="true"';
			txt += '>'+i+'</option>';
		}
		
		$('#heure_fin').html(txt);
	});	
	
	$('#jour_debut').change(function(){
		var dep = parseInt($('#jour_debut').val());
		var fin = parseInt($('#jour_fin').val());
		var txt = '';
		
		for(var i = dep; i <= 7; i++){
			txt += '<option value="'+i+'"';
			if(i == fin) txt += ' selected="true"';
			txt += '>'+($('#jour_debut option[value='+i+']').text())+'</option>';
		}
		
		$('#jour_fin').html(txt);
	});
	
});