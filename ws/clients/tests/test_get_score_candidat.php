<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for get_score_candidat
* @param int $client
* @param string $sesskey
* @param string $idcandidat
* @param string $idfield
* @param string $idexamen
* @return  bilanDetailleRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_score_candidat($lr->getClient(),$lr->getSessionKey(),'','','');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
