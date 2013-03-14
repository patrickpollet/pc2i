// JavaScript Document


/**
 * rev 981 remplacement vieux script MMshowHideLayers
 * garde les memes argument pour compatibilté 
 * @param theDiv
 * @param dummy
 * @param show
 * @return
 */
function showHide (theDiv,dummy,show)  {
	 var f = document.getElementById(theDiv);
     if (f!=null) {
    	 v=(show=='show')?'visible':(show=='hide')?'hidden':show;
    	 f.style.visibility=v;
     }
}



function openPopup(theURL,winName,width,height,features) { 
	if (!features)
		features='scrollbars=yes,resizable=yes';
	features ="height="+height+",width="+width+","+features;
	// v 1.41 centrage du popup 
			var top=(screen.height-height)/2;
			var left=(screen.width-width)/2;
			features="top="+top+",left="+left+","+features;
       // alert(features)
	    window.open(theURL,winName,features);
	}

	
function file(fichier)
{
	if(window.XMLHttpRequest) // FIREFOX
		xhr_object = new XMLHttpRequest(); 
	else if(window.ActiveXObject) // IE
		xhr_object = new ActiveXObject("Microsoft.XMLHTTP"); 
	else 
		return(false); 
	xhr_object.open("GET", fichier, false); 
	xhr_object.send(null); 
}


function imprimer(){
	window.print() ;
}

function redirect(url) {
	document.location.href=url;
}


function checkall(form) {
	  void(el=form.getElementsByTagName('INPUT'));
	  for(i=0;i<el.length;i++) {
		  if(el[i].type=="checkbox")
		   void(el[i].checked=1);
	  }
	}

function uncheckall(form) {
	  void(el=form.getElementsByTagName('INPUT'));
	  for(i=0;i<el.length;i++) {
		  if(el[i].type=="checkbox")
			  void(el[i].checked=0);
	  }
	}

/*
 * effacer tous les critères de selection 
 * mais pas de tri et autres cachés 
 */
function clear_criteres(){
      var f = document.getElementById('form_criteres');
       if (f!=null) {
         for (var i = 0; (node = f.getElementsByTagName("input").item(i)); i++) {
           if (node.type !='hidden') node.value="";
           //else alert(node.name);
         }
         for (var i = 0; (node = f.getElementsByTagName("select").item(i)); i++) {
            node.value="";
         }
        f.submit();
       }
}



function majDiv(theDiv,theScript,theSpinner,theForm) {
	if (theSpinner) Element.show(theSpinner);

    var ar=new Ajax.Updater(theDiv,theScript,{parameters : Form.serialize($(theForm)) ,
                        evalScripts:true, 
                        onComplete : function () {
    	 						if (theSpinner) Element.hide(theSpinner);
    	 						Element.show(theDiv);
                          },
                        onFailure: function(transport){
                              alert("une erreur s'est produite, le serveur est peut-etre temporairement inaccessible");}
                        });
}


function majAjax (theScript,theSpinner,theForm) {
	if (theSpinner) Element.show(theSpinner);
	var ar=new Ajax.Request(theScript,{ parameters : Form.serialize($(theForm)),
		 onComplete : function () {
			if (theSpinner) Element.hide(theSpinner);
			var retour = ar.transport.responseText.evalJSON();
			if (retour.erreur=="") {
				for (var champ in retour) {
					if (champ !="erreur")
						$(champ).value=retour[champ];
				}
			}else {
				alert(retour.erreur);
			}		
		},
		onFailure: function(transport){
			alert("une erreur s'est produite, le serveur est peut-etre temporairement inaccessible");}
});
	
}



function setDefaut(input,defaut) {
	document.getElementById(input).value=document.getElementById(defaut).value;
}

/* This script and many more are available free online at
The JavaScript Source :: http://javascript.internet.com
Created by: Dustin Diaz :: http://www.dustindiaz.com/ */

function getElementsByClassName(searchClass,node,tag) {
  var classElements = new Array();
  if (node == null)
    node = document;
  if (tag == null)
    tag = '*';
  var els = node.getElementsByTagName(tag);
  var elsLen = els.length;
  var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
  for (i = 0, j = 0; i < elsLen; i++) {
    if (pattern.test(els[i].className) ) {
      classElements[j] = els[i];
      j++;
    }
  }
  return classElements;
}

function unmaskPassword(id) {
	  var pw = document.getElementById(id);
	  var chb = document.getElementById(id+'unmask');

	  try {
	    // first try IE way - it can not set name attribute later
	    if (chb.checked) {
	      var newpw = document.createElement('<input type="text" name="'+pw.name+'">');
	    } else {
	      var newpw = document.createElement('<input type="password" name="'+pw.name+'">');
	    }
	    newpw.attributes['class'].nodeValue = pw.attributes['class'].nodeValue;
	  } catch (e) {
	    var newpw = document.createElement('input');
	    newpw.setAttribute('name', pw.name);
	    if (chb.checked) {
	      newpw.setAttribute('type', 'text');
	    } else {
	      newpw.setAttribute('type', 'password');
	    }
	    newpw.setAttribute('class', pw.getAttribute('class'));
	  }
	  newpw.id = pw.id;
	  newpw.size = pw.size;
	  newpw.onblur = pw.onblur;
	  newpw.onchange = pw.onchange;
	  newpw.value = pw.value;
	  pw.parentNode.replaceChild(newpw, pw);
	}

