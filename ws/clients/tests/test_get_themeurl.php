<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_themeurl
* @param int $client
* @param string $sesskey
* @return  string
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_themeurl($lr->getClient(),$lr->getSessionKey());
print($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
