<?php
require_once ('../classes/c2i_soapserver.php');
//important la PF travaille en ISO pour l'instant !!!
//c2i= new c2i_soapserver();
$c2i = new c2i_soapserver("http://localhost/c2i/V1.5/ws/wsdl.php", null, array('encoding'=>'ISO-8859-1','trace'=>'1'));
require_once ('../auth.php');

if ($_SERVER['argc'] != 2) {
    die("usage:php " . __FILE__ . " nom_du_fichier_csv\n");
}
$fichier = $_SERVER['argv'][1];

if (!file_exists($fichier))
    die("fichier $fichier introuvable\n");

$lr = $c2i->login(LOGIN, PASSWORD);

print ("connecte à la PF");

$handle = fopen($fichier, 'rb');
$entete = fgets($handle); // saute 1ere ligne
while (!feof($handle)) {
    $contents = trim(fgets($handle, 4096)); //vire saut de ligne a la fin
    if ($contents == "")
        continue;
    //print "$contents\n";

    $table = explode(';', $contents);
   // print_r($table);

    $examen = new ExamenInputRecord();

    if ($table[0] == 'positionnement') {
        $examen->setPositionnement('OUI');
        $examen->setCertification('NON');
    } else {
        $examen->setPositionnement('NON');
        $examen->setCertification('OUI');
    }

    $examen->setNom_examen($table[1]);


    $examen->setTs_datedebut(conversion_date($table[2],$table[3]));
    $examen->setTs_datefin(conversion_date($table[4],$table[5]));

    $examen->setTs_dureelimitepassage($table[6]);
    $examen->setAffiche_chrono($table[6] >0);

    $examen->setMot_de_passe($table[7]);
    $examen->setType_tirage($table[8]);
    $examen->setOrdre_q($table[9]);
    $examen->setOrdre_r($table[10]);

    $examen->setAuteur($table[11]);
    $examen->setAuteur_mail($table[12]);
    $examen->setVerouille(0);
    print_r($examen);
try {
    $res=$c2i->cree_examen($lr->getClient(),$lr->getSessionKey(),$examen,0);
} catch (Exception $e) {
    print($e->getMessage());
    break;
}

    if ($err=$res->getError()) die ('erreur en création '.$err);
    else print ("examen créé :");
    print_r($res);
  break;

}
fclose($handle);

$c2i->logout($lr->getClient(), $lr->getSessionKey());
print ("deconnecte de la PF");


function conversion_date ($date,$heure) {
    list ($j,$m,$a)=explode('/',$date);
    list ($h,$mm,$s)=explode(':',$heure);
    $ret=mktime($h,$mm,$s,$m,$j,$a);
    $test=date("d/m/y H:i:s",$ret);
    if ($test != $date.' '.$heure) die ('erreur conversion date '.$date.' '.$heure ."<>".$test);
    return $ret;
}


?>
