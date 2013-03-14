<?php

/**
 * @author  Patrick Pollet
 * @version $Id: export_referentiel_objectifs_moodle.php 1159 2010-09-25 23:03:57Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * rev 1001 adaptation a tout type de plateforme+ m�nage caract�res non ascii (saut de lignes...)
 * rev feb 2013 plus de conversion en utf8
 */
ob_start();

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");	//fichier de param�tres

require_login('P');

$filename=$CFG->c2i.'_referentiel_objectifs_'.time().'.csv';

$fp = fopen($CFG->chemin_ressources."/csv/".$filename, "w");

fputs($fp,"outcome_name;outcome_shortname;outcome_description;scale_name;scale_items;scale_description\n");

$scale_name=traduction ('bareme_c2i');
$scale_items=traduction ('non',false).','.traduction ('oui',false);
$scale_description=traduction ('description_bareme_c2i');

$alineas= get_alineas(); 
foreach ($alineas as $alinea) {
	$shortname=$alinea->referentielc2i.'.'.$alinea->alinea;
	fputs($fp,"\"".$shortname.' : '. clean($alinea->aptitude,0))."\";" ;
	fputs($fp,"\"".$shortname."\";");
	fputs($fp,"\"\";");
	
	fputs($fp,"\"$scale_name\";\"$scale_items\";\"$scale_description\"\n");
}

fclose($fp);
header("Location:".$CFG->chemin_commun."/send_csv.php?idf=".$filename);
?>