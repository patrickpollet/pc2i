<?php

/**
 * @author Patrick Pollet
 * @version $Id: export_examen.php 1109 2010-07-21 11:36:47Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");                 //fichier de paramêtres


$eid = required_param("eid", PARAM_CLE_C2I);

require_login("P"); //PP

$ligne=get_examen_byidnat($eid);
$ide=$ligne->id_etab;
$idq=$ligne->id_examen;

if (! is_admin() || ! $CFG->autoriser_export)  // pas d'appel direct !
    erreur_fatale("err_droits");


$dir = $CFG->chemin_ressources.'/tmp/';
cree_dossier_si_absent($dir);
$filename='export_examen_'.$ide.'_'.$idq.'_'.time().'.txt';



$questions=get_questions($idq,$ide,false,false);
//tableau des ids des questions associées
$questionsmap=array();
foreach($questions as $question)
    $questionsmap []=$question->id_etab.'.'.$question->id;

//on ne peut pas l'exporter comme un membre car le pere n'est probablemnt pas déja exporté
if ($ligne->pool_pere) {
	$ligne->pool_pere=0;
	$ligne->type_tirage=EXAMEN_TIRAGE_ALEATOIRE;
}

//ajoute la liste des questions
$ligne->questions=$questionsmap;

$ex2=serialize($ligne);

//print_r(unserialize($ex2));

$fp = fopen($dir."/".$filename, "w");
fwrite($fp,$filename."\n");
fwrite($fp,$CFG->version."\n");

fwrite($fp,$ex2);
fclose($fp);

espion2("exportation","examen",$ide.".".$idq);

//TODO pas la peine de faire un fichier. Bricoler les entetes et imprimer ...
// envoi du fichier avec une entete mime adaptée et donc téléchargement
header("Location:".$CFG->chemin_commun."/send_csv.php?idf=".$filename."&dir=tmp");



?>
