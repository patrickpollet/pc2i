<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_acces.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque de manipulations des l'entités utilisateurs, inscrits et etablissement
 */

/**
 * partie retouche BD
 * rev 1.4--> 1.41
 */

 if (is_admin()) {   //a faire systematiquement NON blocage dans l'installeeur !

    maj_bd_acces();
 }

 function maj_bd_acces () {
  /**
 * bug decouvert le 16/05/2011 la mise a jour des tables inscrits et utilisateuirs DOIT
 * se faire dans lib_auth.php  et pas ici car lorsque l'on inclus lib_access les droits
 * ne sont pas encore connu ... (c'est elle qui les lit !!!) donc is_admùin() renvoie FAUX
 */
  // NE RIEN FAIRE ICI
 }


// context definitions  non utilisées en V 1.5
define('CONTEXT_SITE', 10);
define('CONTEXT_ETABLISSEMENT', 20);
define('CONTEXT_EXAMEN', 30);
define('CONTEXT_QUESTION', 40);


/**
* ces deux fonctions vont remplacer v_d_o_d
* voir la table c2icapacites
* @todo si pas logguÃ© aucune capacitÃ© sauf le positionnement anonyme
*/

function get_context($id,$contextType) {
	global $USER;
	return $USER->id_etab_perso; //tempo
}



function capacite_requise( $capa,$context=false, $id=false) {
	global $USER;
	if (!$id) $id=$USER->id_user;

	if (! a_capacite($capa,$context,$id))
		erreur_fatale(traduction ("err_droits"));
	return true;
}


/**
 * teste une capacité dans un contexte
 * pour l'instant le contexte est un établissement
 * TODO gerer les composantes par héritage
 */

function a_capacite( $capa,$context=false, $id=false) {
	global $USER;
	if (!$id) $id=$USER->id_user;
	if (!$id)  return false; //pas logé rien

	if (! $context) $context=$USER->id_etab_perso;
	if (! $context)  return false;
    if (is_admin($id,$context)) return true;  //pour cet etablissement et ses composantes
    if ($context !=$USER->id_etab_perso) return false;  // pas d'héritage encore sur les autres droits
	if (isset($USER->droits['row_droits']->$capa))
		return $USER->droits['row_droits']->$capa;
	else erreur_fatale ("DEV:err_droit_inconnu",$capa)	;
}




/**remplace l'ancien include/commun/droits.php
 * a lire une fois a chaque entrée sur une page
 *
 */

function lecture_droits ($context=false) {
	global $USER,$CFG;
	// global $row_droits,$row_admin; //compatibilté V <1.41 en attendant gros ménage des tests des droits dans les pages FINI le 20/04/2009 svn 714

	$row_droits= new StdClass();
	$row_admin= new StdClass();

	if (!empty($USER->id_user)) {

		// vérification des droits
		$sql = "select est_superadmin, est_admin_univ, limite_positionnement from {$CFG->prefix}utilisateurs where login='".addslashes($USER->id_user)."'";

		if ($res= get_record_sql($sql,false)) $row_admin=$res;

		$sql_droits = "SELECT  max(`q_ajouter`) as qa , max(`q_modifier`) as qm , max(`q_supprimer`) as qs ";
		$sql_droits .= ", max(`q_dupliquer`) as qd , max(`q_lister`) as ql ";
		$sql_droits .= ", max(`ex_ajouter`) as ea , max(`ex_modifier`) as em , max(`ex_dupliquer`) as ed , max(`ex_lister`) as el , max(`ex_supprimer`) as es ";
		$sql_droits .= ", max(`q_valider`) as qv , max(`acces_tracking`) as at , max(`etudiant_ajouter`) as eta , max(`etudiant_modifier`) as etm, max(`etudiant_lister`) as etl , max(`etudiant_supprimer`) as ets ";
		$sql_droits .= ", max(`resultats_afficher`) as ra , max(`utilisateur_ajouter`) as ua , max(`utilisateur_modifier`) as um , max(`utilisateur_lister`) as ul ";
		$sql_droits .= ", max(`utilisateur_supprimer`) as us , max(`plc_telecharger`) as plct , max(`plp_telecharger`) as plpt , max(`banquedd_telecharger`) as bddt , max(`configurer`) as config ";
		$sql_droits .= " FROM  {$CFG->prefix}droits D  , {$CFG->prefix}profils P";
		$sql_droits .= " WHERE D.id_profil = P.id_profil AND login = '".addslashes($USER->id_user)."'";

		if($res=get_record_sql($sql_droits)) $row_droits=$res;
	}

     $USER->droits['row_admin']=$row_admin;
     $USER->droits['row_droits']=$row_droits;

}

