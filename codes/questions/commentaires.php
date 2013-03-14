<?php

/**
 * @author Patrick Pollet
 * @version $Id: commentaires.php 1230 2011-03-22 18:38:58Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
// voir les commentaires sur une question
//
////////////////////////////////

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres

//rev 981 simplification avec le parametre id complet
if ($id=optional_param('id','',PARAM_CLE_C2I)) {
    $ligne=get_question_byidnat ($id);
    $idq=$ligne->id;
    $ide=$ligne->id_etab;
} else {
    $idq = required_param("idq", PARAM_INT);
    $ide = required_param("ide", PARAM_INT);
    $ligne=get_question($idq,$ide);
}

require_login("P"); //PP
v_d_o_d("ql");

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //créer une instance
//inclure d'autre block de templates

$fiche=<<<EOF
{ici}
EOF;

$tpl->assignInclude("corps",$fiche,T_BYVAR);


$tpl->prepare($chemin);


$link=  "<ul style=\"display:inline;\">".print_menu_item(false, false,get_menu_item_consulter("fiche.php?idq=$idq&amp;ide=$ide" ))."</ul>";

$tpl->assign("_ROOT.titre_popup", traduction( "validation_question")." ".$ide.".".$idq."<br/>".clean($ligne->titre,75)." ".$link);

$tpl->assign("ici",print_commentaires($idq,$ide));

$tpl->print_boutons_fermeture();



$tpl->printToScreen(); //affichage
?>