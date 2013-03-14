<?php

//chaines communes a inclure dans les javascript
$str_confirm_supp_profil=addSlashes(traduction("js_profil_supprimer_0")) ;
$str_confirm_supp_profil1=addSlashes(traduction("js_profil_supprimer_1")) ;

$str_confirm_supp_etab=addSlashes(traduction("js_etablissement_supprimer_0")) ;
$str_confirm_supp_etab1=addSlashes(traduction("js_etablissement_supprimer_1")) ;

$str_confirm_supp_comp=addSlashes(traduction("js_composante_supprimer_0")) ;
$str_confirm_supp_comp1=addSlashes(traduction("js_composante_supprimer_1")) ;



//	"js_examen_fils_supprimer_0" => "attention ! cet examen %s est membre d un pool. Vous allez donc réduire le nombre d examens du pool %s",

//	"js_pool_supprimer_0" => "attention ! vous êtes sur le point de supprimer le pool d examens numero :",
//	"js_pool_supprimer_1" => ". Ceci supprimera automatiquement les %s membres de ce pool ",

$code=<<<EOC

<script type="text/javascript">
//<![CDATA[

 



function consulterProfil (id) {
   doPopup('fiche_profil.php?idq='+id);
}
 
function supprimerProfil (id,titre) {
    if (confirm("$str_confirm_supp_profil"  +titre+ " $str_confirm_supp_profil1"))
        doAction("supprimer_profil",id);
}

function modifierProfil (id) {
     doPopup('ajout.php?idq='+id);
}


function nouveauProfil () {
    doPopup('ajout.php?idq=-1');
}



function consulterEtab (id) {
   doPopup('etablissement/fiche.php?idq='+id);
}
function nouvelEtab () {
    doPopup('etablissement/ajout.php?idq=-1');
}


function modifierEtab (id) {
    doPopup('etablissement/ajout.php?idq='+id);
}

function supprimerEtab (id,titre) {
    if (confirm("$str_confirm_supp_etab"  +titre+ " $str_confirm_supp_etab1"))
        doAction("supprimer_etab",id);
}

function supprimerComp (id,titre) {
    if (confirm("$str_confirm_supp_comp"  +titre+ " $str_confirm_supp_comp1"))
        doAction("supprimer_etab",id);
}


function listePersonnels (id) {
    doPopup('personnel/liste.php?idq='+id);
}

function listeCandidats (id) {
    doPopup('etudiant/liste.php?idq='+id);
}


//]]>
</script>

EOC;

echo $code;
?>
