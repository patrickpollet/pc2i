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
<legend>{nouvel_famille}</legend>

<p class="double">
 <label for="referentielc2i">{t_domaine} </label>
  {select_referentielc2i}  {select_alinea}
</p>

<p class="double">
 <label for="libelle_famille">{t_famille} </label>
 <input class="required" name="libelle_famille" size="60" value="" title="{js_valeur_manquante}"/>
</p>
<p class="double">
 <label for="com_famille">{t_commentaires} </label>
<input class="required" name="com_famille" size="60" value="" title="{js_valeur_manquante}"/>
</p>
<p class="double">
 <label for="mc_famille">{t_motscles} </label>
<input class="required" name="mc_famille" size="60" value="" title="{js_valeur_manquante}"/>
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
      <th  class="bg"> {t_referentiel} </th>
      <th  class="bg"> {t_alinea} </th>
 <!-- START BLOCK : t_ancien_domaine -->
            <th class="bg">
               {t_ancien_domaine} </th>
 <!-- END BLOCK : t_ancien_domaine -->
      <th    class="bg"> {t_famille}</th>
      <th    class="bg"> {t_ordref}</th>

      <th    class="bg"> {t_commentaires}</th>
      <th    class="bg"> {t_motscles}</th>
       <th class='bg'> {t_nbqa} </th>
        <th width="8%" class="bg nosort">{t_consult}</th>
  <!--  <th width="8%" class="bg nosort">{t_modif}</th>  -->
    <th width="8%" class="bg nosort">{t_supp}</th>

      </tr>
</thead>
  <tfoot>
  <tr>
  <td colspan="{colspan}"> {nb} {familles}</td>
  </tr>
  </tfoot>
<tbody>
      <!-- START BLOCK : ligne -->
    <tr  class="{paire_impaire}">
      <td>{idf}</td>
<!-- START BLOCK : ref_V1 -->
      <td>{referentielc2i}</td>
      <td>{alinea}</td>
<!-- END BLOCK : ref_V1 -->

<!-- START BLOCK : ref_V2 -->
       <td class="editable"
          ondblclick="inlineMod('{idf}',this,'referentielc2i','TexteNV','{ajax_modif}');"
                            >{referentielc2i}</td>
       <td class="editable"
          ondblclick="inlineMod('{idf}',this,'alinea','Entier','{ajax_modif}');"
                            >{alinea}</td>
<!-- END BLOCK : ref_V2 -->


 <!-- START BLOCK : ligne_ancien_domaine -->
            <td>
              <s> {ancien_domaine}</s>
          </td>
 <!-- END BLOCK : ligne_ancien_domaine -->

      <td class="editable"
          ondblclick="inlineMod('{idf}',this,'famille','TexteMultiNV','{ajax_modif}');"
                            >{famille}</td>
      <td class="editable"
          ondblclick="inlineMod('{idf}',this,'ordref','Entier','{ajax_modif}');"
                            >{ordref}</td>

      <td class="editable"
          ondblclick="inlineMod('{idf}',this,'commentaires','TexteMulti','{ajax_modif}');"
                            >{commentaires}</td>

      <td class="editable"
          ondblclick="inlineMod('{idf}',this,'mots_clesf','TexteMulti','{ajax_modif}');"
                            >{mots_clesf}</td>
        <td class='droite'>{nbqa}</td>
    <!-- INCLUDE BLOCK : icones_action_liste -->

    </tr>
    <!-- END BLOCK : ligne -->
  </tbody>

</table>




EOM;

$supp_id=optional_param("supp_id",0,PARAM_INT);

if ($supp_id){ //
     delete_records("familles","idf=$supp_id");
}




