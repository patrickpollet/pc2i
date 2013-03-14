<?php
// $Id: c2i_soapserver.class.php 1285 2012-01-09 10:53:25Z vbelleng $

/**
 * class for SOAP protocol-specific server layer. PHP 5 ONLY (may throw an exception !)
 *
 * @package Web Services
 * @version $Id: c2i_soapserver.class.php 1285 2012-01-09 10:53:25Z vbelleng $
 * @author Patrick Pollet <patrick.pollet@insa-lyon.fr> v 1.5
 */

require_once ('c2i_baseserver.class.php');

//    if (DEBUG)
ini_set('soap.wsdl_cache_enabled', 0); // no caching by php in debug mode)

class c2i_soapserver extends c2i_baseserver {

    /**
     * Constructor method.
     *
     * @param none
     * @return c2i_soapserver
     */
    function __construct() {
        parent :: __construct();

    }


     /* Send the error information to the WS client
     * formatted as XML document.
     * to be overriden in descendant classes
     * @param exception $ex
     * @return void
     */
    protected function send_error($ex=null) {
        global $CFG;
        if ($ex) {
            $info = $ex->getMessage();
            if (isset($ex->debuginfo)) {
                $info .= ' - '.$ex->debuginfo;
            }
        } else {
            $info = 'Unknown error';
        }
        $this->debug_output($info);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
<SOAP-ENV:Body><SOAP-ENV:Fault>
<faultcode>WSPP:error</faultcode>
<faultstring>'.$info.'</faultstring>
</SOAP-ENV:Fault></SOAP-ENV:Body></SOAP-ENV:Envelope>';

        $this->send_headers();
        header('Content-Type: application/xml');
        header('Content-Disposition: inline; filename="response.xml"');

        echo $xml;
        die(); // needed ???
    }




    protected function to_primitive($res) {
        return $res;
    }

    /**
    * return a SOAP ready array with filled in attributes from a Moodle object
    * or a blank array with attribute error set
    * @return string
    */
    protected function to_single($res, $className) {

        //Lille : in case to_single is made from to_array
        if (!is_array($res) && !is_object($res)) {
            $this->debug_output("LilleDebug  _mdl_soapserver.class/to_single_ =>  Not Object and not array");
            return $this->error_record($className, $res);
        }
        //end Lille
        // $this->debug_output( "TS0".print_r($res, true));
        // rev 978 enleve des notices si le resultat est un array, pas un object
        if (is_array($res))
            $res = (object) $res;
        if (!isset ($res->error) || !$res->error) { // rev 889 (enleve des notices)
            //in case server class missed some attributes ...
            $soap_res = $this->blank_array($className);
            foreach ($res as $key => $value)
                $soap_res[$key] = $value;
            $soap_res['error'] = '';
            //$this->debug_output( "TS".print_r($soap_res, true));
            return $soap_res;
        } else
            return $this->error_record($className, $res->error);
    }

    /**
    * Convert an array of objects returned by server class to the appropriate format
    * This function should be called for all data returned to client
    * in server's working directory . To generate it, use wsdl2php utility (or mkclasses.sh script)
    * @param string $errMsg the error message to be sent if  no results found.
    * Note that every returned object should have an error attribute set by server class in case
    * it is invalid
    * In case of "fatal errors" (invalid client, not enough rights ..., $res will contains only one
    *  record with error set.
    * In case of not "fatal errrors" (such as one course among a list of course is invalid...),
    *  all "good records" should have error attribute to blank and all bads should have error
    *  attribut set to the cause of the failure.
    * @param string $res
    * @param string $className. The PHP class of the returned  item(s). this class must exist
    * @return string
    */

    protected function to_array($res, $className, $emptyMsg) {
        $ret = array ();
        $this->debug_output( "TSA".print_r($res, true));
        if (!$res || !is_array($res) || (count($res) == 0))
            $ret[] = $this->error_record($className, $emptyMsg);
        else {
            foreach ($res as $r) {
                $ret[] = $this->to_single($r, $className);
            }
        }
        $this->debug_output( "TSA_FINAL".print_r($ret, true));

        return $ret;

    }

    /**
     * Sends an fatal error response back to the client.
     *
     * @param string $msg The error message to return.
     * @return string
     */
    protected function error($msg) {
        parent :: error($msg); //log in error msg
        throw new SoapFault("Server", $msg); // <-- TESTS php4
    }

}
?>
