
$(document).ready(function() {	
	$('.launch_help_popup').click(function(){	 
		$('#help_popup').css('display','block');
		$('#help_popup').css('visibility','visible');
		
		$('#help_popup').draggable({
			handle		: "div#title",
			containment : "body"
		});
		
		$('#help_popup #bar #close').click(function(){			
			$('#help_popup').css('display','none');
			$('#help_popup').css('visibility','hidden');
		});
	});
}); 