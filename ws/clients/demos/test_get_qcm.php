<?php
require_once ('../C2IWS.php');

$c2i=new C2IWS();
require_once ('../auth.php');
/**test code for C2IWS: renvoie un  qcm complet (examen + questions+ reponses
* @param integer $client
* @param string $sesskey
* @param string $value
* @return qcmRecord
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$res=$c2i->get_qcm($lr->getClient(),$lr->getSessionKey(),'65.310');
print_r($res);
print($res->getError());
print($res->getExamen());
print($res->getQuestions());

$c2i->logout($lr->getClient(),$lr->getSessionKey());

?>
