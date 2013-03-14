<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class scoreRecord {
	/** 
	* @var string
	*/
	public $competence;
	/** 
	* @var float
	*/
	public $score;

	/**
	* default constructor for class scoreRecord
	* @return scoreRecord
	*/	 public function scoreRecord() {
		 $this->competence='';
		 $this->score=0.0;
	}
	/* get accessors */

	/**
	* @return string
	*/
	public function getCompetence(){
		 return $this->competence;
	}


	/**
	* @return float
	*/
	public function getScore(){
		 return $this->score;
	}

	/*set accessors */

	/**
	* @param string $competence
	* @return void
	*/
	public function setCompetence($competence){
		$this->competence=$competence;
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
