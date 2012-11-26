/**
 * bubulle
 * =============================================================================
 * permet de créer des infobulles acceptant le html et css
 * 
 * @author      Erwan Lefèvre <erwan.lefevre@aposte.net>
 * @copyright   Erwan Lefèvre 2009
 * @license     Creative Commons - Paternité 2.0 France - http://creativecommons.org/licenses/by/2.0/fr/
 * @version     2.1.1 / 2010-06-02
 * @see			http://www.webbricks.org/bricks/bubulle/
 * 
 * @compatible	au 2 juin 2010, compatibilité assurée pour :
 *				Firefox 1.5+, Internet Explorer 5.5+, Safari 3.2.3., Opéra 10.53, Chrome 4.1 
 *				Autres versions et navigateurs non testés
 */


/* exemple

-	dans le html :

		<a href="#" onmouseover="bubulle.hop('patatie et patata', {classe:'ma_classe_css'})" onmouseout="bubule.php();">une bulle ici</a>
		
		
-	ou en javascript non intrusif (plus pratique pour passer plusieurs options)

		var contenu = 'patatie et patata';
		var actionneur = document.getElementById('mon_element');
		var options = {
			classe : 'ma_classe_css',
			persistance : -1,
			delai : 0,
			fixe après : 0
		};
		bubulle.apply( actionneur , contenu , options );
		
-	astuce : copier le contenu de la bulle depuis un élément caché dans le dom (display:none)

		var contenu = document.getElementById('modele').innerHTML;
		var actionneur = document.getElementById('mon_element');
		bubulle.apply( actionneur , contenu );

*/





/**
 * byId()
 * raccourci pour document.getElementById()
 * retourne l'élément html dont l'attribut id vaut /id/
 *
 * @param		id		{String}		L'attribut id de l'élément recherché
 *
 * @returns		{HTMLElement}
 * 
 * =============================================================================
 */
function byId(id) {
	return document.getElementById(id) ;
}





/**
 * addEvent()
 * 
 * ajoute la fonction /fn/ à la pile de résolution de l'événement /evenType/ de
 * l'objet /obj/
 * 
 * merci à : http://www.scottandrew.com/weblog/articles/cbs-events
 *
 * @param		{Mixed}				obj			window, ou document, ou un élément HTML
 * @param		{String}			evType		type d'event (click, mouseover, mouseout, etc.)
 * @param		{String}			fn			la fonction à ajouter
 * @param		{Boolean}			useCapture	"useCapture un booléen : true pour la phase de capture, ou false pour la phase de bouillonnement et la cible. On utilise quasiment toujours la valeur false."
 * 
 * @returns		void
 * 
 * =============================================================================
 */
function addEvent (obj, evType, fn, useCapture){
	if (obj.addEventListener) { obj.addEventListener(evType, fn, useCapture); }
	else { obj.attachEvent("on"+evType, fn); }
}




/**
 * remEvent()
 * 
 * retire la fonction /fn/ de la pile de résolution de l'événement /evenType/ de
 * l'objet /obj/
 * 
 * merci à : http://www.scottandrew.com/weblog/articles/cbs-events
 * 
 * @param		{Mixed}			obj			window, ou document, ou un élément HTML
 * @param		{string}		evType		type d'event (click, mouseover, mouseout, etc.)
 * @param		{string}		fn			la fonction à supprimer
 * @param		{boolean}		useCapture	"useCapture un booléen : true pour la phase de capture, ou false pour la phase de bouillonnement et la cible. On utilise quasiment toujours la valeur false."
 * 
 * @returns		void
 * 
 * =============================================================================
 */
function remEvent(obj, evType, fn, useCapture){
	if (obj.removeEventListener) { obj.removeEventListener(evType, fn, useCapture); }
	else { obj.detachEvent("on"+evType, fn); }
}




var mousePos = {'x':0,'y':0};
function getMousePos(e) {
	var d = document,
		de = d.documentElement,
		db = document.body;
	
	e = e || window.event;
	if (e.pageX || e.pageY) {
		mousePos.x = e.pageX;
		mousePos.y = e.pageY;
	}
	else if (e.clientX || e.clientY) {
		mousePos.x = e.clientX + db.scrollLeft + de.scrollLeft;
		mousePos.y = e.clientY + db.scrollTop + de.scrollTop;
	}
}
/**
 * mise en place du relevé de coordonées de la souris, en cas de déplacement de la souris
 */
addEvent(document, 'mousemove', getMousePos);




/** scrolled() 
 * =============================================================================
 * retoune les valeurs (horizontale et verticale) de défilement de la fenêtre
 * (en tenant compte du navigateur)
 *
 * @return          Object      {'x','y'}         
 */
function scrolled () {
    var x,y;
    
    // vrais navigateurs
    if ( window.pageXOffset!==undefined) {
        x = window.pageXOffset;
        y = window.pageYOffset;
    }
    // ie
    else {
        x = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : (document.body.scrollLeft?document.body.scrollLeft:0) ;
        y = document.documentElement.scrollTop ? document.documentElement.scrollTop : (document.body.scrollTop?document.body.scrollTop:0) ;
    }
    
    return {'x':x, 'y':y};
}





