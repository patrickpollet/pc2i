function enregistre_reponse(user,examen,reponse,session_ch) {
 // On envoie les valeurs au serveur
 var request_url = '../../commun/ajax/enregistrement.php?user='+user+'&examen='+examen+'&reponse='+reponse+'&'+session_ch;
	
 //alert(request_url);
 new Ajax.Request(request_url,
	{
		method:'post',
		//parameters : {'famille' : encodeURI(famille), 'descf' : encodeURI(descf)},
		onSuccess: function(transport){
			var retour = transport.responseText.evalJSON();  //donne une erreur sous FF ??? 
			if (retour.result != 'ok') {
				alert(retour.result);
			}
		},
		onFailure: function(transport){
			alert("une erreur s'est produite, le serveur est peut-etre temporairement inaccessible");
		}
	});
}
