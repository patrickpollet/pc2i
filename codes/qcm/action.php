<?php
/**
 * @version $Id: action.php 1276 2011-11-05 09:00:59Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";

require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_once ($chemin_commun . "/lib_resultats.php"); //n'est pas charg� par c2i_params

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
if (!is_utilisateur_anonyme())   //l'anonyme a le type_user =A il peut aller ici et dans action.php basta
	require_login("E"); //PP
$idq=required_param("idex",PARAM_INT);
$ide=required_param("idexe",PARAM_INT);
$mot_de_passe_examen=optional_param("mot_de_passe_examen","",PARAM_RAW);
$questions=required_param("questions",PARAM_RAW);
$mode=required_param("mode",PARAM_INT); // mode de passage (voir lib_examens.php/imprime_examens
$nbquestions=required_param("nbquestions",PARAM_INT);
$nbreponses=required_param("nbreponses",PARAM_INT);
$reponses=optional_param("r",array(),PARAM_RAW);  //pb si rien de coch� !
$url_retour=optional_param("url_retour","",PARAM_CLEAN);

// r�vision 981 si c'est le chrono qui a forc� l'envoi il FAUT quand m�me corriger ...
// pb avec beaucoup de candidats certains ne sont alors pas not�s
// cf
$tempsexpire=optional_param("temps_expire",0,PARAM_INT);


$fiche_pos=<<<EOF
<!-- START BLOCK : rafraichi_liste -->
<script type="text/javascript">
        if (window.opener)
            window.opener.location.href='liste.php?{url_retour}';
</script>


<!-- END BLOCK : rafraichi_liste -->

<div id="corps">
<div class="centre">
<span class="titre">{nom_du_candidat} {score_global}  {scoreg}</span><br><br>

{bouton:imprimer}<br />
{msg_mail_envoye}<br/>
<span class="commentaire1"> {msg_pour_sauver} </span>
</div>
    <div id="xx">
        <ul id="tabs">
        <!-- START BLOCK : resultats_tab -->
            <li> <a class="active_tab"  href="#resultats">{resultats} </a></li>
        <!-- END BLOCK : resultats_tab -->
        <!-- START BLOCK : corrige_tab -->
            <li> <a class=""            href="#corrige">{corrige}</a> </li>
        <!-- END BLOCK : corrige_tab -->
         <!-- START BLOCK : parcours_tab -->
            <li> <a class=""            href="#parcours">{parcours}</a> </li>
        <!-- END BLOCK : parcours_tab -->

        </ul>
<!-- START BLOCK : resultats -->
<div class="panel" id="resultats">
{ici}
{liste_liens}
</div>
<!-- END BLOCK : resultats -->
<!-- START BLOCK : corrige -->
<div class="panel" id="corrige">
{ici}
</div>
<!-- END BLOCK : corrige -->
<!-- START BLOCK : parcours -->
<div class="panel" id="parcours">

       <!-- START BLOCK : parcours_cree -->
        <div class="titre1">{nom_parcours} </div>
        <div id="menuLayer" class="gauche">
            {ici}
        </div>
        <!-- END BLOCK : parcours_cree -->
        <!-- START BLOCK : creer_parcours -->

            <div id="maj_ajax">
              <div class="information_gauche">{info_creation_parcours}</div>
         <form id="monform">
        <input type="hidden" name="idq" value="{idq}"/>
        <input type="hidden" name="ide" value="{ide}"/>
        <!-- START BLOCK : id_session -->
            <input name="{session_nom}" type="hidden" value="{session_id}">
        <!-- END BLOCK : id_session -->
        {bouton_creer_parcours}
        </form>
        </div>
        <!-- END BLOCK : creer_parcours -->
    </div>
</div>

<!-- END BLOCK : parcours -->

<div class="centre">
<!-- START BLOCK : fin_fiche_pos -->

<input name="fermer_fiche_pos" type="button" class="saisie_bouton" id="fermer_fiche_pos"
   onClick="if (confirm('{conf_fermeture_fiche_pos}')) window.close();"
   value="{fermer_fiche_pos}">

<!-- END BLOCK : fin_fiche_pos -->

<!-- START BLOCK : fin_fiche_test -->
{bouton_retour}

<!-- END BLOCK : fin_fiche_test -->
</div>
</div>

EOF;

$fiche_cert=<<<EOF
<!-- START BLOCK : rafraichi_liste -->
<script type="text/javascript">
        if (window.opener)
            window.opener.location.href='liste.php?{url_retour}';
</script>


<!-- END BLOCK : rafraichi_liste -->
{corps}
<hr/>
<div class="centre">
{bouton:fermer}
</div>

EOF;


$tpl = new C2IPopup(); //cr�er une instance

if ($USER->type_plateforme == "positionnement" || $mode==QCM_TEST)
    $tpl->assignInclude("corps", $fiche_pos,T_BYVAR);
else if ($USER->type_plateforme == "certification")
	$tpl->assignInclude("corps", $fiche_cert,T_BYVAR);
$tpl->prepare($chemin);

if ($url_retour) {
    $tpl->newBlock ("rafraichi_liste");
    $tpl->assign("url_retour",urldecode($url_retour));
    $tpl->gotoBlock("_ROOT");

}

$CFG->utiliser_prototype_js=1;  //forc�
$CFG->utiliser_fabtabulous_js=1;


set_time_limit(0);


$ligne=get_examen($idq,$ide);


if ($mode!=QCM_TEST) {    // un prof teste son examen ?
	if (!examen_en_cours($ligne)  && !$tempsexpire) // rev 981
		erreur_fatale("err_examen_non_dispo");
	if (($mot_de_passe_examen) && ($mot_de_passe_examen != $ligne->mot_de_passe))
		erreur_fatale("err_examen_mdp");

	if ($USER->type_plateforme == "positionnement"){

		if (!is_utilisateur_anonyme()) {
			$tpl->assign("_ROOT.titre_popup", traduction("resultats_passage")."<br/>".nom_complet_examen($ligne));
		}
		else {
			$tpl->assign("_ROOT.titre_popup", traduction("resultats_passage_anonyme")."<br/>".nom_complet_examen($ligne));
		}
	}else $tpl->assign("_ROOT.titre_popup", traduction("fin_passage")."<br/>".nom_complet_examen($ligne));

} else {
    v_d_o_d("em");  //oui verifier que c'est bien un "bon prof"'
}
$tpl->assign("_ROOT.titre_popup", traduction("fin_passage"));





// V 1.5 on note maintenant pour tous types d'examen


$res=note_examen ($idq,$ide,$mode,$USER->id_user,$questions,$reponses,$nbquestions,$nbreponses);
//print_r($res);

espion3("passage", "qcm", $ide . "." . $idq, $res);

$tpl->assign("_ROOT.msg_mail_envoye", "");


if ($USER->type_plateforme == "positionnement" || $mode==QCM_TEST) { //un prof peut voir ses r�sultats
	$score_global = max(0, round($res->score_global, 2));
    // rev 983
    if (! empty($CFG->ne_pas_afficher_score_global)) {
        $tpl->assign("score_global",traduction ('score_global',true).' : ');
        $tpl->assign("scoreg", $score_global . " %");
    } else {
           $tpl->assign("score_global",'');
           $tpl->assign("scoreg", '');
    }

	$tpl->assign ("nom_du_candidat",get_fullname($USER->id_user));
	$tpl->newBlock("resultats_tab");


	$avecParcours= $ligne->type_tirage!=EXAMEN_TIRAGE_PASSAGE  // REV 871  on n' pas enregistr� les scores dans ce cas !
                  && !is_utilisateur_anonyme() && $CFG->utiliser_notions_parcours && $mode !=QCM_TEST;
	if ($avecParcours) {
            add_javascript($tpl,$CFG->chemin_commun."/pear//HTML_TreeMenu/TreeMenu.js"); //important !
            $tpl->newBlock("parcours_tab");
            $tpl->newBlock("parcours");
            $CFG->parcours_auto_positionnement=0;
            if ($CFG->parcours_auto_positionnement) {
                $tpl->newBlock("parcours_cree");
                require_once($CFG->chemin_commun."/pear/HTML_TreeMenu/TreeMenu.php");
                $idp= cree_parcours_croisement_examen ($idq,$ide,$USER->id_user);
                $menu=parcours_en_menu($idp);
                $treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $CFG->chemin_commun."/pear/HTML_TreeMenu/images",
                    'defaultClass' => 'treeMenuDefault'));

                $tpl->assign("ici",$treeMenu->toHTML());
                $parcours=get_parcours($idp);
                $tpl->assign("nom_parcours",$parcours->titre);
            } else  {

                //TODO utiliser les cases coch�es dans l'�cran r�sultats ???
               $CFG->utiliser_prototype_js=1;  //forc�
               $tpl->newBlock("creer_parcours");
               $tpl->assign("idq",$idq);
               $tpl->assign("ide",$ide);
               form_session($tpl);
               $onClick=<<<EOC
               javascript:majDiv("maj_ajax","$CFG->chemin_commun/ajax/cree_parcours.php",false,"monform");

EOC;
               print_bouton ($tpl,"bouton_creer_parcours","creer_parcours",$onClick);
             }
    }
	$tpl->newBlock("resultats");
    //en version finale il n'est pas n�cessaire d'afficher la colonne parcours
    // si on a un deja l'onglet et la liste statique

	if ($ligne->template_resultat != "")
    		$resultats=affiche_template_resultats($res,$ligne);
	else{

    		$resultats=affiche_resultats($res,$ligne->resultat_mini,false ); //, $avecParcours);
	}
    $tpl->assign("ici",$resultats);  // ligne remise PP 07/04/2009 !

 // rev 877 cr�ation d'un parcours statique � sauver sur cl� (comme en 1.4)
    $parcHTML="";
    if ($CFG->utiliser_notions_parcours && $CFG->creer_parcours_html) {
         $parcHTML=cree_parcours_HTML($ligne,$res);
        $tpl->assign ("liste_liens",$parcHTML);
    } else $tpl->assign ("liste_liens","");


	if (!is_utilisateur_anonyme()) {
        // rev 982 jamais de corrig� comment� si tirage lors du passage car r�sultats pas enregistr�s ...
		if ($ligne->correction == 1 && $ligne->type_tirage!=EXAMEN_TIRAGE_PASSAGE) {
			$tpl->newBlock("corrige_tab");
			$tpl->newBlock("corrige");
			$ret=imprime_examen($idq,$ide,false,false,false,false,QCM_CORRECTION,$USER->id_user,$questions);
			$tpl->assign("ici",$ret[0]);
		}
	}


	if ($ligne->envoi_resultat // rev 835 option par examen
	    ||  is_utilisateur_anonyme()){
		$destinataire = get_mail($USER->id_user);   //cherche partout
		if ( !empty($destinataire)) { // si il a donn� un mail !
		    $message=$resultats;
			// On fait le meange dans le message (lien relatif, bouton)
			$message = str_replace("../..", "$CFG->wwwroot", $message);
			$message = preg_replace('/(.*)input(.*)type="button"?(.*)/i', "", $message);
			$message = preg_replace('/(.*)input(.*)type="submit"?(.*)/i', "", $message);
			$message = preg_replace('/(.*)i_quitter(.*)Fermer"?(.*)/i', "", $message);
			$message = preg_replace('/(.*)fermer(.*)Fermer"?(.*)/i', "", $message);
            // rev 871
            if ($parcHTML)
                $message .= $parcHTML;
			require_once ($CFG->chemin_commun . "/lib_mail.php");
			if (send_mail($USER->id_user,traduction ("resultat_positionnement"),$message))
                $tpl->assign("_ROOT.msg_mail_envoye", traduction("msg_mail_envoye"). " ".$destinataire);
            else
                $tpl->assign("_ROOT.msg_mail_envoye", traduction ("err_msg_mail_envoye"). " ".$destinataire);
		}  else $tpl->assign("_ROOT.msg_mail_envoye", "");
	}

	if ($mode==QCM_TEST) {
		$tpl->gotoBlock("_ROOT");
		$url_retour=$CFG->chemin."/codes/examens/fiche.php?idq=".$idq."&ide=".$ide."#apercus";
    //    $tpl->newBlock("fin_fiche_test");
        $tpl->print_boutons_fermeture($url_retour);

	} else {
	  if (!is_utilisateur_anonyme()) {
		$tpl->newBlock("fin_fiche_pos");
		$tpl->assign("conf_fermeture_fiche_pos", traduction("conf_fermeture_fiche_pos"));
	  }
	}
} else {
	$tpl->assign("corps",traduction("msg_fin_qcm_cert")); //a finir
}



$tpl->printToScreen();

if (is_utilisateur_anonyme()) {
         detruire_session();
}
?>
