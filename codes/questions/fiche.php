<?php


/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Fiche d'item
//
////////////////////////////////
$fiche =<<<EOF
<table class="fiche" width="90%">
        <tbody>
             <tr>
            <th width='30%' >{form_id}</th>
            <td>{id_etab}.{id}</td>
        </tr>
          <tr>
            <th>{form_libelle} </th>
            <td >{titre}</td>
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
            <th>
             {form_date_de_utilisation}</th>
            <td>{date_utilisation}</td>
          </tr>
           <tr>
            <th>
             {form_date_de_envoi}</th>
            <td>{date_envoi}</td>
          </tr>
          <tr>
            <th>
               {form_ref_c2i} </th>
            <td >{domaine}</td>
          </tr>
          <tr>
            <th>
               {form_alinea} </th>
            <td >{alinea}</td>
          </tr>
 <!-- START BLOCK : ancien_domaine -->
         <tr>
            <th>
               {form_ancien_domaine} </th>
            <td >{ancien_domaine} : {ancien_domaine_texte} <br/>
                 {ancien_alinea} : {ancien_alinea_texte}
            </td>
          </tr>
 <!-- END BLOCK : ancien_domaine -->

         <tr>
            <th>
               {form_famille_proposee} </th>
            <td >
              {id_famille_proposee} {famille_proposee}
              <br/> <span class="commentaire1">{mots_cles_fp} </span>
            </td>
          </tr>
          <tr>
            <th>
               {form_famille_validee} </th>
            <td > {id_famille_validee}  {famille_validee}
            <br/> <span class="commentaire1">{mots_cles_fv} </span></td>
          </tr>
           <tr>
            <th>
               {form_typep} </th>
            <td >{typep}</td>
          </tr>
          <tr>
            <th>
               {form_validation} </th>
            <td >{validation}  {consulter_fiche_v}</td>
          </tr>
           <tr>
            <th>
               {form_auteurs} {form_nom_coll} </th>
            <td >{auteur} <ul style="display:inline;">{consulter_fiche}</ul></td>
          </tr>
          <tr>
            <th>
               {form_etablissement} </th>
            <td >{nom_univ} <ul style="display:inline;">{consulter_fiche_u}</ul></td>
          </tr>
              <tr>
            <th>
             {form_filtree}</th>
            <td>{est_filtree}</td>
          </tr>
          

          <tr class="docs">
            <th>
              {documents}</th>
            <td >
<!-- START BLOCK : docs -->
           <ul>
               <!-- START BLOCK : doc -->
				<li  class="doc">{url_doc}</li>
				<!-- END BLOCK : doc -->
			</ul>
<!-- END BLOCK : docs -->
            </td>
          </tr>
          <tr>
            <th>{reponses}</th>
            <td class="commentaire1"> {cochez_cases} </td>
          </tr>

<!-- START BLOCK : rep -->
          <tr>
            <th> <img src="{chemin_images}/case{bonne}.gif"  alt='' />&nbsp;{reponse} {r}</th>
            <td>{val} 
            <!-- START BLOCK : rep_comm -->
            <div class="commentaire2"> {comm}</div>
            <!-- END BLOCK : rep_comm -->
            
            
            </td>
          </tr>

<!-- END BLOCK : rep -->
<!-- START BLOCK : stats -->
   <tr>
            <th>
               {form_stats} </th>
               <td class="droite">
                    <b>{t_examen} </b>: {nb_examen}
                   <b>{t_nb} </b>: {nb}
                   <b>{t_mini} </b>: {mini}
                   <b>{t_maxi} </b>: {maxi}
                   <b>{t_moyenne} </b>: {moyenne}
                   <b>{t_ec} </b>: {stddev}
                    <!-- START BLOCK : stats2 -->
         			<b>{t_idisc}</b>: {idisc}
         			<b>{t_cdisc}</b>: {cdisc}
          			<!-- END BLOCK : stats2 -->

               </td>
          </tr>
<!-- END BLOCK : stats -->
<!-- START BLOCK : examens -->
   <tr>
            <th>
               {form_utilisation} </th>
               <td>
               <ul>
               <!-- START BLOCK : examen -->
                   {fonc} &nbsp; {consulter_fiche} &nbsp;
                <!-- END BLOCK : examen -->
               </ul>
               </td>
          </tr>
