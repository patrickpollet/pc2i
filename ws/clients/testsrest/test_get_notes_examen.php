<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_notes_examen
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @return  noteRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_notes_examen($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
