<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class alineaRecord {
	/** 
	* @var int
	*/
	public $alinea;
	/** 
	* @var string
	*/
	public $aptitude;
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var string
	*/
	public $id;
	/** 
	* @var string
	*/
	public $referentielc2i;

	/**
	* default constructor for class alineaRecord
	* @return alineaRecord
	*/	 public function alineaRecord() {
		 $this->alinea=0;
		 $this->aptitude='';
		 $this->error='';
		 $this->id='';
		 $this->referentielc2i='';
	}
	/* get accessors */

	/**
	* @return int
	*/
	public function getAlinea(){
		 return $this->alinea;
	}


	/**
	* @return string
	*/
	public function getAptitude(){
		 return $this->aptitude;
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
	public function getId(){
		 return $this->id;
	}


	/**
	* @return string
	*/
	public function getReferentielc2i(){
		 return $this->referentielc2i;
	}

	/*set accessors */

	/**
	* @param int $alinea
	* @return void
	*/
	public function setAlinea($alinea){
		$this->alinea=$alinea;
	}


	/**
	* @param string $aptitude
	* @return void
	*/
	public function setAptitude($aptitude){
		$this->aptitude=$aptitude;
	}


	/**
	* @param string $error
	* @return void
	*/
	public function setError($error){
		$this->error=$error;
	}


	/**
	* @param string $id
	* @return void
	*/
	public function setId($id){
		$this->id=$id;
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
