//Objet servant à l'édition de la valeur dans la page
var champ = null;

//On ne pourra éditer qu'une valeur à la fois
var editionEnCours = false;

//variable évitant une seconde sauvegarde lors de la suppression de l'input
var sauve = false;

// variable contenant un div d'info //PP
var info = null;


//Fonction de modification inline de l'élément double-cliqué
//ajout PP l'URL à appeler 
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
	
     //Création de l'objet dont le nom de classe est passé en paramètre
	champ = eval('new ' + classe + '();');
	
	//Assignation des différentes propriétés
	champ.valeur = obj.innerText ? obj.innerText : obj.textContent;
	champ.valeur = trim(champ.valeur);
	
	champ.oldValeur=champ.valeur; //PP
	champ.URL=URLAjax; //PP 
	
	champ.id = id;
	champ.nomChamp = nomChamp;

	//Remplacement du texte par notre objet input
	champ.remplacerTexte(obj, sauverMod,annulerMod, extra);

	//"Activation" du champ (focus, sélection ou autres...)
	champ.activerChamp();
}


//Objet XMLHTTPRequest
var XHR = null;

//Fonction de sauvegarde des modifications apportées
function sauverMod()
{
	//Si on a déjà sauvé la valeur en cours, on sort
	if(sauve)
	{
		return false;
	}
	else
	{
		sauve = true;
	}

	//Vérification d'erreur
	if(champ.erreur())
	{
		erreur(champ.texteErreur);
		champ.texteErreur=""; // reseta tantq ue je n'arrive pas à faire marcher _super !
		sauve = false;
		return false;
	}
		
	//Si l'objet existe déjà on abandonne la requête et on le supprime
	if(XHR && XHR.readyState != 0)
	{
		XHR.abort();
		delete XHR;
	}
	
	
	if (champ.getValeur() == champ.oldValeur)  { //PP rien a faire ;-)
        
		 rienChangeMod();
         return true; 
       }

	

	//Création de l'objet XMLHTTPRequest
	XHR = getXMLHTTP();

	if(!XHR)
	{
		return false;
	}
	
	//URL du script de sauvegarde auquel on passe la requête à exécuter
	XHR.open("GET", champ.URL+"?champ=" + escape(champ.nomChamp) + "&valeur=" + escape(champ.getValeur()) + "&echap=" + champ.echaperValeur() + "&id=" + champ.id + ieTrick(), true);

	//On se sert de l'événement OnReadyStateChange pour supprimer l'input et le replacer par son contenu
	XHR.onreadystatechange = function()
	{
		//Si le chargement est terminé
		if (XHR.readyState == 4)
			if(!XHR.responseText)
			{
				//Réinitialisation de la variable d'état d'édition
				editionEnCours = false;

				//Sortie du mode d'édition
				champ.terminerEdition();
				
				//Réinitialisation de l'affichage d'erreur
				 succes("Modification enregistr&eacute;e");
				
				return true;
			}
			else //S'il y a une réponse texte, c'est une erreur PHP
			{
				//Affichage de l'erreur
				erreur("Erreur de communication "+XHR.status+" msg "+XHR.responseText); 
				champ.annulerEdition();
				sauve = false;
				return false;
			}
	}

	//Envoi de la requête
	XHR.send(null);
}

//Fonction d'annulation des modifications apportées  PP
function annulerMod()
{
	//Réinitialisation de la variable d'état d'édition
	editionEnCours = false;
	//Remplacement de l'input par le texte qu'il contient
	
	champ.annulerEdition();
	informe("Edition annul&eacute;e");
}

//Fonction d'annulation des modifications apportées  PP
function rienChangeMod()
{
	//Réinitialisation de la variable d'état d'édition
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



