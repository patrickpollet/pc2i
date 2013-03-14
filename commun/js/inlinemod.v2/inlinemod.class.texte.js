/********************************************************
* 	CLASSE DE CHAMP DE SAISIE : Texte	     			*
* 														*
* Permet la saisie de texte sur une ligne dans un champ	*
* input													*
*********************************************************/

/*
 * version PP juin 2009 classe Mere de toutes les classes 
 * associées à des saisie filtrées
 */
var input = null;

//Constructeur de l'objet
function Texte()
{
	this.id = -1;
	this.valeur = "";
	this.nomChamp = "";
	this.parent = null;
	this.texteErreur = "";
	this.URL="";
	this.oldValeur="";
	this.extra="";
}

//Fonction de remplacement du texte de parent par le champ
Texte.prototype.remplacerTexte = function (parent, sauvegarde,annulation)
{
	if(!parent || !sauvegarde || !annulation)
	{
		return false;
	}
	else
	{
		this.parent = parent;
	}

	input = document.createElement("input");
	
	input.value = this.valeur;
	input.style.width = getTextWidth(this.valeur) + 10 + "px";
	
	//Assignation des evenements qui declencheront la sauvegarde de la valeur
	
	//Sortie de l'input
	input.onblur = function ()
	{
		sauvegarde.call(this);
	};

	//Appui sur la touche Entrée ou echap 
	input.onkeydown = function keyDown(event)
	{
		if((window.event && (getKeyCode(window.event) == 13)) || (getKeyCode(event) == 13))
		{
			sauvegarde.call(this);
		}	
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
	
	
	//parent.replaceChild(input, parent.firstChild);
}

//Fonction permettant de recuperer la valeur du champ
Texte.prototype.getValeur = function ()
{
	return trim(input.value);
}

//Fonction d'activation du champ
Texte.prototype.activerChamp = function ()
{
	input.focus();
	input.select();
}

//Fonction de sortie du mode d'edition
Texte.prototype.terminerEdition = function ()
{
	this.parent.replaceChild(document.createTextNode(this.getValeur()), this.parent.firstChild);
	delete input;
}

//Fonction de sortie du mode d'edition
Texte.prototype.annulerEdition = function ()
{
	this.parent.replaceChild(document.createTextNode(this.oldValeur), this.parent.firstChild);
	delete input;
}


//Fonction determinant si la valeur passee au script PHP doit etre formatee par celui-ci ou pas (avec mysql_real_escape_string($str);)
//Ici oui, car il s'agit de texte
Texte.prototype.echaperValeur = function ()
{
	return "true";
}

//PAS d'Erreur meme si champ vide
Texte.prototype.erreur = function ()
{
	return false;
}