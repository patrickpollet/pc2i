<?php


/**
 * @author Patrick Pollet
 * @version $Id: verifier_version.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 *

/**
* rev 1.4 PP
* script devant être appelé en asynchrone par Ajax pour ne pas bloquer la plateforme en cas d'erreur réseau
* voir templates/accueil.html
*/
// rev 986 pb avec php5.3 et HTML_tree 
//Deprecated: Assigning the return value of new by reference is deprecated in lib_config
error_reporting(0);
//echo error_reporting();
$chemin="../";
require_once("$chemin/commun/constantes.php");
require_once("$chemin/commun/fonctions_divers.php");
//rev 906 important pour l'option  utiliser_curl !!!
require_once("$chemin/commun/lib_bd.php");
require_once("$chemin/commun/lib_config.php");
//et surtout pas c2i_params

print verifier_version_pf();

?>
