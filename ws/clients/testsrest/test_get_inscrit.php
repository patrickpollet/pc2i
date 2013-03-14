<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_inscrit
* @param int $client
* @param string $sesskey
* @param string $userid
* @param string $idfield
* @return  inscritRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_inscrit($lr->getClient(),$lr->getSessionKey(),'','');
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
