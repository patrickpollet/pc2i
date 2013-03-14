<?php // $Id: wsdl.php 1252 2011-05-23 10:20:26Z ppollet $
/**
 * This file creates a WSDL file for the web service interfaced running on
 * this server with URL paths relative to the currently running server.
 *
 * When referring to this file, you must call it as:
 *
 * http://www.yourhost.com/ ... /ws/wsdl.php
 *
 * Where ... is the path to your C2I root.  This is so that your web server
 * will process the PHP statemtents within the file, which returns a WSDL
 * file to the web services call (or your browser).
 *
 * @version $Id: wsdl.php 1252 2011-05-23 10:20:26Z ppollet $
 * @author Justin Filip <jfilip@oktech.ca>
 * @author Open Knowledge Technologies - http://www.oktech.ca/
 * @author PP
 *           removed the mdl_soapserver. )
 *           added extra API calls
 *           added plural when an array of whatever is required
 *           so defined get_xxx with ONE id and return one record
 *               and get_xxxs with array of id and  return array of record
 * when modifiying this file to add new API calls, run the provided
 * wsdl2php.php utility (or mkclasses.sh script) to generate uptodate
 * class names files (needed by PHP5 clients AND server) and C2IWS class
 * (needed only by PHP5 clients)

 PAS d'accents ou d'apostrophes SVP !!!
 */

ob_start();
$chemin = "..";
require_once ($chemin."/commun/c2i_params.php");
ob_clean();



header('Content-Type: application/xml; charset='.$CFG->encodage."'");
header('Content-Disposition: attachment; filename="c2i1.wsdl"');

//rev 1035  certains admins bloquent l'acces par URL a file_get_contents
//$wsdl=file_get_contents("$CFG->wwwroot/ws/c2iwsdl.xml");
//donc on passe par un acc�s fichier



// use Internet to fetch operations & types
// so as to be in sync with clients
if (empty ($CFG->ws_uselocalwsdl)) {
        $wsdl=file_get_contents($chemin."/ws/c2iwsdl.xml");
    $wsdl=str_replace('CFGWWWROOT',$CFG->wwwroot,$wsdl);

} else {
    //tests avec un wsdl gener� par la suite wshelper
    // et plac� dans chemin_ressources
        $wsdl = $CFG->chemin_ressources . '/ws/c2iws.wsdl';
     if (!file_exists($wsdl)) {
        $chemin = "..";
        cree_dossier_si_absent($CFG->chemin_ressources . "/ws");
            $data = file_get_contents($chemin ."/ws/c2iwsdl.xml");
        $data = str_replace('CFGWWWROOT', $CFG->wwwroot, $data);
        if ($fd = @ fopen($wsdl, 'wb')) {
            fwrite($fd, $data);
            fclose($fd);
        }
    }
    //lecture XML
    $wsdl=file_get_contents($wsdl);
}

// bug avec php 5.3.0 si taille >8192 bytes
// http://www.magentocommerce.com/boards/viewthread/56528/
// revision 1035 lenght �tait mal �crit (lenght) !!!

header ('Content-Length:'.strlen($wsdl));


echo $wsdl;
?>