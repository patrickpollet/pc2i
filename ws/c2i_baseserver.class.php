<?php // $Id: c2i_soapserver.class.php 1198 2011-01-26 17:09:46Z ppollet $

/**
 * class for SOAP protocol-specific server layer. PHP 5 ONLY (may throw an exception !)
 *
 * @package Web Services
 * @version $Id: c2i_soapserver.class.php 1198 2011-01-26 17:09:46Z ppollet $
 * @author Patrick Pollet <patrick.pollet@insa-lyon.fr> v 1.5
 */

/* rev history
*  1.5.4: made  the API calls to try the wshelper utility (www.jool.nl/webservicehelper/)
*/

    // base class that performs data extraction/injection
    require_once('server.class.php');
    // list of required classes for input/output data
    require_once('classes/c2i_soapserver.php');
   // define('DEBUG',true);


    abstract class c2i_baseserver extends server {

    /**
     * Constructor method.
     *
     * @param none
     * @return c2i_baseserver
     */
        function __construct() {
          global $CFG;

       // rev 1.7 use an xception handler to catch all errors sent by Moodle 2.0
        // with Moodle 1.9 we use our function error() that throw a soap exception
        // that is also catched here
        set_exception_handler(array($this,'exception_handler'));

		/// Necessary for processing any DB upgrades.
		parent :: __construct();

		$this->debug_output('    Version: ' . $this->version);
		$this->debug_output('    Session Timeout: ' . $this->sessiontimeout);


		ob_start(); //rev 1.6 buffer all Moodle ouptuts see send function

        }


     /* specific exception handling for Moodle 2.0
     * code borrowed from Moodle's webservice '
     * changed to protected for WsHelper utility that must skip it...
     * BACK to public otherwise not called !!!
     * we get  AFatal error  Call to protected method mdl_soapserver::exception_handler() from context '' in <b>Unknown</b> on line <b>0</b><br />
     * @return void
     */
    function  exception_handler($ex) {
        // now let the plugin send the exception to client
        $this->send_error($ex);
        // not much else we can do now, add some logging later
        exit(1);
    }

         /**
     * Send the error information to the WS client
     * formatted as XML document.
     * to be overriden in descendant classes
     * @param exception $ex
     * @return void
     */
    protected abstract function send_error($ex=null) ;


     /**
     * Internal implementation - sending of page headers.
     * protocol classes should override it and add a content-type
     * @return void
     */
    protected function send_headers() {
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
    }

    /**
	 * if Moodle has complained some way return content of ob_buffer
	 * else pass the real result (from to_single or to_singlearray ) to be sent in XML
     * must be called at every return to client
     * @return string
	 */

	protected function send($result) {
		if (ob_get_length() && trim(ob_get_contents())) {
			/// Return an error with  the contents of the output buffer.
           //$this->debug_output( "SEND".print_r($result, true));
			$msg = trim(ob_get_contents());
			ob_end_clean();
			return $this->error($msg);
		}
		//ob_end_clean();
        //$this->debug_output( "SEND".print_r($result, true));
		return $this->serialize($result);
	}

       protected function serialize ($result) {
        return $result;
    }


	/** since SOAP requires all attributes fields to be filled, even in case of error
	* this function return a "blank array", with error attribute set to $errMsg
	* className is the name of the returned class built with our wsdl2php utility against our WSDL file
    * @param string $classname
    * @return string
    */

	protected function blank_array($className){
		if (class_exists($className)) {
			$res= new $className();
			return get_object_vars($res);// convert to array
		} else
			// throw a fatal exception SoapFault
			$this->error("internal error :class $className not found !");
	}

/**
 * @return string
 */

     protected function error_record ($className,$errMsg) {
		$res= $this->blank_array($className);
		$res['error']=$errMsg;
        //$this->debug_output( "ER".print_r($res, true));
		return $res;
	}

     /* to be overriden in others protocol specific classes
       */
      abstract protected function to_primitive($res);


      /**
       * to be overriden in others protocol specific classes
       */
      abstract protected function to_single($res, $className);


     /**
       * to be overriden in others protocol specific classes
     */
     abstract protected function to_array($res, $className, $emptyMsg);



    /**
     * Sends an fatal error response back to the client.
     *
     * @param string $msg The error message to return.
     * @return string
     */
      protected function error($msg) {
  	    parent::error($msg); //log in error msg
        throw new Exception ($msg); // <-- TESTS php4
        }

    /**
     * Validates a client's login request.
     * @param string $username
     * @param string password
     * @return loginReturn
     */
    public function login($username, $password) {
        return $this->send($this->to_single(
                        parent::login($username, $password),
            'loginReturn'));
    }



    /**
     * Logs a client out of the system by removing the valid flag from their
     * session record and any user ID that is assosciated with their particular
     * session.
     *
     * @param int $client The client record ID.
     * @param string $sesskey The client session key.
     * @return boolean True if successfully logged out, false otherwise.
     */
    public function logout($client, $sesskey) {
        return $this->send($this->to_primitive(parent::logout($client,$sesskey)));
    }

   // requetes a r�ponse unique

/**
 * @param int $client
 * @param string $sesskey
 * @param string $userid
 * @param string $idfield
 * @return inscritRecord
 */
     public  function get_inscrit($client, $sesskey, $userid, $idfield = 'numetudiant') {
		return $this->send($this->to_single(
                        parent::get_inscrit($client, $sesskey,$userid,$idfield),
			'inscritRecord'));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $id
 * @return etablissementRecord
 */
	public function get_etablissement($client, $sesskey, $id) {
		return $this->send( $this->to_single(
			parent::get_etablissement($client, $sesskey,$id),
			'etablissementRecord'));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $userid
 * @param string $idfield
 * @return personnelRecord
 */
     public function get_personnel($client, $sesskey, $userid, $idfield = 'login') {
		return $this->send($this->to_single(
			parent::get_personnel($client, $sesskey,$userid,$idfield),
			'personnelRecord'));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $id
 * @return questionRecord
 */
      public function get_question($client, $sesskey, $id) {
		 return $this->send($this->to_single(
			parent::get_question($client, $sesskey,$id),
			'questionRecord'));
	}

/**
 * @param int $client
 * @param string $sesskey
 * @param string $id
 * @return examenRecord
 */
      public function get_examen($client, $sesskey, $id) {
		return  $this->send($this->to_single(
			parent::get_examen($client, $sesskey,$id),
			'examenRecord'));
        }


/**
 * @param int $client
 * @param string $sesskey
 * @param string $email
 * @return qcmRecord
 */
      public function get_examen_anonyme($client, $sesskey,$email='') {
        list ($idq, $ide) = get_examen_anonyme();
        if (!isset ($idq)) {
            return $this->error ("pas d'examen anonyme");
        }
        if ($err=valide_acces_anonyme($email)) {
            return $this->error(traduction($err));
        }
        return  $this->send($this->to_single(
            parent::get_qcm($client, $sesskey,$ide.'_'.$idq),
            'qcmRecord'));
      }


/**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @return qcmRecord
 */
    public function get_qcm($client, $sesskey, $id_examen) {
        return  $this->send($this->to_single(
            parent::get_qcm($client, $sesskey,$id_examen),
            'qcmRecord'));

        }

/**
 * @param int $client
 * @param string $sesskey
 * @param examenInputRecord $examen
 * @param int $id_etab (optional)
 * @return examenRecord
 */
 	public function cree_examen ($client,$sesskey,$examen,$id_etab='') {
        return  $this->send($this->to_single(
            parent::cree_examen($client, $sesskey,$examen,$id_etab),
            'examenRecord'));
        }


/**
 * @param int $client
 * @param string $sesskey
 * @param string $idexamen
 * @param examenInputRecord $examen
 * @return examenRecord
 */
    public function modifie_examen ($client,$sesskey,$idexamen,$examen) {
        return  $this->send($this->to_single(
            parent::modifie_examen($client, $sesskey,$idexamen,$examen),
            'examenRecord'));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param inscritInputRecord $candidat
 * @return inscritRecord
 */
     public function cree_candidat ($client,$sesskey,$candidat) {
        return $this->send($this->to_single(
            parent::cree_candidat($client, $sesskey,$candidat),
            'inscritRecord'));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param personnelInputRecord $personnel
 * @return personnelRecord
 */
	public function cree_personnel ($client,$sesskey,$personnel) {
        return  $this->send($this->to_single(
            parent::cree_personnel($client, $sesskey,$personnel),
            'personnelRecord'));
        }

 /**
 * @param int $client
 * @param string $sesskey
 * @param string $userid
 * @param string $idfield
 * @param string $id_examen
 * @return string
 */
    public function get_corrige_examen_html($client,$sesskey,$userid,$idfield,$id_examen) {
    	return $this->send($this->to_primitive( //simple chaine HTML en CDATA
            parent::get_corrige_examen_html($client, $sesskey,$userid,$idfield,$id_examen)));

        }

 /**
 * @param int $client
 * @param string $sesskey
 * @param string $userid
 * @param string $idfield
 * @param string $id_examen
 * @return string
 */
	public function get_resultats_examen_html($client,$sesskey,$userid,$idfield,$id_examen) {
		return $this->send($this->to_primitive( //simple chaine HTML en CDATA
            parent::get_resultats_examen_html($client, $sesskey,$userid,$idfield,$id_examen)));
        }

 /**
 * @param int $client
 * @param string $sesskey
 * @param string $userid
 * @param string $idfield
 * @param string $id_examen
 * @return string
 */
    public function get_parcours_examen_html($client,$sesskey,$userid,$idfield,$id_examen) {
		return $this->send($this->to_primitive(
            parent::get_parcours_examen_html($client, $sesskey,$userid,$idfield,$id_examen)));

        }


	// requetes a r�ponse multiples


 /**
 * @param int $client
 * @param string $sesskey
 * @param int $id_pere
 * @return etablissementRecord[]
 */
	public function get_etablissements($client, $sesskey,$id_pere=1) {
		return $this->send( $this->to_array(
			parent::get_etablissements($client, $sesskey,$id_pere),
			'etablissementRecord',
			"aucun etablissement ou composante"));
        }

 /**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @return questionRecord[]
 */
	public function get_questions($client, $sesskey, $id_examen) {
		return $this->send($this->to_array(
			parent::get_questions($client, $sesskey,$id_examen),
			'questionRecord',
			"examen $id_examen non trouvé ou aucune question "));
        }


 /**
 * @param int $client
 * @param string $sesskey
 * @param string $typep
 * @return questionRecord[]
 */
    public function get_toutes_questions($client, $sesskey, $typep) {
       // $this->debug_output('typep'.$typep);
        return $this->send($this->to_array(
            parent::get_toutes_questions($client, $sesskey,$typep),
            'questionRecord',
            "aucune question "));
        }


/**
 * @param int $client
 * @param string $sesskey
 * @param string $typep
 * @return qcmItemRecord[]
 */
    public function get_toutes_questions_et_reponses($client, $sesskey, $typep) {
         return $this->send( $this->to_array(
            parent::get_toutes_questions_et_reponses($client, $sesskey,$typep),
            'qcmItemRecord',
            "aucune question "));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @return referentielRecord[]
 */
	public function get_referentiels($client, $sesskey) {
		return $this->send( $this->to_array(
			parent::get_referentiels($client, $sesskey),
			'referentielRecord',
			"aucun referentiel !"));
        }


/**
 * @param int $client
 * @param string $sesskey
 * @return familleRecord[]
 */
	public function get_familles($client, $sesskey) {
        return $this->send( $this->to_array(
			parent::get_familles($client, $sesskey),
			'familleRecord',
			"aucune famille !"));
        }



/**
 * @param int $client
 * @param string $sesskey
 * @param string $id_ref
 * @return alineaRecord[]
 */
	public function get_alineas($client, $sesskey,$id_ref) {
		return $this->send( $this->to_array(
			parent::get_alineas($client, $sesskey,$id_ref),
			'alineaRecord',
			"aucun alinea pour le referentiel $id_ref!"));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $id_question
 * @return reponseRecord[]
 */
	public function get_reponses($client,$sesskey,$id_question) {
		return $this->send( $this->to_array(
			parent::get_reponses($client, $sesskey,$id_question),
			'reponseRecord',
		"question $id_question non trouvée ou pas de réponses "));
	}

/**
 * @param int $client
 * @param string $sesskey
 * @param string id_question
 * @return documentRecord[]
 */
    public function get_documents($client,$sesskey,$id_question) {
        return $this->send( $this->to_array(
            parent::get_documents($client, $sesskey,$id_question),
            'documentRecord',
        "question $id_question non trouvée ou pas de documents "));
    }


/**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @return inscritRecord[]
 */
	public function get_inscrits($client, $sesskey, $id_examen) {
		return $this->send($this->to_array(
			parent::get_inscrits($client, $sesskey,$id_examen),
			'inscritRecord',
			"examen $id_examen non trouvé ou pas d'inscription"));
        }

/**
 * rev 979 ajout parametre optional id_etab pour r�cuperer les examens d'une composante
 * @param int $client
 * @param string $sesskey
 * @param string $typep
 * @param string $id_etab (optional)
 * @return examenRecord[]
 */
    public function get_examens($client, $sesskey, $typep='',$id_etab='') {
    	return $this->send($this->to_array(
			parent::get_examens($client, $sesskey,$typep,$id_etab),
			'examenRecord',
			"aucun examen trouvé en $typep"));
    }



/**
 * rev 979 cf forum
 * renvoie les examens auquel est inscrit un candidat
 * @param int $client
 * @param string $sesskey
 * @param string $userid
 * @param string $idfield
 * @param string $typep
 * @return examenRecord[]
 */
    public function get_examens_inscrit($client, $sesskey,$userid,$idfield,$typep) {
        return $this->send($this->to_array(
            parent::get_examens_inscrit($client, $sesskey,$userid,$idfield,$typep),
            'examenRecord',
            "aucun examen trouvé en $typep"));
    }


/**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @return bilanDetailleRecord[]
 */
     public function get_bilans_examen($client, $sesskey, $id_examen) {
                return $this->send($this->to_array(
                        parent::get_bilans_examen($client, $sesskey,$id_examen),
                        'bilanDetailleRecord',
                        "examen $id_examen non trouvé ou pas d'inscription ou pas de notes"));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @param int $type
 * @return bilanDetailleRecord[]
 */
	public function get_bilans_detailles_examen($client, $sesskey, $id_examen,$type=3) {
                return $this->send($this->to_array(
                        parent::get_bilans_detailles_examen($client, $sesskey,$id_examen,$type),
                        'bilanDetailleRecord',
                        "examen $id_examen non trouvé ou pas d'inscription ou pas de notes"));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @return noteRecord[]
 */
     public function get_notes_examen($client, $sesskey, $id_examen) {
                return $this->send($this->to_array(
                        parent::get_notes_examen($client, $sesskey,$id_examen),
                        'noteRecord',
                        "examen $id_examen non trouvé ou pas d'inscription ou pas de notes"));
        }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $idcandidat
 * @param string $idfield
 * @param string $typep
 * @param int $consolid
 * @return bilanDetailleRecord[]
 */
     public function get_scores_candidat($client, $sesskey, $idcandidat,$idfield,$typep,$consolid) {
     	 return $this->send($this->to_array(
                        parent::get_scores_candidat($client, $sesskey,$idcandidat,$idfield,$typep,$consolid),
                        'bilandetailleRecord',
                        "candidat $idfield=$idcandidat sans d'inscription ou pas de notes"));
     }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $idcandidat
 * @param string $idfield
 * @param string $idexamen
 * @return bilanDetailleRecord[]
 */
     public function get_score_candidat($client, $sesskey, $idcandidat,$idfield,$idexamen) {
     	 return $this->send($this->to_array(
                        parent::get_score_candidat($client, $sesskey,$idcandidat,$idfield,$idexamen),
                        'bilandetailleRecord',
                        "candidat $idfield=$idcandidat sans inscription ou pas de notes"));
     }


/**
 * @param int $client
 * @param string $sesskey
 * @param inscritInputRecord[] $candidats
 * @return inscritRecord[]
 */
	 public function cree_candidats ($client,$sesskey,$candidats) {
       return $this->send( $this->to_array(
            parent::cree_candidats($client, $sesskey,$candidats),
            'inscritRecord',
            'aucun candidat recu'));
        }



/**
 * @param int $client
 * @param string $sesskey
 * @param string $idcandidat
 * @param string $idfield
 * @param string $idexamen
 * @param string $listequestions
 * @param string[] $listereponses
 * @return bilanDetailleRecord[]
 */
     public function corrige_examen( $client,$sesskey,$idcandidat,$idfield,$idexamen,$listequestions,$listereponses) {
          return $this->send($this->to_array(
                        parent::corrige_examen($client,$sesskey,$idcandidat,$idfield,$idexamen,$listequestions,$listereponses),
                        'bilandetailleRecord',
                        "erreur de format"));
     }


     //requetes de synchronisation locales/nationales
/**
 * @param int $client
 * @param string $sesskey
 * @param string $typep
 * @return questionRecord[]
 */
    public function get_questions_obsoletes ($client,$sesskey,$typep) {
        return $this->send( $this->to_array(
            parent::get_questions_obsoletes($client, $sesskey,$typep),
            'questionRecord',
            "aucune question obsolete en $typep "));
        }


    //rev 944 on peut sp�cifier des logins, numero ou email

 /**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @param string[] $candidats
 * @param string $idfield
 * @param string $tags informations libre associ�es a cette inscription
 * @return affectRecord[]
 */
    public function inscrit_examen ($client,$sesskey,$id_examen,$candidats,$idfield='login',$tags='') {
        if (empty($idfield)) $idfield='login';
        return $this->send($this->to_array(
            parent::inscrit_examen($client, $sesskey,$id_examen,$candidats,$idfield,$tags),
            'affectRecord',
             "aucun candidat à inscrire"));
        }


 /**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @param string[] $candidats
 * @param string $idfield
 * @return affectRecord[]
 */
    public function desinscrit_examen ($client,$sesskey,$id_examen,$candidats,$idfield='login') {
        if (empty($idfield)) $idfield='login';
        return $this->send($this->to_array(
            parent::desinscrit_examen($client, $sesskey,$id_examen,$candidats,$idfield),
            'affectRecord',
             "aucun candidat à desinscrire"));
        }


/**
 * @param int $client
 * @param string $sesskey
 * @param qcmItemRecord[] $questions
 * @return questionRecord[]
 */
	public function envoi_questions ($client,$sesskey,$questions) {
        return $this->send($this->to_array(
            parent::envoi_questions($client, $sesskey,$questions),
            'questionRecord',
             "aucune question n'a été reçue"));
        }


/**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @param string $type_pf
 * @param resultatExamenInputRecord[] $copies
 * @param resultatDetailleInputRecord[] $details
 * @return boolean
 */
	public function envoi_examen ($client,$sesskey,$id_examen,$type_pf,$copies,$details) {
        return $this->send($this->to_primitive(
            parent::envoi_examen($client, $sesskey,$id_examen,$type_pf,$copies,$details)));

        }

/**
 * ajout� revision 969 pour appel par Moodle
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @param int $timestart
 * @return noteRecord[]
 */
    public function get_passages_recents($client,$sesskey,$id_examen,$timestart) {
        return $this->send($this->to_array(
            parent::get_passages_recents($client, $sesskey,$id_examen,$timestart),
            'noteRecord','aucun passage recent'));

        }


 /**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @return boolean
 */
   public function verouille_examen ( $client,$sesskey,$id_examen) {
        return $this->send($this->to_primitive(parent::do_verouille_examen($client,$sesskey,$id_examen, true)));
   }

 /**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @return boolean
 */
   public function deverouille_examen ( $client,$sesskey,$id_examen) {
        return $this->send($this->to_primitive(parent::do_verouille_examen($client,$sesskey,$id_examen, false)));
   }


  /**
 * @param int $client
 * @param string $sesskey
 * @param string $tags
 * @return examenRecord[]
 */

   public function get_examens_bytags($client,$sesskey,$tags) {
      return $this->send($this->to_array(
            parent::get_examens_bytags($client, $sesskey,$tags),
            'examenRecord',
            "aucun examen trouvé avec ces tags $tags" ));
   }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $tags
 * @return questionRecord[]
 */
    public function get_questions_bytags($client,$sesskey,$tags) {
      return $this->send($this->to_array(
            parent::get_questions_bytags($client, $sesskey,$tags),
            'questionRecord',
            "aucune question trouvée avec ces tags $tags" ));
   }

   /**
 * @param int $client
 * @param string $sesskey
 * @param string $tags
 * @return inscritRecord[]
 */
   public function get_inscrits_bytags($client,$sesskey,$tags) {
      return $this->send($this->to_array(
            parent::get_inscrits_bytags($client, $sesskey,$tags),
             'inscritRecord',
             "aucun candidat trouvé avec ces tags $tags"
            ));
   }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $tags
 * @return personnelRecord[]
 */
   public function get_utilisateurs_bytags($client,$sesskey,$tags) {
          return $this->send($this->to_array(
            parent::get_utilisateurs_bytags($client, $sesskey,$tags),
             'personnelRecord',
             "aucun utilisateur trouvé avec ces tags $tags"
            ));
   }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $userid
 * @param string $idfield
 * @param string $id_examen
 * @return boolean
 */
    public function a_passe_examen($client,$sesskey,$userid,$idfield,$id_examen) {
        return $this->send($this->to_primitive(
           parent::a_passe_examen($client,$sesskey,$userid,$idfield,$id_examen)
        ));
    }

 /**
 * @param int $client
 * @param string $sesskey
 * @param string $userid
 * @param string $idfield
 * @param string $id_examen
 * @return boolean
 */
    public function est_inscrit_examen($client,$sesskey,$userid,$idfield,$id_examen) {
        return $this->send($this->to_primitive(
           parent::est_inscrit_examen($client,$sesskey,$userid,$idfield,$id_examen)
        ));
    }


  /**
 * @param int $client
 * @param string $sesskey
 * @return string
 */
    public function get_themeurl($client, $sesskey) {
           return $this->send($this->to_primitive(
           parent::get_themeurl($client,$sesskey)
        ));
    }

  /**
 * @param int $client
 * @param string $sesskey
 * @return string
 */
    public function get_version($client, $sesskey) {
            return $this->send($this->to_primitive(
           parent::get_version($client,$sesskey)
        ));
    }

}


?>
