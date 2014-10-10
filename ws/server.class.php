<?php
// $Id: server.class.php 1308 2012-09-25 09:45:46Z ppollet $

/**
 * Base class for web services server layer. PHP 5 ONLY.
 *
 * @package Web Services
 * @version $Id: server.class.php 1308 2012-09-25 09:45:46Z ppollet $
 * @author Patrick Pollet <patrick.pollet@insa-lyon.fr> v 1.5
 */

/* rev history

*/

$chemin = "../";

define('ACCES_WEBSERVICE', 1);

if (!defined ('NO_HEADERS'))  //rev 889 une notice de moins ...
    define('NO_HEADERS',1); // pas d'entete par c2i_params !
/*
 * tres important
 * dans le cas ou des lignes blanches
 * ou des print trainent dans le code de la PF !!!!!
 */
ob_start();
define('V2', 1);
require_once ("$chemin/commun/c2i_params.php");
require_once ("$chemin/commun/lib_resultats.php");
require_once ("$chemin/commun/lib_ldap.php");
require_once ("filterlib.php");
ob_clean();

/**
 * The main server class.
 *
 * This class is broken up into three main sections of methods:
 * 1. Methods that perform actions related to client requests.
 * 2. Methods that handle server setup, incoming client requests, and returning a
 *    response to the client.
 * 3. Utility functions that perform functions such as datetime format conversion or
 *    replication of Moodle library functions in a manner safe for usage within this
 *    web services implementatation.
 *
 * The only methods that need to be extended in a child class are main() and any of
 * the service methods which need special transport-protocol specific handling of
 * input and / or output data.
 *
 *
 * @package Web Services
 */
class server {

	var $sessiontimeout = 180000; // 30 minutes.
    var $version;

	/**
	 * Constructor method.
	 *
	 * @return server
	 */
	function __construct(){
		global $CFG;
		// setup default values if not set in admin screens (see admin/wspp.php)
		if (empty ($CFG->ws_sessiontimeout))
			$CFG->ws_sessiontimeout = 1800;
		$this->sessiontimeout = $CFG->ws_sessiontimeout;

		if (!isset ($CFG->ws_logoperations))
			$CFG->ws_logoperations = 1;
		if (!isset ($CFG->ws_logerrors))
			$CFG->ws_logerrors = 0;
		if (!isset ($CFG->ws_logdetailedoperations))
			$CFG->ws_logdetailledoperations = 0;
        if (!isset ($CFG->ws_enforceipcheck))
            $CFG->ws_enforceipcheck = 1;
        //oct 2014 ce drapeau doit être dans la confg. avancée comme les autres ci-dessus
        if (!isset ($CFG->ws_debug))
        	set_config('webservice', 'ws_debug', '0', '1');    
        $this->version=$CFG->version; // rev 957

	}

	/**
	 * Creates a new session key.
	 *
	 * @return string A 32 character session key.
	 */
	private function add_session_key() {
		$time = (string) time();
		$randstr = (string) random_string(10);

		/// XOR the current time and a random string.
		$str = $time;
		$str ^= $randstr;

		/// Use the MD5 sum of this random 10 character string as the session key.
		return md5($str);
	}



	/**
	 * Validate's that a client has an existing session.
	 *
	 * @param int $client The client session ID.
	 * @param string $sesskey The client session key.
	 * @return boolean True if the client is valid, False otherwise.
	 */
	private function validate_client($client = 0, $sesskey = '',$operation='',$id_objet='') {
		global $USER,$CFG;
		//	$this->debug_output("validate_client $client user=" . print_r($USER, true));
		/// We can't validate a session that hasn't even been initialized yet.


			 // rev 973 added extra securityu checks
		 $client  = clean_param($client, PARAM_INT);
         $sesskey = clean_param($sesskey, PARAM_ALPHANUM);

		$sql =<<<EOS
 SELECT s.*
 FROM c2iwebservices_sessions s
 WHERE s.id = $client
AND s.verified = 1 AND s.sessionend = 0

EOS;

		if (!$sess = get_record_sql($sql, 0)) {
			return false;
		}

		/// Validate this session.
		if ($sesskey != $sess->sessionkey) {
			return false;
		}
        // rev 843 pas trop longtemps sans logout ...
        if ($sess->sessionbegin + $this->sessiontimeout < time()){
            $sess->sessionend=time();
            update_record('webservices_sessions', $sess,'id');
            return false;

        }

		//$this->debug_output("validate_client : $client OK");

		if ($operation) {   // rev 843 tracking
            //todo tracking
            $USER->id_user=$sess->userid;
            $USER->id_etab_perso=etab($USER->id_user); //rev 919
            $USER->type_plateforme="webservice";
            $USER->operation=$operation;  //m�moire en cas d'erreur
            $USER->id_objet=$id_objet;  //m�moire en cas d'erreur
            // rev 979 pour tests plus fins lors de l'op�rartion
            $USER->type_user='P';
            $USER->ip = getremoteaddr(); // rev 1.5.4
            lecture_droits();
			if  ($CFG->ws_logoperations)
            	espion2($operation,"",$id_objet);
        }
        	$this->debug_output("validate_client OK $operation $client"); // user=" . print_r($USER, true));
        return true;
	}

	/**
	 * Sends an FATAL error response back to the client.
	 *
	 * @todo Override in protocol-specific server subclass, e.g. by throwing a PHP  exception
	 * @param string $msg The error message to return.
	 * @return An object with the error message string.(required by mdl_soapserver)
	 */
	 protected function error($msg) {

        global $USER,$CFG;
		$res = new StdClass();
		$res->error = $msg;
		$this->debug_output("server.soap fatal error : $msg");
		if ($CFG->ws_logerrors)
        	if (!empty($USER->operation))
            	espion2($USER->operation,traduction("echec").' : '.$msg,$USER->id_objet);
		return $res;
	}

	/**
	* Do server-side debugging output (to file).
	*
	* @uses $CFG
	* @param mixed $output Debugging output.
	* @return none
	*/
	protected function debug_output($output) {
		global $CFG;
        // octobre 2014 passé dans la configuration avancée
        if (empty($CFG->ws_debug))
        	return;
		$fp = fopen($CFG->chemin_ressources . '/debug1.out', 'a');
		fwrite($fp, "[" . time() . "] $output\n");
		fflush($fp);
		fclose($fp);
	}

	/**
	* return and object with error attribute set
	* this record will be inserted in client array of responses
	* do not override in protocol-specific server subclass.
	*/
	private function non_fatal_error($msg) {
		$res = new StdClass();
		$res->error = $msg;
		$this->debug_output("server.soap non fatal error : $msg");
		return $res;
	}

