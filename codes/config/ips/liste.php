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

if (!$CFG->restrictions_ip) erreur_fatale('err_restrictions_ip');


require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$modele=<<<EOM

<div class="commentaire1">{info_restrictions_ips}</div>

<div class="gauche">{menu_niveau2} </div>
<div id="criteres">
<form class="normale" name="monform" id="monform" method="post" action="liste.php">
<fieldset>
<legend>{nouvelle_plage}</legend>

<p class="double">
  <label for="nom">{form_nom} </label>
  <input name="nom" id="nom" size="60" value="" class="required"
					title="{js_valeur_manquante}"/>
</p>

<p class="double">
  <label for="adresses">{form_adresses} </label>
 <input name="adresses" id="adresses" size="60" value="" class="required"
					title="{js_valeur_manquante}"/>
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
<div class="commentaire1">{info_restrictions_ips}</div>
</div>



<div id="erreurMsg"> </div>
<table width="100%" class="listing" id="sortable"  >
  <thead>
    <tr {bulle:astuce:msg_tri_colonnes}>
      <th  class="bg"> {t_id} </th>
      <th  class="bg"> {t_nom} </th>
      <th  class="bg"> {t_adresses} </th>
        <th class='bg'  width='10%'> {t_utilisees_examens} </th>
        <th width="8%" class="bg nosort">{t_consult}</th>
    <th width="8%" class="bg nosort">{t_supp}</th>

      </tr>
</thead>
  <tfoot>
  <tr>
  <td colspan="7"> {nb} {plages}</td>
  </tr>
  </tfoot>
<tbody>
      <!-- START BLOCK : ligne -->
    <tr  class="{paire_impaire}">
      <td>{id}</td>
 
       <td class="editable"
          ondblclick="inlineMod('{id}',this,'nom','TexteMultiNV','{ajax_modif}');"
                            >{nom}</td>
      <td class="editable"
          ondblclick="inlineMod('{id}',this,'adresses','TexteMultiNV','{ajax_modif}');"
                            >{adresses}</td>
        <td class='droite'>{nbqa}</td>
    <!-- INCLUDE BLOCK : icones_action_liste -->

    </tr>
    <!-- END BLOCK : ligne -->
  </tbody>

</table>




EOM;

$supp_id=optional_param("supp_id",0,PARAM_INT);

if ($supp_id){ //
    supprime_plage ($supp_id);
}




if (optional_param('add',0,PARAM_INT)) {
	$ligne=new StdClass();
	$ligne->nom=required_param('nom',PARAM_CLEAN);
	$ligne->adresses=required_param('adresses',PARAM_CLEAN);
	$ligne->id_etab=$USER->id_etab_perso;

	$id=ajoute_plage($ligne,1);

}

$tpl->assignInclude("corps",$modele,T_BYVAR);

$CFG->utiliser_validation_js=1;
$CFG->utiliser_tables_sortables_js=1;
$CFG->utiliser_inlinemod_js=1;

$tpl->prepare($chemin,array('icones_action'=>1));
$CFG->wwwroot=$locale_url_univ; //la calcul auto ne marche pas dans ce cas
$url_retour="$CFG->wwwroot/codes/config/ips/liste.php";


$items=get_plages_ip_declarees($USER->id_etab_perso,'id');

$compteur_ligne=0;
foreach ($items as $item) {
    $tpl->newBlock("ligne");
      $tpl->setCouleurLigne($compteur_ligne);
    $tpl->assignobjet($item);

    
    $examens=get_examens_utilisant_plage($item->id);
    if (count($examens)) {
        $tpl->assign('nbqa',count($examens));
    } else {
        $tpl->assign('nbqa','');
    }    
    
    $tpl->newBlockNum("icones_action_liste",$compteur_ligne);
    $tpl->newblockNum("td_consulter_oui",$compteur_ligne);
    $tpl->assignURL("url_consulter","fiche.php?id=".$item->id);
    if (count($examens)) {
        $tpl->newBlock("td_supprimer_non");
    } else {
        $tpl->newBlockNum("td_supprimer_oui",$compteur_ligne);
	    $tpl->assign("js_supp", traduction("js_ips_supprimer_0") . " " . $item->id . " " .
		    traduction("js_action_annuler"));
	    $tpl->assignURL("url_supprimer","liste.php?supp_id=" . $item->id);
    }
    
    $compteur_ligne++;
}
$tpl->assign("_ROOT.nb",$compteur_ligne);


$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.titre_popup", traduction("gestion_ips"));

$tpl->assignGlobal("ajax_modif","modif_ips.php");

print_boutons_criteres($tpl,'boutons_action','criteres');

$items=array();
$items[0]['action']='nouveau';
$items[0]['js']="showHide('criteres','','show')";
$items[0]['texte']='nouvelle_plage';




print_menu($tpl,"_ROOT.menu_niveau2",$items);

$tpl->print_boutons_fermeture();
$tpl->printToScreen(); //affichage


?>
