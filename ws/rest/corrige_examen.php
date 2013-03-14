<?php


/**
 * @author Patrick Pollet
 * @version $Id: corrige_examen.php 1245 2011-05-12 13:48:42Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * version 2.0 la BD est enfin en UTF8 donc on utilise htmlentities($referentiel->domaine,ENT_COMPAT, 'UTF-8') 
 */

ob_start();

$chemin = "../..";

require_once ($chemin . "/commun/c2i_params.php");


require_once ($CFG->chemin . '/commun/lib_xml.php');
//require_once ($CFG->chemin . '/commun/noteuse.class.php');
require_once ($CFG->chemin . '/commun/lib_resultats.php');


$formatin = optional_param("formatin", 'xml', PARAM_ALPHA);
$formatout = optional_param("formatout", 'xml', PARAM_ALPHA);
$email = optional_param("email", '', PARAM_CLEAN);
$dump = optional_param("dump", '0', PARAM_INT);

if ($dump)
    print_object('contenu $_POST',$_POST);



//identifiant de l'examen'
$idnat = required_param('data', PARAM_RAW);

//relecture caract�ristiques
$examen=get_examen_byidnat($idnat);

//identifiant de l'utilisateur'
$user=required_param("user",PARAM_CLEAN);

//dated�but  du passage
$date_debut=required_param("date_debut",PARAM_INT);

$output = array ();
$input = null;

//pour un tracking correct
$USER->type_plateforme="webservice";
$USER->id_etab_perso=$CFG->universite_serveur;
$USER->type_user="A";
$USER->id_user=$user;
$USER->login=$user;  //vire les notices dans la noteuse

switch ($formatin) {
    case 'html':  // le seul support� � ce jour !!!
        $input['questionsids']=required_param('questionsids',PARAM_RAW,'pas de liste de questions');
        $input['reponses']=required_param('r',PARAM_RAW,'pas de r�ponses');
        break;
    case 'json' :
        $input = json_decode($data);
        break;
    case 'xml' :
        try {
            $objXML = new xml2Array();
            $input = $objXML->parse($data);
        } catch (Exception $e) {
            echo $e->getMessage();
            espion2("acces_REST", "corrige_examen", $ide . "." . $idq . traduction('err_xml'));
        }
        break;
    case 'php' :
        $input = unserialize($data);
        break;
    default :
        break;

}

if ($input) {
      espion2("acces_REST", "corrige_examen", $idnat);
    //questionsids doit �tre l'info envoy�e par get_anonyme cad une chaine 1.1_1.2_ ....
    //reponses doit �tre un tableau avec des cl�s idetab_id_question_idreponse =1 si coch�e
    // ceci correspond a l'element id envoy� pour chaque r�ponse
    $noteuse = new noteuseALaVolee($input['questionsids'], $input['reponses']);
    $res=$noteuse->note_etudiant($USER);

     if (($examen->type_tirage!=EXAMEN_TIRAGE_PASSAGE)&& (!empty($user))){
     	$res->ip_max=$REMOTE_ADDR;   //calcul�e par c2i_params
     	$res->ts_date_max=time();
     	$res->origine=$res->ip_max.'@webservice';  // rev 978
     	$res->ts_date_min=$date_debut;
     	enregistre_resultats($examen->id_examen,$examen->id_etab,$user,$res);
     }

     if (!empty($email) && !empty($user)) {
         
         $CFG->smtp_debugging=false; // IMPORTANT sinon pollue la sortie json 
         
         require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

         if ($examen->template_resultat != "")
             $resultats=affiche_template_resultats($res,$ligne);
         else
             $resultats=affiche_resultats($res,$examen->resultat_mini,false ); //, $avecParcours);
         $parcHTML="";
         if ($CFG->utiliser_notions_parcours && $CFG->creer_parcours_html) {
             $parcHTML=cree_parcours_HTML($examen,$res);
         }
         $message=$resultats;
         // On fait le meange dans le message (lien relatif, bouton)
         $message = str_replace("../..", "$CFG->wwwroot", $message);
         $message = preg_replace('/(.*)input(.*)type="button"?(.*)/i', "", $message);
         $message = preg_replace('/(.*)input(.*)type="submit"?(.*)/i', "", $message);
         $message = preg_replace('/(.*)i_quitter(.*)Fermer"?(.*)/i', "", $message);
         $message = preg_replace('/(.*)fermer(.*)Fermer"?(.*)/i', "", $message);
         // rev 871
         if ($parcHTML)
             $message .= $parcHTML;
         require_once ($CFG->chemin_commun . "/lib_mail.php");
         if (send_mail($user,traduction ("resultat_positionnement"),$message))
             $res->info_mail=traduction("msg_mail_envoye"). " ".$email;
         else
             $res->info_mail=traduction("err_msg_mail_envoye"). " ".$email;

     } else $res->info_mail='';

    //m�nage
    unset($res->tab_debug);
    unset($res->tab_points);

     $output = (array)$res;

    // ajout des libell�s des domaines pour affichage
   // $referentiels=get_referentiels();
   //unqiuement ceux trait�s par cet examen
   $referentiels=get_referentiels_liste($examen->referentielc2i,'referentielc2i',false);
    foreach ($referentiels as $referentiel)
    // important de mettre htmlentities voir lib_xml/array_for_question
    	$output["domaines"][$referentiel->referentielc2i]=htmlentities($referentiel->domaine,ENT_COMPAT, 'UTF-8') ;

    $output["resultats"]="";
    $output["examen"]=$idnat;
    $output["examen_titre"]=htmlentities($examen->nom_examen,ENT_COMPAT, 'UTF-8');



} else {
    $output["error"] = traduction("err_donnees_invalides");
    espion2("acces_REST", "corrige_examen", "err_donnees_invalides");
}


$output["version"] = $CFG->version;
ob_clean();

//dump_event ("corrigé examen Android",$output);

switch ($formatout) {
    case 'json' :
       if (!function_exists('json_encode'))  //PHP < 5.2
          echo mon_json_encode($output);
        else echo json_encode($output);
        break;
    case 'xml' :
        $name = 'resultats.xml';
        header("content-type: text/xml");
        header('Content-Disposition: attachment; filename="' . $name . '"');
        try {
            $xml = new array2xml('examen', 'item');
            $xml->createNode($output);
            echo $xml;
        } catch (Exception $e) {
            echo $e->getMessage();
            espion2("acces_REST", "corrige_examen", $ide . "." . $idq . traduction ("err_XML"));
        }
        break;
    case 'php' :
        echo serialize($output);
        break;
    default :
        print_object('',$output);
        break;

}

//important
detruire_session();
die();
?>
