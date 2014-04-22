<?php
// configuration spécifique nationale pas encore migrée
// completer si nécessaire  ce script est inclus dans lib_config.php

//$CFG->debug_templates= $CFG->debug_templates || ($USER->id_user=="pollet");
//$CFG->dump_vars= $CFG->dump_vars || ($USER->id_user=="pollet");
/**/

/*valeurs par défaut sur une nationale (demande des experts QCM)*/
$CFG->peut_dupliquer_question=0;
$CFG->prof_peut_avoir_parcours=0;

$CFG->afficher_lien_mail_liste_questions=0;
$CFG->afficher_lien_mail_liste_examens=0;

$CFG->seulement_validee_en_positionnement=1; 

?>
