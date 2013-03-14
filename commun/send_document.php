<?php

/**
 * @author Patrick Pollet
 * @version $Id: send_document.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


// send_document : envoie un document depuis le dossier ressources/questions/xx_xxx/documents
// en bricolant les entêtes
// ce fichier DOIT etre dans la zone ressources de la pf
// qui peut(doit) etre en dehors la zone accessible par le web
// a partir de la v 1.41


$chemin = '../';
$chemin_commun = $chemin."/commun";


define('NO_HEADERS',1); // pas d'entete par c2i_params !

/*
 * tres important
 * dans le cas ou des lignes blanches
 * ou des print trainent dans le code de la PF !!!!!
 */
ob_start();
define('V2', 1);
require_once ("$chemin/commun/c2i_params.php");

ob_clean();

 //rev 1013 ( v 967) l'anonyme a le type_user =A il venir aussi  ici
 //print_r($USER);die();

// controle retirÃ© juillet 2012 pour acces aux images par smartphones
//if (!is_utilisateur_anonyme())
//    require_login("E"); //PP


$idf=required_param("idf",PARAM_PATH);
$idq=required_param("idq",PARAM_INT);
$ide=required_param("ide",PARAM_INT);
$type=required_param("type",PARAM_ALPHA);  // doc ou image


//if ($type=='doc') {
	header("content-type: ".typeMime($idf)."\n");
	header("content-disposition: attachment; filename=$idf\n");
//}

if (isgoodfile($idf)) {
	//$idf=$CFG->chemin_ressources."/questions/".$ide."_".$idq."/documents/".$idf;  // V 1.41
	$idf=get_document_location($idq,$ide)."/".$idf;
	if (file_exists($idf)) {
		$handle = @fopen($idf, "r");
		if ($handle) {
   			while (!feof($handle)) {
     				$buffer = fgets($handle, 4096);
     				echo $buffer;
   			}
   			fclose($handle);
		}
	}
}

?>
