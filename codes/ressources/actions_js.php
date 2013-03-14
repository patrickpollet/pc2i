<?php

//chaines communes a inclure dans les javascript
$str_confirm_supp=addSlashes(traduction("js_ressource_supprimer_0")) ;
$str_confirm_supp1=addSlashes(traduction("js_ressource_supprimer_1")) ;



$code=<<<EOC

<script type="text/javascript">
//<![CDATA[

function supprimerItem (id) {
    if (confirm("$str_confirm_supp"  +id+ " $str_confirm_supp1"))
        doAction("supprimer",id);
}

function filtrerItem (id) {
        doAction("filtrer",id);
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