//vérifie qu'un droit (ex. "ql","em")  est bien dans la tableau global des
// droit de l'utilisateur courant  version 1.4

//define ('DEBUG_DROITS',1);
function teste_droit( $droit) {
    global $ide,$idexe; //l'etablissement du "machin" concerné" relu par required_param (pas terrible !)
    global $USER,$CFG;

    //manipulation des questions : $idexe a priorité sur $ide !
    //$ide n'est pas toujours renseigné !
    if (!isset($ide))
        $ide=$USER->id_etab_perso;

    //etablissement visé peut etre different (exemple une question a valider
    // il FAUT corriger ca ....)
    if (isset($idexe)) $etab=$idexe;
    else $etab=$ide;
    if (!isset($USER->droits)) return false; // un étudiant ...

    $etab_perso=$USER->id_etab_perso;
    $row_droits=$USER->droits['row_droits'];
    $row_admin=$USER->droits['row_admin'];


     if (defined('DEBUG_DROITS'))
        $CFG->debug_droits=<<<EOR
            usr=$USER->id_user  v=$USER->verif
            ESA: {$row_admin->est_superadmin}
            ELA: {$row_admin->est_admin_univ}
            etab.  visé $ide  ou $idexe = $etab
            etab. perso $USER->id_etab_perso
            droit requis $droit et a {$row_droits->$droit}
EOR;


   if (is_super_admin()) return true;
   if (is_admin($USER->id_user,$etab))  return true;

   if (empty($row_droits)) return false;
   // il doit avoir ce droit
   // cas particulier avec ql et el ( tout etablissement)
   return (($row_droits->$droit==1) && ($etab==$etab_perso)) || ($droit=="ql" || $droit=="el");

}
// vérifie droit or die !
// à inserer au début de chaque page et fichier inclus
function v_d_o_d ($droit) {
    global $USER;
    if (! teste_droit($droit)) {
        @espion2 ("err_acces",$_SERVER['PHP_SELF'],$USER->id_user);
        erreur_fatale ("err_acces");
    }

}

/**
 * renvoie les méthodes d'authentification dans un tableau d'objets
 * prêt à l'emploi dans un select
 * en ajouter si nécessaire et adapter la fonction lib_auth.php:authentifie_compte($compte,$passe)
 * rev 936 on teste si LDAP est pertinent pour cet établissement
 * rev 937 ajout option webservices si compte de type "personnel"
 */
function get_auth_methodes ($ide=false,$estPersonnel=false) {
    global $CFG;
    $ret= array();
    $ret[]=new option_select ('manuel','manuel');
    require_once($CFG->chemin_commun."/lib_ldap.php");
    if (auth_ldap_init($ide))
        $ret[]=new option_select ('ldap','CAS / ldap');
    if ($estPersonnel && !$CFG->ws_disable)
        $ret[]=new option_select ('webservice','Web Services');
    return $ret;
}


/**
 * rev 1022 type d'annauires LDAP supporté
 */
function get_ldap_annuaires() {
       $ret= array();
    $ret[]=new option_select ('rfc2307bis','Open LDAP');
    $ret[]=new option_select ('ad','Active Directory');
    $ret[]=new option_select ('edir','Novell eDirectory');
    return $ret;
}
/**
 * renvoie le nom de l'annuaire
 */
function get_ldap_annuaire_courant($cle) {
    $tmp=get_ldap_annuaires();
    foreach ($tmp as $a)
        if ($a->id==$cle) return $a->texte;
    return 'inconnu';
}



/**
 * @param string $login
 * @param string $die 	si vrai declenche une erreur fatale en cas d'erreur sinon return null
 * @return objet 		la ligne extraite de la BD ou null en cas d'erreur (et $die=0)
 */
function get_utilisateur ($login,$die=1) {

	if ($ret=get_record("utilisateurs","login='".addslashes($login)."'",$die,"err_compte_inconnu",$login))
		$ret->type_user="P";
	return $ret;
}

/**
 * @param string $login
 * @param string $die 	si vrai declenche une erreur fatale en cas d'erreur sinon return null
 * @return objet 		la ligne extraite de la BD ou null en cas d'erreur (et $die=0)
 */
function get_inscrit ($login,$die=1) {
	if( $ret=get_record("inscrits","login='".addslashes($login)."'",$die,"err_compte_inconnu",$login)){
		if(is_utilisateur_anonyme($login))
			$ret->type_user="A";
		else $ret->type_user="E";
	}
	return $ret;
}

/**
 * alias de get_inscrit (je prefere ce terme ...)
 */
function get_candidat ($login,$die=1) {
	return get_inscrit($login,$die);
}



