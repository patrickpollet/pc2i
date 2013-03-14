<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for cree_candidat
* @param int $client
* @param string $sesskey
* @param inscritInputRecord $candidat
* @return  inscritRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$candidat= new inscritInputRecord();
$candidat->setAuth('');
$candidat->setEmail('');
$candidat->setEtablissement(0);
$candidat->setLogin('');
$candidat->setNom('');
$candidat->setNumetudiant('');
$candidat->setOrigine('');
$candidat->setPassword('');
$candidat->setPasswordmd5('');
$candidat->setPrenom('');
$candidat->setTags('');
$res=$client->cree_candidat($lr->getClient(),$lr->getSessionKey(),$candidat);
print_r($res);
print($res->getAuth());
print($res->getEmail());
print($res->getError());
print($res->getEtablissement());
print($res->getExamens());
print($res->getGenre());
print($res->getId());
print($res->getLogin());
print($res->getNom());
print($res->getNumetudiant());
print($res->getOrigine());
print($res->getPrenom());
print($res->getTags());
print($res->getTs_datecreation());
print($res->getTs_datemodification());
print($res->getTs_derniere_connexion());

$client->logout($lr->getClient(),$lr->getSessionKey());

?>
