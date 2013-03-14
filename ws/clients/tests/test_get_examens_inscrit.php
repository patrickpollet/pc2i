<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_examens_inscrit
* @param int $client
* @param string $sesskey
* @param string $userid
* @param string $idfield
* @param string $typep
* @return  examenRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_examens_inscrit($lr->getClient(),$lr->getSessionKey(),'','','');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
