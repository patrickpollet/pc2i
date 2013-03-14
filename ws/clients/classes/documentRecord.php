<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class documentRecord {
	/** 
	* @var string
	*/
	public $base64;
	/** 
	* @var string
	*/
	public $description;
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var string
	*/
	public $extension;
	/** 
	* @var int
	*/
	public $id;
	/** 
	* @var string
	*/
	public $id_doc;
	/** 
	* @var int
	*/
	public $id_etab;
	/** 
	* @var string
	*/
	public $qid;

	/**
	* default constructor for class documentRecord
	* @param string $base64
	* @param string $description
	* @param string $error
	* @param string $extension
	* @param int $id
	* @param string $id_doc
	* @param int $id_etab
	* @param string $qid
	* @return documentRecord
	*/
	 public function documentRecord($base64='',$description='',$error='',$extension='',$id=0,$id_doc='',$id_etab=0,$qid=''){
		 $this->base64=$base64   ;
		 $this->description=$description   ;
		 $this->error=$error   ;
		 $this->extension=$extension   ;
		 $this->id=$id   ;
		 $this->id_doc=$id_doc   ;
		 $this->id_etab=$id_etab   ;
		 $this->qid=$qid   ;
	}
	/* get accessors */

	/**
	* @return string
	*/
	public function getBase64(){
		 return $this->base64;
	}


	/**
	* @return string
	*/
	public function getDescription(){
		 return $this->description;
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
	public function getExtension(){
		 return $this->extension;
	}


	/**
	* @return int
	*/
	public function getId(){
		 return $this->id;
	}


	/**
	* @return string
	*/
	public function getId_doc(){
		 return $this->id_doc;
	}


	/**
	* @return int
	*/
	public function getId_etab(){
		 return $this->id_etab;
	}


	/**
	* @return string
	*/
	public function getQid(){
		 return $this->qid;
	}

	/*set accessors */

	/**
	* @param string $base64
	* @return void
	*/
	public function setBase64($base64){
		$this->base64=$base64;
	}


	/**
	* @param string $description
	* @return void
	*/
	public function setDescription($description){
		$this->description=$description;
	}


	/**
	* @param string $error
	* @return void
	*/
	public function setError($error){
		$this->error=$error;
	}


	/**
	* @param string $extension
	* @return void
	*/
	public function setExtension($extension){
		$this->extension=$extension;
	}


	/**
	* @param int $id
	* @return void
	*/
	public function setId($id){
		$this->id=$id;
	}


	/**
	* @param string $id_doc
	* @return void
	*/
	public function setId_doc($id_doc){
		$this->id_doc=$id_doc;
	}


	/**
	* @param int $id_etab
	* @return void
	*/
	public function setId_etab($id_etab){
		$this->id_etab=$id_etab;
	}


	/**
	* @param string $qid
	* @return void
	*/
	public function setQid($qid){
		$this->qid=$qid;
	}

}

?>
