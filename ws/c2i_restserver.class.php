<?php
require_once ('c2i_baseserver.class.php');

class c2i_restserver extends c2i_baseserver {

	private $formatout = 'php';
	private $singleshot = false;

	function __construct($formatout) {
		parent :: __construct();
		$this->setFormatout($formatout); // may raise an exception
	}

	/**
	 * Sends an fatal error response back to the client  and STOP the processing
	 *  @override server
	 * @param string $msg The error message to return.
	 * @return void
	 */
	protected function error($msg) {
		if ($this->singleshot)
			server :: logout($_REQUEST['client'], $_REQUEST['sesskey']);
		server :: error($msg); //log in error msg
		throw new Exception("Wspp Rest Server :" . $msg);
	}

	protected function send_headers() {
	    // faux avec la PF actuelle
	    global $CFG;
	    $encodage=$CFG->unicodedb? 'utf-8':'iso-8859-1';
	    
	    switch ($this->formatout) {
	        case 'xml' :
	            header("Content-Type: application/xml; charset=$encodage");
	            header('Content-Disposition: inline; filename="response.xml"');
	            break;
	        case 'dump' :
	            header("Content-Type: text/html; charset=$encodage");
	            header('Content-Disposition: inline; filename="response.txt"');
	            break;
	        default :
	            // what could be the good header for php, json or pojo ????
		    // header("Content-Type: application/php; charset=$encodage");
	            break;
	    }
	    parent :: send_headers();
	}



	 /**
     * Send the error information to the WS client
     * formatted as XML document.
     * to be overriden in descendant classes
     * @param exception $ex
     * @return void
     * TODO a more restful return
     */
	protected function send_error($ex=null) {
	    global $CFG;
	    $encodage=$CFG->unicodedb? 'utf-8':'iso-8859-1';
        $debuginfo='';
    	if ($ex) {
        	$info = $ex->getMessage();
        	if (1  and isset($ex->debuginfo)) {
            	$debuginfo = $ex->debuginfo;
        	}
    	} else {
        	$info = 'Unknown error';

    	}
    	$this->debug_output($info.' - '.$debuginfo);
    	switch ($this->formatout) {
        	case 'xml':
            	$xml = '<?xml version="1.0" encoding="'.$encodage.'" ?>'."\n";
            	$xml .= '<EXCEPTION class="'.get_class($ex).'">'."\n";
            	$xml .= '<MESSAGE>'.htmlspecialchars($info, ENT_COMPAT, $encodage).'</MESSAGE>'."\n";
            	if (isset($ex->debuginfo)) {
                	$xml .= '<DEBUGINFO>'.htmlspecialchars($debuginfo, ENT_COMPAT, $encodage).'</DEBUGINFO>'."\n";
            	}
            	$xml .= '</EXCEPTION>'."\n";
                break;
            case 'dump':
                $xml=$info.' - '.$debuginfo;
                break;
            case 'php' :
                 $obj=new StdClass();
                 $obj->error=$info;
                 $xml=serialize($obj);
                 break;  
            default :
                $xml='';$xml = $info . ' - ' . $debuginfo;

        }
        $this->send_headers();
        echo $xml;
        	die (); // needed
    	}

	/**
	  * to be overriden in others protocol specific classes
	  */
	protected function to_primitive($res) {
		return $res;
	}

	/**
	 * to be overriden in others protocol specific classes
	 */
	protected function to_single($res, $className) {
       // $this->debug_output("to_single ".print_r($res,true));
		return $res;
	}

	/**
	  * to be overriden in others protocol specific classes
	  *
	*/
	protected function to_array($res,$className, $emptyMsg) {
		 if (!$res || !is_array($res) || (count($res) == 0))
                return array($this->error_record($className, $emptyMsg));
         return $res;
	}

	/**
	* if Moodle has complained some way return content of ob_buffer
	* else pass the real result (from to_soap or to_soaparray ) to be sent in XML
	* must be called at every return to client
	*/

	protected function serialize($result) {
		switch ($this->formatout) {
			case 'php' :
				return serialize($result);
				break;
			case 'json' :
			    // ne fonctionne PAS avec des donn�es en iso
				return json_encode($result);
				break;
			case 'xml' :
				return '<RESPONSE>' . "\n" . $this->xmlize($result) . '</RESPONSE>' . "\n";
				break;
			case 'dump' :
				return '<pre>' . print_r($result, true) . '</pre>';
				break;
			case 'pojo' :
				return $this ->pojoize($result);
				break;
			default :
				return $this->error(traduction('ws_unknownoutputformat', false, $this->formatout));
				break;
		}
		return $result;
	}

