<?php

/**
 * @author Patrick Pollet
 * @version $Id: lib_telechargements.php 1272 2011-10-17 14:24:58Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */



/**
 * bibliotheque de manipulations des téléchargements
 * ne pas diffuser aux locales
 */
 if (is_admin()) {   //utilisateur courant uniquement
    maj_bd_telec();
 }

 function maj_bd_telec () {
     global $CFG,$USER;

      add_config ("pfc2i","cmd_zip","/usr/bin/zip","/usr/bin/zip",$description='',1,$validation="required",$drapeau=1);

      add_config ("nationale","nombre_questions_a_envoyer_pos",-1,-1,$description='',1,$validation="required",$drapeau=1);
      add_config ("nationale","nombre_questions_a_envoyer_cert",-1,-1,$description='',1,$validation="required",$drapeau=1);

 }

//tests

$CFG->cmd_zip="/usr/bin/zip";
$CFG->nombre_questions_a_envoyer_pos=-1;
$CFG->nombre_questions_a_envoyer_cert=-1;



/**
 * génération des SQL qui vont bien à partir du code de phpmyadmin
 */




/**
 * Increase time limit for script execution and initializes some variables
 */
@set_time_limit(0);

$GLOBALS=array();
$GLOBALS['strAddClause']='test';
$GLOBALS['charset']=$CFG->encodage;
$GLOBALS['strDumpingData']='contenu de la table :';
$GLOBALS['strTableStructure']='structure de la table :';
$GLOBALS['sql_drop_table']=1;
//$GLOBALS['sql_if_not_exists']=1;
//$GLOBALS['sql_auto_increment']=1;

$crlf="\n";
$sql_drop_table=1;

require_once("phpmyadmin/mini.lib.php");  // version très très réduite des fonctions communes de phpmyadmin

require_once("phpmyadmin/sql.php");       // version inchangée de libraries/export/sql.php (sauf les vues)
require_once("phpmyadmin/sqlparser.lib.php"); //inchangée

// Start with empty buffer
$dump_buffer = '';
$dump_buffer_len = 0;


/**
 * on force en latin tant que nous ne sommes pas en UTF8
 */
 function PMA_getDbCollation($db) {
 	global $CFG;
 	if ($CFG->encodage="iso-8859-1") return 'latin1_swedish_ci';
 	else return 'utf8_general_ci';
 }

/**
 * Output handler for all exports, if needed buffering, it stores data into
 * $dump_buffer, otherwise it prints thems out.
 *
 * @param   string  the insert statement
 *
 * @return  bool    Whether output suceeded
 */
function PMA_exportOutputHandler($line){
	global $dump_buffer;

   // We export as html - replace special chars
    //echo htmlspecialchars($line);
    //nous on le garde comme une chaine propre en l'ajoutant au buffer
    $dump_buffer .=$line;

    return TRUE;
} // end of the 'PMA_exportOutputHandler()' function

function gen_create_table ($tablename) {
	global $dump_buffer,$ines_base;
	$dump_buffer = '';
 	PMA_exportStructure($ines_base, $tablename, "\n", false, false, true, false, false, 'create_table', 'db');
 	return $dump_buffer;
}


/**
 * genere les php nécessaire a lancer une requete sur le client
 * @param $bavard si true emet une instruction d'impression du $sql
 */

function gen_php_sql ($sql,$bavard=false) {
     $sql=preg_replace('/[^(\x20-\xFF)]*/','', $sql);
     $print_sql=$bavard ? "print '$sql';print '<br/>';" : "";

	$php=<<<EOP
	\$sql=<<<EOS
	   $sql
EOS;
    $print_sql
    mysql_query (\$sql, \$connexion) or die(mysql_error());

EOP;
	return $php;

}

function gen_insert_data ($tablename,$local_query=false) {
	global $dump_buffer,$ines_base;
	$dump_buffer = '';
	if (! $local_query)$local_query='SELECT * FROM ' . PMA_backquote($ines_base) . '.' . PMA_backquote($tablename);
    PMA_exportData($ines_base, $tablename, "\n", false, $local_query);
    return $dump_buffer;
}


/**
 * ne pas appeler ! c'est à m'admin local de créer sa BD avec les bons droits
 */
