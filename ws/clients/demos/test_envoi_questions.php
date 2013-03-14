<?php
$chemin="../../../";
require_once("$chemin/commun/c2i_params.php" );
require_once ('../C2IWS.php');

$c2i=new C2IWS();
require_once ('../auth.php');
/**test code for C2IWS:soumission de questions locale vers nationale.
         entrée : un tableau de questions avec leurs reponses
         sortie : un tableau de questions (sans reponses) avec le champ error renseigne
* @param integer $client
* @param string $sesskey
* @param (qcmItemRecords) array of qcmItemRecord $questions
* @return questionRecords
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$questions=array();
$q->question=get_question(2405,65);
$q->reponses=get_reponses(2405,65,false,false);
$q->question->id=2406;

foreach ($q->reponses as $num=>$r)
	$q->reponses[$num]->id=$q->question->id;
$questions[]=$q;
print_r($q);	
$res=$c2i->envoi_questions($lr->getClient(),$lr->getSessionKey(),$questions);
print_r($res);
$c2i->logout($lr->getClient(),$lr->getSessionKey());

?>
