<?php
/**
 * An extended reflection/documentation class method parameters
 *
 * This class extends the reflectionParameter class by also parsing the
 * comment for javadoc compatible @tags and by providing help
 * functions to generate a WSDL file. The class might also
 * be used to generate a phpdoc on the fly
 *
 * @version 0.1
 * @author Patrick Pollet
 * @extends reflectionParameter
 */

class IPReflectionParameter extends reflectionParameter{
	
	/** @var string Type description of the property */
	public $type = "";

	/** @var boolean */
	public $optional = false;
	
	/** @var string */
	public $comment = null;
	
	/** @var string */
	public $defaultvalue = null;
	


/*
http://www.php.net/manual/fr/class.reflectionparameter.php
Signature of constructor of ReflectionParameter correctly is: 
public function __construct(array/string $function, string $name); 
where $function is either a name of a global function, or a class/method name pair.
*/
    public function __construct( $function ,$parameter) {
    	parent::__construct($function ,$parameter);
    	if ($this->isDefaultValueAvailable()) { 
    		$this->defaultvalue=$this->getDefaultValue();
    		$this->optional=true;
    	}	
    }
}
?>