/**
 * rev 944  simplificationb et appel par le WS (ex profs ayant passés un examen possible)
 * trouve un compte en BD et ajoute lui son type (dans lattente d'une meilleure gestion des droits)')
 */
function get_compte_by($valeur,$colonne,$die=0) {

    $valeur=addslashes($valeur); // rev 984 au cas ou c'est un login

    if ($ret=get_record("utilisateurs",$colonne."='".$valeur."'",false,"err_compte_inconnu",$colonne."=".$valeur)) {
        $ret->type_user="P";
        return $ret;
    }
    if ($ret=get_record("inscrits",$colonne."='".$valeur."'",$die,"err_compte_inconnu",$colonne."=".$valeur)) {
        if(is_utilisateur_anonyme($ret->login))
            $ret->type_user="A";
        else $ret->type_user="E";
        return $ret;
    }
    return false;
}


/**
 * trouve un compte en BD et ajoute lui son type (dans lattente d'une meilleure gestion des droits)')
 *
 */
function get_compte($login,$die=1) {
    return get_compte_by($login,"login",$die);
}

/**
 * trouve un compte en BD et ajoute lui son type (dans lattente d'une meilleure gestion des droits)')
 */
function get_compte_byemail($email,$die=0) {
   return get_compte_by($email,"email",$die);

}

/**
 * trouve un compte en BD et ajoute lui son type (dans lattente d'une meilleure gestion des droits)')
 */
function __get_compte_byidnumber($idnumber,$die=1) {
   return get_compte_by($idnumber,"numetudiant",$die);
}

/**
 * rev 969 attention sur la nationale !
 */
function get_compte_byidnumber($idnumber,$die=1) {
    global $CFG,$USER;

    if ($CFG->universite_serveur !=1)
        return __get_compte_byidnumber($idnumber,$die);

    $critere= "numetudiant='$idnumber' and etablissement=".$USER->id_etab_perso;
    if ($ret=get_record("utilisateurs",$critere,false)) {
        $ret->type_user="P";
        return $ret;
    }
    if ($ret=get_record("inscrits",$critere,$die,"err_compte_inconnu",$critere)) {
    if(is_utilisateur_anonyme($ret->login))
            $ret->type_user="A";
        else $ret->type_user="E";
        return $ret;
    }
    return false;

}


/**
 * rev 1077 ne pas inclure les anonymes
 */

function get_candidats($critere=false, $tri=false) {

    if (!$critere) $critere="1";
    $critere .= " and not login like 'ANONYME%' ";
    if (!$tri) $tri="nom,prenom asc";
    return  get_records("inscrits",$critere,$tri);
}


function count_candidats ($ide) {
	return count(get_candidats("etablissement=$ide"));
}

function get_utilisateurs ($critere=false, $tri=false) {

    if (!$critere) $critere="1";
    if (!$tri) $tri="nom,prenom asc";
    return  get_records("utilisateurs",$critere,$tri);
}




function get_candidats_bytags($tags,$sort='',$page=0, $recordsperpage=-1, &$totalcount=null) {
    return search_table_bytags('inscrits',$tags, $sort, $page, $recordsperpage,$totalcount);
}


function get_utilisateurs_bytags($tags,$sort='',$page=0, $recordsperpage=-1, &$totalcount=null) {
    return search_table_bytags('utilisateurs',$tags, $sort, $page, $recordsperpage,$totalcount);
}


function get_admin ($ide=false) {
    global $USER;
    if (empty($ide)) $ide=$USER->id_etab_perso;
    $ret=get_records("utilisateurs","etablissement=$ide and est_admin_univ='O'");
    if ($ret) return $ret[0];
    else return false;

}


/**
 * rev 948 ajout champ optionel passwordmd5 pour synchro Moodle ou autre via WS
 * NB: dans ce cas l'authentification doit en tenire compte (voir lib_auth.php/authentifie_compte)'
 *
 */
function cree_candidat ($ligne, $ide=false) {
    global $USER,$CFG;

    if (empty($ligne->login)) return false;
    if (!$ide) $ide=$USER->id_etab_perso;
    $ligne->ts_datecreation=$ligne->ts_datemodification=time();
    //rev 921 valeurs par défaut pour appel par le web service
    if (empty($ligne->etablissement))$ligne->etablissement=$ide;
    //rev 948
    if (!empty($ligne->passwordmd5)) {
	    $ligne->password=$ligne->passwordmd5;

    }else {
	    if (empty($ligne->password))
		    $ligne->password=mot_de_passe_a($CFG->longueur_mot_de_passe_aleatoire);
    }
    unset($ligne->passwordmd5);
	if (empty($ligne->auth))$ligne->auth="manuel";

    espion3("ajout","etudiant", $ligne->login,$ligne); //rev 883

    //attention la table c2iinscrits n'a pas de numéro automatique
    //donc si on demande returnid (3eme paramétre) ca renvoie 0 false !!!!
    return insert_record("inscrits",$ligne,false,'',false );// pas e fatale si echec


}

