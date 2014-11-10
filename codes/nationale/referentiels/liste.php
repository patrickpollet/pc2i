<?php

/**
 * @author Patrick Pollet
 * @version $Id: selection.php 855 2009-06-06 09:24:08Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

require_once("../lib_nationale.php");

require_login('P'); //PP

//seulement sur une nationale
if (($CFG->universite_serveur !=1 ) || !is_admin(false,$CFG->universite_serveur)) erreur_fatale("err_acces");

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	$id_action=required_param('id_action',PARAM_ALPHANUM);
}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$modele=<<<EOM

<div id="criteres">
<form class="normale" name="monform" id="monform" method="post" action="liste.php">
<fieldset>
<legend>{nouvel_referentiel}</legend>
<p class="double">
	<label for="id_ref">{t_id} </label>
	<input class="required" name="id_ref" size="5" value="" title="{js_valeur_manquante}"/>
</p>
<p class="double">
	<label for="libelle_ref">{t_domaine} </label>
	<input class="required" name="libelle_ref" size="60" value="" title="{js_valeur_manquante}"/>
</p>
<p class="simple">
{boutons_action}
</p>
<input name="add" type="hidden" value="1" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

</fieldset>
</form>


</div>

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
      <th  class="bg"> {t_domaine} </th>
      <th class='bg'> {t_nbqa} </th>
	<th class="bg" style="width:100px;">{t_actions}</th>
      
      </tr>
</thead>
  <tfoot>
  <tr>
  <td colspan="5"> {nb} {referentiels}</td>
  </tr>
  </tfoot>
<tbody>
      <!-- START BLOCK : ligne -->
    <tr  class="{paire_impaire}">

      <td>{referentielc2i}</td>

      <td class="editable"
          ondblclick="inlineMod('{referentielc2i}',this,'domaine','TexteMultiNV','{ajax_modif}');"
                            >{domaine}</td>
      <td class='droite'>{nbqa}</td>
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


if ($action=='supprimer'){ //
    if (peut_supprimer_referentiel($id_action))
       delete_records("referentiel","referentielc2i='$id_action'");
}


if (optional_param('add',0,PARAM_INT)) {
    $ligne=new StdClass();
    $ligne->referentielc2i=required_param('id_ref',PARAM_CLEAN);
    $ligne->domaine=required_param('libelle_ref',PARAM_CLEAN);
    insert_record('referentiel',$ligne,false);
}

$tpl->assignInclude("corps",$modele,T_BYVAR);

$CFG->utiliser_validation_js=1;
$CFG->utiliser_tables_sortables_js=1;
$CFG->utiliser_inlinemod_js=1;

$tpl->prepare($chemin,array('icones_action'=>1));


print_form_actions ($tpl,'form_actions','','liste.php');


$items=get_referentiels('referentielc2i',false); //V2 pas d'erreur si aucun ecore defini
$compteur_ligne=0;
foreach ($items as $item) {
    $tpl->newBlock("ligne");
      $tpl->setCouleurLigne($compteur_ligne);
    $tpl->assignobjet($item);


      // rev 977 affiche aussi le nb de questions total (explique pourquoi la poubelle peut �tre absente)
    $nbvalides=get_questions_par_referentiel($item->referentielc2i,true,'',true);
    $nbtotal=get_questions_par_referentiel($item->referentielc2i,false,'',true);

     $tpl->assign('nbqa',$nbvalides.'/'.$nbtotal);

     $items=array();
     $items[]=new icone_action('consulter',"consulterItem('{$item->referentielc2i}')");
      
     if (peut_supprimer_referentiel($item->referentielc2i)) {
     	$items[]=new icone_action('supprimer',"supprimerItem('{$item->referentielc2i}')" );
     }else  $items[]=new icone_action( );
     
     
     $tpl->newBlock ('icones_actions');
     print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);

    $compteur_ligne++;
}
$tpl->assign("_ROOT.nb",$compteur_ligne);
$tpl->assign("_ROOT.nb_items",$compteur_ligne. " ".traduction("referentiels"));

$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.titre_popup", traduction("gestion_referentiels"));

$tpl->assignGlobal("ajax_modif","../ajax/modif_referentiel.php");

$tpl->gotoBlock("_ROOT");
print_boutons_criteres($tpl,'boutons_action','criteres');


$items=array();
$items[0]['action']='nouveau';
$items[0]['js']="showHide('criteres','','show')";
$items[0]['texte']='nouvel_referentiel';

print_menu($tpl,"_ROOT.menu_niveau2",$items);
$tpl->print_boutons_fermeture();
$tpl->printToScreen(); //affichage


?>
