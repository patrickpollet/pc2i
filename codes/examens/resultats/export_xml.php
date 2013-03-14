<?php

/**
 * @version $Id: export_xml.php 552 2009-03-27 10:05:48Z vbelleng $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


ob_start();


$chemin = '../../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");	//fichier de paramètres
require_once($chemin_commun."/lib_resultats.php");

require_login('P');
$idq=required_param("idq",PARAM_INT,"");
$ide=required_param("ide",PARAM_INT,"");
v_d_o_d("el");


$name=$CFG->c2i.'_resultats_'.$ide.'_'.$idq.'_'.time().'.xml';
header("content-type: text/xml");
header('Content-Disposition: attachment; filename="'.$name.'"');



function generate_xml_examen($idq, $ide) {
	$exam = get_examen($idq, $ide);
	if ($exam)
		$tmp = "<id-examen>".$exam->id_etab.".".$exam->id_examen."</id-examen>" .
			"<nom-examen>".$exam->nom_examen."</nom-examen>" .
			"<date-examen>".strftime(traduction("strftimedateshort"),$exam->ts_datedebut)."</date-examen>";
	else
		$tmp = "<id-examen>".$ide.".".$idq."</id-examen>" .
			"<nom-examen>Examen inconnu</nom-examen>" .
			"<date-examen>00/00/0000</date-examen>";

	return $tmp;
}

/**
 * rev 819 gestion du cas des pools
 *         modification structure balise candidat pour tenir compte examen réél
 */

function generate_xml_resultats($idq, $ide) {
	$tmp = "";

	$inscrits = get_inscrits($idq, $ide);
	foreach($inscrits as $inscrit) {
        //pas vrai pour un pool
		//if (compte_passages($idq, $ide, $inscrit->login) == 0) continue;

		$resultats = get_resultats($idq, $ide, $inscrit->login);
        //print_r($resultats);
		if ($resultats->score_global !=-1) {
			$tmp .= "<candidat>\n
				<login>$inscrit->login</login>\n
				<num-etudiant>$inscrit->numetudiant</num-etudiant>\n
                <examen>$resultats->examen</examen>\n
                <date>$resultats->ts_date_max</date>\n


				<score-global>$resultats->score_global</score-global>\n";

			foreach($resultats as $champs=>$value) {
				if (is_array($value) && $champs == "tabref_score"){
					foreach($value as $domaine=>$note) {
						$tmp .= "<domain name=\"$domaine\"><score>$note</score></domain>\n";
					}
				}
			}
			$tmp .= "</candidat>\n";
		}
	}
	return $tmp;
}



$xml  = "<?xml version=\"1.0\" encoding=\"$CFG->encodage\"?><C2i-1><score-examen-theorique>";
$xml .= generate_xml_examen($idq, $ide);
$xml .= generate_xml_resultats($idq, $ide)."</score-examen-theorique></C2i-1>";
while (@ob_end_clean());
echo $xml;

?>