<?php



/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 1277 2011-11-05 15:11:47Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Fiche d'item
//
////////////////////////////////

$fiche=<<<EOF


<table class="fiche">
        <tbody>
          <tr>
            <th>{form_login}</th>
            <td >{login}</td>
          </tr>
            <tr>
            <th>{form_etablissement}</th>
            <td>{etablissement} &nbsp;
                 <ul style="display:inline;">{consulter_fiche}  </ul>
            </td>
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
            <th>{form_admin}</th>
            <td><img src="{chemin_images}/case{ch_a_e}.gif" alt=""/></td>
          </tr>
		  <tr>
            <th>{form_pos}</th>
            <td><img src="{chemin_images}/case{ch_a_p}.gif" alt="" /></td>
          </tr>
		  <tr>
            <th>{profils}</th>
            <td>
            <table class='sansbordure'>
<!-- START BLOCK : fonction_s -->
			<tr>
        <td>{fonc} </td><td><ul style="display:inline;"> {consulter_fiche} </ul></td>
            </tr>
<!-- END BLOCK : fonction_s -->
			</table>
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

<!-- START BLOCK : examens -->
   <tr>
            <th>
               {form_examens_auteur} </th>
               <td>
                 <table class='sansbordure'>
               <!-- START BLOCK : examen -->
                   <tr>
                   <td>{fonc}</td><td> <ul style="display:inline;">{consulter_fiche}</ul></td>
                   </tr>
                <!-- END BLOCK : examen -->
                </table>
               </td>
          </tr>
<!-- END BLOCK : examens -->
<!-- START BLOCK : questions -->
   <tr>
           <th>
               {form_questions_auteur} </th>
               <td>
                <table class='sansbordure'>
               <!-- START BLOCK : question -->
                   <tr>
                   <td>{fonc} </td><td><ul style="display:inline;">{consulter_fiche} </ul></td>
                   </tr>
                <!-- END BLOCK : question -->
                </table>
               </td>
          </tr>
<!-- END BLOCK : questions -->

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
<!-- START BLOCK : modifier -->
<form action="ajout.php?id={id}" method="post">
<input name="ide" type="hidden" value="{ide}" />
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
{bouton_modifier}
</form>
<! -- END BLOCK :modifier -->
EOF;


$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

$id=required_param("id",PARAM_CLEAN);
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT);


//parametres de reaffichage de la liste sous jacente
$url_retour=optional_param("url_retour","",PARAM_RAW);
if ($url_retour)  //garde adresse de rafraichissement de la liste des examens
    var_register_session("liste_personnels", $url_retour);


$refresh=optional_param("refresh",0,PARAM_INT); //rafraichir la liste  sous-jacente qui a ouvert le popup


require_login('P'); //PP
v_d_o_d("ul");

$ligne=get_utilisateur($id);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps",$fiche,T_BYVAR);
$tpl->prepare($chemin);

$tpl->assignObjet($ligne);
$tpl->assign("email", cree_lien_mailto( $ligne->email,$ligne->email));
$tpl->assign("date_creation",userdate($ligne->ts_datecreation,'strftimedatetimeday'));
$tpl->assign("date_modification",userdate($ligne->ts_datemodification,'strftimedatetimeday'));
$tpl->assign("derniere_connexion",format_time($ligne->ts_derniere_connexion));

$et=get_etablissement($ligne->etablissement,false);
if ($et) {
     $tpl->assign("etablissement",$et->nom_etab);
     print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../etablissement/fiche.php?idq=".$et->id_etab));
}
$tpl->setConditionalValue($ligne->est_admin_univ == "O","_ROOT.ch_a_e", "1","0");
$tpl->setConditionalValue($ligne->limite_positionnement == "1","_ROOT.ch_a_p", "1","0");

// profils � indiquer

$profiles=get_profils_utilisateur($ligne->login,"intitule");
    foreach ($profiles as $p) {
        $tpl->newBlock("fonction_s");
        $tpl->assign("fonction_s.fonc", ucfirst($p->intitule));
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../fiche_profil.php?idq=".$p->id_profil));

    }

//questions qu'il a cr��

if ($liste=get_questions_auteur ($ligne->email)) {
   // print_r($liste);
    $tpl->newBlock("questions");
    foreach ($liste as $q) {
        $tpl->newBlock ("question");
        $tpl->assign("fonc", $q->id_etab.".".$q->id);
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../questions/fiche.php?idq=".$q->id."&amp;ide=".$q->id_etab));

    }
}

//examen qu'il a cr��

if ($liste=get_examens_auteur ($ligne->email)) {
   // print_r($liste);
    $tpl->newBlock("examens");
    foreach ($liste as $e) {
        $tpl->newBlock ("examen");
        $tpl->assign("fonc", $e->id_etab.".".$e->id_examen);
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../../examens/fiche.php?idq=".$e->id_examen."&amp;ide=".$e->id_etab));

    }
}

if ($CFG->activer_tags_utilisateur) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}

/**
 * essai d'ajouter d'un bouton modifier � la fiche
 * pb au retour de ajout avec l'opener qui n''est plus liste ...
$tpl->gotoBlock("_ROOT");
if (teste_droit("um")) {
  $tpl->newBlock("modifier");
  $tpl->assign("url_retour",$url_retour);
  $tpl->assign("id",$ligne->login);
    $tpl->assign("ide",$ide);

  $tpl->assign("bouton_modifier",get_bouton_action ("modifier","","","submit"));
    form_session($tpl);
}
**/

$tpl->assign("_ROOT.titre_popup", traduction("fiche_personnel")." ".$ligne->login);

$tpl->print_boutons_fermeture();


$tpl->printToScreen();
?>

