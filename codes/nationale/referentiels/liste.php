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
        <th width="8%" class="bg nosort">{t_consult}</th>
    <!--<th width="8%" class="bg nosort">{t_modif}</th> -->
    <th width="8%" class="bg nosort">{t_supp}</th>

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
    <!-- INCLUDE BLOCK : icones_action_liste -->

    </tr>
    <!-- END BLOCK : ligne -->
  </tbody>

</table>




EOM;

$supp_id=optional_param("supp_id",0,PARAM_ALPHANUM);

if ($supp_id){ //
    if (peut_supprimer_referentiel($supp_id))
       delete_records("referentiel","referentielc2i='$supp_id'");
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
$CFG->wwwroot=$locale_url_univ; //la calcul auto ne marche pas dans ce cas
$url_retour="$CFG->wwwroot/codes/nationale/referentiels/liste.php";

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

       $tpl->newBlockNum("icones_action_liste",$compteur_ligne);
    $tpl->newblockNum("td_consulter_oui",$compteur_ligne);
    $tpl->assignURL("url_consulter","fiche.php?id=".$item->referentielc2i);
    /*  rev 1041 rien a modifier !
    $tpl->newBlockNum("td_modifier_oui",$compteur_ligne);
    $tpl->assignURL("url_modifier","ajout.php?id=".$item->referentielc2i."&url_retour=" . $url_retour);
    */
    if (peut_supprimer_referentiel($item->referentielc2i)) {
	    $tpl->newBlockNum("td_supprimer_oui",$compteur_ligne);
	    $tpl->assign("js_supp", traduction("js_ref_supprimer_0") . " " . $item->referentielc2i . " " .
		    traduction("js_action_annuler"));
	    $tpl->assignURL("url_supprimer","liste.php?supp_id=" . $item->referentielc2i);
    }
    else  $tpl->newBlock("td_supprimer_non");


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