function gen_db_create ($db){
	global $dump_buffer;
	$dump_buffer = '';
	PMA_exportDBCreate($db);
	return $dump_buffer;
}

/**
 * enleve de l'image extraite de subversion les choses
 * qui ne doivent pas descendre sur les locales
 */
function menage_locale($destination, $typepf) {
	@unlink($destination."/commun/constantes.php");
	supprimer_dossier($destination."/codes/nationale");
	supprimer_dossier($destination."/installation/old");
	supprimer_dossier($destination."/tests");
    @unlink($destination."/installation/initbase.php");  // on va le recréer en install et pas besoin en maj
    //supprimer_dossier($destination."/ws");    //tempo
    @unlink($destination."/codes/locale/fr.php");  // pas touche à leurs modifs
    @unlink($destination."/codes/locale/config.php");  // pas touche à leurs modifs

    if ($typepf=="pc") {

    }


	if ($typepf=="p") {
		@unlink($destination."/index.php");
		@unlink($destination."/certification.php");
        @unlink($destination."/cas/certification.php");

	}
	if ($typepf=="c") {
		@unlink($destination."/index.php");
		@unlink($destination."/positionnement.php");
        @unlink($destination."/cas/positionnement.php");
		@unlink($destination."/anonyme.php");
     }
    /**
     * le dossier installation me contient que maj.php et deux lib_maj
     */
    if ($typepf=="maj") {
        @unlink($destination."/installation/index.php");
        @unlink($destination."/installation/installer.php");
        supprimer_dossier($destination."/ressources");
     /**
      * le dossier installation ne contient pas les scripts de mises à jour
      */
    } else {
           @unlink($destination."/installation/maj.php");
           @unlink($destination."/installation/lib_maj13_14.php");
           @unlink($destination."/installation/lib_maj14_15.php");
    }

}



/**
 * rev 1026 7/02/2010
 * personnaisation messagess et logos selon la plateforme
 * les modeles de logos sont dans themes/v14/logos
 * les modèle d e messages dans langues/c2in*.php
 */
function personnalise_plateforme($destination,$typepf) {
    global $CFG;
    //fichiers de langues spécifique à la plate-forme
    @copy($destination.'/langues/'.$CFG->c2i.'.php',$destination.'/langues/plateforme.php');
    @copy($destination.'/langues/'.$CFG->c2i.'_utf8.php',$destination.'/langues/plateforme_utf8.php');


    //virer les modèles (c2i1.php, c2i2*.php et les versions utf8
    foreach (glob("$destination/langues/c2i*.php") as $filename) {
       unlink($filename);
    }
    // logos  logo02.gif et certificat.gif
     @copy($destination."/themes/{$CFG->theme}/logos/logo01_{$CFG->c2i}.gif",
          $destination."/themes/{$CFG->theme}/images/logo01.gif");

    @copy($destination."/themes/{$CFG->theme}/logos/logo02_{$CFG->c2i}.gif",
          $destination."/themes/{$CFG->theme}/images/logo02.gif");

    @copy($destination."/themes/{$CFG->theme}/logos/certificat_{$CFG->c2i}.gif",
          $destination."/themes/{$CFG->theme}/images/certificat.gif");

    @copy($destination."/themes/{$CFG->theme}/logos/favicon_{$CFG->c2i}.ico",
          $destination."/themes/{$CFG->theme}/favicon.ico");

    //virer les modèles de logos
    supprimer_dossier($destination."/themes/{$CFG->theme}/logos");
}

/**
 * creation du fichier de constantes personnalisé
 * ne surtout pas utiliser un template car
 * va virer les balises non renseignées ici
 */
function gen_constantes($destination,$modele) {
    global $CFG,$USER;

    $src =$destination."/commun/".$modele;
    $dest =$destination."/commun/constantes.php";


	$str = @file_get_contents($src);
	if ($str) {
		// v 1.5 pas de personnalisation selon $USER dans constantes.php
		@file_put_contents($dest,$str);
       // @chmod($dest,"666"); //avec un peu de chance restera aussi  après decompression ...
	}

}

if  (0) {
	print gen_db_create('test');
	print gen_create_table("c2iquestions");
	print gen_insert_data("c2iquestions");
}

?>
