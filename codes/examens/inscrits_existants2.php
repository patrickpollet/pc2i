<?php
/**
 * @author Patrick Pollet
 * @version $Id: inscrits_existants2.php 1266 2011-09-20 13:40:42Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
/**
 * essai via ajax
 * rev 1013 le "spinner cach�" perturbait IE !!!
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

require_login("P"); //PP


require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates


$idq=required_param("idq",PARAM_INT);   // -1 en cr�ation
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);


$vires=optional_param("addselect",array(),PARAM_RAW);
$ajouts=optional_param("removeselect",array(),PARAM_RAW); //le 1er
$nom_ch=optional_param("nom_ch","",PARAM_RAW);
$prenom_ch=optional_param("prenom_ch","",PARAM_RAW);
$mail_ch=optional_param("mail_ch","",PARAM_RAW);
$numetudiant_ch=optional_param("numetudiant_ch","",PARAM_RAW);

//important apr�s avoir lu $ide !!!
v_d_o_d("em");


$criteres="";
//rev 856 on limite aux candidats de mon etablissement (important surtout sur nationale)
$criteres="etablissement =".$USER->id_etab_perso;
if ($nom_ch) $criteres=concatAvecSeparateur($criteres, " nom like '$nom_ch%'"," and ");
if ($prenom_ch) $criteres=concatAvecSeparateur($criteres, " prenom like '$prenom_ch%'"," and ");
if ($mail_ch) $criteres=concatAvecSeparateur($criteres, " email like '$mail_ch%'"," and ");
if ($numetudiant_ch) $criteres=concatAvecSeparateur($criteres, " numetudiant like '$numetudiant_ch%'"," and ");



//tempo ...
$CFG->nombre_maxcandidats=3000;
//ajax
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
   if ($criteres) {
   	$dispos=get_candidats_non_inscrits($idq,$ide,$criteres );
   	   foreach($dispos as $qui) {
   	   	$nom_complet=_regle_nom_prenom($qui->nom,$qui->prenom)." ".$qui->numetudiant;
        $info=<<<EOF

        <option value=$qui->login>$nom_complet</option>
EOF;
        print $info;
    }

   }
   die();
}


//print_r($ajouts);
//print_r($vires);


foreach($vires as $vire) {
        desinscrit_candidat($idq,$ide,$vire);
}

$tags='inscription manuelle '.$ide.$idq.' '.time();
foreach($ajouts as $ajout) {
        inscrit_candidat($idq,$ide,$ajout,$tags);
 }

$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$forme=<<<EOL

<form action="inscrits_existants2.php" method="post" id="monform" name="monform"
     onsubmit="selectAll($('removeselect')); selectAll($('addselect'));">


<input name="idq" type="hidden" value="{idq}"/>
<input name="ide" type="hidden" value="{ide}"/>
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->

<fieldset>
<legend>{inscription_manuelle} </legend>
<table width="99%">
<tr><td colspan="3"><span class="commentaire1">{info_inscriptions_manuelle} </span></td></tr>
<tr>
<td>
 <span id="num1">{num1} </span>  {info_nb_inscrits}
    <select name="removeselect[]" size="18" id="removeselect" multiple="multiple"  style="width:220px;"
                  onfocus="document.monform.add.disabled=true;
                           document.monform.remove.disabled=false;
                           document.monform.addselect.selectedIndex=-1;
                           majNombre('removeselect','num1');" >

      {options_del}
    </select>
</td>

<td width="40%">
<div class="centre">
<input name="add" id="add" type="button" class="saisie_bouton"
 value="&nbsp;&nbsp;&nbsp;&#x25C4;&nbsp;{inscrire}" title="{inscrire}"
 onclick="moveOptionsAcross($('addselect'), $('removeselect'))" />
<br/><br/>


<input name="remove" id="remove" type="button" class="saisie_bouton"
 value="&nbsp;&nbsp;&nbsp;{desinscrire}&nbsp;&#x25BA;&nbsp;" title="{desinscrire}"
 onclick="moveOptionsAcross($('removeselect'), $('addselect'))" />

<br/><br/>
<input name="addall" id="addall" type="button" class="saisie_bouton"
 value="&nbsp;&nbsp;&nbsp;&#x25C4;&nbsp;&#x25C4;&nbsp;{inscrire_tous}" title="{inscrire_tous}"
 onclick="addAll($('addselect'), $('removeselect'))"
  />
<br/><br/>

{bouton:enregistrer}
</div>


<div class="mini" id="form_ajax">
<fieldset>
<legend  {bulle:astuce:info_criteres_recherche}>{criteres}</legend>
<div class="commentaire1">{vos_criteres}
<span id="spinner" style="display: none;"><img src="{chemin_images}/spinner.gif" alt="Requ�te en cours "/></span>

</div>
<input name="idq" type="hidden" value="{idq}"/>
<input name="ide" type="hidden" value="{ide}"/>

<p class="double">
<label for="nom_ch"> {form_nom}</label>
<input name="nom_ch" id="nom_ch" type="text" value="{nom_ch}" size="18" autocomplete="OFF" onkeyup='cherche(this);'  />
</p>

<p class="double">
<label for="prenom_ch"> {form_prenom}</label>
<input name="prenom_ch" id="prenom_ch" type="text" value="{prenom_ch}" size="18" autocomplete="OFF" onkeyup='cherche(this);' />
</p>

<p class="double">
<label for="numetudiant_ch"> {form_numetud}</label>
<input name="numetudiant_ch" id="numetudiant_ch" type="text" value="{numetudiant_ch}" size="18" autocomplete="OFF" onkeyup='cherche(this);' />
</p>

<p class="double">
<label for="mail_ch"> {form_mail}</label>
<input name="mail_ch" id="mail_ch" type="text" value="{mail_ch}" size="18" autocomplete="OFF" onkeyup='cherche(this);' />
</p>
<!--
<div class="commentaire1">{info_criteres_recherche} </div>
-->

</fieldset>

</div>

</td>
<td>
 <span id="num2">{num2} </span>  {info_nb_candidats}
  <select name="addselect[]" size="18" id="addselect" multiple="multiple"   style="width:220px;"
                  onfocus="document.monform.add.disabled=false;
                           document.monform.remove.disabled=true;
                           document.monform.addselect.selectedIndex=-1;
                           majNombre('addselect','num2');"   >
    {options_add}
    </select>
</td>
</tr>
</table>
</fieldset>
</form>

<script type="text/javascript">
//<![CDATA[
function majCentral(theSelect,theScript,theSpinner,theInput) {

        Element.show(theSpinner);
        var ar=new Ajax.Updater(theSelect,theScript,{parameters : Form.serialize($("form_ajax")) ,evalScripts:true,
                                          onComplete : function () {
                                                  Element.hide(theSpinner);
                                                   $(theSelect).focus();
                                                   theInput.focus();},

                                          onFailure: function(transport){
                      alert("une erreur s'est produite, le serveur est peut-etre temporairement inaccessible");}
                                           });

}


function cherche (input) {
    var go=false;
    var options = $("form_ajax").getElementsByTagName('input');
  for (var i = 0; i < options.length; i++) {
    if (options[i].type=='text' && options[i].value.length>=2) go=true;
   // alert (options[i].name+" "+options[i].value+" "+options[i].type);
  }

  if (go)
     majCentral("addselect","inscrits_existants2.php","spinner",input);
}
//]]>
</script>


EOL;



$tpl->assignInclude("corps",$forme,T_BYVAR);
$tpl->prepare($chemin);



$CFG->utiliser_prototype_js=1;  //forc�

add_javascript($tpl,$CFG->chemin_commun."/js/listes.js");

$ligne=get_examen($idq,$ide);
$tpl->assign("_ROOT.titre_popup",traduction("inscription_manuelle")."<br/>". nom_complet_examen($ligne));




$tpl->assign("ide", $ide);
$tpl->assign("idq", $idq);
$tpl->assign("retour_fiche", $retour_fiche);

$tpl->assign("nom_ch",$nom_ch);
$tpl->assign("prenom_ch",$prenom_ch);
$tpl->assign("mail_ch",$mail_ch);
$tpl->assign("numetudiant_ch",$numetudiant_ch);



$inscrits=get_inscrits($idq,$ide);
$tous=get_candidats_non_inscrits($idq,$ide);
//$tous=array();
$nbtotal=count($tous);

if ($nbtotal && $criteres) {
	$dispos=get_candidats_non_inscrits($idq,$ide,$criteres );
} else $dispos=$tous;

//pb avec l'anonyme. Ses r�sultats ne sont pas stock�s ...
//maj info affich�es dans le select


foreach ($inscrits as $inscrit) {
    $inscrit->nom_complet=_regle_nom_prenom($inscrit->nom,$inscrit->prenom)." ".$inscrit->numetudiant;
}

foreach ($dispos as $inscrit) {
    $inscrit->nom_complet=_regle_nom_prenom($inscrit->nom,$inscrit->prenom)." ".$inscrit->numetudiant;
}

//$sur = $nbtotal != count($dispos) ? "/$nbtotal "  :" ";
$tpl->assign ("num1" ,count($inscrits));
$tpl->assign ("num2", count($dispos));

print_options_from_table ($tpl,"options_del",       //template et nom balise
                        $inscrits,                  //table des options

                        "login","nom_complet",       // valeur et texte des options
                        false,      // texte option neutre
                        false);                     // valeur a selectionner

print_options_from_table ($tpl,"options_add",       //template et nom balise
                        $dispos,                  //table des options

                        "login","nom_complet",       // valeur et texte des options
                        false,      // texte option neutre
                        false);                     // valeur a selectionner


$tpl->gotoBlock("_ROOT");


/*
$items=array();

$items[]=get_menu_item_legende("inscriptions");

print_menu($tpl,"_ROOT.menu_niveau2",$items);
*/

$tpl->gotoBlock("_ROOT");

$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?refresh=1&ide=" . $ide . "&idq=" . $idq."#inscriptions": "");
$tpl->print_boutons_fermeture($url_retour);

$tpl->printToScreen();										//affichage
?>

