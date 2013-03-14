<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_qcm
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @return  qcmRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_qcm($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
print($res->getError());
print($res->getExamen());
print($res->getQuestions());

$client->logout($lr->getClient(),$lr->getSessionKey());

?>
