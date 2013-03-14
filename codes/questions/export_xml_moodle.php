<?php

/**
 * @version $Id: export_xml_moodle.php 1260 2011-07-20 14:35:30Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */



ob_start(); //important

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");	//fichier de parametres
require_once($chemin_commun."/lib_xml.php"); // classes d'export



$validees=optional_param("validees",1,PARAM_INT);
$type_pf=optional_param("type_pf","positionnement",PARAM_ALPHA);
// rev 1.5 983
$CFG->moodleversion=optional_param("moodleversion",19,PARAM_INT);


require_login('P');

//if (!  is_admin (false,$CFG->universite_serveur))
if (! is_admin ())
            erreur_fatale("err_droits");

$name=$CFG->c2i.'_questions_'.$type_pf.'_'.time().'.xml';

header("content-type: text/xml");
header('Content-Disposition: attachment; filename="'.$name.'"');


function genere_description ($referentiel,$alinea) {

    $titre="";
    $name="";
    if ($referentiel) {
        $ref=get_referentiel($referentiel);
        $name=$ref->referentielc2i;
        $titre=$ref->referentielc2i.":".$ref->domaine;
        if ($alinea) {
            $alin=get_alinea($alinea,$referentiel);
            $name.=".".$alin->alinea;
            $titre .="\n".$alin->alinea.":".$alin->aptitude;
        }

    }

     $q=new description_xml_moodle ($name,$titre);
     return $q->toxml();
}

function genere_categorie($nom) {
	 $cat=new category_xml_moodle($nom);
    return $cat->toxml();
}

function genere_question ($question) {
    global $CFG;
    $typemoodle=isset($CFG->qtype_moodle)?$CFG->qtype_moodle:"multichoice";
    /**
     $q=new question_xml_moodle ($typemoodle,$question->id,$question->id_etab,$question->titre,
     get_reponses($question->id,$question->id_etab,false,false),
     get_documents($question->id,$question->id_etab, false));
     **/

    switch ($CFG->moodleversion) {
        case 19 :
            $q=new question_xml_moodle ($typemoodle,$question,
                get_reponses($question->id,$question->id_etab,false,false),
                get_documents($question->id,$question->id_etab, false));
            break;
        case 20 :
            $q=new question_xml_moodle_20 ($typemoodle,$question,
                get_reponses($question->id,$question->id_etab,false,false),
                get_documents($question->id,$question->id_etab, false));
            break;
        default:
            $q=new question_xml_moodle_21 ($typemoodle,$question,
                get_reponses($question->id,$question->id_etab,false,false),
                get_documents($question->id,$question->id_etab, false));
        break;


    }
    return $q->toxml();
}

function genere_questions($type_pf,$titre,$validees) {
    global $CFG;
    //liste de toutes les questions tri�es
    $questions=get_toutes_questions($validees,'referentielc2i,alinea');
    $topcat=$CFG->c2i.'/'.$type_pf; //cat�gorie Moodle
    $ret=genere_categorie($topcat);
    $d=new description_xml_moodle ($CFG->c2i,$titre);
    $ret .=$d->toxml();
    $curref="";
    $curalinea="";
    foreach ($questions as $question) {
    	if ($question->$type_pf !='OUI')
    		continue;

        if ($question->referentielc2i !=$curref) {

            $ret.=genere_categorie($topcat.'/'.$question->referentielc2i);
            $ret .=genere_description ($question->referentielc2i,false);
            $curref=$question->referentielc2i;
        }
        if ($question->alinea !=$curalinea) {
            $ret.=genere_categorie($topcat.'/'.$question->referentielc2i."/".$question->alinea);
            $ret .=genere_description ($question->referentielc2i,$question->alinea);
            $curalinea=$question->alinea;
        }
        $ret .= genere_question($question);
    }
    return $ret;
}



$xml  = "<?xml version=\"1.0\" encoding=\"$CFG->encodage\"?>\n<quiz>";

//tests
//$USER->type_plateforme="positionnement";
$titre=traduction("plp");
if ($type_pf=='certification')
    $titre=traduction("plc");
else  //eviter un param�tre invalide
    $type_pf="positionnement";
//print ($validees); die();
$xml.=genere_questions($type_pf,$titre,$validees);
$xml .="\n</quiz>";

while (@ob_end_clean());  //important
echo $xml;

?>