	/**
	 * Validates a client's login request.
     * @param string $username
     * @param string password
     * @return loginRecord
	 */
	function login($username, $password) {
		global $CFG,$USER;

		if (!empty ($CFG->ws_disable))
			return $this->error(traduction('ws_accessdisabled'));

         $userip = getremoteaddr(); // rev 1.5.4
         if (!empty($CFG->ws_enforceipcheck)) {
            if (!get_record('webservices_clients_allow',"client='$userip'",false))
            return $this->error(traduction('ws_accessrestricted',false,$userip));

         }

  // rev 1.6.3 added extra security checks
         $username = clean_param($username, PARAM_NOTAGS);
         $password = clean_param($password, PARAM_NOTAGS);

		/// Use Moodle authentication.
		/// FIRST make sure user exists , otherwise account WILL be created with CAS authentification ....
        // on ne cherche pas un �tudiant
		//if (!$USER = get_utilisateur($username, 0)) {
        // rev 984 un candidat peut aussi passer par les WS ( smartphones)
        // attention donc ensuite aux droits
         if (!$USER = get_compte($username, 0)) {
			return $this->error(traduction ('ws_invaliduser'));
		}
		$this->debug_output($username . " " . print_r($USER, true));
		//if (md5($password) != $user->password)
		if (! authentifie_compte($USER,$password))   // rev 920 acc�s ldap OK
			return $this->error(traduction('ws_invaliduser'));

        // rev 979 lecture r��lle des droits pour acc�s par un admin d'une composante !!!!
        $USER->id_user=$USER->login;
        $USER->id_etab_perso=$USER->etablissement;
        lecture_droits();
        //$this->debug_output(print_r($USER,true));
        // c'est ici qu'il va falloir un jour faire attention !!!!
        if (! is_admin($USER->login,$USER->etablissement))
       // if ($user->est_admin_univ !='O')
            return $this->error(traduction ('ws_norights'));

		/// Verify that an active session does not already exist for this user.
		$sql = "SELECT s.*
                FROM {$CFG->prefix}webservices_sessions s
                WHERE s.userid = '{$USER->login}' AND
	                   s.verified = 1 AND ip='$userip' AND
		               s.sessionend = 0 AND
		               (" . time() . " - s.sessionbegin) < " . $this->sessiontimeout;

		if ($sess=get_record_sql($sql, 0)) {
    		//return $this->error('A session already exists for this user (' . $user->login . ')');
    		// return $this->init($sess->id) ; // V1.5 reutilise
    		$sess->sessionbegin = time();
    		$sess->sessionend = 0;
    		/** surtout pas update_record vire la cl� id !!!!
            if (!update_record('webservices_sessions', $sess,'id')) {
        		$this->debug_output('No update');
        		return $this->error('Could not initialize client session (' . $client . ').');
    		}
            **/
             //give him more time
            set_field('webservices_sessions', 'sessionbegin', time(), "id=". $sess->id);
		} else {
    		/// Login valid, create a new session record for this client.
    		$sess = new stdClass;
    		$sess->userid = $USER->login;
    		$sess->verified = true;
    		$sess->ip = getremoteaddr(); // rev 1.5.4
    		$sess->sessionkey = $this->add_session_key();
            $sess->sessionbegin = time();
            $sess->sessionend = 0;

    		$sess->id = insert_record('webservices_sessions', $sess);
    		maj_info_connexion($USER,"webservice","webservice"); //tracking
    		//return $this->init($sess->id);
		}
		/// Return standard data to be converted into the appropriate data format
		/// for return to the client.
		$ret= new LoginReturn();
		$ret->setClient($sess->id);
		$ret->setSessionkey($sess->sessionkey);
		$this->debug_output("Login successful. $sess->id:$sess->sessionkey");
		return $ret;

	}

