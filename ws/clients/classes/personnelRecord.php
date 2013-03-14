<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class personnelRecord {
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
	public $profils;
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
	* default constructor for class personnelRecord
	* @param string $auth
	* @param string $email
	* @param string $error
	* @param int $etablissement
	* @param int $id
	* @param string $login
	* @param string $nom
	* @param string $numetudiant
	* @param string $origine
	* @param string $prenom
	* @param string $profils
	* @param string $tags
	* @param int $ts_datecreation
	* @param int $ts_datemodification
	* @param int $ts_derniere_connexion
	* @return personnelRecord
	*/
	 public function personnelRecord($auth='',$email='',$error='',$etablissement=0,$id=0,$login='',$nom='',$numetudiant='',$origine='',$prenom='',$profils='',$tags='',$ts_datecreation=0,$ts_datemodification=0,$ts_derniere_connexion=0){
		 $this->auth=$auth   ;
		 $this->email=$email   ;
		 $this->error=$error   ;
		 $this->etablissement=$etablissement   ;
		 $this->id=$id   ;
		 $this->login=$login   ;
		 $this->nom=$nom   ;
		 $this->numetudiant=$numetudiant   ;
		 $this->origine=$origine   ;
		 $this->prenom=$prenom   ;
		 $this->profils=$profils   ;
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
	public function getProfils(){
		 return $this->profils;
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
	* @param string $profils
	* @return void
	*/
	public function setProfils($profils){
		$this->profils=$profils;
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
