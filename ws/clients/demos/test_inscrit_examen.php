<?php
require_once ('../C2IWS.php');

$c2i=new C2IWS();
require_once ('../auth.php');
/**test code for C2IWS:inscription de candidats a un examen existant.
         entrée : id examen et tableau de candidats 
         sortie : un tableau de numeros suppann des candidats avec le champ error renseigne
* @param integer $client
* @param string $sesskey
* @param string $id
* @param (stringInputRecords) array of string $candidats
* @return stringRecords
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$candidats=array();
for ($num=1;$num <=100;$num++)
$candidats[]='wsdemo'.$num;

$res=$c2i->inscrit_examen($lr->getClient(),$lr->getSessionKey(),'65.310',$candidats);
print_r($res);
$c2i->logout($lr->getClient(),$lr->getSessionKey());

?>
