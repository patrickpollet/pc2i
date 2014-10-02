<?php


/**
 * @author Patrick Pollet
 * @version $Id: ajout.php 1265 2011-09-20 07:01:56Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Ajout et modification d'item
//	et duplication (ajout par recopie)
//
////////////////////////////////
/*
*rev 1.41 utilise $CFG->chemin_ressources pour export XML
*/

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

if ($dup_id=optional_param('dup_id','',PARAM_CLE_C2I)) {
    $ligne=get_question_byidnat ($dup_id);
    $copie_id=$ligne->id;
    $copie_ide=$ligne->id_etab;
} else {
    $copie_id=optional_param("copie_id",0,PARAM_INT); //duplication ?
    $copie_ide=optional_param("copie_ide",0,PARAM_INT);
}

$url_retour=optional_param("url_retour","",PARAM_CLEAN);



require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

require_once ($CFG->chemin_commun.'/lib_editeur.php');

$fiche=<<<EOF

<div id="corps2">
	<div id="xx">
		<ul id="tabs">
			<li> <a class="active_tab" 	href="#fiche">{fiche} </a></li>
			<li> <a class="" 			href="#methodo">{methodologie}</a> </li>
			<li> <a class="" 			href="#contrat">{contrat_usage}</a> </li>

		</ul>

<div class="panel" id="fiche">
<form class="mini" action="action.php" method="post" enctype="multipart/form-data" name="monform" id="monform">
<input type="hidden" name="MAX_FILE_SIZE" value="750000000" />

<input type="hidden" name="url_retour" value="{url_retour}" />
  <table class="fiche">
        <tbody>

          <tr>
            <th>{form_libelle}</th>
            <td>
            <!--
            <textarea name="titre" cols="60" rows="5" class="required" title="{js_libelle_manquant}">{titre}</textarea>
             -->
             {editeur_titre}
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
<!-- START BLOCK : docs-->
          <tr>
            <th>{documents}</th>
            <td class="commentaire1">{cochez_cases_sup}</td>
          </tr>
<!-- START BLOCK : doc-->
          <tr>
            <td>{urldoc}</td>
            <td>
             <p class="double">
            <label for="file{n}">{form_fichier_local}</label>
             <input type="file" size="50" name="file{n}" id="file{n}"/>
            </p>
             <p class="double">
             <label for="descs_{n}">{form_description}</label>
             <input name="descs[{n}]" size="40" class="saisie" value="{description}" id="descs_{n}"/>

               <!-- START BLOCK : supp_doc -->
               <input type="checkbox" value="{n}" name="supps[{n}]"/>
                <!-- END BLOCK : supp_doc -->
              </p>
               </td>
          </tr>
<!-- END BLOCK : doc-->
<!-- END BLOCK : docs-->
          <tr>
            <th>{reponses}</th>
            <td class="commentaire1"> {cochez_cases} </td>
          </tr>

<!-- START BLOCK : rep -->
          <tr>
            <th>{form_reponse} {r}</th>
            <td>
            <!--
            <textarea name="reponse_{r}" rows="3" cols="45" class="saisie {classe_reponse}"
            title="{js_reponse_manquante}">{reponse}</textarea>
            -->
            {editeur}
            <input type="checkbox" value="OUI" name="bonne_{r}" {chr} />
             <!-- START BLOCK : rep_comm -->
              <div class="commentaire2">{commentaires_reponses}</div>
              {editeur_comm}
             <!-- END BLOCK : rep_comm -->
            </td>
            
          </tr>

<!-- END BLOCK : rep -->

<!-- START BLOCK : test -->
<tr>
<th></th>
<td>{test}</td>
</tr>
<!-- END BLOCK : test -->
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
      <br/>
<div class="centre">
      {bouton_annuler} &nbsp;{bouton_reset} &nbsp; {bouton:enregistrer}

<input name="id" type="hidden" value="{id}"/>
<input name="ide" type="hidden" value="{ide}"/>
<input name="dupliquer" type="hidden" value="{dupliquer}"/>


<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
<!-- START BLOCK : duplication -->
<input name="duplication" type="hidden" value="1"/>
<!-- END BLOCK : duplication -->
<!-- START BLOCK : consulter -->
<input name="consulter" type="hidden" value="1"/>
<!-- END BLOCK : consulter -->
</div>

</form>

</div>

<div class="panel" id="methodo">
     <iframe src="http://c2i.education.fr/pf/question_methodologie.html" name="ext" height="400" width="800" frameborder="0">
     </iframe> 
