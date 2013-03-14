<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_passages_recents
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @param int $timestart
* @return  noteRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_passages_recents($lr->getClient(),$lr->getSessionKey(),'',0);
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
