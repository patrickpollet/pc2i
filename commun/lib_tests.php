<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_tests.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * bibliotheque de tests. Fonction destin�e a �tre appel�e dans du code de test
 */

function cree_examen_test ($nom,$est_pool=false,$type_tirage=false,$ts_debut=false,$ts_fin=false){
	global $CFG,$USER;
	//existe d�ja ?
	if ($ex=get_record("examens","nom_examen='$nom'",false)) return $ex;

	$ligne=new StdClass();
	// standard
	$ligne->auteur=get_fullname($USER->id_user,false);
    $ligne->auteur_mail=get_mail($USER->id_user,false);
    $ligne->id_etab=$USER->id_etab_perso;
    $ligne->ts_datecreation=$ligne->ts_datemodification=time();
    $ligne->positionnement=$ligne->certification='NON';
    if ($USER->type_plateforme=="positionnement"){
    	 $ligne->positionnement='OUI';
    } else {
    	$ligne->certification='OUI';
    }
    $ligne->resultat_mini = $CFG->examen_seuil_validation;
	$ligne->ordre_q=$CFG->examen_ordre_questions_defaut;
	$ligne->ordre_r=$CFG->examen_ordre_reponses_defaut;

    //personnalisation
    $ligne->nom_examen=$nom;
    $type_tirage=$type_tirage?$type_tirage:$CFG->examen_type_tirage_defaut;
    $ligne->type_tirage=$type_tirage;
    if ($est_pool) {
		$ligne->est_pool=1;
		$ligne->nb_q_pool= $CFG-> pool_nb_questions_defaut;
		$ligne->pool_nb_groupes=$CFG->pool_nb_groupes_defaut;

	}
	$ligne->ts_datedebut=mktime( $CFG->examen_heure_debut_defaut, $CFG->examen_minute_debut_defaut,0, (int) date('m'), (int) date('d') + $CFG->examen_date_defaut, (int) date('Y'));
    $ligne->ts_datefin=$ligne->ts_datedebut+3600*$CFG->examen_duree_defaut;  // 1 heure par d�faut
	$idq=insert_record("examens",$ligne,true,'id_examen');

	if ($type_tirage==EXAMEN_TIRAGE_ALEATOIRE)
		 tirage_questions ($idq,$ligne->id_etab);
	return get_examen($idq,$ligne->id_etab);

}


function cree_candidat_test ($login,$etab=false,$numetud,$nom,$prenom,$mail=false,$auth=false) {
	global $USER,$CFG;
	//existe ?
	if ($ret=get_candidat($login,false)) return $ret;


	$ligne=new StdClass();
	if (!$auth) {
		$ligne->auth="manuel";
		$ligne->password= mot_de_passe_a($CFG->longueur_mot_de_passe_aleatoire);
	}else {
		$ligne->auth=$auth;
	}


	$ligne->login=$login;
	$ligne->nom=$nom;
	$ligne->prenom=$prenom;
	$ligne->numetudiant=$numetud;
	if ($mail) $ligne->email=$mail;
    $ligne->origine='test';
	$id=cree_candidat($ligne,$etab);
	return get_candidat($login);
}


