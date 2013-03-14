<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class affectRecord {
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var string
	*/
	public $value;

	/**
	* default constructor for class affectRecord
	* @return affectRecord
	*/	 public function affectRecord() {
		 $this->error='';
		 $this->value='';
	}
	/* get accessors */

	/**
	* @return string
	*/
	public function getError(){
		 return $this->error;
	}


	/**
	* @return string
	*/
	public function getValue(){
		 return $this->value;
	}

	/*set accessors */

	/**
	* @param string $error
	* @return void
	*/
	public function setError($error){
		$this->error=$error;
	}


	/**
	* @param string $value
	* @return void
	*/
	public function setValue($value){
		$this->value=$value;
	}

}

?>
