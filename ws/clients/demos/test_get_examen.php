<?php
require_once ('../C2IWS.php');

$c2i=new C2IWS();
require_once ('../auth.php');
/**test code for C2IWS: renvoie les info sur un examen
* @param integer $client
* @param string $sesskey
* @param string $value
* @return examenRecord
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$res=$c2i->get_examen($lr->getClient(),$lr->getSessionKey(),'65.297');
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
