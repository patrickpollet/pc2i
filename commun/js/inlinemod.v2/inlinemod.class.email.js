/********************************************************
* 	CLASSE DE CHAMP DE SAISIE : Texte	     	*
* 							*
* Permet la saisie de texte sur une ligne dans un champ	*
* input. PP valeur vide NON permise			*
*********************************************************/



//Constructeur de l'objet
function Email()
{
	Texte.call(this);
}


//Erreur si champ vide
Email.prototype.erreur = function ()
{
	var v=trim(this.getValeur());
	if (v != "")
	{
		if( /\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/.test(v)) return false;
		
		this.texteErreur = "adresse mail invalide ";
		//alert(this.texteErreur);
		return true;
	}
	else
		return false;
}

//important de le faire APRES la surcharge des méthodes de la classe Mere 
// avec la version de heriter qui supporte _super !
heriter(Email.prototype, Texte.prototype); 