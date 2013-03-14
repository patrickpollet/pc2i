<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_question
* @param int $client
* @param string $sesskey
* @param string $id
* @return  questionRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_question($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
print($res->getAlinea());
print($res->getAuteur());
print($res->getAuteur_mail());
print($res->getCertification());
print($res->getError());
print($res->getEtat());
print($res->getId());
print($res->getId_etab());
print($res->getId_famille_proposee());
print($res->getId_famille_validee());
print($res->getLangue());
print($res->getPositionnement());
print($res->getQid());
print($res->getReferentielc2i());
print($res->getTags());
print($res->getTitre());
print($res->getTs_datecreation());
print($res->getTs_dateenvoi());
print($res->getTs_datemodification());
print($res->getTs_dateutilisation());

$client->logout($lr->getClient(),$lr->getSessionKey());

?>
