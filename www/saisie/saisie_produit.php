<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <title>E-Pick - Saisir une commande</title>

<link rel="stylesheet" href="themes/test.min.css" /> 
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile.structure-1.2.0.min.css" />
<script language="javascript" type="text/javascript" src="niceforms.js"></script>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>


<script type="text/javascript">


function VoirType(Lequel) {
    if (Lequel == 1) {
        document.getElementById("LocEan").style.display = "block";
        document.getElementById("LocPrdt").style.display = "none";
    } else {
        document.getElementById("LocEan").style.display = "none";
        document.getElementById("LocPrdt").style.display = "block";
    }
}

function VoirTypeSaisie(Lequel) {
    if (Lequel == 1) {
        document.getElementById("Commande").style.display = "block";
        document.getElementById("Ranger").style.display = "none";
    } else {
        document.getElementById("Commande").style.display = "none";
        document.getElementById("Ranger").style.display = "block";
    }
}


// Recherche code EAN


var counter = 1;
var nbcar = 13;
function addInput(divName) {
    var newdiv = document.createElement('div');
    newdiv.innerHTML = "<dl><dt>EAN :</dt><dd><img class='NFTextLeft' src='img/0.png'><div class='NFTextCenter'><input class='NFText' type='text' maxlength='13' name='myInputs[]' id='TB" + (counter + 1) + "' tabindex='" + (counter + 1) + "' onkeyup='Autotab(" + (counter + 2) + ", this.value)'></div> <img class='NFTextRight' src='img/0.png'>  <div class=resultat id='result" + (counter + 1) + "'></div></dd></dl>";
    document.getElementById(divName).appendChild(newdiv);
    counter++;
}

function Autotab(box, ean) {
    if (ean.length > 12) {
        //ajax_searchProduit(Ean]
        xmlhttpPost("/E-Pick/flux/in/produits/geoloc.php", ean, box);
        //Fin recherche produit
        addInput('dynamicInput');
        document.getElementById('TB' + box).focus();
    }
}


