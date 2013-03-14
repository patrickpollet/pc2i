<?php

/**
 * @author Patrick Pollet
 * @version $Id: get_anonyme.php 1256 2011-05-24 13:41:25Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /**
  * rev 978 les messages d'erreur DOIVENT etre en UF8 pour �tre correctement d�cod� par les smartphones !
  * 
  * version 2.0 la BD est enfin en UTF8 donc on utilise 
  */

ob_start();
$chemin = "../..";

require_once ($chemin . "/commun/c2i_params.php");
require_once ($CFG->chemin . '/commun/lib_xml.php');

$format = optional_param("format", 'xml', PARAM_ALPHA);
$email = optional_param("email", '', PARAM_RAW);


$output = array ();

//pour un tracking correct
$USER->type_plateforme="webservice";
$USER->id_etab_perso=$CFG->universite_serveur;
$USER->type_user="A";



list ($idq, $ide) = get_examen_anonyme();
if (!isset ($idq)) {
    $output["error"]= traduction ('err_pas_examen_anonyme');
    espion2("acces_REST","get_examen_anonyme",traduction('err_pas_examen_anonyme'));

} else {
    $ligne = get_examen($idq, $ide, false);
    if (!$ligne) {
        $output["error"] = traduction('err_examen_inconnu',false,$ide.'.'.$idq);
        espion2("acces_REST","get_examen_anonyme",traduction('err_examen_inconnu',false,$ide.'.'.$idq));
    } else {
        // rev 978 controle adresse email
        if ($err=valide_acces_anonyme($email)) {
            $output["error"] = traduction($err);
        }
        else {
            $output= array_for_examen($ligne);
            $output["date_debut" ]=time();
            $output["montrer_domaines"]=false; // tests
            // examen avec m�morisation des r�sultats en base au retour
            if ($ligne->type_tirage!=EXAMEN_TIRAGE_PASSAGE) {
                $compte=cree_compte_anonyme($ide, $email);
                $tags='webservice REST get_anonyme '.$ide.'.'.$idq.' '.getremoteaddr().' '.time();
                inscrit_candidat($idq,$ide,$compte->login,$tags);
                $output["user"]=$compte->login;
            } else
                $output["user"]="";
            $USER->id_user=$output ["user"];
            espion2("acces_REST","get_examen_anonyme",$ide.".".$idq.' format='.$format. 'email= '.$email);
        }
    }
}

ob_clean();
$output["version"] = $CFG->version;


switch ($format) {
    case 'json':
        // rev 977 bug accents corrig� nationale
        if (! function_exists('json_encode')) { //PHP < 5.2
          echo (mon_json_encode($output));
        }
       else  echo (json_encode($output));
        break;
    case 'xml':
    $name = 'anonyme.xml';
    header("content-type: text/xml");
    header('Content-Disposition: attachment; filename="' . $name . '"');
    try {
        $xml = new array2xml('examen','item');
        $xml->createNode($output);
        echo $xml;
    } catch (Exception $e) {
        echo $e->getMessage();
        espion2("acces_REST","get_examen_anonyme",$ide.".".$idq.traduction("err_xml"));
    }
    break;
    case 'php':
        echo serialize($output);
    break;
    default:
        print_object('',$output);
    break;

}

die();

?>
