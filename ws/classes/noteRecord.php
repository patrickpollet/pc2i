<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class noteRecord {
	/** 
	* @var int
	*/
	public $date;
	/** 
	* @var string
	*/
	public $examen;
	/** 
	* @var string
	*/
	public $ip;
	/** 
	* @var string
	*/
	public $login;
	/** 
	* @var string
	*/
	public $numetudiant;
	/** 
	* @var string
	*/
	public $origine;
	/** 
	* @var float
	*/
	public $score;

	/**
	* default constructor for class noteRecord
	* @return noteRecord
	*/	 public function noteRecord() {
		 $this->date=0;
		 $this->examen='';
		 $this->ip='';
		 $this->login='';
		 $this->numetudiant='';
		 $this->origine='';
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
	public function getExamen(){
		 return $this->examen;
	}


	/**
	* @return string
	*/
	public function getIp(){
		 return $this->ip;
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
	public function getNumetudiant(){
		 return $this->numetudiant;
	}


	/**
	* @return string
	*/
	public function getOrigine(){
		 return $this->origine;
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
	* @param string $examen
	* @return void
	*/
	public function setExamen($examen){
		$this->examen=$examen;
	}


	/**
	* @param string $ip
	* @return void
	*/
	public function setIp($ip){
		$this->ip=$ip;
	}


	/**
	* @param string $login
	* @return void
	*/
	public function setLogin($login){
		$this->login=$login;
	}


	/**
	* @param string $numetudiant
	* @return void
	*/
	public function setNumetudiant($numetudiant){
		$this->numetudiant=$numetudiant;
	}


	/**
	* @param string $origine
	* @return void
	*/
	public function setOrigine($origine){
		$this->origine=$origine;
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
