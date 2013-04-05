<?php

/**
 * @author Patrick Pollet
 * @version $Id: preconisations_fr_utf8.php 1294 2012-03-06 11:00:50Z vbelleng $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * preconisation suite a un passage en positionnement voir lib_resultats/get_preconisations
 * mode d'emploi : indice score en dessous duquel la preconisation correspondante sera affichée
 * par défaut 40, 70 et 100. Vous pouvez en ajouter ...

 *
 */

// libelle => structure du texte correspondant
$domaine_a_revoir = array(
"Domaine à revoir"=>"[[Domaine_a_revoir]]"
);

$test_score = array(
"Score global inf. à 40%"=>"##SI score_global < 40%##<br/><i>saisir votre texte ici,<br/>les pourcentages sont libre de saisie<br/></i>##FIN SI##",
"Score global égal à 0%"=>"##SI score_global == 0%##<br/><i>saisir votre texte ici,<br/>les pourcentages sont libre de saisie<br/></i>##FIN SI##",
"Score global entre 40 et à 70%"=>"##SI score_global >= 40% && score_global < 70%##<br/><i>saisir votre texte ici,<br/>les pourcentages sont libre de saisie<br/></i>##FIN SI##",
"Score global sup. ou égal à 70%"=>"##SI score_global >= 70%##<br/><i>saisir votre texte ici,<br/>les pourcentages sont libre de saisie<br/></i>##FIN SI##"
);


$preconisations=array (

"40"=>"Vous ne maîtrisez pas les bases du référentiel C2i.
<br/><br/>Les diff&eacute;rents points du r&eacute;f&eacute;rentiel doivent &ecirc;tre travaill&eacute;s",

"70"=>"Plusieurs points du r&eacute;f&eacute;rentiel m&eacute;ritent d'&ecirc;tre approfondis.
Lorsque vous avez un score inf&eacute;rieur &agrave; 40% dans un domaine, il faut reprendre les bases et acqu&eacute;rir les connaissances et savoir-faire associ&eacute;s ;
lorsque vous avez un score compris entre 40% et 70%, vous devez approfondir le domaine en tenant compte de vos r&eacute;sultats dans les diff&eacute;rents alin&eacute;as,
lorsque vous avez un score compris entre 70% et 100%, vous avez, &agrave; priori, les comp&eacute;tences du C2i de ce domaine, vous pouvez compl&eacute;ter vos connaissances en auto-formation.",

"100"=>"Vous avez, &agrave; priori, les comp&eacute;tences du C2i.<br/>
Vous pouvez envisager de travailler certains points.<br/>
Lorsque vous avez un score inf&eacute;rieur &agrave; 40% dans un domaine, il faut reprendre les bases
et acqu&eacute;rir les connaissances et savoir-faire associ&eacute;s ;
 lorsque vous avez un score compris entre 40% et 70%, vous devez approfondir le domaine en tenant compte de vos r&eacute;sultats dans les diff&eacute;rents alin&eacute;s,
lorsque vous avez un score compris entre 70% et 100%, vous avez, &agrave; priori, les comp&eacute;tences du C2i de ce domaine vous pouvez compl&eacute;ter vos connaissances en auto-formation.",

);

$constantes = array(
"Date du jour"=>"[[Date_du_jour]]",
"Client"=>"[[Client]]"
);

$preconisations_demo=array(
"20"=>"t'es nul",
"40"=>"bof",
"60"=>"pas mal",
"80"=>"t'as triché ?",
"100"=>"tu prends ma place"
);

