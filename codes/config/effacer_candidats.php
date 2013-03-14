<?php
////////////////////////////////
// purge des candidats qui ne sont plus inscrits à un examen
////////////////////////////////

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres
require_once($chemin_commun."/lib_cron.php");
require_login('P'); //PP
if (! is_admin())
    erreur_fatale("err_droits");

$ide = optional_param("ide",$USER->id_etab_perso, PARAM_INT);
$supp = optional_param("supp", "", PARAM_INT);

$nb= compte_candidats_non_inscrits($ide);

if ($supp) {
    purge_candidats_non_inscrits($ide);
    espion2("purge_candidats_non_inscrits", $nb, $ide);
    ferme_popup("",false); //pas besoin de rafraichir
}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //créer une instance

$corps=<<<EOL
<form action="effacer_candidats.php" method="post" name="monform" id="monform">
<input name="ide" type="hidden" value="{ide}" />
<input name="supp" type="hidden" value="1" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
<div class='information'>
{info_purger_candidats_non_inscrits}

<br/>{bouton_confirmer}
</div>
</form>
EOL;


$tpl->assignInclude("corps", $corps,T_BYVAR);
$tpl->prepare($chemin);
$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.ide",$ide);
$tpl->assign("_ROOT.titre_popup",traduction("purger_candidats"));
$tpl->assign("_ROOT.info_purger_candidats_non_inscrits",traduction("info_purger_candidats_non_inscrits",false,$nb,nom_univ($ide)));



$tpl->print_boutons_fermeture();

print_bouton_confirmer($tpl);

$tpl->printToScreen(); //affichage
?>
