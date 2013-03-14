<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class resultatDetailleInputRecord {
	/** 
	* @var int
	*/
	public $date;
	/** 
	* @var string
	*/
	public $login;
	/** 
	* @var string
	*/
	public $question;
	/** 
	* @var float
	*/
	public $score;

	/**
	* default constructor for class resultatDetailleInputRecord
	* @param int $date
	* @param string $login
	* @param string $question
	* @param float $score
	* @return resultatDetailleInputRecord
	*/
	 public function resultatDetailleInputRecord($date=0,$login='',$question='',$score=0.0){
		 $this->date=$date   ;
		 $this->login=$login   ;
		 $this->question=$question   ;
		 $this->score=$score   ;
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
	* @return string
	*/
	public function getQuestion(){
		 return $this->question;
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
	* @param string $question
	* @return void
	*/
	public function setQuestion($question){
		$this->question=$question;
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
