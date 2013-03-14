<?php

/**
 * @author Patrick Pollet
 * @version $Id: selection.php 855 2009-06-06 09:24:08Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres

require_login('P'); //PP
if (!is_admin(false,$CFG->universite_serveur)) erreur_fatale("err_acces");


$supp_id=optional_param("supp_id",0,PARAM_INT);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //créer une instance
//inclure d'autre block de templates

$modele=<<<EOM
<table width="100%">
<tr class="gauche"><td>{menu_niveau2}</td>
<td class="droite commentaire1">{nb_items}</td>
</tr></table>
<div id="erreurMsg"> </div>
<table width="100%" class="listing" id="sortable"  >
  <thead>
    <tr {bulle:astuce:msg_tri_colonnes}>
      <th  class="bg"> {t_id} </th>
      <th  class="bg"> {t_nom} </th>
      <th  class="bg"> {t_nb_aleatoire} </th>
      <th  class="bg"> {t_nb_items} </th>
    <th width="8%" class="bg nosort">{t_consult}</th>
    <th width="8%" class="bg nosort">{t_modif}</th>
    <th width="8%" class="bg nosort">{t_supp}</th>
      </tr>
</thead>
  <tfoot>
  <tr>
  <td colspan="7"> {nb} {etablissements}</td>
  </tr>
  </tfoot>
<tbody>
      <!-- START BLOCK : ligne -->
    <tr  class="{paire_impaire}">
      <td>{id_etab}</td>

      <td class="editable"
          ondblclick="inlineMod('{id_etab}',this,'nom_etab','TexteNV','{ajax_modif}');"
                            >{nom_etab}</td>
      <td class="editable droite"
          ondblclick="inlineMod('{id_etab}',this,'param_nb_aleatoire','Nombre','{ajax_modif}');"
                            >{param_nb_aleatoire}</td>

      <td class="editable droite"
          ondblclick="inlineMod('{id_etab}',this,'param_nb_items','Nombre','{ajax_modif}');"
                            >{param_nb_items}</td>

    <!-- INCLUDE BLOCK : icones_action_liste -->
    </tr>
    <!-- END BLOCK : ligne -->
  </tbody>

</table>
EOM;




$tpl->assignInclude("corps",$modele,T_BYVAR);

$CFG->utiliser_tables_sortables_js=1;
$CFG->utiliser_inlinemod_js=1;

$CFG->montrer_fiche_apres_modification=0; //important on revient ici

$tpl->prepare($chemin,array('icones_action'=>1));

$CFG->wwwroot=$locale_url_univ; //la calcul auto ne marche pas dans ce cas
$url_retour="$CFG->wwwroot/codes/nationale/etablissements/liste.php";

$url_gestion="$CFG->chemin/codes/acces/etablissement";


if ($supp_id){ //
     supprime_etablissement($supp_id,false);  //test
}


$items=get_etablissements_filtre('id_etab');
$compteur_ligne=0;
foreach ($items as $item) {
    $tpl->newBlock("ligne");
      $tpl->setCouleurLigne($compteur_ligne);
    $tpl->assignobjet($item);

    $tpl->newBlockNum("icones_action_liste",$compteur_ligne);
    $tpl->newblockNum("td_consulter_oui",$compteur_ligne);
    $tpl->assignURL("url_consulter",$url_gestion."/fiche.php?idq=".$item->id_etab);
    $tpl->newBlockNum("td_modifier_oui",$compteur_ligne);
    $tpl->assignURL("url_modifier",$url_gestion."/ajout.php?idq=".$item->id_etab."&amp;url_retour=" . $url_retour);

   if (etablissement_est_supprimable($item->id_etab)) {
    $tpl->newBlockNum("td_supprimer_oui",$compteur_ligne);
    $tpl->assign("js_supp", traduction("js_etablissement_supprimer_0") . " " . $item->id_etab . " " .
	    traduction("js_action_annuler"));
    $tpl->assignURL("url_supprimer","liste.php?supp_id=" . $item->id_etab);
   } else  $tpl->newBlock("td_supprimer_non");


    $compteur_ligne++;
}
$tpl->assign("_ROOT.nb",$compteur_ligne);
$tpl->assign("_ROOT.nb_items",$compteur_ligne. " ".traduction("etablissements"));


$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.titre_popup", traduction("gestion_etablissements"));

$tpl->assignGlobal("ajax_modif","../ajax/modif_etablissement.php");
$items=array();
$items[0]['action']='nouveau';
$items[0]['url']=$url_gestion."/ajout.php?url_retour=" . $url_retour;
$items[0]['texte']='nouvel_etablissement';




print_menu($tpl,"_ROOT.menu_niveau2",$items);
$tpl->print_boutons_fermeture();
$tpl->printToScreen(); //affichage


?>
