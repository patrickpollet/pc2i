<?php
require_once ('../C2IWS.php');

$c2i=new C2IWS();
//$c2i= new C2IWS("http://localhost/c2i/V1.5/ws/wsdl.php", null, array('encoding'=>'ISO-8859-1','trace'=>'1'));
require_once ('../auth.php');
/**test code for C2IWS:creation d un examen.
         entrÃ©e : examen partiellement rempli
         sortie : examen complete ou erreur
* @param integer $client
* @param string $sesskey
* @param examenInputRecord $examen
* @return examenRecord
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$examen= new examenInputRecordV2();
$examen->setNom_examen('test via ws 2');
$examen->setAuteur('votre nom');
$examen->setAuteur_mail('votremail@nowhere.com');
// début demain à 9h15
//$examen->setTs_datedebut(mktime(9, 0 ,0, (int) date('m'), (int) date('d') + 1, (int) date('Y')));
//$examen->setTs_datefin($examen->getTs_datedebut()+3600);  // une heure
$examen->setPositionnement('OUI');
$examen->setCertification('NON');
$examen->setType_tirage('aléatoire');
//$examen->setOrdre_q('');
//$examen->setOrdre_r('');
//$examen->setCorrection('');
//$examen->setResultat_mini(50);
//$examen->setMot_de_passe('');
//$examen->setEnvoi_resultat(0);
$res=$c2i->cree_examen($lr->getClient(),$lr->getSessionKey(),$examen);
print_r($res);
print($res->getError());
print($res->getEid());
print($res->getId_etab());
print($res->getId_examen());
print($res->getNom_examen());
print($res->getAuteur());
print($res->getAuteur_mail());
print($res->getTs_datecreation());
print($res->getTs_datemodification());
print($res->getTs_datedebut());
print($res->getTs_datefin());
print($res->getLangue());
print($res->getPositionnement());
print($res->getCertification());
print($res->getType_tirage());
print($res->getOrdre_q());
print($res->getOrdre_r());
print($res->getCorrection());
print($res->getResultat_mini());
print($res->getMot_de_passe());
print($res->getEnvoi_resultat());

$c2i->logout($lr->getClient(),$lr->getSessionKey());

?>
