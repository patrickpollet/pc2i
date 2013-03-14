<?php
/*
 * Created on 20 mars 2009
 *
 * @author Patrick Pollet
 * @version $Id: get_familles.php 633 2009-04-05 12:16:29Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  * renvoie les mots clés d'une famille
  * utilisé par les listes dynamiques
  * comme cette info est "publique" , authentification relachée
  */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");                 //fichier de paramètres
require_once($chemin_commun."/lib_ajax.php");                 //fichier de paramètres

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$famille_prec=optional_param("famille_prec","",PARAM_INT); //précédente pour reselection
$famille=optional_param("famille",$famille_prec,PARAM_INT); //actuelle

if ($famille)
	if ($fam=get_famille($famille,false)) {
               print get_infos_famille($fam);

    }
    else print ("&nbsp;");
else print "&nbsp;";

//print "hello pp";
die_ok(true);

 ?>

