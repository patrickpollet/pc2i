<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_referentiels
* @param int $client
* @param string $sesskey
* @return  referentielRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_referentiels($lr->getClient(),$lr->getSessionKey());
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