</div>

<div class="panel" id="contrat">
     <iframe src="http://c2i.education.fr/pf/contrat_usage.html" name="ext" height="400" width="800" frameborder="0">
     </iframe> 
</div>

</div>

</div>
{active}

EOF;


//$tpl->assignInclude("corps", $chemin . "/templates/question.html"); // le template g�rant la liste des questions
$tpl->assignInclude("corps", $fiche,T_BYVAR);

$tpl->prepare($chemin);


$CFG->utiliser_validation_js=1;
$CFG->utiliser_fabtabulous_js=1;



//////////////////////////////
// gestion de la duplication
// V 1.5 faite ici et plus dans un inc_dupliquer
$dupliquer = 0;

if ($copie_id && $copie_ide) {
	 v_d_o_d("qd");
    $idq=copie_question ($copie_id,$copie_ide);
     $dupliquer = 1;
}





    /////////////////////////////
if ($idq!=-1) { // modification de la question
    if ($dupliquer == 0) {
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
            v_d_o_d("qm");
            // rev 977 pas d'appel direct de ce script avec les param�tres qui vont bien ...
            // rev�rification de l'utilisation de cet item dans un examen
            $nb = est_utilise_examen($idq, $ide);
            if ($nb) erreur_fatale('err_action_invalide','');
        }
    } else
        	$tpl->assign("_ROOT.titre_popup", traduction("dupliquer_question") . " " . $copie_ide . "." . $copie_id."->".$ide.".".$idq );

    $ligne=get_question ($idq,$ide);
    $reponses=get_reponses($idq,$ide,false);
    $documents=get_documents($idq,$ide);





} else {
    v_d_o_d("qa");

    $tpl->assign("_ROOT.titre_popup", traduction( "nouvelle_question"));
    $ligne=new StdClass();
    $ligne->titre=$ligne->referentielc2i=$ligne->alinea=$ligne->famille_proposee=$ligne->mots_cles="";
    $ligne->auteur=get_fullname($USER->id_user);
    $ligne->auteur_mail=get_mail($USER->id_user);
    $ligne->id_etab=$USER->id_etab_perso;
    $ligne->tags='';

    $ligne->id_famille_proposee =$ligne->id_famille_validee= 0;
    $ligne->ts_datecreation=$ligne->ts_datemodification=time();
    $reponses= array();
    $documents=array();
    //il faut cr�er un record pour avoir un id et un dossier de documents correct
    if ($CFG->utiliser_editeur_html) {
        $ligne->etat= QUESTION_NONEXAMINEE;
        $ligne->positionnement=$ligne->certification='NON';
        if ($USER->type_plateforme=="positionnement"){
            $ligne->positionnement='OUI';
        } else {
            $ligne->certification='OUI';
        }
        $idq=insert_record("questions",$ligne,true,"id");
        $dupliquer=1;  // pour m�nage si annulation
    }
}

if ($CFG->utiliser_editeur_html) {
    get_document_location($idq,$ide,true); // autocreation dossier des documents
}

//tests
/***
if ($CFG->utiliser_editeur_html) {
    $tpl->newBlock('test');
	require ($CFG->chemin_commun.'/lib_editeur.php');
	//function print_textarea($usehtmleditor, $rows, $cols, $width, $height, $name, $value='', $class='',$title='',$return=true, $question_id='',$id='') {
	$str=print_textarea(true, 10,60,0,0,"toto","",'required',traduction('js_libelle_manquant') ,true,'','');
	$tpl->assign("test",$str);
	$tpl->assign ("_ROOT.active",use_html_editor("toto",'', '',true) );
} else $tpl->assign ("_ROOT.active","");
***/

$tpl->gotoBlock('_ROOT');
$tpl->assign("_ROOT.id", $idq);  // apr�s une eventuelle cr�ation
$tpl->assign("_ROOT.ide", $ide);
$tpl->assign("url_retour", $url_retour);
$tpl->assign("dupliquer",$dupliquer);

$tpl->assignObjet($ligne);
// rev 979 remettre le libell� SANS la conversion nl2br
//$tpl->assign("titre",$ligne->titre);
//<textarea name="titre" cols="60" rows="5" class="required" title="{js_libelle_manquant}">{titre}</textarea>
$tpl->assign('editeur_titre',print_textarea($CFG->utiliser_editeur_html,5,60,0,0,"titre",$ligne->titre,'saisie required',
             traduction('js_libelle_manquant') ,true,$idq,$ide));

