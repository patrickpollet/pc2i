<?php // $Id: service.php 857 2009-06-07 16:36:53Z ppollet $

/**
 * PHP5 only SOAP server for Moodle
 * @package Web Services
 * @author Patrick Pollet <patrick.pollet@insa-lyon.fr>
 */

 set_time_limit(0);

define ('LIB_PATH','clients/lib/');
require_once(LIB_PATH.'wshelper/WSDLStruct.class.php');
require_once(LIB_PATH.'wshelper/WSDLException.class.php');
require_once(LIB_PATH.'wshelper/WSException.class.php');
require_once(LIB_PATH.'wshelper/IPXMLSchema.class.php');
require_once(LIB_PATH.'wshelper/IPPhpDoc.class.php');
require_once(LIB_PATH.'wshelper/IPReflectionClass.class.php');
require_once(LIB_PATH.'wshelper/IPReflectionCommentParser.class.php');
require_once(LIB_PATH.'wshelper/IPReflectionMethod.class.php');
require_once(LIB_PATH.'wshelper/IPReflectionProperty.class.php');

require_once(LIB_PATH.'wshelper/IPReflectionParameter.class.php'); // added PP


// SOAP service class
require('c2i_soapserver.class.php');


  //$serviceNameSpace = $CFG->wwwroot.'/ws/wsdl';
  //$serviceURL = $CFG->wwwroot.'/ws/service.php';;

$serviceNameSpace = 'CFGWWWROOT/ws/wsdl';
$serviceURL = 'CFGWWWROOT/ws/service.php';

        $wsdl = new WSDLStruct($serviceNameSpace, $serviceURL, SOAP_RPC, SOAP_ENCODED);
        $wsdl->setService(new IPReflectionClass('c2i_soapserver'));
        $wsdl->_debug=true;

        $wsdl->setStrictErrorChecking(true);
        $wsdl->setFormatOutput(true);


        try {
            $gendoc = $wsdl->generateDocument();
            //print ($gendoc);
            $nb=file_put_contents('c2iwsdlv3.xml',$gendoc);
             echo "$nb bytes written\n";

        } catch (WSDLException $exception) {
            print ($exception->msg);
            print_r ($exception->getTrace());

        }


?>
