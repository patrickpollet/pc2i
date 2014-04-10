/****************************************************************
*	CLASSE DE CHAMP DE SAISIE : Nombre			*
*								*
* Permet la saisie de nombre sur une ligne dans un champ 	*
* input.							*
*****************************************************************/



//Constructeur de l'objet
function Nombre()
{
	TexteNV.call(this);
}



//Fonction d�terminant si la valeur pass�e au script PHP doit �tre format�e par celui-ci ou pas (avec mysql_real_escape_string($str);)
//Ici non, car il s'agit de valeur num�rique
Nombre.prototype.echaperValeur = function ()
{
	return "false";
}

//Si le texte entr� ne repr�sente pas un nombre, on renvoie true
Nombre.prototype.erreur = function ()
{
	
	//marche pas renvoie undefined meme en cas d'erreur ???
	//if (this._super("erreur",arguments)) return true;

	/****
	this._super("erreur",arguments);  
	//ok mais ne reset pas le texteErreur !
	if (this.texteErreur!="") return true;
    ****/
	var v=this.getValeur();
	if(v =="" || isNaN(v))
	{
		this.texteErreur = "Vous devez entrer une valeur num&eacute;rique !";
		return true;
	}
	else
		return false;
}

//important de le faire APRES la surcharge des m�thodes de la classe Mere 
// si on utilise la version de h�riter qui supporte _super ! 
heriter(Nombre.prototype, TexteNV.prototype); 