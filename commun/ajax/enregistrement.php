<?php

/**
 * @author Patrick Pollet
 * @version $Id: enregistrement.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

// Enregistrement � la vol�e des r�sultats des �tudiants au test ajax a revoir

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");
require_once($chemin_commun."/lib_ajax.php");

//utiliser la session uniquement
if (!is_utilisateur_anonyme())   //l'anonyme a le type_user =A il peut aller ici et dans action.php basta
	if (! require_login ("E",false))
     	die_erreur("erreur 0");

//parametres envoy�s par enregistre.js

$user=optional_param("user","",PARAM_RAW);
$examen=optional_param("examen","",PARAM_CLE_C2I);
$reponse=optional_param("reponse","",PARAM_RAW);

if (!$user || ! $examen || !$reponse || $user !=$USER->id_user)
   die_erreur("erreur 1");

//die_erreur("zzzz");
// on regarde si la p�riode de passage de l'examen est en ce moment
$exam1 = explode('.',$examen);

$ligne=get_examen ($exam1['1'],$exam1['0'],false);
if (! $ligne)
    die_erreur("erreur, cet examen n'existe pas ou n'est pas disponible");

$type_tirage_examen = $ligne->type_tirage;

if (! examen_en_cours($ligne))
    die_erreur("erreur, vous r�pondez en dehors de la p�riode pr�vue pour passer cet examen, aucune nouvelle r�ponse ne sera enregistr�e");

//on devrait aussi regarder si il est inscrit ???
if (!est_inscrit_examen($exam1['1'],$exam1['0'],$user))
        die_erreur("erreur 2");

//Num�ro de la r�ponse (en enlevant le pr�fixe)
$ch_rq = explode('_',$reponse);
if (count($ch_rq) !=3)
        die_erreur("erreur 3");


$reponse = $ch_rq[2];
$question = $ch_rq[0].".".$ch_rq[1];

// On regarde si la r�ponse a d�j� �t� donn�e

$critere =<<<EOC
login = '$user'
 AND   examen = '$examen'
 AND   reponse = '$reponse'
 AND question = '$question'
EOC;

$sql=<<<EOS
  SELECT login
  FROM {$CFG->prefix}resultats
  WHERE $critere
EOS;

// Si la case a d�j� �t� coch�e, on supprime l'entr�e
if (count_records_sql($sql,false)) {
    delete_records("resultats",$critere,false);
    die_ok();
} else  {
     $ligne=new StdClass();
     $ligne->login=$user;
     $ligne->question=$question;
     $ligne->reponse=$reponse;
     $ligne->examen=$examen;

     $ligne->ip=$REMOTE_ADDR;   //calcul�e par c2i_params
     $ligne->ts_date=time();
     // on n'ecrit PLUS les dates au format francais

     if (insert_record("resultats",$ligne,false,"",false)) die_ok();
     else die_erreur('erreur acc�s BD');
 }

?>