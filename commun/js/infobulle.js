/* ----- info-bulle.js ----- */
// http://cipcnet.insa-lyon.fr/portal_javascripts/info-bulle.js?original=1
var IB=new Object;var posX=0;var posY=0;var xOffset=10;var yOffset=10;



function AffBulle(texte) {
	contenu = '<table style="border: 0;"  cellpadding="' + IB.NbPixel + '">';
	contenu += '<tr style="background-color: ' + IB.ColContour + '">';
	contenu += '<td>';
	contenu += '<table style="border: 0; background-color: ' + IB.ColFond + ';"   >';
	contenu += '<tr>';
	contenu += '<td style="font-family: arial; font-size: 0.9em; color:'
			+ IB.ColTexte + '">' + texte + '</td>';
	contenu += '</tr>';
	contenu += '</table>';
	contenu += '</td>';
	contenu += '</tr>';
	contenu += '</table>&nbsp;';
	var finalPosX = posX - xOffset;
	if (finalPosX < 0)
		finalPosX = 0;
	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = posY + yOffset;
		document.layers["bulle"].left = finalPosX;
		document.layers["bulle"].visibility = "show"
	} else if (document.all) {
		bulle.innerHTML = contenu;
		document.all["bulle"].style.top = posY + yOffset;
		document.all["bulle"].style.left = finalPosX;
		document.all["bulle"].style.visibility = "visible"
	} else if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset;
		document.getElementById("bulle").style.left = finalPosX;
		document.getElementById("bulle").style.visibility = "visible"
	}
}
function getMousePos(e) {
	if (document.all) {
		posX = event.x + document.body.scrollLeft;
		posY = event.y + document.body.scrollTop
	} else {
		posX = e.pageX;
		posY = e.pageY
	}
}
function HideBulle() {
	if (document.layers) {
		document.layers["bulle"].visibility = "hide"
	} else if (document.all) {
		document.all["bulle"].style.visibility = "hidden"
	} else if (document.getElementById) {
		document.getElementById("bulle").style.visibility = "hidden"
	}
}
function InitBulle(ColTexte, ColFond, ColContour, NbPixel) {

	//alert(chemin_images);
	IB.ColTexte = ColTexte;
	IB.ColFond = ColFond;
	IB.ColContour = ColContour;
	IB.NbPixel = NbPixel;
	if (document.layers) {
		window.captureEvents(Event.MOUSEMOVE);
		window.onMouseMove = getMousePos;
		document
				.write('<layer name="bulle" top="0" left="0" visibility="hide"></layer>')
	} else if (document.all) {
		document.onmousemove = getMousePos;
		document
				.write('<div id="bulle" style="position:absolute; top:0; left:0; visibility:hidden;"></div>')
	} else if (document.getElementById) {
		document.onmousemove = getMousePos;
		document
				.write('<div id="bulle" style="position:absolute; top:0px; left:0px; visibility:hidden;"></div>')
	}
}
function AffBulle2(strTitre, strIcone, texte) {
	// alert(chemin_images);
	strIcone = chemin_images + '/bulles/'+strIcone; // PP
	
	var contenu = '<table Id="HelpTable" style="width: 335px;"  >';
	contenu += '<tr style="height: 30px;">';
	contenu += '<td style="width: 10px; background: url(' + chemin_images + '/bulles/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
	contenu += '<td style="width: 30px; background: url(' + chemin_images + '/bulles/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
	contenu += '<td style="width: 285px; background: url(' + chemin_images + '/bulles/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
	contenu += '<td style="width: 10px; background: url(' + chemin_images + '/bulles/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';
	if (strTitre != "") {
		contenu += '<tr style="height: 30px;">';
		contenu += '<td style="width: 10px; background: url(' + chemin_images + '/bulles/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu += '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
		contenu += '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
		contenu += '<b>' + strTitre + '</b>';
		contenu += '</td>';
		contenu += '<td style="width: 10px; background: url(' + chemin_images + '/bulles/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu += '</tr>'
	}
	contenu += '<tr> ';
	contenu += '<td style="width: 10px; background: url(' + chemin_images + '/bulles/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
	contenu += '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;"><div style="overflow:auto; width: 300px;">' + texte + '</div></td>';
	contenu += '<td style="width: 10px; background: url(' + chemin_images + '/bulles/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
	contenu += '</tr>';
	contenu += '<tr style="height: 10px;">';
	contenu += '<td style="width: 10px; background: url(' + chemin_images + '/bulles/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
	contenu += '<td colspan="2" style="width: 305px; background: url(' + chemin_images + '/bulles/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
	contenu += '<td style="width: 10px; background: url(' + chemin_images + '/bulles/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';
	contenu += '</table>';
	var finalPosX = posX - xOffset;
	if (finalPosX < 0)
		finalPosX = 0;
	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = posY + yOffset + "px";
		document.layers["bulle"].left = finalPosX + "px";
		document.layers["bulle"].visibility = "show"
	} else if (document.all) {
		bulle.innerHTML = contenu;
		document.all["bulle"].style.top = posY + yOffset + "px";
		document.all["bulle"].style.left = finalPosX + "px";
		document.all["bulle"].style.visibility = "visible"
	} else if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset + "px";
		document.getElementById("bulle").style.left = finalPosX + "px";
		document.getElementById("bulle").style.visibility = "visible"
	}
}
