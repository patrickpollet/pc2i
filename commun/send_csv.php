<?php

/**
 * @author Patrick Pollet
 * @version $Id: send_csv.php 1019 2010-02-01 17:05:54Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


// send_csv : envoie un fichier csv depuis le dossier ressources/csv
// ou un autre dossier avec le parametre $dir (apogee, tmp ...)
// en bricolant les entêtes
// ce fichier DOIT etre dans la zone ressources de la pf
// qui peut(doit) etre en dehors la zone accessible par le web
// a partir de la v 1.41

$chemin = '../';
$chemin_commun = $chemin."/commun";
/*
 * tres important
 * dans le cas ou des lignes blanches
 * ou des print trainent dans le code de la PF !!!!!
 */

define('NO_HEADERS',1); // pas d'entete par c2i_params !
ob_start();

require_once ("$chemin/commun/c2i_params.php");

ob_clean();


require_login('P'); //PP
//v_d_o_d("ra");  // seulement si droits OK !

//print_r($_GET);
//die();

$idf=required_param("idf",PARAM_PATH);
$dir=optional_param("dir","csv",PARAM_PATH);
$ide=optional_param("ide","",PARAM_PATH);

if ($ide) $ide.="/";  //dossier de téléchargement d'un établissement

//print($idf);
//print typeMime($idf);
//print($dir);
//die();
header("content-type: ".typeMime($idf)."\n");
header("content-disposition: attachment; filename=$idf\n");


if (isgoodfile($idf)) {
	$idf=$CFG->chemin_ressources."/".$dir."/".$ide.$idf;  // V 1.41
	if (file_exists($idf)) {
		$handle = @fopen($idf, "r");
		if ($handle) {
   			while (!feof($handle)) {
     				$buffer = fgets($handle, 4096);
     				echo $buffer;
   			}
   			fclose($handle);
		} else echo "pb handle !!!";
	}else echo "$idf existe pas !!!";
} else echo "extension pas bonne $idf" ;

?>