/**
 * transfer
 * =============================================================================
 * retourne un objet contenant les propriétés et méthodes de l'objet /dest/,
 * complétées et/ou écrasées par celles de l'objet /source/
 *
 * @param       source       {object}        l'objet source
 * @param       dest         {object}        l'objet de destination
 * @return      {object}
 *
 */
function transfer (source, dest) {
    var prop, transfered={};
    for ( prop in dest ) { transfered[prop] = dest[prop]; }
    for ( prop in source ) { transfered[prop] = source[prop]; }
    return transfered; 
}




/** 
 * winDim() 
 * -----------------------------------------------------------------------------
 * retourne les dimentions intérieurs de la fenêtre
 *
 * @return		{Object}
 */

function winDim() {
	var w,h,
		i = window,
		d = document,
		de = d.documentElement,
		db = d.body;
		
	if ( i.innerWidth ) { // autres que IE
		w = i.innerWidth;
		h = i.innerHeight;
	} else if ( de.clientWidth ) { // IE8
		w = de.clientWidth;
		h = de.clientHeight;
	}
	else { // IE6
		w = db.clientWidth;
		h = db.clientHeight;
	}

	return {'w':w, 'h':h} ;
}





/**
 * bubulle 2.1.1
 * =============================================================================
 * permet de créer des infobulles acceptant le html et les css
 * 
 * @uses	addEvent
 * @uses	byId
 * @uses	mousePos
 * @uses	remEvent
 * @uses	scrolled
 * @uses	transfer
 * @uses	winDim
 * @optionnal		AjReq		pour charger le contenu de la bulle par ajax
 * @optionnal		setOpacity	pour faire apparaître la bulle en fondu
 * @optionnal		fade		pour faire apparaître la bulle en fondu
 * 
 */
