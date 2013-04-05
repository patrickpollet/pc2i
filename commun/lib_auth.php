<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_auth.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque de manipulations de l'authentification'
*/

if (is_admin()) {   // toujours pour l'instant
maj_bd_auth();
}


function maj_bd_auth () {
    global $CFG;

        
      
}



/**
 * authentifie une compte (personnel ou etudiant selon sa m�thode d'authentifaction)
 * @param $compte objet extrait de la BD via get_compte donc existe !
 * @param $passe  mot de passe en clair
*/
function authentifie_compte($compte,$passe) {
	global $CFG,$USER;
	if (! $compte || ! $compte->auth || ! $compte->type_user ) return false;

	//rev 976 gestion du passwordsalt pour les comptes autocr��s depuis Moodle
    $md5=md5($passe);
	if (!empty($CFG->moodlepasswordsalt))
    $md5ps=md5($passe,$CFG->moodlepasswordsalt);
	else
    $md5ps=$md5;
/**
print_object('CFG',$CFG);
print_object('CPT',$compte);
print ('md5='.$md5);
print ('md5ps='.$md5ps);
die();
**/

	switch ($compte->auth) {
		case "manuel":
		 	if 	($compte->type_user=="P") return ($md5==$compte->password || $md5ps==$compte->password);
		 	// rev 948 :cas d'un compte autocr�� via le web service (mdp recu en md5)'
		 	if ($compte->type_user=="E") return ( $passe==$compte->password || $md5==$compte->password || $md5ps==$compte->password);
		 	if ($compte->type_user=="A") return true;
			return false;
			break;
		case "ldap":
			require_once($CFG->chemin_commun."/lib_ldap.php");
            // avec la PF on peut avoir un ldap different par �tablissement !
            // donc le mettre en m�moire pour une initialisation correcte des parsm LDAP !
    $USER->id_etab_perso=$compte->etablissement; // important

            if (auth_user_login ($compte->login,$passe)) {
	            if ($CFG->synchro_infos_ldap_a_la_connexion) {
    $cpt= auth_get_userinfo_asobj($compte->login); // va le chercher dans ldap au BON format !
                    // rev 978 pas de ligne dans le tracking ( etablissement pas encore connu)
		            if  ($compte->type_user=="P") update_utilisateur($cpt,false);
		            else if ($compte->type_user=="E") update_candidat($cpt,false);
	            }
	            return true;
            }else return false;
            break;
         case "webservice":
         if (!defined('ACCES_WEBSERVICE')) return false; // appel sans passer par server.class.php
         // rev 984 un candidat pourra aussi passer par le web service ... (attention aux droits)
         if 	($compte->type_user=="P") return ($md5==$compte->password || $md5ps==$compte->password);
		 if ($compte->type_user=="E") return ( $passe==$compte->password || $md5==$compte->password || $md5ps==$compte->password);
         return false;
            break;
         /***
          * ajouter d'autres appels de "plugins" ici '
          */

		default :return false;
	}
}

/**
 *  mise � jour des date de derni�re connexion et de connexion actuelle
 *  remplissage de la globale $USER
 */

function maj_info_connexion ($compte,$verif, $typepf) {
global $USER;
    $ligne=new StdClass();
    $ligne->login=$compte->login;
    $now=time();
    $ligne->ts_connexion=$now;
    $ligne->ts_derniere_connexion= $now;
 //   $ligne->connexion=date("Y-m-d H:i:s",$now);
 //   $ligne->derniere_connexion=date("Y-m-d H:i:s",$now);

	if ($compte->type_user=='P')
         update_record("utilisateurs",$ligne,"login","",true);
    else if ($compte->type_user=='E' || $compte->type_user="A")
         update_record("inscrits",$ligne,"login","",true);
	else
        erreur_fatale ("err_type_user_inconnu",$compte->type_user);

    $USER->id_user=$compte->login;
    $USER->type_plateforme =$typepf;
    $USER->id_etab_perso=$compte->etablissement;
    $USER->type_user=$compte->type_user;
    $USER->auth=$compte->auth;
    $USER->verif=$verif;
    $USER->derniere_connexion=$now;   // gard�e en cache
	// tracking
    if ($compte->type_user=='P') // rev 1027 test type utilisateur pour bonne info tracking
	   espion3("connexion", "utilisateur",$compte->login,$USER);
    else
        espion3("connexion", "etudiant",$compte->login,$USER);
}

