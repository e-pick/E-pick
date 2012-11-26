 /**
*** Logiciel E-Pick ***
 *
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
 *
 * Commande.js
 *
 * Script javascript 
 *
 */ 
  
$(document).ready(function() {	

	var path = $('body').attr('path');
	$( ".date" ).datepicker({ 
			showOtherMonths: true,
			selectOtherMonths: true
	}); 
	
	$('#pageChange').change(function(){
		$('#submitFilter').click();
	});
	
	$('#cheminOriginal').click(function(){
		$('#submitTypeChemin').click();
	});
	
	$('#cheminOptimise').click(function(){
		$('#submitTypeChemin').click();
	});
});
