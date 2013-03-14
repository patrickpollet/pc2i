<?php
/**
 * c2i_soapserverrest class file
 * 
 * @author    Patrick Pollet :<patrick.pollet@insa-lyon.fr>
 * @copyright (c) P.Pollet 2007 under GPL
 * @package   MoodleWS
 */

define('DEBUG',true);
/**
 * bilanDetailleRecord class
 */
require_once 'bilanDetailleRecord.php';
/**
 * scoreRecord class
 */
require_once 'scoreRecord.php';
/**
 * inscritInputRecord class
 */
require_once 'inscritInputRecord.php';
/**
 * inscritRecord class
 */
require_once 'inscritRecord.php';
/**
 * examenInputRecord class
 */
require_once 'examenInputRecord.php';
/**
 * examenRecord class
 */
require_once 'examenRecord.php';
/**
 * personnelInputRecord class
 */
require_once 'personnelInputRecord.php';
/**
 * personnelRecord class
 */
require_once 'personnelRecord.php';
/**
 * affectRecord class
 */
require_once 'affectRecord.php';
/**
 * resultatExamenInputRecord class
 */
require_once 'resultatExamenInputRecord.php';
/**
 * resultatDetailleInputRecord class
 */
require_once 'resultatDetailleInputRecord.php';
/**
 * qcmItemRecord class
 */
require_once 'qcmItemRecord.php';
/**
 * documentRecord class
 */
require_once 'documentRecord.php';
/**
 * questionRecord class
 */
require_once 'questionRecord.php';
/**
 * reponseRecord class
 */
require_once 'reponseRecord.php';
/**
 * alineaRecord class
 */
require_once 'alineaRecord.php';
/**
 * etablissementRecord class
 */
require_once 'etablissementRecord.php';
/**
 * qcmRecord class
 */
require_once 'qcmRecord.php';
/**
 * familleRecord class
 */
require_once 'familleRecord.php';
/**
 * noteRecord class
 */
require_once 'noteRecord.php';
/**
 * referentielRecord class
 */
require_once 'referentielRecord.php';
/**
 * loginReturn class
 */
require_once 'loginReturn.php';

/**
 * c2i_soapserverrest class
		* the two attributes are made public for debugging purpose
		* i.e. accessing $client->client->__getLast* methods
 * 
 *  
 * 
 * @author    Patrick Pollet :<patrick.pollet@insa-lyon.fr>
 * @copyright (c) P.Pollet 2007 under GPL
 * @package   MoodleWS
 */
class c2i_soapserverrest {

	    private $serviceurl='';
		private $formatout='php';
	    private $verbose=false;
	    private $postdata='';
	    public $requestResponse='';


		/**
		 * Constructor method
		 * @param string $wsdl URL of the WSDL
		 * @param string $uri
		 * @param string[] $options  Soap Client options array (see PHP5 documentation)
		 * @return c2i_soapserverrest
		 */
  public function c2i_soapserverrest($serviceurl = "http://prope.insa-lyon.fr/c2i/V2/ws/service.php", $options = array()) {
     $this->serviceurl=$serviceurl;
      $this->verbose=! empty($options['trace']);
 		if (!empty($options['formatout']))
     			$this->setFormatout($options['formatout']);
  }


        
      private function castTo($className,$res){
        	// if ($this->formatout==='php') return $res;  //NO todo on client side
            if (class_exists($className)) {
                $aux= new $className();
                // rev V2 don't get extra fields returned by WS
                // and not anymore in our DB 
                /* 
                foreach ($res as $key=>$value)
                    $aux->$key=$value;
                */
                foreach ($aux as $key=>$tmp) 
                    if (isset($res->$key))
                        $aux->$key=$res->$key;
                return $aux;
             } else
                return $res;
        }  
        
         private function castToArray ($className,$res) {
           $aux=array();
            if (! is_array($res))
               $res=array($res);
           foreach ($res as $element)
               $aux[]=$this->castTo($className,$element);
           return $aux;
       } 
  
