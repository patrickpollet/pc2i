<?php
require_once ('../C2IWS.php');

$c2i=new C2IWS();
require_once ('../auth.php');
/**test code for C2IWS: renvoie le bilan d un examen par referentiel ET/ou par alinea ou les 2
* @param integer $client
* @param string $sesskey
* @param string $value1
* @param string $value2
* @return bilanDetailleRecords
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$res=$c2i->get_bilans_detailles_examen($lr->getClient(),$lr->getSessionKey(),'65.297','3');
print_r($res);
$c2i->logout($lr->getClient(),$lr->getSessionKey());

?>
