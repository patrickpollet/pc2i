<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for envoi_questions
* @param int $client
* @param string $sesskey
* @param qcmItemRecord[] $questions
* @return  questionRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$questions=array();
$res=$client->envoi_questions($lr->getClient(),$lr->getSessionKey(),$questions);
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