	function __call ($methodname, $params) {
		$params['wsformatout']=$this->formatout;
		$params['wsfunction']=$methodname;
          // forcing the separator to '&' is capital with some php version that use otherwise &amp;
        // in 'apache mode' but not in 'cli mode' and break parameter parsing on the server side ...
		$this->postdata = http_build_query($params,'','&');

		//print_r($this);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->serviceurl);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postdata);
		if ($this->verbose)
			curl_setopt($ch, CURLOPT_VERBOSE, true);
		$this->requestResponse = curl_exec($ch);
		//print_r("retour curl".$this->requestResponse);
		curl_close($ch);
		if ($methodname==='login') return $this->deserialize($this->requestResponse,'php');
		else return $this->deserialize($this->requestResponse);

	}



	function deserialize ($data,$formatout='') {
		$formatout=$formatout?$formatout:$this->formatout;
		switch ($formatout) {
			case 'xml':break;
			case 'json':break;
			case 'php':$data=unserialize($data); break;
			case 'dump':break;
		}
		return $data;
	}

	function getFormatout() {
		return $this->formatout;
	}

	function setFormatout($formatout='php') {
		if (empty($formatout)) $formatout='php';
		$this->formatout=$formatout;
	}

	function getPostdata() {
		return $this->postdata;
	}

