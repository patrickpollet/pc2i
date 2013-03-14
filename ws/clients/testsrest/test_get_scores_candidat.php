<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_scores_candidat
* @param int $client
* @param string $sesskey
* @param string $idcandidat
* @param string $idfield
* @param string $typep
* @param int $consolid
* @return  bilanDetailleRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_scores_candidat($lr->getClient(),$lr->getSessionKey(),'','','',0);
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
