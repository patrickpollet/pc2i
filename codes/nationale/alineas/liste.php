<?php

/**
 * @author Patrick Pollet
 * @version $Id: selection.php 855 2009-06-06 09:24:08Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramétres

require_once("../lib_nationale.php");

require_login('P'); //PP

//seulement sur une nationale
if (($CFG->universite_serveur !=1 ) || !is_admin(false,$CFG->universite_serveur)) erreur_fatale("err_acces");

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	$id_action=required_param('id_action',PARAM_INT);
}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$modele=<<<EOM


<div id="criteres">
<form class="normale" name="monform" id="monform" method="post" action="liste.php">
<fieldset>
<legend>{nouvel_alinea}</legend>
<p class="double">
	<label for="referentielc2i">{t_domaine} </label>
    {select_referentielc2i}
</p>
<p class="double">
	<label for="alinea">{t_alinea} </label>
	<input class="required validate-digits" name="alinea" size="5" value=""  title="{js_valeur_numerique_attendue}"/>
</p>
<p class="double">
	<label for="libelle_alinea">{t_competence} </label>
	<input class="required" name="libelle_alinea" size="60" value="" title="{js_valeur_manquante}"/>
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
      <th  class="bg"> {t_aptitude} </th>
      <th class='bg'> {t_nbqa} </th>

<th class="bg" style="width:100px;">{t_actions}</th>
 
      </tr>
</thead>
  <tfoot>
  <tr>
  <td colspan="6"> {nb} {competences}</td>
  </tr>
  </tfoot>
<tbody>
      <!-- START BLOCK : ligne -->
    <tr  class="{paire_impaire}">
      <td>{referentielc2i}.{alinea}</td>
      <td>{domaine}</td>

      <td class="editable"
          ondblclick="inlineMod('{id}',this,'aptitude','TexteMultiNV','{ajax_modif}');"
                            >{aptitude}</td>
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

if ($action=="supprimer"){ 
    if (peut_supprimer_alinea_byid($id_action)) 
          delete_records("alinea","id=$id_action");
}


if (optional_param('add',0,PARAM_INT)) {
    $ligne=new StdClass();
    $ligne->alinea=required_param('alinea',PARAM_INT);
    $ligne->referentielc2i=required_param('referentielc2i',PARAM_CLEAN);
    $ligne->aptitude=required_param('libelle_alinea',PARAM_CLEAN);
    insert_record('alinea',$ligne,false);
}


$tpl->assignInclude("corps",$modele,T_BYVAR);

$CFG->utiliser_validation_js=1;
$CFG->utiliser_tables_sortables_js=1;
$CFG->utiliser_inlinemod_js=1;

$tpl->prepare($chemin,array('icones_action'=>1));

print_form_actions ($tpl,'form_actions','','liste.php');

$items=get_alineas(false,'',false);  //V2 pas d'erreur si aucun ecore defini

//print_r($items);
$compteur_ligne=0;
foreach ($items as $item) {
    $tpl->newBlock("ligne");
      $tpl->setCouleurLigne($compteur_ligne);

      $ref=get_referentiel($item->referentielc2i);
      $item->domaine=$ref->domaine;
    $tpl->assignobjet($item);

  // rev 977 affiche aussi le nb de questions total (explique pourquoi la poubelle peut �tre absente)
    $nbvalides=get_questions_par_alinea($item->referentielc2i,$item->alinea,true,'',true);
    $nbtotal=get_questions_par_alinea($item->referentielc2i,$item->alinea,false,'',true);

     $tpl->assign('nbqa',$nbvalides.'/'.$nbtotal);
     
     $items=array();
     $items[]=new icone_action('consulter',"consulterItem('{$item->id}')");
     
      if (peut_supprimer_alinea($item->referentielc2i,$item->alinea)) {
     		$items[]=new icone_action('supprimer',"supprimerItem('{$item->id}')" );     	 
     }else  $items[]=new icone_action( );


     $tpl->newBlock ('icones_actions');
     print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);
    $compteur_ligne++;
}
$tpl->assign("_ROOT.nb",$compteur_ligne);
$tpl->assign("_ROOT.nb_items",$compteur_ligne. " ".traduction("alineas"));

$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.titre_popup", traduction("gestion_alineas"));

$tpl->assignGlobal("ajax_modif","../ajax/modif_alinea.php");

print_boutons_criteres($tpl,'boutons_action','criteres');

$table=get_referentiels();
       foreach($table as $ref) $ref->domaine=$ref->referentielc2i." - ".$ref->domaine;

$attrs_ref= "style='width:200px;'   title=\"".traduction("js_referentiel_manquant")."\"";       



print_select_from_table($tpl,'select_referentielc2i',$table,"referentielc2i",'required validate-selection',
                              $attrs_ref,'referentielc2i','domaine',traduction ("ref_c2i"),'');





$items=array();
$items[0]['action']='nouveau';
$items[0]['js']="showHide('criteres','','show')";
$items[0]['texte']='nouvel_alinea';


print_menu($tpl,"_ROOT.menu_niveau2",$items);

$tpl->print_boutons_fermeture();

$tpl->printToScreen(); //affichage


?>
