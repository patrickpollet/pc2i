<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_etablissement
* @param int $client
* @param string $sesskey
* @param string $id
* @return  etablissementRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_etablissement($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
print($res->getCertification());
print($res->getError());
print($res->getId_etab());
print($res->getLocale());
print($res->getNationale());
print($res->getNom_etab());
print($res->getPere());
print($res->getPositionnement());

$client->logout($lr->getClient(),$lr->getSessionKey());

?>
