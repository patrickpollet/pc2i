/********************************************************
* 	CLASSE DE CHAMP DE SAISIE : Texte	     	*
* 							*
* Permet la saisie de texte sur une ligne dans un champ	*
* input. PP valeur vide NON permise			*
*********************************************************/



//Constructeur de l'objet
function URL()
{
	Texte.call(this);
}


//Erreur si champ vide
URL.prototype.erreur = function ()
{
	var v=trim(this.getValeur());
	if (v != "")
	{
		if (/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i.test(v)) return false;
		this.texteErreur = "adresse internet invalide ";
		//alert(this.texteErreur);
		return true;
	}
	else
		return false;
}

//important de le faire APRES la surcharge des méthodes de la classe Mere 
// avec la version de heriter qui supporte _super !
heriter(URL.prototype, Texte.prototype); 