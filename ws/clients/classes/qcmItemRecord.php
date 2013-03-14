<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class qcmItemRecord {
	/** 
	* @var documentRecord[]
	*/
	public $documents;
	/** 
	* @var questionRecord
	*/
	public $question;
	/** 
	* @var reponseRecord[]
	*/
	public $reponses;

	/**
	* default constructor for class qcmItemRecord
	* @param documentRecord[] $documents
	* @param questionRecord $question
	* @param reponseRecord[] $reponses
	* @return qcmItemRecord
	*/
	 public function qcmItemRecord($documents=array(),$question=NULL,$reponses=array()){
		 $this->documents=$documents   ;
		 $this->question=$question   ;
		 $this->reponses=$reponses   ;
	}
	/* get accessors */

	/**
	* @return documentRecord[]
	*/
	public function getDocuments(){
		 return $this->documents;
	}


	/**
	* @return questionRecord
	*/
	public function getQuestion(){
		 return $this->question;
	}


	/**
	* @return reponseRecord[]
	*/
	public function getReponses(){
		 return $this->reponses;
	}

	/*set accessors */

	/**
	* @param documentRecord[] $documents
	* @return void
	*/
	public function setDocuments($documents){
		$this->documents=$documents;
	}


	/**
	* @param questionRecord $question
	* @return void
	*/
	public function setQuestion($question){
		$this->question=$question;
	}


	/**
	* @param reponseRecord[] $reponses
	* @return void
	*/
	public function setReponses($reponses){
		$this->reponses=$reponses;
	}

}

?>
