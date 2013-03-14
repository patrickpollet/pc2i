<?php
require_once ('../C2IWS.php');

$c2i=new C2IWS();
require_once ('../auth.php');
/**test code for C2IWS:creation d un compte de candidat.
         entrée : candidat partiellement rempli 
         sortie : candidat complete ou erreur
* @param integer $client
* @param string $sesskey
* @param inscritInputRecord $candidat
* @return inscritRecord
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$candidat= new inscritInputRecord();
$candidat->setNom('demo 1');
$candidat->setPrenom('ws');
$candidat->setLogin('wsdemo200');
//$candidat->setPassword('');
//$candidat->setEtablissement(0);
$candidat->setNumetudiant('wsdemo200');
$candidat->setEmail('wsdemo1@nowhere.org');
$candidat->setAuth('manuel');
$res=$c2i->cree_candidat($lr->getClient(),$lr->getSessionKey(),$candidat);
print_r($res);
print($res->getError());
print($res->getId());
print($res->getNom());
print($res->getPrenom());
print($res->getLogin());
print($res->getGenre());
print($res->getEtablissement());
print($res->getNumetudiant());
print($res->getExamens());
print($res->getEmail());
print($res->getAuth());
print($res->getTs_datecreation());
print($res->getTs_datemodification());
print($res->getTs_derniere_connexion());

$c2i->logout($lr->getClient(),$lr->getSessionKey());

?>