$tpl->assign("date_creation",userdate($ligne->ts_datecreation,'strftimedatetime'));
$tpl->assign("date_modification",userdate($ligne->ts_datemodification,'strftimedatetime'));
$tpl->assign("auteur",cree_lien_mailto($ligne->auteur_mail,$ligne->auteur));
$et=get_etablissement($ligne->id_etab,false);
if ($et) {
     $tpl->assign("etablissement",$et->nom_etab);
     print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("{$CFG->chemin}/codes/acces/etablissement/fiche.php?idq=".$et->id_etab));
}

// affichage des r�ponses
 while (count($reponses) < $CFG->nombre_reponses_maxi)
    	$reponses[]=get_reponse_vide();

$i=1;
//$tpl->assignGlobal("js_reponse_manquante",traduction("js_reponse_manquante",false,$CFG->nombre_reponses_mini,$CFG->nombre_reponses_maxi));
$js_reponse_manquante=traduction("js_reponse_manquante",false,$CFG->nombre_reponses_mini,$CFG->nombre_reponses_maxi);
foreach ($reponses as $reponse) {
    $tpl->newBlock("rep");
    $tpl->assign("r", $i);
    //$tpl->assign("reponse", $reponse->reponse);
    //if ($i<=$CFG->nombre_reponses_mini)
    //       $tpl->assign ("classe_reponse","required");
    //else    $tpl->assign ("classe_reponse","");

     //<textarea name="reponse_{r}" rows="3" cols="45" class="saisie {classe_reponse}"
      //      title="{js_reponse_manquante}">{reponse}</textarea>

     $tpl->assign('editeur',print_textarea($CFG->utiliser_editeur_html,3,45,0,0,
             "reponse_$i",$reponse->reponse,
             'saisie'.($i<=$CFG->nombre_reponses_mini?' required':''),$js_reponse_manquante ,true,$idq,$ide));



    $tpl->setChecked ($reponse->bonne == "OUI","chr");
    if ($CFG->utiliser_commentaires_reponses) {
        $tpl->newBlockNum('rep_comm',$i,'r');
        
        $tpl->assign('editeur_comm',print_textarea($CFG->utiliser_editeur_html,3,45,0,0,
                     "comm_$i",$reponse->commentaires,
                     'saisie'.'','',true,$idq,$ide));
        
        
        
    }

    $i++;

}

// gestion des documents attach�s


if (!$CFG->utiliser_editeur_html) {
        $tpl->newBlock("docs");
    $i=1;
    $indices_libres=array();
    for ($i=1; $i<=$CFG->max_documents_par_question;$i++)
        $indice_libres[$i]=$i;

    //on peut supprimer des documents dans le desordre ...
    foreach ($documents as $document) {
        $tpl->newBlock("doc");
        $tpl->assign("n", $document->id_doc);
        $tpl->assign("urldoc",  $document->url);
        $tpl->assign("description",$document->description );
        $tpl->newBlock("supp_doc");   //V 1.5 suppression uniquement pour les existants
        $tpl->assign("n", $document->id_doc);  //num�ro du document
        unset($indice_libres[$document->id_doc]);
    }

    foreach ($indice_libres as $libre) {
        $tpl->newBlock("doc");
        $tpl->assign("n", $libre);
        $tpl->assign("urldoc","");
        $tpl->assign("description","");

    }
}




$tpl->gotoBlock("_ROOT");

// génération des listes déroulantes
$attrs_ref= "style='width:380px;'   title=\"".traduction("js_referentiel_manquant")."\"";
$attrs_alinea="style='width:380px;'   title=\"".traduction("js_alinea_manquant")."\"";
$attrs_famille="style='width:380px;'";

// rev 977 valeurs par défaut

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


if ($CFG->activer_tags_question) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}

if ($CFG->utiliser_editeur_html) {
    $tpl->assign ("_ROOT.active",use_html_editor('','', '',true) );
} else $tpl->assign ("_ROOT.active","");


 // rev 984 avec l'�diteur HTML on a d�ja cr�� la question (pour g�rer les ressources attach�es)
if ($dupliquer || $CFG->utiliser_editeur_html)
    print_bouton_annuler_duplication($tpl);
else
    print_bouton_annuler($tpl);

if ($idq=="-1")
    print_bouton_reset($tpl,"validator.reset();");
else
    $tpl->assign("_ROOT.bouton_reset","");

$tpl->printToScreen(); //affichage
?>