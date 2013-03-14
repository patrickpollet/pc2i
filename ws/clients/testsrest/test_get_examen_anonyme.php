<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_examen_anonyme
* @param int $client
* @param string $sesskey
* @param string $email
* @return  qcmRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_examen_anonyme($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
print($res->getError());
print($res->getExamen());
print($res->getQuestions());

$client->logout($lr->getClient(),$lr->getSessionKey());

?>
