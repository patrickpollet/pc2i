<?php

/**
 * @author Patrick Pollet
 * @version $Id: actionv2.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Action (ins�rer / mettre � jour l'item / attacher un document)
//	� la sortie, si un document est attach� on retourne � l'ajout (en mode modification)
//	sinon on affiche la fiche de l'item et la page appelante (liste d'items) est recharg�e
//  rev 1.41 utilise $CFG->chemin_ressources

// version r�duite pour ne changer QUE les domaine/comp�tence/famille


////////////////////////////////


$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

require_login('P'); //PP

$id=required_param("id",PARAM_INT);
$url_retour=optional_param("url_retour","",PARAM_CLEAN);
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //important de le retrouver


// rev 836 gestion de annuler en cas de duplication
$annuler=optional_param("bouton_annuler","",PARAM_CLEAN);
if ($annuler) {
    ferme_popup("liste.php?".$url_retour,true);
}

//donn�es recues du formulaire
// rev 977

$data=array('referentielc2i','alinea');

$ligne=new StdClass();

foreach ($data as $champ)
    $ligne->$champ=required_param($champ,PARAM_CLEAN);


// rev 836  sur nationale seulement

// rev 836 sur la nationale on peut/doit changer directement la famille valid�e
if ($CFG->universite_serveur==1 && is_super_admin()) {
    if ($famille=optional_param("famille",0,PARAM_INT))
        $ligne->id_famille_validee=$famille;  //sinon touche pas !
}
else {
    $ligne->famille_proposee=optional_param("famille_proposee","",PARAM_RAW);
     if ($famille=optional_param("famille",0,PARAM_INT))
        $ligne->id_famille_proposee=$famille; //sinon touche pas !
}




if ( $id == "-1" ){ // ajout de l'item
    erreur_fatale('err_action_invalide','');
}
else { // modification de l'item

    //rev 843 un expert peut modifier une question sur la nationale ...
	 if ($CFG->universite_serveur == 1) {
             $nbvalid = nb_validations($id, $ide);
            if ($nbvalid > 0) {
                if (! is_super_admin()) {
                    erreur_fatale("err_modif_question_validee",$nbvalid);
                }
            } else if (! a_capacite("qv",1)
                       && ! a_capacite("qm"))  // sur CETTE question, pas sur 1 !!!
                            erreur_fatale("err_droits");

        } else v_d_o_d("qm");

	$ligne->id=$id; //important !
    $ligne->id_etab=$ide; // capital (recu de formulaire)
    $ligne->ts_datemodification=time();
    // eexmple d'usage d'update_record avec deux cl�s identifiant le bon record ...
     update_record("questions",$ligne, 'id','id_etab');
	//tracking :
	espion3("modification","question", $ide.".".$id,$ligne);
}


/////////////////////////////////////////////
// transformation de la question en xml
/////////////////////////////////////////////
if ($CFG->generer_xml_qti) {
    require_once ($CFG->chemin_commun."/lib_xml.php");
    to_xml($id, $ide);
}
/////////////////////////////////////////////

// url de la fiche de l'item

$url_retour=optional_param("url_retour","",PARAM_CLEAN);

if ($CFG->montrer_fiche_apres_modification)
    redirect("fiche.php?idq=".$id."&ide=".$ide,"liste.php",$url_retour);
else
    ferme_popup("liste.php?".$url_retour,true);

?>
