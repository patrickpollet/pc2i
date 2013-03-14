<?php

/**
 * @author Patrick Pollet
 * @version $Id: version_imprimable.php 1082 2010-05-14 15:26:57Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 set_time_limit(0); //important pour OOo

 $chemin='../..';
require_once ("$chemin/commun/c2i_params.php");

require_login('P'); //PP

$idq=required_param("idq",PARAM_INT,"");
$ide=required_param("ide",PARAM_INT,"");
$mode=optional_param("mode",QCM_PREVISUALISATION,PARAM_INT);


v_d_o_d("etl"); // droits apres lecture $ide

set_time_limit(0);

require_once ("$chemin/commun/lib_OOo.php");

$odf=examen_to_OOo($idq,$ide,$mode);

// We export the file
if ($mode==QCM_CORRIGE)
    $odf->exportAsAttachedFile("corrige_examen_{$ide}_{$idq}.odt");
else
    $odf->exportAsAttachedFile("examen_{$ide}_{$idq}.odt");
?>
