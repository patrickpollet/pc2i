<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_toutes_questions_et_reponses
* @param int $client
* @param string $sesskey
* @param string $typep
* @return  qcmItemRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_toutes_questions_et_reponses($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
