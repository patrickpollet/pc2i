<?php
/**
 * @author Patrick Pollet
 * @version $Id: fiche.php 1252 2011-05-23 10:20:26Z ppollet $
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
            <th>{form_libelle}</th>
            <td>{titre}</td>
          </tr>
          <tr>
            <th>
             {form_ref_c2i}</th>
            <td>{domaine}</td>
          </tr>
          <tr>
            <th>{form_alinea}</th>
            <td>{alinea}</td>
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
<!--
   <tr>
             <th>
             {form_ordre}</th>
            <td>{ordre}</td>
          </tr>
-->
          <tr>
            <th>{form_etablissement}</th>
            <td>{etablissement}&nbsp; <ul style="display:inline;">{consulter_fiche}</ul></td>
          </tr>
          <tr>
            <th>{form_lien}</th>
			<td>
			<a href="#" onclick="openPopup('{url}','','{lp}','{hp}}')">{url}</a>
            <br/>

          </td>
          </tr>
<!--
           <tr>
             <th>
             {form_version}</th>
            <td>{version}</td>
          </tr>
-->          
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
             {form_modifiable}</th>
            <td>{modifiable}</td>
          </tr>
          
            <tr>
            <th>
             {form_filtree}</th>
            <td>{filtree}</td>
          </tr>
          
          
              <tr>

            <th>{form_utilisee_parcours}</th>
            <td>
            <table class='sansbordure'>
            <!-- START BLOCK : fonction_s -->
                <tr>
				<td>{fonc} &nbsp; [{type}]</td><td> <ul style="display:inline;">{consulter_fiche}</ul></td>
				</tr>
<!-- END BLOCK : fonction_s -->
             </table>
            </td>
          </tr>
          <tr>
            <th>
               {form_tags} </th>
            <td >{tags}
            <br/>
              <span class="commentaire1">{info_tags}</span></td>
          </tr>

        </tbody>
</table>
      <br/>


EOF;



$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_login("E"); //PP 07/02/2009 consultable aussi par un candidat !

 if (!$CFG->utiliser_notions_parcours)
    erreur_fatale("err_pas_de_notions_parcours_ici");


$id = required_param("id", PARAM_INT);
$url_retour = optional_param("url_retour", "",PARAM_INT);  //retour parcours (non vide) ou liste notions (vide)


$ligne = get_ressource($id);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $fiche, T_BYVAR);

$tpl->prepare($chemin);
$tpl->assign("_ROOT.titre_popup", traduction("fiche_ressource") . " " . $id);

$tpl->assignObjet($ligne);

$tpl->assign("titre", affiche_texte($ligne->titre));
if( !$ref=get_referentiel($ligne->domaine,false)) {
    $ref=new StdClass();
    $ref->referentielc2i='???';
    $ref->domaine='???';
}

// rev 872 sur la nationale 36 questions invalid�es anciennes avaient un alineas � -1 !

if (!$al=get_alinea($ligne->competence,$ligne->domaine,false)) {
    $al=new StdClass();
    $al->alinea="???";
    $al->aptitude="???";
}

$tpl->assign("domaine",  $ref->referentielc2i . " - " . clean($ref->domaine));
$tpl->assign("alinea",  $al->alinea . " - " . clean($al->aptitude));

$tpl->assign("date_creation",userdate($ligne->ts_datecreation,'strftimedatetimeday'));
$tpl->assign("date_modification",userdate($ligne->ts_datemodification,'strftimedatetimeday'));

$et=get_etablissement($ligne->id_etab,false);
if ($et) {
    $tpl->assign("etablissement",$et->nom_etab);
    if (teste_droit("etl"))
        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../acces/etablissement/fiche.php?idq=".$et->id_etab));
    else $tpl->assign("consulter_fiche","");
} else
  		$et=get_etablissement ($USER->id_etab_perso);

$lien=get_lien($ligne);
$tpl->assign('url',$lien->URL);

$tpl->setConditionalvalue($ligne->modifiable,"modifiable",traduction("oui"),traduction ("non"));
$tpl->setConditionalvalue($ligne->filtree,"filtree",traduction("oui"),traduction ("non"));



//montrer les parcours qui l'utilisent
// si admin tous sinon les miens
$usages=get_parcours_ressource($ligne->id,is_admin());
$aucun=is_admin()?  traduction ("aucun",false): traduction ("aucun_de_vos",false);

$nb=count($usages)?count($usages):$aucun;

$tpl->assign("_ROOT.form_utilisee_parcours",traduction ("form_utilisee_parcours",true,$nb));
foreach($usages as $rowd) {
	$tpl->newBlock("fonction_s");
	$tpl->assign("fonction_s.fonc", $rowd->id_parcours);
	switch ($rowd->type) {
		case "cr�ation":  $type=$rowd->type ." ".$rowd->login." : ".$rowd->titre ; break;
        case  "examen" :   $type=$rowd->type ." ".$rowd->examen; break;
        case  "croisement creation/examen":$type=$rowd->type ." ".$rowd->examen."/".$rowd->login;  break;
         default:
           $type=""; break;
	}
    $tpl->assign("type",$type);
    print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../parcours/fiche.php?id=".$rowd->id_parcours));
}




//selon qu'elle est appel�e par la liste ds notions ou un parcours c'est different !
// $CFG->boutons_retour_fermer_haut=0;
$tpl->print_boutons_fermeture($url_retour);

$tpl->printToScreen(); //affichage
?>

