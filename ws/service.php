<?php


// $Id: service.php 1297 2012-05-07 08:37:34Z ppollet $

/**
 * PHP5 only SOAP server for Moodle
 * @package Web Services
 * @author Patrick Pollet <patrick.pollet@insa-lyon.fr>
 */

/*revisions
 1.5.1 :added a basic support for enumeration of functions (a la nusoap)
*/
set_time_limit(0);

//vire les evnetuelles notices & warnings 
ob_start();

// get Moodle site config infos
require_once ('../commun/c2i_params.php');

ob_end_clean();

$wsfunction = optional_param('wsfunction', '', PARAM_ALPHAEXT); // letters+underscore
$wsformatout = optional_param('wsformatout', '', PARAM_ALPHA);

//ws_error_log(print_r($_REQUEST,true));

/**
 *  log all DB errors specific to new Moodle 2.0 API
 */

 function ws_error_log ($ex) {
    global $CFG;
    if (is_object($ex)){
        $info=$ex->getMessage() . '\n' . $ex->getTraceAsString();
    }else $info=$ex;
    error_log ($info."\n",3,$CFG->chemin_ressources.'/wspp_db_errors.log' );
}


if ($wsfunction && $wsformatout) {
    // REST service
//     ws_error_log ("REST");
    unset ($_REQUEST['wsfunction']);
    unset ($_REQUEST['wsformatout']);
//   ws_error_log( "1");
    require_once ('c2i_restserver.class.php');
//   ws_error_log("2");
    $server = new c2i_restserver($wsformatout);
    $server->handle($wsfunction);

    die();

} else {
//    ws_error_log ("SOAP");

    // SOAP service class
    require ('c2i_soapserver.class.php');

    //$CFG->ws_uselocalwsdl=1;

    // use Internet to fetch operations & types
    // so as to be in sync with clients
    if (empty ($CFG->ws_uselocalwsdl)) {
        $wsdl = $CFG->wwwroot . "/ws/wsdl.php";
    } else {
        //some versions of PHP 5 have a problem reading 'big wsdls over the Internet'
        // but not from a 'locally copied' wsdl file
        // see http://bugs.php.net/bug.php?id=48216
        // so we created the appropriate wsdl file in moodle's data dir (in call to wsdl.php) and use it also here'
         $wsdl = $CFG->chemin_ressources . '/ws/c2iws.wsdl';

    }
    //print ($wsdl); die();

    $server = new SoapServer($wsdl, array (
        'encoding' => $CFG->encodage
    ));

    // all function of this class are calleable if cited in wsdl file
    $server->setClass("c2i_soapserver");

    if ($_SERVER["REQUEST_METHOD"] == "POST")
        $server->handle();
    /*************/
    else {
        echo "Ce serveur SOAP peut gerer les fonctions suivantes : ";
        $functions = $server->getFunctions();
        foreach ($functions as $func) {
            echo $func . "\n";
        }
    }

}
?>
