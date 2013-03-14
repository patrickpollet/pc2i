<?php
require_once ('../classes/c2i_soapserverrest.php');

$client=new c2i_soapserverrest();
require_once ('../auth.php');
/**test code for get_examen
* @param int $client
* @param string $sesskey
* @param string $id
* @return  examenRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$res=$client->get_examen($lr->getClient(),$lr->getSessionKey(),'');
print_r($res);
print($res->getAffiche_chrono());
print($res->getAuteur());
print($res->getAuteur_mail());
print($res->getCertification());
print($res->getCorrection());
print($res->getEid());
print($res->getEnvoi_resultat());
print($res->getError());
print($res->getId_etab());
print($res->getId_examen());
print($res->getLangue());
print($res->getMot_de_passe());
print($res->getNbinscrits());
print($res->getNbpassages());
print($res->getNbquestions());
print($res->getNom_examen());
print($res->getOrdre_q());
print($res->getOrdre_r());
print($res->getPositionnement());
print($res->getReferentielc2i());
print($res->getResultat_mini());
print($res->getTags());
print($res->getTs_datecreation());
print($res->getTs_datedebut());
print($res->getTs_datefin());
print($res->getTs_datemodification());
print($res->getTs_dureelimitepassage());
print($res->getType_tirage());
print($res->getVerouille());

$client->logout($lr->getClient(),$lr->getSessionKey());

?>
