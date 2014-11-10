<?php
/**
 * @author Patrick Pollet
 * @version $Id: entete.php 288 2008-06-18 23:21:13Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 *
 * convertit en png les documents de type gif qui ne sont pas
 * reconnus par laTex et met a jour la bd nationale
 * prerequis ! installer imagemagic pour le programme convert
 *
 */


set_time_limit(0);
$chemin = '../../../';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");                 //fichier de param�tres
require_once($chemin_commun."/lib_sync.php");

//if ($CFG->universite_serveur !=1) die("pas nationale");


//$registrationurl = 'https://c2i.education.fr/c2iws/service.php';
$registrationurl=$CFG->adresse_serveur_public_c2i;
//$registrationurl = 'http://prope.insa-lyon.fr/c2i/c2iws/service.php';


$data = array(
      'wsfunction'=>'c2i_get_all_ressources',
      'wsformatout'=>'php',
      'c2i'=>$CFG->c2i,
//'domaine'=>'D1'

);
$request = array(
CURLOPT_URL        => $registrationurl,
CURLOPT_POST       => 1,
CURLOPT_POSTFIELDS => $data,
);

$res=c2i_http_request($request);

print_r($res);
if ($res->errno==0) {
    $ressources=unserialize($res->data);
   // print_r($ressources);
    if (is_array($ressources)){
        foreach ($ressources as $ressource) {
            if (!$CFG->unicodedb) {
                foreach ($ressource as $key=>$value)
                if (is_string($value)) {
                    $ressource->$key=utf8_decode($value);
                    //print "*";
                }
            }
            unset($ressource->url); //tempo
            $ressource->id_etab=1;
            $ressource->modifiable=0;
            $ressource->ts_datemodification=time();

            // gros pb avec l'ordre mal renvoy� par la nationale 'domaine,competence,ordre'
            // et elles ne sont pas dans l'ordre D1,D2 ....' ...
            if ($old=get_record('ressources','id='.$ressource->id.' and id_etab=1',false)) {
                print ("maj ".$ressource->id); //avant
                update_record('ressources', $ressource,'id','id_etab');
            }

            else  {
                $ressource->ts_datecreation=time();
                $id=insert_record('ressources',$ressource);
                print ("ajout ".$id);
            }
        }
    } else {
        //message d'erreur renvoy� par CURL ( not found) ou par le WS
        print_r($res->data);
    }


     

}
else {
    print_r($res);

}


/**
 * 
 * excute une requete HTTP en mode REST via curl
 * pour l'instant utilis�e seulement vers https://c2i.education.fr/c2iws/service.php
 * pour obtenir les ressources 
 * @param array $config
 * @param boolean $quiet
 * @return StdClass
 */
/*** DEPLACEE DANS lib_sync.php

function c2i_http_request($config, $quiet=false,$id_objet='') {
    global $CFG;
    
    if(!extension_loaded('curl')) {
        if ($quiet)
            return false;
        else 
            die ('extension php curl non install�e');
    }
    
    
    $ch = curl_init();

    // standard curl_setopt stuff; configs passed to the function can override these
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if (!ini_get('open_basedir')) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    }
    curl_setopt_array($ch, $config);
    
    if (!empty($CFG->proxy_host)) curl_setopt($ch,CURLOPT_PROXY,$CFG->proxy_host);
    // rev 984 bug avec certains PHP cf http://www.thedeveloperday.com/php-soapclient-proxy/
    // donc on force proxyort en int
    if (!empty($CFG->proxy_port)) curl_setopt($ch,CURLOPT_PROXYPORT, (int)$CFG->proxy_port);
    if (!empty($CFG->proxy_login) && !empty($CFG->proxy_password)) 
         curl_setopt($ch,CURLOPT_PROXYUSERPWD,$CFG->proxy_login.':'.$CFG->proxy_password);
    
    
    $result = new StdClass();
    // peut contenir un simple texte HTMl avec erreur 404
    $result->data = curl_exec($ch);
    $result->info = curl_getinfo($ch);

    $result->error = curl_error($ch);
    $result->errno = curl_errno($ch);

    if ($result->errno) {
        if ($quiet) {
            // When doing something unimportant like fetching rss feeds, some errors should not pollute the logs.
            $dontcare = array(
            CURLE_COULDNT_RESOLVE_HOST, CURLE_COULDNT_CONNECT, CURLE_PARTIAL_FILE, CURLE_OPERATION_TIMEOUTED,
            CURLE_GOT_NOTHING,

            );
            $quiet = in_array($result->errno, $dontcare);
        }
        if (!$quiet) {
           // log_warn('Curl error: ' . $result->errno . ': ' . $result->error);
            espion3('erreur', 'curl', 'synchro nationale',$id_objet, $result);
        }
    }

    curl_close($ch);

    return $result;
}

***********/


?>

