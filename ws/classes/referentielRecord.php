<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class referentielRecord {
	/** 
	* @var string
	*/
	public $domaine;
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var string
	*/
	public $referentielc2i;

	/**
	* default constructor for class referentielRecord
	* @return referentielRecord
	*/	 public function referentielRecord() {
		 $this->domaine='';
		 $this->error='';
		 $this->referentielc2i='';
	}
	/* get accessors */

	/**
	* @return string
	*/
	public function getDomaine(){
		 return $this->domaine;
	}


	/**
	* @return string
	*/
	public function getError(){
		 return $this->error;
	}


	/**
	* @return string
	*/
	public function getReferentielc2i(){
		 return $this->referentielc2i;
	}

	/*set accessors */

	/**
	* @param string $domaine
	* @return void
	*/
	public function setDomaine($domaine){
		$this->domaine=$domaine;
	}


	/**
	* @param string $error
	* @return void
	*/
	public function setError($error){
		$this->error=$error;
	}


	/**
	* @param string $referentielc2i
	* @return void
	*/
	public function setReferentielc2i($referentielc2i){
		$this->referentielc2i=$referentielc2i;
	}

}

?>
