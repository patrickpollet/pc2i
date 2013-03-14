/****************************************************************
*	CLASSE DE CHAMP DE SAISIE : Nombre			*
*								*
* Permet la saisie de nombre sur une ligne dans un champ 	*
* input.							*
*****************************************************************/



//Constructeur de l'objet
function Entier()
{
	Nombre.call(this);
}




//Si le texte entré ne représente pas un nombre, on renvoie true
Entier.prototype.erreur = function ()
{
	
	//marche pas renvoie undefined meme en cas d'erreur ???
	//if (this._super("erreur",arguments)) return true;

	/**this._super("erreur",arguments);  
	//ok mais ne reset pas le texteErreur !
	if (this.texteErreur!="") return true;
	**/
	var v=this.getValeur();
	if (v =="" || /[^\d]/.test(v)) {
		this.texteErreur = "valeur entière invalide ";
		//alert(this.texteErreur);
		return true;
	}
	return false;
}

//important de le faire APRES la surcharge des méthodes de la classe Mere 
// si on utilise la version de hériter qui supporte _super ! 
heriter(Entier.prototype,Nombre.prototype); 