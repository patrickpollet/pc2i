<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for est_inscrit_examen
* @param int $client
* @param string $sesskey
* @param string $userid
* @param string $idfield
* @param string $id_examen
* @return  boolean
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->est_inscrit_examen($lr->getClient(),$lr->getSessionKey(),'','','');
print($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
