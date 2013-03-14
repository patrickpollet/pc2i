<?php
/*
 * Created on 20 mars 2009
 *
 * @author Patrick Pollet
 * @version $Id: cree_parcours.php 727 2009-04-23 08:25:10Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  * renvoie les familles (toutes ou celle d'un  alineas & referentiel)
  * utilisé par les listes dynamiques
  * comme cette info est "publique" , authentification relachée
  */


$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");                 //fichier de paramètres
require_once($chemin_commun."/lib_ajax.php");                 //fichier de paramètres

if (! require_login ("E",false))
        die_erreur(traduction ("err_acces"));

$idq=optional_param("idq",0,PARAM_INT);
$ide=optional_param("ide",0,PARAM_INT);


if (!$idq || ! $ide)
   die_erreur("erreur 1");


require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
require_once($CFG->chemin_commun."/pear/HTML_TreeMenu/TreeMenu.php");

//eviter d'en crééer plusieurs (tests ou rechargement page resultats qcm)

if (!$idp=existe_parcours_croisement_examen ($idq,$ide,$USER->id_user))
    $idp= cree_parcours_croisement_examen ($idq,$ide,$USER->id_user);
$parcours=get_parcours($idp);

/** je n'arrive pas a inserer le parcours dans le div . le menu arborescent remplace toute la fenetre ..


$menu=parcours_en_menu($idp,false,false);  //sans lien à cliquer (pb avec MM_swapimages)
$treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $CFG->chemin_commun."/pear/HTML_TreeMenu/images",
                'defaultClass' => 'treeMenuDefault'));

$out=<<<EOO
 <div class="titre1">{$parcours->titre}</div>
        <div id="menuLayer" class="gauche">
EOO;
echo $out;
echo $treeMenu->toHTML();
echo "</div>";
***/

$link=$chemin."/codes/parcours/fiche.php?idp=".$idp;
$onClick="openPopup(\"$link\",\"\",$CFG->largeur_popups,$CFG->hauteur_popups);";

$btn=print_bouton (false,false,"consulter_parcours",$onClick);
$info=traduction ("info_consultation_parcours",false,$parcours->titre);
$out=<<<EOO

<div class="information_gauche">
$info
</div>
$btn
EOO;
echo $out;

die_ok(true);


 ?>

