<?php

//chaines communes a inclure dans les javascript
//$str_confirm_supp=addSlashes(traduction("js_parcours_supprimer_0")) ;
//$str_confirm_supp1=addSlashes(traduction("js_parcours_supprimer_1")) ;



$code=<<<EOC

<script type="text/javascript">
//<![CDATA[
function consulterItem (idq,ide) {
   doPopup('passage.php?idq='+idq+'&ide='+ide);
}
//]]>
</script>

EOC;

echo $code;
?>
