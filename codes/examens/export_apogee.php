<?php


/**
 * @author Patrick
 * @version $Id: export_apogee.php 985 2009-12-04 17:44:58Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */



$chemin = '../..';

require_once ($chemin. "/commun/c2i_params.php"); //fichier de paramètres
require_once($CFG->chemin_commun."/lib_import_export.php");

require_login("P"); //PP



$idq = required_param("idq", PARAM_INT, "");
$ide = required_param("ide", PARAM_INT, "");
$fichier_apogee = optional_param("fichier_apogee","", PARAM_PATH);
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);

v_d_o_d("em"); //PP

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(); //créer une instance
//inclure d'autre block de templates


$fiche=<<<EOF
<!-- START BLOCK : infos -->
{resultats_op}
<a href="{url_telecharger}">{telecharger_fichier_apogee}</a>
<br/>
<!-- END BLOCK : infos -->


<form action="export_apogee.php" method="post" name="monform">

<input name="idq" type="hidden" value="{idq}">
<input name="ide" type="hidden" value="{ide}">
<input type="hidden" name="retour_fiche" value="{retour_fiche}" >
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}">
<!-- END BLOCK : id_session -->

<fieldset>
<legend>{resultats_apogee} </legend>
<table width="99%">
<tr><td colspan="2" class="commentaire1">{info_export_apogee}</td></tr>
<tr>
<td>
{select_fichier}
</td>
<td>
<input name="exporter" id="add" type="submit" class="saisie_bouton"
value="{exporter}" title="{exporter}" /><br />
</td>
</tr>

</fieldset>
</table>
</form>

EOF;

$tpl->assignInclude("corps", $fiche, T_BYVAR);
$tpl->prepare($chemin);

$ligne = get_examen($idq, $ide);

$tpl->assign("_ROOT.titre_popup",traduction("resultats_apogee")."<br/>". nom_complet_examen($ligne));
$tpl->assign("ide", $ide);
$tpl->assign("idq", $idq);
$tpl->assign("retour_fiche", $retour_fiche);


//gi go ou gi go pas ?
if ($fichier_apogee) {

    $in=$CFG->chemin_ressources."/apogee/".$fichier_apogee;
    $out=$CFG->marqueur_export_apogee.$ide."_".$idq."_".$fichier_apogee.".txt";
    $resultats=ecriture_apogee ($idq,$ide,$in,$CFG->chemin_ressources."/apogee/".$out);

    if (count($resultats)) {
       $tpl->newBlock("infos");
       $tpl->assign("resultats_op",print_details($resultats));
       $tpl->assignURL("url_telecharger",$CFG->chemin_commun."/send_csv.php?idf=".$out."&dir=apogee");


    }

}



$tpl->gotoBlock("_ROOT");

$dispos=get_list_of_files($CFG->marqueur_export_apogee,$CFG->chemin_ressources."/apogee");
$table=array();
foreach ($dispos as $nom)
    $table[]=new option_select($nom,$nom);

print_select_from_table($tpl,"select_fichier",$table,"fichier_apogee","",
                            "","id","texte",traduction ("selectionner"),$fichier_apogee);



$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?ide=" . $ide . "&idq=" . $idq."#resultats": "");
$tpl->print_boutons_fermeture($url_retour);


$tpl->printToScreen(); //affichage


?>
