<?php


/**
 * @author Patrick Pollet
 * @version $Id: archiver_examen.php 1082 2010-05-14 15:26:57Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * rev 973
 */

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";

require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres
require_once ($chemin_commun . "/lib_resultats.php");

$eid = required_param("eid", PARAM_CLE_C2I);

require_login("P"); //PP

$ligne = get_examen_byidnat($eid);
$ide = $ligne->id_etab;
$idq = $ligne->id_examen;

if (! teste_droit("em")) // pas d'appel direct !
    erreur_fatale("err_droits");

set_time_limit(0);

$dir = $CFG->chemin_ressources . '/tmp/';
cree_dossier_si_absent($dir);

$dir .= time();
cree_dossier_si_absent($dir);

require_once ($CFG->chemin . "/commun/lib_OOo.php");

if ($ligne->est_pool == 1) { //pas le choix
    $groupes = liste_groupe_pool($idq, $ide);
    // TODO envoyer dans le CSV plus d'info en cas de pool ($ide, $idq ,$indice?)
    foreach ($groupes as $groupe) {
        $odf = examen_to_OOo($groupe->id_examen, $groupe->id_etab, QCM_PREVISUALISATION);
        $odf->saveToDisk($dir . "/examen_{$groupe->id_etab}_{$groupe->id_examen}.odt");

        $odf = examen_to_OOo($groupe->id_examen, $groupe->id_etab, QCM_CORRIGE);
        $odf->saveToDisk($dir . "/corrige_examen_{$groupe->id_etab}_{$groupe->id_examen}.odt");
    }
} else {
    $odf = examen_to_OOo($idq, $ide, QCM_PREVISUALISATION);
    $odf->saveToDisk($dir . "/examen_{$ide}_{$idq}.odt");

    $odf = examen_to_OOo($idq, $ide, QCM_CORRIGE);
    $odf->saveToDisk($dir . "/corrige_examen_{$ide}_{$idq}.odt");
}

$filename = liste_inscrits_to_OOo($idq, $ide, 0);
copy($filename, $dir . '/' . basename($filename));
unlink($filename);

$filename = liste_inscrits_to_OOo($idq, $ide, 1);
copy($filename, $dir . '/' . basename($filename));
unlink($filename);

list ($filename_ods, $filename_csv) = resultats_synthetiques_to_OOo($idq, $ide);
copy($filename_ods, $dir . '/' . basename($filename_ods));
unlink($filename_ods);
copy($filename_csv, $dir . '/' . basename($filename_csv));
unlink($filename_csv);

if ($ligne->est_pool == 1) { //pas le choix
    $groupes = liste_groupe_pool($idq, $ide);
    // TODO envoyer dans le CSV plus d'info en cas de pool ($ide, $idq ,$indice?)
    foreach ($groupes as $groupe) {
        list ($filename_ods, $filename_csv) = resultats_complets_to_OOo($groupe->id_examen, $groupe->id_etab);
        copy($filename_ods, $dir . '/' . basename($filename_ods));
        unlink($filename_ods);
        copy($filename_csv, $dir . '/' . basename($filename_csv));
        unlink($filename_csv);
    }
} else {
    list ($filename_ods, $filename_csv) = resultats_complets_to_OOo($idq, $ide);
    copy($filename_ods, $dir . '/' . basename($filename_ods));
    unlink($filename_ods);
    copy($filename_csv, $dir . '/' . basename($filename_csv));
    unlink($filename_csv);
}

// fichier zip final
$filename = "archive_{$ide}_{$idq}.zip";
//supprimer eventuel ancien
if (file_exists($CFG->chemin_ressources . '/archives/'.$filename))
    unlink ($CFG->chemin_ressources . '/archives/'.$filename);

zip_dossier($dir, '.', "../../archives/" . $filename);
supprimer_dossier($dir); // cleanup the temp directory

espion2("archivage", "examen", $ide . "." . $idq);

//TODO pas la peine de faire un fichier. Bricoler les entetes et imprimer ...
// envoi du fichier avec une entete mime adaptée et donc téléchargement
header("Location:" . $CFG->chemin_commun . "/send_csv.php?idf=" . $filename . "&dir=archives");
?>
