<?php
/**
 * @author Patrick Pollet
 * @version $Id: inscrits_ldap.php 1053 2010-03-10 13:55:17Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once($chemin_commun."/lib_ldap.php");

require_login("P"); //PP



$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
$champs=get_champs_recherche_ldap($ide);
//important apr�s avoir lu $ide !!!
v_d_o_d("em");

//ajax
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
    $quoi=array();
    foreach($champs as $champ) {
        $val=optional_param ($champ->champ_LDAP,"",PARAM_RAW);
        if ($val && strlen ($val)>=2)
            $quoi[$champ->champ_LDAP]=$val;
    }
   if (count($quoi)>0) {
        $ret=@recherche_ldap($quoi,$CFG->nbre_reponses_ldap,$ide);

   } else $ret=array();

    foreach($ret as $qui) {
        $info=<<<EOF
        <option value=$qui->login>$qui->nom $qui->prenom $qui->numetudiant $qui->email</option>
EOF;
        print $info;
    }
   die();
}

$idq=required_param("idq",PARAM_INT);   // -1 en cr�ation

$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);

$test=optional_param("test","",PARAM_ALPHA);
$liste=optional_param("removeselect","",PARAM_RAW);  //liste des comptes retenus comme le nom ne l'indique pas ...


require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates
$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$forme=<<<EOL
{resultats_op}


<form action="inscrits_ldap.php" method="post" name="monform" id="monform" onsubmit="selectAll($('removeselect'));">
<fieldset>
<legend>{recherches_ldap} </legend>
<table width="99%">
<tr><td colspan="3" class="commentaire1">{info_inscriptions_ldap} <br/></td></tr>

<tr>
<td>


<input name="idq" type="hidden" value="{idq}"/>
<input name="ide" type="hidden" value="{ide}"/>
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->

  <span id="num1">0 </span> {info_nb_trouves}
    <select name="removeselect[]" size="22" id="removeselect" multiple="multiple"  style="width:220px;"
                  onfocus="document.monform.add.disabled=true;
                           document.monform.remove.disabled=false;
                           document.monform.addall.disabled=true;
                           majNombre('removeselect','num1');">

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
<legend>{criteres_ldap}</legend>
<div class="commentaire1">{vos_criteres}
<span id="spinner" style="display: none;"><img src="{chemin_images}/spinner.gif" alt="Requ�te en cours "/></span>

</div>
<input name="idq" type="hidden" value="{idq}"/>
<input name="ide" type="hidden" value="{ide}"/>

<!-- START BLOCK : critere -->
<p class="double">
<label for="{champ}">{nom_champ}</label>
<input name="{champ}" id="{champ}" type="text" value="" autocomplete="OFF" onkeyup='cherche(this);'/>
</p>
<!-- END BLOCK : critere -->


</fieldset>
</div>

</td>
<td>

<span id="num2">0 </span>  {info_nb_candidats}
  <select name="addselect[]" size="22" id="addselect" multiple="multiple"  style="width:220px;"
                  onfocus="document.monform.add.disabled=false;
                           document.monform.remove.disabled=true;
                           document.monform.addall.disabled=false;
                           majNombre('addselect','num2');">

    </select>
</td>

</tr>
</table>
</fieldset>
</form>

<script type="text/javascript" >

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
  for (var i = 0; i < options.length; i++)
      if (options[i].type=='text' && options[i].value.length>=2) go=true;
   if (go)
     majCentral("addselect","inscrits_ldap.php","spinner",input);
}
//]]>
</script>


EOL;


$CFG->utiliser_prototype_js=1;  //forc�

add_javascript($tpl,$CFG->chemin_commun."/js/listes.js");

$tpl->assignInclude("corps",$forme,T_BYVAR);
$tpl->prepare($chemin);

$ligne=get_examen($idq,$ide);

if ($liste) { // enregistrer
    //print_r($liste);
     $liste=implode("\r\n",$liste); //fait comme avec le textarea de inscrits_groupe_ldap
     //print_r($liste);
     $resultats=inscription_massive_ldap($idq,$ide,$liste,false);
    if (count($resultats))
        $tpl->assign("resultats_op",print_details($resultats));
    else $tpl->assign("resultats_op","");
} else $tpl->assign("resultats_op","");



$tpl->assign("_ROOT.titre_popup",traduction("recherches_ldap")."<br/>". nom_complet_examen($ligne));
$tpl->assign("ide", $ide);
$tpl->assign("idq", $idq);
$tpl->assign("retour_fiche", $retour_fiche);



$tpl->gotoBlock("_ROOT");


//criteres de recherche
$cnt=0;
foreach ($champs as $champ ) {
		$tpl->newBlock("critere");
		$tpl->assign("champ",$champ->champ_LDAP);
		$tpl->assign("nom_champ",$champ->nom_champ);
        //$tpl->setConditionalvalue($cnt>0,"et",traduction("et",false),"");
        $cnt++;
}



$tpl->gotoBlock("_ROOT");
$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?refresh=1&ide=" . $ide . "&idq=" . $idq."#inscriptions": "");
$tpl->print_boutons_fermeture($url_retour);


$tpl->printToScreen();										//affichage
?>