	/**
	 * TODO
	 */
	protected function pojoize($result) {
		return $result;
	}

	/**
	 * very similar to Moodle 2.0 REST function webservice/rest/locallib.php@xmlize_result
	 * so we expected that parsing by existing Moodle 2.0 clients will be the same ...
	 */
	protected function xmlize($result) {
	    global $CFG;
	    $encodage=$CFG->unicodedb? 'utf-8':'iso-8859-1';

		if (is_array($result)) {
			$mult = '<MULTIPLE>' . "\n";
			if (!empty ($result)) {
				foreach ($result as $val) {
					$mult .= $this->xmlize($val);
				}
			}
			$mult .= '</MULTIPLE>' . "\n";
			return $mult;

		} else
			if (is_object($result)) {
				$single = '<SINGLE>' . "\n";
				foreach ($result as $key => $val) {

					$single .= '<KEY name="' . $key . '">' . $this->xmlize($val) . '</KEY>' . "\n";
				}
				$single .= '</SINGLE>' . "\n";
				return $single;

			} else
				if (is_scalar($result)) {
					return '<VALUE>' . htmlspecialchars($result, ENT_COMPAT, $encodage) . '</VALUE>' . "\n";

				} else {
					return '<VALUE null="null"/>' . "\n";
				}

	}

	/**
	 * @param string $wsfunction  the called operation name
	 * @uses $_REQUEST
	 */
	private function trysingleshot($wsfunction) {
		if ($wsfunction === 'login')
			return;
		if (!empty ($_REQUEST['wsusername']) && !empty ($_REQUEST['wspassword'])) {
			$lr = server :: login($_REQUEST['wsusername'], $_REQUEST['wspassword']);
			// if no exception sent
			unset ($_REQUEST['wsusername']);
			unset ($_REQUEST['wspassword']); // do not log them !!!
			//$this->debug_output("singleshot_login" . print_r($lr, true));
			$this->singleshot = true;
			//does not work order of elements are changed ..
			$_REQUEST['client'] = $lr->getClient();
			$_REQUEST['sesskey'] = $lr->getSessionKey();
		}
	}

	/**
	 * we must do some refection since there is no garantee that the expected parameters
	 * will be in the proper order in the global $_REQUEST
	 */
	public function handle($wsfunction) {
		try {
			$method = new ReflectionMethod($this, $wsfunction);
		} catch (ReflectionException $ex) {
		    ws_error_log($ex);
			$this->error(traduction('ws_unknownoperation', false, $wsfunction));
		}
		if (!$method->isPublic()) {
			$this->error(traduction('ws_unknownoperation', false, $wsfunction));
		}

		$this->trysingleshot($wsfunction);
		$expectedparameters = $method->getParameters();

		$params = array ();
		foreach ($expectedparameters as $expectedparameter) {
			$pname = $expectedparameter->getName();
			if (isset ($_REQUEST[$pname]))
				$params[] = $_REQUEST[$pname];
			else {
				if ($expectedparameter->isDefaultValueAvailable())
					$params[] = $expectedparameter->getDefaultValue(); // caution
				else
					$this->error(traduction('ws_valeurmanquante', false, $pname));
			}
		}
		//$this->debug_output("rest_input=" . print_r($params, true));
		$res = call_user_func_array(array (
			$this,
			$wsfunction
		), $params);
		//$this->debug_output("rest_output" . print_r($res, true));

		if ($this->singleshot) {
			$lr = server :: logout($_REQUEST['client'], $_REQUEST['sesskey']);
			$this->debug_output("singleshot_logout" . print_r($lr, true));
		}
		$this->send_headers(); // in REST mode we send headers even in case of no errors
		print $res;
		die();
	}

	public function setFormatout($formatout) {
		switch ($formatout) {
			case 'php' :
			case 'json' :
			case 'xml' :
			case 'dump' :
			case 'pojo' :
				$this->formatout = $formatout;
				break;
			default :
				return $this->error(traduction('ws_unknownoutputformat', false, $formatout));
				break;
		}
	}

}
?>
