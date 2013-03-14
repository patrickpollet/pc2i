<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for corrige_examen
* @param int $client
* @param string $sesskey
* @param string $idcandidat
* @param string $idfield
* @param string $idexamen
* @param string $listequestions
* @param string[] $listereponses
* @return  bilanDetailleRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$listereponses=array();
$res=$client->corrige_examen($lr->getClient(),$lr->getSessionKey(),'','','','',$listereponses);
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
