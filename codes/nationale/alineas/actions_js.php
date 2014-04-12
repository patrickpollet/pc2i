<?php

//chaines communes a inclure dans les javascript
$str_confirm_supp=addSlashes(traduction("js_alinea_supprimer_0")) ;
$str_confirm_supp1=addSlashes(traduction("js_action_annuler")) ;



$code=<<<EOC

<script type="text/javascript">
//<![CDATA[

function supprimerItem (id) {
    if (confirm("$str_confirm_supp"  +id+ " $str_confirm_supp1"))
        doAction("supprimer",id);
}




function consulterItem (id) {
   doPopup('fiche.php?id='+id);
}


//]]>
</script>

EOC;

echo $code;
?>
