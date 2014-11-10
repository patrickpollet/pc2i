<?php


/**
 * @author Patrick Pollet
 * @version $Id: archiver_examen.php 1082 2014-11-10 15:26:57Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


$chemin = '../../..';
$chemin_commun = $chemin . "/commun";


require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_once ($CFG->chemin . "/commun/lib_resultats.php");
require_once ($CFG->chemin . "/commun/lib_OOo.php");

$eid = required_param("eid", PARAM_CLE_C2I);

require_login("P"); //PP

$ligne = get_examen_byidnat($eid);

if (! teste_droit("em")) // pas d'appel direct !
    erreur_fatale("err_droits");

set_time_limit(0);


$filename = archiver_examen($ligne);

//TODO pas la peine de faire un fichier. Bricoler les entetes et imprimer ...
// envoi du fichier avec une entete mime adaptée et donc téléchargement forcé
header("Location:" . $CFG->chemin_commun . "/send_csv.php?idf=" . $filename . "&dir=archives");
?>
