 /**
*** Logiciel E-Pick ***
 *
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
 *
 * Rayon.js
 *
 * Script javascript 
 *
 */ 
  
$(document).ready(function() {	
	
	
	
	$('.Segment').each(function(){
		var width = parseInt($(this).css('width'));
		$(this).css('width', (width - 2) + 'px');
	});
	
	$('.Etagere').each(function(){
		var height = parseInt($(this).css('height'));
		$(this).css('height', (height - 1) + 'px');
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
	
});