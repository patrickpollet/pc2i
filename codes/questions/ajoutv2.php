<?php


/**
 * @author Patrick Pollet
 * @version $Id: ajoutv2.php 1259 2011-06-06 11:56:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	version REDUITE de la modif d'une question pour lui affecter SEULEMENT
// un nouveau domaine/comp�tence
//
////////////////////////////////

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_login('P'); //PP

// rev 981 simplification des appels via idnationale ( ou -1)
if ($id=optional_param('id','',PARAM_CLEAN)) {  //attention pas CLE_C2I
    if ($id !=-1) {
        $ligne=get_question_byidnat ($id);
        $idq=$ligne->id;
        $ide=$ligne->id_etab;
    } else  {
       $idq=-1; $ide= $USER->id_etab_perso;
    }
} else {
    $idq=optional_param("idq",-1,PARAM_INT);   // -1 en cr�ation
    $ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
}


$url_retour=optional_param("url_retour","",PARAM_PATH);



require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$fiche=<<<EOF

<div id="corps2">
	<div id="xx">
		<ul id="tabs">
			<li> <a class="active_tab" 	href="#fiche">{fiche} </a></li>
		</ul>

<div class="panel" id="fiche">
<form class="mini" action="actionv2.php" method="post" enctype="multipart/form-data" name="monform" id="monform">

<input type="hidden" name="url_retour" value="{url_retour}" />
  <table class="fiche">
        <tbody>

          <tr>
            <th>{form_libelle}</th>
            <td>{titre}
            </td>
          </tr>
          <tr>
            <th>{form_ref_c2i}</th>
            <td>
         {select_referentielc2i}   </td>
          </tr>
          <tr>
            <th>{form_alinea}</th>
            <td>
         {select_alinea}   </td>
          </tr>
           <!-- START BLOCK : famille_proposee -->
          <tr>
            <th>
              {form_famille_proposee}</th>
            <td>
         {select_famille_proposee}
         <br/> <div id="mots_clesf" class="commentaire1">{mots_cles_famille_proposee} </div>
         <!-- START BLOCK : nouvelle_famille_proposee -->
            <br />ou autre famille : <input size="33" type="text" value="{famille_proposee}"
                  name="famille_proposee" class="saisie" onchange="this.form.id_famille_proposee.selectedIndex=0">
          <!-- END BLOCK : nouvelle_famille_proposee -->
            </td>
          </tr>
           <!-- END BLOCK : famille_proposee -->
           <!-- START BLOCK : famille_validee -->
          <tr>
            <th>
              {form_famille_validee}</th>
            <td>
         {select_famille_validee}
         <br/> <div id="mots_clesf" class="commentaire1">{mots_cles_famille_validee}</div>
            </td>
          </tr>
           <!-- END BLOCK : famille_validee -->
          <tr>
           <th>
             {form_date_de_creation}</th>
            <td>{date_creation}</td>
          </tr>
         <tr>
            <th>{form_date_de_modification}</th>
            <td>{date_modification}</td>
          </tr>
          <tr>
            <th> {form_auteurs} {form_nom_coll}</th>
            <td>{auteur}</td>
          </tr>
          <tr>
            <th>{universite}</th>
            <td>{etablissement} <ul style="display:inline;">{consulter_fiche}</ul></td>
          </tr>
          <tr>
            <th>{reponses}</th>
            <td class="commentaire1"> {cochez_cases} </td>
          </tr>

<!-- START BLOCK : rep -->
          <tr>
            <th>{form_reponse} {r}</th>
            <td>
            {reponse} <input type="checkbox" disabled="true" value="OUI" name="bonne_{r}" {chr} /></td>
          </tr>

<!-- END BLOCK : rep -->

        </tbody>
  </table>

<div class="centre">
      {bouton_annuler} &nbsp;{bouton_reset} &nbsp; {bouton:enregistrer}

<input name="id" type="hidden" value="{id}"/>
<input name="ide" type="hidden" value="{ide}"/>

<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->

<!-- START BLOCK : consulter -->
<input name="consulter" type="hidden" value="1"/>
<!-- END BLOCK : consulter -->
</div>

</form>

</div>


</div>

</div>

EOF;


//$tpl->assignInclude("corps", $chemin . "/templates/question.html"); // le template g�rant la liste des questions
$tpl->assignInclude("corps", $fiche,T_BYVAR);


$tpl->prepare($chemin);


$CFG->utiliser_validation_js=1;
$CFG->utiliser_fabtabulous_js=1;




$tpl->assign("_ROOT.id", $idq);
$tpl->assign("_ROOT.ide", $ide);
$tpl->assign("url_retour", $url_retour);

    /////////////////////////////
if ($idq!=-1) { // modification de la question
    	$tpl->assign("_ROOT.titre_popup",traduction("modifier_question") . " " . $ide . "." . $idq);
        if ($CFG->universite_serveur == 1) {
             $nbvalid = nb_validations($idq, $ide);
            if ($nbvalid > 0) {
                if (! is_super_admin()) {
                    erreur_fatale("err_modif_question_validee",$nbvalid);
                }
            } else if (! a_capacite("qv",1)  //rev 843 un expert peut modifier une question sur la nationale ...
                       && ! a_capacite("qm")) // pas 1 (sur $ide !!!)
                            erreur_fatale("err_droits");

        } else {
            //v_d_o_d("qm");
            // rev 977 pas d'appel direct de ce script avec les param�tres qui vont bien ...
            // rev�rification de l'utilisation de cet item dans un examen
            //erreur_fatale('err_action_invalide','');
        }

    $ligne=get_question ($idq,$ide);
    $reponses=get_reponses($idq,$ide,false);

} else {
    erreur_fatale('err_action_invalide','');
}



$tpl->gotoBlock('_ROOT');


$tpl->assignObjet($ligne);

// action des eventuels filtres ...
$tpl->assign("titre",affiche_texte_question($ligne->titre));

$tpl->assign("date_creation",userdate($ligne->ts_datecreation,'strftimedatetime'));
$tpl->assign("date_modification",userdate($ligne->ts_datemodification,'strftimedatetime'));
$tpl->assign("auteur",cree_lien_mailto($ligne->auteur_mail,$ligne->auteur));
$et=get_etablissement($ligne->id_etab,false);
if ($et) {
     $tpl->assign("etablissement",$et->nom_etab);
     print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("{$CFG->chemin}/codes/acces/etablissement/fiche.php?idq=".$et->id_etab));
}

// affichage des r�ponses

$i=1;
foreach ($reponses as $reponse) {
    $tpl->newBlock("rep");
    $tpl->assign("r", $i);
    $tpl->assign("reponse", $reponse->reponse);
    $tpl->setChecked ($reponse->bonne == "OUI","chr");
    if ($i<=$CFG->nombre_reponses_mini)
    		$tpl->assign ("classe_reponse","required");
    else 	$tpl->assign ("classe_reponse","");
    $i++;

}




$tpl->gotoBlock("_ROOT");

// g�n�ration des listes d�roulantes
$attrs_ref= "style='width:380px;'   title=\"".traduction("js_referentiel_manquant")."\"";
$attrs_alinea="style='width:380px;'   title=\"".traduction("js_alinea_manquant")."\"";
$attrs_famille="style='width:380px;'";

// rev 977 valeurs par d�faut
$ref=$ligne->referentielc2i;
$al=$ligne->alinea;


// rev 836 sur la nationale on peut/doit changer directement la famille valid�e
if ($CFG->universite_serveur==1 && is_super_admin()) {
    // si pas encore de famille valid�e
    $ligne->id_famille_validee=$ligne->id_famille_validee?$ligne->id_famille_validee:$ligne->id_famille_proposee;
	$tpl->newBlock("famille_validee");
	print_selecteur_ref_alinea_famille($tpl,"monform",
		"_ROOT.select_referentielc2i",'required validate-selection', $attrs_ref, //select referentiel
		"_ROOT.select_alinea",'required validate-selection',$attrs_alinea,       //select alinea
		"select_famille_validee",'',$attrs_famille,                       //select famille
		false,false,false,                                                 //input famille
		$ref,$al,$ligne->id_famille_validee,false);     //valeurs actuelles

} else {
	$tpl->newBlock("famille_proposee");
	$famille_selecte=$ligne->famille_proposee;
	if ($CFG->peut_proposer_nouvelle_famille)
		$tpl->newBlock ("famille_proposee");
	print_selecteur_ref_alinea_famille($tpl,"monform",
		"_ROOT.select_referentielc2i",'required validate-selection', $attrs_ref, //select referentiel
		"_ROOT.select_alinea",'required validate-selection',$attrs_alinea,       //select alinea
		"select_famille_proposee",'',$attrs_famille,                       //select famille
		false,false,false,                                                 //input famille
		$ref,$al,$ligne->id_famille_proposee,false);     //valeurs actuelles
}



print_bouton_annuler($tpl);

if ($idq=="-1")
    print_bouton_reset($tpl,"validator.reset();");
else
    $tpl->assign("_ROOT.bouton_reset","");

$tpl->printToScreen(); //affichage
?>