<?php

/**
 * @author Patrick Pollet
 * @version $Id: aj_famille.php 1113 2010-09-07 11:23:04Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
// ajout d'une famille � un couple domaine / alin�a  AJAX
//
////////////////////////////////


//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");
require_once($chemin_commun."/lib_ajax.php");

if (! require_login ("P",false))
        die_erreur(traduction ("err_acces"));

//ajax pas d'erreur fatale ...
$ref=optional_param("ref","",PARAM_ALPHANUM);
$alin=optional_param("alin",0,PARAM_INT);
$famille=optional_param("famille","",PARAM_RAW);
$descf=optional_param("descf","",PARAM_RAW);
$motsclesf=optional_param("motsclesf","",PARAM_RAW);

if (!$ref || ! $alin || !$famille)
      die_erreur(traduction ("err_param_requis"));

if (! teste_droit("qv")) die_erreur (traduction ("err_acces"));

$famille = trim(urldecode($famille));
$descf = trim(urldecode($descf));
$motsclesf = trim(urldecode($motsclesf)); //rev 938


if (get_famille_par_nom ($famille,$ref,$alin))
    die_erreur(traduction("err_famille_existante"));

$ligne=new StdClass();
$ligne->famille=$famille;
$ligne->commentaires=$descf;
$ligne->mots_clesf=$motsclesf;

//rev 977

$ligne->referentielc2i=$ref;
$ligne->alinea=$alin;


if ($idf=ajoute_famille($ligne,false)) {
				echo "{";
				echo "\"idf\":\"".$idf."\",";
				echo "\"result\":\"ok\"}";
			}
else
	die_erreur("{ \"result\":\"erreur lors de l'insertion\" }");


?>