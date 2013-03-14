<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class examenInputRecord {
	/** 
	* @var int
	*/
	public $affiche_chrono;
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
	public $correction;
	/** 
	* @var int
	*/
	public $envoi_resultat;
	/** 
	* @var string
	*/
	public $mot_de_passe;
	/** 
	* @var int
	*/
	public $nbquestions;
	/** 
	* @var string
	*/
	public $nom_examen;
	/** 
	* @var string
	*/
	public $ordre_q;
	/** 
	* @var string
	*/
	public $ordre_r;
	/** 
	* @var string
	*/
	public $positionnement;
	/** 
	* @var string
	*/
	public $referentielc2i;
	/** 
	* @var string
	*/
	public $resultat_mini;
	/** 
	* @var string
	*/
	public $tags;
	/** 
	* @var int
	*/
	public $ts_datedebut;
	/** 
	* @var int
	*/
	public $ts_datefin;
	/** 
	* @var int
	*/
	public $ts_dureelimitepassage;
	/** 
	* @var string
	*/
	public $type_tirage;
	/** 
	* @var int
	*/
	public $verouille;

	/**
	* default constructor for class examenInputRecord
	* @param int $affiche_chrono
	* @param string $auteur
	* @param string $auteur_mail
	* @param string $certification
	* @param string $correction
	* @param int $envoi_resultat
	* @param string $mot_de_passe
	* @param int $nbquestions
	* @param string $nom_examen
	* @param string $ordre_q
	* @param string $ordre_r
	* @param string $positionnement
	* @param string $referentielc2i
	* @param string $resultat_mini
	* @param string $tags
	* @param int $ts_datedebut
	* @param int $ts_datefin
	* @param int $ts_dureelimitepassage
	* @param string $type_tirage
	* @param int $verouille
	* @return examenInputRecord
	*/
	 public function examenInputRecord($affiche_chrono=0,$auteur='',$auteur_mail='',$certification='',$correction='',$envoi_resultat=0,$mot_de_passe='',$nbquestions=0,$nom_examen='',$ordre_q='',$ordre_r='',$positionnement='',$referentielc2i='',$resultat_mini='',$tags='',$ts_datedebut=0,$ts_datefin=0,$ts_dureelimitepassage=0,$type_tirage='',$verouille=0){
		 $this->affiche_chrono=$affiche_chrono   ;
		 $this->auteur=$auteur   ;
		 $this->auteur_mail=$auteur_mail   ;
		 $this->certification=$certification   ;
		 $this->correction=$correction   ;
		 $this->envoi_resultat=$envoi_resultat   ;
		 $this->mot_de_passe=$mot_de_passe   ;
		 $this->nbquestions=$nbquestions   ;
		 $this->nom_examen=$nom_examen   ;
		 $this->ordre_q=$ordre_q   ;
		 $this->ordre_r=$ordre_r   ;
		 $this->positionnement=$positionnement   ;
		 $this->referentielc2i=$referentielc2i   ;
		 $this->resultat_mini=$resultat_mini   ;
		 $this->tags=$tags   ;
		 $this->ts_datedebut=$ts_datedebut   ;
		 $this->ts_datefin=$ts_datefin   ;
		 $this->ts_dureelimitepassage=$ts_dureelimitepassage   ;
		 $this->type_tirage=$type_tirage   ;
		 $this->verouille=$verouille   ;
	}
	/* get accessors */

	/**
	* @return int
	*/
	public function getAffiche_chrono(){
		 return $this->affiche_chrono;
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
	public function getCorrection(){
		 return $this->correction;
	}


	/**
	* @return int
	*/
	public function getEnvoi_resultat(){
		 return $this->envoi_resultat;
	}


	/**
	* @return string
	*/
	public function getMot_de_passe(){
		 return $this->mot_de_passe;
	}


	/**
	* @return int
	*/
	public function getNbquestions(){
		 return $this->nbquestions;
	}


	/**
	* @return string
	*/
	public function getNom_examen(){
		 return $this->nom_examen;
	}


	/**
	* @return string
	*/
	public function getOrdre_q(){
		 return $this->ordre_q;
	}


	/**
	* @return string
	*/
	public function getOrdre_r(){
		 return $this->ordre_r;
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
	public function getReferentielc2i(){
		 return $this->referentielc2i;
	}


	/**
	* @return string
	*/
	public function getResultat_mini(){
		 return $this->resultat_mini;
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
	public function getTs_datedebut(){
		 return $this->ts_datedebut;
	}


	/**
	* @return int
	*/
	public function getTs_datefin(){
		 return $this->ts_datefin;
	}


	/**
	* @return int
	*/
	public function getTs_dureelimitepassage(){
		 return $this->ts_dureelimitepassage;
	}


	/**
	* @return string
	*/
	public function getType_tirage(){
		 return $this->type_tirage;
	}


	/**
	* @return int
	*/
	public function getVerouille(){
		 return $this->verouille;
	}

	/*set accessors */

	/**
	* @param int $affiche_chrono
	* @return void
	*/
	public function setAffiche_chrono($affiche_chrono){
		$this->affiche_chrono=$affiche_chrono;
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
	* @param string $correction
	* @return void
	*/
	public function setCorrection($correction){
		$this->correction=$correction;
	}


	/**
	* @param int $envoi_resultat
	* @return void
	*/
	public function setEnvoi_resultat($envoi_resultat){
		$this->envoi_resultat=$envoi_resultat;
	}


	/**
	* @param string $mot_de_passe
	* @return void
	*/
	public function setMot_de_passe($mot_de_passe){
		$this->mot_de_passe=$mot_de_passe;
	}


	/**
	* @param int $nbquestions
	* @return void
	*/
	public function setNbquestions($nbquestions){
		$this->nbquestions=$nbquestions;
	}


	/**
	* @param string $nom_examen
	* @return void
	*/
	public function setNom_examen($nom_examen){
		$this->nom_examen=$nom_examen;
	}


	/**
	* @param string $ordre_q
	* @return void
	*/
	public function setOrdre_q($ordre_q){
		$this->ordre_q=$ordre_q;
	}


	/**
	* @param string $ordre_r
	* @return void
	*/
	public function setOrdre_r($ordre_r){
		$this->ordre_r=$ordre_r;
	}


	/**
	* @param string $positionnement
	* @return void
	*/
	public function setPositionnement($positionnement){
		$this->positionnement=$positionnement;
	}


	/**
	* @param string $referentielc2i
	* @return void
	*/
	public function setReferentielc2i($referentielc2i){
		$this->referentielc2i=$referentielc2i;
	}


	/**
	* @param string $resultat_mini
	* @return void
	*/
	public function setResultat_mini($resultat_mini){
		$this->resultat_mini=$resultat_mini;
	}


	/**
	* @param string $tags
	* @return void
	*/
	public function setTags($tags){
		$this->tags=$tags;
	}


	/**
	* @param int $ts_datedebut
	* @return void
	*/
	public function setTs_datedebut($ts_datedebut){
		$this->ts_datedebut=$ts_datedebut;
	}


	/**
	* @param int $ts_datefin
	* @return void
	*/
	public function setTs_datefin($ts_datefin){
		$this->ts_datefin=$ts_datefin;
	}


	/**
	* @param int $ts_dureelimitepassage
	* @return void
	*/
	public function setTs_dureelimitepassage($ts_dureelimitepassage){
		$this->ts_dureelimitepassage=$ts_dureelimitepassage;
	}


	/**
	* @param string $type_tirage
	* @return void
	*/
	public function setType_tirage($type_tirage){
		$this->type_tirage=$type_tirage;
	}


	/**
	* @param int $verouille
	* @return void
	*/
	public function setVerouille($verouille){
		$this->verouille=$verouille;
	}

}

?>
