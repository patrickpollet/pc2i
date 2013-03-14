<?php
/*
 * Created on 20 mars 2009
 *
 * @author Patrick Pollet
 * @version $Id: get_familles.php 1151 2010-09-17 12:05:14Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  * renvoie les familles (toutes ou celle d'un  alineas & referentiel)
  * utilis� par les listes dynamiques
  * comme cette info est "publique" , authentification relach�e
  */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");                 //fichier de param�tres
require_once($chemin_commun."/lib_ajax.php");                 //fichier de param�tres

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates


$alinea_prec=optional_param("alinea_prec","",PARAM_INT);

//rev 977

$referentielc2i=optional_param("referentielc2i","",PARAM_ALPHANUM);
$alinea=optional_param("alinea",$alinea_prec,PARAM_INT);

$famille=optional_param("famille_prec","",PARAM_INT); //pr�c�dente pour reselection


if ($referentielc2i && $alinea) {
    $table=get_familles_associees ($referentielc2i,$alinea,"ordref,famille",false);
}
else if ($famille) {
          if ($fam=get_famille($famille,false))
              $table=get_familles_associees ($fam->referentielc2i,$fam->alinea,"ordref,famille",false);
     
} else  $table=array(); //get_familles($tri = 'famille');

foreach ($table as $ligne) {
//    $ligne->famille=$ligne->idf." (".$ligne->referentielc2i.".".$ligne->alinea.") ".$ligne->famille;
// rev 977 ajout N� famille dans la liste
    $ligne->famille=$ligne->idf." - ".$ligne->famille;
// rev 843 KS veut juste intitul� famille + mots-cl�s si presents
    if ($ligne->mots_clesf)
        $ligne->famille=$ligne->famille." (".$ligne->mots_clesf.")";

}
echo get_options_from_table(false,$table,"idf","famille",traduction("famille") ,$famille);


die_ok(true);


 ?>

