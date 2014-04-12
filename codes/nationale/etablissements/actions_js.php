<?php

//chaines communes a inclure dans les javascript
$str_confirm_supp=addSlashes(traduction("js_etablissement_supprimer_0")) ;
$str_confirm_supp1=addSlashes(traduction("js_action_annuler")) ;

// NB ce script utilise une globale chemin_serveur

$code=<<<EOC

<script type="text/javascript">
//<![CDATA[

 
function supprimerItem (id) {
    if (confirm("$str_confirm_supp"  +id+ " $str_confirm_supp1"))
        doAction("supprimer",id);
}




function consulterItem (id) {
   doPopup(chemin_serveur+'/codes/acces/etablissement/fiche.php?idq='+id);
}


function modifierItem (id) {
     doPopup(chemin_serveur+'/codes/acces/etablissement/ajout.php?idq='+id);
}


//]]>
</script>

EOC;

echo $code;
?>
