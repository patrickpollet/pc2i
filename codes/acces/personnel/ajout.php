<?php

/**
 * @author Patrick Pollet
 * @version $Id: ajout.php 1260 2011-07-20 14:35:30Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Ajout et modification d'item
//	et duplication (ajout par recopie)
//
////////////////////////////////


$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres
require_login('P'); //PP


$ide=required_param("ide",PARAM_INT);  //etablissement
$id=optional_param("id","-1",PARAM_CLEAN); // nouveau ou modif
$url_retour=optional_param("url_retour","liste.php",PARAM_CLEAN);

v_d_o_d("ua");


//ajax rev 936  consultation annuaire
// rev 962 la fonction json_encode de PHP >=5.2  ne fonctionne pas avec des caractères latins
// donc j'utilise toujpours le mien !
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
    $qui=optional_param("login","inconnu",PARAM_RAW);
      require_once($CFG->chemin_commun."/lib_ldap.php");
       if (auth_ldap_init($ide)) {
            if( $cpt= auth_get_userinfo_asobj($qui)) {
                $cpt->erreur="";
                print (mon_json_encode($cpt));
            } else {
                $cpt=new StdClass();
                $cpt->erreur=traduction ("err_login_pas_ldap",false,$qui);
                //$cpt->nom=$cpt->prenom=$cpt->numetudiant=$cpt->email="";
                print (mon_json_encode($cpt));
            }
       }
   die();
}


$fiche=<<<EOF

 <script type="text/javascript">
//<![CDATA[

function verif_form1(){
    document.monform.hidden_profils.value="";
    // gestion de l'envoi des listes déroulantes dans des champs récupérables
    for (i=0;i<document.monform.fonctions_s.options.length;i++) {
        if (i>0) document.monform.hidden_profils.value += "*";
        document.monform.hidden_profils.value += document.monform.fonctions_s.options[i].value;
    }
}

function inverse(l1,l2) {
    if (l1.options.selectedIndex>=0) {
        o=new Option(l1.options[l1.options.selectedIndex].text,l1.options[l1.options.selectedIndex].value);
        l2.options[l2.options.length]=o;
        l1.options[l1.options.selectedIndex]=null;
    }else{
        alert("Aucun item sélectionné");
    }
}

function ajouter(liste,champ) {
    if (champ.value!=""){
        var o=new Option(champ.value,champ.value);
        liste.options[liste.options.length]=o;
    }
}

function maj_mdp() {
    if (document.monform.auth.value=='ldap') {
        document.monform.password.value='{fourni par ldap}';
        document.monform.ldap.className="saisie_bouton visible";
    }
     else {
        document.monform.password.value='';
        document.monform.ldap.className="saisie_bouton cache";
     }
     document.monform.cmdp.value=document.monform.password.value;
}


//]]>

</script>

<div id="corps2">
	<div id="xx">
		<ul id="tabs">
			<li> <a class="active_tab" 	href="#fiche">{fiche} </a></li>
			<li> <a class="" 			href="#profils">{legende_profils}</a> </li>

		</ul>
    </div>
<div class="panel" id="fiche">
<form name="monform" id="monform" action="action.php" method="post" onsubmit="return(verif_form1())">
<table class="fiche">
        <tbody>
          <tr>
            <th>{form_login}</th>
            <td><input {readonly} type="text" name="login" size="40" class="required"
                    title="{js_login_manquant}" value="{login}" />
                 <input type="hidden" id="numetudiant" name="numetudiant" value="{numetudiant}" />
            </td>
          </tr>
          <tr>
            <th>{form_nom}</th>
            <td><input type="text" name="nom" id="nom"  size="40" class="required"
            title="{js_nom_manquant}" value="{nom}" /></td>
          </tr>
          <tr>
            <th>{form_prenom}</th>
            <td><input type="text" name="prenom" id="prenom" size="40" class="required"
            title="{js_prenom_manquant}" value="{prenom}" /></td>
          </tr>
          <tr>
            <th>{form_mail}</th>
            <td><input type="text" name="email" id="email"  size="40" class="required validate-email"
             title="{js_mail_incorrect}" value="{email}" /></td>
          </tr>
          <tr>
            <th>{form_admin}</th>
            <!-- START BLOCK : change_admin -->
            <td><input type="checkbox" class="saisie" value="O" name="a_e" {ch_a_e}/></td>
            <!-- END BLOCK : change_admin -->
            <!-- START BLOCK : no_change_admin -->

            <td><img src="{chemin_images}/case{ch_a_e}.gif" alt=""/></td>
            <!-- END BLOCK : no_change_admin -->

          </tr>
          <tr>
            <th>{form_mdp}</th>
            <td><input type="password" id="password" name="password" size="15"  class="required validate-password"
            title="{js_mdp_vide}" value="{password}" />
            </td>
          </tr>
          <tr>
            <th>{form_cmdp}</th>
            <td>
               <input type="password" name="cmdp" size="15" class="required validate-password-confirm"
            title="{js_mdp_conf_vide}" value="{password}" />
               <input type="hidden" name="old_mdp" value="{password}" />
            </td>
          </tr>
          <tr>
            <th>{form_auth}</th>
            <td>
             {select_auth}
             <!-- START BLOCK : ldap -->
                 <input id="ldap" name="ldap" type="button" class="saisie_bouton {visible}"
                   onclick="{action}" value="{consulter_annuaire}" />

             <!-- END BLOCK : ldap -->




            </td>
          </tr>
          <tr>
            <th>{form_pos}</th>
            <!-- START BLOCK : change_limit_pos -->
            <td><input type="checkbox" class="saisie" value="1" name="a_p" {ch_a_p} /></td>
            <!-- END BLOCK : change_limit_pos -->
            <!-- START BLOCK : no_change_limit_pos -->
            <td><img src="{chemin_images}/case{ch_a_p}.gif" alt=""/></td>
            <!-- END BLOCK : no_change_limit_pos -->
          </tr>
          <tr>
            <th>{profils}</th>
            <td><table  class="centre" >
              <tr>
                <td class="centre" ><span class="form_libelle">{disponibles}</span><br/>
                    <select name="fonctions" class="saisie" size="5">
                      <!-- START BLOCK : fonction -->
                      <option value="{num_fonc}">{fonc}</option>
                      <!-- END BLOCK : fonction -->
                  </select></td>
                <td class="centre" ><input name="button" type="button" class="saisie_bouton"
                onclick="inverse(this.form.fonctions,this.form.fonctions_s);" value="   ajouter &gt;&gt;   "/>
                    <br/>
                    <br/>
                    <input name="button" type="button" class="saisie_bouton"
                    onclick="inverse(this.form.fonctions_s,this.form.fonctions);" value="&lt;&lt; supprimer" /></td>
                <td class="centre" ><span class="form_libelle">{attribues}</span><br/>
                    <select name="fonctions_s" class="saisie" size="5" multiple="multiple">
                      <!-- START BLOCK : fonction_s -->
                      <option value="{num_fonc}">{fonc}</option>
                      <!-- END BLOCK : fonction_s -->
                  </select></td>
              </tr>
            </table>
            <input name="hidden_profils" type="hidden" id="hidden_profils"/>
            </td>
          </tr>
<!-- START BLOCK : mail_type -->
          <tr>
            <th>{envoyer_mail}</th>
            <td><input type="checkbox" name="env_mail" class="saisie" value="1" /></td>
          </tr>
          <tr>
            <th>{texte_mail}</th>
            <td><textarea name="texte_mail" cols="60" rows="5" class="saisie">{val_texte_mail}</textarea></td>
          </tr>
<!-- END BLOCK : mail_type -->

<!-- START BLOCK : tags -->
<tr>
<th>{form_tags}<br/>
<div class="commentaire1">{info_tags}</div></th>

 <td><textarea name="tags" cols="60" rows="5"
                   >{tags}</textarea></td>
</tr>
<!-- END BLOCK : tags -->
        </tbody>
      </table>

<div class="centre">

      {bouton:annuler} &nbsp; {bouton_reset} &nbsp;{bouton:enregistrer}

<input name="id" type="hidden" value="{id}" />
<input name="ide" type="hidden" value="{etablissement}" />
<input name="etablissement" type="hidden" value="{etablissement}" />

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
<input type="hidden" name="url_retour" value="{url_retour}" />
</div>

</form>
</div>


<div class="panel" id="profils">
<!-- START BLOCK : profil -->
<table class="fiche">
        <tbody>
          <tr>
            <th class="bg">{form_libelle}</th>
            <th class="bg">{titre}</th>
          </tr>
<!-- START BLOCK : detail -->
<!--INCLUDE BLOCK : table_profil -->
<!-- START BLOCK : detail -->

        </tbody>
      </table>
<!-- END BLOCK : profil -->
</div>

</div>

EOF;




require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //créer une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $fiche,T_BYVAR); // le template gérant le personnel
$tpl->assignInclude("table_profil",profil_en_table(),T_BYVAR); //table des droits d'un profil'
$tpl->prepare($chemin);

$CFG->utiliser_prototype_js=1;  //forcé
$CFG->utiliser_validation_js=1;
$CFG->utiliser_fabtabulous_js=1;


$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.id", $id);

$tpl->assign("_ROOT.url_retour", $url_retour);


//SB
// profils disponibles
$profils=get_profils("intitule");
//
if ($id !=-1) { // modification de la personne

        v_d_o_d("um");
        $tpl->assign("_ROOT.titre_popup", traduction("modifier_personnel")." ".$id);

    $ligne=get_utilisateur($id);


    $tpl->assign("readonly", " readonly=\"readonly\"");
        $tab = array (); // tableau contenant les profils à indiquer; permet de gérer les doublons avec la liste restante

    // profils à indiquer
    $profils_attr=get_profils_utilisateur ($id,$tri='intitule');
  foreach($profils_attr as $profil) {
        $tpl->newBlock("fonction_s");
        $tpl->assign("fonction_s.num_fonc", $profil->id_profil);
        $tpl->assign("fonction_s.fonc", ucfirst($profil->intitule));
        if (!in_array($profil->id_profil, $tab)) {
            $tab[] = $profil->id_profil;
        }
    }

    ////////////////////////////////////////////////////////////////////////
} else { // nouvelle personne
    $tpl->traduit("_ROOT.titre_popup","nouveau_personnel");
    $ligne=new StdClass();
    $ligne->login=$ligne->nom=$ligne->prenom=$ligne->email=$ligne->password="";
    $ligne->auth="manuel";
    $ligne->est_admin_univ = "N";
    $ligne->limite_positionnement = "0";
    $tpl->assign("readonly", "");
    $ligne->etablissement=$ide;
    $ligne->numetudiant=""; // rev 936 (synchro ldap)
    $ligne->tags='';
    $tab=array(); //vide

    ////////////////////////////////////////////////////////////////////////
    $tpl->newBlock("mail_type");

}


$tpl->gotoBlock("_ROOT");
$tpl->assignObjet($ligne,true);


//maj des  limtes des mots de passe
$tpl->assign("longueur_pwd",$CFG->longueur_mini_password);
$tpl->assign("js_mdp_vide",traduction ("js_mdp_vide",false,$CFG->longueur_mini_password));
$tpl->assign("js_mdp_conf_vide",traduction ("js_mdp_conf_vide",false,$CFG->longueur_mini_password));

print_select_from_table($tpl,"select_auth",get_auth_methodes($ide,true),"auth","","onchange='maj_mdp();'","id","texte","",$ligne->auth);

if (auth_ldap_init($ide))  {// rev 936
    $tpl->newBlock("ldap");
    $tpl->assign("visible", $ligne->auth=="ldap"? "visible":"cache");
    $tpl->assign("action","javascript:majAjax('ajout.php',false,'monform');");

}

// profils disponibles
foreach($profils as $profil) {
	if (!in_array($profil->id_profil, $tab)) {
		$tpl->newBlock("fonction");
		$tpl->assign("fonction.num_fonc", $profil->id_profil);
		$tpl->assign("fonction.fonc", ucfirst($profil->intitule));
	}
}

// V 1.5 seul un admin peut changer ca ...
if (is_admin()) {
    $tpl->newBlock("change_admin");
    $tpl->setChecked( $ligne->est_admin_univ == "O","ch_a_e");
    $tpl->newBlock("change_limit_pos");
    $tpl->setChecked( $ligne->limite_positionnement =='1',"ch_a_p");
} else {
    $tpl->newBlock("no_change_admin");

    $tpl->setConditionalValue($ligne->est_admin_univ == "O","ch_a_e", "1","0");
    $tpl->newBlock("no_change_limit_pos");
    $tpl->setConditionalValue($ligne->limite_positionnement == 1,"ch_a_p", "1","0");
}

if ($CFG->activer_tags_utilisateur) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}

$tpl->gotoBlock("_ROOT");

if ($id=="-1")
    print_bouton_reset($tpl,"validator.reset();");
else
    $tpl->assign("bouton_reset","");


$tpl->gotoBlock("_ROOT");

//////////////////////////////////////////////////////////////////////////////////////////////////////

foreach($profils as $ligne ) {
    $tpl->newBlock("profil");
     $tpl->assign("titre", str_replace('"', "&quot;", $ligne->intitule));
    $tpl->newBlock("detail");
     garni_table_profil($tpl,$ligne);

}
//
$tpl->printToScreen(); //affichage
?>