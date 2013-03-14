<?php
/**
 * @author Patrick Pollet
 * @version $Id: import_optique.php 1052 2010-03-10 12:39:28Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";

require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres
require_once($chemin_commun."/lib_ldap.php");
require_once($chemin_commun."/lib_import_export.php");



require_login("P"); //PP


require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates


$idq=required_param("idq",PARAM_INT);   // -1 en création
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //étab de l'examen, défaut = ici '
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);
$liste=optional_param("liste","",PARAM_RAW);
//surtout pas ne vas pas encore dans $_FILES
//$fichier_upl=optional_param("fichier_upl","",PARAM_RAW);

$format=optional_param("format","",PARAM_ALPHANUM);
v_d_o_d("em");

$tpl = new C2IPopup();	//créer une instance
//inclure d'autre block de templates

$forme=<<<EOL

{resultats_op}

<form action="import_optique.php" method="post" name="monform"  enctype="multipart/form-data">

<input name="idq" type="hidden" value="{idq}" />
<input name="ide" type="hidden" value="{ide}" />
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

<fieldset>
<legend>{import_lecteurs_optiques} </legend>
<table width="99%">
<tr><td colspan="3" class="commentaire1">{info_import_lecteurs_optiques} </td></tr>

<tr>
<th>{format_optique}</th>
<td>
{select_format}
</td>
</tr>
<tr>
<th>{form_fichier}</th>
  <td><input type="file" class="saisie" name="fichier_upl" size="68" /></td>
<td>
 <input name="add_file" id="add_file" type="submit" class="saisie_bouton"  value="{bouton_importer}" title="{bouton_importer}" />
</td>
</tr>

</table>
</fieldset>
</form>

EOL;


$tpl->assignInclude("corps",$forme,T_BYVAR);
$tpl->prepare($chemin);

$ligne=get_examen($idq,$ide);
$tpl->assign("_ROOT.titre_popup",traduction("import_lecteurs_optiques")."<br/>". nom_complet_examen($ligne));


//gi go ou gi go pas ?
if (isset($_FILES["fichier_upl"]) && !empty ($_FILES["fichier_upl"]["name"])){ //important le 2eme test
	//print_r($_FILES);
	//garder ce fichier soit dans csv soit dans apogee
	$dir=$CFG->chemin_ressources."/tmp";
	//récupere le en verifiant taille et type mime ...
	$fichier_garde=upload_file('fichier_upl',$dir, $CFG->max_taille_fichiers_uploades,array());
	//, array('text/plain')); pb avec les mac qui n'envoient pas ce type mime

	if (!$fichier_garde)
		erreur_fatale("err_upload_fichier",$_FILES["fichier_upl"]["tmp_name"]);
	else {
		switch ($format) {

			case "qcmdirect": $resultats=resultats_lecture_optique_QCMdirect($idq, $ide, $fichier_garde); break;
			case "icr" :break; // non implementé
            case "amc" :  $resultats=resultats_lecture_optique_AMC($idq, $ide, $fichier_garde); break;
		}
	}
	if (count($resultats))
		$tpl->assign("resultats_op",print_details($resultats));
	else $tpl->assign("resultats_op","");
}
else $tpl->assign("resultats_op","");


$tpl->assign("ide", $ide);
$tpl->assign("idq", $idq);
$tpl->assign("retour_fiche", $retour_fiche);


print_select_from_table($tpl,"select_format",get_import_resultats_methodes (),"format",false,false,"id","texte","",$format);


$tpl->gotoBlock("_ROOT");

$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?refresh=1&ide=" . $ide . "&idq=" . $idq."#optique" : "");
$tpl->print_boutons_fermeture($url_retour);


$tpl->printToScreen();										//affichage
?>

