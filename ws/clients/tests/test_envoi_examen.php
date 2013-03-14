<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for envoi_examen
* @param int $client
* @param string $sesskey
* @param string $id_examen
* @param string $type_pf
* @param resultatExamenInputRecord[] $copies
* @param resultatDetailleInputRecord[] $details
* @return  boolean
*/

$lr=$client->login(LOGIN,PASSWORD);
$copies=array();
$details=array();
$res=$client->envoi_examen($lr->getClient(),$lr->getSessionKey(),'','',$copies,$details);
print($res);
$client->logout($lr->getClient(),$lr->getSessionKey());

?>
