<?php

//chaines communes a inclure dans les javascript
$str_confirm_supp=addSlashes(traduction("js_examen_supprimer_0")) ;
$str_confirm_supp1=addSlashes(traduction("js_examen_supprimer_1")) ;



$str_confirm_supp_pool=addSlashes(traduction("js_pool_supprimer_0"));
$str_confirm_supp_pool1=addSlashes(traduction("js_pool_supprimer_1"));
$str_confirm_supp_fils=addSlashes(traduction("js_examen_fils_supprimer_0"));



//	"js_examen_fils_supprimer_0" => "attention ! cet examen %s est membre d un pool. Vous allez donc réduire le nombre d examens du pool %s",

//	"js_pool_supprimer_0" => "attention ! vous êtes sur le point de supprimer le pool d examens numero :",
//	"js_pool_supprimer_1" => ". Ceci supprimera automatiquement les %s membres de ce pool ",

$code=<<<EOC

<script type="text/javascript">
//<![CDATA[

 
function supprimerItem (id) {
    if (confirm("$str_confirm_supp"  +id+ " $str_confirm_supp1"))
        doAction("supprimer",id);
}


function supprimerPoolPere (id,nb) {
	 if (confirm("$str_confirm_supp_pool"  +id+ sprintf("$str_confirm_supp_pool1",nb)+" $str_confirm_supp1"))
        doAction("supprimer",id);
}


function supprimerPoolFils (id,pere) {
	if (confirm(sprintf("$str_confirm_supp_fils",id,pere)+" $str_confirm_supp1"))
        doAction("supprimer",id);
}



function consulterItem (id) {
   doPopup('fiche.php?id='+id);
}

function dupliquerItem (id) {
    doPopup('ajout.php?dup_id='+id);
}


function modifierItem (id) {
     doPopup('ajout.php?id='+id);
}


function nouvelItem () {
    doPopup('ajout.php?id=-1');
}

//]]>
</script>

EOC;

echo $code;
?>
