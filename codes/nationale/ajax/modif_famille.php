<?php
/**
 * @author Patrick Pollet
 * @version $Id: selection.php 855 2009-06-06 09:24:08Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");
//require_once($chemin_commun."/lib_ajax.php");

if (! require_login ("P",false))
        die(traduction ("err_acces"));

//ajax pas d'erreur fatale ...
$id=optional_param("id","",PARAM_INT);
$champ=optional_param("champ","",PARAM_RAW);
$echap=optional_param("echap","",PARAM_RAW);
$valeur=optional_param("valeur","",PARAM_RAW);

if (!$id  || !$champ)
      die (traduction ("err_param_requis"));

if (! teste_droit("qv")) die (traduction ("err_acces"));

$ret=set_field("familles",$champ,stripslashes($valeur),"idf=$id",false);
if (! $ret)__envoi_erreur_fatale("","","maj_famille");



?>
