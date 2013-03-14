<?php

/**
 * @author Patrick Pollet
 * @version $Id: liste_emargement.php 1082 2010-05-14 15:26:57Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 set_time_limit(0); //important pour OOo


 $chemin='../..';
require_once ("$chemin/commun/c2i_params.php");

require_login('P'); //PP

$idq=required_param("idq",PARAM_INT,"");
$ide=required_param("ide",PARAM_INT,"");

v_d_o_d("etl"); // droits apres lecture $ide

require_once ("$chemin/commun/lib_OOo.php");

$odf=listeemargement_OOo($idq, $ide);


// We export the file
$odf->exportAsAttachedFile("liste_emargement_{$ide}_{$idq}.odt");

?>
