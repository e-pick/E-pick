 /**
*** Logiciel E-Pick ***
 *
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
 * Produit.js
 *
 * Script javascript 
 *
 */ 
 
 
 
 
 
  
$(document).ready(function() {	
	
	$('#etage').change(function() {
		$('#zone').attr('value', '');
		$('#rayon').attr('value', '');
		$('#segment').attr('value', '');
		$('#etagere').attr('value', '');
		$("#submitFilter").click();
	});
	
	$('#zone').change(function() {
		$('#rayon').attr('value', '');
		$('#segment').attr('value', '');
		$('#etagere').attr('value', '');
		$("#submitFilter").click();
	});
	
	$('#rayon').change(function() {
		$('#segment').attr('value', '');
		$('#etagere').attr('value', '');
		$("#submitFilter").click();
	});
	
	$('#segment').change(function() {
		$('#etagere').attr('value', '');
		$("#submitFilter").click();
	});
	
	$('#etagere').change(function() {
		$("#submitFilter").click();
	});
	
	$("#selectAllProduits").click(function()
	{
		var checked_status = this.checked;
		
		$(".checkProduit").each(function()
		{
			this.checked = checked_status;
		});
	});
	
	$('.blocDisplayRayon').click(function() {
		$('#idEtagere').val($(this).parent().attr('id'));
		$("#submitFilter").click();
	});
	
	$('#saveGeoloc').click(function(){
		$('#save').val('1');
		$("#submitFilter").click();
	});
	
	$('#idEtagere').val($(".selected").attr('id'));
	
	$('#ajouterEan').click(function(){
		$('#conteneurEans').append('<label> ean </label><input type="text" name="ean[]"/>');
	});
	
	$('#pageChange').change(function(){
		$('#submitFilter').click();
	});
	
	$('.supprimerEan').click(function(){
		var conf = confirm('Etes vous sur?');
		if(conf){
			$('#eanToDelete').attr('value',$(this).attr('id'));
			$('#submitEdition').click();
		}
	});
	
});