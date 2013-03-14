<?php
/**
 * @author Patrick Pollet
 * @version $Id: action.php 1276 2011-11-05 09:00:59Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Action (insérer / mettre à jour l'item)
//	à la sortie on affiche la fiche de l'item et la page appelante (liste d'items) est rechargée
//TODO réécrire avec insert_record !!!!!
////////////////////////////////


$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres
require_login('P'); //PP
v_d_o_d("config");

$id=required_param("id",PARAM_INT);
//$ligne=new StdClass();
$ligne=new profil();

$ligne->intitule=required_param("intitule",PARAM_CLEAN);


foreach (get_object_vars($ligne) as $cle=>$valeur ) {
    if (is_numeric($valeur))
        $ligne->$cle=optional_param($cle,0,PARAM_INT);
}

if ($id==-1) {
        $id=insert_record("profils",$ligne,true,'id_profil');
		//tracking :
		espion3("ajout", "profil",$id,$ligne);
}
else {
    $ligne->id_profil=$id;
    update_record("profils",$ligne,'id_profil','');
	//tracking :
	espion3("modification","profil", $id,$ligne);
}

// rafraichir l'ouvreur (../acces.php)'

// $parent=$CFG->W3C_strict?'acces2.php':'acces.php';
$parent='acces2.php';

if ($CFG->montrer_fiche_apres_modification)
    redirect("fiche_profil.php?idq=".$id,$parent);
else
    ferme_popup($parent,true);


?>