function update_candidat ($ligne,$espion=true) {
    if (empty($ligne->login)) return false;
    $ligne->ts_datemodification=time();
    //tracking :
    if ($espion)
        espion3("modification","etudiant", $ligne->login,$ligne); // rev 883
    return update_record("inscrits",$ligne,'login',false,false);  //pas trop grave si ca echoue
}


function supprime_candidat ($supp_id) {
      global $CFG;
	  v_d_o_d("ets");

	  if (est_inscrit_examen(false,false,$supp_id)==0  || is_utilisateur_anonyme($supp_id)) {
            //ses notes
            //TODO selon un CFG garder ses résultats pour les stats examens mais pas son historique !
            //BIZARRE CE CODE si il n'est inscrit a rien
            // comment peut-on retrouver ces examens pour les purger ?????
            // car on a pu le desinscrire "a la main" ou c'est un anonyme que l'on peut supprimer'
            $exams=get_examens_inscrits($supp_id,'id');
            require_once($CFG->chemin_commun."/lib_resultats.php");  // non chargée par c2i_params
			foreach($exams as $exam) {
			   purge_resultats_inscrit($exam->id_examen,$exam->id_etab,$supp_id,$avecHistorique=true);
			}
            //ses parcours
			if ($CFG->utiliser_notions_parcours) { // rev 984 attention si lib_nions a été chargée
				$parcs=get_parcours_utilisateur($supp_id);
				foreach($parcs as $p)
				supprime_parcours($p->id_parcours);
			}
            //ses inscriptions
             delete_records("qcm","login='" . addslashes($supp_id)."'"); // rev 984
            // suppression de l'utilisateur
            delete_records("inscrits","login='" . addslashes($supp_id)."'"); // rev 984
            //tracking :
            espion3("suppression","candidat", $supp_id,null);
        }
}


function supprime_utilisateur ($supp_id) {
    global $CFG;
	  v_d_o_d("ets");
	// suppression des attachements de profils
		delete_records("droits","login='" . addslashes($supp_id) . "'"); // rev 984

		//rev 948 si un "prof" peut passer un QCM
		$exams=get_examens_inscrits($supp_id,'id');
		require_once($CFG->chemin_commun."/lib_resultats.php");  // non chargée par c2i_params
		foreach($exams as $exam) {
			purge_resultats_inscrit($exam->id_examen,$exam->id_etab,$supp_id,$avecHistorique=true);
		}
		//ses parcours
		if ($CFG->utiliser_notions_parcours) { // rev 984 attention si lib_nions a été chargée
			$parcs=get_parcours_utilisateur($supp_id);
			foreach($parcs as $p)
			supprime_parcours($p->id_parcours);
		}
		//ses inscriptions
		delete_records("qcm","login='" .addslashes($supp_id)."'"); // rev 984


		// suppression de l'utilisateur
		delete_records("utilisateurs","login='" . addslashes($supp_id) . "'");  // rev 984
		//tracking :
		espion3("suppression","personnel",$supp_id,null);
}

/**
 * ajouté revision 962
 * supprime un utilisateur d'un profil
 */

function supprime_utilisateur_profil ($idprofil,$login) {
    global $CFG;
    delete_records("droits",'id_profil='.(int)$idprofil." and login='".addslashes($login)."'"); // rev 984
    espion3("suppression_profil","personnel",$idprofil.' '.$login,null);
}


function cree_utilisateur ($ligne, $ide=false) {
    global $USER,$CFG;

    if (empty($ligne->login)) return false;
    if (!$ide) $ide=$USER->id_etab_perso;
    $ligne->ts_datecreation=$ligne->ts_datemodification=time();
      //rev 921 valeurs par défaut pour appel par le web service
    if (empty($ligne->etablissement))$ligne->etablissement=$ide;
	if (empty($ligne->password)){
	 	$ligne->password=mot_de_passe_a($CFG->longueur_mot_de_passe_aleatoire);
	    $ligne->password=md5($ligne->password);
	 }
	 if (empty($ligne->auth))$ligne->auth="manuel";

    //tracking :
    espion3("ajout","personnel", $ligne->login,$ligne); //rev 883
    return insert_record("utilisateurs",$ligne,'',false,false);// pas e fatale si echec

}

