 /**
*** Logiciel E-Pick ***
 *
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
 * Affectation.js
 *
 * Script javascript 
 *
 */ 
  
$(document).ready(function() {
	var path = $('body').attr('path');
	setTimeout(fadeout, 2000); 
	function fadeout(){         
			$('.success_ux').fadeOut(2000, function () {});
	}
	
	$(".selectAll").click(function(){
		var id = $(this).attr('value').split('_');		
		var erreur = false;
		var state = this.checked;
		if(this.checked == true){
			$(".check").each(function()	{				
				var id2 = $(this).attr('value').split('_');		
				if(this.checked == true){	
					if(id[0] != id2[0]){
						erreur = true;
						return false;
					}
				} 
			});	
		
			if(erreur){
				this.checked = false;
				alert("Impossible de selectionner des éléments sur plusieurs étages.");
				return false;
			} 	
		}
		$(".check").each(function()	{				
			var id2 = $(this).attr('value').split('_');	
			if(id[0] == id2[0]){
				this.checked = state;
			}
		
		});	
	});
		
	$(".check").click(function(){
		var erreur = false;
		var all_checked = true;
		var id = $(this).attr('value').split('_');
		if(this.checked == true){
			$(".check").each(function()	{				
				var id2 = $(this).attr('value').split('_');		
				if(this.checked == true){	
					if(id[0] != id2[0]){
						erreur = true;
						return false;
					}
				}
				else{
					if(id[0] == id2[0] && $(this).attr('class') != 'check selectAll'){
						all_checked = false;					
					}
				}
			});	
		
			if(erreur){
				this.checked = false;
				alert("Impossible de selectionner des éléments sur plusieurs étages.");
			} 
		}
		else if(this.checked == false){
			all_checked = false;
		} 
		if(!erreur){
			$(".selectAll").each(function()	{
				var id2 = $(this).attr('value').split('_');
				if(id[0] == id2[0]){ 
					this.checked = all_checked;					
				}
			});
		}
		
	});
		 
	
	$('.radio').click(function(){
		
		$('#sumitForm').click();
	});
	
	
	$('.submitListeCommande').click(function(){
		var nb_coms = 0;
		$('.check').each(function(){
			if (this.checked) nb_coms++;
		});
		
		if(nb_coms == 0){
			alert('Merci de sélectionner au moins une commande');
			return false;
		}
		
		$(this).css('display','none');
		$(this).parent().append('<img src="' + $('body').attr('path') + 'images/spinner.gif" style="height:18px;width:18px;" alt="chargement" />');
		setTimeout(function(){ $('.submitListeCommandeValid').click()},500) ;
	});
	
	$('.submitExport').click(function(){
		var nb_coms = 0;
		$('.check').each(function(){
			if (this.checked) nb_coms++;
		});
		
		if(nb_coms == 0){
			alert('Merci de sélectionner au moins une preparation');
			return false;
		}
		
		if(!$('#exportPDA').is(':checked') && !$('#exportPDF').is(':checked')){
			alert('Merci de sélectionner au moins un mode d\'export');
			return false;
		}
		
	});
	
	$('#submitChoixUtilisateur').click(function(){
	
		nb_bons = parseInt($('#nb_bons').html());
		if(nb_bons == 0){
			alert('Merci de sélectionner au moins un bon de préparation');
			return false;
		}
	});
	
	$('#submit_user').click(function(){
	
		var nb_coms = 0;
		$('.choix').each(function(){
			if ($(this).attr('value') != '') nb_coms = 1;
			else nb_coms = 0;
		});
		
		if(nb_coms == 0){
			alert('Merci de sélectionner un préparateur pour chaque préparation');
			return false;
		}
		
	});
	
	$(".prepa").click(function(){	 
		nb_bons = parseInt($('#nb_bons').html());
		nb_bons = (this.checked) ? (nb_bons+1): (nb_bons-1);				
		$('#nb_bons').html(nb_bons);
	});
	
	function select(val){
		$(".prepa").each(function()	{
				this.checked = val;
				nb_bons = parseInt($('#nb_bons').html());
				nb_bons = (val == true) ? (nb_bons+1): (nb_bons-1);				
				$('#nb_bons').html(nb_bons);
		});	
	}
	
	$(".select").click(function(){	 
		select(true); 		
		$('.select').attr('style','display:none;');		
		$('.deSelect').removeAttr('style');
	});
	$(".deSelect").click(function(){	 
		select(false); 		
		$('.deSelect').attr('style','display:none;');		
		$('.select').removeAttr('style');
	});
	
	
	
	//filter
	$( ".date" ).datepicker({ 
			showOtherMonths: true,
			selectOtherMonths: true
	}); 
	
	
	$('#pageChange').change(function(){
		$('#submitFilter').click();
	});
	
	//total
	//$(".selectAll").click(function(){
//			total_second($(this));
	//});
	
	
	$(".check").click(function(){ 
		total_second($(this));
	});
	
	function total_second(elem){
		var id1 = elem.attr('value').split('_');	
		var classs = elem.attr('class');
		var total = 0;
		// alert(classs);
		if(classs == 'total commande'  || classs == 'total entry')
			var etage = 0;
		else
			var etage = parseInt(id1[0]);
		$(".total").each(function()	{	
			if(this.checked == true){	
				var id2 = $(this).attr('value').split('_');	
				if(classs != 'total commande'  && classs != 'total entry')
					etage = parseInt(id2[0]); 
				total += parseInt(id2[2]);
			}
		});   
		
		
		$('.total_prep_'+etage).html(do_transforme_second(total)); 
	}
	
	function do_transforme_second(second){
		if (second != 0) {
			var time=second;
			if( time>=3600){
				// si le nombre de secondes ne contient pas de jours mais contient des heures
				heure = Math.floor(time/3600);
				reste = time%3600;
				minute = Math.ceil(reste/60);
				result = heure+' h '+minute+' min';
			}
			else{
			// si le nombre de secondes ne contient pas d'heures mais contient des minutes
			minute = Math.ceil(time/60);
			seconde = time%60;
			result = minute+' min';
			}

			return result;
		}
		else{
			return '0 min';
		}
	}

	
	$('.details_bons').click(function(){  
		var attribut = $(this).attr('id').split('_');
		var id = attribut[0];
		var type = attribut[1];
		var etage = attribut[2];
		var position = $(this).position();
		$('#info').remove();
		$('#conteneur_menu').append('<div id="info" style="position:absolute;text-align:center;"></div>');  
		$('#info').css("top",(position.top+20)+'px');
		$('#info').css("left",(position.left+20)+'px'); 
		$('#info').css("width",'700px'); 
		$('#info').css("height",'300px'); 
		$('#info').css("background-color",'white');
		$('#info').css("border",'1px solid black'); 
		$('#info').css("overflow-y",'scroll'); 
		
		$.ajax({ 
			type: "POST", 
			url: path+'affectation/details/',  			
			data: {  
					id : id,
					type : type,
					etage : etage,
					appel_ajax: 'appel_ajax'
			},
			dataType: 'html',	
			success: function(data){  
				text= '<br /><a href="#" id="close_popup" onclick="return false;">Fermer</a>' + data; 			
				$('#info').html(text);
				  
				$("#close_popup").click(function() {  
					$('#info').remove();
				});
			} ,
			error: function(){
				alert('may be a bad path : ' +path+'affectation/details/'+id);
			}
		});
	});
	
	
});
