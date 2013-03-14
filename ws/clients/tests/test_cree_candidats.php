<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for cree_candidats
* @param int $client
* @param string $sesskey
* @param inscritInputRecord[] $candidats
* @return  inscritRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$candidats=array();
$res=$client->cree_candidats($lr->getClient(),$lr->getSessionKey(),$candidats);
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