function update_utilisateur ($ligne,$espion=true) {
    if (empty($ligne->login)) return false;
    $ligne->ts_datemodification=time();
    if ($espion)
        espion3("modification","personnel", $ligne->login,$ligne); //rev 883
    update_record("utilisateurs",$ligne,'login',false,true);  //pas trop grave si ca echoue
}



/**
 * @access private
 * retourne la concaténation du nom et du prénom de la personne ayant pour login $login
 * selon les régles de configuration
 */

function _regle_nom_prenom ($nom,$prenom){
	global $CFG;

$prenom=mkjoli($prenom);

if ( $CFG->regle_nom_en_majuscule)
			$nom=strtoupper($nom);
		else
			//$nom=strtolower($nom);
            $nom =mkjoli($nom);
		if ( $CFG->regle_nom_prenom==1)
			return ucfirst( $nom)." ".ucfirst($prenom);
		else
			return ucfirst($prenom." ".ucfirst( $nom));
}

/**
 * essaie d'appliquer la régle nom prénom a un nom complet
 * d'auteur tel qu'écrit dans les tables questions et examens
 */

function applique_regle_nom_prenom($auteur){ // rev 841

    $tmp=explode(" ",$auteur);
    if (count($tmp) !=2 ) return $auteur; //raté ou nom composé ex Bruno Le Berre


    return _regle_nom_prenom ($tmp[0],$tmp[1]);

}

/**
 * le parametre connexion est resté pour la comat V 1.4
 * il n'est pas utilisé'
 */
function nom_user($login,$die=1){
	global $CFG;
	//
	if ($e=get_utilisateur($login,$die))
			return _regle_nom_prenom($e->nom,$e->prenom) ;
	else return "";
}

function nom_inscrit($login,$die=1){

	if ($e=get_inscrit($login,$die))
		return _regle_nom_prenom($e->nom,$e->prenom) ;
	else return "";
}

/**
 * tant que les deux tables c2iinscrits et céiutilisataurs n'ont pas été fusionnées
 * passer par ici
 * TODo passer par get_xxxx et virer le global connexion
 */
function get_fullname($login,$die=0) {
	if ($m=nom_user($login,false)) return $m;
	if ($m=nom_inscrit($login,$die)) return $m;
	return "";
}

function mel_user($login,$die=1){
	if ($e=get_utilisateur($login,$die))
	   return isset($e->email)?$e->email:"";
    else
        return false;

}

function mel_inscrit($login,$die=1){
    if ($e=get_inscrit($login,$die))
	   return isset( $e->email)?$e->email:"";
    else
        return false;
}

/**
 * tant que les deux tables c2iinscrits et c2iutilisateurs n'ont pas été fusionnées
 * passer par ici
 *  * TODo passer par get_xxxx et virer le global connexion
 */
function get_mail($login,$die=0) {
	if ($m=mel_user($login,false)) return $m;
	if ($m=mel_inscrit($login,$die)) return $m;
	return "";
}



/**
 * @param string $id_profil
 * @param string $die   si vrai declenche une erreur fatale en cas d'erreur sinon return null
 * @return objet        la ligne extraite de la BD ou null en cas d'erreur (et $die=0)
 */
function get_profil ($id,$die=1) {
    return get_record("profils","id_profil=".(int)$id,$die,"err_profil_inconnu",$id);
}


function get_profils ($tri='intitule',$die=0) {
    return get_records("profils","",$tri,0,0,$die,"err_aucun_profil_defini","????");
}


/**
 * @param string $login
 * @param string $die 	si vrai declenche une erreur fatale en cas d'erreur sinon return null
 * @return objet 		la ligne extraite de la BD ou null si pas de profils
 */
function get_profils_utilisateur ($login,$tri='intitule',$die=0) {
    global $CFG;

   $loginsl=addslashes($login); // rev 984
   $sql =<<<EOR
SELECT P.* FROM {$CFG->prefix}droits D,{$CFG->prefix}profils P
WHERE D.login='$loginsl'
AND P.id_profil=D.id_profil
order by $tri
EOR;
	return get_records_sql($sql,$die,"","");
}


function get_utilisateurs_avec_profil($id,$tri='nom,prenom',$die=0) {
    global $CFG;
    $id=(int)$id;
	$sql=<<<EOS
	select U.*
	from {$CFG->prefix}utilisateurs as U, {$CFG->prefix}profils as P, {$CFG->prefix}droits as D
	where U.login=D.login and D.id_profil=P.id_profil
	and P.id_profil=$id
	order by $tri
EOS;
	return get_records_sql($sql,$die);
}