if (optional_param('add',0,PARAM_INT)) {
	$ligne=new StdClass();
    $ligne->alinea=required_param('alinea',PARAM_INT);
    $ligne->referentielc2i=required_param('referentielc2i',PARAM_CLEAN);
    $ligne->famille=required_param('libelle_famille',PARAM_CLEAN);
	$ligne->commentaires=optional_param('com_famille','',PARAM_CLEAN);
	$ligne->mots_clesf=optional_param('mc_famille','',PARAM_CLEAN);
	$idf=ajoute_famille ($ligne,1);
}

$tpl->assignInclude("corps",$modele,T_BYVAR);

$CFG->utiliser_validation_js=1;
$CFG->utiliser_tables_sortables_js=1;
$CFG->utiliser_inlinemod_js=1;

$tpl->prepare($chemin,array('icones_action'=>1));
$CFG->wwwroot=$locale_url_univ; //la calcul auto ne marche pas dans ce cas
$url_retour="$CFG->wwwroot/codes/nationale/familles/liste.php";

$colspan=10;
//  $items=get_familles('referentielc2i,alinea,ordref');
 $items=get_familles('idf');

$tpl->assign('_ROOT.colspan',$colspan);
$compteur_ligne=0;
foreach ($items as $item) {
    $tpl->newBlock("ligne");
      $tpl->setCouleurLigne($compteur_ligne);
    $tpl->assignobjet($item);

    // rev 977 affiche aussi le nb de questions total (explique pourquoi la poubelle peut �tre absente)
    $nbvalides=get_questions_par_famille($item->idf,true,'',true);
    $nbtotal=get_questions_par_famille($item->idf,false,'',true);

     $tpl->assign('nbqa',$nbvalides.'/'.$nbtotal);

 //retouche rev 977

    $tpl->newBlock('ref_V1');
    $tpl->assign('referentielc2i',$item->referentielc2i);
    $tpl->assign('alinea',$item->alinea);
    $tpl->newBlockNum("icones_action_liste",$compteur_ligne);
    $tpl->newblockNum("td_consulter_oui",$compteur_ligne);

    $tpl->assignURL("url_consulter","fiche.php?id=".$item->idf);
    /*
    $tpl->newBlockNum("td_modifier_oui",$compteur_ligne);
    $tpl->assignURL("url_modifier","ajout.php?id=".$item->idf."&url_retour=" . $url_retour);
    */
    if (peut_supprimer_famille($item->idf)) {
	    $tpl->newBlockNum("td_supprimer_oui",$compteur_ligne);
	    $tpl->assign("js_supp", traduction("js_famille_supprimer_0") . " " . $item->idf . " " .
		    traduction("js_action_annuler"));
	    $tpl->assignURL("url_supprimer","liste.php?supp_id=" . $item->idf);
    }
    else  $tpl->newBlock("td_supprimer_non");


    $compteur_ligne++;
}
$tpl->assign("_ROOT.nb",$compteur_ligne);
$tpl->assign("_ROOT.nb_items",$compteur_ligne. " ".traduction("familles"));

$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.titre_popup", traduction("gestion_familles"));

$tpl->assignGlobal("ajax_modif","../ajax/modif_famille.php");

print_boutons_criteres($tpl,'boutons_action','criteres');

// g�n�ration des listes d�roulantes
$attrs_ref= "style='width:200px;'   title=\"".traduction("js_referentiel_manquant")."\"";
$attrs_alinea="style='width:150px;'   title=\"".traduction("js_alinea_manquant")."\"";

print_selecteur_ref_alinea_famille($tpl, "monform", "select_referentielc2i", "required validate-selection", $attrs_ref, //select referentiel
"select_alinea", "required validate-selection", $attrs_alinea, //select alinea
"", "", "", //select famille
false, false, false, //input famille
'','','', false); //valeurs actuelles

$items=array();
$items[0]['action']='nouveau';
$items[0]['js']="showHide('criteres','','show')";
$items[0]['texte']='nouvel_famille';




print_menu($tpl,"_ROOT.menu_niveau2",$items);

$tpl->print_boutons_fermeture();
$tpl->printToScreen(); //affichage


?>
