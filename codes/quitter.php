<?php


/**
 * @author Patrick Pollet
 * @version $Id: quitter.php 1179 2010-12-11 12:25:54Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  *  deconnexion , destruction session, retour page d'origine
 */

if (!isset ($chemin))
    $chemin = "..";
require ($chemin . "/commun/c2i_params.php");


$err_c2i=optional_param("err_c2i",0,PARAM_ALPHA);
$id_examen=optional_param('id_examen',0,PARAM_INT);
$id_etab=optional_param('id_etab',0,PARAM_INT);

//gros pb si la session a expiré plus rien d'ou le test...
/**
print_r($_SESSION);
print_r($USER);
print $err_c2i;
die();
**/
if ($USER->type_plateforme) {
    $page_origine = $USER->type_plateforme . ".php";
    // tracking  pas de notice php on le vire
    if (!empty($USER->id_user) && !empty($USER->type_user))
        @maj_info_deconnexion($USER->id_user);
    @detruire_session();
    if ($id_examen && $id_etab)
        $extra='&amp;id_examen='.$id_examen.'&amp;id_etab='.$id_etab;
    else
        $extra='';
     header("location:$chemin/$page_origine?err_c2i=" . $err_c2i.$extra);
} else {
    @ detruire_session(); // inutile ?
    header("location:$chemin/index.php");
}

?>