<!-- END BLOCK : examens -->
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


EOF;

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

//rev 981 simplification avec le parametre id complet
if ($id=optional_param('id','',PARAM_CLE_C2I)) {
    $ligne=get_question_byidnat ($id);
    $idq=$ligne->id;
    $ide=$ligne->id_etab;
} else {
    $idq=required_param("idq",PARAM_INT);
    $ide=required_param("ide",PARAM_INT);
    $ligne=get_question ($idq,$ide);
}
require_login("P"); //PP
v_d_o_d("ql"); //PP



$url_retour=optional_param('url_retour','',PARAM_CLEAN);
//echo $url_retour;

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $fiche,T_BYVAR);
$tpl->prepare($chemin);


$tpl->assign("_ROOT.titre_popup",traduction("fiche_question") . " " . $ide . "." . $idq);



if( !$ref=get_question_referentiel($ligne)) {
    $ref=new StdClass();
    $ref->referentielc2i='???';
    $ref->domaine='???';
}

// rev 872 sur la nationale 36 questions invalid�es anciennes avaient un alineas � -1 !

if (!$al=get_question_alinea($ligne)) {
    $al=new StdClass();
    $al->alinea="???";
    $al->aptitude="???";
}

$fam=get_famille($ligne->id_famille_validee,false);     // ca peut arriver !
$fam=$fam?$fam:get_famille_vide();

$fam_prop=get_famille($ligne->id_famille_proposee,false);
$fam_prop=$fam_prop?$fam_prop:get_famille_vide();
if (empty($fam_prop->mots_clesf)) $fam_prop->mots_clesf=traduction("msg_pas_de_mots_cle_famille");
if (empty($fam_prop->famille) && $ligne->famille_proposee != "") $fam_prop->famille = $ligne->famille_proposee;
if (empty($fam->mots_clesf)) $fam->mots_clesf=traduction("msg_pas_de_mots_cle_famille");


$tpl->gotoBlock("_ROOT");

if (!$ligne->id_famille_validee) $ligne->id_famille_validee="";
if (!$ligne->id_famille_proposee) $ligne->id_famille_proposee="";
$tpl->assignObjet($ligne);

// action des eventuels filtres ...
$tpl->assign("titre",affiche_texte_question($ligne->titre));


$ligne->auteur=applique_regle_nom_prenom($ligne->auteur); // rev 841

$tpl->assign("auteur",cree_lien_mailto($ligne->auteur_mail,$ligne->auteur));

$tpl->assign("domaine",  $ref->referentielc2i . " - " . clean($ref->domaine));
$tpl->assign("alinea",  $al->alinea . " - " . clean($al->aptitude));


$tpl->assign("famille_validee", $fam->famille);
if ($fam->idf)
    $tpl->assign("mots_cles_fv", get_infos_famille($fam,true));  // rev 841 mots cl�s ET commentaires
else
    $tpl->assign("mots_cles_fv","");
$tpl->assign("famille_proposee", $fam_prop->famille);
if ($fam_prop->idf)
    $tpl->assign("mots_cles_fp", get_infos_famille($fam_prop,true));  // rev 841 mots cl�s ET commentaires
else
    $tpl->assign("mots_cles_fp","");

//$tpl->assign("validation", strtolower( $ligne->validation));
$nb_avis=nb_avis($ligne->id,$ligne->id_etab);
$nb_experts=config_nb_experts();
// gestion de l'affichage de l'ic�ne de validation
 switch ($ligne->etat) {
      case QUESTION_VALIDEE :
      case QUESTION_REFUSEE :
          $tpl->assign("validation",get_etat_validation($ligne->etat));
         break;
      case QUESTION_NONEXAMINEE:
            default :
            if($nb_avis) 
               $tpl->assign('validation',traduction ("alt_attente")." : ".traduction ("nombre_davis_sur",false, $nb_avis,$nb_experts));
            else 
               $tpl->assign('validation',traduction('alt_non_examinee'));   
            break;
}