var bubulle = {
	/**
	 * parDefaut
	 * -------------------------------------------------------------------------
	 * réglages par défaut
	 * 
	 */
	parDefaut : {
		classe : null,			// classe css à appliquer à la bulle
		contenuAjax : null,		// en cas de chargement du contenu de la bulle par ajax, url du fichier à interroger (requiert AjReq.js)
		fonduOpt : {			// options du fondu (requiert fade.js. Voir ce fichier pour le détail des options)
			duration : 500
		},
		fixeApres : 200,		// avant ce délai (en milliseconde), la bulle bouge avec la souris.
								//		ce délai est diminué du montant de l'option "delai"
								//		-1 fait que la bulle bougera tout le temps avec la souris
								//		avec survolBulle:true + delai:0, je préconise de ne pas mettre fixeApres à 0, car il faut laisser un peut de temps à l'utilisateur pour stabiliser sa main (200ms peuvent faire l'affaire)
		
		// options liées à la persistance de la bulle
		actionneur : null,		// rappel de l'élément actionnant la bulle (factultatif avec bubulle.apply)
		survolBulle : 0,		// indique si survoler la bulle la maintient ouverte
		persistance : 0,		// durée pdt laquelle la bulle reste affichée, même après mouseout. (-1 pour qu'elle ne disparaisse pas sur mouseout),
		delai : 200,			// délai avant affichage de la bulle ()
		
		rien : 0				// pour pas me prendre la tête avec les vigules
	},
	
	/**
	 * parici()
	 * -------------------------------------------------------------------------
	 * positionne la bulle
	 * 
	 */
	parici : function() {
		var bulle = byId('bubulle');
		if (bulle) {
			var winDims = winDim();
			var winPos = scrolled();
			
			// position horizontale
			var bordDroit = mousePos.x + bulle.offsetWidth ;
			bulle.style.left = ( (bordDroit>winDims.w) ? mousePos.x - (bordDroit - winDims.w) : mousePos.x) + "px";
	
			// position verticale
			var auDessus = mousePos.y - (bulle.offsetHeight + 5);
			bulle.style.top = ( (auDessus<winPos.y) ? mousePos.y + 10 : auDessus) + "px";
		}
	},
	
	
	
	
	/**
	 * hop()
	 * -------------------------------------------------------------------------
	 * programme l'ouverture d'une infobulle avec le message /blabla/ et les options /options/
	 *
	 * @param		{mixed}		blabla		voir bubulle.open()
	 * @param		{object}	options		(facultatif) voir bubulle.open()
	 * 
	 */
	hop : function (blabla, options) {
		
		// lectude du délai avant ouverture
		options = transfer(options, this.parDefaut);
		if (options.fixeApres>0) {
			var diff = options.fixeApres-options.delai;
			options.fixeApres = diff<0 ? 0 : diff;
		}
		this.options = options;
				
		// programmation de l'ouverture
		this.prog = setTimeout(function(){
			bubulle.creer(blabla, options);
		},options.delai||1);
	},
	
	
	
	
	/**
	 * creer()
	 * -------------------------------------------------------------------------
	 * ouvre (immédiatement) une infobulle avec le message /blabla/ et les options /options/
	 * (normalement, cette méthode ne doit être activée que par l'intermédiaire de "hop")
	 *
	 * @param		{String}	blabla		le message à afficher dans la bulle.
	 * @param		{object}	options		(facultatif) les options pour cette bulle (voir this.parDefaut)
	 */
	creer : function (blabla, options) {
		
		this.hover = true;
		
		// si on est toujours sur le même actionneur
		if (this.actionneur==options.actionneur){
			this.pop(); // reprogrammer la fermeture de la bulle
		}
		// si on est sur un nouvel actionneur
		else {
			// une seule bulle à la fois : s'il en existe une, on la ferme
			
				this.hover=0;
				this.achever(); 
				this.actionneur = options.actionneur;
			
			// création de la bulle + paramètres de base
			
				var bulle = document.createElement("div");
				bulle.id = "bubulle"; // pour la retrouver facilement
				bulle.innerHTML = blabla; // contenu de la bulle
				bulle.style.position = "absolute";
				bulle.style.top = bulle.style.left = '0px'; bulle.style.zIndex = -9; // temporaire. Evite des problèmes d'affichages avec les systèmes un peu lents
				document.getElementsByTagName("body")[0].appendChild(bulle); // pour ie, je suis contraint d'insérer ma bulle ds la page avant d'appeler parici()
				bulle.className = options.classe; // classe : design de la bulle (doit être traité avant le positionnement de la bulle)
				bubulle.parici(); // positionnement initial
				bulle.style.zIndex = '';
				if (window.setOpacity) {setOpacity(bulle,0);} // prépare l'apparition en fondu, si le script existe
				
			// gestion des options (autres que la classe css)
				
				// lecture des options
				options = transfer(options, this.parDefaut);
				this.options = options;
				
				// survolBulle : reporter fermeture tant qu'on survolle la bulle
				if (options.survolBulle) {
					bulle.onmouseover=function(){bubulle.hover=1;};
					bulle.onmouseout=this.pop;
				}
				
				// contenuAjax : chargement du contenu ajax
				if (options.contenuAjax && window.AjReq) { // n'activer que si le script AjReq a été déclaré
					this.ajax = new AjReq();
					this.ajax.send(options.contenuAjax,{ // lancer un requête ajax
						forceMime : 'text/html',
						onSuccess : function(){ // si la requête aboutit...
							var b = byId('bubulle');
							if (b) { b.innerHTML=bubulle.ajax.back.response; } // ...modifier le contenu de labulle...
							bubulle.parici(); // ...puis la repositionner.
						}
					});
				}
				
				// fonduOpt : apparition en fondu
				if (window.fade) {
					options.fonduOpt.duration = options.fonduOpt.duration||1;
					fade(bulle,1,0,options.fonduOpt);
				}
				
				// fixeApres : mobilité de la bulle
				var fixeApres = options.fixeApres;
				remEvent(window, 'mousemove', bubulle.parici);
				addEvent(window, 'mousemove', bubulle.parici);
				if (fixeApres>=0) {
					setTimeout(
						function(){ remEvent(window, 'mousemove', bubulle.parici);},
						fixeApres||1
					);
				}
		}
	},
	
	
	
	
	/**
	 * deprog()
	 * -------------------------------------------------------------------------
	 * annule la programmation de fermeture (acherver) ou d'ouverture (creer) de la bulle
	 *
	 */
	deprog : function () {
		if(this.prog){
			clearTimeout(this.prog);
			this.prog=0;
		}
	},
	
	
	
	
	/**
	 * achever()
	 * -------------------------------------------------------------------------
	 * ferme l'infobulle pour de bon
	 *
	 */
	achever : function () {
		var bulle = byId("bubulle");
		if (bulle && !bubulle.hover) {
			bubulle.deprog();
			bubulle.actionneur=0;
			if (bulle) { document.getElementsByTagName("body")[0].removeChild(bulle); }
			if (bubulle.ajax) { bubulle.ajax.abort(); bubulle.ajax=0; }
		}
	},
	
	
	
	
	/**
	 * pop()
	 * -------------------------------------------------------------------------
	 * programme la fermeture de l'infobulle
	 *
	 */
	pop : function () {
		bubulle.hover=0;
		bubulle.deprog();
		bubulle.prog = setTimeout( bubulle.achever , bubulle.options.persistance );
	},
	
	
	
	
	/**
	 * apply()
	 * -------------------------------------------------------------------------
	 * met en place une infobulle sur l'élément /elem/, contenant le texte /blabla/,
	 * avec les options /options/
	 *
	 * @param		elem		{HMLTElement}		l'élément auquel appliquer l'infobulle
	 * @param		blabla		{String}			le message à afficher dans la bulle.
	 * @param		options		{Object}			les options pour cette bulle (voir this.parDefaut)
	 */
	apply : function (elem, blabla, options) {
		// préparation des options
		options = transfer(options, this.parDefaut); // lecture des options
		options.actionneur = options.actionneur || elem; // une option à part
		
		// mise en place des events sur l'élément HTML
		addEvent(elem,'mouseover',function(){bubulle.hop(blabla,options);});
		if (options.persistance>=0) { addEvent(elem,'mouseout',bubulle.pop); }
	}
};