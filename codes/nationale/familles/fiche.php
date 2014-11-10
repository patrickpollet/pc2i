<?php


/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 1252 2011-05-23 10:20:26Z ppollet $
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
            <th width='30%'>{form_id} </th>
            <td >{idf}</td>
          </tr>
        <tr>
            <th>{form_famille} </th>
            <td >{famille}</td>
          </tr>


          <tr>
            <th>{form_ref_c2i}</th>
            <td>{referentielc2i}</td>
        </tr>

          <tr>
            <th>{form_alinea}</th>
            <td>{alinea}</td>
        </tr>
 <!-- START BLOCK : ancien_domaine -->
         <tr>
            <th>
               {form_ancien_domaine} </th>
            <td >{ancien_domaine} : {ancien_domaine_texte} <br/>
                 {ancien_alinea} : {ancien_alinea_texte}
            </td>
          </tr>
 <!-- END BLOCK : ancien_domaine -->

        <tr>
            <th>{form_ordref} </th>
            <td >{ordref}</td>
          </tr>

          <tr>
            <th>{form_commentaires} </th>
            <td >{commentaires}</td>
          </tr>
<tr>
            <th>{form_motscles} </th>
            <td >{mots_clesf}</td>
          </tr>

               <tr>
            <th>
             {form_date_de_creation}</th>
            <td>{date_creation}</td>
          </tr>

           <tr>
            <th>
             {form_date_de_utilisation}</th>
            <td>{date_utilisation}</td>
          </tr>

         <tr>
            <th>
               {form_auteurs} {form_nom_coll} </th>
            <td >{auteur} <ul style="display:inline;"> {consulter_fiche} </ul></td>
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
                   <td>{fonc}</td><td><ul style="display:inline;"> {consulter_fiche} </ul> </td>
                   </tr>
                <!-- END BLOCK : question -->
                </table>
               </td>
          </tr>
<!-- END BLOCK : questions -->
  <!-- START BLOCK : tags -->
          <tr>
            <th>
               {form_tags} </th>
            <td >{tags}
            <br/>
              <span class="commentaire1">{info_tags}</span></td>
          </tr>
<!-- END BLOCK : tags -->
        </tbody>
</table>


EOF;

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

require_once("../lib_nationale.php");


$id=required_param("id",PARAM_CLEAN);

require_login("P"); //PP


$ligne=get_famille($id);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $fiche,T_BYVAR);
$tpl->prepare($chemin);


$tpl->assign("_ROOT.titre_popup",traduction("fiche_famille") . " " . $ligne->idf);


$tpl->gotoBlock("_ROOT");

$ligne->date_creation=userdate($ligne->ts_datecreation,'strftimedatetimeday');
$ligne->date_utilisation=format_time($ligne->ts_dateutilisation);





$tpl->assignObjet($ligne);


if ($ligne->auteur_mail && $ligne->auteur) {
if ($cpt=get_compte_byemail($ligne->auteur_mail))
    print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("{$CFG->chemin}/codes/acces/personnel/fiche.php?id=".$cpt->login));
else
   $tpl->assign("consulter_fiche","<li class=\"menu_niveau2_item\"></li>");  //W3C
}
else $tpl->assign("consulter_fiche","<li class=\"menu_niveau2_item\"></li>");


//retouche v977 nouveau referentiel
if( !$ref=get_question_referentiel($ligne)) {
    $ref=new StdClass();
    $ref->referentielc2i='???';
    $ref->domaine='???';
}

// rev 872 sur la nationale 36 questions invalid�es anciennes avaient un alineas � -1 !

if (!$al=get_question_alinea($ligne)) {
    $al=new StdClass();
    $al->alinea="???";
    $al->aptitude="???";
}

$tpl->assign("referentielc2i",  $ref->referentielc2i . " - " . clean($ref->domaine));
$tpl->assign("alinea",  $al->alinea . " - " . clean($al->aptitude));


if ($liste=get_questions_par_famille($id,true)) {  //seulement les valid�es)
    $tpl->newBlock("questions");
    foreach ($liste as $q) {
        $tpl->newBlock ("question");

         $tpl->assign("fonc", $q->id_etab.".".$q->id. ' '.clean($q->titre,70));
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../questions/fiche.php?idq=".$q->id."&amp;ide=".$q->id_etab));

    }
}

$tpl->gotoBlock("_ROOT");
$tpl->assign('nbquestions',count($liste));



if ($CFG->activer_tags_famille) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}



$tpl->print_boutons_fermeture();
$tpl->printToScreen(); //affichage
?>



