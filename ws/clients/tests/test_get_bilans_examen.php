<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_bilans_examen
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @return  bilanDetailleRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_bilans_examen($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
