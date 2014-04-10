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

require_once("../lib_nationale.php");

require_login('P'); //PP
if (!is_admin(false,$CFG->universite_serveur)) erreur_fatale("err_acces");


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

        <th width="8%" class="bg nosort">{t_consult}</th>
   <!-- <th width="8%" class="bg nosort">{t_modif}</th>  -->
    <th width="8%" class="bg nosort">{t_supp}</th>

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
    <!-- INCLUDE BLOCK : icones_action_liste -->
    </tr>
    <!-- END BLOCK : ligne -->
  </tbody>
</table>



EOM;

$supp_id=optional_param("supp_id",0,PARAM_INT);

if ($supp_id){ //
    if (peut_supprimer_alinea_byid($supp_id)) 
          delete_records("alinea","id=$supp_id");
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
$CFG->wwwroot=$locale_url_univ; //la calcul auto ne marche pas dans ce cas
$url_retour="$CFG->wwwroot/codes/nationale/alineas/liste.php";

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

     //$tpl->assign('nbqa',get_questions_par_alinea($item->referentielc2i,$item->alinea,true,'',true));

       $tpl->newBlockNum("icones_action_liste",$compteur_ligne);
    $tpl->newblockNum("td_consulter_oui",$compteur_ligne);
    $tpl->assignURL("url_consulter","fiche.php?id=".$item->id);
    /** rev 1041 rien a modifier
    $tpl->newBlockNum("td_modifier_oui",$compteur_ligne);
    $tpl->assignURL("url_modifier","ajout.php?id=".$item->id."&url_retour=" . $url_retour);
    **/
    if (peut_supprimer_alinea($item->referentielc2i,$item->alinea)) {
	    $tpl->newBlockNum("td_supprimer_oui",$compteur_ligne);
	    $tpl->assign("js_supp", traduction("js_alinea_supprimer_0") . " "
          . $item->referentielc2i . "." .$item->alinea . " ".
		    traduction("js_action_annuler"));
	    $tpl->assignURL("url_supprimer","liste.php?supp_id=" . $item->id);
    }
    else  $tpl->newBlock("td_supprimer_non");


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
