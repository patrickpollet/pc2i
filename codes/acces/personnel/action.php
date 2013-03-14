<?php

/**
 *  @author Patrick Pollet
 * @version $Id: action.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
*/

////////////////////////////////
//
//	Action (insérer / mettre à jour l'item)
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



/**
print_r($_POST);
print_r($_GET);
die();
**/

$id=required_param("id",PARAM_CLEAN);
$old_mdp=optional_param("old_mdp","",PARAM_CLEAN);
$env_mail=optional_param("env_mail",0,PARAM_INT);
$texte_mail=optional_param("texte_mail","",PARAM_RAW);
$hidden_profils=optional_param("hidden_profils","",PARAM_RAW);


$data=array('login','nom','prenom','password','email','auth','etablissement');

$ligne=new StdClass();

foreach ($data as $champ)
	$ligne->$champ=required_param($champ,PARAM_CLEAN);

//seul un admin peut "promouvoir ou pas un autre admin"
if (is_admin()) {
    $ligne->est_admin_univ=optional_param("a_e","N",PARAM_ALPHA);
    $ligne->limite_positionnement=optional_param("a_p","0",PARAM_INT);
}

$ligne->tags=optional_param("tags",'',PARAM_CLEAN);

if ($id == "-1") { // ajout de l'item
   v_d_o_d("ua");

   //  $ligne->etablissement=$USER->id_etab_perso;  NON !!!
    $mdp=$ligne->password; // a garder pour le mail
     $ligne->password = md5($ligne->password);
     $ligne->ts_datecreation=$ligne->ts_datemodification=time();
     $ligne->origine="manuel";  // rev 980
	$insertion = 0;
	$i=0;
	$base=$ligne->login;
	while (($insertion==0) && ($i < 21)){
		$ligne->login=$base;
		if ($i > 0) $ligne->login .= $i;
		if (insert_record("utilisateurs",$ligne,false,'id',false)) {
			$insertion=1;
			//tracking :
			espion3("ajout","utilisateur", $ligne->login,$ligne);
		}
		$i++;
	}
    $id=$ligne->login; // important pour la redirection en bas !
	if (! $insertion) { // impossible
		erreur_fatale("login_non_disponible",$id);
	}

} else { // modification de l'item
    v_d_o_d("um");

    //si le mot de passe a changé (on a envoyé le md5 dans la fiche d'ajout)
    if ($ligne->password != $old_mdp)
         $ligne->password = md5($ligne->password);
    else unset($ligne->password); // pas de mise a jour
    $ligne->ts_datemodification=time();

    update_record ("utilisateurs",$ligne,"login");

    //tracking :
    espion3("modification","utilisateur", $id,$ligne);

    // désaffecter les profils existants pour cet utilisateur
    delete_records("droits","login='".addslashes($id)."'");  // rev 984
}

// affecter les nouveaux profils à la personne
$hf = explode("*", $hidden_profils);
$prof=new StdClass();
$prof->login=$id;
foreach ($hf as $fval) {
    if (($fval != "0") && ($fval != "")) {
        $prof->id_profil=$fval;
        insert_record("droits",$prof,false,'');
    }
}
if ($env_mail) {
    // envoyer un mail
    require_once ($CFG->chemin_commun . "/lib_mail.php");
    $ligne->password=$mdp; // non encodé md5 !!!
    list($bidon,$text_to_send)=substitue("",$texte_mail,false,$ligne);
    $ret=send_mail($ligne->login,traduction("compte_cree"),"",$text_to_send);
   // print("sm =".$ret); print($text_to_send);die();
}
// url de la fiche de l'item

$url_retour=optional_param("url_retour","",PARAM_CLEAN);
$liste="liste.php?idq=".$ligne->etablissement;
if ($CFG->montrer_fiche_apres_modification)
    redirect("fiche.php?refresh=1&id=".$id,$liste,$url_retour);
else
    ferme_popup($liste."&amp;".$url_retour,true);



?>