<?php

/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 942 2009-10-25 09:37:41Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
// options d'adminstrtaion sur un examen'
//
////////////////////////////////

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");                 //fichier de paramètres

//require_login('P'); //PP
//if (! is_admin())
//    erreur_fatale("err_droits");


$questions=get_records('questions');

foreach ($questions as $q) {
   // print "question {$q->id_etab}.{$q->id}:\n";
    $docs=get_documents($q->id,$q->id_etab);
    foreach ($docs as $d) {
        if ($b64=@encode_document($d->id_doc.'.'.$d->extension, $q->id,$q->id_etab )) {
            echo "question {$q->id_etab}.{$q->id} état : {$q->etat} a le document {$d->id_doc}.{$d->extension} OK <br/>\n";
        }
        else
          echo "question {$q->id_etab}.{$q->id} CERT = {$q->certification} POS = {$q->positionnement}  état : {$q->etat} N'A PLUS LE DOCUMENT {$d->id_doc}.{$d->extension} <br/>\n";

    }


}


foreach ($questions as $q) {

    $reps=get_reponses($q->id,$q->id_etab,false,false);
    $nb=0;
    if (empty($reps)) {
        print "question {$q->id_etab}.{$q->id} : ";
        print "CERT = {$q->certification} POS = {$q->positionnement} : ";
        print $q->etat.' ';
        print  ("KO question sans AUCUNE réponse <br/>\n");
    }
    else {
        $nbr=count($reps);
        foreach ($reps as $rep) {
            if ($rep->bonne=='OUI') $nb++;
        }
        if ($nb==0) {
            print "question {$q->id_etab}.{$q->id} : ";
            print "CERT = {$q->certification} POS = {$q->positionnement} : ";
            print $q->etat.' ';
            print  ("KO question ($nbr réponses) sans BONNE réponse <br/>\n");
        }
    }
    // else print ("OK\n");

}
?>
