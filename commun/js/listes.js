// moveOptionsAcross
//
// Move selected options from one select list to another
// http://blog.pothoven.net/2006/10/move-options-across-select-lists.html
//


var somethingMoved=0; //ajout PP 11/05/2009

function moveOptionsAcross(fromSelectList, toSelectList) {
  var selectOptions = fromSelectList.getElementsByTagName('option');
  for (var i = 0; i < selectOptions.length; i++) {
     var opt = selectOptions[i];
     if (opt.selected) {
      fromSelectList.removeChild(opt);
      toSelectList.appendChild(opt);

 // originally, this loop decremented from length to 0 so that you
 // wouldn't have to worry about adjusting the index.  However, then
 // moving multiple options resulted in the order being reversed from when
 // was in the original selection list which can be confusing to the user.
 // So now, the index is adjusted to make sure we don't skip an option.
      i--;
     }
   }

// simuleClick(toSelectList);
 //simuleClick(fromSelectList);
  fromSelectList.focus();
  toSelectList.focus();
  somethingMoved=1;    // pour controle lors de la fermeture si nécessaire 
  
}

function simuleClick(target) {
	  
	  oEvent = document.createEvent( "MouseEvents" );
	  oEvent.initMouseEvent(
	    "click",    // le type d'événement souris
	    true,       // est-ce que l'événement doit se propager (bubbling) ?
	    true,       // est-ce que le défaut pour cet événement peut être annulé ?
	    window,     // l' 'AbstractView' pour cet événement
	    1,          // details -- Pour les événements click, le nombre de clicks
	    1,          // screenX
	    1,          // screenY
	    1,          // clientX
	    1,          // clientY
	    false,      // est-ce que la touche Ctrl est pressée ?
	    false,      // est-ce que la touche Alt est pressée ?
	    false,      // est-ce que la touche Shift est pressée ?
	    false,      // est-ce que la touche Meta est pressée ?
	    0,          // quel est le bouton pressé
	    target      // l'élément source de cet événement
	  );
	  //FF 
	  try {
	  target.dispatchEvent('oEvent');
	  //IE
	  } catch ( e) {target.fireEvent('oEvent');}
}

function selectAll(liste) {
     var selectOptions = liste.getElementsByTagName('option');
    for (var i = 0; i < selectOptions.length; i++) {
         var opt = selectOptions[i];
         opt.selected="selected";
    }
}

function addAll(fromList,toList) {
    selectAll(fromList);
    moveOptionsAcross(fromList,toList);
}


function majNombre(select,num) {
	document.getElementById(num).innerHTML=document.getElementById(select).options.length;
}
