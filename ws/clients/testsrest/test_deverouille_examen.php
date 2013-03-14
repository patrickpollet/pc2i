<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for deverouille_examen
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @return  boolean
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->deverouille_examen($lr->getClient(),$lr->getSessionKey(),'');
print($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
