 /**
*** Logiciel E-Pick ***
 *
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
 * Modelisation.js
 *
 * Script javascript permettant de faire la demo de la modelisation et du pvc
 *
 */ 
  
$(document).ready(function() {	 
 
	var decalageY = $('.etage_demo').offset().top;
	var decalageX = $('.etage_demo').offset().left; 
	var hauteursegment = 13; 
 
 
	//on trace les traits si il y a un resultat dans le textarea
	if($('#point_res').text() != ""){	
		var points = $('#point_res').text().split(';');
		from = points[0].split(',');
		var depart = from;
		for(var i=1; i < points.length-1; i++){		
			to = points[i].split(','); 
			drawLine(parseInt(from[1]),parseInt(from[0]),parseInt(to[1]),parseInt(to[0]),'#'+from[2],'1','etage_demo'); 
			from = to; 
		}			 
	}	
	
	//à l'initialisation on rotate tous les rayons
	$('.rayon_demo').each(function(){
		rotate_a_div($(this),$(this).attr('sens'));
	});
	 
	 
	//quand on click sur un rayon, il est sélectionné. Il est ajouté dans le formulaire caché 
	$('.rayon_demo').click(function (e){
  
		var elem = $(this); 
		var pos = elem.position();
		coor = top_left(elem,pos.top,pos.left);					
		x_top = coor[0];
		x_left = coor[1];
		
		
		if($.browser.msie){
			
			var hauteur = parseInt(elem.attr("hauteur_ray"));
			var largeur = parseInt(elem.attr("largeur_ray")); 	
		
		}else{	
			var hauteur = parseInt(elem.css("height"));
			var largeur = parseInt(elem.css("width")); 	
		} 
		
		var type = elem.attr('type_ray');
		var idrayon = elem.attr('id');	
		var sens = elem.attr('sens');

		var leftclick = (e.pageX-decalageX);
		var topclick = (e.pageY-decalageY);													
		var nbsegment = parseInt(elem.attr('nb_segment')); 
		var largeur_segment = largeur/nbsegment;
		//determination du segment 
		var num_segment;
		
		if(type == "classique"){
			var rad = (sens*Math.PI/180);
			
			if($('body').attr('finesse') > 1){
				for (i=1; i<=nbsegment; i++){ 
				
					var dx = (leftclick - x_left);
					var dy = (topclick - x_top);
					// alert(dy + "," + dx);

					var xRot = (dx*Math.cos(-rad) - dy*Math.sin(-rad));
					var yRot = (dx*Math.sin(-rad) + dy*Math.cos(-rad)); 
					if(xRot > (i-1)*largeur_segment && xRot < i*largeur_segment ){ //&& yRot > 0 && yRot < hauteursegment
						num_segment = i;
						break;
					}
				} 
				var divheight = hauteursegment;
				var divwidth = largeur_segment;
				var divtop = x_top +((num_segment-1)*largeur_segment)*Math.sin(rad);
				var divleft = (x_left + ((num_segment-1)*largeur_segment)*Math.cos(rad));
				
				if(num_segment == null)
					return false;
			}
			else{
				var divheight = hauteur;
				var divwidth = largeur;
				var divtop = x_top ;
				var divleft = x_left ;
				num_segment = 1;
			}
		 
			}
		else{
			num_segment = 1;		
			var divheight = hauteur;
			var divwidth = largeur;
			var divleft = x_left ;
			var divtop = x_top  ;
		
		}
		//on ajoute un div pour bien signaler qu'il est sélectionné
		$('.etage_demo').append('<div class="segmentselec" sens="'+sens+'" top_deb="'+divtop+'" left_deb="'+divleft+'" id="'+idrayon+'_'+num_segment+'" style="cursor:pointer;border:1px solid blue;position:absolute;top:'+divtop+'px;left:'+divleft+'px;width:'+divwidth+'px;height:'+divheight+'px;">&nbsp;</div>');

		rotate_a_div($('#'+idrayon+'_'+num_segment),sens);
		suppr_segment();
		
	 
		var content = $('#segment_select').html();
		if(content == "")
			$('#segment_select').html(idrayon+'_'+num_segment);
		else
			$('#segment_select').html(content+';'+idrayon+'_'+num_segment);
		
		
		
 });
 
 
 
 //possibilité de cliquer sur un segment pour le supprimer
 function suppr_segment(){
	 $('.segmentselec').click(function(){
		var key = $(this).attr('id');
		
		var content = $('#segment_select').text();
		var values = content.split(';');
		var futurcontent = '';
		
		values.forEach(function(item){ 
			if(item != key){
				if(futurcontent != '')
					futurcontent = futurcontent + ';' + item;
				else
					futurcontent = item;
			}
		});
		 
		$('#segment_select').text(futurcontent);
		$(this).remove();
	 
	 });
 }
 
  
 //fonction permettant de tracer une ligne entre deux points
function drawLine(x1,y1,x2,y2,color,espacementPointille,classId){
	if(espacementPointille<1) { espacementPointille=1; }
	var lg=Math.sqrt((x1-x2)*(x1-x2)+(y1-y2)*(y1-y2));
	var nbPointCentraux=Math.ceil(lg/espacementPointille)-1;
	
	var stepX=(x2-x1)/(nbPointCentraux+0);
	var stepY=(y2-y1)/(nbPointCentraux+0);
	var strNewPoints='';
	for(var i=1 ; i<nbPointCentraux ; i++)	{	  
		strNewPoints+='<div style="font-size:1px; width:3px; height:3px; background-color:'+color+'; position:absolute; top:'+Math.round(y1+i*stepY)+'px; left:'+Math.round(x1+i*stepX)+'px; ">&nbsp;</div>';
	}
	
	/*strNewPoints+='<div Name=One style="font-size:1px; width:4px; height:4px; background-color:'+color+'; position:absolute; top:'+(y1-1)+'px; left:'+(x1-1)+'px; ">&nbsp;</div>';*/
	strNewPoints+='<div Name=two style="font-size:1px; width:6px; height:6px; background-color:'+color+'; position:absolute; top:'+(y2-1)+'px; left:'+(x2-1)+'px; ">&nbsp;</div>';

	$('.etage_demo').append(strNewPoints);  
}
  
  
  
  
  
  
  
  
  
  
  
   $('.etage_demo').mousemove(function(e){
      $('#debug_test').html('top'+ (e.pageY-decalageY) + ' left ' + (e.pageX-decalageX) );
   }); 


   
   
   
   
   
   
   



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
	// alert('AVANT ('+parseInt(top_init)+','+parseInt(left_init)+') -> APRES ('+top+','+left+')');
	return new Array(top,left);
}
   
 
 
 
 
 
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

			// force an origin of 50%, 50%
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
			 