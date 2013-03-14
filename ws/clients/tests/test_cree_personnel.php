<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for cree_personnel
* @param int $client
* @param string $sesskey
* @param personnelInputRecord $personnel
* @return  personnelRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$personnel= new personnelInputRecord();
$personnel->setAuth('');
$personnel->setEmail('');
$personnel->setEtablissement(0);
$personnel->setLogin('');
$personnel->setNom('');
$personnel->setNumetudiant('');
$personnel->setOrigine('');
$personnel->setPassword('');
$personnel->setPrenom('');
$personnel->setTags('');
$res=$client->cree_personnel($lr->getClient(),$lr->getSessionKey(),$personnel);
print_r($res);
print($res->getAuth());
print($res->getEmail());
print($res->getError());
print($res->getEtablissement());
print($res->getId());
print($res->getLogin());
print($res->getNom());
print($res->getNumetudiant());
print($res->getOrigine());
print($res->getPrenom());
print($res->getProfils());
print($res->getTags());
print($res->getTs_datecreation());
print($res->getTs_datemodification());
print($res->getTs_derniere_connexion());

$client->logout($lr->getClient(),$lr->getSessionKey());

?>
