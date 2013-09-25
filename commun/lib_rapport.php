<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_rapport.php 598 2009-03-26 13:00:49Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


/**
 * gestion des rapports d'op�rations
 * usage
 * voir


 **/


function set_erreur ($msg,&$resultats) {
    $resultats[1][]=$msg;
}

function set_ok ($msg,&$resultats) {
    $resultats[0][]=$msg;
}


function table_en_texte($table) {
  if (count($table))
    return implode("\r\n",$table);
  else return "";
}

/**
 * affiche les r�sulats d'une op�ration
 * @param $resultats : tableau de chaine. [0] les Ok, [1] les echecs
 * TODO utiliser des div
 */

function print_details ($resultats,$rows=6){
    global $CFG;

    $details=<<<EOF
<fieldset>
<legend>{resultats}</legend>
<div class="information">
<!-- START BLOCK : oks -->
<label for "oks">{succes}</label> :
<textarea id="oks"  rows="$rows" cols="80" class="saisie" readonly>
{details}
</textarea>
<!-- END BLOCK : oks -->
<br/>
<!-- START BLOCK : kos -->
<label for "kos">{echecs}</label> :
<textarea id="kos"  rows="$rows" cols="80" class="saisie" readonly>
{details}
</textarea>
<!-- END BLOCK : kos -->
</div>
</fieldset>

EOF;

$tpl= new SubTemplatePower($details,T_BYVAR);    //cr�er une instance
// a le meme chemin que le template porteur
$tpl->prepare($CFG->chemin);

//$tpl->assign("bouton_details",get_bouton_action( "details",""));
if (@count($resultats[0])==0) {
    $tpl->traduit("resultats","msg_operation_echouee");
    $tpl->newBlock("kos");
    $tpl->assign("details",table_en_texte($resultats[1]));
}
else if (@count($resultats[1])) {
    $tpl->traduit("resultats","msg_operation_partie_reussie");
    $tpl->newBlock("oks");
    $tpl->assign("details",table_en_texte($resultats[0]));
    $tpl->newBlock("kos");
    $tpl->assign("details",table_en_texte($resultats[1]));

}  else {
    $tpl->traduit("resultats","msg_operation_reussie");
    $tpl->newBlock("oks");
    $tpl->assign("details",table_en_texte($resultats[0]));
}
 /*
  print_object("ok",$resultats[0]);
  print_object("pb",$resultats[1]);
 */
 return $tpl->getOutputContent();
}
