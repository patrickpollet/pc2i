<?php


/**
 * @author Patrick Pollet
 * @version $Id: action.php 1276 2011-11-05 09:00:59Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Action (insérer / mettre à jour l'item / attacher un document)
//	à la sortie, si un document est attaché on retourne à l'ajout (en mode modification)
//	sinon on affiche la fiche de l'item et la page appelante (liste d'items) est rechargée
//
////////////////////////////////


//******** Pour chaque page $chemin représente le path(chemin) de script dans le site (à la racine)
//******** ---------------- $chemin_commun représente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images représente le path des images
$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres
require_login('P'); //PP




$id=required_param("id",PARAM_CLEAN);
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT);

$url_retour=optional_param("url_retour","",PARAM_LOCALURL);

v_d_o_d("config");

//données recues du formulaire
$data=array('nom_etab','param_nb_items','param_nb_aleatoire'); // rev 928 sans interet ,'nb_quest_recup');


//pour une nouvelle composante on prends par défaut le père
if ($id ==-1){
	$ligne_pere=get_etablissement($ide,false); //infos du père , pas fatale
	if ($ligne_pere) $ligne=$ligne_pere;
    else $ligne=new StdClass();
    $ligne->nb_telechargements=0; // obligatoire !

} else
		$ligne=new StdClass(); // on ne met a jour que ce qui était dans la forme

//recup des valeurs saisies
foreach ($data as $champ)
	$ligne->$champ=required_param($champ,PARAM_CLEAN); // tout est requis et a été validé par validation.js

//cas particulier des checkboxes (n'envoient rien si non cochées !')

$ligne->positionnement=optional_param("positionnement",0,PARAM_INT); // si non recue alors 0
$ligne->certification=optional_param("certification",0,PARAM_INT);
$ligne->locale=1; //forcement
$ligne->nationale=0; //forcement

if ($id == "-1") { // ajout de l'item
    if ($ide == 1) {
        $sqlm = "select max(id_etab) as maxi from {$CFG->prefix}etablissement where id_etab<1000;";
        $rowm=get_record_sql($sqlm); //ne peut pas échouer ???
        if ($rowm->maxi > 0) {
            $ligne->id_etab = $rowm->maxi + 1;
            $ligne->pere=$ide;
            $id=insert_record("etablissement",$ligne,true,'id');
        }
    } else {
        // récupération du numéro max de la composante commençant par
        $usp1 = intval($CFG->universite_serveur) + 1;
        $sqlm = "select max(id_etab) as maxi from {$CFG->prefix}etablissement where id_etab>" . intval($CFG->universite_serveur) . "000 and id_etab<" . $usp1 . "000;";
        $rowm = get_record_sql($sqlm);   // ne peut pas échouer ???
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
    $ligne->id_etab=$id;  //critère de maj
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