<?php

/**
 * @author Patrick Pollet
 * @version $Id: ajout.php 1219 2011-03-15 10:45:33Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Ajout et modification d'item
//	et duplication (ajout par recopie)
//
////////////////////////////////

//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

require_login('P'); //PP


$idq=optional_param("idq","-1",PARAM_INT);
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT);  // id du p�re

$url_retour=optional_param("url_retour","",PARAM_LOCALURL);

v_d_o_d("config");

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates


$fiche=<<<EOF



<form id="monform" action="action.php" method="post">
<!-- START BLOCK : infos -->
<div class="commentaire1">
{msg_parametrage_composante}
</div>
<!-- END BLOCK : infos -->
<table class="fiche">
        <tbody>
          <tr>
            <th>{form_libelle}</th>
            <td><input type="text" name="nom_etab" size="50" class="required"
					title="{js_libelle_manquant}"
             value="{nom_etab}" /></td>
          </tr>
          <tr>
            <th>{form_type_p}</th>
            <td>
               <input type="checkbox" value="1" name="positionnement" {ch_p} />{plp}
               &nbsp;
               <input type="checkbox" value="1" name="certification" {ch_c} />{plc}
             </td>
          </tr>
<!-- START BLOCK : OUT -->
          <tr>
            <th>{l}</th>
            <td><input type="checkbox" value="1" name="loc" {ch_l} /></td>
          </tr>

          <tr>
            <th>{n}</th>
            <td><input type="checkbox" value="1" name="nat" {ch_n} /></td>
          </tr>
<!-- END BLOCK : OUT -->
<!--  rev 928 vir�
          <tr>
            <th>{form_nbqar}</th>
            <td><input type="text" name="nb_quest_recup" size="5" class="required validate-digits"
				title= "{js_valeur_numerique_attendue}" value="{nb_quest_recup}" /></td>
          </tr>
 -->
          <tr>
            <th>{form_nb_aleatoire}</th>
            <td><input type="text" name="param_nb_aleatoire" size="5" class="required validate-digits"
				title= "{js_valeur_numerique_attendue}" value="{param_nb_aleatoire}" /></td>
          </tr>

            <tr>
            <th>{form_nb_items}</th>
            <td><input type="text" name="param_nb_items" size="5" class="required validate-digits"
				title= "{js_valeur_numerique_attendue}"  value="{param_nb_items}" /></td>
          </tr>

        </tbody>
      </table>

<div class="centre">
    {bouton_annuler} &nbsp; {bouton_reset} &nbsp;{bouton_enregistrer}

<input name="id" type="hidden" value="{id}" />
<input name="ide" type="hidden" value="{ide}" />
<input type="hidden" name="url_retour" value="{url_retour}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
</div>

</form>


EOF;


$CFG->utiliser_validation_js=1;

$tpl->assignInclude("corps",$fiche,T_BYVAR); // le template g�rant un �tablissement

$tpl->prepare($chemin);

$tpl->assign("id", $idq);  //nouvel ou modif
$tpl->assign("ide", $ide);  // id de l'�tablissement pere'

if ($idq!=-1) { // modification de la question
	$ligne=get_etablissement($idq);
    if ($ligne->pere==1)
        $tpl->assign("titre_popup", traduction("modifier_etablissement")." $idq <br/>".$ligne->nom_etab);
    else
        $tpl->assign("titre_popup", traduction("modifier_composante")." $idq <br/>".$ligne->nom_etab);
} else {
	if ($USER->id_etab_perso==1)
    	$tpl->assign("titre_popup", traduction("nouvel_etablissement"));
    else {
        $tpl->assign("titre_popup", traduction("nouvelle_composante"));
        $tpl->newBlock ('infos');
    }
	$ligne=get_etablissement($ide); // par d�faut on prend les infos du p�re
	$ligne->nom_etab="";
}

$tpl->gotoBlock("_ROOT");
// tres important pour retour a la liste
$tpl->assign("url_retour", $url_retour);

$tpl->assignObjet($ligne);
$tpl->setChecked($ligne->positionnement == 1,"ch_p");
$tpl->setChecked($ligne->certification == 1,"ch_c");



print_bouton_annuler($tpl);
if ($idq=="-1")
    print_bouton_reset($tpl,"validator.reset();");
else
    $tpl->assign("bouton_reset","");
print_bouton_enregistrer($tpl);
$tpl->printToScreen(); //affichage

?>