function supprime_profil ($supp_id) {
	 // double vérification que le profil n'est pas utilisé
        if (!get_utilisateurs_avec_profil($supp_id)) {
            	// suppression du profil
            	delete_records("profils","id_profil=" . (int)$supp_id,true,"err_suppression_profil",$supp_id);
            //tracking :
            espion3("suppression","profil",$supp_id,null);
        }
}

/**
 * renvoie un soustemplate pr pour u n assignInclude()
 * est appelé par la fiche d'un profil et pour rappeler les profils connus (ajout personnel)'
 * completer sin on ajoute des capacités a un profil
 */
function profil_en_table() {
	$res=<<<EOF

          <tr>
            <th>{question}</th>
            <td>
            	<table class="sansbordure">
              	<tr>
                	<td><img src="{chemin_images}/case{q_ajouter}.gif" alt="" />{ajouter}</td>
                	<td><img src="{chemin_images}/case{q_modifier}.gif" alt="" />{modifier}</td>

                	 <td><img src="{chemin_images}/case{q_lister}.gif" alt="" />{lister}</td>

                	<td><img src="{chemin_images}/case{q_supprimer}.gif" alt="" />{supprimer}</td>
                	<td><img src="{chemin_images}/case{q_dupliquer}.gif" alt="" />{dupliquer}</td>
                 	<td><img src="{chemin_images}/case{q_valider}.gif" alt="" />{valider}</td>
              </tr>
            </table>
            </td>
          </tr>
          <tr>
            <th>{examen}</th>
            <td><table class="sansbordure">
              <tr>
                <td><img src="{chemin_images}/case{ex_ajouter}.gif" alt="" />{ajouter}</td>
                <td><img src="{chemin_images}/case{ex_modifier}.gif" alt="" />{modifier}</td>


                <td><img src="{chemin_images}/case{ex_lister}.gif" alt="" />{lister}</td>

                <td><img src="{chemin_images}/case{ex_supprimer}.gif" alt="" />{supprimer}</td>
                  <td><img src="{chemin_images}/case{ex_dupliquer}.gif" alt="" />{dupliquer}</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <th>{etudiant}</th>
            <td><table class="sansbordure">
              <tr>
                <td><img src="{chemin_images}/case{etudiant_ajouter}.gif" alt="" />{ajouter}</td>
                 <td><img src="{chemin_images}/case{etudiant_modifier}.gif" alt="" />{modifier}</td>

                <td><img src="{chemin_images}/case{etudiant_lister}.gif" alt="" />{lister}</td>
                <td><img src="{chemin_images}/case{etudiant_supprimer}.gif" alt="" />{supprimer}</td>
              </tr>
            </table>
         </td>
       </tr>
       <tr>
          <th>{utilisateur}</th>
          <td><table class="sansbordure">
              <tr>
                <td><img src="{chemin_images}/case{utilisateur_ajouter}.gif" alt="" />{ajouter}</td>
                <td><img src="{chemin_images}/case{utilisateur_modifier}.gif" alt="" />{modifier}</td>
                <td><img src="{chemin_images}/case{utilisateur_lister}.gif" alt="" />{lister}</td>
                <td><img src="{chemin_images}/case{utilisateur_supprimer}.gif" alt="" />{supprimer}</td>
              </tr>
            </table>
          </td>
       </tr>
       <tr>
         <th>{telecharger}</th>
         <td><table class="sansbordure">
              <tr>
                <td><img src="{chemin_images}/case{plc_telecharger}.gif" alt="" />{plc}</td>

                <td><img src="{chemin_images}/case{plp_telecharger}.gif" alt="" />{plp}</td>

                <td><img src="{chemin_images}/case{banquedd_telecharger}.gif" alt="" />{bdd_qcm}</td>
             </tr>
            </table>
         </td>
       </tr>
        <tr>
            <th>{autres_droits}</th>
            <td><table class="sansbordure">
              <tr>
                <td><img src="{chemin_images}/case{configurer}.gif" alt="" />{config}</td>

                <td><img src="{chemin_images}/case{acces_tracking}.gif" alt="" />{track} {conf_actif}</td>

                <td><img src="{chemin_images}/case{resultats_afficher}.gif" alt="" />{result}</td>
              </tr>
            </table>
         </td>
      </tr>
EOF;
	return $res;
}


