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
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

require_login('P'); //PP
if (!is_admin(false,$CFG->universite_serveur)) erreur_fatale("err_acces");

//seulement sur une nationale
//if (($CFG->universite_serveur !=1 ) || !is_admin(false,$CFG->universite_serveur)) erreur_fatale("err_acces");


// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	$id_action=required_param('id_action',PARAM_INT);
}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$modele=<<<EOM

<!-- INCLUDESCRIPT BLOCK : ./actions_js.php -->

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
   <th class="bg" style="width:100px;">{t_actions}</th>   
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

          <!-- START BLOCK : icones_actions -->
          <td>
          {icones_actions}
          </td>
<!-- END BLOCK : icones_actions -->
                            
    </tr>
    <!-- END BLOCK : ligne -->
  </tbody>

</table>

{form_actions}
EOM;




$tpl->assignInclude("corps",$modele,T_BYVAR);

$CFG->utiliser_tables_sortables_js=1;
$CFG->utiliser_inlinemod_js=1;

$CFG->montrer_fiche_apres_modification=0; //important on revient ici

$tpl->prepare($chemin,array('icones_action'=>1));

//attention url-retour est filtré par clean_param (LOCALURL)
//donc eviter un double slash dedans ...
$url_retour=add_slash_url($CFG->wwwroot).'codes/nationale/etablissements/liste.php';

print_form_actions ($tpl,'form_actions',$url_retour,$url_retour);

$url_gestion="$CFG->chemin/codes/acces/etablissement";

if ($action=="supprimer"){
	if (etablissement_est_supprimable($id_action))
     supprime_etablissement($id_action,false);  //test
}


$items=get_etablissements_filtre('id_etab');
$compteur_ligne=0;
foreach ($items as $item) {
    $tpl->newBlock("ligne");
      $tpl->setCouleurLigne($compteur_ligne);
    $tpl->assignobjet($item);
    
    $items=array();
    $items[]=new icone_action('consulter',"consulterItem('{$item->id_etab}')");
    // liens vers descripts dans codes/acces/etablissements
    $items[]=new icone_action('modifier',"modifierItem('{$item->id_etab}')");
    
    if (etablissement_est_supprimable($item->id_etab)) { 
    	$items[]=new icone_action('supprimer',"supprimerItem('{$item->id_etab}')" );
    }else  $items[]=new icone_action( );
    
    $tpl->newBlock ('icones_actions');
    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);
    

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
