<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class questionRecord {
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
	public $certification;
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var string
	*/
	public $etat;
	/** 
	* @var int
	*/
	public $id;
	/** 
	* @var int
	*/
	public $id_etab;
	/** 
	* @var int
	*/
	public $id_famille_proposee;
	/** 
	* @var int
	*/
	public $id_famille_validee;
	/** 
	* @var string
	*/
	public $langue;
	/** 
	* @var string
	*/
	public $positionnement;
	/** 
	* @var string
	*/
	public $qid;
	/** 
	* @var string
	*/
	public $referentielc2i;
	/** 
	* @var string
	*/
	public $tags;
	/** 
	* @var string
	*/
	public $titre;
	/** 
	* @var int
	*/
	public $ts_datecreation;
	/** 
	* @var int
	*/
	public $ts_dateenvoi;
	/** 
	* @var int
	*/
	public $ts_datemodification;
	/** 
	* @var int
	*/
	public $ts_dateutilisation;

	/**
	* default constructor for class questionRecord
	* @param int $alinea
	* @param string $auteur
	* @param string $auteur_mail
	* @param string $certification
	* @param string $error
	* @param string $etat
	* @param int $id
	* @param int $id_etab
	* @param int $id_famille_proposee
	* @param int $id_famille_validee
	* @param string $langue
	* @param string $positionnement
	* @param string $qid
	* @param string $referentielc2i
	* @param string $tags
	* @param string $titre
	* @param int $ts_datecreation
	* @param int $ts_dateenvoi
	* @param int $ts_datemodification
	* @param int $ts_dateutilisation
	* @return questionRecord
	*/
	 public function questionRecord($alinea=0,$auteur='',$auteur_mail='',$certification='',$error='',$etat='',$id=0,$id_etab=0,$id_famille_proposee=0,$id_famille_validee=0,$langue='',$positionnement='',$qid='',$referentielc2i='',$tags='',$titre='',$ts_datecreation=0,$ts_dateenvoi=0,$ts_datemodification=0,$ts_dateutilisation=0){
		 $this->alinea=$alinea   ;
		 $this->auteur=$auteur   ;
		 $this->auteur_mail=$auteur_mail   ;
		 $this->certification=$certification   ;
		 $this->error=$error   ;
		 $this->etat=$etat   ;
		 $this->id=$id   ;
		 $this->id_etab=$id_etab   ;
		 $this->id_famille_proposee=$id_famille_proposee   ;
		 $this->id_famille_validee=$id_famille_validee   ;
		 $this->langue=$langue   ;
		 $this->positionnement=$positionnement   ;
		 $this->qid=$qid   ;
		 $this->referentielc2i=$referentielc2i   ;
		 $this->tags=$tags   ;
		 $this->titre=$titre   ;
		 $this->ts_datecreation=$ts_datecreation   ;
		 $this->ts_dateenvoi=$ts_dateenvoi   ;
		 $this->ts_datemodification=$ts_datemodification   ;
		 $this->ts_dateutilisation=$ts_dateutilisation   ;
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
	public function getCertification(){
		 return $this->certification;
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
	public function getEtat(){
		 return $this->etat;
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
	public function getId_etab(){
		 return $this->id_etab;
	}


	/**
	* @return int
	*/
	public function getId_famille_proposee(){
		 return $this->id_famille_proposee;
	}


	/**
	* @return int
	*/
	public function getId_famille_validee(){
		 return $this->id_famille_validee;
	}


	/**
	* @return string
	*/
	public function getLangue(){
		 return $this->langue;
	}


	/**
	* @return string
	*/
	public function getPositionnement(){
		 return $this->positionnement;
	}


	/**
	* @return string
	*/
	public function getQid(){
		 return $this->qid;
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
	* @return string
	*/
	public function getTitre(){
		 return $this->titre;
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
	public function getTs_dateenvoi(){
		 return $this->ts_dateenvoi;
	}


	/**
	* @return int
	*/
	public function getTs_datemodification(){
		 return $this->ts_datemodification;
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
	* @param string $certification
	* @return void
	*/
	public function setCertification($certification){
		$this->certification=$certification;
	}


	/**
	* @param string $error
	* @return void
	*/
	public function setError($error){
		$this->error=$error;
	}


	/**
	* @param string $etat
	* @return void
	*/
	public function setEtat($etat){
		$this->etat=$etat;
	}


	/**
	* @param int $id
	* @return void
	*/
	public function setId($id){
		$this->id=$id;
	}


	/**
	* @param int $id_etab
	* @return void
	*/
	public function setId_etab($id_etab){
		$this->id_etab=$id_etab;
	}


	/**
	* @param int $id_famille_proposee
	* @return void
	*/
	public function setId_famille_proposee($id_famille_proposee){
		$this->id_famille_proposee=$id_famille_proposee;
	}


	/**
	* @param int $id_famille_validee
	* @return void
	*/
	public function setId_famille_validee($id_famille_validee){
		$this->id_famille_validee=$id_famille_validee;
	}


	/**
	* @param string $langue
	* @return void
	*/
	public function setLangue($langue){
		$this->langue=$langue;
	}


	/**
	* @param string $positionnement
	* @return void
	*/
	public function setPositionnement($positionnement){
		$this->positionnement=$positionnement;
	}


	/**
	* @param string $qid
	* @return void
	*/
	public function setQid($qid){
		$this->qid=$qid;
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
	* @param string $titre
	* @return void
	*/
	public function setTitre($titre){
		$this->titre=$titre;
	}


	/**
	* @param int $ts_datecreation
	* @return void
	*/
	public function setTs_datecreation($ts_datecreation){
		$this->ts_datecreation=$ts_datecreation;
	}


	/**
	* @param int $ts_dateenvoi
	* @return void
	*/
	public function setTs_dateenvoi($ts_dateenvoi){
		$this->ts_dateenvoi=$ts_dateenvoi;
	}


	/**
	* @param int $ts_datemodification
	* @return void
	*/
	public function setTs_datemodification($ts_datemodification){
		$this->ts_datemodification=$ts_datemodification;
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
