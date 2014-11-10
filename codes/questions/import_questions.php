<?php

/**
 * @author Patrick Pollet Pierre Raynaud
 * @version $Id: import_questions.php 1281 2012-01-07 14:34:26Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	import de questions au format qcmdirect  avec referentiel
////////////////////////////////
// v 1.3 et v  1.4
//	Fonction d'import de QCM � partir d'un fichier
// Structure:
//		1. (competence,alin�a) intitul� premi�re question
//		A. intitul� premi�re r�ponse
//		B. intitul� deuxi�me r�ponse	V
//		C. intitul� troisi�me r�ponse
//	Pierre Raynaud
//	pierre.raynaud@u-clermont1.fr
//
////////////////////////////////
/**
 * v 1.5 Les deux scripts import_questions.php et import.php (l'action du formulaire on �t�
 * regroup� ici en un seul et l'affichage du succ�s de lop�ration a donc �t� am�lior�... )
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

require_login('P'); //PP
v_d_o_d("qa");

$go = optional_param("go",0, PARAM_INT);
$type_questP = optional_param("type_questP","",PARAM_ALPHA);
$type_questC = optional_param("type_questC","",PARAM_ALPHA);
$type_fichier = optional_param("type_fichier","QCM",PARAM_ALPHA);
$url_retour=optional_param("url_retour","",PARAM_CLEAN);

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IMiniPopup( );	//cr�er une instance
//inclure d'autre block de templates


//template de saisie du nom du fichier

$fiche_saisie=<<<EOF

<form name="form" method="post"
    enctype="multipart/form-data"   action="import_questions.php">
<table  >
    <tr>
        <td width="19" class="gauche"  ><img
            src="{chemin_images}/ii_config.gif"  alt="configuration"
            name="c4" width="19" height="19" id="et2" /></td>
        <td class="gauche"  >{etiquette}</td>
        <td class="gauche"  ><input type="file" class="saisie" name="fichier" /> <br />
    </td>
    </tr>

<tr>
<td colspan="2">{type_fichier_questionnaire_concerne}</td>
<td colspan="2">
<input type="radio" name="type_fichier" value="QCM" checked="checked" />{fichier_qcmdirect}{menu_niveau2}
&nbsp;<br/>
<input type="radio" name="type_fichier" value="XML"/> {fichier_xml}{menu_niveau3}

<!--
&nbsp;<br/>
<input type="radio" name="type_fichier" value="XML_MOODLE" /> {fichier_xml_moodle}{menu_niveau4}
-->
</td>
</tr>


<tr>
<td colspan="2">{questionnaire_concerne}</td>
<td colspan="2">
<input type="checkbox" name="type_questP" value="P"  /> {positionnement}
&nbsp;<br/>
<input type="checkbox" name="type_questC" value="C" /> {certification}
</td>
</tr>
</table>
<div class="centre">
{bouton:annuler} &nbsp; {bouton:ok}
 <!-- START BLOCK : id_session -->
  <input   name="{session_nom}" type="hidden" value="{session_id}"/>
  <!-- END BLOCK : id_session -->

<input name="go" type="hidden" value="1" />
<input type="hidden" name="url_retour" value="{url_retour}"/>
</div>
</form>
EOF;


//template d'affichage du r�sultat de l'op�ration
$fiche_reponse=<<<EOF
{resultats_op}
<div class="centre">
{bouton:fermer}
</div>

EOF;



if ($go ) {
	require_once($CFG->chemin_commun."/lib_import_export.php");
	$tpl->assignInclude("contenu",$fiche_reponse,T_BYVAR);
	$tpl->prepare($chemin);
	if (isset ($_FILES['fichier']) && !empty($_FILES['fichier']["name"])) {
			if ((isset($type_questP) &&  !empty($type_questP)) || (isset($type_questC) &&  !empty($type_questC))){
				$type_quest = $type_questP.$type_questC;
                switch ($type_fichier) {
                case "QCM" :
                     $result = from_qcmdirect($_FILES['fichier']["tmp_name"], $type_quest);
                     break;
                 case "XML":
                    $result = from_xml_pfc2i($_FILES['fichier']["tmp_name"], $type_quest);
                    break;
                 case "XML_MOODLE":
                    $result = from_xml_moodle($_FILES['fichier']["tmp_name"], $type_quest);
                    break;
                 default:
                    erreur_fatale ("err_format_import_incorrect",$type_fichier);
                    break;

                }
                $tpl->assign("resultats_op",print_details($result));
				rafraichi_liste("liste.php",$url_retour);
			}
			else erreur_fatale ("err_pas_de_type_de_plateforme");
	}
	else erreur_fatale ("err_pas_de_fichier");
}

else  {
	$tpl->assignInclude("contenu",$fiche_saisie,T_BYVAR);
	$tpl->prepare($chemin);
	print_menu($tpl,"_ROOT.menu_niveau2",array(get_menu_item_legende("import")));
	print_menu($tpl,"_ROOT.menu_niveau3",array(get_menu_item_legende("import_xml")));
    //print_menu($tpl,"_ROOT.menu_niveau4",array(get_menu_item_legende("import_xml_moodle")));
	$tpl->assign("_ROOT.etiquette" , traduction( "fichier au format d'import d�fini"));
	$tpl->assign("url_retour",$url_retour);
}

$tpl->assign("_ROOT.titre_popup" ,traduction("importer_questions"));
$tpl->assign("elt","");



$tpl->printToScreen();
?>
