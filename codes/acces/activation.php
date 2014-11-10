<?php

/**
 * @author Patrick Pollet
 * @version $Id: activation.php 1219 2011-03-15 10:45:33Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	v�rification de l'identifiant / m�l
//  envoi d'un nouveau mot de passe
//  l'utilisateur devra l'activer
//
////////////////////////////////


//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

//cl� d'activation  unique parametre requis
$act=required_param("act",PARAM_CLEAN);

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates

$tpl = new C2IMiniPopup( );    //cr�er une instance

//reponse
$info=<<<EOF
{infos}
<hr/>
<div class="centre">
 {bouton:fermer}
 </div>
EOF;

$tpl->assignInclude("contenu",$info,T_BYVAR);  // le template g�rant la configuration
$tpl->prepare($chemin);
$tpl->assign("elt","");
$info=traduction("err_mdp_active");
if (strlen($act)==32) {
    $ligne=get_record("utilisateurs","futur_verif='".$act."' and futur_mdp!=''");
	if ($ligne) {
        $ligne->password=$ligne->futur_mdp;
        $ligne->futur_mdp='';
        espion2 ("activation_mot_de_passe","utilisateur",$ligne->login);
        update_record("utilisateurs",$ligne,"login");
        $info=traduction("mdp_active");
    } else espion2("activation_mot_de_passe","utilisateur inconnu",$act);
} else espion2("activation_mot_de_passe","cl� d'activation inconnue",$act);
$tpl->assign("infos",$info);
$tpl->traduit ("titre_popup","mdp_active");
$tpl->printToScreen();

?>