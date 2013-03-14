<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_tracking.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/*
 * bibliotheque de manipulations du tracking
 */
 //if (is_admin()) {   //ne PAS tester ca c'est trop tot !
    maj_bd_tracking();
 //}

    function maj_bd_tracking () {
	    global $CFG,$USER;

    }


// v <1.4
function espion($action, $login, $id_objet, $type_utilisateur, $objet, $connexion=false){

    /////////////////////////////////////////
    // fonction de tracking de tout ce qui se fait sur l'application
    // $action est l'action d'ajouter, supprimer, modifier ...
    // $id_objet est l'identifiant de l'objet $objet (question, examen, ...) sur lequel l'utilisateur $login agit
    // $type_utilisateur est le type de l'utilisateur qui fait l'action
    /////////////////////////////////////////
    global $USER,$CFG;

    $ligne=new StdClass();
    //parametres
    $ligne->action=$action;
    $ligne->login=$login;
    $ligne->id_objet=$id_objet;
    $ligne->type_utilisateur=$USER->type_user;  //on ignore $type_utilisateur
    $ligne->objet=$objet;
    // ajout systematique
    $ligne->date=time();
    $ligne->plateforme=$USER->type_plateforme;

    $ligne->ip=$USER->ip;
    $ligne->etablissement=$USER->id_etab_perso;
   // print_r($ligne);
    insert_record("tracking",$ligne,false,'id_tracking',false);  //pas d'erreur fatale si echec IMPORTANT aux 1er lancement

}

//V2
function espion2($action,$objet,$id_objet) {
    global $USER;
    //rev 978 passage en UTF8
    // si l'action/ objet est une cl� de traduction et est traduite, la traduire
    // sinon la prendre telle quelle
    @espion (traduction_cond($action),$USER->id_user,$id_objet,false,traduction_cond($objet));

}

// rev 922
function espion3($action,$objet,$id_objet,$instance) {
    espion2($action,$objet,$id_objet);
    //TODO declencher une action sp�cifique ex mail aux experts ...
	//a ce jour espion3 est appel�e pour
	//     objet=question action=ajout,envoi,validation,invalidation,suppression
    //     rev 922 aussi par remont�e d'une locale via le web service
	//     rev 948 objet=examen depuis admin des examens
    // rev 1073 modif d'une question
    if (!event_trigger($objet,$action,$id_objet,$instance))
        espion2('bug events',$objet.'_'.$action,$id_objet); //bug a signaler aux developpeurs
}





?>
