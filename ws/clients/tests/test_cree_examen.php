<?php
require_once ('../classes/c2i_soapserver.php');

$client=new c2i_soapserver();
require_once ('../auth.php');
/**test code for cree_examen
* @param int $client
* @param string $sesskey
* @param examenInputRecord $examen
* @param int $id_etab
* @return  examenRecord
*/

$lr=$client->login(LOGIN,PASSWORD);
$examen= new examenInputRecord();
$examen->setAffiche_chrono(0);
$examen->setAuteur('');
$examen->setAuteur_mail('');
$examen->setCertification('');
$examen->setCorrection('');
$examen->setEnvoi_resultat(0);
$examen->setMot_de_passe('');
$examen->setNbquestions(0);
$examen->setNom_examen('');
$examen->setOrdre_q('');
$examen->setOrdre_r('');
$examen->setPositionnement('');
$examen->setReferentielc2i('');
$examen->setResultat_mini('');
$examen->setTags('');
$examen->setTs_datedebut(0);
$examen->setTs_datefin(0);
$examen->setTs_dureelimitepassage(0);
$examen->setType_tirage('');
$examen->setVerouille(0);
$res=$client->cree_examen($lr->getClient(),$lr->getSessionKey(),$examen,0);
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
