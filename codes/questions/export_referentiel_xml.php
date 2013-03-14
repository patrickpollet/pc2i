<?php

/**
 * @author Vincent Bellenger
 * @version $Id: export_referentiel_xml.php 1198 2011-01-26 17:09:46Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * rev 1001 adaptation a tout type de plateforme+ ménage caractères non ascii (saut de lignes...)
 */
ob_start();

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");	//fichier de paramètres

require_login('P');



$name=$CFG->c2i.'_referentiel_emaeval_'.time().'.xml';

header("content-type: text/xml");
header('Content-Disposition: attachment; filename="'.$name.'"');

function generate_xml_referentiel() {
	$tmp = "";
	$referentiel = get_referentiels();
	foreach($referentiel as $domaine) {
		$alineas = get_alineas($domaine->referentielc2i);


		$tmp .= "<domain name=\"".clean($domaine->referentielc2i,0)."\">\n" .
			"<description>".clean($domaine->domaine,0)."</description>\n";
		$alineas = get_alineas($domaine->referentielc2i);

		foreach($alineas as $champs=>$value) {
			$tmp .= "<competence name=\"$value->alinea\">
				<description>".clean($value->aptitude,0)."</description>
				</competence>\n";
		}
		$tmp .= "</domain>\n";
	}

	return $tmp;
}


$description=traduction('description_referentiel');
$xml  = "<?xml version=\"1.0\" encoding=\"$CFG->encodage\"?><referentiels>
		    <referentiel code=\"".$CFG->c2i."\" "."name=\"".traduction('nom_referentiel')."\">
<description>$description</description>".generate_xml_referentiel()."</referentiel></referentiels>";
while (@ob_end_clean());

echo $xml;

?>