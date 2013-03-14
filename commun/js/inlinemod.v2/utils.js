// retourne un objet xmlHttpRequest.
// méthode compatible entre tous les navigateurs (IE/Firefox/Opera)
function getXMLHTTP()
{
    var xhr = null;
    if(window.XMLHttpRequest)
    { // Firefox et autres
        xhr = new XMLHttpRequest();
    }
    else if(window.ActiveXObject)
    { // Internet Explorer
        try
        {
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch(e)
        {
            try
            {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e1)
            {
                xhr = null;
            }
        }
    }
    else
    { // XMLHttpRequest non supporté par le navigateur
        alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
    }

    return xhr;
}


//Fonction renvoyant le code de la touche appuyée lors d'un événement clavier
function getKeyCode(evenement)
{
    for (prop in evenement)
    {
        if(prop == 'which')
        {
            return evenement.which;
        }
    }

    return event.keyCode;
}


/**
*
*  Javascript trim, ltrim, rtrim
*  http://www.webtoolkit.info/
*  Without the second parameter, they will trim these characters:

    * " " (ASCII 32 (0x20)), an ordinary space.
    * "\t" (ASCII 9 (0x09)), a tab.
    * "\n" (ASCII 10 (0x0A)), a new line (line feed).
    * "\r" (ASCII 13 (0x0D)), a carriage return.
    * "\0" (ASCII 0 (0x00)), the NUL-byte.
    * "\x0B" (ASCII 11 (0x0B)), a vertical tab.

*
**/
 
function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}




//Fonction donnant la largeur en pixels du texte donné (merci SpaceFrog !)
function getTextWidth(texte)
{
	//Valeur par défaut : 150 pixels
	var largeur = 150;

	if(trim(texte) == "")
	{
		return largeur;
	}

	//Création d'un span caché que l'on "mesurera"
	var span = document.createElement("span");
	span.style.visibility = "hidden";
	span.style.position = "absolute";

	//Ajout du texte dans le span puis du span dans le corps de la page
	span.appendChild(document.createTextNode(texte));
	document.getElementsByTagName("body")[0].appendChild(span);

	//Largeur du texte
	largeur = span.offsetWidth;

	//Suppression du span
	document.getElementsByTagName("body")[0].removeChild(span);
	span = null;

	return largeur;
}

//remplacement des retours chariot (\n) par des balises ajout PP<br>.
function nl2br(string){
    return string.replace(/\n/g,'<br />');
}



function getTextHeight(texte)  //ajout PP 
{
	//Valeur par dÃ©faut : 150 pixels
	var hauteur = 40;

	if(trim(texte) == "")
	{
		return hauteur;
	}

	//Création d'un span caché que l'on "mesurera"
	var span = document.createElement("span");
	span.style.visibility = "hidden";
	span.style.position = "absolute";

	//Ajout du texte dans le span puis du span dans le corps de la page
	span.appendChild(document.createTextNode(nl2br(texte)));
	document.getElementsByTagName("body")[0].appendChild(span);

	//Largeur du texte
	hauteur = span.offsetHeight;

	//Suppression du span
	document.getElementsByTagName("body")[0].removeChild(span);
	span = null;

	return hauteur;
}



//Fonction renvoyant une valeur "aléatoire" pour forcer le navigateur (ie...)
//à envoyer la requête de mise à jour
function ieTrick(sep)
{
	d = new Date();
	trick = d.getYear() + "ie" + d.getMonth() + "t" + d.getDate() + "r" + d.getHours() + "i" + d.getMinutes() + "c" + d.getSeconds() + "k" + d.getMilliseconds();

	if (sep != "?")
	{
		sep = "&";
	}

	return sep + "ietrick=" + trick;
}


/**/
function __heriter(classeEnfant, classeParent)
{
	function heritage() {}
	heritage.prototype = classeParent.prototype;
	
	classeEnfant.prototype 				= new heritage();
	classeEnfant.prototype.constructor 	= classeEnfant;
	classeEnfant.constructeurParent 		= classeParent;
	classeEnfant.classeParent			= classeParent.prototype;
}
/**/


function _heriter(destination, source) { 

	  for (var element in source) { 

	    destination[element] = source[element]; 

	  } 

	}
/**
 * heriatge avec possibilté d'utiliser _super 
 * @param destination
 * @param source
 * @return
 */
function heriter(destination, source) { 

    function initClassIfNecessary(obj) { 
        if( typeof obj["_super"] == "undefined" ) { 
            obj["_super"] = function() { 
                var methodName = arguments[0]; 
                var parameters = arguments[1]; 
                this["__parent_methods"][methodName].apply(this, parameters); 
            } 
        }     

        if( typeof obj["__parent_methods"] == "undefined" ) { 
            obj["__parent_methods"] = {} 
        } 
    } 


    for (var element in source) { 
        if( typeof destination[element] != "undefined" ) { 
            initClassIfNecessary(destination); 
            destination["__parent_methods"][element] = source[element]; 
        } else { 
            destination[element] = source[element]; 
        } 
    } 
} 

	 