<?php

/**
 * @author Patrick Pollet
 * @version $Id: lib_telechargements.php 816 2009-05-25 13:06:33Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 function peut_supprimer_famille ($id) {
    return count_records("questions","id_famille_validee=$id")+
           count_records("questions","id_famille_proposee=$id")==0;

 }


 function peut_supprimer_referentiel ($id) {
    global $CFG;
         return count_records("questions","referentielc2i='".$id."'")+
         count_records("alinea","referentielc2i='".$id."'")+
         count_records("notions","referentielc2i='".$id."'")==0;
 }


 function peut_supprimer_alinea ($id_referentiel,$id_alinea) {
    global $CFG;
         return count_records("questions","referentielc2i='$id_referentiel' and alinea='$id_alinea'")+
         count_records("notions","referentielc2i='$id_referentiel' and alinea='$id_alinea'")+
         count_records("familles","referentielc2i='$id_referentiel' and alinea='$id_alinea'")==0;
 }

/**
 * rev 1041, plus facile a retrouver avec seulement son id num�rique (cl� autonum)
 */
function peut_supprimer_alinea_byid ($id_alinea) {
   if ($alinea=get_alinea_byid($id_alinea,false))
    return peut_supprimer_alinea($alinea->referentielc2i,$alinea->alinea);
   else
    return false;


}


function get_questions_par_famille ($idf,$validees=1,$tri='',$compte=false) {
    global $CFG,$USER;


    $criteres= 'id_famille_validee='.$idf;
    $criteres.=" and {$USER->type_plateforme}='OUI'";
    if ($validees) $criteres.=' and etat='.QUESTION_VALIDEE;

    if($compte)
        return count_records("questions",$criteres,false);

     if (!$tri) $tri="id_etab,id";
      $res = get_records("questions",$criteres,$tri);
      foreach ($res as $num=>$q) {
        $res[$num]->qid=$q->id_etab.".".$q->id;
      }
      return $res;
}


function get_questions_par_referentiel ($ref,$validees=1,$tri='',$compte=false) {
    global $CFG,$USER;
    $criteres= "referentielc2i='".$ref."'";
    $criteres.=" and {$USER->type_plateforme}='OUI'";
    if ($validees) $criteres.=' and etat='.QUESTION_VALIDEE;

    if($compte)
        return count_records("questions",$criteres,false);

    if (!$tri) $tri="id_etab,id";
    $res = get_records("questions",$criteres,$tri);
    foreach ($res as $num=>$q) {
        $res[$num]->qid=$q->id_etab.".".$q->id;
    }
    return $res;
}

function get_questions_par_alinea ($ref,$alinea,$validees=1,$tri='',$compte=false) {
    global $CFG,$USER;
    $criteres= "referentielc2i='".$ref."' and alinea='".$alinea."'";
    $criteres.=" and {$USER->type_plateforme}='OUI'";
    if ($validees) $criteres.=' and etat='.QUESTION_VALIDEE;

    if($compte)
        return count_records("questions",$criteres,false);


    if (!$tri) $tri="id_etab,id";
    $res = get_records("questions",$criteres,$tri);
    foreach ($res as $num=>$q) {
        $res[$num]->qid=$q->id_etab.".".$q->id;
    }
    return $res;
}


?>
