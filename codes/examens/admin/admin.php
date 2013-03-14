<?php
/**
 * @author Patrick Pollet
 * @version $Id: admin.php 1276 2011-11-05 09:00:59Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
// options d'adminstrtaion sur un examen'
//
////////////////////////////////

$chemin = '../../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres
require_once($chemin_commun."/lib_cron.php");
require_once($chemin_commun."/lib_resultats.php");

require_login('P'); //PP
if (! is_admin())
    erreur_fatale("err_droits");

$eid = required_param("eid", PARAM_CLE_C2I);
$action = required_param("action", PARAM_TEXT);
$doit = optional_param("doit",0, PARAM_INT);

$ligne=get_examen_byidnat($eid);
$ide=$ligne->id_etab;
$idq=$ligne->id_examen;

$nbPassages=compte_passages($idq,$ide); //inclus les résultats non enregistrés
$nbInscrits=compte_inscrits($idq,$ide);
$nbInscritsNP=$nbInscrits-$nbPassages;

$url_retour=$CFG->chemin."/codes/examens/fiche.php?ide=" . $ide . "&idq=" . $idq;

if ($doit) {
	require_once($CFG->chemin_commun."/lib_resultats.php");
	switch ($action) {

        case 'arc':
            archive_examen($idq,$ide);
        break;

		case 'sim' :
			simule_passage($idq,$ide); //pools gérés dans cette fonction
			 espion3("simulation_passage","examen",$ide.".".$idq,$ligne);
		break;
		case 'asim' :
			annule_simulation($idq,$ide); //pools gérés dans cette fonction
			 espion3("annulation_simulation_passage","examen",$ide.".".$idq,$ligne);
		break;

        case 'verouille' :
            verouille_examen($idq,$ide);
            // espion3("verouillage","examen",$ide.".".$idq,$ligne);
        break;

        case 'deverouille' :
            deverouille_examen($idq,$ide);
            // espion3("deverouillage","examen",$ide.".".$idq,$ligne);
        break;

		case 'pur' :
			purge_resultats_examen($idq,$ide); // 4 tables
			delete_records("resultats","examen='" . $ide . "." . $idq . "'"  ); // et ajax
			espion3("purge_resultats","examen",$ide.".".$idq,$ligne);
			// rev 1023 purge aussi les membres du pool
			if ($ligne->est_pool) {
				$fils=liste_groupe_pool($idq,$ide);
				foreach($fils as $f) {
					$fidq=$f->id_examen;
					$fide=$f->id_etab;
					purge_resultats_examen($fidq,$fide); // 4 tables
					delete_records("resultats","examen='" . $fide . "." . $fidq . "'"  ); // et ajax
					espion3("purge_resultats","examen",$fide.".".$fidq,$f);
				}
			}

			break;
		case 'si' :
            //d'abord les résultats
			purge_resultats_examen($idq,$ide); // 4 tables
			delete_records("resultats","examen='" . $ide . "." . $idq . "'"  ); // et ajax
			$critere=" id_examen=$idq and id_etab=$ide";
            //puis les inscriptions
		    delete_records("qcm",$critere,false);  //  pas fatal !
			espion3("purge_inscrits","examen",$ide.".".$idq,$ligne);
            // rev 1023 purge aussi les membres du pool
            if ($ligne->est_pool) {
                $fils=liste_groupe_pool($idq,$ide);
                foreach($fils as $f) {
                    $fidq=$f->id_examen;
                    $fide=$f->id_etab;
                    purge_resultats_examen($fidq,$fide); // 4 tables
                    delete_records("resultats","examen='" . $fide . "." . $fidq . "'"  ); // et ajax
                    $critere=" id_examen=$fidq and id_etab=$fide";
                    delete_records("qcm",$critere,false);  //  inscriptions au membre
                    espion3("purge_inscrits","examen",$fide.".".$fidq,$f);
                }
            }
 		break;
		case 'sinp' :
    		$inscrits=get_inscrits($idq,$ide);
    		$critere=" id_examen=$idq and id_etab=$ide";
    		foreach ($inscrits as $i) {
            		if (compte_passages($idq, $ide, $i->login)==0) {
                		//pas de résultats normalement
                		purge_resultats_inscrit($idq,$ide,$i->login,true);
                		delete_records("qcm",$critere." and login='".addslashes($i->login)."' ",false);  //  pas fatal // slashes rev 984
                        // rev 978 ménage des anonymes ne l'ayant pas terminés
                        if (is_utilisateur_anonyme($i->login)) {
                            supprime_candidat($i->login);
            		    }
                    }
    		}
    		// nb si c'est un pool , les non inscrits ne sont pas dans les membres
    		//donc inutile d'aller y voir
    		espion3("purge_inscrits_non_passes","examen",$ide.".".$idq,$ligne);
    		break;


        default :erreur_fatale("err_action_invalide",$action,false);

	}
	//rafraichi liste des examens en dessous  et n'ajout pas psession pour retour bon onglet '
	redirect($url_retour."&refresh=1#admin",false,false,false);

}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //créer une instance

switch ($action) {
	case 'sim' :
		$action_titre="simuler_passage";
		$action_info=traduction ("info_simuler_passage",true,$nbInscrits-$nbPassages,$nbInscrits);
		break;
	case 'asim' :
		$action_titre="annuler_simuler_passage";
		$nbPassages= compte_simulations($idq,$ide);
		$action_info=traduction ("info_annuler_simuler_passage",true,$nbPassages,$nbInscrits);
		break;
	case 'pur' :
		$action_titre="purger_resultats";
		$action_info=traduction ("info_purger_resultats",true,$nbPassages,$nbInscrits);
		break;
	case 'si' :
		$action_titre="supprimer_inscrits";
		$action_info=traduction ("info_supprimer_inscrits",true,$nbInscrits);
		break;
	case 'sinp' :
		$action_titre="supprimer_inscrits_np";
		$action_info=traduction ("info_supprimer_inscrits_np",false,$nbInscritsNP,$nbInscrits);
		break;

    case 'verouille' :
            $action_titre="verouiller_examen";
            $action_info=traduction ("info_verouiller_examen",false);
            break;
    case 'deverouille' :
            $action_titre="deverouiller_examen";
            $action_info=traduction ("info_deverouiller_examen",false);
            break;
    case 'arc':
        break;

	default :erreur_fatale("err_action_invalide",$action,false);
}


$corps=<<<EOL
<form action="admin.php" method="post" name="monform" id="monform">
<input name="eid" type="hidden" value="$eid" />
<input name="action" type="hidden" value="$action" />
<input name="doit" type="hidden" value="1" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
<div class='information'>
{info_action_admin}

<br/>{bouton_confirmer}
</div>
</form>
EOL;


$tpl->assignInclude("corps", $corps,T_BYVAR);
$tpl->prepare($chemin);
$tpl->gotoBlock("_ROOT");

$tpl->assign("_ROOT.titre_popup",nom_complet_examen($ligne)."<br/>".traduction($action_titre));
$tpl->assign("_ROOT.info_action_admin",$action_info);



$tpl->print_boutons_fermeture($url_retour.'#admin');

print_bouton_confirmer($tpl);

$tpl->printToScreen(); //affichage
?>
