//Objet servant � l'�dition de la valeur dans la page
var champ = null;

//On ne pourra �diter qu'une valeur � la fois
var editionEnCours = false;

//variable �vitant une seconde sauvegarde lors de la suppression de l'input
var sauve = false;

// variable contenant un div d'info //PP
var info = null;


//Fonction de modification inline de l'�l�ment double-cliqu�
//ajout PP l'URL � appeler 
function inlineMod(id, obj, nomChamp, classe, URLAjax, extra)
{
	if(editionEnCours)
	{
		if(XHR && XHR.readyState != 0)	{  //PP 
			XHR.abort();
			delete XHR;      
		}
	   // pb : enlever l'ancien input ...
		if (champ !=null) champ.annulerEdition();
		editionEnCours = false;
		return false;
	}
	else
	{
		editionEnCours = true;
		sauve = false;
	}
	
	 info=document.getElementById("erreurMsg");
     
     informe("Edition en cours. Echap. pour annuler ou cliquez ailleurs pour valider");        
	
     //Cr�ation de l'objet dont le nom de classe est pass� en param�tre
	champ = eval('new ' + classe + '();');
	
	//Assignation des diff�rentes propri�t�s
	champ.valeur = obj.innerText ? obj.innerText : obj.textContent;
	champ.valeur = trim(champ.valeur);
	
	champ.oldValeur=champ.valeur; //PP
	champ.URL=URLAjax; //PP 
	
	champ.id = id;
	champ.nomChamp = nomChamp;

	//Remplacement du texte par notre objet input
	champ.remplacerTexte(obj, sauverMod,annulerMod, extra);

	//"Activation" du champ (focus, s�lection ou autres...)
	champ.activerChamp();
}


//Objet XMLHTTPRequest
var XHR = null;

//Fonction de sauvegarde des modifications apport�es
function sauverMod()
{
	//Si on a d�j� sauv� la valeur en cours, on sort
	if(sauve)
	{
		return false;
	}
	else
	{
		sauve = true;
	}

	//V�rification d'erreur
	if(champ.erreur())
	{
		erreur(champ.texteErreur);
		champ.texteErreur=""; // reseta tantq ue je n'arrive pas � faire marcher _super !
		sauve = false;
		return false;
	}
		
	//Si l'objet existe d�j� on abandonne la requ�te et on le supprime
	if(XHR && XHR.readyState != 0)
	{
		XHR.abort();
		delete XHR;
	}
	
	
	if (champ.getValeur() == champ.oldValeur)  { //PP rien a faire ;-)
        
		 rienChangeMod();
         return true; 
       }

	

	//Cr�ation de l'objet XMLHTTPRequest
	XHR = getXMLHTTP();

	if(!XHR)
	{
		return false;
	}
	
	//URL du script de sauvegarde auquel on passe la requ�te � ex�cuter
	// pb avec UTF8 et la fonction javascript escape
//	XHR.open("GET", champ.URL+"?champ=" + escape(champ.nomChamp) + "&valeur=" + escape(champ.getValeur()) + "&echap=" + champ.echaperValeur() + "&id=" + champ.id + ieTrick(), true);
	XHR.open("GET", champ.URL+"?champ=" + champ.nomChamp + 
			                  "&valeur=" + encodeURI(champ.getValeur()) +
			                  "&echap=" + champ.echaperValeur() + 
			                  "&id=" + champ.id + ieTrick(), true);

	//On se sert de l'�v�nement OnReadyStateChange pour supprimer l'input et le replacer par son contenu
	XHR.onreadystatechange = function()
	{
		//Si le chargement est termin�
		if (XHR.readyState == 4)
			if(!XHR.responseText)
			{
				//R�initialisation de la variable d'�tat d'�dition
				editionEnCours = false;

				//Sortie du mode d'�dition
				champ.terminerEdition();
				
				//R�initialisation de l'affichage d'erreur
				 succes("Modification enregistr&eacute;e");
				
				return true;
			}
			else //S'il y a une r�ponse texte, c'est une erreur PHP
			{
				//Affichage de l'erreur
				erreur("Erreur de communication "+XHR.status+" msg "+XHR.responseText); 
				champ.annulerEdition();
				sauve = false;
				return false;
			}
	}

	//Envoi de la requ�te
	XHR.send(null);
}

//Fonction d'annulation des modifications apport�es  PP
function annulerMod()
{
	//R�initialisation de la variable d'�tat d'�dition
	editionEnCours = false;
	//Remplacement de l'input par le texte qu'il contient
	
	champ.annulerEdition();
	informe("Edition annul&eacute;e");
}

//Fonction d'annulation des modifications apport�es  PP
function rienChangeMod()
{
	//R�initialisation de la variable d'�tat d'�dition
	editionEnCours = false;
	//Remplacement de l'input par le texte qu'il contient
	champ.annulerEdition();
	informe("Rien de chang&eacute;");
}


function erreur (msg) {
	if (info !=null) {
             info.innerHTML="<font color='red'>"+msg+"</font>"; 
        } else alert (msg);
}

function informe (msg) {
       if (info !=null) {
            info.innerHTML="<font color='orange'>"+msg+"</font>";
       }
}

function succes (msg) {
       if (info !=null) {
            info.innerHTML="<font color='green'>"+msg+"</font>";
       }
}



