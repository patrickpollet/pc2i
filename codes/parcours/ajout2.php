<?php

/**
 * @author Patrick Pollet
 * @version $Id: ajout.php 621 2009-04-02 17:31:40Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Ajout et modification d'item
//	et duplication (ajout par recopie)
//
////////////////////////////////


//inclure d'autre block de templates

$fiche=<<<EOF

    <form action="action2.php" class="normale" method="post" name="monform" id="monform">
<p class="double">
    <label for="titre">{form_libelle}</label>
    <textarea name="titre" cols="70" rows="2" class="required"
                    title="{js_libelle_manquant}">{titre}</textarea>
</p>
<!-- START BLOCK : tags -->
<p class="double">
    <label for="tags">{form_tags}</label>
    <textarea name="tags" cols="70" rows="2" 
                    title="{info_tags}">{tags}</textarea>
</p>
<!-- END BLOCK : tags -->

<p class="simple">
{bouton_tout_cocher}   {bouton_tout_decocher}
</p>
 <div id="menuLayer" class="gauche">
{ici}
</div>

<div class="centre">
{bouton_annuler} &nbsp;{bouton_reset} &nbsp; {bouton:enregistrer}

<input name="id" type="hidden" value="{id}"/>
<input name="dupliquer" type="hidden" value="{dupliquer}"/>
<input type="hidden" name="url_retour" value="{url_retour}"/>
<input type="hidden" name="login" value="{login}"/>
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
</div>

</form>

EOF;


$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_once($CFG->chemin_commun."/pear/HTML_TreeMenu/TreeMenu.php");


 if (!$CFG->utiliser_notions_parcours)
    erreur_fatale("err_pas_de_notions_parcours_ici");

require_login("E"); //PP

$id=optional_param("id",-1,PARAM_INT);
$dup_id=optional_param("dup_id",0,PARAM_INT);
$url_retour=optional_param("url_retour","",PARAM_CLEAN);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance

$tpl->assignInclude("corps", $fiche,T_BYVAR); // le template g�rant la liste des questions

$tpl->prepare($chemin);



//////////////////////////////
// V 1.5 faites ici desormais
//gestion de la duplication

$dupliquer = 0;

if ($dup_id) {
	$id=copie_parcours($dup_id);
    $parc=get_parcours($id);
	$tpl->assign("_ROOT.titre_popup", traduction("dupliquer_parcours") . "<br/>".$parc->titre);
    $dupliquer=1;
}
else
	if ($id != -1) {
        $parc=get_parcours($id);
		$tpl->assign("_ROOT.titre_popup", traduction("modifier_parcours") . "<br/>".$parc->titre);
    }
	else {
        $parc=new StdClass();
        $parc->titre=traduction ("nouveau_parcours");
        $parc->login=$USER->id_user; 
		$tpl->assign("_ROOT.titre_popup", traduction("nouveau_parcours"));
    }

///////////////////////////////
$tpl->assign("_ROOT.id", $id); // important !
$tpl->assign ("_ROOT.titre",$parc->titre);
$tpl->assign ("_ROOT.login",$parc->login);
$tpl->assign("_ROOT.url_retour",$url_retour);
$tpl->assign("dupliquer",$dupliquer);

add_javascript($tpl,$CFG->chemin_commun."/pear//HTML_TreeMenu/TreeMenu.js");

$menu= nouveau_parcours_en_menu ($id);
$treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $CFG->chemin_theme . "/images/treemenu",
 'defaultClass' => 'treeMenuDefault'));

 $tpl->assign("ici",$treeMenu->toHTML());

if ($CFG->activer_tags_parcours) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$parc->tags);
}


$tpl->gotoBlock("_ROOT");
print_bouton_tout_cocher($tpl,"monform");
print_bouton_tout_decocher($tpl,"monform");

if ($dupliquer)
    print_bouton_annuler_duplication($tpl);
else
    print_bouton_annuler($tpl);

if ($id=="-1")
    print_bouton_reset($tpl,"validator.reset();");
else
    $tpl->assign("bouton_reset","");

$tpl->printToScreen(); //affichage
?>
