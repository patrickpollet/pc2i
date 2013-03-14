<?php

//chaines communes a inclure dans les javascript
$str_confirm_supp=addSlashes(traduction("js_question_supprimer_0")) ;
$str_confirm_inval=addSlashes(traduction("js_question_invalider_0")) ;
$str_confirm_supp1=addSlashes(traduction("js_question_supprimer_1")) ;
$str_confirm_inval1=addSlashes(traduction("js_question_invalider_1")) ;


$code=<<<EOC

<script type="text/javascript">
//<![CDATA[
function clear_criteres_questions(){
      var f = document.getElementById('form_criteres');
       if (f!=null) {
         for (var i = 0; (node = f.getElementsByTagName("input").item(i)); i++) {
           if (node.type !='hidden') node.value="";
         }
         for (var i = 0; (node = f.getElementsByTagName("select").item(i)); i++) {
            node.value="";
         }
        document.getElementById("filtre_valid").selectedIndex=1;
        f.submit();
       }
}

function supprimerItem (id) {
    if (confirm("$str_confirm_supp"  +id+ " $str_confirm_supp1"))
        doAction("supprimer",id);
}

function filtrerItem (id) {
        doAction("filtrer",id);
}

function invaliderItem (id) {
    if (confirm("$str_confirm_inval" +id+" $str_confirm_inval1"))
        doAction("invalider",id);
}

function consulterItem (id) {
   doPopup('fiche.php?id='+id);
}

function dupliquerItem (id) {
    doPopup('ajout.php?dup_id='+id);
}

function validerItem (id) {
     doPopup('valider.php?id='+id);
}

function commenterItem (id) {
     doPopup('commentaires.php?id='+id);
}


function commenterEmailItem (id) {
     doPopup('commenter_email.php?id='+id);
}

function modifierItem (id) {
     doPopup('ajout.php?id='+id);
}


function modifierItem2 (id) {
    doPopup('ajoutv2.php?id='+id);
}


function nouvelItem () {
    doPopup('ajout.php?id=-1');
}

//]]>
</script>

EOC;

echo $code;
?>
