<?php


/**
 * @author Patrick Pollet
 * @version $Id: configuration.php 964 2009-11-05 18:13:54Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * rev 1.51 retour a la config LDAP de base dans c2ietablissement
 * les parametres envoy�s a config_m doivent �tre exactement les noms des colonnes de la BD
 */

////////////////////////////////
//
//	Page de la configuration
//
////////////////////////////////
/*----------------REVISIONS----------------------
v 1.1 : PP 17/10/2006
           ajout des trois attributs LDAP pour les membres d'un groupe et l'id unique
-----------------------------------------------*/

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

require_login('P'); //PP
v_d_o_d("config");


$page =<<<EOP
<div class="phpinfo">
{ici}
</div>
EOP;

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup();
//inclure d'autre block de templates
$tpl->assignInclude("corps", $page, T_BYVAR); // le template g�rant la configuration




$tpl->prepare($chemin);

   ob_start();
    phpinfo(INFO_GENERAL + INFO_CONFIGURATION + INFO_MODULES);
    $html = ob_get_contents();
    ob_end_clean();

/// Delete styles from output
    $html = preg_replace('#(\n?<style[^>]*?>.*?</style[^>]*?>)|(\n?<style[^>]*?/>)#is', '', $html);
    $html = preg_replace('#(\n?<head[^>]*?>.*?</head[^>]*?>)|(\n?<head[^>]*?/>)#is', '', $html);
/// Delete DOCTYPE from output
    $html = preg_replace('/<!DOCTYPE html PUBLIC.*?>/is', '', $html);
/// Delete body and html tags
    $html = preg_replace('/<html.*?>.*?<body.*?>/is', '', $html);
    $html = preg_replace('/<\/body><\/html>/is', '', $html);

 $tpl->assign ("ici",$html);
 $tpl->assign("_ROOT.titre_popup",traduction("info_php"));

$tpl->print_boutons_fermeture();
$tpl->printToScreen(); //affichage
?>
