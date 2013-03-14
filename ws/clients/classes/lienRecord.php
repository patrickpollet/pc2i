<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class lienRecord {
	/** 
	* @var string
	*/
	public $URL;
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var int
	*/
	public $id;
	/** 
	* @var int
	*/
	public $id_notion;
	/** 
	* @var string
	*/
	public $origine;

	/**
	* default constructor for class lienRecord
	* @param string $URL
	* @param string $error
	* @param int $id
	* @param int $id_notion
	* @param string $origine
	* @return lienRecord
	*/
	 public function lienRecord($URL='',$error='',$id=0,$id_notion=0,$origine=''){
		 $this->URL=$URL   ;
		 $this->error=$error   ;
		 $this->id=$id   ;
		 $this->id_notion=$id_notion   ;
		 $this->origine=$origine   ;
	}
	/* get accessors */

	/**
	* @return string
	*/
	public function getURL(){
		 return $this->URL;
	}


	/**
	* @return string
	*/
	public function getError(){
		 return $this->error;
	}


	/**
	* @return int
	*/
	public function getId(){
		 return $this->id;
	}


	/**
	* @return int
	*/
	public function getId_notion(){
		 return $this->id_notion;
	}


	/**
	* @return string
	*/
	public function getOrigine(){
		 return $this->origine;
	}

	/*set accessors */

	/**
	* @param string $URL
	* @return void
	*/
	public function setURL($URL){
		$this->URL=$URL;
	}


	/**
	* @param string $error
	* @return void
	*/
	public function setError($error){
		$this->error=$error;
	}


	/**
	* @param int $id
	* @return void
	*/
	public function setId($id){
		$this->id=$id;
	}


	/**
	* @param int $id_notion
	* @return void
	*/
	public function setId_notion($id_notion){
		$this->id_notion=$id_notion;
	}


	/**
	* @param string $origine
	* @return void
	*/
	public function setOrigine($origine){
		$this->origine=$origine;
	}

}

?>
