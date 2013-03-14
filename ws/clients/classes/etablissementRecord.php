<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class etablissementRecord {
	/** 
	* @var int
	*/
	public $certification;
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var int
	*/
	public $id_etab;
	/** 
	* @var int
	*/
	public $locale;
	/** 
	* @var int
	*/
	public $nationale;
	/** 
	* @var string
	*/
	public $nom_etab;
	/** 
	* @var int
	*/
	public $pere;
	/** 
	* @var int
	*/
	public $positionnement;

	/**
	* default constructor for class etablissementRecord
	* @param int $certification
	* @param string $error
	* @param int $id_etab
	* @param int $locale
	* @param int $nationale
	* @param string $nom_etab
	* @param int $pere
	* @param int $positionnement
	* @return etablissementRecord
	*/
	 public function etablissementRecord($certification=0,$error='',$id_etab=0,$locale=0,$nationale=0,$nom_etab='',$pere=0,$positionnement=0){
		 $this->certification=$certification   ;
		 $this->error=$error   ;
		 $this->id_etab=$id_etab   ;
		 $this->locale=$locale   ;
		 $this->nationale=$nationale   ;
		 $this->nom_etab=$nom_etab   ;
		 $this->pere=$pere   ;
		 $this->positionnement=$positionnement   ;
	}
	/* get accessors */

	/**
	* @return int
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
	* @return int
	*/
	public function getId_etab(){
		 return $this->id_etab;
	}


	/**
	* @return int
	*/
	public function getLocale(){
		 return $this->locale;
	}


	/**
	* @return int
	*/
	public function getNationale(){
		 return $this->nationale;
	}


	/**
	* @return string
	*/
	public function getNom_etab(){
		 return $this->nom_etab;
	}


	/**
	* @return int
	*/
	public function getPere(){
		 return $this->pere;
	}


	/**
	* @return int
	*/
	public function getPositionnement(){
		 return $this->positionnement;
	}

	/*set accessors */

	/**
	* @param int $certification
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
	* @param int $id_etab
	* @return void
	*/
	public function setId_etab($id_etab){
		$this->id_etab=$id_etab;
	}


	/**
	* @param int $locale
	* @return void
	*/
	public function setLocale($locale){
		$this->locale=$locale;
	}


	/**
	* @param int $nationale
	* @return void
	*/
	public function setNationale($nationale){
		$this->nationale=$nationale;
	}


	/**
	* @param string $nom_etab
	* @return void
	*/
	public function setNom_etab($nom_etab){
		$this->nom_etab=$nom_etab;
	}


	/**
	* @param int $pere
	* @return void
	*/
	public function setPere($pere){
		$this->pere=$pere;
	}


	/**
	* @param int $positionnement
	* @return void
	*/
	public function setPositionnement($positionnement){
		$this->positionnement=$positionnement;
	}

}

?>
