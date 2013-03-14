<?php

/**
 * @author Patrick Pollet
 * @version $Id: nouv_mdp.php 1266 2011-09-20 13:40:42Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");                 //fichier de paramètres

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates


if (!empty($CFG->pas_de_motdepasse_oublie)) // rev 897
   erreur_fatale("err_acces");

$tpl = new C2IMiniPopup( );    //créer une instance



//forme de saisie
$forme=<<<EOF
<form id="monform" name="monform" method="post" action="nouv_mdp.php">
{mdp_oublie_ligne1}

    <input name="mel" type="text" size="40" class="required" title="{js_valeur_non_vide_attendue}" id="mel" />
    <hr/>
{mdp_oublie_ligne2}
    <div class="centre">
    {bouton:annuler} &nbsp; &nbsp;{bouton:ok}
    </div>
</form>

EOF;

//reponse
$info=<<<EOF
{infos}
<div class="centre">
 {bouton:fermer}
</div>

EOF;

$mel=optional_param("mel","",PARAM_RAW);

if ($mel) {

    $tpl->assignInclude("contenu",$info,T_BYVAR);  // le template gérant la configuration

	$tpl->prepare($chemin);

    $CFG->utiliser_infobulle_js=0; // pas besoin
    $CFG->err_mysql_avec_requete=0; //ne pas afficher l'erreur mysql aux hackers !  rev 897
    $CFG->err_mysql_sans_mysqlerror=1;
    // on passe AUSSI un errMsg1 non vide pour ne pas dévoiler l'erreur MySql !  rev 897
	$ligne=get_record ("utilisateurs","login='" . addslashes($mel) . "' or email='" . $mel . "'",true,"err_droits");  // rev 984
	if (!$ligne || empty($ligne->email)) {
		$tpl->assign ("infos",traduction("err_pas_de_mail"));
	}  else
		//if (is_admin($ligne->login,$ligne->etablissement)) {NON ! $USER pas renseigné !
        // rev 894-896 on n'envoie pas le passe d'un admin meme à lui !
        if ($ligne->est_admin_univ=="O" || $ligne->est_superadmin=="O") {
            // rev 977 personnalisation du message
            if ($CFG->universite_serveur==1)
                $tpl->assign ("infos",traduction("err_pas_envoi_mdp_admin_national"));
            else
                $tpl->assign ("infos",traduction("err_pas_envoi_mdp_admin_local"));
            espion2('envoi_mail_refuse','perte_mot_de_passe', $ligne->login);
		} else {
			$nouveau_passe = mot_de_passe_a(7); // génére un nouveau mot de passe de 7 caractères
			$futur_verif = md5(uniqid(rand())); // clé de vérification
			$rec=new StdClass();
			$rec->login=$ligne->login;
			$rec->futur_mdp=md5($nouveau_passe);
			$rec->futur_verif=$futur_verif;
			update_record("utilisateurs",$rec,"login");


			require_once ($CFG->chemin_commun . "/lib_mail.php");

			//utilisation de la fonction traduction avec 6 parametres voir langues/fr.php
			// cette chaine mail_mdp_oublie a 6 %s dedans !
			$html_to_send= traduction ("mail_mdp_oublie",false, $CFG->wwwroot,$futur_verif,$CFG->wwwroot,$futur_verif,$ligne->login,$nouveau_passe);

			espion2('envoi_mail','perte_mot_de_passe', $ligne->login);

			if (send_mail($ligne->login,traduction("mot_de_passe_oublie"),$html_to_send))
				$tpl->assign ("infos",traduction ("info_mail_mdp_oublie",false,$ligne->email));
			else {
				$tpl->assign ("infos",traduction("err_msg_mail_envoye").$ligne->email);
				//print($html_to_send);
			}
		}
} else {
	$tpl->assignInclude("contenu",$forme,T_BYVAR);  // le template gérant la configuration

	$tpl->prepare($chemin);
   $CFG->utiliser_validation_js=1;

}

$tpl->traduit ("titre_popup","mdp_oublie");
      $tpl->assign("_ROOT.elt","");

$tpl->printToScreen();                                      //affichage
?>
