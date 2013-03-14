<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_version
* @param int $client
* @param string $sesskey
* @return  string
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_version($lr->getClient(),$lr->getSessionKey());
print($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
