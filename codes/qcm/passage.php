<?php
/**
 * @author Patrick Pollet
 * @version $Id: passage.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	passage de qcm
//
////////////////////////////////

set_time_limit(0);
/*
* Pour la description des diff�rentes m�thodes de la classe TemplatePower,
* il faut se ref�rer � http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

if (!is_utilisateur_anonyme())   //l'anonyme a le type_user =A il peut aller ici et dans action.php basta
	require_login("E"); //PP

$idq=required_param("idq",PARAM_INT);
$ide=required_param("ide",PARAM_INT);
$entree_mdp=optional_param("entree_mdp","",PARAM_CLEAN); //retour formulaire saisie MDP
$url_retour=optional_param("url_retour","",PARAM_PATH);

// v�rification du fait que l'examen se d�roule bien maintenant
//fait plus tard a cause des pools ????

$ligne=get_examen($idq,$ide);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

//si c'est un pool va chercher le meilleur fils et fait l'inscription
if ($ligne->est_pool == 1) {
	$ligne = affecte_groupe_pool($idq, $ide,$USER->id_user);
	if (!$ligne)
		erreur_fatale("pas de groupe pour ce cr�neau horaire");
	$idq = $ligne->id_examen;
	$ide = $ligne->id_etab;
}

if (! examen_en_cours($ligne)) {
	if (is_utilisateur_anonyme($USER->id_user))
	   detruire_session();
	erreur_fatale("erreur, ce n'est pas la p�riode pr�vue pour passer cet examen");
}

// rev 986
/// Check subnet access pas en mode pr�visualsation 

$ips=get_ips_liste_csv($ligne->subnet);
if (!empty($ips) && !address_in_subnet( $REMOTE_ADDR , $ips)) {
    erreur_fatale(  'erreur_ip_invalide', $REMOTE_ADDR );
}



$type_tirage_examen = $ligne->type_tirage;


$ligne->mot_de_passe = trim($ligne->mot_de_passe);
$form_mdp=0;
if ($ligne->mot_de_passe != "") {
	$form_mdp = 1;
    $errone=0;
	if ($entree_mdp) { //tentative
		if ($entree_mdp == $ligne->mot_de_passe) {
			var_register_session("mot_de_passe_examen", $entree_mdp);
			$form_mdp = 0;
		} else
			$errone = 1;
	} else {
        $mot_de_passe_examen=var_get_session ("mot_de_passe_examen");
	   // retour ici en rechargeant la page ...
		if ($mot_de_passe_examen == $ligne->mot_de_passe)
			$form_mdp = 0;
		else
		    $errone = 2;
    }
}

if ($form_mdp == 1) { // formulaire mot de passe
    $forme=<<<EOF
    <form name="formulaire" action="passage.php" method="post" class="normale">
    <div>
<input type="hidden" name="idq" value={idex} />
<input type="hidden" name="ide" value={idexe} />

<input type="hidden" name="url_retour" value={url_retour} />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

<p class="double">
<label for="entree_mdp">{mdp_requis} </label>
<input type="password" name="entree_mdp" class="saisie" />
</p>
<div class="rouge2">{errone}</div>
<div class="centre">
{bouton:ok}
</div>
</div>

</form>
EOF;
	$tpl->assignInclude("corps", $forme,T_BYVAR); // le template g�rant le formulaire du qcm

	$tpl->prepare($chemin);


    $tpl->assign("_ROOT.titre_popup",nom_complet_examen($ligne));
	$tpl->gotoBlock("_ROOT");

	$tpl->assign("idex", $idq);
	$tpl->assign("idexe", $ide);
    $tpl->assign("url_retour",$url_retour);
	$tpl->setConditionalValue( $errone==1,"errone",traduction("mdp_errone"),"");
	$tpl->assign("terminer", traduction( "bouton_envoyer"));

} else { // passage du qcm

	
	$tpl->assignInclude("corps","<div class='commentaire2'>{infos}</div><hr/>{timer}{ici}",T_BYVAR);
	$tpl->prepare($chemin);
       $tpl->assign("_ROOT.titre_popup",nom_complet_examen($ligne));
add_javascript($tpl,$CFG->chemin_commun."/js/quiz.js");

	// v�rification d'un �ventuel passage pr�c�dent de l'utilisateur pour cet examen
	// et que ce n'est pas un examen anonyme (pouraquoi id_user est different a chaque fois ?)
    //POURQUOI c'est permis dans ce cas ????
	if ($type_tirage_examen != EXAMEN_TIRAGE_PASSAGE && !is_utilisateur_anonyme($USER->id_user)) {
		if (compte_passages($idq,$ide,$USER->id_user)>0)
			erreur_fatale("err_examen_deja_passe");
		else {
			// stockage de l'ip lors du passage
			$sql_ip = "update {$CFG->prefix}qcm set ip='" . $REMOTE_ADDR .
                       "' where login='" . addslashes($USER->id_user) .  // rev 984 slashes
                       "' and id_examen='" . $idq . "' and id_etab='" . $ide . "';";
			$res_ip = ExecRequete($sql_ip);
		}
	}


	if ($ligne->type_tirage==EXAMEN_TIRAGE_PASSAGE)
		$tpl->traduit('infos',"msg_info_tirage_passage");
	else $tpl->assign("infos","");
    $mode=$type_tirage_examen != EXAMEN_TIRAGE_PASSAGE ? QCM_NORMAL:QCM_PASSAGE; //rev 860 important pas enregistrement du tout, peut le passer plusieurs fois
	$ret=imprime_examen($idq,$ide,true,true,false,false,$mode,$USER->id_user,false,$url_retour); //envoie le qcm
	$tpl->assign("ici",$ret[0]);

	$tpl->gotoBlock("_ROOT");
	//$tpl->assign("session_ch", session_name() . "=" . session_id());
	if ($type_tirage_examen != EXAMEN_TIRAGE_PASSAGE) {
		$tpl->assignGlobal("conf_validation", addslashes(str_replace('"', "&quot;", traduction("conf_validation"))));
	} else {
		$tpl->assignGlobal("conf_validation", addslashes(str_replace('"', "&quot;", traduction("conf_fin")))); //SB
	}



  $tpl->assign ("timer",print_timer($ligne));

}

// rev 981 pas de liens validation W3C pour un psagge de QCM pour de vrai !!!
$CFG->W3C_validateurs=0;
$tpl->printToScreen(); //affichage

?>

