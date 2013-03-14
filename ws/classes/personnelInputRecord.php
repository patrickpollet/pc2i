<?php
/**
 * 
 * @package	MoodleWS
 * @copyright	(c) P.Pollet 2007 under GPL
 */
class personnelInputRecord {
	/** 
	* @var string
	*/
	public $auth;
	/** 
	* @var string
	*/
	public $email;
	/** 
	* @var int
	*/
	public $etablissement;
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
	public $password;
	/** 
	* @var string
	*/
	public $prenom;
	/** 
	* @var string
	*/
	public $tags;

	/**
	* default constructor for class personnelInputRecord
	* @return personnelInputRecord
	*/	 public function personnelInputRecord() {
		 $this->auth='';
		 $this->email='';
		 $this->etablissement=0;
		 $this->login='';
		 $this->nom='';
		 $this->numetudiant='';
		 $this->origine='';
		 $this->password='';
		 $this->prenom='';
		 $this->tags='';
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
	* @return int
	*/
	public function getEtablissement(){
		 return $this->etablissement;
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
	public function getPassword(){
		 return $this->password;
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
	* @param int $etablissement
	* @return void
	*/
	public function setEtablissement($etablissement){
		$this->etablissement=$etablissement;
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
	* @param string $password
	* @return void
	*/
	public function setPassword($password){
		$this->password=$password;
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

}

?>
