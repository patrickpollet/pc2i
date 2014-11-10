<?php

/**
 *  @author Patrick Pollet
 * @version $Id: ajout.php 1252 2011-05-23 10:20:26Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
*/

////////////////////////////////
//
//	Ajout et modification d'item
//	et duplication (ajout par recopie)
//
////////////////////////////////


$fiche=<<<EOF


<form class="normale" action="action.php" method="post" name="monform" id="monform" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="75000000000" />
<!-- START BLOCK : url_retour -->
<input type="hidden" name="url_retour" value="{url_retour}" />
<!-- END BLOCK : url_retour -->
  <table class="fiche">
        <tbody>
          <tr>
            <th>{form_libelle}</th>
            <td><textarea name="titre" cols="80" rows="5" class="saisie required"
               title="{js_libelle_manquant}">{titre}</textarea></td>
          </tr>

        <tr>
            <th>{form_ref_c2i}</th>
            <td>  {select_referentielc2i} </td>
        </tr>
        <tr>
            <th>{form_alinea}</th>
            <td>  {select_alinea} </td>
        </tr>

          <tr>
            <th>
              {form_lien}
              <br/>
<!-- START BLOCK : doc_url -->
            <a href="#"  onclick="openPopup('{url}','','{lp}','{hp}}')">{lien}</a>
<!-- END BLOCK : doc_url -->

            </th>
            <td>
            <input type="text" size="80" name="url" id="url" value="{url}" class="required saisie validate-url" title="{js_valeur_url_incorrecte}"
            </td>
          </tr>



<tr>
<th>{form_tags}<br/>
<div class="commentaire1">{info_tags}</div></th>

 <td><textarea name="tags" cols="60" rows="5"
                   >{tags}</textarea></td>
</tr>
        </tbody>
  </table>
 <div class="centre">
      {bouton_annuler} &nbsp; {bouton_reset} &nbsp;{bouton_enregistrer}

<input name="id" type="hidden" value="{id}" />
<input name="id_etab" type="hidden" value="{id_etab}" />
<input name="dupliquer" type="hidden" value="{dupliquer}" />
<input type="hidden" name="url_retour" value="{url_retour}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
</div>


</form>

EOF;

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

 if (!$CFG->utiliser_notions_parcours)
    erreur_fatale("err_pas_de_notions_parcours_ici");

require_login('P'); //PP

$id=optional_param("id",-1,PARAM_INT);
$dup_id=optional_param("dup_id",0,PARAM_INT);
$url_retour=optional_param("url_retour","",PARAM_CLEAN);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$tpl->assignInclude("corps", $fiche,T_BYVAR); // le template g�rant la liste des questions
$tpl->prepare($chemin);


$tpl->assign("_ROOT.id", $id);
$tpl->assign("_ROOT.url_retour",$url_retour);

$CFG->utiliser_validation_js=1;


//////////////////////////////
// gestion de la duplication
$dupliquer = 0;

if ($dup_id) {
    v_d_o_d("qd");
    $id = copie_ressource($dup_id);
    $dupliquer=1;
}

if ($id !=-1) { // modification de la ressource
	if ($dupliquer == 0)
		v_d_o_d("qm");
	if ($dupliquer == 0) { // pas de duplication
		$tpl->assign("_ROOT.titre_popup", traduction("modifier_ressource") . " " . $id);
	} else { // duplication
		$tpl->assign("_ROOT.titre_popup", traduction("dupliquer_ressource") . " " . $dup_id." => ".$id);
	}
	$ligne=get_ressource($id);
	$lien=get_lien($ligne);


} else {
	v_d_o_d("qa");
	$tpl->assign("_ROOT.titre_popup", traduction("nouvelle_ressource"));
	$ligne=new StdClass();
	$ligne->titre=$ligne->domaine=$ligne->competence=$ligne->modifiable="";
	$ligne->id_etab=$USER->id_etab_perso;
	$nbliens=0;
	$ligne->tags=$ligne->version='';
	$lien=new StdClass();
	$lien->URL='';
	
}




$tpl->gotoBlock("_ROOT");
$tpl->assign("id",$id);
$tpl->assignObjet($ligne);
$tpl->assign("dupliquer",$dupliquer);

$tpl->assign('url',$lien->URL);
if ($lien->URL) {
    $tpl->newBlock('doc_url');
    $tpl->assign('url',$lien->URL);
} 

$tpl->gotoBlock("_ROOT"); // V 1.41 important on �tait dans le block id_session !

// g�n�ration des listes d�roulantes
$attrs_ref= "style='width:450px;'   title=\"".traduction("js_referentiel_manquant")."\"";
$attrs_alinea="style='width:450px;' title=\"".traduction("js_alinea_manquant")."\"";

// rev 977 valeurs par d�faut

    $ref=$ligne->domaine;
    $al=$ligne->competence;


print_selecteur_ref_alinea_famille($tpl,"monform",
                    "select_referentielc2i",'required validate-selection', $attrs_ref, //select referentiel
                    "select_alinea",'required validate-selection',$attrs_alinea,       //select alinea
                    false,false,false,                                                 //select famille
                    false,false,false,                                                 //input famille
                    $ref,$al,false,false);                //valeurs actuelles


$tpl->gotoBlock("_ROOT");
if ($dupliquer)
    print_bouton_annuler_duplication($tpl);
else
    print_bouton_annuler($tpl);
if ($id=="-1")
    print_bouton_reset($tpl,"validator.reset();");
else
    $tpl->assign("bouton_reset","");
print_bouton_enregistrer($tpl);

if ($url_retour) {
    $tpl->newBlock("url_retour");
    $tpl->assign("url_retour", $url_retour);
}

$tpl->printToScreen(); //affichage
?>