function profil_en_inputs() {
    $res=<<<EOF
      <tr>
            <th>{question}</th>
            <td><table  class="sansbordure">
              <tr>
                <td><input type="checkbox" value="1" name="q_ajouter" {ch_q_ajouter}/>
                {ajouter}</td>
                <td><input type="checkbox" value="1" name="q_modifier" {ch_q_modifier}/>
                {modifier}</td>
                <td><input type="checkbox" value="1" name="q_dupliquer" {ch_q_dupliquer}/>
                {dupliquer}</td>
              </tr>
              <tr>
                <td><input type="checkbox" value="1" name="q_lister" {ch_q_lister}/>
                {lister}</td>
                <td><input type="checkbox" value="1" name="q_valider" {ch_q_valider}/>
                {valider}</td>
                <td><input type="checkbox" value="1" name="q_supprimer" {ch_q_supprimer}/>
                {supprimer}</td>
              </tr>
            </table>            </td>
          </tr>
          <tr>
            <th>{examen}</th>
            <td><table class="sansbordure">
              <tr>
                <td><input type="checkbox" value="1" name="ex_ajouter" {ch_ex_ajouter}/>{ajouter}</td>
                <td><input type="checkbox" value="1" name="ex_modifier" {ch_ex_modifier}/>{modifier}</td>
                <td><input type="checkbox" value="1" name="ex_dupliquer" {ch_ex_dupliquer}/>{dupliquer}</td>
              </tr>
              <tr>
                <td><input type="checkbox" value="1" name="ex_lister" {ch_ex_lister}/>{lister}</td>
                <td>&nbsp; </td>
                <td><input type="checkbox" value="1" name="ex_supprimer" {ch_ex_supprimer}/>{supprimer}</td>
              </tr>
            </table>            </td>
          </tr>
          <tr>
            <th>{etudiant}</th>
            <td><table  class="sansbordure">
              <tr>
                <td><input type="checkbox" value="1" name="etudiant_ajouter" {ch_etudiant_ajouter}/>{ajouter}</td>
                <td><input type="checkbox" value="1" name="etudiant_modifier" {ch_etudiant_modifier}/>{modifier}</td>
                <td><input type="checkbox" value="1" name="etudiant_lister" {ch_etudiant_lister}/>{lister}</td>
                <td><input type="checkbox" value="1" name="etudiant_supprimer" {ch_etudiant_supprimer}/>{supprimer}</td>
              </tr>
            </table>            </td>
          </tr>
          <tr>
            <th>{utilisateur}</th>
            <td><table  class="sansbordure">
              <tr>
                <td><input type="checkbox" value="1" name="utilisateur_ajouter" {ch_utilisateur_ajouter}/>{ajouter}</td>
                <td><input type="checkbox" value="1" name="utilisateur_modifier" {ch_utilisateur_modifier}/>{modifier}</td>
                <td><input type="checkbox" value="1" name="utilisateur_lister" {ch_utilisateur_lister}/>{lister}</td>
                <td><input type="checkbox" value="1" name="utilisateur_supprimer" {ch_utilisateur_supprimer}/>{supprimer}</td>
              </tr>
            </table>            </td>
          </tr>

          <tr>
            <th>{telecharger}</th>
            <td><table class="sansbordure">
              <tr>
                <td><input type="checkbox" value="1" name="plc_telecharger" {ch_plc_telecharger}/>{plc}</td>
              </tr>
              <tr>
                <td><input type="checkbox" value="1" name="plp_telecharger" {ch_plp_telecharger}/>{plp}</td></tr><tr>
                <td><input type="checkbox" value="1" name="banquedd_telecharger" {ch_banquedd_telecharger}/>{bdd_qcm}</td></tr>
            </table>            </td>
          </tr>
          <tr>
            <th>{autres_droits}</th>
            <td><table class="sansbordure">
              <tr>
                <td><input type="checkbox" value="1" name="configurer" {ch_configurer}/>{config}</td></tr><tr>
                <td><input type="checkbox" value="1" name="acces_tracking" {ch_acces_tracking}/>{track} (nécessite que configuration soit actif)</td></tr><tr>
                <td><input type="checkbox" value="1" name="resultats_afficher" {ch_resultats_afficher}/>{result}</td></tr>
            </table>            </td>
          </tr>

EOF;
    return $res;
}


/**
 * coche ou non les cases du profil en table renvoyé ci-dessus
 * completer sin on ajoute des capacités a un profil
 */
