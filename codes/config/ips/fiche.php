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
            <td >{id}</td>
          </tr>
        <tr>
            <th>{form_nom} </th>
            <td >{nom}</td>
          </tr>


          <tr>
            <th>{form_adresses}</th>
            <td>{adresses}</td>
        </tr>
              <tr>
            <th>
             {form_date_de_creation}</th>
            <td>{date_creation}</td>
          </tr>

           <tr>
            <th>
             {form_date_de_modification}</th>
            <td>{date_modification}</td>
          </tr>

<!-- START BLOCK : examens -->
   <tr>
            <th>
               {form_utilisation} </th>
               <td>
               <ul>
               <!-- START BLOCK : examen -->
                   {fonc} &nbsp; {consulter_fiche} &nbsp;
                <!-- END BLOCK : examen -->
               </ul>
               </td>
          </tr>
<!-- END BLOCK : examens -->

        </tbody>
</table>


EOF;

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres

$id=required_param("id",PARAM_CLEAN);

require_login("P"); //PP


$ligne=get_plage($id);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //créer une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $fiche,T_BYVAR);
$tpl->prepare($chemin);


$tpl->assign("_ROOT.titre_popup",traduction("fiche_plage") . " " . $ligne->id);


$tpl->gotoBlock("_ROOT");

$ligne->date_creation=userdate($ligne->ts_datecreation,'strftimedatetimeday');
$ligne->date_modification=format_time($ligne->ts_datemodification);



$tpl->assignObjet($ligne);




$tpl->gotoBlock("_ROOT");
//examen l'utilisant
$liste=get_examens_utilisant_plage ($id);  //renvoie un tableau
$tpl->newBlock("examens");
$tpl->assign("form_utilisation",traduction("form_utilisation",false,count($liste)));
//print_r($liste);
foreach ($liste as $examenid) {
    $tpl->newBlock ("examen");
    $tpl->assign("fonc", $examenid);
    print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../examens/fiche.php?id=".$examenid));

}




$tpl->print_boutons_fermeture();
$tpl->printToScreen(); //affichage
?>



