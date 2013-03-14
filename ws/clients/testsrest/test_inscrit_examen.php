<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for inscrit_examen
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @param string[] $candidats
* @param string $idfield
* @param string $tags
* @return  affectRecord[]
*/

$lr=$client->login(LOGIN,PASSWORD);
$candidats=array();
$res=$client->inscrit_examen($lr->getClient(),$lr->getSessionKey(),'',$candidats,'','');
print_r($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
