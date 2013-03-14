<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class resultatExamenInputRecord {
	/** 
	* @var int
	*/
	public $date;
	/** 
	* @var string
	*/
	public $login;
	/** 
	* @var float
	*/
	public $score;

	/**
	* default constructor for class resultatExamenInputRecord
	* @return resultatExamenInputRecord
	*/	 public function resultatExamenInputRecord() {
		 $this->date=0;
		 $this->login='';
		 $this->score=0.0;
	}
	/* get accessors */

	/**
	* @return int
	*/
	public function getDate(){
		 return $this->date;
	}


	/**
	* @return string
	*/
	public function getLogin(){
		 return $this->login;
	}


	/**
	* @return float
	*/
	public function getScore(){
		 return $this->score;
	}

	/*set accessors */

	/**
	* @param int $date
	* @return void
	*/
	public function setDate($date){
		$this->date=$date;
	}


	/**
	* @param string $login
	* @return void
	*/
	public function setLogin($login){
		$this->login=$login;
	}


	/**
	* @param float $score
	* @return void
	*/
	public function setScore($score){
		$this->score=$score;
	}

}

?>
