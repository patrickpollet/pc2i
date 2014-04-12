/********************************************************
* 	CLASSE DE CHAMP DE SAISIE : Texte	     	*
* 							*
* Permet la saisie de texte sur une ligne dans un champ	*
* input. PP valeur vide NON permise			*
*********************************************************/



//Constructeur de l'objet
function TexteNV()
{
	Texte.call(this);
}


//Erreur si champ vide
TexteNV.prototype.erreur = function ()
{
	if(trim(this.getValeur()) == "")
	{
		this.texteErreur = "Aucune saisie effectu&eacute;e !";
		//alert(this.texteErreur);
		return true;
	}
	else
		return false;
}

//important de le faire APRES la surcharge des méthodes de la classe Mere 
// avec la version de heriter qui supporte _super !
heriter(TexteNV.prototype, Texte.prototype); 