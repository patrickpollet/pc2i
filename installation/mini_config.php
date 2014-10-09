<?php
/**
 * @author Patrick Pollet
 * @version $Id: mini_config.php 1225 2011-03-16 18:37:54Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  * valeurs suffisantes pour l'installeur fonctionne sans passer par c2i_params
  * qui vérifie trop de choses ... et eliminer un maxi de notices php
  */



$CFG=new StdClass();
//valeur non relues depuis la BD car changeant a chaque fois
$CFG->chemin=$chemin; //relatif
$CFG->chemin_templates=$CFG->chemin."/templates2";
$CFG->chemin_commun=$chemin."/commun";

//pas de th�me peut �tre valeur par d�faut
$CFG->chemin_theme=$chemin."/themes/v15";
$CFG->chemin_images=$chemin."/themes/v15/images";

// rev 1.41 on travaille en absolu !
//valeur pas d�faut si pas chang�e dans c2iconfig
$CFG->chemin_ressources=dirname(realpath($chemin."/index.php"))."/ressources";

//chemin absolu vers la base de l'installation
$CFG->dirroot=realpath($chemin);

if (isset($_SERVER['HTTP_REFERER'])) {
	//calcul de l'URL de la plateforme
	$pu=parse_url($_SERVER['HTTP_REFERER']);
	$tmp=add_slash_url($chemin);   // ../..  --> ../../
	$base=dirname($pu['path']);
	while (strpos($tmp,"../")===0) {
		$base=dirname($base);
		$tmp=substr($tmp,3);
	}
	$CFG->wwwroot=$pu['scheme']."://".$pu['host'].$base;
}else {
//TODO page recharg�e ou acc�s direct ...on relit constantes.php
    $CFG->wwwroot=$locale_url_univ;
}

$CFG->admin_mail='' ;
$CFG->adresse_forum='http://www.c2i.education.fr/forum-c2i-1/' ;
$CFG->adresse_pl_nationale='https://c2i.education.fr/plate-forme/' ;
$CFG->adresse_serveur_public_c2i ='https://c2i.education.fr/c2iws/service.php';

$CFG->adresse_version='http://c2i.education.fr/version.txt' ;
$CFG->adresse_wiki='http://c2i.education.fr/wikipfc2i-X/index.php/Accueil' ;
$CFG->bodydir='ltr' ;
$CFG->boutons_retour_fermer_haut='1' ;
$CFG->cas='0' ;
$CFG->cas_force='0' ;
$CFG->chemin_ressources=realpath($chemin."/ressources");

$CFG->date_creation_config='1238785335' ;
$CFG->date_derniere_maj='1238785335' ;
$CFG->debug_traduction=0;
$CFG->encodage='UTF-8' ;
$CFG->err_mysql_avec_requete='1' ;

$CFG->hauteur_minipopups='320' ;
$CFG->hauteur_popups='500' ;
$CFG->langue='fr' ;
$CFG->largeur_minipopups='405' ;
$CFG->largeur_popups='800' ;
$CFG->ldap_version='2' ;
$CFG->log_erreur_fatale='1' ;

$CFG->montrer_progression_ajax='1' ;

$CFG->noreplyaddress='nepasrepondre@education.gouv.fr' ;

$CFG->prefix='c2i' ;

$CFG->session_nom='c2i' ;
$CFG->smtphosts='' ;
$CFG->smtppass='' ;
$CFG->smtpuser='' ;
$CFG->smtp_debugging='0' ;
$CFG->syslog_level='0' ;
$CFG->theme='v15' ;
$CFG->theme_js_calendar='system' ;
$CFG->tpl_montrer_balises='0' ;
$CFG->tpl_pas_trad_auto='0' ;
$CFG->tsv_separateur=' ' ;
$CFG->utiliser_infobulle_js='1' ;

$CFG->utiliser_notions_parcours='1' ;
$CFG->pool_en_positionnement='1';
$CFG->version='2.0 ' ;

$CFG->version_release='20130715' ;
$CFG->regle_nom_prenom=1;
$CFG->regle_nom_en_majuscule=1;
$CFG->utiliser_mkjoli=1;


$USER=new StdClass();
$USER->type_plateforme='certification';
$USER->id_user='admin1'; //tempo pour mise � jour


//existe pas en installation (seulement en maj)
@$USER->id_etab_perso=$universite_serveur ;   //extrait de constantes.php v 1.4 (tempo)