function maj_info_deconnexion ($login) {
global $USER;
	// tracking
    if ($USER->type_user=='P')
	    espion3("deconnexion", "utilisateur",$login,$USER);
    else 
        espion3("deconnexion", "etudiant",$login,$USER);
	unset ($USER);
}

/**
 * rev 978
 * @param string $email
 * @return string  vide si OK  sinon un message d'erreur � traduire
 */
function valide_acces_anonyme ($email) {
    global $CFG;
    // si pas de controle et adresse vide, c'est OK
    if (empty($CFG->anonyme_controle_adresse_mail)&& trim($email)=='')
        return '';
    if (!is_valid_email($email)) return 'err_mail_invalide';
    switch ( $CFG->anonyme_controle_adresse_mail) {
        case 2:
            if (! get_compte_byemail($email)) return 'err_mail_inconnu_pf';
            break;
        case 3:
            require_once($CFG->chemin_commun.'/lib_ldap.php');
            if (!ldap_get_compte_byemail($email,$CFG->universite_serveur)) return 'err_mail_inconnu_ldap';
            break;
    }
    return '';
}



/**
 * rev 986
 * renvoie la liste des plages IP declar�es pour cet �tablissement
 */ 
 
function get_plages_ip_declarees ($id_etab='',$tri='nom') {
    global $CFG,$USER;
    if (empty($id_etab))
        $id_etab=$USER->id_etab_perso;
    return get_records('plagesip','',$tri);
}

function get_plage ($id,$die=1) {
    return get_record('plagesip', 'id='.$id,$die,"err_plage_inconnu" ,$id);
}


function ajoute_plage ($ligne,$die) {
    $ligne->ts_datecreation=$ligne->ts_datemodification = time();
    if ($id=insert_record("plagesip",$ligne,true,'id',$die))
        espion3("ajout","plage",$id,$ligne);
    else espion2("err_ajout_plage","plage",$ligne->nom);
    return $id;
}

function supprime_plage ($id,$die=1) {
    $ligne=get_plage($id,$die);  
    delete_records("plagesip","id=$id");
    espion3("suppression", "plage",$id , $ligne);
}


/**
* rev 986 retrouve les ips de la liste $liste
* utilis� pour des controle d'acc�s aux qcm par ip
* ex get_ips_liste('1,2','nom',1)
* @return array 
*/
function get_ips_liste ($liste='',$tri='nom',$die=0) {
    global $CFG,$USER;
    // cas de c2iexamen.referentielc2i vide ou valeur par d�faut (-1)
    if (empty($liste) || $liste==-1) 
        return array();
    return get_records('plagesip','id in ('. $liste.')', $tri,0,0,$die,"err_pas_de_plages_ip","????");
}

/**
 * rev 986 
 * @param string $liste (liste d'ids tels que stock� dans le champ subnet d'un examen ex 1,5,8 
 * @param unknown_type $tri
 * @param unknown_type $die
 * ^return string ex 1.2.2.,1.2,134.214.152/20 ...
 */
function get_ips_liste_csv ($liste='',$tri='nom',$die=0) {
    $res=get_ips_liste($liste,$tri,$die);
    if (! $res) return '';
    $tmp='';
    $i=0;
    foreach ($res as $ip) {
        if ($i >0)
            $tmp .=',';
        $tmp .=$ip->adresses;
        $i++;
    }
    return $tmp;
}

/**
 * renvoie la liste des id examen utilisant la plage $id
 * Enter description here ...
 * @param unknown_type $id
 */
function get_examens_utilisant_plage ($id) {
    global $USER;
    $ret=array();
    $examens=get_examens($USER->id_etab_perso,'id_examen',true, false);
    foreach ($examens as $examen) {
        $tmp=explode(',',$examen->subnet);
        if (in_array($id,$tmp))
            $ret[]=$examen->id; // id national unique
    }
    return $ret;
}



function evt_plage_modification ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_plage_ajout ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}


function evt_plage_suppression ($data) {
    dump_event(__FUNCTION__,$data);
    return true;
}

