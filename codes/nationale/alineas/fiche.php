<?php


/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//  Fiche d'item
//
////////////////////////////////
$fiche =<<<EOF
<table class="fiche" width="90%">
        <tbody>
          <tr>
            <th width='30%'>{form_id}</th>
            <td>{alinea}</td>
        </tr>
          <tr>
            <th>{form_ref_c2i}</th>
            <td>{referentielc2i}</td>
        </tr>
          <tr>
            <th>{form_aptitude} </th>
            <td >{aptitude}</td>
          </tr>
          <tr>
            <th>
               {form_nbquestions_associees} </th>
            <td >{nbquestions}</td>
          </tr>

<!-- START BLOCK : questions -->
   <tr>
           <th>
               {form_questions_associees} </th>
               <td>
                <table class="sansbordure">
               <!-- START BLOCK : question -->
                   <tr>
                   <td>{fonc} </td><td> <ul style="display:inline;">{consulter_fiche} </ul></td>
                   </tr>
                <!-- END BLOCK : question -->
                </table>
               </td>
          </tr>
<!-- END BLOCK : questions -->


        </tbody>
</table>


EOF;

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_once("../lib_nationale.php");

$id=required_param("id",PARAM_CLEAN);

require_login("P"); //PP


$ligne=get_alinea_byid($id);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $fiche,T_BYVAR);
$tpl->prepare($chemin);


$tpl->assign("_ROOT.titre_popup",traduction("fiche_alinea") . " " . $ligne->referentielc2i.'.'.$ligne->alinea);


$tpl->gotoBlock("_ROOT");

$tpl->assignObjet($ligne);


if ($liste =get_questions_par_alinea($ligne->referentielc2i,$ligne->alinea,true)) {  //seulement les valid�es)
    $tpl->newBlock("questions");
    foreach ($liste as $q) {
        $tpl->newBlock ("question");
        $tpl->assign("fonc", $q->id_etab.".".$q->id. ' '.clean($q->titre,70));
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../questions/fiche.php?idq=".$q->id."&amp;ide=".$q->id_etab));

    }
}

$tpl->gotoBlock("_ROOT");
$tpl->assign('nbquestions',count($liste));





$tpl->print_boutons_fermeture();

$tpl->printToScreen(); //affichage
?>



