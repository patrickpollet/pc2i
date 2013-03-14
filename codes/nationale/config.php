<?php
// configuration sp�cifique nationale pas encore migr�e
// completer si n�cessaire  ce script est inclus dans lib_config.php
//set_config('','nombre_reponses_mini',4);
//set_config('','examen_anonyme',"1");
//set_config('','afficher_lien_mail_liste_qcm',"0");
//set_config('','peut_dupliquer_question',0);


//$CFG->debug_templates= $CFG->debug_templates || ($USER->id_user=="pollet");
//$CFG->dump_vars= $CFG->dump_vars || ($USER->id_user=="pollet");
/**/
if ($USER->id_user=="pollet") {
//    $USER->id_etab_perso=1;
//    $USER->droits['row_admin']->est_superadmin="O";

}
//$CFG->W3C_strict=0;
/**/
 $CFG->peut_dupliquer_question=0;
 $CFG->prof_peut_avoir_parcours=0;

$CFG->afficher_lien_mail_liste_questions=0;
$CFG->afficher_lien_mail_liste_examens=0;

 $CFG->regle_nom_en_majuscule=0;
 $CFG->regle_nom_prenom=2;


//$CFG->debug_ldap_groupes=0;
//$CFG->pas_de_motdepasse_oublie=1;

 $CFG->afficher_score_question=1;
 //$CFG->recalculer_score_question=1;

 //$CFG->calcul_indice_discrimination=1;
// $CFG->pas_de_scores_negatifs=1;



$CFG->adresse_feedback_questions="patrick.pollet@insa-lyon.fr";

?>
