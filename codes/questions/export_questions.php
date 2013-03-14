<?php
/**
 * @author Patrick Pollet
 * @version $Id: export_questions.php 1219 2011-03-15 10:45:33Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 *
 *
 * rev 1012 ajout selecteur par �tablissement ( etab. courant par d�faut*
 * rev      07/02/2011 attention aux deux versions du rferentiel
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";

require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

require_login("P"); //PP



set_time_limit(0); //important

$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '

$export=optional_param("questions",array(),PARAM_RAW);  //vide si aucune case coch�e


v_d_o_d("ql"); //apres lecture $ide


// rev 1012 action sur le bouton OK rappatri�e ici
if (count($export)) {
  ob_start(); //important
    require_once($chemin_commun."/lib_xml.php"); // classes d'export
    $name=$CFG->c2i.'_questions_'.$USER->type_plateforme.'_'.$ide.'_'.time().'.xml';

header("content-type: text/xml");
header('Content-Disposition: attachment; filename="'.$name.'"');


function genere_question ($question) {
    global $CFG;
    $q=new question_xml_c2i ($question, get_reponses($question->id,$question->id_etab,false,false),
    get_documents($question->id,$question->id_etab, false));
    return $q->toxml();
}

function genere_questions($type_pf,$export) {

    //liste de toutes les questions tri�es
    $ret = "";
    foreach ($export as $id=>$tmp) {
        // rev 989 securisation de l'acc�s � la BD
        if ($question = get_question_byidnat( $id ,false)) {
            $ret .= genere_question($question);
        }
    }
    return $ret;
}



$xml  = "<?xml version=\"1.0\" encoding=\"$CFG->encodage\"?>\n<questions>";

//tests

$xml.=genere_questions($USER->type_plateforme,$export);
$xml .="\n</questions>";
echo htmlentities(($xml));
while (@ob_end_clean()); //important
echo $xml;
die();

}



require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates
$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$forme1=<<<EOF

<div id="maj_ajax">

<div id="criteres1">
<form name="form_criteres" id="form_criteres" method="post" action="export_questions.php">
          {select_filtre_univ}
</form>
</div>

<form class="normale" id="monform" name="monform" method="post" action="export_questions.php">
<fieldset>
<legend>{export_questions} </legend>

<div class="gauche">{menu_niveau2} </div>



<div class="commentaire1">{info_export_questions} </div>

<input name="ide" type="hidden" value="{ide}" />



<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->




<table width="100%" class="listing" id="sortable" >
  <thead>
    <tr {bulle:astuce:msg_tri_colonnes}>
      <th  class="bg"> {t_id} </th>
      <th  class="bg"> {t_competence}</th>
      <th  class="bg"> {t_titre}</th>
      <th  class="bg"> {t_auteur}</th>
       <th  class="bg"> {t_etat}</th>
        <th  class="bg"> {t_type}</th>
      <th  class="bg nosort"> {t_envoyer}</th>
    </tr>
</thead>

<tfoot>
<tr>
<td colspan="7" > {nb} {questions} </td>
</tr>
</tfoot>
<tbody>

<!-- START BLOCK : ligne_q -->
<tr class="{paire_impaire}">
<td>{id}</td>
<td>{ref_alin}</td>
<td>{libelle} <ul style="display:inline;"> {consulter_fiche} </ul></td>
<td>{auteur}</td>
<td>{etat}</td>
<td>{pf}</td>

<td>
<input type ="checkbox" value="1" name="questions[{id}]" checked="checked" />
</td>

</tr>
<!-- END BLOCK : ligne_q -->

<!-- START BLOCK : no_results -->
<TR class="information">
<td colspan ="7">
		{msg_pas_de_questions}
</TD>
</TR>
<!-- END BLOCK : no_results -->
</tbody>

</table>

<div class="centre">
{bouton:ok}
</div>

</fieldset>
</form>
</div>

EOF;

$forme2=<<<EOF
{resultats_op}
EOF;





$tpl->assignInclude("corps",$forme1,T_BYVAR);
$tpl->prepare($chemin);

$tpl->assign("_ROOT.titre_popup",traduction("export_questions"));
$tpl->assign("ide", $ide);

$CFG->utiliser_tables_sortables_js=1;

//rev 941 OK pour les non valid�es en positionnement (forcement on ne peut pas encore les valider localement !)

//$questions=get_questions_locales($CFG->remonter_validees_seulement && $USER->type_plateforme=='certification' ,false);

$critere_recherche=$USER->type_plateforme."='OUI'";
if ($ide)
    $critere_recherche.=' and id_etab='.$ide ;

    $critere_tri="referentielc2i,alinea,id";
    $skip='referentielc2i';  // sauter si cette colonne est vide


$questions=get_records("questions",$critere_recherche,$critere_tri);

$compteur_ligne = 0;
foreach ($questions as $q) {
    if (empty($q->$skip)) continue;
	$tpl->newBlock("ligne_q");
	$tpl->setCouleurLigne($compteur_ligne);
	$tpl->assign("id",$q->id_etab.".".$q->id);
    $tpl->assign ("ref_alin",$q->referentielc2i.".".$q->alinea);
	$tpl->assign ("libelle",clean($q->titre,70));
	$tpl->assign ("auteur", cree_lien_mailto($q->auteur_mail,$q->auteur));
	$tpl->assign ("etat",$q->etat);
	$type="";
	if ($q->certification=="OUI") $type.="C";
	if ($q->positionnement=="OUI") $type.="P";
	$tpl->assign("pf",$type);
	print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../questions/fiche.php?idq=".$q->id."&amp;ide=".$q->id_etab));
	$compteur_ligne ++;
}

$tpl->gotoBlock("_ROOT");

$tpl->assign("nb",$compteur_ligne);
if ($compteur_ligne==0)
	$tpl->newBlock("no_results");

print_menu($tpl,"_ROOT.menu_niveau2",array(get_menu_item_legende("import_xml")));

//liste d�roulante des universit�s
$ets = get_etablissements('nom_etab');
// rev 839 ajout N� etablissement
foreach ($ets as $et) {
    $et->nom_etab = sprintf("%s (%s)", $et->nom_etab, $et->id_etab);
}
print_select_from_table($tpl, "_ROOT.select_filtre_univ", $ets, "ide", null,
     "style=\"width:300px\" onchange=\"document.getElementById('form_criteres').submit();\"",
     "id_etab", "nom_etab", traduction("universite"), $ide);


$tpl->print_boutons_fermeture();


$tpl->printToScreen();										//affichage
?>

