<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for desinscrit_examen
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @param string[] $candidats
* @param string $idfield
* @return  affectRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$candidats=array();
$res=$client->desinscrit_examen($lr->getClient(),$lr->getSessionKey(),'',$candidats,'');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
