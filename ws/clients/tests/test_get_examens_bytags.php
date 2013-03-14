<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_examens_bytags
* @param int $client
* @param string $sesskey
* @param string $tags
* @return  examenRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_examens_bytags($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
