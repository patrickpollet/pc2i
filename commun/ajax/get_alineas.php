<?php
/*
 * Created on 20 mars 2009
 *
 * @author Patrick Pollet
 * @version $Id: get_alineas.php 1151 2010-09-17 12:05:14Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  * renvoie les alineas d'un referentiel
  * utilis� par les listes dynamiques
  * comme cette info est "publique" , authentification relach�e
  */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");                 //fichier de param�tres
require_once($chemin_commun."/lib_ajax.php");                 //fichier de param�tres

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
//require_login("E"); //PP


$referentielc2i=optional_param("referentielc2i","",PARAM_ALPHANUM);

$tri='referentielc2i,alinea';

$alinea=optional_param("alinea_prec","",PARAM_INT);//reselection en mode modif ...


$table=array();  // select vide si pas de referentiel
if ($referentielc2i)
           $table=get_alineas($referentielc2i, $tri,false);
foreach ($table as $ligne) {
    $ligne->aptitude=$ligne->alinea." - ".$ligne->aptitude;
}
echo get_options_from_table(false,$table,"alinea","aptitude",traduction("alinea") ,$alinea);


die_ok(true);


 ?>

