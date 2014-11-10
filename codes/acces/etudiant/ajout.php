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
//   ce script n'est PAS utilis� poour cr�er un compte ????
////////////////////////////////


/*
* Pour la description des diff�rentes m�thodes de la classe TemplatePower,
* il faut se ref�rer � http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
$chemin = '../../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres

require_login('P'); //PP
$ide=required_param("ide",PARAM_INT);
v_d_o_d("eta");   // droits de(ajout �tudiants)


//ajax rev 936  consultation annuaire
// rev 962 la fonction json_encode de PHP >=5.2  ne fonctionne pas avec des caract�res latins
// donc j'utilise toujpours le mien !
if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
    $qui=optional_param("login","inconnu",PARAM_RAW);
      require_once($CFG->chemin_commun."/lib_ldap.php");
       if (auth_ldap_init($ide)) {
            if( $cpt= auth_get_userinfo_asobj($qui)) {
                $cpt->erreur="";  //print_r($cpt,true);
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

 // nouveau ou modif
$id=optional_param("id","-1",PARAM_CLEAN); // ajout ou modificationd'un login possible
//if (!$id) $id=optional_param("id","-1",PARAM_ALPHANUM); // ou alors login auto de la forme (num_etab.num_etudiant)

$url_retour=optional_param("url_retour","",PARAM_CLEAN);

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates


$fiche=<<<EOF

<script type="text/javascript">

function maj_mdp() {
    if (document.monform.auth.value=='ldap') {
        document.monform.password.value='{fourni par ldap}';
        document.monform.ldap.className="saisie_bouton visible";
    } else  {
     document.monform.password.value='';
     document.monform.ldap.className="saisie_bouton cache";
    }
     document.monform.cmdp.value=document.monform.password.value;
}

</script>

<form name="monform" id="monform" action="action.php" method="post">
<table class="fiche">
        <tbody>
          <tr>
            <th>{form_login}</th>
            <td><input {readonly} type="text" name="login" size="40" class="required"
                    title="{js_login_manquant}" value="{login}" /></td>
          </tr>
          <tr>
            <th>{form_nom}</th>
            <td><input type="text" name="nom" id="nom" size="40" class="required"
            title="{js_nom_manquant}" value="{nom}" /></td>
          </tr>
          <tr>
            <th>{form_prenom}</th>
            <td><input type="text" name="prenom" id="prenom" size="40" class="required"
            title="{js_prenom_manquant}" value="{prenom}"/></td>
          </tr>
          <tr>
            <th>{form_numetud}</th>
            <td><input type="text" name="numetudiant" id="numetudiant" size="40" class="required"
            title="{js_numetudiant_manquant}" value="{numetudiant}" /></td>
          </tr>
          <tr>
            <th>{form_mdp}</th>
            <td>


            <input type="password" id="password" name="password" size="15" class="required validate-password"
            title="{js_mdp_vide}" value="{password}" />

           <span class="unmask">
              <input id="passwordunmask" value="1" type="checkbox"
                     onclick="unmaskPassword('password')" />  {reveler}
           </span>



            </td>
          </tr>
          <tr>
            <th>{form_cmdp}</th>
            <td><input type="password" id="cmdp" name="cmdp" size="15" class="required validate-password-confirm"
            title="{js_mdp_conf_vide}" value="{password}" />

             <span class="unmask">
              <input id="cmdpunmask" value="1" type="checkbox"
                     onclick="unmaskPassword('cmdp')" /> {reveler}
             </span>


            </td>
          </tr>
           <tr>
            <th>{form_mail}</th>
            <td><input type="text" name="email" id="email" size="40" class="validate-email"
             title="{js_mail_incorrect}" value="{email}" /></td>
          </tr>
             <tr>
            <th>{form_auth}</th>
            <td>{select_auth}
             <!-- START BLOCK : ldap -->
                 <input id="ldap" type="button" class="saisie_bouton  {visible}"
                   onclick="{action}" value="{consulter_annuaire}" />

             <!-- END BLOCK : ldap -->


            </td>
          </tr>


<!-- START BLOCK : EXAMENS -->
            <tr>
            <th>{form_attribues}</th>
            <td>{exam_attribues}</td>
          </tr>
<!-- END BLOCK : EXAMENS -->

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

EOF;


$tpl->assignInclude("corps",$fiche,T_BYVAR);	// le template g�rant le etudiant

$tpl->prepare($chemin);

$tpl->assign("_ROOT.id",$id);
$tpl->assign("_ROOT.url_retour",$url_retour);

$CFG->utiliser_prototype_js=1;  //forc�
$CFG->utiliser_validation_js=1;


if ($id !=-1){   //modif
    v_d_o_d("um");
	$tpl->assign("_ROOT.titre_popup",traduction("modifier_etudiant")." ".$id);
	$ligne=get_inscrit($id);
	$tpl->assign("readonly"," readonly");
    $tpl->newBlock("EXAMENS");
    $examens="";
    $res=get_examens_inscrits($id,'nom_examen',0);
    foreach($res as $exam)
        $examens.=$exam->nom_examen."<br/>";
    $tpl->assign('exam_attribues',$examens);
}
else{
	$tpl->assign("_ROOT.titre_popup",traduction ("nouveau_candidat"));
	$tpl->assign("readonly"," ");
	//pas de notice PHP !
	$ligne=new StdClass();
	$ligne->login=$ligne->nom=$ligne->prenom=$ligne->email=$ligne->numetudiant=$ligne->password="";
	$ligne->auth="manuel";
    $ligne->etablissement=$ide;
     $ligne->tags='';

}


$tpl->gotoBlock("_ROOT");
$tpl->assignObjet($ligne,true);
//maj des  limtes des mots de passe
$tpl->assign("longueur_pwd",$CFG->longueur_mini_password);
$tpl->assign("js_mdp_vide",traduction ("js_mdp_vide",false,$CFG->longueur_mini_password));
$tpl->assign("js_mdp_conf_vide",traduction ("js_mdp_conf_vide",false,$CFG->longueur_mini_password));

print_select_from_table($tpl,"select_auth",get_auth_methodes($ide,false),"auth","","onchange='maj_mdp();'","id","texte","",$ligne->auth);

if (auth_ldap_init($ide))  {// rev 936
    $tpl->newBlock("ldap");
    $tpl->assign("visible", $ligne->auth=="ldap"? "visible":"cache");
    $tpl->assign("action","javascript:majAjax('ajout.php',false,'monform');");

}

if ($CFG->activer_tags_candidat) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}

$tpl->gotoBlock("_ROOT");

if ($id=="-1")
    print_bouton_reset($tpl,"validator.reset();");
else
    $tpl->assign("bouton_reset","");


$tpl->printToScreen();										//affichage
?>
