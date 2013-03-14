<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_bilans_detailles_examen
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @param int $type
* @return  bilanDetailleRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_bilans_detailles_examen($lr->getClient(),$lr->getSessionKey(),'',0);
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
