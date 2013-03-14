<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_reponses
* @param int $client
* @param string $sesskey
* @param string $id_question
* @return  reponseRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_reponses($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
