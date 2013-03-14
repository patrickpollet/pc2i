<?php

/**
 * @author Patrick Pollet
 * @version $Id: test_algo_tirage_passage.php 1109 2010-07-21 11:36:47Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
// options d'adminstrtaion sur un examen'
// test de la qualit� de l'algorithme de tirage des questions avec examen de type lors du passage
////////////////////////////////

$chemin = '../../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");                 //fichier de param�tres
require_once($chemin_commun."/lib_tests.php");                 //fichier de param�tres


require_once($chemin_commun."/ProgressBar.class.php");                 //fichier de param�tres


set_time_limit(0);
/***
require_login('P'); //PP
if (! is_admin())
    erreur_fatale("err_droits");
 **/   
 
$nbmax = optional_param("nbmax",1000, PARAM_INT);
$typep=optional_param("typep","positionnement",PARAM_ALPHA); 
 
$USER->type_plateforme=$typep;

$USER->id_etab_perso=$CFG->universite_serveur; 

$bar = new ProgressBar();
    
$nbmax = optional_param("nbmax",1000, PARAM_INT);
$bar->initialize($nbmax);
$bar->increase(); //call for first element

$qusage=array();

$examen=cree_examen_test("test tirage aléatoire au passage",false,EXAMEN_TIRAGE_PASSAGE);

$idq=$examen->id_examen;
$ide=$examen->id_etab;

$nb=0;

//effectue 1000 tirages al�atoires
while ($nb <$nbmax) {
    $questions=tire_questions($idq,$ide);
    foreach ($questions as $question) {
        $qid=$question->id_etab.".".$question->id;
        if (isset($qusage[$qid])) $qusage[$qid]++;
        else $qusage[$qid]=1;
    }    
    $nb++;
    $bar->increase();       
   
}   
// non on n'est pas authentifi�
//pas grave car cree_examen_test ne le cr�� pas deux fois ;-) '
//supprime_examen($idq,$ide); 
ksort($qusage);

echo ("<br/>---------------------<br/>");
echo "nombre de tirages = ".$nbmax."<br/>";
echo "nombre de questions tirées = ".count($qusage)."<br/>";

$total=0;

foreach ($qusage as $qid=>$nb) {
    echo ("$qid  $nb <br/>");
    $total +=$nb;
}    

echo ("<br/>---------------------<br/>");    
echo (" total usage questions = ".$total."<br/>");
?>