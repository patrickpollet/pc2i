<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_questions
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @return  questionRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_questions($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
