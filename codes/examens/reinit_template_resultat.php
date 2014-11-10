<?php


$chemin = '../..';
require_once ($chemin. "/commun/c2i_params.php"); //fichier de param�tres

require_login("P"); //PP


$idq = required_param("idq", PARAM_INT, "");
$ide = required_param("ide", PARAM_INT, "");
$retour_fiche = optional_param("retour_fiche","0",PARAM_INT);
$supp = optional_param("supp", "", PARAM_ALPHANUM);

v_d_o_d("el"); //PP

$ligne=get_examen($idq,$ide);
$nomexamen= nom_complet_examen($ligne);
$template_resultat = $ligne->template_resultat;

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance


if ($supp == "O" ){
	set_template_examen ($idq, $ide, "");
	//tracking :
	espion2("reinitialisation_template", "examen", $ide.".".$idq);
	$template_resultat = "";
$corps=<<<EOL
<input type="hidden" name="retour_fiche" value="{retour_fiche}" >
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}">
<!-- END BLOCK : id_session -->
<div class='commentaire2'>{infos}</div><hr/>{reinit_template_resultats_complet}<br/>
EOL;
}
else{
$corps=<<<EOL
<form action="reinit_template_resultat.php" method="post" name="monform" id="monform">
<input name="idq" type="hidden" value="{idq}">
<input name="ide" type="hidden" value="{ide}">
<input name="supp" type="hidden" value="O">
<input type="hidden" name="retour_fiche" value="{retour_fiche}" >
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}">
<!-- END BLOCK : id_session -->
<div class='commentaire2'>{infos}</div><hr/>{recap_resultat}<br/>{template_question}<br/>{bouton_confirmer}
</form>
EOL;

}

$tpl->assignInclude("corps", $corps,T_BYVAR);
$tpl->prepare($chemin);
$tpl->gotoBlock("_ROOT");
$affiche_test = "<div class=\"test_resultat\">$template_resultat</div>";
$tpl->assign("_ROOT.recap_resultat",$affiche_test);
$tpl->assign("_ROOT.template_question", traduction("reinit_template_resultats_question"));
$tpl->assign("_ROOT.ide",$ide);
$tpl->assign("_ROOT.idq",$idq);
$tpl->assign("_ROOT.retour_fiche", $retour_fiche);
$tpl->assign("_ROOT.titre_popup",traduction("reinit_template_resultats")."<br/>".$nomexamen );
$tpl->assign("_ROOT.infos",traduction("reinit_template_resultats"));


$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?ide=$ide&idq=$idq#template_resultats" : "");
$tpl->print_boutons_fermeture($url_retour);

print_bouton_confirmer($tpl);

$tpl->printToScreen(); //affichage
?>
