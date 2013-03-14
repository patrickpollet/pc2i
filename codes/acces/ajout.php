<?php
/**
 * @author Patrick Pollet
 * @version $Id: ajout.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Ajout et modification d'item
//	et duplication (ajout par recopie)
//
////////////////////////////////
/*
* Pour la description des différentes méthodes de la classe TemplatePower,
* il faut se reférer à http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin représente le path(chemin) de script dans le site (à la racine)
//******** ---------------- $chemin_commun représente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images représente le path des images
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres

require_login('P');
v_d_o_d("config");

$idq=optional_param("idq","-1",PARAM_INT); // nouveau ou modif

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //créer une instance
//inclure d'autre block de templates

$rangees=profil_en_inputs();

$fiche=<<<EOF


<form name="monform" id="monform" action="action.php" method="post">
<div class="centre">
{bouton_tout_cocher}   {bouton_tout_decocher}
</div>
<table class="fiche" width="90%">
        <tbody>
          <tr>
            <th>{form_libelle}</th>
            <td>
            	<input  type="text" name="intitule" class="required"
					title="{js_libelle_manquant}" size="40" value="{intitule}"/>
            </td>
          </tr>
        $rangees
        </tbody>
      </table>

<div class="centre">
    {bouton_annuler} &nbsp; {bouton_reset} &nbsp;{bouton_enregistrer}

<input name="id" type="hidden" value="{id}"/>
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
</div>
</form>
EOF;





$CFG->utiliser_validation_js=1;

$tpl->assignInclude("corps", $fiche,T_BYVAR); // le template gérant un profil

$tpl->prepare($chemin);

$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.id", $idq);

if ($idq !=-1) { // modification

	$ligne=get_profil($idq);
	$tpl->assign("_ROOT.titre_popup", traduction("modifier_profil") . "<br/> " .  $ligne->intitule);

} else {
    $tpl->assign("_ROOT.titre_popup", traduction("nouveau_profil"));

    $ligne = new profil();

}
$tpl->assign("intitule", str_replace('"', "&quot;", $ligne->intitule));


foreach (get_object_vars($ligne) as $cle=>$valeur ) {
    if (is_numeric($valeur))
        $tpl->setChecked($valeur==1, "ch_".$cle);
}


print_bouton_annuler($tpl);
print_bouton_tout_cocher($tpl,"monform");
print_bouton_tout_decocher($tpl,"monform");

if ($idq=="-1")
    print_bouton_reset($tpl);
else
    $tpl->assign("bouton_reset","");
print_bouton_enregistrer($tpl);


$tpl->printToScreen(); //affichage
?>