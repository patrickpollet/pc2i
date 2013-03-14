<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class qcmRecord {
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var examenRecord
	*/
	public $examen;
	/** 
	* @var qcmItemRecord[]
	*/
	public $questions;

	/**
	* default constructor for class qcmRecord
	* @return qcmRecord
	*/	 public function qcmRecord() {
		 $this->error='';
		 $this->examen=NULL;
		 $this->questions=array();
	}
	/* get accessors */

	/**
	* @return string
	*/
	public function getError(){
		 return $this->error;
	}


	/**
	* @return examenRecord
	*/
	public function getExamen(){
		 return $this->examen;
	}


	/**
	* @return qcmItemRecord[]
	*/
	public function getQuestions(){
		 return $this->questions;
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
	* @param examenRecord $examen
	* @return void
	*/
	public function setExamen($examen){
		$this->examen=$examen;
	}


	/**
	* @param qcmItemRecord[] $questions
	* @return void
	*/
	public function setQuestions($questions){
		$this->questions=$questions;
	}

}

?>
