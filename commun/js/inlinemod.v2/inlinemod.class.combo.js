/****************************************************************
*	CLASSE DE CHAMP DE SAISIE : combo	     	*
*								*
* Permet la saisie de texte via un select	*
* NON TESTE.							*
*****************************************************************/


//Constructeur de l'objet
function Combo()
{
	Texte.call(this);
}


//Fonction de remplacement du texte de parent par un champ textarea
Combo.prototype.remplacerTexte = function (parent, sauvegarde, annulation,tableau)
{
	if(!parent || !sauvegarde || ! annulation || ! tableau)
	{
		return false;
	}
	else
	{
		this.parent = parent;
	}
    //en utilisant input on n'a pas besoin de surcharger les autres méthodes
	input = document.createElement("select");
	
	//input.appendChild(document.createElement("option")) ;
    var k ;
    for (i = 0 ; i < tab.length ; i++) {
        if (navigator.appName == "Microsoft Internet Explorer") {
            k = new Option(tab[i][1],tab[i][0]);
            input.add(k);
        }
        else {
            var k = document.createElement("option");
            k.value = tab[i][0] ;
            k.text = tab[i][1] ;
            input.appendChild(k) ;
        }
    }
	
	
	input.value = this.valeur;
	input.style.width = getTextWidth(this.valeur) + 30 + "px";

	
	//Assignation des événements qui déclencheront la sauvegarde de la valeur
	
	//Sortie du textarea
	
	input.onBlur = function ()
	{
		sauvegarde.call(this);
	};

	//Appui sur la touche echap (entrée est permise !) 
	input.onkeydown = function keyDown(event)
	{
		if((window.event && (getKeyCode(window.event) == 27)) || (getKeyCode(event) == 27))
		{
			annulation.call(this);
		}
			
	};
	//Remplacement du texte par notre objet input
	// PP sécurité si l'objet était vide ! (colonne de BD nulle )
	if (parent.firstChild) 
		parent.replaceChild(input, parent.firstChild);
	else
		parent.appendChild(input); 
	
	//parent.replaceChild(textarea, parent.firstChild);
}



Combo.prototype.echaperValeur = function() {
    return "false" ;
}
 
Combo.prototype.erreur = function() {
    if (this.getValeur() == "") {
        this.texteErreur = "Aucune saisie effectuée !" ;
        return true ;
    }
    else return false ;
}


//important de le faire APRES la surcharge des méthodes de la classe Mere 
//avec la version de heriter qui supporte _super !
heriter(Combo.prototype, Texte.prototype); 
