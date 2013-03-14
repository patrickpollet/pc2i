<?php
/**
 * @author Patrick Pollet
 * @version $Id: action.php 1252 2011-05-23 10:20:26Z ppollet $
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
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tre

 if (!$CFG->utiliser_notions_parcours)
    erreur_fatale("err_pas_de_notions_parcours_ici");

require_login("P"); //PP


$ligne=new StdClass();
$id=required_param("id",PARAM_INT);    //-1 nouveau sinon modif
$ide=$USER->id_etab_perso; //etablissement de la notion
$url_retour=optional_param("url_retour","",PARAM_CLEAN);


// rev 836 gestion de annuler en cas de duplication
$annuler=optional_param("bouton_annuler","",PARAM_CLEAN);
if ($annuler) {
    if ($dupliquer=optional_param("dupliquer",0,PARAM_INT)) {
            supprime_ressource ($id);
    }
    ferme_popup("liste.php?".$url_retour,true);
}

//donn�es recues du formulaire
$data=array('titre','referentielc2i','alinea');


foreach ($data as $champ)
    $ligne->$champ=required_param($champ,PARAM_CLEAN);

$ligne->domaine=$ligne->referentielc2i;
$ligne->competence=$ligne->alinea;
// les colonnes sont nommées differemment dans cette table
unset($ligne->referentielc2i);
unset ($ligne->alinea); 
    

$ligne->tags=optional_param("tags",'',PARAM_CLEAN);


$ligne->fichier=required_param("url",PARAM_RAW); //les liens de type http


// les ressources locales sont dans $_FILES (file1, file2 ... plus facile qu'un tableau)


$ligne->modifiable=true;
$ligne->ts_datemodification=time();
$ligne->id_etab=$ide;

if ($id == -1) { // ajout de l'item
	v_d_o_d("qa"); //PP

	$ligne->ts_datecreation=$ligne->ts_datemodification;

	//insert record va virer ligne->id_notion
	$id=insert_record("ressources",$ligne,true,'id',true);

	//tracking :
	 espion3("ajout", "ressource",$id,$ligne);


} else { // modification de l'item
	if (isset ($duplication))  //cette varaible n'existe pas ?'
		v_d_o_d("qd");
	else
		v_d_o_d("qm"); //PP
	//update_record VEUT ligne->id_notion
	$ligne->id=$id;
	update_record("ressources",$ligne,'id');

	//tracking :
	espion3("modification", "ressource",$id,$ligne);

}



if ($CFG->montrer_fiche_apres_modification)
    redirect("fiche.php?id=".$id."&ide=".$ide,"liste.php",$url_retour);
else
    ferme_popup("liste.php?".$url_retour,true);




?>

