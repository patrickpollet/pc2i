<?php

/**
 * @author Patrick Pollet
 * @version $Id: modif_selection_manuelle.php 634 2009-04-05 17:20:15Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Action (s�lection / deselection d'une question dans un examen manuel)
//via ajax
//
//	variables :
//	$idex = identifiant examen
///	$idexe = identifiant �tablissement examen
//	idq = identifiant question
//	ide = identifiant �tablissement question
// la question DOIT etre valid�e  en certification (a tester dans la page selection !)
// non v�rifi� ici
//
////////////////////////////////

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");
require_once($chemin_commun."/lib_ajax.php");

if (! require_login ("P",false))
        die_erreur("erreur 0");

//ajax pas d'erreur fatale ...
$idq=optional_param("idq",0,PARAM_INT);
$ide=optional_param("ide",0,PARAM_INT);
$idex=optional_param("idex",0,PARAM_INT);
$idexe=optional_param("idexe",0,PARAM_INT);

if (!$idq || ! $ide || !$idex || !$idexe)
      die_erreur("erreur 1");

if (! teste_droit("em")) die_erreur ("erreur 2");

$sel=optional_param("sel",0,PARAM_INT);


$ligne=get_examen ($idex,$idexe,false);
if (!$ligne) die_erreur(traduction ("err_examen_inconnu"));

if ($ligne->pool_pere)
    die_erreur (traduction("err_tirage_pas_membre_pool"));

if (!$ligne->type_tirage==EXAMEN_TIRAGE_MANUEL) die_erreur(traduction("err_tirage_pas_manuel"));

$question=get_question($idq,$ide,false);
if (!$question) die_erreur(traduction("err_question_inconnu"));


if ($sel=="0"){
	if (supprime_question_examen($idq,$ide,$idex,$idexe,false)) //tracking fait la bas
		die_ok();
	else die_erreur(traduction("err_retrait_question"));
} else {
	if (ajoute_question_examen($idq,$ide,$idex,$idexe,false)) {
		//tracking :
		espion2("ajout","tirage", $idexe.".".$idex);
		if ($USER->type_plateforme == "certification"  && $CFG->pas_plus_une_question_par_famille){
			// v�rification de doublon de famille
			$requeted =<<<EOR
				select count(Q.id_famille_validee) as nbf,Q.id_famille_validee
				from {$CFG->prefix}questions Q , {$CFG->prefix}questionsexamen  QE
				where Q.id =QE.id
					and Q.id_etab = QE.id_etab
						and QE.id_examen =$idex and  QE.id_examen_etab = $idexe
							group by Q.id_famille_validee having nbf>1
EOR;
				$nb=count_records_sql($requeted,false);
				if ($nb)
					die_erreur(traduction("err_deux_questions_meme_famille"));
		}
        die_ok();
	} else die_erreur(traduction("err_ajout_question"));
}
?>
