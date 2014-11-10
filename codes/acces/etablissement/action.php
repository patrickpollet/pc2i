<?php


/**
 * @author Patrick Pollet
 * @version $Id: action.php 1276 2011-11-05 09:00:59Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Action (ins�rer / mettre � jour l'item / attacher un document)
//	� la sortie, si un document est attach� on retourne � l'ajout (en mode modification)
//	sinon on affiche la fiche de l'item et la page appelante (liste d'items) est recharg�e
//
////////////////////////////////


//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_login('P'); //PP




$id=required_param("id",PARAM_CLEAN);
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT);

$url_retour=optional_param("url_retour","",PARAM_LOCALURL);

v_d_o_d("config");

//donn�es recues du formulaire
$data=array('nom_etab','param_nb_items','param_nb_aleatoire'); // rev 928 sans interet ,'nb_quest_recup');


//pour une nouvelle composante on prends par d�faut le p�re
if ($id ==-1){
	$ligne_pere=get_etablissement($ide,false); //infos du p�re , pas fatale
	if ($ligne_pere) $ligne=$ligne_pere;
    else $ligne=new StdClass();
    $ligne->nb_telechargements=0; // obligatoire !

} else
		$ligne=new StdClass(); // on ne met a jour que ce qui �tait dans la forme

//recup des valeurs saisies
foreach ($data as $champ)
	$ligne->$champ=required_param($champ,PARAM_CLEAN); // tout est requis et a �t� valid� par validation.js

//cas particulier des checkboxes (n'envoient rien si non coch�es !')

$ligne->positionnement=optional_param("positionnement",0,PARAM_INT); // si non recue alors 0
$ligne->certification=optional_param("certification",0,PARAM_INT);
$ligne->locale=1; //forcement
$ligne->nationale=0; //forcement

if ($id == "-1") { // ajout de l'item
    if ($ide == 1) {
        $sqlm = "select max(id_etab) as maxi from {$CFG->prefix}etablissement where id_etab<1000;";
        $rowm=get_record_sql($sqlm); //ne peut pas �chouer ???
        if ($rowm->maxi > 0) {
            $ligne->id_etab = $rowm->maxi + 1;
            $ligne->pere=$ide;
            $id=insert_record("etablissement",$ligne,true,'id');
        }
    } else {
        // r�cup�ration du num�ro max de la composante commen�ant par
        $usp1 = intval($CFG->universite_serveur) + 1;
        $sqlm = "select max(id_etab) as maxi from {$CFG->prefix}etablissement where id_etab>" . intval($CFG->universite_serveur) . "000 and id_etab<" . $usp1 . "000;";
        $rowm = get_record_sql($sqlm);   // ne peut pas �chouer ???
        if ($rowm->maxi > 0)
            $ligne->id_etab = $rowm->maxi + 1;
        else
            $ligne->id_etab= intval($CFG->universite_serveur . "001");
        $ligne->pere=$ide ; // on peut avoir des sous-sous composantes ! $CFG->universite_serveur; // obligatoire !
        $id=insert_record("etablissement",$ligne,true,'id');
    }
    //tracking :
    espion3("ajout","etablissement", $id,$ligne);

} else { // modification de l'item
    $ligne->id_etab=$id;  //crit�re de maj
    update_record("etablissement",$ligne,"id_etab",'',true);
    //tracking :
    espion3("modification", "etablissement", $id,$ligne);
}


// url de la fiche de l'item
// rafraichir l'ouvreur (../acces.php)'


if (!$url_retour) {
    $parent=$CFG->W3C_strict?'../acces2.php':'../acces.php';
	if ($CFG->montrer_fiche_apres_modification)
		redirect("fiche.php?idq=".$id,$parent);
	else
		ferme_popup($parent,true);
} else
	ferme_popup($url_retour,true);

?>