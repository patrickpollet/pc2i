<?php

//chaines communes a inclure dans les javascript
$str_confirm_supp=addSlashes(traduction("js_personnel_supprimer_0")) ;
$str_confirm_supp1=addSlashes(traduction("js_personnel_supprimer_1")) ;

/**
 * attention il faut toujours passer aussi l'établissement visé '
 * pour gérer l'usage par un admin d'établissement avec composantes ou le super_admin
 */

$code=<<<EOC

<script type="text/javascript">
//<![CDATA[

 function supprimerItem (id) {
    if (confirm("$str_confirm_supp"  +id+ " $str_confirm_supp1"))
        doAction("supprimer",id);
}


function consulterItem (id,ide) {
   doPopup('fiche.php?id='+id+'&ide='+ide);
}




function modifierItem (id,ide) {
     doPopup('ajout.php?id='+id+'&ide='+ide);
}


function nouvelItem (ide) {
    doPopup('ajout.php?id=-1&ide='+ide);
}





//]]>
</script>

EOC;

echo $code;
?>
