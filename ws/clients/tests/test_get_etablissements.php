<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_etablissements
* @param int $client
* @param string $sesskey
* @param int $id_pere
* @return  etablissementRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_etablissements($lr->getClient(),$lr->getSessionKey(),0);
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
