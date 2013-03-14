<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class familleRecord {
	/** 
	* @var int
	*/
	public $alinea;
	/** 
	* @var string
	*/
	public $auteur;
	/** 
	* @var string
	*/
	public $auteur_mail;
	/** 
	* @var string
	*/
	public $commentaires;
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var string
	*/
	public $famille;
	/** 
	* @var int
	*/
	public $idf;
	/** 
	* @var string
	*/
	public $mots_clesf;
	/** 
	* @var string
	*/
	public $referentielc2i;
	/** 
	* @var string
	*/
	public $tags;
	/** 
	* @var int
	*/
	public $ts_datecreation;
	/** 
	* @var int
	*/
	public $ts_dateutilisation;

	/**
	* default constructor for class familleRecord
	* @return familleRecord
	*/	 public function familleRecord() {
		 $this->alinea=0;
		 $this->auteur='';
		 $this->auteur_mail='';
		 $this->commentaires='';
		 $this->error='';
		 $this->famille='';
		 $this->idf=0;
		 $this->mots_clesf='';
		 $this->referentielc2i='';
		 $this->tags='';
		 $this->ts_datecreation=0;
		 $this->ts_dateutilisation=0;
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
	public function getAuteur(){
		 return $this->auteur;
	}


	/**
	* @return string
	*/
	public function getAuteur_mail(){
		 return $this->auteur_mail;
	}


	/**
	* @return string
	*/
	public function getCommentaires(){
		 return $this->commentaires;
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
	public function getFamille(){
		 return $this->famille;
	}


	/**
	* @return int
	*/
	public function getIdf(){
		 return $this->idf;
	}


	/**
	* @return string
	*/
	public function getMots_clesf(){
		 return $this->mots_clesf;
	}


	/**
	* @return string
	*/
	public function getReferentielc2i(){
		 return $this->referentielc2i;
	}


	/**
	* @return string
	*/
	public function getTags(){
		 return $this->tags;
	}


	/**
	* @return int
	*/
	public function getTs_datecreation(){
		 return $this->ts_datecreation;
	}


	/**
	* @return int
	*/
	public function getTs_dateutilisation(){
		 return $this->ts_dateutilisation;
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
	* @param string $auteur
	* @return void
	*/
	public function setAuteur($auteur){
		$this->auteur=$auteur;
	}


	/**
	* @param string $auteur_mail
	* @return void
	*/
	public function setAuteur_mail($auteur_mail){
		$this->auteur_mail=$auteur_mail;
	}


	/**
	* @param string $commentaires
	* @return void
	*/
	public function setCommentaires($commentaires){
		$this->commentaires=$commentaires;
	}


	/**
	* @param string $error
	* @return void
	*/
	public function setError($error){
		$this->error=$error;
	}


	/**
	* @param string $famille
	* @return void
	*/
	public function setFamille($famille){
		$this->famille=$famille;
	}


	/**
	* @param int $idf
	* @return void
	*/
	public function setIdf($idf){
		$this->idf=$idf;
	}


	/**
	* @param string $mots_clesf
	* @return void
	*/
	public function setMots_clesf($mots_clesf){
		$this->mots_clesf=$mots_clesf;
	}


	/**
	* @param string $referentielc2i
	* @return void
	*/
	public function setReferentielc2i($referentielc2i){
		$this->referentielc2i=$referentielc2i;
	}


	/**
	* @param string $tags
	* @return void
	*/
	public function setTags($tags){
		$this->tags=$tags;
	}


	/**
	* @param int $ts_datecreation
	* @return void
	*/
	public function setTs_datecreation($ts_datecreation){
		$this->ts_datecreation=$ts_datecreation;
	}


	/**
	* @param int $ts_dateutilisation
	* @return void
	*/
	public function setTs_dateutilisation($ts_dateutilisation){
		$this->ts_dateutilisation=$ts_dateutilisation;
	}

}

?>
