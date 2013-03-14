<?php
/**
 * @author Patrick Pollet
 * @version $Id: fr.php 822 2009-05-28 16:04:32Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  * exemple de personnalisation de la traduction
  *  spécifique nationale
  *  * ATTENTION : ce fichier est declaré en encodage UTF-8 sous Eclipse !
  */


$supp=array(
"gestion_etablissements"=>"gestion des établissements",
"gestion_familles"=>"gestion des thèmes",
"gestion_referentiels"=>"gestion des domaines",
"gestion_alineas"=>"gestion des compétences",

"maj_domaines_questions"=>"mettre à jour les domaines des questions à partir des thèmes",


"nouvel_famille"=>"nouveau thème",
"nouvel_referentiel"=>"nouveau domaine",
"nouvel_alinea"=>"nouvelle compétence",

"fiche_referentiel"=>"Fiche domaine",
"fiche_alinea"=>"Fiche compétence",
"fiche_famille"=>"Fiche thème",


"js_famille_supprimer_0" => "attention ! vous êtes sur le point de supprimer le thème numéro :",
"js_ref_supprimer_0" => "attention ! vous êtes sur le point de supprimer le domaine :",
"js_alinea_supprimer_0" => "attention ! vous êtes sur le point de supprimer la compétence  :",


"js_action_annuler" => "cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",




"t_nb_aleatoire"=>"nb questions par QCM",
"t_nb_items"=>"nb items dans listes",

"t_aptitude"=>"compétence",

"t_nbqa"=>"Questions validées",

"form_nbquestions_associees"=>"nombre de questions",
"form_questions_associees"=>"questions associées",

);

 $textes_langues=array_merge($textes_langues,$supp);


?>
