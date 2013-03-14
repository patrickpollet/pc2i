<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class inscritRecord {
	/** 
	* @var string
	*/
	public $auth;
	/** 
	* @var string
	*/
	public $email;
	/** 
	* @var string
	*/
	public $error;
	/** 
	* @var int
	*/
	public $etablissement;
	/** 
	* @var string
	*/
	public $examens;
	/** 
	* @var string
	*/
	public $genre;
	/** 
	* @var int
	*/
	public $id;
	/** 
	* @var string
	*/
	public $login;
	/** 
	* @var string
	*/
	public $nom;
	/** 
	* @var string
	*/
	public $numetudiant;
	/** 
	* @var string
	*/
	public $origine;
	/** 
	* @var string
	*/
	public $prenom;
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
	public $ts_datemodification;
	/** 
	* @var int
	*/
	public $ts_derniere_connexion;

	/**
	* default constructor for class inscritRecord
	* @param string $auth
	* @param string $email
	* @param string $error
	* @param int $etablissement
	* @param string $examens
	* @param string $genre
	* @param int $id
	* @param string $login
	* @param string $nom
	* @param string $numetudiant
	* @param string $origine
	* @param string $prenom
	* @param string $tags
	* @param int $ts_datecreation
	* @param int $ts_datemodification
	* @param int $ts_derniere_connexion
	* @return inscritRecord
	*/
	 public function inscritRecord($auth='',$email='',$error='',$etablissement=0,$examens='',$genre='',$id=0,$login='',$nom='',$numetudiant='',$origine='',$prenom='',$tags='',$ts_datecreation=0,$ts_datemodification=0,$ts_derniere_connexion=0){
		 $this->auth=$auth   ;
		 $this->email=$email   ;
		 $this->error=$error   ;
		 $this->etablissement=$etablissement   ;
		 $this->examens=$examens   ;
		 $this->genre=$genre   ;
		 $this->id=$id   ;
		 $this->login=$login   ;
		 $this->nom=$nom   ;
		 $this->numetudiant=$numetudiant   ;
		 $this->origine=$origine   ;
		 $this->prenom=$prenom   ;
		 $this->tags=$tags   ;
		 $this->ts_datecreation=$ts_datecreation   ;
		 $this->ts_datemodification=$ts_datemodification   ;
		 $this->ts_derniere_connexion=$ts_derniere_connexion   ;
	}
	/* get accessors */

	/**
	* @return string
	*/
	public function getAuth(){
		 return $this->auth;
	}


	/**
	* @return string
	*/
	public function getEmail(){
		 return $this->email;
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
	public function getEtablissement(){
		 return $this->etablissement;
	}


	/**
	* @return string
	*/
	public function getExamens(){
		 return $this->examens;
	}


	/**
	* @return string
	*/
	public function getGenre(){
		 return $this->genre;
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
	public function getLogin(){
		 return $this->login;
	}


	/**
	* @return string
	*/
	public function getNom(){
		 return $this->nom;
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
	* @return string
	*/
	public function getPrenom(){
		 return $this->prenom;
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
	public function getTs_datemodification(){
		 return $this->ts_datemodification;
	}


	/**
	* @return int
	*/
	public function getTs_derniere_connexion(){
		 return $this->ts_derniere_connexion;
	}

	/*set accessors */

	/**
	* @param string $auth
	* @return void
	*/
	public function setAuth($auth){
		$this->auth=$auth;
	}


	/**
	* @param string $email
	* @return void
	*/
	public function setEmail($email){
		$this->email=$email;
	}


	/**
	* @param string $error
	* @return void
	*/
	public function setError($error){
		$this->error=$error;
	}


	/**
	* @param int $etablissement
	* @return void
	*/
	public function setEtablissement($etablissement){
		$this->etablissement=$etablissement;
	}


	/**
	* @param string $examens
	* @return void
	*/
	public function setExamens($examens){
		$this->examens=$examens;
	}


	/**
	* @param string $genre
	* @return void
	*/
	public function setGenre($genre){
		$this->genre=$genre;
	}


	/**
	* @param int $id
	* @return void
	*/
	public function setId($id){
		$this->id=$id;
	}


	/**
	* @param string $login
	* @return void
	*/
	public function setLogin($login){
		$this->login=$login;
	}


	/**
	* @param string $nom
	* @return void
	*/
	public function setNom($nom){
		$this->nom=$nom;
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
	* @param string $prenom
	* @return void
	*/
	public function setPrenom($prenom){
		$this->prenom=$prenom;
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
	* @param int $ts_datemodification
	* @return void
	*/
	public function setTs_datemodification($ts_datemodification){
		$this->ts_datemodification=$ts_datemodification;
	}


	/**
	* @param int $ts_derniere_connexion
	* @return void
	*/
	public function setTs_derniere_connexion($ts_derniere_connexion){
		$this->ts_derniere_connexion=$ts_derniere_connexion;
	}

}

?>
