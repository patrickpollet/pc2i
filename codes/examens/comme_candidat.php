<?php

/**
 * @author Patrick Pollet
 * @version $Id: comme_candidat.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	vue du qcm comme candidat
// nettoy� PP v 1.41 (utilise des minitemplates en variables !)
// sort enfin comme candidat ET dans un popup !!!
// l'export QCM direct qui avait �t� accroch� � a script a �t d�plac� dans exportQCMDirect.php'
////////////////////////////////

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun."/c2i_params.php"); //fichier de param�tres

require_login("P"); //PP


$idq = required_param("idq", PARAM_INT, "");
$ide = required_param("ide", PARAM_INT, "");
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);

$version_imprimable = optional_param('version_imprimable', 0, PARAM_INT);  //deux colonnes non implement�
$montre_ref=optional_param("montre_ref",0,PARAM_INT);
$melange_questions=optional_param("melange_questions",0,PARAM_INT);
$melange_reponses=optional_param("melange_reponses",0,PARAM_INT);
$mode=optional_param("mode",QCM_PREVISUALISATION,PARAM_INT);   // 	0  pr�visu  4 passer l'examan SANS enregistrer maie en le notant � '

v_d_o_d("el"); //PP

$ligne=get_examen($idq,$ide);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(); //cr�er une instance

$tpl->assignInclude("corps","<div class='commentaire2'>{infos}</div>{timer}<hr/>{ici}<br/>",T_BYVAR);

$tpl->prepare($chemin);

// rev 986
/// Check subnet access pas en mode pr�visualsation
/*
$ips=get_ips_liste_csv($ligne->subnet);
if (!empty($ips) && !address_in_subnet( $REMOTE_ADDR , $ips)) {
    erreur_fatale(  'erreur_ip_invalide', $REMOTE_ADDR );
}
*/

$tpl->gotoBlock("_ROOT");

switch ($mode) {
    case QCM_PREVISUALISATION: $info="previsualisation";$infos2=""; break;
    case QCM_NORMAL: $info="normal"; $infos2="";break;
    case QCM_CORRECTION: $info="correction_de";$infos2=""; break;
    case QCM_CORRIGE: $info="corrige_de"; $infos2="";$montre_ref=true;break;
    case QCM_TEST: $info="test_de"; $infos2="msg_info_pas_enregistrer_reponses";break;
    default: $info="normal"; break;
}


$tpl->assign ("titre_popup",traduction($info). " : ".nom_complet_examen($ligne));


if ($ligne->type_tirage== EXAMEN_TIRAGE_PASSAGE)
	$tpl->assign('_ROOT.infos',traduction("msg_info_tirage_passage"));
else $tpl->setConditionalValue($infos2,"_ROOT.infos",traduction($infos2),"");

$ret=imprime_examen($idq,$ide,$melange_questions,$melange_reponses,$version_imprimable,$montre_ref,$mode);
$tpl->assign("ici",$ret[0]);

// rev 958
if ($mode != QCM_CORRECTION && $mode !== QCM_CORRIGE) {

	add_javascript($tpl,$CFG->chemin_commun."/js/quiz.js");
	$tpl->assign ("timer",print_timer($ligne));
} else $tpl->assign ("timer","");

$tpl->assign("terminer", traduction("bouton_terminer"));
$tpl->gotoBlock("_ROOT");

$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?ide=" . $ide . "&amp;idq=" . $idq."#apercus": "");
$tpl->print_boutons_fermeture($url_retour);



$tpl->printToScreen(); //affichage
?>
