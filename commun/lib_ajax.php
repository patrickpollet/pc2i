<?php
/**
 * @author Patrick Pollet
 * @version $Id: lib_ajax.php 1215 2011-03-11 17:45:36Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  * a inclure dans tout script ajax appel� via prototype.js
  */

/**
 * important Prototype travaille en UFT8 par d�faut
 */

header( "'Content-Type: text/xml; charset=". $CFG->encodage."'" );

//header( 'Content-Type: text/xml; charset=ISO-8859-1' );
//header( 'Content-Type: text/xml; charset=UTF8' );

/**
 * si pas appel� par Ajax depuis le client rien
 * ca limite pas mal les tentatives car une r�quete ajax ne peut
 * �tre dirig�e que vers le serveur qui a lanc� la page qui la porte
 * desactiver pour des tests...
 *
 */

if (! @$_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") die_erreur("bug");

/*
 * version r�duite de c2iparam pour appel ajax (plus rapide)
 * TODO
 **/
 if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_ajax();
 }

 function maj_bd_ajax () {
	 global $CFG,$USER;
 }


 function envoi_erreur ($cletrad,$extra=false){
	 print " <span class='rouge'>".traduction($cletrad)." " .$extra."</span><br/>";
 }

 function envoi_ok ($cletrad,$extra=false){
	 print " <span class='vert'>".traduction($cletrad)." " .$extra."</span><br/>";
 }

 function envoi_bof ($cletrad,$extra=false){
	 print " <span class='orange'>".traduction($cletrad)." " .$extra."</span><br/>";
 }


/**
 * fin de traitement renvoie OK
 */
function die_ok($silencieux=false) {
    if (!$silencieux)
        echo "{ \"result\":\"ok\" }";
    die();
}

/**
 * fin de traitement avec erreur
 */
function die_erreur ($msg) {
    echo "{ \"result\":\"erreur .$msg.\" }";
     die();
}


?>
