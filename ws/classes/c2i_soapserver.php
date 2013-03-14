<?php
/**
 * c2i_soapserver class file
 * 
 * @author    Patrick Pollet :<patrick.pollet@insa-lyon.fr>
 * @copyright (c) P.Pollet 2007 under GPL
 * @package   MoodleWS
 */

define('DEBUG',true);
if (DEBUG) ini_set('soap.wsdl_cache_enabled', '0');  // no caching by php in debug mode

/**
 * bilanDetailleRecord class
 */
require_once 'bilanDetailleRecord.php';
/**
 * scoreRecord class
 */
require_once 'scoreRecord.php';
/**
 * inscritInputRecord class
 */
require_once 'inscritInputRecord.php';
/**
 * inscritRecord class
 */
require_once 'inscritRecord.php';
/**
 * examenInputRecord class
 */
require_once 'examenInputRecord.php';
/**
 * examenRecord class
 */
require_once 'examenRecord.php';
/**
 * personnelInputRecord class
 */
require_once 'personnelInputRecord.php';
/**
 * personnelRecord class
 */
require_once 'personnelRecord.php';
/**
 * affectRecord class
 */
require_once 'affectRecord.php';
/**
 * resultatExamenInputRecord class
 */
require_once 'resultatExamenInputRecord.php';
/**
 * resultatDetailleInputRecord class
 */
require_once 'resultatDetailleInputRecord.php';
/**
 * qcmItemRecord class
 */
require_once 'qcmItemRecord.php';
/**
 * documentRecord class
 */
require_once 'documentRecord.php';
/**
 * questionRecord class
 */
require_once 'questionRecord.php';
/**
 * reponseRecord class
 */
require_once 'reponseRecord.php';
/**
 * alineaRecord class
 */
require_once 'alineaRecord.php';
/**
 * etablissementRecord class
 */
require_once 'etablissementRecord.php';
/**
 * qcmRecord class
 */
require_once 'qcmRecord.php';
/**
 * familleRecord class
 */
require_once 'familleRecord.php';
/**
 * noteRecord class
 */
require_once 'noteRecord.php';
/**
 * referentielRecord class
 */
require_once 'referentielRecord.php';
/**
 * loginReturn class
 */
require_once 'loginReturn.php';

?>
