<?php

/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 1249 2011-05-21 16:16:41Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
////////////////////////////////
//
//	Fiche d'item
//
////////////////////////////////
$fiche=<<<EOF
<table class="fiche" width="90%">
        <tbody>
          <tr>
            <th>
              {form_login}</th>
            <td>{login}</td>
           </tr>

           <tr>
            <th>{form_etablissement}</th>
            <td>{etablissement}&nbsp; <ul style="display:inline;"> {consulter_fiche}</ul></td>
          </tr>
          <tr>
            <th>{form_nom}</th>
            <td>{nom}</td>
          </tr>
		  <tr>
            <th>{form_prenom}</th>
            <td>{prenom}</td>
          </tr>
		  <tr>
            <th>{form_numetud}</th>
            <td>{numetudiant}</td>
          </tr>
           <tr>
            <th>{form_mail}</th>
            <td>{email}</td>
          </tr>
           <tr>
            <th>{form_auth}</th>
            <td>{auth}</td>
          </tr>
              <tr>
            <th>{form_origine}</th>
            <td>{origine}</td>
          </tr>
             <tr>
            <th>{form_mdp}</th>
            <td>{password}</td>
          </tr>
		  <tr>
            <th>{examens}</th>
            <td>
<!-- START BLOCK : fonction_s -->
				{fonc} &nbsp; <ul style="display:inline;">{consulter_fiche}</ul><br/>
<!-- END BLOCK : fonction_s -->
 <!-- START BLOCK : autre_pf -->
              <div class="commentaire1">
              {aussi_inscrit}
              </div>
            <!-- END BLOCK : autre_pf -->
			</td>
          </tr>
          <tr>
            <th>
             {form_date_de_creation}</th>
            <td>{date_creation}</td>
          </tr>

         <tr>
            <th>
             {form_date_de_modification}</th>
            <td>{date_modification}</td>
          </tr>

            <tr>
            <th>{form_derniere_connexion}</th>
            <td>{derniere_connexion}</td>
          </tr>
  <!-- START BLOCK : tags -->
          <tr>
            <th>
               {form_tags} </th>
            <td >{tags}
            <br/>
              <span class="commentaire1">{info_tags}</span></td>
          </tr>
<!-- END BLOCK : tags -->
        </tbody>
      </table>
      <br/>
EOF;

$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramï¿½tres


 $id=required_param("id",PARAM_CLEAN);  //tempo

$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT);

//parametres de reaffichage de la liste sous jacente
$url_retour=optional_param("url_retour","",PARAM_RAW);
if ($url_retour)  //garde adresse de rafraichissement de la liste des examens
    var_register_session("liste_candidats", $url_retour);


$ligne=get_inscrit($id);

require_login('P'); //PP
v_d_o_d("etl"); // droits de regarder un étudiant

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //crï¿½er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps",$fiche,T_BYVAR);

$tpl->prepare($chemin);

if ($ligne->auth=='manuel') {
    if (! $CFG->montrer_password_inscrits)
    $ligne->password='********';
}
else $ligne->password ="<span class='commentaire1'>".traduction("msg_password_ldap")."</span>";

$tpl->assignObjet($ligne);
$tpl->assign("email", cree_lien_mailto($ligne->email,$ligne->email));
$tpl->assign("date_creation",userdate($ligne->ts_datecreation,'strftimedatetimeday'));
$tpl->assign("date_modification",userdate($ligne->ts_datemodification,'strftimedatetimeday'));
$tpl->assign("derniere_connexion",format_time($ligne->ts_derniere_connexion));

$et=get_etablissement($ligne->etablissement,false);
if ($et) {
    $tpl->assign("etablissement",$et->nom_etab);
    print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../etablissement/fiche.php?idq=".$et->id_etab));
}

// examens à indiquer
$res=get_examens_inscrits($id,'id');
foreach($res as $rowd) {
	$tpl->newBlock("fonction_s");
	$tpl->assign("fonction_s.fonc", $rowd->id_etab . "." . $rowd->id_examen . " " . ucfirst($rowd->nom_examen));
     print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../examens/fiche.php?idq=".$rowd->id_examen."&amp;ide=".$rowd->id_etab));
   }

 $nbe=est_inscrit_examen(false,false,$ligne->login); //nombre examens (toute PF !)
    if ($nbe>count($res)) {  //inscrit aussi autre PF  rev 843 explique pourquoi pas de poubelle
        $tpl->newBlock("autre_pf");
        if ($USER->type_plateforme=="positionnement")
            $autre_pf=traduction("certification");
         else
            $autre_pf=traduction ("positionnement");
        $tpl->assign ("aussi_inscrit",traduction ("msg_aussi_inscrit",false, $nbe-count($res),$autre_pf));

    }

if ($CFG->activer_tags_candidat) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}

$tpl->assign("_ROOT.titre_popup", traduction("fiche_etudiant")." ".$ligne->login);


$tpl->print_boutons_fermeture();

$tpl->printToScreen(); //affichage
?>