function xmlhttpPost(strURL, MySearch, Count) {
    var xmlHttpReq = false;
    var self = this;
    // Mozilla/Safari
    if (window.XMLHttpRequest) {
        self.xmlHttpReq = new XMLHttpRequest();
    }
    // IE
    else if (window.ActiveXObject) {
        self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    self.xmlHttpReq.open('POST', strURL, true);
    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    self.xmlHttpReq.onreadystatechange = function() {
        if (self.xmlHttpReq.readyState == 4) {
            updatepage(self.xmlHttpReq.responseText, Count);
        }
    }
        var querystring = strURL + '?ean=' + MySearch;
        self.xmlHttpReq.send(getquerystring(MySearch));
    
}

function getquerystring(ChercheEan) {
    var form = document.forms['f1'];
     qstr = 'ean=' + escape(ChercheEan); // NOTE: no '?' before querystring
    return qstr;
}

function updatepage(str, Compte, Type) {
    if (str.length > 1) {

        var splitChaine = str.split('|');
        var Libelle = splitChaine[0];
        var Vignette = splitChaine[1];
        var Rayon = " - Rayon : " + splitChaine[2];
        var Etage = "- Etag&egrave;re : " + splitChaine[3];
        var Segment = " - Segment : " + splitChaine[4];
        var Zone = "<br>Zone : " + splitChaine[5];
        var id_prod = splitChaine[6];
        var Data_Order = "<input type='Hidden' name='Lib_Produit[]' value='" + splitChaine[0] + "'><input type='Hidden' name='CodeProd[]' value='" + id_prod + "'>";
        Affiche = Libelle + Zone + Rayon + Segment + Etage + Data_Order;
    } else {
        Affiche = "Pas de r&eacutesultat";
    }
    document.getElementById("result"+(Compte-1)).innerHTML = Affiche;
	
}


// Recherche Produit Libellé


function lookup(inputString) {
   if (inputString.length == 0) {
        // Hide the suggestion box.
        $('#suggestions').hide();
    } else {
        $.post("rpc.php", {
            queryString: "" + inputString + ""
        }, function (data) {
            if (data.length > 0) {
                $('#suggestions').show();
                $('#autoSuggestionsList').html(data);
            }
        });
    }
} // lookup



function fill(thisValue, Code_Prod) {
    $('#inputString').val(thisValue);
    setTimeout("$('#suggestions').hide();", 200);
    //Rechercher le produit
    xmlhttpPostProduit("/E-Pick/flux/in/produits/geoloc.php", Code_Prod);
   
}



function xmlhttpPostProduit(strURL, MySearch) {
    var xmlHttpReq = false;
    var self = this;
    // Mozilla/Safari
    if (window.XMLHttpRequest) {
        self.xmlHttpReq = new XMLHttpRequest();
    }
    // IE
    else if (window.ActiveXObject) {
        self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    self.xmlHttpReq.open('POST', strURL, true);
    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    self.xmlHttpReq.onreadystatechange = function() {
        if (self.xmlHttpReq.readyState == 4) {
            updatepageProduit(self.xmlHttpReq.responseText);
        }
    }
        var querystring = strURL + '?Code_Prod=' + MySearch;
        self.xmlHttpReq.send(getquerystringProduit(MySearch));
    
}



function getquerystringProduit(ChercheProduit) {
    var form = document.forms['f1'];
        qstr = 'CodeProd=' + escape(ChercheProduit); // NOTE: no '?' before querystring
    return qstr;
}



function updatepageProduit(str) {
   if (str.length > 1) {
   		var splitChaine = str.split('|');
        var Libelle = splitChaine[0];
        var Vignette = splitChaine[1];
        var Rayon = " - Rayon : " + splitChaine[2];
        var Etage = " - Etag&egrave;re : " + splitChaine[3];
        var Segment = " - Segment : " + splitChaine[4];
        var Zone = "<br>Zone : " + splitChaine[5];
        var id_prod = splitChaine[6];
        var Data_Order = "<input type='Hidden' name='Lib_Produit[]' value='" + splitChaine[0] + "'><input type='Hidden' name='CodeProd[]' value='" + id_prod + "'>";
        Affiche = Zone + Rayon + Segment + Etage + Data_Order;
		document.getElementById("ResultLib").innerHTML = Affiche;
	 
var list = $("#ListResult");
list.append('<li>'+ Libelle + Data_Order +'</li>');

		} else {
        Affiche = "Pas de r&eacutesultat";
		  }
    
}



function Clear(){
document.getElementById("ResultLib").innerHTML = '';
}


</script> 
  </head>
  <body>
  
  
  <div data-role="page" data-theme="a">
			<div data-role="header" data-position="inline">
				<h1><img src="img/logo.png" style="margin-bottom:5px;"></a></h1>
			</div>
			<div data-role="content" data-theme="a">
				<p>This is content color swatch "A" and a preview of a <a href="#" class="ui-link">link</a>.</p>
				<label for="slider1">Input slider:</label>
				<input type="range" name="slider1" id="slider1" value="50" min="0" max="100" data-theme="a" />
				<fieldset data-role="controlgroup"  data-type="horizontal" data-role="fieldcontain">
				<legend>Cache settings:</legend>
				<input type="radio" name="radio-choice-a1" id="radio-choice-a1" value="on" checked="checked" />
				<label for="radio-choice-a1">On</label>
				<input type="radio" name="radio-choice-a1" id="radio-choice-b1" value="off"  />
				<label for="radio-choice-b1">Off</label>
				</fieldset>
				
				
	<ul data-role="listview" data-inset="true" data-filter="true">
	<li><a href="#">Acura</a></li>
	<li><a href="#">Audi</a></li>
	<li><a href="#">BMW</a></li>
	<li><a href="#">Cadillac</a></li>
	<li><a href="#">Ferrari</a></li>
	</ul>
				
				
				
			</div>
		</div>
		
		
		

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
    <div id="container">
      <a href="index.php">
        
      <div class="niceform">
        <fieldset>    	
          <legend>Param&egrave;tre de la saisie
          </legend>        
          <dl>        	
            <dt>
              <label for="color">Saisir des :
              </label>
            </dt>            
            <dd>
              <input type="radio" name="Type_Saisie" id="TypePrdt" value="LocPrdt" onclick = "VoirType(2)" checked="checked">
              <label for="TypePrdt" class="opt"> Libell&eacute;s Produit
              </label>            

              <input type="radio"  name="Type_Saisie" id="TypeEan" value="LocEan" onclick = "VoirType(1)">
              <label for="TypeEan" class="opt"> Code EAN
              </label>
            </dd>        
          </dl>             
          <dl>        	
            <dt>
              <label for="color">Cr&eacute;er
              </label>
            </dt>            
            <dd>
              <input type="radio" name="Saisie" id="rg" value="Ranger" onclick = "VoirTypeSaisie(2)"  checked="checked">
              <label for="rg" class="opt"> Ranger des produits
              </label>
              <input type="radio" name="Saisie" id="cmd" value="Commande" onclick = "VoirTypeSaisie(1)">
              <label for="cmd" class="opt"> Une commande
              </label>            
            </dd>        
          </dl>
        </fieldset>
      </div>
      <form action="/E-Pick/flux/in/commandes/commandes_recup_online.php" method="post"  name="f1" class="niceform">
        <div id="Commande" style="display:none">	
          <fieldset>    	
            <legend>Coordonn&eacute;es du client
            </legend>        
            <dl>        	
              <dt>
                <label for="Nom">Nom :
                </label>
              </dt>            
              <dd>
                <input type="text" name="Nom" size="32" maxlength="128" />
              </dd>        
            </dl>        
            <dl>        	
              <dt>
                <label for="Prenom">Pr&eacute;nom : 
                </label>
              </dt>            
              <dd>
                <input type="text" name="Nom" size="32" maxlength="128" />
              </dd>        
            </dl>        
            <dl>        	
              <dt>
                <label for="email">Email :
                </label>
              </dt>            
              <dd>
                <input type="text" name="email" size="32" maxlength="128" />
              </dd>        
            </dl>        
            <dl>        	
              <dt>
                <label for="societe">Soci&eacute;t&eacute; :
                </label>
              </dt>            
              <dd>
                <input type="text" name="Société" size="32" maxlength="128" />
              </dd>        
            </dl>        
            <dl>        	
              <dt>
                <label for="adresse">Adresse :
                </label>
              </dt>            
              <dd>
                <input type="text" name="Adresse" size="32" maxlength="128" />
              </dd>        
            </dl>        
            <dl>        	
              <dt>
                <label for="email">Code postal
                </label>
              </dt>            
              <dd>
                <input type="text" name="CP" size="6" maxlength="6" />
              </dd>        
            </dl>        
            <dl>        	
              <dt>
                <label for="ville">Ville
                </label>
              </dt>            
              <dd>
                <input type="text" name="Ville" size="32" maxlength="128" />
              </dd>        
            </dl>             
          </fieldset>    
        </div>	 	 	
        <div id="Ranger" style="display:block">	
          <fieldset>    	
            <legend>Nom du pr&eacute;parateur
            </legend>        
            <dl>        	
              <dt>
                <label for="Nom">Nom :
                </label>
              </dt>            
              <dd>
                <input type="text" name="Prenom" size="32" maxlength="128" />
              </dd>			
              <input type="hidden" Value="Re-stocking Retour Client" name="Nom_Prep">        
            </dl>    
          </fieldset>    
        </div>	          
        <fieldset>              	
          <legend>Liste des articles de la commande
          </legend>              
          <div id="LocEan" style="display:none">       
            <div id="dynamicInput">       
              <dl>          
                <dt>EAN :
                </dt>
                <dd>
                  <input type="text" name="myInputs[]" value="325039094932" maxlength="13" length="13" id="TB1" tabindex="1" onkeyup="Autotab(2, this.value)"/>		   
                  <div class="resultat" id="result1"> 
                  </div>
                </dd>        
              </dl>                 
            </div>
          </div>
          <div id="LocPrdt" style="display:block">
	        <div id="dynamicInput2">       
              <dl>          
                <dt>Produit :</dt>		  
                <dd>
							
				  <input type="text" size="60" name="myInputs2" id="inputString"  onClick="javascript:this.value='';" onkeyup="lookup(this.value);" onblur="fill();">		  
                  <div class="resultat" id="ResultLib">  </div>
                  <div class="suggestionsBox" id="suggestions" style="display: none;z-index:50;">			
                    <img src="upArrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" style="z-index:99999;" />		 
                    <div class="suggestionList" id="autoSuggestionsList"></div>		 
                    </div>			
                 <p>
				 <ul>
				 <List id="ListResult"></List>
				</ul>
				  
                </dd>        
              </dl>                 
            </div>
          </div>        
          <dl>        	
            <dt>
            </dt>            
            <dd>

            <button onClick=submit();>G&eacute;olocaliser la liste de produits</button>
            </dd>        
          </dl>    
        </fieldset>       

          
      </form>
    </div>     	 
  </body>
</html>