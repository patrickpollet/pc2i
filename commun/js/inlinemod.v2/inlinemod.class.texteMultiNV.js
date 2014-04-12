/********************************************************
* 	CLASSE DE CHAMP DE SAISIE : Texte	     	*
* 							*
* Permet la saisie de texte sur une ligne dans un champ	*
* input. PP valeur vide NON permise			*
*********************************************************/



//Constructeur de l'objet
function TexteMultiNV()
{
	TexteMulti.call(this);
}


//Erreur si champ vide
TexteMultiNV.prototype.erreur = function ()
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

//important de le faire APRES la surcharge des m�thodes de la classe Mere 
// avec la version de heriter qui supporte _super !
heriter(TexteMultiNV.prototype, TexteMulti.prototype); 