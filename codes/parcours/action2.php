<?php

/**
 * @author Patrick Pollet
 * @version $Id: action.php 621 2009-04-02 17:31:40Z ppollet $
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
/**
 *
 * rev 1.41 PP 07/02/2009 ne doit rien afficher et recharger la liste !
 */
//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

 if (!$CFG->utiliser_notions_parcours)
    erreur_fatale("err_pas_de_notions_parcours_ici");

require_login("E"); //PP


$ligne=new StdClass();
$id=required_param("id",PARAM_INT);    //-1 nouveau sinon modif
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); // rev 970  (notice en ligne 87)


// rev 836 gestion de annuler en cas de duplication
$annuler=optional_param("bouton_annuler","",PARAM_CLEAN);
if ($annuler) {
    if ($dupliquer=optional_param("dupliquer",0,PARAM_INT)) {
             supprime_parcours($id);
    }
    ferme_popup("liste.php?".$url_retour,true);
}


$titre=optional_param("titre","",PARAM_RAW);
//$notion=optional_param("notion",array(),PARAM_RAW);  //tableau des notions coch�es
$notion=optional_param("ressource",array(),PARAM_RAW);  //tableau des notions coch�es
$url_retour=optional_param("url_retour","",PARAM_CLEAN);

$ligne->login=optional_param('login',$USER->id_user,PARAM_RAW);  // on g�re SES parcours sauf un admin

$ligne->ts_datemodification=time();
$ligne->titre=$titre;

$ligne->tags=optional_param("tags",'',PARAM_CLEAN);

/////////////////////////////////////////
if ($id == -1) { // ajout de l'item

   $ligne-> type="creation";
   $ligne->ts_datecreation=$ligne->ts_datemodification=time();
   $ligne->examen="";
   $id=insert_record("parcours",$ligne,"id_parcours",true,true);
    //tracking :
    espion3("ajout","parcours",$id,$ligne);

} else { // modification de l'item
	$ligne->id_parcours=$id;  //important
    update_record("parcours",$ligne,"id_parcours");
    //delete_records("notionsparcours",'id_parcours='.$id);    //purge anciennes notions
    delete_records("ressourcesparcours",'id_parcours='.$id);    //purge anciennes notions
    //tracking :
    espion3("modification", "parcours", $id,$ligne);
}

$ligne_np=new StdClass(); //partie comune
$ligne_np->id_parcours=$id;
$ligne_np->ts_datecreation=$ligne_n->ts_datemodification=time();
$nb=0;
foreach($notion as $n) {
      //$ligne_np->id_notion=$n;
      //insert_record("notionsparcours",$ligne_np,false,false);
      $ligne_np->id_ressource=$n;
      insert_record("ressourcesparcours",$ligne_np,false,false);
      $nb++;
}


if ($CFG->montrer_fiche_apres_modification)
    redirect("fiche.php?id=".$id."&ide=".$ide,"liste.php",$url_retour);
else
    ferme_popup("liste.php?".$url_retour,true);


?>