$tpl->assign("consulter_fiche_v","");
if ($nb_avis)
    if (($CFG->universite_serveur==1 && a_capacite("qv",1)) ||a_capacite("qv", $ligne->id_etab))
	   print_menu_item($tpl,"consulter_fiche_v",get_menu_item_consulter("commentaires.php?idq=" . $ligne->id . "&ide=" . $ligne->id_etab));



//rev 820 des questions peuvent �tre pour les 2 plateforme, donc le dire ici
$typep="";
if ($ligne->certification=="OUI")
    $typep.=" ".traduction ("certification");
if ($ligne->positionnement=="OUI")
    $typep .=" ".traduction ("positionnement");
 $tpl->assign("typep",$typep);


//$tpl->assign("mots_cles",  $ligne->mots_cles);
$tpl->assign("date_creation",userdate($ligne->ts_datecreation,'strftimedatetimeday'));
$tpl->assign("date_modification",userdate($ligne->ts_datemodification,'strftimedatetimeday'));
$tpl->assign("date_utilisation",format_time($ligne->ts_dateutilisation));
$tpl->assign("date_envoi",userdate($ligne->ts_dateenvoi,'strftimedatetimeday')); // rev 921

$tpl->assign("nom_univ",nom_univ($ligne->id_etab ));
print_menu_item($tpl,"consulter_fiche_u",get_menu_item_consulter("{$CFG->chemin}/codes/acces/etablissement/fiche.php?idq=".$ligne->id_etab));



if ($ligne->auteur_mail && $ligne->auteur) {
if ($cpt=get_compte_byemail($ligne->auteur_mail))
    print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("{$CFG->chemin}/codes/acces/personnel/fiche.php?id=".$cpt->login));
else
   $tpl->assign("consulter_fiche","");
}  else $tpl->assign("consulter_fiche","");

$tpl->setConditionalvalue($ligne->est_filtree,"est_filtree",traduction("oui"),traduction ("non"));

$stats=get_stats_question($idq,$ide,1,time());
if ($stats->nb) {
	$tpl->newBlock("stats");
	$tpl->assignObjet($stats);
	if ($CFG->calcul_indice_discrimination) {
							$tpl->newBlock("stats2");
							$tpl->assign("idisc",$stats->idisc);
							$tpl->assign("cdisc",$stats->cdisc);
	}
}

///////////////////////////////
// gestion des documents attach�s

$docs=get_documents($idq,$ide,false);
		if (count($docs)>0) {
			$tpl->newBlock("docs");
			foreach ($docs as $doc){
				$tpl->newBlock("doc");
				$tpl->assign("url_doc",$doc->url);
			}
		}
///////////////////////////////
// affichage des r�ponses :
$reponses=get_reponses($idq,$ide,false,false);  //certaines questions refus�es n'ont pas de r�ponses ...
$num_rep=0;
foreach($reponses as $ligne_r) {
	$num_rep++;
	$tpl->newBlock("rep");
	$tpl->assign("r", $num_rep);
	$tpl->assign("val", affiche_texte_reponse($ligne_r->reponse));
	if ($ligne_r->bonne == "OUI")
		$tpl->assign("bonne", "1");
	else
		$tpl->assign("bonne", "0");
	if ($CFG->utiliser_commentaires_reponses && !empty($ligne_r->commentaires)) {
	    $tpl->newBlock('rep_comm');
	    $tpl->assign ('comm','('.$ligne_r->commentaires.')');
	}
}

//examen l'utilisant
//if ($liste=get_examens_question ($idq,$ide,false)) {
$liste=get_examens_question ($idq,$ide,false);
    $tpl->newBlock("examens");
    $tpl->assign("form_utilisation",traduction("form_utilisation",false,count($liste)));
    foreach ($liste as $examen) {
        $tpl->newBlock ("examen");
        $tpl->assign("fonc", $examen->id_examen_etab.".".$examen->id_examen);
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../examens/fiche.php?idq=".$examen->id_examen."&amp;ide=".$examen->id_examen_etab));

    }
//}



if ($CFG->activer_tags_question) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$ligne->tags);
}
$tpl->print_boutons_fermeture();

$tpl->printToScreen(); //affichage
?>