	/**
	 * Logs a client out of the system by removing the valid flag from their
	 * session record and any user ID that is assosciated with their particular
	 * session.
	 *
	 * @param integer $client The client record ID.
	 * @param string $sesskey The client session key.
	 * @return boolean True if successfully logged out, false otherwise.
	 */
	function logout($client, $sesskey) {
		if (!$this->validate_client($client, $sesskey,"logout")) {
			return $this->error(traduction('ws_invalidclient'));
		}

		$sql =<<<EOS
SELECT s.*
FROM c2iwebservices_sessions s
WHERE s.id = $client
AND s.verified = 1 AND s.sessionend = 0
EOS;
		if ($sess = get_record_sql($sql, 0)) {

			// rev 928 : deja fait par validate_client
			//maj_info_deconnexion ($sess->userid); //tracking
            $sess->verified = 0;
			$sess->sessionend = time();
			if (update_record('webservices_sessions', $sess,'id')) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}

	function get_version($client, $sesskey) {
		global $CFG;
		if (!$this->validate_client($client, $sesskey,__FUNCTION__)) {
			return $this->error(traduction('ws_invalidclient'));
		}
		return $CFG->version;
	}


	function get_themeurl($client, $sesskey) {
		global $CFG;
		//if (!$this->validate_client($client, $sesskey,__FUNCTION__)) {
		//	return $this->error(traduction('ws_invalidclient'));
		//}
		return add_slash_url($CFG->wwwroot).'themes/'.$CFG->theme;
	}
	/**
	* verifie les droits d'acc�s
	*/
	protected function verifie_droits($client, $capacite, $context = false) {
        global $USER;
		$id=$USER->id_user;
		//return a_capacite($capacite, $context, $id); //TODO
		//return a_capacite("bddt", $context, $id); //pour l'instant OK si peut t�l�charger la BD
        //tempo on a v�rifi� qu'il est admin au login...

        return true;
	}

	/* debut des operations */

	function get_inscrit($client, $sesskey, $userid, $idfield) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$userid))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/inscrit:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		//if ($ret = get_inscrit($userid, 0))
		if ($ret=get_record("inscrits","$idfield='".addslashes($userid)."'",false))  // rev 929 peut chercher par login,email ou num
			return filter_inscrit($client, $ret);
		else
			return $this->error(traduction ('ws_candidatinconnu',false,$idfield,$userid ));
	}

	function get_etablissement($client, $sesskey, $id) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/etablissement:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		if ($ret = get_etablissement($id, 0))
			return filter_etablissement($client, $ret);
		else
			return $this->error(traduction('ws_etablissementinconnu',false, $id));
	}

	function get_personnel($client, $sesskey, $userid, $idfield) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$userid))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/utilisateur:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		//if ($ret = get_utilisateur($userid, 0))
		if ($ret=get_record("utilisateurs","$idfield='".addslashes($userid)."'",false))  // rev 929 peut chercher par login,email ou num
			return filter_utilisateur($client, $ret);
		else
			return $this->error(traduction ('ws_utilisateurinconnu',false,$idfield,$userid));

	}

	function get_question($client, $sesskey, $id) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/question:consulter"))
			return $this->error(traduction('ws_illegaloperation'));

		if (!$ret=get_question_byidnat($id,false))
			return $this->error("identifiant de question $id incorrect");

		return filter_question($client, $ret);
	}

	function get_examen($client, $sesskey, $id_examen) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/examen:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		  if (!$ret=get_examen_byidnat($id_examen,false))
		    return $this->error(traduction ('ws_exameninconnu',false,$id_examen));
		return filter_examen($client, $ret);
	}

	// requetes a reponse multiples

	function get_referentiels($client, $sesskey) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,""))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/inscrit:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		if ($ret = get_referentiels(false,0))  // critere de tri pas die !
			return filter_referentiels($client, $ret);
		else
			return $this->error(traduction ('ws_pasdereferentiels'));
	}

	function get_alineas($client, $sesskey, $id_ref) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_ref))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/inscrit:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		if ($ret = get_alineas($id_ref,false,0))   // refrentiel, tri par d�faut, pas die
			return filter_alineas($client, $ret);
		else
			return $this->error(traduction ('ws_pasdalineas',false,$id_ref));
	}



	function get_familles($client, $sesskey) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,""))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/notion:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		if ($ret = get_familles(false,0))
			return filter_familles($client, $ret);
		else
			return $this->error(traduction ('ws_pasdefamilles'));
	}

	function get_etablissements($client, $sesskey, $id_pere) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_pere))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/etablissement:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		if ($ret = get_composantes($id_pere,0))
			return filter_etablissements($client, $ret);
		else
			return $this->error(traduction('ws_pasdetablissements'));
	}

	function get_questions($client, $sesskey, $id_examen) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/question:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		if (!$ret=get_examen_byidnat($id_examen,false))
		    return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

	    $idq=$ret->id_examen;
	    $ide=$ret->id_etab;
		$res = get_questions($idq, $ide, false,false,false,0);
		return filter_questions($client, $res);
	}



    /**
     * LA NATIONALE NE RENVOIE que LES VALID�ES !
     */
    function get_toutes_questions($client, $sesskey, $typep) {
        global $CFG;
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$typep))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/question:consulter"))
            return $this->error(traduction('ws_illegaloperation'));
        if ($typep !='certification' && $typep !='positionnement')
            return $this->error(traduction('ws_plateformeinconnue',false,$typep));
        if ($CFG->universite_serveur==1)
            $res = get_records("questions","etat='valid�e' and $typep='OUI'");
        else
             $res = get_records("questions","$typep='OUI'");
        $this->debug_output(print_r($res,true));
        return filter_questions($client, $res);

    }


     function get_questions_obsoletes ($client,$sesskey,$typep){
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$typep))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/question:consulter"))
            return $this->error(traduction('ws_illegaloperation'));
        if ($typep !='certification' && $typep !='positionnement')
            return $this->error(traduction('ws_plateformeinconnue',false,$typep));
        $res = get_records("questions","etat='refus�e' and $typep='OUI'");
        return filter_questions($client, $res);

     }


	function get_reponses($client, $sesskey, $id_question) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_question))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/question:consulter"))
			return $this->error(traduction('ws_illegaloperation'));


		if (!$ret=get_question_byidnat($id_question,false))
			return $this->error(traduction ('ws_questioninconnue',false,$id_question));
		$res = get_reponses($ret->id,$ret->id_etab, false,0);
		return filter_reponses($client, $res);
	}

    function get_documents($client, $sesskey, $id_question) {
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_question))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/question:consulter"))
            return $this->error(traduction('ws_illegaloperation'));


        if (!$q=get_question_byidnat($id_question,false))
            return $this->error(traduction ('ws_questioninconnue',false,$id_question));
        $res = get_documents($q->id,$q->id_etab, false);
        $ret=array();
        foreach ($res as $doc) {
            unset($doc->url);
            if ($b64=  encode_document($doc->id_doc.'.'.$doc->extension, $q->id,$q->id_etab )) {
                $doc->base64=$b64;
                $doc->error='';
            } else
                $doc->error=traduction ('ws_documentmanquant',false,$doc->id_doc,$doc->extension, $id_question);

            //$this->debug_output(print_r($doc,true));
            $ret[]=$doc;
        }
        return filter_documents($client, $ret);
    }

	 function get_examens($client, $sesskey, $typep,$id_etab='') {
	 	global $USER;
	 		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$typep))
			return $this->error(traduction('ws_invalidclient'));

		if (!$this->verifie_droits($client, "c2i/examen:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		
		if (empty($typep))
		   $typep='CERTIFICATION';
		
		$USER->type_plateforme=$typep;
        // rev 979 possibilt� de sp�cifier l'�tablissement vis�
        if (empty($id_etab)) $id_etab=$USER->id_etab_perso;

         // rev 979 possibilit� pour un admin de cr�er un examen dans une de ses composantes
         if (!empty($id_etab)) {
            if (! get_etablissement($id_etab, 0))
                return $this->error(traduction('ws_etablissementinconnu',false, $id_etab));
           // $this->debug_output(print_r($USER,true));
            if (! is_admin(false,$id_etab))
                return $this->error(traduction('ws_illegaloperation'));
         }

		//$this->debug_output(print_r($USER,true));
        // rev 979 ajoute les examens des composantes (parametre true))
		return filter_examens($client,get_examens($id_etab,'nom_examen',true));


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
    global $USER;
            if (!$this->validate_client($client, $sesskey,__FUNCTION__,$typep))
            return $this->error(traduction('ws_invalidclient'));

        if (!$this->verifie_droits($client, "c2i/examen:consulter"))
            return $this->error(traduction('ws_illegaloperation'));
        $USER->type_plateforme=$typep;

        if (!$cpt=get_compte_by($userid,$idfield))
                return $this->error(traduction('ws_compteinconnu',false,$idfield,$userid));
        return filter_examens($client,get_examens_inscrits($cpt->login,'id',false));

    }



	function get_inscrits($client, $sesskey, $id_examen) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
			return $this->error(traduction('ws_invalidclient'));

		if (!$this->verifie_droits($client, "c2i/inscrit:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
  		if (!$ex=get_examen_byidnat($id_examen,false))
		    return $this->error(traduction ('ws_exameninconnu',false,$id_examen ));

	    $idq=$ex->id_examen;
	    $ide=$ex->id_etab;

		if ($users = get_inscrits($idq, $ide,false,0))
				return filter_inscrits($client, $users);
			else
				return $this->error(traduction('ws_pasdinscrits',false,$id_examen));
	}

/**
 * rev 968 ajout parametre timestart pour filtrage
 * voir get_passages_recents
 */

	function get_notes_examen($client, $sesskey, $id_examen,$timestart=0) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/resultat:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
	  if (!$ret=get_examen_byidnat($id_examen,false))
		    return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

	    $idq=$ret->id_examen;
	    $ide=$ret->id_etab;
		$ret = array ();
		if ($users = get_inscrits($idq, $ide,false,0)) { //inclus les personnels ;-)
				foreach ($users as $etudiant) {
                     $res=get_resultats($idq,$ide,$etudiant->login,false);
					//$this->debug_output(print_r($res, true));
					if ($res->score_global !=-1) { // l'a passe ???
                        // rev 969 if faut renvoie un tableau d'objets comme d'hab, pas de tableaux
                        $tmp=new NoteRecord();
                        $tmp->setLogin($etudiant->login);
                        $tmp->setNumetudiant($etudiant->numetudiant);
                        $tmp->setScore($res->score_global);
                        $tmp->setExamen($ide.".".$idq);
                        $tmp->setIp($res->ip_max);
                        $tmp->setOrigine($res->origine);
                        $tmp->setDate($res->ts_date_max);


						if ($tmp=filter_note($client,$tmp,$timestart))
							$ret[] = $tmp;
					}
				}

			} else {
				return $this->non_fatal_error(traduction ('ws_pasdinscrits',false,$id_examen));
			}
		//$this->debug_output(print_r($ret, true));
        //$this->debug_output("ts=".$timestart);

		return $ret;

	}


	/**
	version  tout C2I  les referentiels ne sont plus en dur dans le wsdl mais relus et recodes ici
	@param id_examen  string codeetablissemnt.codexamen

	*/
	function get_bilans_examen($client, $sesskey, $id_examen) {
		return server::get_bilans_detailles_examen($client, $sesskey, $id_examen, 1);
        //et surtout pas $this qui appelle deux fois  cekui du dessous !
	}

	/**
	 version  tout C2I  les referentiels
	 @param id_examen  string codeetablissemnt.codexamen
	 @param type int 1=par referentiel 2 par competence (ref et alinea) 3 les 2
	 */
	function get_bilans_detailles_examen($client, $sesskey, $id_examen, $type) {
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen." ".$type))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/resultat:consulter"))
			return $this->error(traduction('ws_illegaloperation'));

		$ret = array ();

		if (!$ex=get_examen_byidnat($id_examen,false))
			return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

		$idq=$ex->id_examen;
		$ide=$ex->id_etab;

		if ($users = get_inscrits($idq, $ide,false,0)) { //inclus les personnels ;-)
			// $this->debug_output(print_r($users, true));
			foreach ($users as $etudiant) {

				$res=get_resultats($idq,$ide,$etudiant->login,false);
				//$this->debug_output($etudiant->login);

				//$this->debug_output(print_r($res, true));
				if ($res->score_global !=-1) { // l'a passe ???

					//ajouter les notes par referentiel, competences, ou les 2
					$refs = array ();
					foreach ($res->tabref_score as $ref => $note) {
						$tmp = new scoreRecord();
                        $tmp->setCompetence($ref);
						$tmp->setScore($note);
						//filtrage ????
						$refs[] = $tmp;
					}
					$comps = array ();
					foreach ($res->tabcomp_score as $comp => $note) {
                        $tmp = new ScoreRecord();
                        $tmp->setCompetence($comp);
                        $tmp->setScore($note);
						//filtrage ????
						$comps[] = $tmp;
					}

					$tmp = new bilanDetailleRecord();
					$tmp->setLogin($etudiant->login);
					$tmp->setNumetudiant($etudiant->numetudiant);
					$tmp->setScore($res->score_global);
					$tmp->setExamen($ide.".".$idq);
					$tmp->setIp($res->ip_max);
				    $tmp->setOrigine($res->origine);
                    $tmp->setDate($res->ts_date_max);

					if ($type == 1)
						$tmp->setDetails($refs);
					elseif ($type == 2) $tmp->setDetails($comps);
					else
						$tmp->setDetails(array_merge($refs, $comps));

					$ret[] = $tmp;
				}
			}

		} else {
			$ret[]= $this->non_fatal_error(traduction ('ws_pasdinscrits',false,$id_examen));
		}
		// et roulez jeunesse ..
		//$this->debug_output( "GBE".print_r($ret, true));
		return $ret;

	}

	 function get_score_candidat($client, $sesskey, $userid,$idfield,$idexamen) {
	 	global $USER,$CFG;
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$idfield."=".$userid))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/examen:consulter"))
			return $this->error(traduction('ws_illegaloperation'));


             // rev 929 peut chercher par login,email ou num
        //if (!$etudiant=get_record("inscrits","$idfield='$userid'",false))
        // rev 944 recherche dans les deux tables
         if (!$etudiant=get_compte_by($userid,$idfield,false))
            return $this->error(traduction ('ws_candidatinconnu',false,$idfield,$userid)) ;

		if (!$ex=get_examen_byidnat($idexamen,false))
			return $this->error(traduction ('ws_exameninconnu',false,$idexamen ));
		$res=get_resultats($ex->id_examen,$ex->id_etab,$etudiant->login,false);
		$ret=array();
		if ($res->score_global !=-1) { // l'a passe ???
					//ajouter les notes par referentiel, competences, ou les 2
					$refs = array ();
					foreach ($res->tabref_score as $ref => $note) {
                        $tmp = new scoreRecord();
                        $tmp->setCompetence('D:'.$ref);
                        $tmp->setScore($note);

						//filtrage ????
						$refs[] = $tmp;
					}
					$comps = array ();
					foreach ($res->tabcomp_score as $comp => $note) {
                        $tmp = new scoreRecord();
                        $tmp->setCompetence( 'A:'.$comp);
                        $tmp->setScore($note);

						//filtrage ????
						$comps[] = $tmp;
					}
                    $tmp = new bilanDetailleRecord();
                    $tmp->setLogin($etudiant->login);
                    $tmp->setNumetudiant($etudiant->numetudiant);
                    $tmp->setScore($res->score_global);
                    $tmp->setExamen($idexamen);
                    $tmp->setIp($res->ip_max);
                    $tmp->setOrigine($res->origine);
                    $tmp->setDate($res->ts_date_max);
					$tmp->setDetails(array_merge($refs, $comps));

					$ret[] = $tmp;
				}else {
					$ret[]=$this->non_fatal_error(traduction ('ws_paspasse',false,$idfield,$userid,$idexamen));
				}

			return $ret;

	 }

	function get_scores_candidat($client, $sesskey, $userid,$idfield,$typep,$consolid) {
		global $USER,$CFG;
		if (!$this->validate_client($client, $sesskey,__FUNCTION__,$idfield."=".$userid))
			return $this->error(traduction('ws_invalidclient'));
		if (!$this->verifie_droits($client, "c2i/examen:consulter"))
			return $this->error(traduction('ws_illegaloperation'));
		 // rev 929 peut chercher par login,email ou num
		//if (!$etudiant=get_record("inscrits","$idfield='$userid'",false))
        // rev 944 recherche dans les deux tables
         if (!$etudiant=get_compte_by($userid,$idfield,false))
			return $this->error(traduction ('ws_candidatinconnu',false,$idfield,$userid)) ;

		$ret = array();
		$ress=get_resultats_consolides($etudiant->login,$typep,$consolid);
		foreach ($ress as $res) {
			//if ($res->score_global !=-1) (fait pas la consolidation)
			//ajouter les notes par referentiel, competences, ou les 2
					$refs = array ();
					foreach ($res->tabref_score as $ref => $note) {
                        $tmp = new scoreRecord();
                        $tmp->setCompetence('D:'.$ref);
                        $tmp->setScore($note);

						//filtrage ????
						$refs[] = $tmp;
					}
					$comps = array ();
					foreach ($res->tabcomp_score as $comp => $note) {
                        $tmp = new scoreRecord();
                        $tmp->setCompetence( 'A:'.$comp);
                        $tmp->setScore($note);
						//filtrage ????
						$comps[] = $tmp;
					}
                    $tmp = new bilanDetailleRecord();
                    $tmp->setLogin($etudiant->login);
                    $tmp->setNumetudiant($etudiant->numetudiant);
                    $tmp->setScore($res->score_global);
                    $tmp->setExamen($res->examen);
                    $tmp->setIp($res->ip_max);
                    $tmp->setOrigine($res->origine);
                    $tmp->setDate($res->ts_date_max);



					$tmp->setDetails(array_merge($refs, $comps));
					$ret[] = $tmp;
			}
		return $ret;

	}
    /*
     * rev 843 export d'un qcm complet
     */
      function get_qcm($client, $sesskey, $id_examen) {
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/examen:consulter"))
            return $this->error(traduction('ws_illegaloperation'));

        $ret = array();
        if (!$examen=get_examen_byidnat($id_examen,false))
		    return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

	    $idq=$examen->id_examen;
	    $ide=$examen->id_etab;


        $examen->error=""; //important car n'est pas au 1er niveau (attribut pas mis par to_soap)
        $ret=new qcmRecord();
        $ret->setExamen(filter_examen($client,$examen)); //ajout eid
        $ret->setQuestions(array());
        //pas melang�es, tri�es id id question pas d'erreur
        $questions=get_questions ($idq,$ide,false,false,false);
        foreach ($questions as $q) {
            $q->error="";   //important car n'est pas au 1er niveau (attribut pas mis par to_soap)
            $tmp=new qcmItemRecord();
            $tmp->setQuestion(filter_question($client,$q));
            $tmp->setReponses(array());
            $tmp->setDocuments(array()); // rev 1003

            //pas melang�e pas d'erreur
            $reponses=get_reponses($q->id,$q->id_etab,false,false);
            foreach ($reponses as $rep) {
                $rep->error="";  //important car n'est pas au 1er niveau (attribut pas mis par to_soap)
                // rev 1003 ne donne pas quelle reponse est la bonne
                $tmp->reponses[] =filter_reponse($client,$rep,false);
            }
            //rev 1003 ajout des documents
            $documents = get_documents($q->id,$q->id_etab, false);
            foreach ($documents as $doc) {
                unset($doc->url);
                if ($b64=  encode_document($doc->id_doc.'.'.$doc->extension, $q->id,$q->id_etab )) {
                    $doc->base64=$b64;
                    $doc->error='';
                } else {
                    $doc->error=traduction ('ws_documentmanquant',false,$doc->id_doc,$doc->extension,$q->qid);
                    $doc->base64='';
                }
                $tmp->documents[]=filter_document($client,$doc);
            }
          $ret->questions []=$tmp;
        }

       // $this->debug_output(print_r($ret, true));

        return $ret;

      }
        /**
     * LA NATIONALE NE RENVOIE que LES VALID�ES !
     */
    function get_toutes_questions_et_reponses($client, $sesskey, $typep) {
        global $USER,$CFG;
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$typep))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/question:consulter"))
            return $this->error(traduction('ws_illegaloperation'));
        if ($typep !='certification' && $typep !='positionnement')
            return $this->error(traduction ('ws_plateformeinconnue',false,  $typep));
        $ret=array();
        $USER->type_plateforme=$typep;
        $questions=get_toutes_questions($CFG->universite_serveur==1,false);
        foreach ($questions as $q) {
	        $q->error="";   //important car n'est pas au 1er niveau (attribut pas mis par to_soap)
             $tmp=new qcmItemRecord();
	        $tmp->setQuestion(filter_question($client,$q));
	        $tmp->setReponses(array());
	        //pas melang�e pas d'erreur
	        $reponses=get_reponses($q->id,$q->id_etab,false,false);
	        foreach ($reponses as $rep) {
		        $rep->error="";  //important car n'est pas au 1er niveau (attribut pas mis par to_soap)
		        $tmp->reponses[]=filter_reponse($client,$rep);
	        }
	        // rev 958 ajout des documents
	        $tmp->setDocuments(array());
	        $res = get_documents($q->id,$q->id_etab, false);
	        foreach ($res as $doc) {
		        unset($doc->url);
		        if ($b64=  encode_document($doc->id_doc.'.'.$doc->extension, $q->id,$q->id_etab )) {
			        $doc->base64=$b64;
			        $doc->error='';
		        } else {
			        $doc->error=traduction('ws_documentmanquant',false,$doc->id_doc,$doc->extension,$q->qid);
                    $doc->base64='';
                }
		        $tmp->documents []=filter_document($client,$doc);
	        }
	        $ret[]=$tmp;
        }
        //$this->debug_output(print_r($ret, true));
        return $ret;

    }



    function cree_examen ($client,$sesskey,$examen,$id_etab) {
    	 global $USER,$CFG;
    	 if (!$this->validate_client($client, $sesskey,__FUNCTION__,$examen->nom_examen))
            return $this->error(traduction('ws_invalidclient'));

        //TODO verifier les droits sur l'�tablissement de cl'eaxmen � cr�er !!!
        if (!$this->verifie_droits($client, "c2i/examen:ajouter"))
            return $this->error(traduction('ws_illegaloperation'));

         $requis=array('nom_examen','auteur','auteur_mail','positionnement','certification');
         foreach ($requis as $r) {
         	if (empty($examen->$r))
         		return $this->error(traduction ('ws_valeurmanquante',false,$r ));
         }

         if ($examen->positionnement=="OUI") {
	         $examen->certification="NON";
	         $USER->type_plateforme="positionnement"; //important pour le tirage des questions
         }else  if ($examen->certification=="OUI") {
	         $examen->positionnement="NON";
	         $USER->type_plateforme="certification"; //important pour le tirage des questions
         }else return $this->error(traduction ('ws_valeurincorrecte',false,'type_plateforme'));

        // rev 979 possibilit� pour un admin de cr�er un examen dans une de ses composantes
         if (!empty($id_etab)) {
            if (! get_etablissement($id_etab, 0))
                return $this->error(traduction('ws_etablissementinconnu',false, $id_etab));
           // $this->debug_output(print_r($USER,true));
            if (! is_admin(false,$id_etab))
                return $this->error(traduction('ws_illegaloperation'));
         }else $id_etab=$USER->id_etab_perso;

        $examen->verouille=true; // par d�faut si cr�� via le WS
       	 if ($id=cree_examen($examen,$id_etab)) {
            $examen= get_examen($id,$id_etab);
            // rev 982 cf http://www.c2i.education.fr/forum/viewtopic.php?f=4&t=183
            if (($examen->type_tirage == EXAMEN_TIRAGE_ALEATOIRE)  && ($examen->pool_pere == 0)) {
                   tirage_questions ($id,$id_etab);
            }
       	 	return filter_examen($client, $examen);
         }
       	 else return $this->error(traduction ('ws_sqlerror',false,__FUNCTION__).  mysql_error());
        }



    /**
     * rev 930 pour appel depuis Moodle
     */
    function modifie_examen ($client,$sesskey,$id_examen,$examen) {
	    global $USER,$CFG;
	    if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
		    return $this->error(traduction('ws_invalidclient'));
           //TODO verifier les droits sur l'�tablissement de cl'eaxmen � modifier !!!
	    if (!$this->verifie_droits($client, "c2i/examen:modifier"))
		    return $this->error(traduction('ws_illegaloperation'));


	    if (!$ret=get_examen_byidnat($id_examen,false))
		    return $this->error(traduction ('ws_exameninconnu',false,$id_examen ));

	    $idq=$ret->id_examen;
	    $ide=$ret->id_etab;
	    if ($ide !=$USER->id_etab_perso)
		    return $this->error(traduction('ws_illegaloperation'));


	    $change=0;
	    $exa=get_object_vars($examen);
	    foreach($exa as $f=>$v) {
		    if (empty($examen->$f))unset($examen->$f);  // rien de vide
		    else if ($examen->$f==$ret->$f) unset($examen->$f); // si pas chang�
		    else $change++;
	    }

	    if(!$change) return $ret;

	    $examen->id_etab=$ide; // capital (recu de formulaire)
	    $examen->id_examen=$idq; // ne pas oublier !!!!!
	    $examen->ts_datemodification=time();
	    // exemple d'usage d'update_record avec deux cl�s identifiant le bon record ...
	    if(update_record("examens",$examen, 'id_etab','id_examen',false)) {
		    //tracking :
		    espion3("modification","examen", $ide . "." . $idq,$examen);
		    $ret=get_examen($idq,$ide); //relecture
		    return $ret;
	    } else return $this->error(traduction ('ws_sqlerror',false, __FUNCTION__));
    }

    function cree_candidat ($client,$sesskey,$candidat) {
	    global $USER,$CFG;
	    if (!$this->validate_client($client, $sesskey,__FUNCTION__,$candidat->login))
		    return $this->error(traduction('ws_invalidclient'));
            //TODO verifier les droits sur l'�tablissement du candidat � cr�er !!!
	    if (!$this->verifie_droits($client, "c2i/inscrit:ajouter"))
		    return $this->error(traduction('ws_illegaloperation'));

	    $utiliserLDAP=$candidat->auth=="ldap" && auth_ldap_init($USER->id_etab_perso);

	    $requis=array('numetudiant');
	    if (!$utiliserLDAP)
		    $requis=array( 'numetudiant','login','nom','prenom');
	    foreach ($requis as $r) {
		    if (empty($candidat->$r))
			    return $this->error(traduction ('ws_valeurmanquante',false, $r));
	    }


	    if ($cpt=get_compte_byidnumber($candidat->numetudiant,false)) {
		    $cpt->error=traduction('ws_compteexistant',false,'numetudiant',$candidat->numetudiant);
		    return filter_inscrit( $client,$cpt);
	    }

	    if ($utiliserLDAP && empty($candidat->login)){
		    if ( !$cpt=ldap_get_compte_byidnumber($candidat->numetudiant,$USER->id_etab_perso)) {
			    $candidat->error=traduction ("err_numetudiant_pas_ldap",false,$candidat->numetudiant);
			    return $candidat;
		    }
		    $candidat=$cpt;
	    }

	    if ($cpt=get_compte($candidat->login,false)) {  //candidat ou personnel !
		    $cpt->error=traduction ('ws_compteexistant',false,'login',$candidat->login);
		    return filter_inscrit( $client,$cpt);
	    }
		//$this->debug_output(print_r($candidat,true));i
        if (empty($candidat->origine)) // rev 980
                $candidat->origine="webservice";
	    if (cree_candidat($candidat,$candidat->etablissement)){ //gere les valeurs manquantes
	    	return filter_inscrit($client,get_inscrit($candidat->login,false));
	    }else {
	    	$this->debug_output( mysql_error());
	    	return $this->error(traduction ('ws_sqlerror',false, __FUNCTION__));
	    }

    }


    function cree_candidats($client,$sesskey,$candidats) {
    	$ret=array();
    	foreach($candidats as $c)
    		$ret[]=$this->cree_candidat($client,$sesskey,$c);
    	return $ret;
    }





	function cree_personnel ($client,$sesskey,$personnel) {
		 global $USER,$CFG;
		 if (!$this->validate_client($client, $sesskey,__FUNCTION__,$personnel->login))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/personnel:ajouter"))
            return $this->error(traduction('ws_illegaloperation'));
        $utiliserLDAP=$personnel->auth=="ldap" && auth_ldap_init($USER->id_etab_perso);

         $requis=array('numetudiant');
	    if (!$utiliserLDAP)
		    $requis=array( 'numetudiant','login','nom','prenom','email','password');
	    foreach ($requis as $r) {
		    if (empty($personnel->$r))
			    return $this->error(traduction('ws_valeurmanquante',false,$r));
	    }
	    $personnel->password=md5($personnel->password);

	    if ($cpt=get_compte_byidnumber($personnel->numetudiant,false)) {
		    $cpt->error=traduction ('ws_compteexistant',false,'numetudiant',$personnel->numetudiant);
		    return filter_utilisateur( $client,$cpt);
	    }

	    if ($utiliserLDAP && empty($personnel->login)){
		    if ( !$cpt=ldap_get_compte_byidnumber($personnel->numetudiant,$USER->id_etab_perso)) {
			    $personnel->error=traduction ("err_numetudiant_pas_ldap",false,$personnel->numetudiant);
			    return $personnel;
		    }
		    $personnel=$cpt;
	    }

	    if ($cpt=get_compte($personnel->login,false)) {  //candidat ou personnel !
		    $cpt->error=traduction ('ws_compteexistant',false,'login',$personnel->login);
		    return filter_utilisateur( $client,$cpt);
	    }
         if (empty($personnel->origine)) // rev 980
                $personnel->origine="webservice";

	    if (cree_utilisateur($personnel,$personnel->etablissement)){
	    	return filter_utilisateur($client,get_utilisateur($personnel->login,false));
	    }else return $this->error(traduction ('ws_sqlerror',false, __FUNCTION__));


        }


    //rev 944 on peut sp�cifier des logins, numero ou email
    function inscrit_examen ($client,$sesskey,$id_examen,$candidats,$idfield='login',$tags='') {
    	 global $USER,$CFG;
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/examen:modifier"))
            return $this->error(traduction('ws_illegaloperation'));

        if (!$ret=get_examen_byidnat($id_examen,false))
			return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

	    $idq=$ret->id_examen;
	    $ide=$ret->id_etab;
		$rets=array();
        if (empty($tags))
            $tags='inscription webservice '.$USER->ip.' '.time();

		//une liste de num�ros d'�tudiants
		foreach($candidats as $c) {
			$ret=new affectRecord();
			$ret->value=$c; //renvoie identifiant �tudiant trait�
			if (! $cpt=get_compte_by($c,$idfield,false)) //candidat ou utilsateur
				$ret->error=traduction ("info_candidat_inconnu",false,$c);
			else if (est_inscrit_examen($idq, $ide,$cpt->login))
				$ret->error=traduction("info_candidat_deja_inscrit",false,$c,$ide,$idq);
			else {
				inscrit_candidat($idq,$ide,$cpt->login,$tags);
				$ret->error="";
			}
			$rets[]=$ret;
		}
		return $rets;
	}


      //rev 979 on peut sp�cifier des logins, numero ou email
    function desinscrit_examen ($client,$sesskey,$id_examen,$candidats,$idfield='login') {
         global $USER,$CFG;
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/examen:modifier"))
            return $this->error(traduction('ws_illegaloperation'));

        if (!$ret=get_examen_byidnat($id_examen,false))
            return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

        $idq=$ret->id_examen;
        $ide=$ret->id_etab;
        $rets=array();

        //une liste de num�ros d'�tudiants
        foreach($candidats as $c) {
            $ret=new affectRecord();
            $ret->value=$c; //renvoie identifiant �tudiant trait�
            if (! $cpt=get_compte_by($c,$idfield,false)) //candidat ou utilsateur
                $ret->error=traduction ("info_candidat_inconnu",false,$c);
            else if (!est_inscrit_examen($idq, $ide,$cpt->login))
                $ret->error=traduction("info_candidat_pas_inscrit",false,$c,$ide,$idq);
            else {
                desinscrit_candidat($idq,$ide,$cpt->login);
                $ret->error="";
            }
            $rets[]=$ret;
        }
        return $rets;
    }



    /**
     * partie recpetion sur la nationale
     */
    function envoi_questions ($client,$sesskey,$questions) {
        global $USER,$CFG;

        if ($CFG->universite_serveur !=1)
            return $this->error(traduction ('ws_paslocallocal'));

        if (!$this->validate_client($client, $sesskey,__FUNCTION__,""))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/question:ajouter"))
            return $this->error(traduction('ws_illegaloperation'));

        $rets=array();
        $requisq=array('id','id_etab','titre','positionnement','certification',
            'auteur','auteur_mail','ts_datecreation','ts_datemodification');
        $requisq []='referentielc2i';
        $requisq []='alinea';

        $requisr=array('num','id','id_etab','bonne','reponse');

        foreach ($questions as $qcmitem) {
            $question=$qcmitem->question;
            $reponses=$qcmitem->reponses;
            $documents=$qcmitem->documents;
             $this->debug_output("question recue");
             $this->debug_output(print_r($qcmitem,true));

            unset($question->id_famille_validee);
            unset($question->qid);
            unset($question->error);
            $question->ts_dateenvoi=time();
            $question->etat=QUESTION_NONEXAMINEE; 

            foreach ($requisq as $r) {
                if (empty($question->$r)) {
                    $question->error=traduction ('ws_valeurmanquante',false, $r);
                    break;
                }
            }

            if (!empty($question->error)) {
                $rets[]=filter_question($client,$question);
                continue;
            }
            $qid=$question->id_etab.".".$question->id;
            $nbr=0;

            foreach($reponses as $reponse) {
                foreach ($requisr as $r) {
                    if (empty($reponse->$r))
                        $question->error=traduction('ws_valeurmanquante',false,$r);
                    break;
                }
                $nbr++;
                unset($reponse->qid);
                unset($reponse->error);
                unset($reponse->num); // autoincrement !!!
            }

            if (!empty($question->error)) {
                $rets[]=filter_question($client,$question);
                continue;
            }

            if ($nbr==0) {
                $question->error=traduction ('ws_questionsansreponse',false,$qid);
                $rets[]=filter_question($client,$question);
                continue;
            }

            if ($connue=get_question ($question->id,$question->id_etab,false)) {
                $connue->error=traduction ('ws_questiondejasoumise',false,$qid);
                $rets[]=filter_question($client,$connue);
                continue;
            }
            // rev 979 si on veut que les experts les voient
            // elles doivent �tre en certification
            $question->certification='OUI';
            // mais surtout pas dans les examens de positionnement sur la nationale (normalement c'est jamais car pas valid�e)
            $question->positionnement='NON';
            if ( insert_record("questions",$question,false,false,false)) {
                espion3("envoi", "question", $qid, $question); //rev 922

                foreach($reponses as $reponse) {
                    $this->debug_output("reponse ". print_r($reponse,true));
                    insert_record("reponses",$reponse,false,false,false);
                }

                //rev 956 reception des documents
                if ($documents)
                    foreach ($documents as $doc) {
                    $idf=$doc->id_doc.".".$doc->extension;
                    if (decode_document( $idf, $doc->base64,$question->id,$question->id_etab )) {
                        unset($doc->base64);
                        unset($doc->qid);
                        unset($doc->error);
                        insert_record("questionsdocuments",$doc,false,false,false);

                    }
                }



                $connue=get_question ($question->id,$question->id_etab,false);
                $connue->error="";
                $rets[]=filter_question($client,$connue);
            }else {
                $question->error=traduction ('ws_sqlerror',false, __FUNCTION__);
                $this->debug_output("erreur ". print_r($question,true));

                $rets[]=filter_question($client,$question);
            }

        }
        return $rets;
    }


   function envoi_examen ($client,$sesskey,$id_examen,$type_pf,$copies,$details) {
   	  global $CFG;

   	   if ($CFG->universite_serveur !=1)
		    return $this->error(traduction('ws_paslocallocal'));

	  if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
		    return $this->error(traduction('ws_invalidclient'));
	  if (!$this->verifie_droits($client, "c2i/examen:ajouter"))
		    return $this->error(traduction('ws_illegaloperation'));

	  //cr�� ou r�cupere l'examen global de remont�e pour ce type de plateforme
	  if (! $examen=get_examen_remontee($type_pf)) return false;
	  $clee=$examen->id_etab."_".$examen->id_examen;
	  foreach ($copies as $copie) {
	  	$copie->examen=$clee;
	  	$copie->origine="locale: ".$id_examen;
	  	if (! $id=insert_record("resultatsexamens",$copie,true,'id',false)) return false;
	  }
	  foreach ($details as $detail) {
	  	$detail->examen=$clee;

	  	//ajouter cette question � l'examen si pas d�ja dedans
	  	if ($question=get_question_byidnat($detail->question,false)) { //connue nationalement ?
	  		ajoute_question_examen($question->id,$question->id_etab,$examen->id_examen,$examen->id_etab,false);
	  	    if (! $id=insert_record("resultatsdetailles",$detail,true,'id',false)) return false;
	  	}
	  }
	  espion3("envoi", "examen", $id_examen,$examen); //rev 922
	 return true;
   }


     function get_corrige_examen_html($client,$sesskey,$userid,$idfield,$id_examen) {
     	 global $USER,$CFG;
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/examen:modifier"))
            return $this->error(traduction('ws_illegaloperation'));

        if (!$ex=get_examen_byidnat($id_examen,false))
			return $this->error(traduction ('ws_exameinconnu',false,$id_examen));


	    if (!$ex->correction)
	    	return $this->error(traduction ('ws_pasdecorrige',false,$id_examen));
	      $idq=$ex->id_examen;
	    $ide=$ex->id_etab;

        $CFG->afficher_score_question=1; // affiicher le score obtenu

	    if (empty($userid)){
	    	$mode=QCM_CORRIGE;
	    }else {
	    	//if ($etudiant=get_record("inscrits","$idfield='$userid'",false))  {// rev 929 peut chercher par login,email ou num
			 //rev 951 un prof peut passer un examen ...
            if (!$etudiant=get_compte_by($userid,$idfield,false))
                    return $this->error(traduction('ws_candidatinconnu',false,$idfield,$userid)) ;
            $userid=$etudiant->login;
			if (compte_passages($idq,$ide,$userid)>0)
				$mode=QCM_CORRECTION;
	    }
	    require_once( $CFG->chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates
	    $ret=imprime_examen($idq,$ide,false,false,false,false,$mode,$userid);
	    //return '<![CDATA['.$ret[0].']]>';
	    return  $ret[0];

    }

     function get_resultats_examen_html($client,$sesskey,$userid,$idfield,$id_examen) {
	     global $USER,$CFG;
	     if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
		     return $this->error(traduction('ws_invalidclient'));
	     if (!$this->verifie_droits($client, "c2i/examen:modifier"))
		     return $this->error(traduction('ws_illegaloperation'));

	     if (!$ex=get_examen_byidnat($id_examen,false))
		     return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

	    // if (!$etudiant=get_record("inscrits","$idfield='$userid'",false))  // rev 929 peut chercher par login,email ou num
		//rev 951 un prof peut passer un examen ...
          if (!$etudiant=get_compte_by($userid,$idfield,false))
             return $this->error(traduction ('ws_candidatinconnu',false,$idfield,$userid)) ;

	     $idq=$ex->id_examen;
	     $ide=$ex->id_etab;
	     $res=get_resultats($idq,$ide,$etudiant->login,false);  //relire depuis la BD
	     require_once( $CFG->chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates
	     if ($ex->template_resultat != "")
		     $resultats=affiche_template_resultats($res,$ex);
	     else
		     $resultats=affiche_resultats($res,$ex->resultat_mini,false ); //, $avecParcours);


	     return $resultats;
     }

    function get_parcours_examen_html($client,$sesskey,$userid,$idfield,$id_examen) {
    	 global $USER,$CFG;
        if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/examen:modifier"))
            return $this->error(traduction('ws_illegaloperation'));

        if (!$ex=get_examen_byidnat($id_examen,false))
			return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

		//if (!$etudiant=get_record("inscrits","$idfield='$userid'",false))  // rev 929 peut chercher par login,email ou num
		  //rev 951 un prof peut passer un examen ...
          if (!$etudiant=get_compte_by($userid,$idfield,false))
            return $this->error(traduction ('ws_candidatinconnu',false,$idfield,$userid)) ;


	    $idq=$ex->id_examen;
	    $ide=$ex->id_etab;
	     require_once( $CFG->chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates
	      $res=get_resultats($idq,$ide,$etudiant->login,false);  //relire depuis la BD
		return cree_parcours_HTML($ex,$res);
		}


      //rev 951  (interface Moodle)

      function est_inscrit_examen($client,$sesskey,$userid,$idfield,$id_examen) {
         if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/examen:modifier"))
            return $this->error(traduction('ws_illegaloperation'));

        if (!$ex=get_examen_byidnat($id_examen,false))
            return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

        //if (!$etudiant=get_record("inscrits","$idfield='$userid'",false))  // rev 929 peut chercher par login,email ou num
        //rev 951 un prof peut passer un examen ...
          if (!$etudiant=get_compte_by($userid,$idfield,false))
            return $this->error(traduction ('ws_candidatinconnu',false,$idfield,$userid)) ;
        $idq=$ex->id_examen;
        $ide=$ex->id_etab;
        $ret=est_inscrit_examen($idq,$ide,$etudiant->login);
        $this->debug_output($ret);
        return $ret>0;

      }

     function a_passe_examen($client,$sesskey,$userid,$idfield,$id_examen) {
         if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/examen:modifier"))
            return $this->error(traduction('ws_illegaloperation'));

        if (!$ex=get_examen_byidnat($id_examen,false))
            return $this->error(traduction ('ws_exameninconnu',false,$id_examen));

        //if (!$etudiant=get_record("inscrits","$idfield='$userid'",false))  // rev 929 peut chercher par login,email ou num
            //rev 951 un prof peut passer un examen ...
          if (!$etudiant=get_compte_by($userid,$idfield,false))
            return $this->error(traduction ('ws_candidatinconnu',false,$idfield,$userid)) ;
        $idq=$ex->id_examen;
        $ide=$ex->id_etab;
        return compte_passages($idq,$ide,$etudiant->login) >0;

      }

      // rev 974 clients smartphones vers examen anonyme
       function corrige_examen( $client,$sesskey,$idcandidat,$idfield,$idexamen,$listequestions,$listereponses) {

        global $USER;

       	  if (!$this->validate_client($client, $sesskey,__FUNCTION__,$idexamen))
            return $this->error(traduction('ws_invalidclient'));
          //on ne v�rifie pas les droits pour une correction anonyme

     //listequestions doit �tre l'info envoy�e par get_anonyme cad une chaine 1.1_1.2_ ....
    //listereponses doit �tre un tableau avec des cl�s idetab_id_question_idreponse si coch�e
    // ceci correspond a l'element id envoy� pour chaque r�ponse

          //conversion au format attendu par la noteuse
          $tablereponses=array();
          foreach( $listereponses as $id)
          		$tablereponses[$id]=1;

           $noteuse = new noteuseALaVolee($listequestions,$tablereponses);
    	   $res = $noteuse->note_etudiant($idcandidat);

    	   $ret=array();
		   if ($res->score_global !=-1) { // l'a passe ???

                        $res->ip_max=$USER->ip;   //calcul�e par c2i_params
                        $res->ts_date_max=time();
                        $res->origine=$res->ip_max.'@webservice';  // rev 978
                        /** TODO
                        $res->ts_date_min=$date_debut;
                        enregistre_resultats($examen->id_examen,$examen->id_etab,$user,$res);
                        **/

					//ajouter les notes par referentiel, competences, ou les 2
					$refs = array ();
					foreach ($res->tabref_score as $ref => $note) {
                          $tmp = new scoreRecord();
                        $tmp->setCompetence('D:'.$ref);
                        $tmp->setScore($note);

						//filtrage ????
						$refs[] = $tmp;
					}
					$comps = array ();
					foreach ($res->tabcomp_score as $comp => $note) {
						  $tmp = new scoreRecord();
                        $tmp->setCompetence('A:'.$comp);
                        $tmp->setScore($note);

						//filtrage ????
						$comps[] = $tmp;
					}

					$questions=array();
					foreach ($res->tab_points as $question => $note) {
                          $tmp = new scoreRecord();
                        $tmp->setCompetence('Q:'.$question);
                        $tmp->setScore($note);

						//filtrage ????
						$questions[] = $tmp;
					}
                    $tmp = new bilanDetailleRecord();
                    $tmp->setLogin($idcandidat);
                    //$tmp->setNumetudiant($etudiant->numetudiant);
                    $tmp->setScore($res->score_global);
                    $tmp->setExamen($idexamen);
                    $tmp->setIp($res->ip_max);
                    $tmp->setOrigine($res->origine);
                    $tmp->setDate($res->ts_date_max);
					$tmp->setDetails(array_merge($refs, $comps,$questions));
					$ret[] = $tmp;
				}else {
					$ret[]=$this->non_fatal_error(traduction ('ws_erreurcorrectionexamen'));
				}
			return $ret;



       }



 /**
 * ajout� revision 969 pour appel par Moodle
 */
    function get_passages_recents($client,$sesskey,$id_examen,$timestart) {
        //attention    server:: et pas $this-> qui remet $timestart � 0 !
       return server::get_notes_examen($client,$sesskey,$id_examen,$timestart);
    }



 /**
 * @param int $client
 * @param string $sesskey
 * @param string $id_examen
 * @param boolean $verouille
 * @return boolean
 */
   protected function do_verouille_examen ( $client,$sesskey,$id_examen,$verouille) {

      if (!$this->validate_client($client, $sesskey,__FUNCTION__,$id_examen))
            return $this->error(traduction('ws_invalidclient'));
        if (!$this->verifie_droits($client, "c2i/examen:modifier"))
            return $this->error(traduction('ws_illegaloperation'));

        if (!$ex=get_examen_byidnat($id_examen,false))
            return $this->error(traduction ('ws_exameninconnu',false,$id_examen));
        $tmp=_do_verouille_examen($ex->id_examen,$ex->id_etab,$verouille);
        $this->debug_output("verouillage ".$tmp);
        return $tmp;
   }




     /**
 * @param int $client
 * @param string $sesskey
 * @param string $tags
 * @return examenRecord[]
 */

   public function get_examens_bytags($client,$sesskey,$tags) {
        if (!$this->validate_client($client, $sesskey,__FUNCTION__))
            return $this->error(traduction('ws_invalidclient'));

        if (!$this->verifie_droits($client, "c2i/examen:consulter"))
            return $this->error(traduction('ws_illegaloperation'));

        return filter_examens($client,get_examens_bytags($tags));
   }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $tags
 * @return questionRecord[]
 */
    public function get_questions_bytags($client,$sesskey,$tags) {
            if (!$this->validate_client($client, $sesskey,__FUNCTION__))
            return $this->error(traduction('ws_invalidclient'));

        if (!$this->verifie_droits($client, "c2i/question:consulter"))
            return $this->error(traduction('ws_illegaloperation'));

               return filter_questions($client,get_questions_bytags($tags));
   }

   /**
 * @param int $client
 * @param string $sesskey
 * @param string $tags
 * @return inscritRecord[]
 */
   public function get_inscrits_bytags($client,$sesskey,$tags) {
        if (!$this->validate_client($client, $sesskey,__FUNCTION__))
            return $this->error(traduction('ws_invalidclient'));

        if (!$this->verifie_droits($client, "c2i/inscrit:consulter"))
            return $this->error(traduction('ws_illegaloperation'));
        return filter_inscrits($client,get_candidats_bytags($tags));
   }

/**
 * @param int $client
 * @param string $sesskey
 * @param string $tags
 * @return personnelRecord[]
 */
   public function get_utilisateurs_bytags($client,$sesskey,$tags) {
            if (!$this->validate_client($client, $sesskey,__FUNCTION__))
            return $this->error(traduction('ws_invalidclient'));

        if (!$this->verifie_droits($client, "c2i/personnel:consulter"))
            return $this->error(traduction('ws_illegaloperation'));
        return filter_utilisateurs($client,get_utilisateurs_bytags($tags));
   }






}
?>