function garni_table_profil($tpl,$ligne) {
	//si on avait le bonne idée de mettre les meme noms aux balises
// alors un tpl->assignObject($ligne) devrait faire !  FAIT rev 962
/***
$tpl->assign("ch_a_q", $ligne->q_ajouter);
$tpl->assign("ch_m_q", $ligne->q_modifier);
$tpl->assign("ch_d_q", $ligne->q_dupliquer);
$tpl->assign("ch_l_q", $ligne->q_lister);
$tpl->assign("ch_s_q", $ligne->q_supprimer);
$tpl->assign("ch_v_q", $ligne->q_valider);
$tpl->assign("ch_a_e", $ligne->ex_ajouter);
$tpl->assign("ch_m_e", $ligne->ex_modifier);
$tpl->assign("ch_d_e", $ligne->ex_dupliquer);
$tpl->assign("ch_l_e", $ligne->ex_lister);
$tpl->assign("ch_s_e", $ligne->ex_supprimer);
$tpl->assign("ch_track", $ligne->acces_tracking);
$tpl->assign("ch_config", $ligne->configurer);
$tpl->assign("ch_result", $ligne->resultats_afficher);
$tpl->assign("ch_plc", $ligne->plc_telecharger);
$tpl->assign("ch_plp", $ligne->plp_telecharger);
$tpl->assign("ch_bdd", $ligne->banquedd_telecharger);
$tpl->assign("ch_a_et", $ligne->etudiant_ajouter);
$tpl->assign("ch_m_et", $ligne->etudiant_modifier);
$tpl->assign("ch_l_et", $ligne->etudiant_lister);
$tpl->assign("ch_s_et", $ligne->etudiant_supprimer);
$tpl->assign("ch_a_u", $ligne->utilisateur_ajouter);
$tpl->assign("ch_m_u", $ligne->utilisateur_modifier);
$tpl->assign("ch_l_u", $ligne->utilisateur_lister);
$tpl->assign("ch_s_u", $ligne->utilisateur_supprimer);
**/

/**
$tpl->assignGlobal("a",traduction("ajouter"));
$tpl->assignGlobal("m",traduction("modifier"));
$tpl->assignGlobal("s",traduction("supprimer"));
$tpl->assignGlobal("d",traduction("dupliquer"));
$tpl->assignGlobal("l",traduction("lister"));
$tpl->assignGlobal("v",traduction("valider"));

$tpl->assign("bdd",traduction("bdd_qcm"));
**/

$tpl->assignObjet($ligne);
}




/** recherche du numero anonyme suivant
 * donc ne pas appeler deux fois pour un même compte !
 */
function no_anonyme(){
	global $CFG;
	$sql = "SELECT lpad(max(right(login, 6))+1, 7, '0') max FROM {$CFG->prefix}inscrits where login like 'ANONYME%' ";
	$res = ExecRequete ($sql);
	$row = LigneSuivante($res);
	if ($row && $row->max == "") return "0000001";
	else return $row->max;
}



/**
 * cree un compte anonyme dans l'établissement $ide
 * @param $ide l'établissement de ratachment du compte (normalement celui de l'examen associé)
 * @param $email adresse de courriel éventuelle
 * TODO verifier qu'on ne peut pas se connecter avec ce compte .... '
 *
 **/

function cree_compte_anonyme($ide,$email='') {
	global $CFG;
	$num=no_anonyme();
	$cpt=new StdClass();
	$cpt->login="ANONYME".$num;
	$cpt->nom =traduction("candidat")." ".traduction("anonyme");
	$cpt->prenom="";
	$cpt->email=$email;
	$cpt->etablissement=$ide;
    $cpt->ts_datecreation=$cpt->ts_datemodification=$cpt->ts_connexion=$cpt->ts_derniere_connexion=time();

    $cpt->auth='anonyme';
	insert_record("inscrits",$cpt,true,'id');
	return $cpt;
}



///////////////////////////////////////////////////////////////// fin V 1.5

///////////////////////////////////////////////////////////////// debut V 1.4 a revoir

//revision 962 simplification des éditions des profils


class profil {
   var $intitule="";
   var $q_ajouter = 0;
   var $q_modifier=0;
   var $q_dupliquer=0;
   var $q_lister=0;
   var $q_supprimer=0;
   var $q_valider=0;
   var $ex_ajouter=0;
   var $ex_modifier=0;
   var $ex_dupliquer=0;
   var $ex_lister=0;
   var $ex_supprimer=0;
   var $acces_tracking=0;
   var $configurer=0;
   var $resultats_afficher=0;
   var $plc_telecharger=0;
   var $plp_telecharger=0;
   var $banquedd_telecharger=0;
   var $etudiant_ajouter=0;
   var $etudiant_modifier=0;
   var $etudiant_lister=0;
   var $etudiant_supprimer=0;
   var $utilisateur_ajouter=0;
   var $utilisateur_modifier=0;
   var $utilisateur_lister=0;
   var $utilisateur_supprimer=0;
}




function evt_utilisateur_modification ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_utilisateur_ajout ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_utilisateur_suppression ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_utilisateur_connexion ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

function evt_utilisateur_deconnexion ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

function evt_etudiant_modification ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_etudiant_ajout ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_etudiant_suppression ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_etudiant_connexion ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_etudiant_deconnexion ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

function evt_profil_modification ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_profil_ajout ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_profil_suppression ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


?>
