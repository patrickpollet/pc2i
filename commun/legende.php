<?php

/**
 *  @author Patrick Pollet
 * @version $Id: legende.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * envoie dans un popup standard un document situé dans ../templates2/

*
* rev 1..5 voir get_menu_legende dans commun/weblib.php
*
* les fichiers doivent se nommer xxxx.html et n'avoir que l "corps", en principe une table width=100%
*
*
*
*/
$chemin = '..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");


$quoi=optional_param("quoi","filenotfound",PARAM_CLEAN);
$ou=optional_param("ou","legende",PARAM_ALPHA);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(  );    //créer une instance

// rev 986 accepte des URL exterieurs 
if (is_url($quoi)) {
    $html=<<<EOH
        <iframe src="$quoi" name="ext" height="400" width="800" frameborder="0">
        </iframe> 
EOH;
    
    $options=array (
        "corps_byvar"=>$html
    );  
    $tpl->prepare($chemin,$options);
} else {

//inclure d'autre block de templates
    $page=$chemin."/templates2/".$ou."s/".$quoi.".html";
	if (file_exists($page)) {
        $tpl->assignInclude("corps",$page);
        $tpl->prepare($chemin);
	} else {
        $tpl->assignInclude("corps",$chemin."/templates/retour.html");
        $tpl->prepare($chemin);
        $tpl->assign("resultat",traduction("err_page_aide_non_trouve",0,$page));
    }
}

    $tpl->print_boutons_fermeture();


    $tpl->traduit("_ROOT.titre_popup",$ou);
    $tpl->printToScreen();										//affichage
?>