	function getRequestResponse() {
		return $this->requestResponse;
	}


  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $userid
   * @param string $idfield
   * @param string $id_examen
   * @return boolean
   */
  public function a_passe_examen($client, $sesskey, $userid, $idfield, $id_examen) {
    $res= $this->__call('a_passe_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'userid'=>$userid,
            'idfield'=>$idfield,
            'id_examen'=>$id_examen
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $idcandidat
   * @param string $idfield
   * @param string $idexamen
   * @param string $listequestions
   * @param string[] $listereponses
   * @return bilanDetailleRecord[]
   */
  public function corrige_examen($client, $sesskey, $idcandidat, $idfield, $idexamen, $listequestions, $listereponses) {
    $res= $this->__call('corrige_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'idcandidat'=>$idcandidat,
            'idfield'=>$idfield,
            'idexamen'=>$idexamen,
            'listequestions'=>$listequestions,
            'listereponses'=>$listereponses
      ));
  return $this->castToArray ('bilanDetailleRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param inscritInputRecord $candidat
   * @return inscritRecord
   */
  public function cree_candidat($client, $sesskey, inscritInputRecord $candidat) {
    $res= $this->__call('cree_candidat', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'candidat'=>$candidat
      ));
  return $this->castTo ('inscritRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param inscritInputRecord[] $candidats
   * @return inscritRecord[]
   */
  public function cree_candidats($client, $sesskey, $candidats) {
    $res= $this->__call('cree_candidats', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'candidats'=>$candidats
      ));
  return $this->castToArray ('inscritRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param examenInputRecord $examen
   * @param int $id_etab
   * @return examenRecord
   */
  public function cree_examen($client, $sesskey, examenInputRecord $examen, $id_etab) {
    $res= $this->__call('cree_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'examen'=>$examen,
            'id_etab'=>$id_etab
      ));
  return $this->castTo ('examenRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param personnelInputRecord $personnel
   * @return personnelRecord
   */
  public function cree_personnel($client, $sesskey, personnelInputRecord $personnel) {
    $res= $this->__call('cree_personnel', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'personnel'=>$personnel
      ));
  return $this->castTo ('personnelRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @param string[] $candidats
   * @param string $idfield
   * @return affectRecord[]
   */
  public function desinscrit_examen($client, $sesskey, $id_examen, $candidats, $idfield) {
    $res= $this->__call('desinscrit_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen,
            'candidats'=>$candidats,
            'idfield'=>$idfield
      ));
  return $this->castToArray ('affectRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @return boolean
   */
  public function deverouille_examen($client, $sesskey, $id_examen) {
    $res= $this->__call('deverouille_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @param string $type_pf
   * @param resultatExamenInputRecord[] $copies
   * @param resultatDetailleInputRecord[] $details
   * @return boolean
   */
  public function envoi_examen($client, $sesskey, $id_examen, $type_pf, $copies, $details) {
    $res= $this->__call('envoi_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen,
            'type_pf'=>$type_pf,
            'copies'=>$copies,
            'details'=>$details
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param qcmItemRecord[] $questions
   * @return questionRecord[]
   */
  public function envoi_questions($client, $sesskey, $questions) {
    $res= $this->__call('envoi_questions', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'questions'=>$questions
      ));
  return $this->castToArray ('questionRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $userid
   * @param string $idfield
   * @param string $id_examen
   * @return boolean
   */
  public function est_inscrit_examen($client, $sesskey, $userid, $idfield, $id_examen) {
    $res= $this->__call('est_inscrit_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'userid'=>$userid,
            'idfield'=>$idfield,
            'id_examen'=>$id_examen
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_ref
   * @return alineaRecord[]
   */
  public function get_alineas($client, $sesskey, $id_ref) {
    $res= $this->__call('get_alineas', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_ref'=>$id_ref
      ));
  return $this->castToArray ('alineaRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @param int $type
   * @return bilanDetailleRecord[]
   */
  public function get_bilans_detailles_examen($client, $sesskey, $id_examen, $type) {
    $res= $this->__call('get_bilans_detailles_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen,
            'type'=>$type
      ));
  return $this->castToArray ('bilanDetailleRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @return bilanDetailleRecord[]
   */
  public function get_bilans_examen($client, $sesskey, $id_examen) {
    $res= $this->__call('get_bilans_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen
      ));
  return $this->castToArray ('bilanDetailleRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $userid
   * @param string $idfield
   * @param string $id_examen
   * @return string
   */
  public function get_corrige_examen_html($client, $sesskey, $userid, $idfield, $id_examen) {
    $res= $this->__call('get_corrige_examen_html', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'userid'=>$userid,
            'idfield'=>$idfield,
            'id_examen'=>$id_examen
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_question
   * @return documentRecord[]
   */
  public function get_documents($client, $sesskey, $id_question) {
    $res= $this->__call('get_documents', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_question'=>$id_question
      ));
  return $this->castToArray ('documentRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id
   * @return etablissementRecord
   */
  public function get_etablissement($client, $sesskey, $id) {
    $res= $this->__call('get_etablissement', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id'=>$id
      ));
  return $this->castTo ('etablissementRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param int $id_pere
   * @return etablissementRecord[]
   */
  public function get_etablissements($client, $sesskey, $id_pere) {
    $res= $this->__call('get_etablissements', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_pere'=>$id_pere
      ));
  return $this->castToArray ('etablissementRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id
   * @return examenRecord
   */
  public function get_examen($client, $sesskey, $id) {
    $res= $this->__call('get_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id'=>$id
      ));
  return $this->castTo ('examenRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $email
   * @return qcmRecord
   */
  public function get_examen_anonyme($client, $sesskey, $email) {
    $res= $this->__call('get_examen_anonyme', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'email'=>$email
      ));
  return $this->castTo ('qcmRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $typep
   * @param string $id_etab
   * @return examenRecord[]
   */
  public function get_examens($client, $sesskey, $typep, $id_etab) {
    $res= $this->__call('get_examens', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'typep'=>$typep,
            'id_etab'=>$id_etab
      ));
  return $this->castToArray ('examenRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $tags
   * @return examenRecord[]
   */
  public function get_examens_bytags($client, $sesskey, $tags) {
    $res= $this->__call('get_examens_bytags', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'tags'=>$tags
      ));
  return $this->castToArray ('examenRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $userid
   * @param string $idfield
   * @param string $typep
   * @return examenRecord[]
   */
  public function get_examens_inscrit($client, $sesskey, $userid, $idfield, $typep) {
    $res= $this->__call('get_examens_inscrit', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'userid'=>$userid,
            'idfield'=>$idfield,
            'typep'=>$typep
      ));
  return $this->castToArray ('examenRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @return familleRecord[]
   */
  public function get_familles($client, $sesskey) {
    $res= $this->__call('get_familles', array(
            'client'=>$client,
            'sesskey'=>$sesskey
      ));
  return $this->castToArray ('familleRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $userid
   * @param string $idfield
   * @return inscritRecord
   */
  public function get_inscrit($client, $sesskey, $userid, $idfield) {
    $res= $this->__call('get_inscrit', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'userid'=>$userid,
            'idfield'=>$idfield
      ));
  return $this->castTo ('inscritRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @return inscritRecord[]
   */
  public function get_inscrits($client, $sesskey, $id_examen) {
    $res= $this->__call('get_inscrits', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen
      ));
  return $this->castToArray ('inscritRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $tags
   * @return inscritRecord[]
   */
  public function get_inscrits_bytags($client, $sesskey, $tags) {
    $res= $this->__call('get_inscrits_bytags', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'tags'=>$tags
      ));
  return $this->castToArray ('inscritRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @return noteRecord[]
   */
  public function get_notes_examen($client, $sesskey, $id_examen) {
    $res= $this->__call('get_notes_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen
      ));
  return $this->castToArray ('noteRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $userid
   * @param string $idfield
   * @param string $id_examen
   * @return string
   */
  public function get_parcours_examen_html($client, $sesskey, $userid, $idfield, $id_examen) {
    $res= $this->__call('get_parcours_examen_html', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'userid'=>$userid,
            'idfield'=>$idfield,
            'id_examen'=>$id_examen
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @param int $timestart
   * @return noteRecord[]
   */
  public function get_passages_recents($client, $sesskey, $id_examen, $timestart) {
    $res= $this->__call('get_passages_recents', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen,
            'timestart'=>$timestart
      ));
  return $this->castToArray ('noteRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $userid
   * @param string $idfield
   * @return personnelRecord
   */
  public function get_personnel($client, $sesskey, $userid, $idfield) {
    $res= $this->__call('get_personnel', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'userid'=>$userid,
            'idfield'=>$idfield
      ));
  return $this->castTo ('personnelRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @return qcmRecord
   */
  public function get_qcm($client, $sesskey, $id_examen) {
    $res= $this->__call('get_qcm', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen
      ));
  return $this->castTo ('qcmRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id
   * @return questionRecord
   */
  public function get_question($client, $sesskey, $id) {
    $res= $this->__call('get_question', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id'=>$id
      ));
  return $this->castTo ('questionRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @return questionRecord[]
   */
  public function get_questions($client, $sesskey, $id_examen) {
    $res= $this->__call('get_questions', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen
      ));
  return $this->castToArray ('questionRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $tags
   * @return questionRecord[]
   */
  public function get_questions_bytags($client, $sesskey, $tags) {
    $res= $this->__call('get_questions_bytags', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'tags'=>$tags
      ));
  return $this->castToArray ('questionRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $typep
   * @return questionRecord[]
   */
  public function get_questions_obsoletes($client, $sesskey, $typep) {
    $res= $this->__call('get_questions_obsoletes', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'typep'=>$typep
      ));
  return $this->castToArray ('questionRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @return referentielRecord[]
   */
  public function get_referentiels($client, $sesskey) {
    $res= $this->__call('get_referentiels', array(
            'client'=>$client,
            'sesskey'=>$sesskey
      ));
  return $this->castToArray ('referentielRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_question
   * @return reponseRecord[]
   */
  public function get_reponses($client, $sesskey, $id_question) {
    $res= $this->__call('get_reponses', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_question'=>$id_question
      ));
  return $this->castToArray ('reponseRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $userid
   * @param string $idfield
   * @param string $id_examen
   * @return string
   */
  public function get_resultats_examen_html($client, $sesskey, $userid, $idfield, $id_examen) {
    $res= $this->__call('get_resultats_examen_html', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'userid'=>$userid,
            'idfield'=>$idfield,
            'id_examen'=>$id_examen
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $idcandidat
   * @param string $idfield
   * @param string $idexamen
   * @return bilanDetailleRecord[]
   */
  public function get_score_candidat($client, $sesskey, $idcandidat, $idfield, $idexamen) {
    $res= $this->__call('get_score_candidat', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'idcandidat'=>$idcandidat,
            'idfield'=>$idfield,
            'idexamen'=>$idexamen
      ));
  return $this->castToArray ('bilanDetailleRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $idcandidat
   * @param string $idfield
   * @param string $typep
   * @param int $consolid
   * @return bilanDetailleRecord[]
   */
  public function get_scores_candidat($client, $sesskey, $idcandidat, $idfield, $typep, $consolid) {
    $res= $this->__call('get_scores_candidat', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'idcandidat'=>$idcandidat,
            'idfield'=>$idfield,
            'typep'=>$typep,
            'consolid'=>$consolid
      ));
  return $this->castToArray ('bilanDetailleRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @return string
   */
  public function get_themeurl($client, $sesskey) {
    $res= $this->__call('get_themeurl', array(
            'client'=>$client,
            'sesskey'=>$sesskey
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $typep
   * @return questionRecord[]
   */
  public function get_toutes_questions($client, $sesskey, $typep) {
    $res= $this->__call('get_toutes_questions', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'typep'=>$typep
      ));
  return $this->castToArray ('questionRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $typep
   * @return qcmItemRecord[]
   */
  public function get_toutes_questions_et_reponses($client, $sesskey, $typep) {
    $res= $this->__call('get_toutes_questions_et_reponses', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'typep'=>$typep
      ));
  return $this->castToArray ('qcmItemRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $tags
   * @return personnelRecord[]
   */
  public function get_utilisateurs_bytags($client, $sesskey, $tags) {
    $res= $this->__call('get_utilisateurs_bytags', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'tags'=>$tags
      ));
  return $this->castToArray ('personnelRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @return string
   */
  public function get_version($client, $sesskey) {
    $res= $this->__call('get_version', array(
            'client'=>$client,
            'sesskey'=>$sesskey
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @param string[] $candidats
   * @param string $idfield
   * @param string $tags
   * @return affectRecord[]
   */
  public function inscrit_examen($client, $sesskey, $id_examen, $candidats, $idfield, $tags) {
    $res= $this->__call('inscrit_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen,
            'candidats'=>$candidats,
            'idfield'=>$idfield,
            'tags'=>$tags
      ));
  return $this->castToArray ('affectRecord',$res);
  }

  /**
   *  
   *
   * @param string $username
   * @param string $password
   * @return loginReturn
   */
  public function login($username, $password) {
    $res= $this->__call('login', array(
            'username'=>$username,
            'password'=>$password
      ));
  return $this->castTo ('loginReturn',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @return boolean
   */
  public function logout($client, $sesskey) {
    $res= $this->__call('logout', array(
            'client'=>$client,
            'sesskey'=>$sesskey
      ));
   return $res;
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $idexamen
   * @param examenInputRecord $examen
   * @return examenRecord
   */
  public function modifie_examen($client, $sesskey, $idexamen, examenInputRecord $examen) {
    $res= $this->__call('modifie_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'idexamen'=>$idexamen,
            'examen'=>$examen
      ));
  return $this->castTo ('examenRecord',$res);
  }

  /**
   *  
   *
   * @param int $client
   * @param string $sesskey
   * @param string $id_examen
   * @return boolean
   */
  public function verouille_examen($client, $sesskey, $id_examen) {
    $res= $this->__call('verouille_examen', array(
            'client'=>$client,
            'sesskey'=>$sesskey,
            'id_examen'=>$id_examen
      ));
   return $res;
  }

}

?>
