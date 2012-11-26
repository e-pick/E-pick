 /**
*** Logiciel E-Pick ***
 *
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
 *
 * Zone.js
 *
 * Script javascript 
 *
 */ 
  
$(document).ready(function() {	

	$('#selectZone').change(function() {
		$("#submitAfficher").click(); 
	});
	
	$("#selectAll").click(function()
	{
		var checked_status = this.checked;
		
		$(".checkRayon").each(function()
		{
			this.checked = checked_status;
		});
	});
	
	$('#etage').change(function() {
		$('#zone').attr('value', '');
		$("#submitFilter").click();
	});
	
	$('#zone').change(function() {
		$("#submitFilter").click();
	});
	
	$('#pageChange').change(function(){
		$('#submitFilter').click();
	});
	
	$('#choisir_couleur').click(function(){ 
		$('#infobulle').remove();
	 	$('#conteneur_menu').append('<div id="infobulle"></div>');
		$('#infobulle').css("top",($(this).position().top + 20)+'px');
		$('#infobulle').css("left",($(this).position().left +60)+'px'); 
		$('#infobulle').css("width",'300px'); 
		$('#infobulle').css("height",'50px'); 
		$('#infobulle').css("overflow",'hidden'); 
		
		var text = "";
	
		for(i = 0; i < 9; i++)
			text= text + '<div class="couleur_a_choisir" style="background-color:#'+Math.round(0xffffff * Math.random()).toString(16)+';"> </div>'; 			
		
		text = text + '<br /><br /><a href="#" id="close_popup" onclick="return false;">Fermer</a>'; 
		$('#infobulle').html(text);

		$("#close_popup").click(function() { 
			$('#infobulle').remove();
		});
		
		
		$('.couleur_a_choisir').click(function(){
			var couleur = $(this).css('background-color'); 
			$('.couleur_choisie').css('background-color',couleur); 
			$('#couleur').val(rgb2hex(couleur)); 
			$('#infobulle').remove();
		});
	});
	
	
	
	function rgb2hex(rgb) {
		 if (  rgb.search("rgb") == -1 ) {
			  return rgb.substr(1,rgb.length);
		 } else {
			  rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
			  function hex(x) {
				   return ("0" + parseInt(x).toString(16)).slice(-2);
			  }
			  return hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]); 
		 }
	}
	
	// $.ajax({ // Requete ajax
 
		// type: "POST", 		// envoie en POST
		// url: "C:\wamp\www\ProxiPicking\application\controllers\ZoneController.php", 	// url cible du script PHP
		// async: true, 		// mode asynchrone
		// data: "", 			// données envoyées
		 
		// success: function(xml){ // Lorsque le PHP à renvoyé une réponse
			// var elementsArray = new Array();
			
			// elementsArray = '<?php  echo $arrayRayons; ?>';
			// alert(elementsArray);
			 
			// $(xml).find('element').each(function(){ // pour chaque "element"
				// elementsArray.push($(this).text()); // ajout dans le tableau
			// });
			 
			// $("#libelle").autocomplete(elementsArray); // activation de l'autocompletion
		// },
		
		// error: function(){
			// alert('error');
		// }
	 
	// });
		
});