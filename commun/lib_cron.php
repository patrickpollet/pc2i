<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_cron.php 728 2009-04-23 10:28:05Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
require_once ($CFG->chemin_commun."/lib_ldap.php");
/*
 * taches de maintenance
 * n'est pas charg�es dans les pages courantes
 * utiliser require_once quand n�cessaire
 */
 if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_cron();
 }

 function maj_bd_cron () {
	 global $CFG,$USER;
 }


/**
 * suppression des inscrits qui ne sont pas inscrits � un examen
 */

function compte_candidats_non_inscrits($ide){
    global $CFG;

    if (!is_admin()) v_d_o_d("config");
    $requete = "select count(login) as NB FROM {$CFG->prefix}inscrits WHERE etablissement='$ide' and login not in (select distinct login from {$CFG->prefix}qcm)";
    if ($res=get_record_sql($requete,false))
        return  $res->NB;
    else return 0;

    //TODO voir les notes ???
}

function purge_candidats_non_inscrits($ide){
	global $CFG;

	if (!is_admin()) v_d_o_d("config");
	$requete = "delete FROM {$CFG->prefix}inscrits WHERE etablissement='$ide' and login not in (select distinct login from {$CFG->prefix}qcm)";
	ExecRequete ($requete);


	//TODO voir les notes ???
}



/**
 * supprime les comptes qui ne sont plus dans l'annuaire LDAP'
 * TODO
 */
function purge_comptes_plus_ldap() {
	/*
	 * algo
	 * pour tous les comptes etudiants +profs connus
	 * 		si plus dans ldap ET ldap OK !!!
	 * 			virer les resultats si �tudiants
	 * 			virer les inscriptions
	 * 			virer du tracking ???
	 * puis appel a purge_non_inscrits ...
	 *
	 */

}

/**
 * synchronise les infos interne avec le LDAP
 */
function synchro_comptes_ldap() {
	/*
	 * algo
	 * si ldap vivant
	 * 	pour touts les comptes (profs+etudiants)
	 * 	    synchro_compte_avec_ldap($compte) ; //ce qui se fait normalement a la connexion
	 */

}
