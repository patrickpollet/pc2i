<?php
/**
 * @author Patrick Pollet
 * @version $Id: convocations_mail.php 1219 2011-03-15 10:45:33Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once($chemin_commun."/lib_mail.php");
require_login("P"); //PP



$idq=required_param("idq",PARAM_INT);   // -1 en cr�ation
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);

$liste=optional_param("removeselect","",PARAM_RAW);  //liste des comptes retenus comme le nom ne l'indique pas ...

$sujet=optional_param("sujet","",PARAM_RAW);
$message=optional_param("message","",PARAM_RAW);
$copy_self=optional_param("copy_self",0,PARAM_INT);

// 2 boutons submit sur la forme
$bouton_envoyer=optional_param("bouton_envoyer","",PARAM_ALPHA);
//$bouton_apercu=optional_param("bouton_apercu","",PARAM_ALPHA);

v_d_o_d("em");

$ligne=get_examen($idq,$ide);
$mapomme=get_compte($USER->id_user);

//ajax
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
    // un petit peu d'ajax
    //require_once($CFG->chemin_commun."/lib_ajax.php");   //bonnes entetes avec encodage iso ...
    $mapomme->password="******";
    list($sujet,$message)=substitue($sujet,$message,$ligne,$mapomme);
    print (affiche_texte($sujet)."<br/><br/>".affiche_texte($message));

    die();
}

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates
$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$forme=<<<EOL
{resultats_op}
<form class="normale" action="convocations_mail.php" method="post" name="monform" id="monform"
 onsubmit="selectAll($('removeselect'));">
<fieldset>
<legend>{convocations_mail} </legend>
<table width="99%">
<tr><td colspan="3" class="commentaire1">{info_convocations_mail}</td></tr>

<tr>
<td>

<input name="idq" type="hidden" value="{idq}"/>
<input name="ide" type="hidden" value="{ide}"/>
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

  <span id="num1">{nb_convoques}</span> {info_nb_convoques}
    <select name="removeselect[]" size="18" id="removeselect" multiple="multiple"  style="width:220px;"
                  onfocus="document.monform.add.disabled=true;
                           document.monform.remove.disabled=false;
                            document.monform.addall.disabled=true;
                           document.monform.removeall.disabled=false;
      document.monform.addselect.selectedIndex=-1;
       majNombre('removeselect','num1');">

      {options_del}
    </select>

</td>
<td width="60%">
<div class="centre">
<br/>
<input name="add" id="add" type="button" class="saisie_bouton"
   value="&nbsp;&nbsp;&nbsp;&#x25C4;&nbsp;{bouton_ajouter}" title="{alt_ajouter}"
   onclick="moveOptionsAcross($('addselect'), $('removeselect'))" />
<br/><br/>


<input name="remove" id="remove" type="button" class="saisie_bouton"
     value="&nbsp;&nbsp;&nbsp;{bouton_enlever}&nbsp;&#x25BA;&nbsp;" title="{alt_enlever}"
     onclick="moveOptionsAcross($('removeselect'), $('addselect'))"/>

<br/><br/>
<input name="addall" id="addall" type="button" class="saisie_bouton"
       value="&nbsp;&nbsp;&nbsp;&#x25C4;&nbsp;&#x25C4;&nbsp;{bouton_ajouter_tout}" title="{alt_ajouter_tout}"
       onclick="addAll($('addselect'), $('removeselect'))"
       />
<br/><br/>


<input name="removeall" id="removeall" type="button" class="saisie_bouton"
             value="&nbsp;&nbsp;&nbsp;{bouton_enlever_tout}&nbsp;&#x25BA;&nbsp;&#x25BA;&nbsp;" title="{bouton_enlever_tout}"
             onclick="addAll($('removeselect'), $('addselect'))" />
<br/><br/>

<hr/>
</div>
</td>
<td>

<span id="num2">0 </span>  {info_nb_non_convoques}
  <select name="addselect[]" size="18" id="addselect" multiple="multiple"  style="width:220px;"
                  onfocus="document.monform.add.disabled=false;
                           document.monform.remove.disabled=true;
                           document.monform.addall.disabled=false;
                           document.monform.removeall.disabled=true;
                           document.monform.addselect.selectedIndex=-1;
                            majNombre('addselect','num2');" >

    </select>

</td>

</tr>
<tr>
<td colspan="2">
<fieldset>
<legend>{message_a_envoyer}</legend>

<p class="double">
<label for="sujet">{form_sujet_message}</label>
<input name="sujet" id="sujet" type="text" size="45" value="{sujet_standard_convocation}" class="required"  title="{js_sujet_manquant}" />
</p>
<p class="double">
<label for="message">{form_texte_message}</label>
<textarea name="message" id="message" cols="60" rows="8" class="required"  title="{js_corps_manquant}">{message_standard_convocation}</textarea>
</p>
<p class="centre">
<input type="checkbox" class="saisie" name="copy_self" value="1" checked="checked"  />  {form_copy_self}
<br/>
{bouton_apercu} &nbsp;{bouton_envoyer}
</p>
</fieldset>
</td>
<td>
<div id="apercu"></div>

</td>
</tr>

</table>


</fieldset>
</form>


EOL;


$CFG->utiliser_prototype_js=1;  //forc�
$CFG->utiliser_validation_js=1;

add_javascript($tpl,$CFG->chemin_commun."/js/listes.js");

$tpl->assignInclude("corps",$forme,T_BYVAR);
$tpl->prepare($chemin);


//print_r($liste);
if ($bouton_envoyer && $liste && count($liste>0))  { // enregistrer
    $resultats=convoque_mail($idq,$ide,$liste,$sujet,$message);
    if ($copy_self) {
        list($sujet,$message)=substitue(traduction ("rapport_de_convocation_par_mail"),
                                        traduction("texte_rapport"),$ligne,$mapomme);
        if (@count($resultats[0]))
            $message=str_replace('[oks]',table_en_texte($resultats[0]),$message);
        else $message=str_replace('[oks]','',$message);

        if (@count($resultats[1]))
            $message=str_replace('[kos]',table_en_texte($resultats[1]),$message);
        else $message=str_replace('[kos]','',$message);

        if (send_mail($mapomme->login,$sujet,'',$message))
            set_ok (traduction ("mail_copie_envoye",true,$mapomme->email),$resultats);
        else
            set_erreur (traduction("err_mail_copie_envoye",true,$mapomme->email),$resultats);
        //suggestion P.Gillois copie aussi � l'auteur  de l'examen  rev 978
        if (!empty($CFG->convoque_mail_copie_auteur)) {
            if ($ligne->auteur_mail != get_mail($mapomme->login)) {
                if ($auteur=get_compte_byemail($ligne->auteur_mail))
                    if (send_mail($auteur->login,$sujet,'',$message))
                        set_ok (traduction ("mail_copie_envoye",true,$auteur->email),$resultats);
                    else
                        set_erreur (traduction("err_mail_copie_envoye",true,$auteur->email),$resultats);
            }
        }
    }

   if (count($resultats))
        $tpl->assign("resultats_op",print_details($resultats));
    else $tpl->assign("resultats_op","");
} else $tpl->assign("resultats_op","");



$tpl->assign("_ROOT.titre_popup",traduction("convocations_mail")."<br/>". nom_complet_examen($ligne));
$tpl->assign("ide", $ide);
$tpl->assign("idq", $idq);
$tpl->assign("retour_fiche", $retour_fiche);

$tpl->gotoBlock("_ROOT");

$inscrits=get_inscrits($idq,$ide);
$mailables=array();

foreach ($inscrits as $inscrit) {
	if (!empty($inscrit->email) && ! is_utilisateur_anonyme($inscrit->login)) {
		$inscrit->nom_complet=_regle_nom_prenom($inscrit->nom,$inscrit->prenom)." ".$inscrit->numetudiant." ".$inscrit->email;
		$mailables[]=$inscrit;
	}
}

$tpl->assign ("nb_convoques",count($mailables));

print_options_from_table ($tpl,"options_del",       //template et nom balise
                        $mailables,                  //table des options
                        "login","nom_complet",       // valeur et texte des options
                        false,      // texte option neutre
                        false);                     // valeur a selectionner




print_bouton($tpl,"bouton_envoyer","envoyer","","","submit" );
print_bouton($tpl,"bouton_apercu","apercu","javascript:majDiv(\"apercu\",\"convocations_mail.php\",false,\"monform\");","","button" );
//print_bouton($tpl,"bouton_tester","tester","","","submit" );
//function print_bouton ($tpl,$balise,$action,$onClick="",$class="",$type="")

$tpl->gotoBlock("_ROOT");

$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?ide=" . $ide . "&idq=" . $idq."#inscriptions": "");
$tpl->print_boutons_fermeture($url_retour);



$tpl->printToScreen();										//affichage
?>

