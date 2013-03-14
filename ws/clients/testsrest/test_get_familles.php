<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_familles
* @param int $client
* @param string $sesskey
* @return  familleRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_familles($lr->getClient(),$lr->getSessionKey());
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
