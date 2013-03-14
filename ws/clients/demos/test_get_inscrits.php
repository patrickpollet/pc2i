<?php
require_once ('../C2IWS.php');

$c2i=new C2IWS();
require_once ('../auth.php');
/**test code for C2IWS: renvoie les inscrits  a un examen
* @param integer $client
* @param string $sesskey
* @param string $value
* @return inscritRecords
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$res=$c2i->get_inscrits($lr->getClient(),$lr->getSessionKey(),'65.297');
print_r($res);
$c2i->logout($lr->getClient(),$lr->getSessionKey());

?>
