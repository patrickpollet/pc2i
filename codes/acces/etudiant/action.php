<?php
/**
 * @author Patrick Pollet
 * @version $Id: action.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Action (insérer / mettre à jour l'item)
//
////////////////////////////////
/*
 * rev 1.5 ajout droit etudiant_modifier
 */

//******** Pour chaque page $chemin représente le path(chemin) de script dans le site (à la racine)
//******** ---------------- $chemin_commun représente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images représente le path des images
$chemin = '../../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de paramètres

require_login('P'); //PP


/*
 * Array ( [titre] => toto [nom] => toto1 [prenom] => toto1 [numetud] => 123456
 *         [mdp] => toto [cmdp] => toto [mail] => tt@insa [auth] => manuel [bouton_enregistrer] => Enregistrer
 *         [id] => toto [ide] => 65 [c2i] => bfaca697b3c672cdbdba62bf84389063 )
 * Array ( )

print_r($_POST);
print_r($_GET);
die();
*/


$id=required_param("id",PARAM_CLEAN);
$url_retour=optional_param("url_retour","",PARAM_CLEAN);

$data=array('login','nom','prenom','numetudiant','password','email','auth','etablissement');

$ligne=new StdClass();

foreach ($data as $champ)
	$ligne->$champ=required_param($champ,PARAM_CLEAN);

$ligne->tags=optional_param("tags",'',PARAM_CLEAN);

if ( $id == "-1" ){ // ajout de l'item
   v_d_o_d("eta");
      $ligne->ts_datecreation=$ligne->ts_datemodification=time();
      $ligne->origine="manuel";  // rev 980
	$insertion = 0;
	$i=0;
	$base=$ligne->login;
	while (($insertion==0) && ($i < 21)){
		$ligne->login=$base;
		if ($i > 0) $ligne->login .= $i;
		if (insert_record("inscrits",$ligne,false,'id',false)) {
			$insertion=1;
			//tracking :
			espion3("ajout","etudiant", $ligne->login,$ligne);
		}
		$i++;
	}
     $id=$ligne->login; // important pour la redirection en bas !
	if (! $insertion) { // impossible
		erreur_fatale("login_non_disponible",$id);
	}

}
else { // modification de l'item
   v_d_o_d("etm");
    $ligne->ts_datemodification=time();
	update_record ("inscrits",$ligne,"login");

	//tracking :
	espion3("modification","etudiant", $id,$ligne);

}


$liste="liste.php?idq=".$ligne->etablissement;


if ($CFG->montrer_fiche_apres_modification)
    redirect("fiche.php?refresh=1&id=".$id,$liste,$url_retour);
else
    ferme_popup($liste."&amp;".$url_retour,true);

?>