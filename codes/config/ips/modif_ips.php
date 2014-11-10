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
// pose un pb d'entetes HTTP
//require_once($chemin_commun."/lib_ajax.php");

if (! require_login ("P",false))
        die(traduction ("err_acces"));

//ajax pas d'erreur fatale ...
$id=optional_param("id","",PARAM_RAW);
$champ=optional_param("champ","",PARAM_RAW);
$valeur=optional_param("valeur","",PARAM_RAW);

if (!$id  || !$champ)
      die (traduction ("err_param_requis"));

if (! is_admin()) die (traduction ("err_acces"));

// nb table c2ialinea mise � jour avec un id unique !!!

    $ret=set_field("plagesip",$champ,stripslashes($valeur),"id=$id",false);
    if ($ret)
        $ret=set_field("plagesip",'ts_datemodification',time(),"id=$id",false);
if (! $ret) envoi_erreur("maj_restriction");


?>
