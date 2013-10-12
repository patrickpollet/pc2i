<?php


/**
 * @author Patrick Pollet
 * @version $Id: bilan_questions.php 1198 2011-01-26 17:09:46Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	bilan des questions
// liste des questions par �tat et ref/alin/famille
//
////////////////////////////////

set_time_limit(0); //important pour OOo

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

require_login('P'); //PP
v_d_o_d("ql");

$referentielc2i = optional_param("referentielc2i", "", PARAM_ALPHANUM);
$alinea = optional_param("alinea", 0, PARAM_INT);


$famille = optional_param("famille", "", PARAM_INT); //critere de recherche
$filtre_valid = optional_param("filtre_valid", QUESTION_TOUTE, PARAM_INT);
$doit = optional_param("doit", 0, PARAM_INT);
$ooo = optional_param("OOo", "", PARAM_TEXT);

if ($doit) {

    //repare un bug dans php2odt qui a un pb quand un texte contient un symbole $
    // ce qui perturbe esnuite le parser XML d'OOo
    function trim_OOo($string) {
        $string=trim($string);
        $string=str_replace ('$','{dollar}',$string);
        //  $string=str_replace ('>','\&gt;',$string);
        return $string;
    }

    $ligne_e = new StdClass();
    $ligne_e->id_examen = $ligne_e->id_etab = $ligne->type_tirage = ""; // bidon pour imprime_question
    // CM ajout type_p = 'oui' pour bilan certification OU positionnement
    $critere = $USER->type_plateforme . "='oui'";
    $oooname = 'questions_' . $USER->type_plateforme;
    if ($referentielc2i) {
        $critere .= " and referentielc2i='" . $referentielc2i . "'";
        $oooname .= '_' . $referentielc2i;
    }
    if ($alinea) {
        $critere .= " and alinea='" . $alinea . "'";
        $oooname .= '_' . $alinea;
    }
    if ($famille) {
        $critere .= " and id_famille_validee='" . $famille . "'";
        $oooname .= '_' . $famille;
    }
    if ($filtre_valid != QUESTION_TOUTE) {
        //V2 $filtre_valid=0 = Non examinée
        $critere .= " and etat='" . $filtre_valid . "'";
        $oooname .= '_' . get_etat_validation($filtre_valid);
    }

    //  print $critere;
    $res = get_records("questions", $critere, "etat,referentielc2i,alinea,id_famille_validee");

    $etat_courant = "";
    $num_q = 0;

    if ($ooo) {
        // sortie OpenOffice Writer (rev 1009) et arr�t imm�diat

        // Make sure you have Zip extension or PclZip library loaded
        require_once ($CFG->chemin_commun . '/OOo/odtphp/library/odf.php');
        $filename = '/templates2/OOo/bilan_questions.odt';
        //essayer d'utiliser un mod�le local
        if (file_exists($CFG->chemin . '/locale' . $filename))
        $odf = new odf($CFG->chemin . '/locale' . $filename);
        else // si pas trouv� alors standard
        $odf = new odf($CFG->chemin . $filename);

        $row = $odf->setSegment('categorie');

        $images = array (
			'jpg',
			'jpeg',
			'png',
			'gif'
        ); //gif non reconnu par latex

        $max=85;
        $min=85;

        $nb=0;

        foreach ($res as $ligne_q) {
            $nb++;
            //       if ($nb <=$min) continue;
            if ($etat_courant != $ligne_q->etat) {
                $etat_courant = $ligne_q->etat;
                if ($num_q > 0)
                $row->merge();
                $num_q = 1;
                $row->setVars('titrecategorie', traduction("liste_questions") . ' ' . get_etat_validation($ligne_q->etat) . 's');
                $row->setVars('date', date('d/m/Y'));

            }

            $row->question->setVars('numero', $num_q++);
            $row->question->setVars('texte', trim_OOo($ligne_q->titre));
            //$referentiel = trim($ligne_q->referentielc2i) . "." . trim($ligne_q->alinea);
            $referentiel=get_domaine_traite($ligne_q);
            $id = $ligne_q->id_etab . "." . $ligne_q->id;
            $row->question->setVars('ref', $referentiel);
            $row->question->setVars('id', $id);

            $docs = get_documents($ligne_q->id, $ligne_q->id_etab, false);
            if (!empty ($docs)) {
                foreach ($docs as $doc) {
                    //print_r($doc); print('<br/>');
                    if (in_array($doc->extension, $images)) {
                        $dir = get_document_location($ligne_q->id, $ligne_q->id_etab);
                        $fic = $dir . '/' . $doc->id_doc . '.' . $doc->extension;
                        if (file_exists($fic)) {
                            $nomFic = $ligne_q->id_etab . '_' . $ligne_q->id . '_' . $doc->id_doc . '.' . $doc->extension;
                            copier_element($fic, $dir . '/' . $nomFic);
                            $row->question->setImage('image', $dir . '/' . $nomFic);
                            break; //une seule
                        }
                    }
                }

            } else
            $row->question->setVars('image', '');

            $lettre_r = ord("A");
            $num_r = 1;
            $reponses = get_reponses($ligne_q->id, $ligne_q->id_etab, false, false);
            foreach ($reponses as $ligne_r) {

                if ($ligne_r->bonne == 'OUI')
                $row->question->reponse->case('[B]');
                else
                $row->question->reponse->case('[M]');

                if ($CFG->numerotation_reponses == 2)
                $row->question->reponse->lettre(chr($lettre_r++));
                else
                $row->question->reponse->lettre($num_r++);


                $row->question->reponse->reponse(trim_OOo($ligne_r->reponse));

                $row->question->reponse->merge();
            }
            $row->question->merge();

            //     if ($nb>$max) break ;
        }
        $row->merge();
        $odf->mergeSegment($row);

        $odf->exportAsAttachedFile($oooname . ".odt");
        die();

    }
}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$fiche =<<<EOF

<form method="post" name="monform" id="monform"  action="bilan_questions.php">
<span class="taille2">{options_selection}</span>
<div class="gauche">
       {select_referentielc2i} | {select_alinea} | {select_famille}
  <!-- START BLOCK : filtre_etat -->        
        | {select_filtre_valid}  
<!-- END BLOCK : filtre_etat -->     
         <input type="submit" name="ok" value="HTML"/>
          <input type="submit" name="OOo" value="OOo"/>
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
<input name="doit" type="hidden" value="1"/>
</div>
</form>
<hr/>
<!-- START BLOCK : etat -->
<table width="100%" id="qcm">
<tr>
    <td class="titre1">{liste_questions}</td>
</tr>
<!-- START BLOCK : question -->
{question_ici}
 <tr>
 <td>

<!-- START BLOCK : commentaires -->

<div class="information_gauche">
<b>Commentaires des experts valideurs:</b> <br/>
<!-- START BLOCK : commentaire -->
{login} {date} : <br/>
<p>{com}</p>
<p>{mod}</p>
<!-- END BLOCK : commentaire -->
</div>
<!-- END BLOCK : commentaires -->
</td>
</tr>
<!-- END BLOCK : question -->
</table>
<hr/>
<!-- END BLOCK : etat -->

EOF;

$tpl->assignInclude("corps", $fiche, T_BYVAR);

$tpl->prepare($chemin);
$CFG->utiliser_prototype_js = 1; //forc�
//print "r=$referentielc2i a=$alinea f=$famille";

$droit_commentaires = 0;
if ($CFG->universite_serveur == 1)
if ($USER->type_plateforme == "certification")
if (a_capacite("qv", 1))
$droit_commentaires = 1;

$tpl->traduit("titre_popup", "bilan_questions");

// g�n�ration des listes d�roulantes
$attrs_ref = "style='width:180px;'   title=\"" . traduction("js_referentiel_manquant") . "\"";
$attrs_alinea = "style='width:180px;'";
$attrs_famille = "style='width:180px;'";

print_selecteur_ref_alinea_famille($tpl, "monform", "select_referentielc2i", 'required validate-selection', $attrs_ref, //select referentiel
"select_alinea", '', $attrs_alinea, //select alinea
"select_famille", '', $attrs_famille, //select famille
false, false, false, //input famille
$referentielc2i, $alinea, $famille, false); //valeurs actuelles


//liste d�roulante des �tats de questions
// seuls les experts validateurs peuvent voir les non validées
if (a_capacite("qv") || empty($CFG->seulement_validees_liste)) {
	$tpl->newBlock ('filtre_etat');
	print_select_from_table($tpl, "select_filtre_valid", get_etats_validation(), "filtre_valid", null, "", "id", "texte", traduction("alt_validation"), $filtre_valid);
}
$tpl->gotoBlock("_ROOT");

if ($doit) {

    $etat_courant = "";
    $num_q = 0;

    if (!$ooo) {
        // sortie OpenOffice Writer d�ja faite (rev 1009)
         
        if (empty($res)) { // rien trouvé
            $tpl->newBlock("etat");
            $tpl->assign("liste_questions", traduction("liste_questions") . ' ' . get_etat_validation($filtre_valid) . 's');
             
        } else {

            foreach ($res as $ligne_q) {

                if ($etat_courant != $ligne_q->etat) {
                    $etat_courant = $ligne_q->etat;
                    $tpl->newBlock("etat");
                    $tpl->assign("liste_questions", traduction("liste_questions") . ' ' . get_etat_validation($ligne_q->etat) . 's');
                    $num_q = 0;
                }
                $num_q++;
                $tpl->newBlock("question");

                list ($fiche, $nbr) = imprime_question($num_q, $ligne_e, $ligne_q, false, false, false, 
                      3, QCM_CORRECTION, false,$CFG->utiliser_commentaires_reponses);
                $tpl->assign("question_ici", $fiche);

                if ($droit_commentaires == 1) {
                    $aviss = get_question_avis_validations($ligne_q->id, $ligne_q->id_etab);
                    if (count($aviss)) {
                        $tpl->newBlock("commentaires");
                        foreach ($aviss as $avis) {
                            $tpl->newBlock("commentaire");
                            $tpl->assign("login", get_fullname($avis->login, false));
                            $tpl->assign("date", userdate($avis->ts_date, 'strftimedatetimeshort'));
                            $tpl->assign("com", affiche_texte($avis->remarques));
                            $tpl->assign("mod", affiche_texte($avis->modifications));
                        }
                    }
                }
            }

        }
    }
}

$tpl->print_boutons_fermeture();

$tpl->printToScreen(); //affichage
?>

