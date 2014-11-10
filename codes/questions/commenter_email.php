<?php

/**
 * @author Patrick Pollet
 * @version $Id: valider.php 1266 2011-09-20 13:40:42Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
ce script passe la validation W3C avec les commentaires <!-- --> dans le javascript en ligne
 on ne PEUT pas mettre un CDATA sinon ne fait pas les assign aux parametres ref, alin et session_id
**/

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

//rev 981 simplification avec le parametre id complet
if ($id=optional_param('id','',PARAM_CLE_C2I)) {
    $ligne=get_question_byidnat ($id);
    $idq=$ligne->id;
    $ide=$ligne->id_etab;
} else {
    $idq = required_param("idq", PARAM_INT);
    $ide = required_param("ide", PARAM_INT);
    $ligne=get_question($idq,$ide);

}

$idnat=$ide.'.'.$idq;

$doit=optional_param("doit",0,PARAM_INT);  //action ?

$url_retour=optional_param("url_retour","",PARAM_CLEAN);

require_login("P"); //PP



//$CFG->adresse_feedback_questions='pp@patrickpollet.net';
/*** toutes les questions sont commentables pas seulement les nationales
if ($ide !=1) erreur_fatale ('err_question_non_nationale');
else 
****/
if (empty($CFG->adresse_feedback_questions))
    erreur_fatale('err_adresse_experts_non_definie');

//$CFG->adresse_feedback_questions="patrick.pollet@insa-lyon.fr";


$ligne=get_question($idq,$ide);

// autocreation dossier des documents au cas ou on ajouterais une image ... 
get_document_location($idq,$ide,true); 

$user=get_utilisateur($USER->id_user);
$from='<'._regle_nom_prenom($user->nom,$user->prenom).'>'.$user->email;

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
if ($doit) { // action
/*******************    
    print_r($_POST);
    die();
    Array (
     [id] => 1.2606 
     [doit] => 1 
     [url_retour] => filtre_valid=toutes&indice_deb=0&indice_ecart=99&tri=-8 
     [c2iv15] => 5690aa806dd41f5bce343595fb7c5d37 
     [de] => patrick.pollet@insa-lyon.fr 
     [copy_self] => 1 
     [envoi_anonyme] => 1
     [sujet] => Commentaire sur la question 1.2606 
     [message] => aaaaaaaaaaa 
     [bouton_envoyer] => Envoyer )    
*******************/
    require_once($chemin_commun."/lib_mail.php");
    $copy_self=optional_param('copy_self',0,PARAM_BOOL);
    $envoi_anonyme=optional_param('envoi_anonyme',0,PARAM_BOOL);
    $sujet=required_param('sujet',PARAM_CLEAN);
    $message=required_param('message',PARAM_CLEANHTML);
    
    //compte bidon
    $to=new StdClass();
    $to->login=$to->email=$CFG->adresse_feedback_questions;
    
    
    $ligne_e = new StdClass();
    $ligne_e->id_examen = $ligne_e->id_etab = $ligne->type_tirage = ""; // bidon pour imprime_question
    list($qtext,$tmp)=imprime_question(1,$ligne_e, $ligne, false,false, false, 3,QCM_CORRECTION,null);
    
    //remplace chemin relatif par absolu dans les images 
    $qtext=str_replace('../../themes/',$CFG->wwwroot .'/themes/',$qtext);
    
    // ainsi que pour les images inser�es par l'�diteur HTML 
    $qtext=str_replace('../../commun/send_document.php',$CFG->wwwroot .'/commun/send_document.php',$qtext);
    // aussi dans le corps du message 
    $message=str_replace('../../commun/send_document.php',$CFG->wwwroot .'/commun/send_document.php',$message);
    
    
    if ($envoi_anonyme) 
        $entete=traduction ('salutation_commentaire_mail_anonyme',true,$idnat); 
    else 
        $entete=traduction ('salutation_commentaire_mail',true,$from,nom_univ($USER->id_etab_perso),$idnat);
    $rappel='<hr/>'.traduction ('pour_memoire').'<hr/><table>'.$qtext.'</table>';
    
    $message=$entete.$message.$rappel;
    $ret=send_mail($to,$sujet,$message,'');
    if ($ret && $copy_self)
        $ret= send_mail($USER->id_user,$sujet,$message,'');
//   print($message);
//    die();
    if ($ret)
        ferme_popup("liste.php?".urldecode($url_retour),true);
    else  erreur_fatale('err_mail_copie_envoye');
}



$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
require_once ($CFG->chemin_commun.'/lib_editeur.php');
$forme=<<<EOF
<form class="normale" action="commenter_email.php" method="post" name="monform" id="monform">
<fieldset>
<legend>{message_a_envoyer} </legend>
<div class="commentaire1">{info_commenter_email}</div>
<input name="id" type="hidden" value="{id}"/>
<input name="doit" type="hidden" value="1"/>
<input type="hidden" name="url_retour" value="{url_retour}" />

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
<p class="double">
<label for="de">{form_de}</label>
<input name="de" id="de" type="text" size="60" value="{de}" readonly="1"/>
</p>
<p class="centre"> 
<input type="checkbox" class="saisie" name="envoi_anonyme" value="1" />  {form_envoi_anonyme}
<input type="checkbox" class="saisie" name="copy_self" value="1" checked="checked"  />  {form_copie_commentaire_self}
</p>

<p class="double">
<label for="sujet">{form_sujet_message}</label>
<input name="sujet" id="sujet" type="text" size="60" value="{sujet_standard_commentaire_question}" class="required"  title="{js_sujet_manquant}" />
</p>

<label for="message">{form_texte_message}</label>
{corps_message}

<p class="centre">{bouton_envoyer}</p>


</fieldset>
</form>
{active_html_editor}


EOF;


$CFG->utiliser_validation_js=1;

$CFG->utiliser_editeur_html=1;


$tpl->assignInclude("corps", $forme ,T_BYVAR);
$tpl->prepare($chemin);




$link= "<ul style=\"display:inline;\">".print_menu_item(false, false,get_menu_item_consulter("fiche.php?idq=$idq&amp;ide=$ide" ))."</ul>";

$tpl->assign("_ROOT.titre_popup", traduction( "commenter_question")." ".$idnat."<br/>".
                clean(affiche_texte_question($ligne->titre),75)." ".$link);

$tpl->assign("id", $idnat);
$tpl->assign("url_retour", $url_retour);

$tpl->assign("de",$from);
$tpl->assign('sujet_standard_commentaire_question',traduction('sujet_standard_commentaire_question',true,$idnat));

$tpl->assign('info_commenter_email',traduction('info_commenter_email',true,$CFG->adresse_feedback_questions));

$tpl->assign('corps_message',print_textarea($CFG->utiliser_editeur_html,12,60,0,0,"message",'','saisie required',
traduction('js_libelle_manquant') ,true,$idq,$ide));


$tpl->gotoBlock("_ROOT");

print_bouton($tpl,"bouton_envoyer","envoyer","","","submit" );

$tpl->print_boutons_fermeture();

if ($CFG->utiliser_editeur_html) {
    $tpl->assign ("_ROOT.active_html_editor",use_html_editor('message','', '',true) );
} else $tpl->assign ("_ROOT.active_html_editor","");


$tpl->printToScreen(); //affichage
?>