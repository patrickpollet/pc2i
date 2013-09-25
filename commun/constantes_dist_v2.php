<?php
/**
 * @author Patrick Pollet
 * @version $Id: constantes_dist_v15.php 1263 2011-09-19 17:04:33Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


/**
 * section 0
 * en cas d'erreurs bizarres (�crans blanc...)
 * decommentez les deux lignes ci dessous
 */

//error_reporting(E_ALL);
//ini_set("display_errors", 1);


/**
 * section 1 adresse de consultation de la version la plus récente
 */
define ('ADRESSE_VERSION', 'http://c2i.education.fr/version.txt');

/**
 * section 2
 * ces 7 parametres sont n�cessaires au bon lancement de la plateforme
 * les autres options ont �t� d�plac�es dans la table c2iconfig
 */

$session_nom	="{c2i}v2";				// nom de la session à utiliser pour cette version IMPORTANT

$adresse_serveur = "{serveur_bdd}"; 	// adresse du serveur sur lequel se trouve la Base de Données
$ines_base		 = "{nom_bdd}"; 		// nom de la Base de Données dans mysql
$base_utilisateur= "{user_bdd}"; 		// utilisateur ayant les droits d'administrer la Base de Données
$base_mdp		 = "{pass_bdd}"; 		// mot-de-passe de cet utilisateur dans mysql
$mysql_names     = 'UTF8';              //laisser vide pour le latin1 (changer en utf8 en version 1.6)
$prefix          = "{prefix}";          //prefix des tables 

$locale_url_univ = "{locale_url_univ}";  // adresse internet plateforme locale

$chemin_ressources ="{chemin_ressources}";    // emplacement de la zone des ressources doit etre accessible en �criture � l'utilisateur 'apache

$fichier_langue_defaut = "fr_utf8.php"; 	// fichier de langue par défaut
$fichier_langue_plateforme= "{c2i}_utf8.php"; // fichier spécifique au type de plateforme



/**
 * section 4  service CAS
 * si les  variables cas_* sont d�finies ici, les authentifications
 * se feront par le CAS,
 * si la valeur cas_force est � 1 on passe obligatoirement par la CAS
 * sinon la plateforme affiche un �cran de login 'mixte'
*/

/*
$cas_force=0;
$cas_url="cas.exemple.fr";
$cas_port=443;
$cas_service="cas";
*/

/**
 * section 5
 * exportation directe des r�sultats vers une base de donn�es MySQL
 * export direct des resultats synthetiques vers une Bd MySQL externe
 *  cette zone DOIT etre adaptee a la base MySQL ET a la structure de la table visee
 *  une option supplementaire apparaitra dans la fiche de consultation d'un examen
 */

$exportDB=0;    		// mettre a 1 pour action cette option
$extDB=array();
$extDB['host']='';      // serveur externe
$extDB['db']='';        // base MySQL visee
$extDB['table']='';     //table visee
$extDB['user']='';      // login
$extDB['password']='';  // mot de passe

/**
 * le CSV contient TOUJOURS les colonnes suivantes
 * Num�ro �tudiant;Login;Nom;Prenom;Examen;Score;A1;A2;B1;B2;B3;B4;B5;B6;B7;Date
 * 2517932;jkarnold;ARNOLD;Jean-Kristian;65.175;3.57%;0%;0%;0%;0%;0%;0%;16.67%;12.5%;0%;2007-06-02 00:00:00
 * mettre DANS cet ordre le nom des colonnes de la table correpondante dans le tableau $extDB ci-dessous
 * mettre � blanc pour ne pas �crire la colonne (ex $extDB[1]="" , on n'ecrira pas la colonne Nom)
 * si on change la structure du CSV "synthetiques, ne pas oublier de MAJ ce tableau !!!
 * exemple avec la BD suivante :
--
-- Structure de la table `c2i`
--

CREATE TABLE IF NOT EXISTS `c2i` (
  `annee` int(11) NOT NULL default '2007',      //annee scolaire
  `numero` int(11) NOT NULL default '0',        // numero etudiant
  `examen` varchar(8) NOT NULL default '',      // code examen (ex 65.188)
  `type` enum('C','P') NOT NULL default 'P',    // type d'examen
  `score` decimal(5,2) NOT NULL default '0.00', // score global
  `D1` decimal(5,2) NOT NULL default '0.00',    // score par referentiel
  `D2` decimal(5,2) NOT NULL default '0.00',
  `D3` decimal(5,2) NOT NULL default '0.00',
  `D4` decimal(5,2) NOT NULL default '0.00',
  `D5` decimal(5,2) NOT NULL default '0.00',
  `date_examen` varchar(25) NOT NULL default '00/00/0000 00:00:00', //date de fin de passage l'examen en chaine 
  ts_examen int(10) NOT NULL default 0 COMMENT 'timestamp unix debut examen'
  PRIMARY KEY  (`annee`,`numero`,`examen`,`type`)                // au cas ou !
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='resultats  aux tests c2i';

*/

$extDB['map']=array(
"numero",    	//mettre la 1ere colonne du CSV dans numero
"",          	// ne pas transferer la 2eme colonne du CSV (login)
"",	   			// ne pas transferer la 3eme colonne du CSV (nom)
"",         	// ne pas transferer la 4eme colonne du CSV (prénom)
"examen",    	//mettre la 5eme colonne du CSV dans examen
"score",
"D1",
"D2",
"D3",
"D4",
"D5",
"date_examen"
);

// decrit les colonnes supplementaires qui ne sont pas dans le CSV
$extraColonnes=array(
// nom de la colonne dans la table ==> VARIABLE C2I calculee par la plateforme
// ANNEE= anneescolaire en cours (selon date du jour) avant janvier = an moins 1
// TYPEP =type de plateforme (P ou C)
// ... liste a completer selon besoins
"annee"=>"ANNEE",
"type"=>"TYPEP",
"ts_examen"=>"TSDEBUT" // ajout� rev 984 pour r�gler des pb de conversions de date entre le CSV et MySQL
);

//fin export BD externe



?>
