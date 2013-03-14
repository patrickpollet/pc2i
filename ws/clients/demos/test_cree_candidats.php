<?php
require_once ('../C2IWS.php');

$c2i=new C2IWS();
require_once ('../auth.php');
/**test code for C2IWS:creation ds plusieurs comptes de candidat.
         entrée : tableau candidats partiellement rempli 
         sortie : tableau candidats completes ou erreur
* @param integer $client
* @param string $sesskey
* @param (inscritInputRecords) array of inscritInputRecord $candidats
* @return inscritRecords
*/

$lr=$c2i->login(LOGIN,PASSWORD);
$candidats=array();

for ($num=2; $num <=99; $num++) {
	
	$candidat= new inscritInputRecord();
	$candidat->setNom('demo '.$num);
	$candidat->setPrenom('ws');
	$candidat->setLogin('wsdemo'.$num);
	$candidat->setPassword('wsdemo!'.$num);
	//$candidat->setEtablissement(0);
	$candidat->setNumetudiant('wsdemo'.$num);
	$candidat->setEmail('wsdemo'.$num.'@nowhere.org');
	$candidat->setAuth('manuel');
	$candidats[]=$candidat;
	
}

print_r($candidats);


$res=$c2i->cree_candidats($lr->getClient(),$lr->getSessionKey(),$candidats);
print_r($res);
$c2i->logout($lr->getClient(),$lr->getSessionKey());

?>
