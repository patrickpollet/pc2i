<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_session_user
* @param int $client
* @return  string
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_session_user($lr->getClient());
print($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
