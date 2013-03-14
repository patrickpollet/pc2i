/****************************************************************
*	CLASSE DE CHAMP DE SAISIE : TexteMulti		     	*
*								*
* Permet la saisie de texte sur plusieurs lignes dans un champ	*
* textarea.							*
*****************************************************************/


//Constructeur de l'objet
function TexteMulti()
{
	Texte.call(this);
}



//Fonction de remplacement du texte de parent par un champ textarea
TexteMulti.prototype.remplacerTexte = function (parent, sauvegarde, annulation)
{
	if(!parent || !sauvegarde || ! annulation )
	{
		return false;
	}
	else
	{
		this.parent = parent;
	}
    //en utilisant input on n'a pas besoin de surcharger les autres méthodes
	input = document.createElement("textarea");
	
	input.value = this.valeur;
	input.style.width = getTextWidth(this.valeur) + 30 + "px";
	input.style.height =parent.offsetHeight+30 +"px"; //getTextHeight(input.value)+30 +"px";
	
	//Assignation des événements qui déclencheront la sauvegarde de la valeur
	
	//Sortie du textarea
	
	input.onblur = function ()
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

//important de le faire APRES la surcharge des méthodes de la classe Mere 
//avec la version de heriter qui supporte _super !
heriter(TexteMulti.prototype, Texte.prototype); 
